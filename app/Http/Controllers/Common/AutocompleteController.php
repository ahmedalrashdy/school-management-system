<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Http\Requests\Common\AutocompleteRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class AutocompleteController extends Controller
{
    public function __invoke(AutocompleteRequest $request): JsonResponse
    {
        $resourceKey = $request->string('resource')->toString();
        $resource = config("autocomplete.resources.$resourceKey");

        abort_if(is_null($resource), 404, 'المورد المطلوب غير متاح.');

        /** @var class-string<Model> $modelClass */
        $modelClass = $resource['model'];
        $textColumn = $resource['text_column'] ?? 'name';
        $searchableColumns = $resource['searchable_columns'] ?? [$textColumn];
        $perPage = $request->integer('per_page', config('autocomplete.default_per_page', 10));
        $cursor = $request->input('cursor');

        $relationsToLoad = $this->extractRelations(array_merge($searchableColumns, [$textColumn]));

        /** @var Builder $query */
        $query = $modelClass::query()->select("{$modelClass::query()->getModel()->getTable()}.*");

        if ($relationsToLoad) {
            $query->with($relationsToLoad);
        }

        // Apply query modifiers from config if available
        if (isset($resource['query_modifier']) && is_callable($resource['query_modifier'])) {
            $resource['query_modifier']($query);
        }

        if ($search = $request->string('search')->trim()->value()) {
            $query->where(function (Builder $builder) use ($searchableColumns, $search): void {
                foreach ($searchableColumns as $column) {
                    if (Str::contains($column, '.')) {
                        [$relation, $relatedColumn] = explode('.', $column, 2);
                        $builder->orWhereHas($relation, function (Builder $relationQuery) use ($relatedColumn, $search): void {
                            $relationQuery->where($relatedColumn, 'like', "%{$search}%");
                        });

                        continue;
                    }

                    $builder->orWhere($column, 'like', "%{$search}%");
                }
            });
        }

        $query->orderBy($resource['order_by'] ?? $query->getModel()->getKeyName(), $resource['order_direction'] ?? 'asc');

        $paginator = $query->cursorPaginate($perPage, ['*'], 'cursor', $cursor);

        $data = collect($paginator->items())->map(fn(Model $model): array => [
            'id' => $model->getKey(),
            'text' => $this->resolveColumnValue($model, $textColumn),
        ])->values();

        return response()->json([
            'data' => $data,
            'meta' => [
                'next_cursor' => optional($paginator->nextCursor())->encode(),
                'has_more_pages' => $paginator->hasMorePages(),
            ],
        ]);
    }

    /**
     * @param  array<int, string>  $columns
     * @return array<int, string>
     */
    private function extractRelations(array $columns): array
    {
        return collect($columns)
            ->filter(fn(string $column): bool => Str::contains($column, '.'))
            ->map(fn(string $column): string => explode('.', $column)[0])
            ->unique()
            ->values()
            ->all();
    }

    private function resolveColumnValue(Model $model, string $column): string
    {
        $value = Str::contains($column, '.')
            ? data_get($model, $column)
            : ($model->{$column} ?? null);

        return (string) ($value ?? '');
    }
}

