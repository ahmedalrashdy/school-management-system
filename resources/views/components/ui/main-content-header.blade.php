@props([
    'title' => '',
    'description' => '',
    'buttonText' => null,
    'buttonLink' => null,
    'btnPermissions' => null,
    'actions' => null,
])

<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                {{ $title }}
            </h2>

            @if ($description)
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    {{ $description }}
                </p>
            @endif
        </div>

        @if ($buttonText && $buttonLink && ($btnPermissions == null || auth()->user()->can($btnPermissions)))
            <a
                href="{{ $buttonLink }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition"
            >
                {{ $buttonText }}
            </a>
        @elseif ($actions)
            {{ $actions }}
        @endif
    </div>
</div>
