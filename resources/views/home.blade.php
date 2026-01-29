<x-layouts.guest pageTitle="الصفحة الرئيسية">
    @section('title', 'الصفحة الرئيسية')
    @php
        $setting = fn(string $key, $default = null) => school()->schoolSetting($key, $default);
    @endphp
    <!-- Navigation Bar -->
    <nav
        class="fixed top-0 left-0 right-0 z-50 transition-all duration-300"
        x-data="{ scrolled: false, mobileMenuOpen: false }"
        @scroll.window="scrolled = window.scrollY > 50"
        :class="scrolled ? 'bg-white/90 dark:bg-gray-900/90 backdrop-blur-md shadow-lg' : 'bg-transparent'"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Logo -->
                <div class="flex items-center gap-3">
                    @if ($setting('school_logo'))
                        <img
                            class="w-12 h-12 bg-gray-800 hover:bg-primary-600 rounded-lg flex items-center justify-center transition-colors"
                            src="{{ \Storage::url($setting('school_logo')) }}"
                        />
                    @else
                        <div
                            class="w-12 h-12 bg-linear-to-br from-primary-600 to-primary-800 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-graduation-cap text-white text-xl"></i>
                        </div>
                    @endif
                    <div>
                        <h1 class="text-xl font-bold text-gray-900 dark:text-white">
                            {{ $setting('school_name') ?? config('app.name') }}</h1>
                        <p class="text-xs text-gray-600 dark:text-gray-400">نظام إدارة المدرسة</p>
                    </div>
                </div>

                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center gap-8">
                    <a
                        class="text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 transition-colors font-medium"
                        href="#home"
                    >الرئيسية</a>
                    <a
                        class="text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 transition-colors font-medium"
                        href="#about"
                    >عن المدرسة</a>
                    <a
                        class="text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 transition-colors font-medium"
                        href="#stats"
                    >الإحصائيات</a>
                    <a
                        class="text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 transition-colors font-medium"
                        href="#news"
                    >الأخبار</a>
                    <a
                        class="text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 transition-colors font-medium"
                        href="#facilities"
                    >المرافق</a>
                    <a
                        class="text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 transition-colors font-medium"
                        href="#contact"
                    >اتصل بنا</a>
                </div>

                <!-- Auth Buttons -->
                <div class="hidden md:flex items-center gap-4">
                    @auth
                        @can(\Perm::AccessAdminPanel->value)
                            <a
                                class="px-6 py-2.5 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors font-medium shadow-lg hover:shadow-xl"
                                href="{{ route('dashboard.index') }}"
                            >
                                لوحة التحكم
                            </a>
                            @elserole('مدرس')
                            <a
                                class="px-6 py-2.5 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors font-medium shadow-lg hover:shadow-xl"
                                href="{{ route('portal.teacher.index') }}"
                            >
                                لوحة التحكم
                            </a>
                            @elserole('ولي أمر')
                            <a
                                class="px-6 py-2.5 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors font-medium shadow-lg hover:shadow-xl"
                                href="{{ route('portal.guardian.index') }}"
                            >
                                لوحة التحكم
                            </a>
                            @elserole('طالب')
                            <a
                                class="px-6 py-2.5 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors font-medium shadow-lg hover:shadow-xl"
                                href="{{ route('portal.student.index') }}"
                            >
                                لوحة التحكم
                            </a>
                        @else
                            <form
                                action="{{ route('logout') }}"
                                method="post"
                            >
                                @csrf
                                <button
                                    type="submit"
                                    class="px-6 py-2.5 bg-danger-600 text-white rounded-lg hover:bg-primary-700 transition-colors font-medium shadow-lg hover:shadow-xl"
                                >
                                    تسجيل الخروج
                                </button>
                            </form>
                        @endcan
                    @else
                        <a
                            class="px-6 py-2.5 text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 transition-colors font-medium"
                            href="{{ route('login') }}"
                        >
                            تسجيل الدخول
                        </a>
                    @endauth
                </div>

                <!-- Mobile Menu Button -->
                <button
                    class="md:hidden p-2 text-gray-700 dark:text-gray-300"
                    @click="mobileMenuOpen = !mobileMenuOpen"
                >
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>

            <!-- Mobile Menu -->
            <div
                class="md:hidden pb-4 border-t border-gray-200 dark:border-gray-700 mt-4"
                x-show="mobileMenuOpen"
                x-transition
            >
                <div class="flex flex-col gap-4 pt-4">
                    <a
                        class="text-gray-700 dark:text-gray-300 hover:text-primary-600"
                        href="#home"
                    >الرئيسية</a>
                    <a
                        class="text-gray-700 dark:text-gray-300 hover:text-primary-600"
                        href="#about"
                    >عن المدرسة</a>
                    <a
                        class="text-gray-700 dark:text-gray-300 hover:text-primary-600"
                        href="#stats"
                    >الإحصائيات</a>
                    <a
                        class="text-gray-700 dark:text-gray-300 hover:text-primary-600"
                        href="#news"
                    >الأخبار</a>
                    <a
                        class="text-gray-700 dark:text-gray-300 hover:text-primary-600"
                        href="#facilities"
                    >المرافق</a>
                    <a
                        class="text-gray-700 dark:text-gray-300 hover:text-primary-600"
                        href="#contact"
                    >اتصل بنا</a>
                    @auth
                        <a
                            class="px-4 py-2 bg-primary-600 text-white rounded-lg text-center"
                            href="{{ route('dashboard.index') }}"
                        >لوحة التحكم</a>
                    @else
                        <a
                            class="px-4 py-2 border border-primary-600 text-primary-600 rounded-lg text-center"
                            href="{{ route('login') }}"
                        >تسجيل الدخول</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section
        class="relative min-h-screen flex items-center justify-center overflow-hidden pt-20"
        id="home"
    >
        <!-- Background Gradient -->
        <div
            class="absolute inset-0 bg-gradient-to-br from-primary-50 via-blue-50 to-indigo-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
        </div>

        <!-- Animated Background Elements -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute top-20 left-20 w-72 h-72 bg-primary-300/20 rounded-full blur-3xl animate-pulse"></div>
            <div
                class="absolute bottom-20 right-20 w-96 h-96 bg-blue-300/20 rounded-full blur-3xl animate-pulse delay-1000">
            </div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <!-- Content -->
                <div
                    class="text-center lg:text-right space-y-8"
                    x-data="{
                        show: false,
                        init() {
                            setTimeout(() => this.show = true, 100);
                        }
                    }"
                    x-show="show"
                    x-transition:enter="transition ease-out duration-1000"
                    x-transition:enter-start="opacity-0 translate-x-10"
                    x-transition:enter-end="opacity-100 translate-x-0"
                >
                    <h1 class="text-5xl md:text-6xl lg:text-7xl font-bold text-gray-900 dark:text-white leading-tight">
                        نبني
                        <span class="bg-gradient-to-r from-primary-600 to-blue-600 bg-clip-text text-transparent">
                            قادة المستقبل
                        </span>
                    </h1>
                    <p class="text-xl md:text-2xl text-gray-600 dark:text-gray-300 leading-relaxed">
                        التميز الأكاديمي يبدأ هنا. نقدم تعليماً راقياً يهيئ طلابنا ليكونوا قادة الغد ومبدعي المستقبل.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                        <a
                            class="px-8 py-4 bg-gradient-to-r from-primary-600 to-blue-600 text-white rounded-xl font-semibold text-lg shadow-xl hover:shadow-2xl transform hover:scale-105 transition-all duration-300 flex items-center justify-center gap-2"
                            href="#about"
                        >
                            <span>تعرّف علينا</span>
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        @guest
                            <a
                                class="px-8 py-4 bg-white dark:bg-gray-800 text-primary-600 dark:text-primary-400 rounded-xl font-semibold text-lg border-2 border-primary-600 dark:border-primary-400 shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300 flex items-center justify-center gap-2"
                                href="{{ route('login') }}"
                            >
                                <span>سجّل الآن</span>
                                <i class="fas fa-user-plus"></i>
                            </a>
                        @endguest
                    </div>
                </div>

                <!-- Image -->
                <div
                    class="relative"
                    x-data="{
                        show: false,
                        init() {
                            setTimeout(() => this.show = true, 300);
                        }
                    }"
                    x-show="show"
                    x-transition:enter="transition ease-out duration-1000"
                    x-transition:enter-start="opacity-0 translate-x-10"
                    x-transition:enter-end="opacity-100 translate-x-0"
                >
                    <div
                        class="relative rounded-3xl overflow-hidden shadow-2xl transform hover:scale-105 transition-transform duration-500">
                        <img
                            class="w-full h-[600px] object-cover"
                            src="https://images.pexels.com/photos/8793386/pexels-photo-8793386.jpeg"
                            alt="طلاب المدرسة"
                        >
                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                    </div>
                    <!-- Decorative Elements -->
                    <div class="absolute -bottom-6 -right-6 w-32 h-32 bg-primary-400/30 rounded-full blur-2xl"></div>
                    <div class="absolute -top-6 -left-6 w-24 h-24 bg-blue-400/30 rounded-full blur-2xl"></div>
                </div>
            </div>
        </div>

        <!-- Scroll Indicator -->
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
            <a
                class="text-gray-600 dark:text-gray-400 hover:text-primary-600 transition-colors"
                href="#about"
            >
                <i class="fas fa-chevron-down text-2xl"></i>
            </a>
        </div>
    </section>

    <!-- About Us Section -->
    <section
        class="py-20 bg-white dark:bg-gray-800"
        id="about"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-4">عن المدرسة</h2>
                <div class="w-24 h-1 bg-gradient-to-r from-primary-600 to-blue-600 mx-auto rounded-full"></div>
            </div>

            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div class="space-y-6">
                    <p class="text-lg text-gray-600 dark:text-gray-300 leading-relaxed">
                        نحن مدرسة رائدة في مجال التعليم، نسعى لتقديم تعليم متميز يجمع بين الأصالة والمعاصرة.
                        نؤمن بأن التعليم هو الأساس لبناء جيل واعٍ ومبدع قادر على مواجهة تحديات المستقبل.
                    </p>
                    <div class="space-y-4">
                        <div class="flex items-start gap-4">
                            <div
                                class="w-12 h-12 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center shrink-0">
                                <i class="fas fa-eye text-primary-600 dark:text-primary-400 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">رؤيتنا</h3>
                                <p class="text-gray-600 dark:text-gray-300">
                                    أن نكون المدرسة الرائدة في تقديم تعليم متميز يهيئ الطلاب ليكونوا قادة المستقبل
                                    ومبدعيه.
                                </p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div
                                class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center shrink-0">
                                <i class="fas fa-bullseye text-blue-600 dark:text-blue-400 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">رسالتنا</h3>
                                <p class="text-gray-600 dark:text-gray-300">
                                    تقديم تعليم عالي الجودة يطور قدرات الطلاب الفكرية والاجتماعية، ويرسخ القيم الأصيلة،
                                    ويهيئهم للمساهمة الفعالة في بناء مجتمعهم.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="relative">
                    <div class="rounded-2xl overflow-hidden shadow-2xl">
                        <img
                            class="w-full h-[500px] object-cover"
                            src="https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=800&q=80"
                            alt="حرم المدرسة"
                        >
                    </div>
                    <div class="absolute -bottom-6 -right-6 w-48 h-48 bg-primary-200/20 rounded-full blur-3xl"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section
        class="py-20 bg-gradient-to-br from-primary-600 to-blue-600 relative overflow-hidden"
        id="stats"
    >
        <div
            class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60"
            height="60"
            viewBox="0 0 60 60"
            xmlns="http://www.w3.org/2000/svg"%3E%3Cg
            fill="none"
            fill-rule="evenodd"%3E%3Cg
            fill="%23ffffff"
            fill-opacity="0.1"%3E%3Cpath
            d="M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z"
            /%3E%3C/g%3E%3C/g%3E%3C/svg%3E')]
            opacity-20"
        ></div>
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold text-white mb-4">إحصائياتنا</h2>
                <p class="text-xl text-white/90">أرقام تتحدث عن التميز والإنجاز</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- Students Stat -->
                <div
                    class="bg-white/10 backdrop-blur-md rounded-2xl p-8 text-center border border-white/20 shadow-2xl transform hover:scale-105 transition-transform duration-300"
                    x-data="{
                        count: 0,
                        target: {{ $stats['total_students'] }},
                        init() {
                            let duration = 2000;
                            let steps = 60;
                            let increment = this.target / steps;
                            let current = 0;
                            let timer = setInterval(() => {
                                current += increment;
                                if (current >= this.target) {
                                    this.count = this.target;
                                    clearInterval(timer);
                                } else {
                                    this.count = Math.floor(current);
                                }
                            }, duration / steps);
                        }
                    }"
                >
                    <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-user-graduate text-white text-3xl"></i>
                    </div>
                    <div
                        class="text-5xl md:text-6xl font-bold text-white mb-2"
                        x-text="count.toLocaleString('ar-EG')"
                    ></div>
                    <div class="text-xl text-white/90">طالب</div>
                </div>

                <!-- Teachers Stat -->
                <div
                    class="bg-white/10 backdrop-blur-md rounded-2xl p-8 text-center border border-white/20 shadow-2xl transform hover:scale-105 transition-transform duration-300"
                    x-data="{
                        count: 0,
                        target: {{ $stats['total_teachers'] }},
                        init() {
                            let duration = 2000;
                            let steps = 60;
                            let increment = this.target / steps;
                            let current = 0;
                            let timer = setInterval(() => {
                                current += increment;
                                if (current >= this.target) {
                                    this.count = this.target;
                                    clearInterval(timer);
                                } else {
                                    this.count = Math.floor(current);
                                }
                            }, duration / steps);
                        }
                    }"
                >
                    <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-chalkboard-teacher text-white text-3xl"></i>
                    </div>
                    <div
                        class="text-5xl md:text-6xl font-bold text-white mb-2"
                        x-text="count.toLocaleString('ar-EG')"
                    ></div>
                    <div class="text-xl text-white/90">معلّم</div>
                </div>

                <!-- Graduates Stat -->
                <div
                    class="bg-white/10 backdrop-blur-md rounded-2xl p-8 text-center border border-white/20 shadow-2xl transform hover:scale-105 transition-transform duration-300"
                    x-data="{
                        count: 0,
                        target: {{ $stats['total_graduates'] }},
                        init() {
                            let duration = 2000;
                            let steps = 60;
                            let increment = this.target / steps;
                            let current = 0;
                            let timer = setInterval(() => {
                                current += increment;
                                if (current >= this.target) {
                                    this.count = this.target;
                                    clearInterval(timer);
                                } else {
                                    this.count = Math.floor(current);
                                }
                            }, duration / steps);
                        }
                    }"
                >
                    <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-trophy text-white text-3xl"></i>
                    </div>
                    <div
                        class="text-5xl md:text-6xl font-bold text-white mb-2"
                        x-text="count.toLocaleString('ar-EG')"
                    ></div>
                    <div class="text-xl text-white/90">خريج</div>
                </div>
            </div>
        </div>
    </section>

    <!-- News & Events Section -->
    <section
        class="py-20 bg-gray-50 dark:bg-gray-900"
        id="news"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-4">الأخبار والفعاليات</h2>
                <div class="w-24 h-1 bg-gradient-to-r from-primary-600 to-blue-600 mx-auto rounded-full"></div>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach ($news as $item)
                    <div
                        class="bg-white dark:bg-gray-800 rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transform hover:scale-105 transition-all duration-300">
                        <div class="relative h-48 overflow-hidden">
                            <img
                                class="w-full h-full object-cover transform hover:scale-110 transition-transform duration-500"
                                src="{{ $item['image'] }}"
                                alt="{{ $item['title'] }}"
                            >
                            <div
                                class="absolute top-4 right-4 bg-primary-600 text-white px-4 py-2 rounded-lg text-sm font-semibold">
                                {{ \Carbon\Carbon::parse($item['date'])->format('d M') }}
                            </div>
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3 line-clamp-2">
                                {{ $item['title'] }}</h3>
                            <p class="text-gray-600 dark:text-gray-300 mb-4 line-clamp-3">{{ $item['excerpt'] }}</p>
                            <a
                                class="text-primary-600 dark:text-primary-400 font-semibold hover:underline flex items-center gap-2"
                                href="#"
                            >
                                <span>اقرأ المزيد</span>
                                <i class="fas fa-arrow-left text-sm"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Facilities Section -->
    <section
        class="py-20 bg-white dark:bg-gray-800"
        id="facilities"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-4">مرافقنا</h2>
                <div class="w-24 h-1 bg-gradient-to-r from-primary-600 to-blue-600 mx-auto rounded-full"></div>
                <p class="text-lg text-gray-600 dark:text-gray-300 mt-4">مرافق حديثة ومجهزة بأحدث التقنيات</p>
            </div>

            <div class="grid md:grid-cols-2 gap-8">
                @foreach ($facilities as $facility)
                    <div
                        class="group relative overflow-hidden rounded-2xl shadow-xl hover:shadow-2xl transform hover:scale-105 transition-all duration-500">
                        <div class="relative h-80 overflow-hidden">
                            <img
                                class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700"
                                src="{{ $facility['image'] }}"
                                alt="{{ $facility['name'] }}"
                            >
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent">
                            </div>
                            <div class="absolute bottom-0 left-0 right-0 p-6 text-white">
                                <h3 class="text-2xl font-bold mb-2">{{ $facility['name'] }}</h3>
                                <p class="text-white/90">{{ $facility['description'] }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer
        class="bg-gray-900 text-white py-16"
        id="contact"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-3 gap-12 mb-12">
                <!-- School Info -->
                <div>
                    <div class="flex items-center gap-3 mb-6">
                        @if ($setting('school_logo'))
                            <img
                                class="w-12 h-12 bg-gray-800 hover:bg-primary-600 rounded-lg flex items-center justify-center transition-colors"
                                src="{{ \Storage::url($setting('school_logo')) }}"
                            />
                        @else
                            <div
                                class="w-12 h-12 bg-linear-to-br from-primary-600 to-primary-800 rounded-xl flex items-center justify-center shadow-lg">
                                <i class="fas fa-graduation-cap text-white text-xl"></i>
                            </div>
                        @endif
                        <div>
                            <h3 class="text-xl font-bold">{{ $setting('school_name') ?? config('app.name') }}</h3>
                            <p class="text-sm text-gray-400">نظام إدارة المدرسة</p>
                        </div>
                    </div>
                    <p class="text-gray-400 leading-relaxed">
                        نقدم تعليماً متميزاً يجمع بين الأصالة والمعاصرة، ويهيئ طلابنا ليكونوا قادة المستقبل.
                    </p>
                </div>

                <!-- Contact Info -->
                <div>
                    <h4 class="text-xl font-bold mb-6">معلومات الاتصال</h4>
                    <div class="space-y-4">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-map-marker-alt text-primary-400 mt-1"></i>
                            <div>
                                <p class="text-gray-400">العنوان:</p>
                                <p class="text-white">{{ $setting('school_address') }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <i class="fas fa-phone text-primary-400 mt-1"></i>
                            <div>
                                <p class="text-gray-400">الهاتف:</p>
                                <p class="text-white">{{ $setting('school_phone') }}</p>
                                <p class="text-white">{{ $setting('school_phone_secandory') }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <i class="fas fa-envelope text-primary-400 mt-1"></i>
                            <div>
                                <p class="text-gray-400">البريد الإلكتروني:</p>
                                <p class="text-white">{{ $setting('school_email') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Social Media -->
                <div>
                    <h4 class="text-xl font-bold mb-6">تابعنا</h4>
                    <div class="flex gap-4 mb-6">
                        @php
                            $linkIcons = [
                                'twitter' => 'fa-twitter',
                                'youtube' => 'fa-youtube',
                                'instagram' => 'fa-instagram',
                                'facebook' => 'fa-facebook-f',
                            ];

                        @endphp
                        @foreach ($setting('social_links') ?? [] as $key => $link)
                            @if (array_key_exists($key, $linkIcons))
                                <a
                                    class="w-12 h-12 bg-gray-800 hover:bg-primary-600 rounded-lg flex items-center justify-center transition-colors"
                                    href="{{ $link }}"
                                >
                                    <i class="fab {{ $linkIcons[$key] }}"></i>
                                </a>
                            @else
                                <a
                                    class="w-12 h-12 bg-gray-800 hover:bg-primary-600 rounded-lg flex items-center justify-center transition-colors"
                                    href="{{ $link }}"
                                >
                                    <i class="fab fa-chrome"></i>
                                </a>
                            @endif
                        @endforeach
                    </div>
                    <!-- Map Placeholder -->
                    <div class="bg-gray-800 rounded-lg h-32 flex items-center justify-center">
                        <i class="fas fa-map text-gray-600 text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Copyright -->
            <div class="border-t border-gray-800 pt-8 text-center">
                <p class="text-gray-400">
                    &copy; {{ date('Y') }} {{ $setting('school_name') ?? config('app.name') }}. جميع الحقوق
                    محفوظة.
                </p>
            </div>
        </div>
    </footer>

    <!-- Smooth Scroll Script -->
    <script>
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</x-layouts.guest>
