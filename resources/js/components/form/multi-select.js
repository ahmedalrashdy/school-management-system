export function multiSelect(config = {}, livewireValue = null) {
    return {
        name: config.name,
        multiple: config.multiple ?? true,
        searchEnabled: config.searchEnabled ?? false,
        placeholder: config.placeholder ?? 'اختر من القائمة',
        searchPlaceholder: config.searchPlaceholder ?? 'ابحث...',
        noResultsText: config.noResultsText ?? 'لا توجد نتائج',
        options: config.options ?? [],
        selectedOptions: config.initialSelected ?? [],
        searchTerm: '',
        isOpen: false,
        disabled: config.disabled ?? false,
        livewireValue,

        init() {
            if (this.livewireValue !== null) {
                this.syncFromLivewire(this.livewireValue);
                this.$watch('livewireValue', (value) => this.syncFromLivewire(value));
            }
        },

        get filteredOptions() {
            if (!this.searchEnabled || !this.searchTerm) {
                return this.options;
            }

            const term = this.searchTerm.toLowerCase();

            return this.options.filter((option) =>
                String(option.label).toLowerCase().includes(term),
            );
        },

        get hasSelection() {
            return this.selectedOptions.length > 0;
        },

        get selectedValues() {
            return this.selectedOptions.map((option) => option.value);
        },

        toggleDropdown() {
            if (this.disabled) {
                return;
            }

            this.isOpen = !this.isOpen;

            if (this.isOpen && this.searchEnabled) {
                this.$nextTick(() => {
                    this.$root.querySelector('input')?.focus();
                });
            }
        },

        closeDropdown() {
            this.isOpen = false;
        },

        selectOption(option) {
            if (this.isSelected(option.value)) {
                this.removeOption(option.value);
                return;
            }

            if (!this.multiple) {
                this.selectedOptions = [option];
                this.closeDropdown();
            } else {
                this.selectedOptions.push(option);
            }

            this.emitSelected();
        },

        removeOption(value) {
            this.selectedOptions = this.selectedOptions.filter((option) => option.value !== value);
            this.emitSelected();
        },

        clear() {
            this.selectedOptions = [];
            this.emitSelected();
        },

        isSelected(value) {
            return this.selectedOptions.some((option) => option.value === value);
        },

        emitSelected() {
            if (this.livewireValue === null) {
                return;
            }

            this.livewireValue = this.multiple
                ? this.selectedValues
                : this.selectedValues[0] ?? null;
        },

        syncFromLivewire(value) {
            if (value === undefined) {
                return;
            }

            const normalized = this.multiple
                ? Array.isArray(value) ? value : []
                : value !== null && value !== undefined ? [value] : [];

            this.selectedOptions = normalized
                .map((val) => this.options.find((option) => option.value == val))
                .filter(Boolean);
        },
    };
}

