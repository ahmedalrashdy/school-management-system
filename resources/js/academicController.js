document.addEventListener('alpine:init', () => {
    Alpine.data('academicController', ({ yearsTree, defaultYear, defaultTerm }) => ({
        years: yearsTree,
        yearId: defaultYear,
        termId: defaultTerm,
        
        get currentTerms() {
            if (this.yearId === undefined || this.yearId === null || this.yearId === "") return [];
            const year = this.years.find(y => y.id == this.yearId);
            return year ? year.academic_terms : [];
        },

        yearInput: {
            ['x-model']: 'yearId',
            ['@change']() {
                this.termId = null;
            }
        },

        termInput: {
            ['x-model']: 'termId',
            [':disabled']() {
                return !this.yearId;
            },
            ['x-html']() {
                let options = '<option value="">اختر الفصل الدراسي</option>';
                this.currentTerms.forEach(term => {
                    const selected = term.id == this.termId ? 'selected' : '';
                    options += `<option value="${term.id}" ${selected}>${term.name}</option>`;
                });
                return options;
            }
        }
    }));
});
