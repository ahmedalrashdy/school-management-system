@props([
    'appName' => config('app.name', 'Laravel'),
    'pageTitle' => '',
    'sidebar' => null,
])

<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    dir="rtl"
    x-init="$store.theme.init()"
    :class="$store.theme.themeMode === 'dark' ? 'dark' : $el.classList.remove('dark')"
>

<head>
    <script>
        function initTheme() {
            let theme = localStorage.getItem('theme') || 'system';
            let systemDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

            let mode = theme === 'dark' ?
                'dark' :
                theme === 'light' ?
                'light' :
                systemDark ? 'dark' : 'light';

            document.documentElement.classList.add(mode === 'dark' ? 'dark' : '');
        };
    </script>
    <meta charset="utf-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >
    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <title>{{ "$pageTitle - $appName" }}</title>

    <!-- Fonts -->
    <link
        rel="preconnect"
        href="https://fonts.bunny.net"
    >
    <link
        href="https://fonts.bunny.net/css?family=tajawal:400,500,700&display=swap"
        rel="stylesheet"
    />

    <!-- Font Awesome -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous"
        referrerpolicy="no-referrer"
    />

    @livewireStyles()
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>

<body class="font-sans antialiased">
    <script>
        initTheme();
    </script>
    <div class="h-screen bg-gray-50 dark:bg-gray-900 transition-colors duration-200">
        <div class="flex h-screen overflow-hidden">
            <!-- Sidebar -->
            @if ($sidebar)
                {{ $sidebar }}
            @endif

            <!-- Main Content -->
            <div class="flex-1 flex flex-col overflow-hidden">
                <!-- Navbar -->
                <x-ui.navbar :title="$pageTitle ?? ''" />
                <!-- Page Content -->
                <main class="flex-1 overflow-y-auto ">
                    <!-- Breadcrumbs Slot -->
                    @isset($breadcrumbs)
                        {{ $breadcrumbs }}
                    @endisset

                    <!-- Main Content -->
                    <div class="p-4 sm:p-6 lg:p-8">
                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>
    </div>
    <x-ui.sonner-toaster />



    @stack('scripts')

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('theme', {
                theme: 'light',
                themeMode: 'light',
                init() {
                    this.theme = localStorage.getItem('theme') || 'system';
                    this.setThemeMode(this.theme);
                    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
                        if (this.theme === 'system') {
                            this.themeMode = window.matchMedia('(prefers-color-scheme: dark)')
                                .matches ? 'dark' : 'light';
                        }
                    });
                    console.log(this.themeMode);
                },
                setTheme(value) {
                    this.theme = value;
                    localStorage.setItem('theme', value);
                    this.setThemeMode(value);
                },
                setThemeMode(value) {
                    this.themeMode = (value === 'system' && window.matchMedia(
                            '(prefers-color-scheme: dark)').matches) ?
                        'dark' : value === 'dark' ? 'dark' : 'light';
                }
            });
        });
    </script>
    @livewireScripts()
</body>

</html>
