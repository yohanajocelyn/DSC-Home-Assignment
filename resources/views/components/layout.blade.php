<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Favicon -->
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/favicon/apple-touch-icon.png') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon/favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon/favicon-16x16.png') }}">
        <link rel="manifest" href="{{ asset('images/favicon/site.webmanifest') }}">
        <link rel="shortcut icon" href="{{ asset('images/favicon/favicon.ico') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800,900" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-primary-50 text-primary-900 selection:bg-primary-300 selection:text-primary-900">
        <div class="min-h-screen flex flex-col relative overflow-hidden">
            <!-- Optional subtle background decoration -->
            <div class="absolute inset-0 z-0 pointer-events-none opacity-50 bg-[radial-gradient(ellipse_at_top,_var(--color-primary-200),_transparent_70%)]"></div>
            
            <main class="flex-1 w-full flex flex-col z-10">
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
