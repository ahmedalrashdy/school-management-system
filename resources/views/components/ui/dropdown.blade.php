@props(['align' => 'left', 'width' => 'w-56'])

@php
    $alignmentClasses = match ($align) {
        'left' => 'origin-top-left left-0',
        'right' => 'origin-top-right right-0',
        'top' => 'origin-top',
        default => 'origin-top-left left-0',
    };
@endphp

<div x-data="dropdown" class="relative h-full w-full">
    {{-- Trigger --}}
    <div x-bind="trigger" {{ $trigger->attributes }}>
        {{ $trigger }}
    </div>

    {{-- Content --}}
    <div x-bind="dialogue"
         {{ $attributes->merge(['class' => "absolute z-50 mt-2 $width rounded-xl shadow-lg 
            bg-white ring-1 ring-black/5 
            dark:bg-gray-800 dark:ring-1 dark:ring-white/10 dark:shadow-black/50
            focus:outline-none $alignmentClasses"]) }}
         style="display: none;">
        
        <div class="py-1 divide-y divide-gray-100 dark:divide-gray-700 max-h-[calc(100vh-200px)] overflow-y-auto">
            {{ $slot }}
        </div>
    </div>
</div>
@pushOnce('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('dropdown', () => ({
                open: false,

                trigger: {
                    ['x-ref']: 'trigger',
                    ['@click']() {
                        this.open = !this.open
                    },
                    [':aria-expanded']() {
                        return this.open
                    }
                },

                dialogue: {
                    ['x-show']() {
                        return this.open
                    },
                    ['@click.outside']() {
                        this.open = false
                    },
                    ['style']: 'display: none;',
                    // Transitions definition
                    ['x-transition:enter']: 'transition ease-out duration-100',
                    ['x-transition:enter-start']: 'opacity-0 scale-95',
                    ['x-transition:enter-end']: 'opacity-100 scale-100',
                    ['x-transition:leave']: 'transition ease-in duration-75',
                    ['x-transition:leave-start']: 'opacity-100 scale-100',
                    ['x-transition:leave-end']: 'opacity-0 scale-95',
                },
            }))
        })
    </script>
@endPushOnce
