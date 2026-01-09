<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Shipping Management') }} - @yield('title')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('scripts')
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen">
        @auth
            <!-- Navigation -->
            <nav class="bg-white border-b border-gray-200">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <div class="flex-shrink-0 flex items-center">
                                <a href="{{ route('merchant.dashboard') }}" class="flex items-center gap-2">
                                    <img src="{{ asset('logo_white_background.png') }}" alt="Logo" class="h-10 w-auto">
                                    <span class="text-xl font-bold text-primary hidden sm:inline">{{ __('nav.brand') }}</span>
                                </a>
                            </div>
                            <div class="hidden gap-8 sm:-my-px sm:ms-10 sm:flex">
                                <a href="{{ route('merchant.dashboard') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('merchant.dashboard') ? 'border-secondary text-primary' : 'border-transparent text-gray-500 hover:text-primary hover:border-secondary' }} text-sm font-medium">
                                    {{ __('nav.dashboard') }}
                                </a>
                                <a href="{{ route('merchant.orders.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('merchant.orders.index') ? 'border-secondary text-primary' : 'border-transparent text-gray-500 hover:text-primary hover:border-secondary' }} text-sm font-medium">
                                    {{ __('nav.my_orders') }}
                                </a>
                                <a href="{{ route('merchant.orders.create') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('merchant.orders.create') ? 'border-secondary text-primary' : 'border-transparent text-gray-500 hover:text-primary hover:border-secondary' }} text-sm font-medium">
                                    {{ __('nav.create_order') }}
                                </a>
                            </div>
                        </div>
                        <div class="hidden sm:flex sm:items-center sm:ms-6 gap-4">
                            <!-- Locale Switcher -->
                            <div class="flex items-center gap-1">
                                <a href="{{ request()->fullUrlWithQuery(['locale' => 'en']) }}"
                                   class="px-2 py-1 text-xs font-medium rounded {{ app()->getLocale() === 'en' ? 'bg-primary text-white' : 'text-gray-500 hover:text-gray-700' }}">
                                    EN
                                </a>
                                <a href="{{ request()->fullUrlWithQuery(['locale' => 'ar']) }}"
                                   class="px-2 py-1 text-xs font-medium rounded {{ app()->getLocale() === 'ar' ? 'bg-primary text-white' : 'text-gray-500 hover:text-gray-700' }}">
                                    AR
                                </a>
                            </div>
                            <div class="ms-3 relative">
                                <span class="text-sm text-gray-700 me-4">{{ auth()->user()->name }}</span>
                                <form method="POST" action="{{ route('logout') }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-sm text-gray-500 hover:text-gray-700">
                                        {{ __('nav.logout') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>
        @endauth

        <!-- Page Content -->
        <main>
            @yield('content')
        </main>
    </div>
</body>
</html>
