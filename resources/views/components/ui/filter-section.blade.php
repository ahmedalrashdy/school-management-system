@props([
    'href' => null,
    'showReset' => false,
])
<x-ui.card class="mb-6">
    <form
        method="GET"
        id="filterSection"
        action="{{ $href ?? url()->current() }}"
        class="flex flex-wrap gap-4"
    >
        {{ $slot }}
        <div class="flex items-end gap-2">
            <x-ui.button
                type="submit"
                variant="primary"
                class="inline-flex mb-4 items-center gap-2"
            >
                <i class="fas fa-filter mr-2"></i>
                تصفية
            </x-ui.button>
            @if ($showReset)
                <a
                    href="{{ $href ?? url()->current() }}"
                    class="inline-flex mb-4 items-center gap-2 px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition"
                >
                    <i class="fas fa-times"></i>
                    إعادة تعيين
                </a>
            @endif
        </div>
    </form>
</x-ui.card>

@pushOnce('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('filterSection');

            if (!form) return;

            form.addEventListener('submit', () => {
                const inputs = form.querySelectorAll('input, select, textarea');

                inputs.forEach(input => {
                    const value = input.value?.trim();

                    if (value === '' || value === null || value === undefined) {
                        input.disabled = true;
                    }
                });
            });
        });
    </script>
@endPushOnce
