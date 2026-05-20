<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'FibreTrace'))</title>

    <!-- Google Material Symbols & Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Filled:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="@yield('body-classes', 'bg-surface text-on-surface font-body-md min-h-screen flex flex-col selection:bg-secondary-container selection:text-primary relative')">
    
    @yield('background')
    
    @yield('header')

    <!-- Support both Blade component slots (Breeze) and traditional yields (Custom UI) -->
    @if(isset($slot) && $slot->isNotEmpty())
        @include('layouts.navigation')
        
        <!-- Page Heading -->
        @isset($header)
            <header class="bg-white shadow z-20 relative">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset
        
        <main class="flex-1 w-full relative z-10">
            {{ $slot }}
        </main>
    @else
        <main class="flex-1 w-full relative z-10 flex flex-col">
            @yield('content')
        </main>
    @endif

    @stack('scripts')
</body>
</html>
