@props([
    'headers' => [],
])

@php
    $processedHeaders = collect($headers)->map(function ($header, $index) {
        if (is_string($header)) {
            $header = ['label' => $header];
        }
        $label = $header['label'] ?? '';
        $textAlign = $header['text_align'] ?? ($header['textAlign'] ?? 'right');
        $textAlign = in_array($textAlign, ['left', 'center', 'right'], true) ? $textAlign : 'right';

        return [
            'label' => $label,
            'text_align' => $textAlign,
            'icon' => $header['icon'] ?? null,
            'sortable' => (bool) ($header['isSortable'] ?? ($header['sortable'] ?? false)),
            'key' =>
                $header['key'] ?? ($header['name'] ?? \Illuminate\Support\Str::slug($label ?: 'column_' . $index, '_')),
        ];
    });

    $textAlignmentClasses = [
        'left' => 'text-left',
        'center' => 'text-center',
        'right' => 'text-right',
    ];
@endphp

<div x-data="{
    sortColumn: null,
    sortDirection: 'asc',
    toggleSort(column) {
        if (!column) {
            return;
        }

        if (this.sortColumn === column) {
            this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            this.sortColumn = column;
            this.sortDirection = 'asc';
        }

        this.$dispatch('table-sort-changed', { column, direction: this.sortDirection });
    }
}" {{ $attributes->merge(['class' => 'block']) }}>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            @if ($processedHeaders->isNotEmpty())
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        @foreach ($processedHeaders as $header)
                            @php
                                $alignClass =
                                    $textAlignmentClasses[$header['text_align']] ?? $textAlignmentClasses['right'];
                            @endphp
                            <th
                                class="px-6 py-3 {{ $alignClass }} text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                @if ($header['sortable'])
                                    <button type="button"
                                        class="inline-flex items-center gap-2 text-xs font-medium uppercase tracking-wider"
                                        x-on:click="toggleSort('{{ $header['key'] }}')"
                                        x-bind:aria-pressed="sortColumn === '{{ $header['key'] }}'">
                                        @if ($header['icon'])
                                            <i class="{{ $header['icon'] }} text-base"></i>
                                        @endif
                                        <span>{{ $header['label'] }}</span>
                                        <span class="text-gray-400 dark:text-gray-500" x-cloak
                                            x-show="sortColumn === '{{ $header['key'] }}'">
                                            <i class="fas"
                                                :class="sortDirection === 'asc' ? 'fa-sort-up' : 'fa-sort-down'"></i>
                                        </span>
                                        <span class="text-gray-300 dark:text-gray-600" x-cloak
                                            x-show="sortColumn !== '{{ $header['key'] }}'">
                                            <i class="fas fa-sort"></i>
                                        </span>
                                    </button>
                                @else
                                    <div class="inline-flex items-center gap-2">
                                        @if ($header['icon'])
                                            <i class="{{ $header['icon'] }} text-base"></i>
                                        @endif
                                        <span>{{ $header['label'] }}</span>
                                    </div>
                                @endif
                            </th>
                        @endforeach
                    </tr>
                </thead>
            @endif
            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                {{ $slot }}
            </tbody>
        </table>
    </div>
</div>
