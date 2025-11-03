@props([
    'title' => config('app.name', 'Laravel'),
    'subtitle' => '',
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=tajawal:400,500,700&display=swap" rel="stylesheet" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen flex">
        <!-- Right Side - Form -->
        <div class="flex-1 flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-20 xl:px-24 bg-white dark:bg-gray-900">
            <div class="mx-auto w-full max-w-md">
                <!-- Logo -->
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-primary-600 rounded-2xl mb-4">
                        <i class="fas fa-graduation-cap text-white text-3xl"></i>
                    </div>
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white">
                        نظام إدارة المدرسة
                    </h2>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        {{ $subtitle ?? 'مرحباً بك في نظام إدارة المدرسة المتكامل' }}
                    </p>
                </div>

                <!-- Content -->
                <div>
                    {{ $slot }}
                </div>

                <!-- Footer -->
                <div class="mt-8 text-center">
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        &copy; {{ date('Y') }} {{ config('app.name') }}. جميع الحقوق محفوظة.
                    </p>
                </div>
            </div>
        </div>

        <!-- Left Side - Image/Pattern -->
        <div class="hidden lg:block relative flex-1 bg-gradient-to-br from-primary-600 via-primary-700 to-primary-900">
            <div class="absolute inset-0 bg-grid-pattern opacity-10"></div>
            <div class="relative h-full flex flex-col justify-center items-center p-12 text-white">
                <div class="max-w-md text-center">
                    <i class="fas fa-school text-8xl mb-8 opacity-90"></i>
                    <h3 class="text-4xl font-bold mb-4">
                        نظام متكامل لإدارة المدرسة
                    </h3>
                    <p class="text-xl text-primary-100 leading-relaxed">
                        منصة حديثة وشاملة لإدارة جميع جوانب العملية التعليمية من مكان واحد
                    </p>
                    
                    <div class="mt-12 grid grid-cols-2 gap-6">
                        <div class="text-center">
                            <div class="text-3xl font-bold mb-2">100%</div>
                            <div class="text-sm text-primary-200">رقمي بالكامل</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold mb-2">24/7</div>
                            <div class="text-sm text-primary-200">متاح دائماً</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold mb-2">آمن</div>
                            <div class="text-sm text-primary-200">حماية البيانات</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold mb-2">سهل</div>
                            <div class="text-sm text-primary-200">واجهة بسيطة</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .bg-grid-pattern {
            background-image: 
                linear-gradient(to right, rgba(255, 255, 255, 0.1) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(255, 255, 255, 0.1) 1px, transparent 1px);
            background-size: 40px 40px;
        }
    </style>
</body>
</html>

