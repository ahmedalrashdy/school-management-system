@props([
    'items' => [],
    'separator' => 'chevron', // options: 'chevron', 'slash', 'arrow'
])

<nav
    class="flex mt-2"
    aria-label="Breadcrumb"
>
    <ol class="items-center hidden md:inline-flex  space-x-1 md:space-x-3 space-x-reverse rtl:space-x-reverse">
        @foreach ($items as $index => $item)
            @php
                $isLast = $index === count($items) - 1;
                $url = $item['url'] ?? '#';
                $label = $item['label'] ?? '';
                $icon = $item['icon'] ?? null;
            @endphp

            <li class="inline-flex items-center group">
                @if (!$isLast)
                    {{-- Active breadcrumb item (clickable) --}}
                    <a
                        href="{{ $url }}"
                        class="inline-flex items-center text-xs font-medium text-gray-600 dark:text-gray-400
                              hover:text-primary-600 dark:hover:text-primary-400
                              transition-all duration-200 ease-in-out
                              hover:scale-105"
                    >
                        @if ($icon)
                            <i
                                class="{{ $icon }} ml-2 text-gray-500 dark:text-gray-500 group-hover:text-primary-500 transition-colors duration-200"></i>
                        @endif
                        <span class="relative">
                            {{ $label }}
                            <span
                                class="absolute bottom-0 left-0 w-0 h-0.5 bg-primary-500 group-hover:w-full transition-all duration-300"
                            ></span>
                        </span>
                    </a>

                    {{-- Separator --}}
                    <span class="mx-2 text-gray-400 dark:text-gray-600">
                        @if ($separator === 'chevron')
                            <svg
                                class="w-4 h-4 rtl:rotate-180"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd"
                                ></path>
                            </svg>
                        @elseif($separator === 'slash')
                            <span class="text-lg font-light">/</span>
                        @elseif($separator === 'arrow')
                            <i class="fas fa-arrow-left text-xs"></i>
                        @endif
                    </span>
                @else
                    {{-- Current page (not clickable) --}}
                    <div class="inline-flex items-center text-xs font-semibold text-gray-800 dark:text-gray-200">
                        @if ($icon)
                            <i class="{{ $icon }} ml-2 text-primary-500 dark:text-primary-400"></i>
                        @endif
                        <span class="relative">
                            {{ $label }}
                            <span
                                class="absolute -bottom-1 left-0 w-full h-0.5 bg-gradient-to-r from-primary-500 to-primary-600 rounded-full"
                            ></span>
                        </span>
                    </div>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
