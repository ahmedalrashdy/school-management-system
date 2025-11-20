@php
    $exceptionMessage = null;
    if (isset($exception) && method_exists($exception, 'getMessage')) {
        $message = $exception->getMessage();
        // عرض الرسالة فقط إذا كانت مختلفة عن الرسالة الافتراضية
        if ($message && $message !== 'Not Found' && $message !== '404' && $message !== '') {
            $exceptionMessage = $message;
        }
    }
@endphp

<x-layouts.guest pageTitle="404 - الصفحة غير موجودة">
    <div class="min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8 relative">
        <div class="max-w-2xl w-full text-center relative z-10">
            <!-- Animated 404 Number -->
            <div class="mb-8" x-data="{
                count: 0,
                target: 404,
                duration: 2000,
                init() {
                    const increment = this.target / (this.duration / 16);
                    const timer = setInterval(() => {
                        this.count += increment;
                        if (this.count >= this.target) {
                            this.count = this.target;
                            clearInterval(timer);
                        }
                    }, 16);
                }
            }">
                <h1
                    class="text-9xl sm:text-[12rem] font-bold text-transparent bg-clip-text bg-gradient-to-r from-primary-500 via-accent-500 to-secondary-500 dark:from-primary-400 dark:via-accent-400 dark:to-secondary-400">
                    <span x-text="Math.floor(count)">404</span>
                </h1>
            </div>

            <!-- Error Icon -->
            <div class="mb-6 flex justify-center">
                <div class="relative">
                    <div
                        class="absolute inset-0 bg-primary-500/20 dark:bg-primary-400/20 rounded-full blur-2xl animate-pulse">
                    </div>
                    <div
                        class="relative bg-white dark:bg-gray-800 p-6 rounded-full shadow-lg border-2 border-primary-100 dark:border-primary-900">
                        <i class="fas fa-exclamation-triangle text-6xl text-primary-600 dark:text-primary-400"></i>
                    </div>
                </div>
            </div>

            <!-- Error Message -->
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                عذراً، الصفحة غير موجودة
            </h2>

            <!-- Custom Error Message from abort(404, 'message') -->
            @if($exceptionMessage)
                <div class="mb-6 max-w-md mx-auto">
                    <div
                        class="bg-danger-50 dark:bg-danger-900/30 border-r-4 border-danger-500 dark:border-danger-400 p-4 rounded-lg">
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-danger-500 dark:text-danger-400 mt-1 ml-3"></i>
                            <p class="text-sm text-danger-700 dark:text-danger-300 text-right flex-1">
                                {{ $exceptionMessage }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <p class="text-lg text-gray-600 dark:text-gray-400 mb-8 max-w-md mx-auto">
                @if(!$exceptionMessage)
                    يبدو أن الصفحة التي تبحث عنها قد تم نقلها أو حذفها أو لم تكن موجودة من الأساس.
                @else
                    يرجى التحقق من المعلومات أعلاه أو العودة للصفحة الرئيسية.
                @endif
            </p>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                <a href="{{ route('home') }}"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-primary-600 hover:bg-primary-700 dark:bg-primary-500 dark:hover:bg-primary-600 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-all duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                    <i class="fas fa-home"></i>
                    <span>العودة للصفحة الرئيسية</span>
                </a>

                @auth
                    <a href="{{ route('dashboard.index') }}"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-white dark:bg-gray-800 border-2 border-gray-300 dark:border-gray-600 hover:border-primary-500 dark:hover:border-primary-400 text-gray-700 dark:text-gray-300 font-medium rounded-lg shadow-md hover:shadow-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>لوحة التحكم</span>
                    </a>
                @else
                    <a href="{{ route('login') }}"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-white dark:bg-gray-800 border-2 border-gray-300 dark:border-gray-600 hover:border-primary-500 dark:hover:border-primary-400 text-gray-700 dark:text-gray-300 font-medium rounded-lg shadow-md hover:shadow-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>تسجيل الدخول</span>
                    </a>
                @endauth
            </div>

            <!-- Helpful Links -->
            <div class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-700">
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">ربما تبحث عن:</p>
                <div class="flex flex-wrap justify-center gap-4">
                    @auth
                        <a href="{{ route('dashboard.index') }}"
                            class="text-sm text-primary-600 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-300 hover:underline transition-colors">
                            <i class="fas fa-home mr-1"></i> لوحة التحكم
                        </a>
                    @endauth
                    <a href="{{ route('home') }}"
                        class="text-sm text-primary-600 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-300 hover:underline transition-colors">
                        <i class="fas fa-info-circle mr-1"></i> الصفحة الرئيسية
                    </a>
                </div>
            </div>
        </div>

        <!-- Decorative Elements -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none -z-10">
            <div
                class="absolute top-1/4 left-1/4 w-72 h-72 bg-primary-200 dark:bg-primary-900/40 rounded-full mix-blend-multiply dark:mix-blend-screen filter blur-xl opacity-20 animate-blob">
            </div>
            <div
                class="absolute top-1/3 right-1/4 w-72 h-72 bg-accent-200 dark:bg-accent-900/40 rounded-full mix-blend-multiply dark:mix-blend-screen filter blur-xl opacity-20 animate-blob animation-delay-2000">
            </div>
            <div
                class="absolute bottom-1/4 left-1/3 w-72 h-72 bg-secondary-200 dark:bg-secondary-900/40 rounded-full mix-blend-multiply dark:mix-blend-screen filter blur-xl opacity-20 animate-blob animation-delay-4000">
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            @keyframes blob {
                0% {
                    transform: translate(0px, 0px) scale(1);
                }

                33% {
                    transform: translate(30px, -50px) scale(1.1);
                }

                66% {
                    transform: translate(-20px, 20px) scale(0.9);
                }

                100% {
                    transform: translate(0px, 0px) scale(1);
                }
            }

            .animate-blob {
                animation: blob 7s infinite;
            }

            .animation-delay-2000 {
                animation-delay: 2s;
            }

            .animation-delay-4000 {
                animation-delay: 4s;
            }
        </style>
    @endpush
</x-layouts.guest>