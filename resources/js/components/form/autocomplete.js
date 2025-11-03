export function autocompleteField(config = {}) {
    return {
        name: config.name,
        resource: config.resource,
        endpoint: config.endpoint,
        multiple: config.multiple ?? false,
        placeholder: config.placeholder ?? 'ابدأ بالكتابة للبحث...',
        searchPlaceholder: config.searchPlaceholder ?? 'ابحث...',
        noResultsText: config.noResultsText ?? 'لا توجد نتائج',
        loadingText: config.loadingText ?? 'جارِ التحميل...',
        fetchMoreText: config.fetchMoreText ?? 'جارِ تحميل المزيد...',
        errorText: config.errorText ?? 'حدث خطأ أثناء الجلب',
        retryText: config.retryText ?? 'حاول مجدداً',
        perPage: config.perPage ?? 10,
        minCharacters: config.minCharacters ?? 0,
        selectedItems: config.initialSelected ?? [],
        items: [],
        nextCursor: null,
        search: '',
        isOpen: false,
        isLoading: false,
        isFetchingMore: false,
        error: null,
        failedCursor: null,
        disabled: config.disabled ?? false,
        debounceTimer: null,

        init() {
            this.fetchItems();
        },

        get hasSelection() {
            return this.selectedItems.length > 0;
        },

        get selectedValues() {
            return this.selectedItems.map((item) => item.id);
        },
        get selectedValue(){
            return this.selectedItems.length>0?this.selectedItems[0].id:null;
        },

        toggleDropdown() {
            if (this.disabled) {
                return;
            }

            this.isOpen = !this.isOpen;

            if (this.isOpen && !this.items.length) {
                this.fetchItems();
            }
        },

        closeDropdown() {
            this.isOpen = false;
        },

        handleSearchInput() {
            if (!this.isOpen) {
                this.isOpen = true;
            }

            clearTimeout(this.debounceTimer);

            if (this.search.length === 0) {
                this.debounceTimer = setTimeout(() => this.fetchItems(), 300);
                return;
            }

            if (this.minCharacters > 0 && this.search.length < this.minCharacters) {
                return;
            }

            this.debounceTimer = setTimeout(() => this.fetchItems(), 300);
        },

        handleScroll(event) {
            const element = event.target;
            if (this.isFetchingMore || !this.nextCursor) {
                return;
            }

            if (element.scrollTop + element.clientHeight >= element.scrollHeight - 60) {
                this.fetchItems(this.nextCursor, true);
            }
        },

        fetchItems(cursor = null, append = false) {
            if (!this.endpoint || !this.resource) {
                return;
            }

            if (cursor) {
                this.isFetchingMore = true;
            } else {
                this.isLoading = true;
                this.items = append ? this.items : this.items;
            }

            this.error = null;
            this.failedCursor = null;

            const params = {
                resource: this.resource,
                per_page: this.perPage,
            };

            if (cursor) {
                params.cursor = cursor;
            }

            if (this.search && this.search.length >= this.minCharacters) {
                params.search = this.search;
            }

            window.axios
                .get(this.endpoint, { params })
                .then(({ data }) => {
                    const payload = data.data ?? [];

                    this.items = append ? [...this.items, ...payload] : payload;
                    this.nextCursor = data.meta?.next_cursor ?? null;
                })
                .catch((error) => {
                    this.error = error.response?.data?.message ?? this.errorText;
                    this.failedCursor = cursor;
                })
                .finally(() => {
                    this.isLoading = false;
                    this.isFetchingMore = false;
                });
        },

        retryFetch() {
            this.fetchItems(this.failedCursor, Boolean(this.failedCursor));
        },

        selectItem(item) {
            if (this.isSelected(item.id)) {
                this.removeItem(item.id);
                return;
            }

            if (!this.multiple) {
                this.selectedItems = [item];
                this.closeDropdown();
            } else {
                this.selectedItems.push(item);
            }

        },

        removeItem(id) {
            this.selectedItems = this.selectedItems.filter((item) => item.id !== id);
        },

        isSelected(id) {
            return this.selectedItems.some((item) => item.id === id);
        },
    };
}

