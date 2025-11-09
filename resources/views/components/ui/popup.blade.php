@props(['width' => 'w-64'])

<div
    x-data="smartPopup"
    class="relative h-full w-full "
    x-cloak
>
    {{-- Trigger --}}
    <div
        x-bind="trigger"
        {{ $trigger->attributes }}
    >
        {{ $trigger }}
    </div>

    {{-- Content --}}
    <div
        x-bind="dialogue"
        {{ $attributes->merge([
            'class' => "absolute z-50 $width rounded-xl shadow-lg
                                                                                                                                                    bg-white ring-1 ring-black/5
                                                                                                                                                    dark:bg-gray-800 dark:ring-1 dark:ring-white/10 dark:shadow-black/50
                                                                                                                                                    focus:outline-none",
        ]) }}
    >

        <div
            class="py-1 divide-y divide-gray-100 dark:divide-gray-700 max-h-[calc(100vh-150px)] overflow-y-auto scrollbar-hide">
            {{ $slot }}
        </div>
    </div>
</div>

@pushOnce('scripts')
    <script>
        if (typeof window.smartPopup === 'undefined') {
            window.smartPopup = function() {
                return {
                    open: false,
                    isTop: false, // هل يظهر في الأعلى؟
                    alignRight: true, // هل يحاذي اليمين (مناسب للعربية RTL)؟
                    trigger: {
                        ['x-ref']: 'trigger',
                        ['@click']() {
                            this.open = !this.open;
                            if (this.open) {
                                // ننتظر حتى يتم رسم العنصر لحساب القياسات
                                this.$nextTick(() => this.calculatePosition());
                            }
                        },
                        ['@resize.window.debounce']() {
                            if (this.open) this.calculatePosition();
                        }
                    },

                    dialogue: {
                        ['x-ref']: 'content',
                        ['x-show']() {
                            return this.open
                        },
                        ['@click.outside']() {
                            this.open = false
                        },
                        ['style']: 'display: none;',
                        // نربط الكلاسات بالمتغيرات المحسوبة
                        [':class']() {
                            return {
                                'top-full mt-2': !this.isTop, // يظهر في الأسفل (الوضع الطبيعي)
                                'bottom-full mb-2': this.isTop, // يظهر في الأعلى
                                'right-0 origin-top-right': this.alignRight && !this.isTop,
                                'left-0 origin-top-left': !this.alignRight && !this.isTop,
                                'right-0 origin-bottom-right': this.alignRight && this.isTop,
                                'left-0 origin-bottom-left': !this.alignRight && this.isTop,
                            }
                        },
                        // تأثيرات حركية
                        ['x-transition:enter']: 'transition ease-out duration-200',
                        ['x-transition:enter-start']() {
                            return this.isTop ? 'opacity-0 translate-y-2' : 'opacity-0 -translate-y-2'
                        },
                        ['x-transition:enter-end']: 'opacity-100 translate-y-0',
                        ['x-transition:leave']: 'transition ease-in duration-150',
                        ['x-transition:leave-start']: 'opacity-100 translate-y-0',
                        ['x-transition:leave-end']() {
                            return this.isTop ? 'opacity-0 translate-y-2' : 'opacity-0 -translate-y-2'
                        },
                    },
                    init() {
                        this.open = false;

                        // 2. حل مشكلة زر الرجوع (History Back)
                        window.addEventListener('pageshow', (event) => {
                            this.open = false;
                        });
                    },
                    calculatePosition() {
                        const triggerEl = this.$refs.trigger;
                        const contentEl = this.$refs.content;

                        // 1. إظهار العنصر خفية لحساب أبعاده الحقيقية
                        // نحتاج هذه الحيلة لأن العنصر المخفي (display: none) لا أبعاد له
                        const prevVisibility = contentEl.style.visibility;
                        const prevDisplay = contentEl.style.display;
                        contentEl.style.visibility = 'hidden';
                        contentEl.style.display = 'block';

                        const triggerRect = triggerEl.getBoundingClientRect();
                        const contentRect = contentEl.getBoundingClientRect();
                        const viewportHeight = window.innerHeight;
                        const viewportWidth = window.innerWidth;

                        // 2. إعادة العنصر لوضعه الطبيعي
                        contentEl.style.display = prevDisplay;
                        contentEl.style.visibility = prevVisibility;

                        // 3. الحساب العمودي (هل يوجد مساحة في الأسفل؟)
                        const spaceBelow = viewportHeight - triggerRect.bottom;
                        // إذا كانت المساحة تحت أقل من ارتفاع المحتوى، والمساحة فوق أكبر -> اقلب للأعلى
                        this.isTop = (spaceBelow < contentRect.height + 20) && (triggerRect.top >
                            contentRect.height + 20);

                        // 4. الحساب الأفقي (هل خرج عن الشاشة؟)
                        // نفترض المحاذاة لليمين (RTL) كبداية
                        // في RTL: right-0 تعني أن الحافة اليمنى للقائمة مع الحافة اليمنى للزر
                        // لذا الامتداد سيكون لليسار. نفحص هل خرجت القائمة من يسار الشاشة؟
                        const contentEndsAt = triggerRect.right - contentRect
                            .width; // أين تنتهي القائمة يساراً

                        if (document.dir === 'rtl') {
                            // في العربية: الديفولت يمين، إذا خرج من اليسار (صار سالب) -> اقلبه يسار
                            this.alignRight = (contentEndsAt > 0);
                        } else {
                            // في الإنجليزية: الديفولت يسار، نعكس المنطق
                            const contentEndsRight = triggerRect.left + contentRect.width;
                            this.alignRight = (contentEndsRight > viewportWidth);
                        }
                    }
                };
            }
        }
    </script>
@endPushOnce
