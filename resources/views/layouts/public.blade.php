<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ isset($title) ? $title.' - ' : '' }}{{ config('app.name', 'Wedspiration') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600|playfair-display:500,600&display=swap" rel="stylesheet"/>

    <!-- Tailwind CSS & Alpine.js via CDN -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.15.1/dist/cdn.min.js"></script>
    <style type="text/tailwindcss">
        @theme {
            --font-sans: 'Figtree', ui-sans-serif, system-ui, sans-serif;
            --font-serif: 'Playfair Display', ui-serif, Georgia, serif;
        }
    </style>
</head>
<body class="font-sans antialiased text-stone-800">
<div class="min-h-screen flex flex-col bg-stone-50">
    @include('layouts.public_navigation')

    <!-- Success message after a form was saved -->
    @if (session('success'))
        <div class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 pt-6">
            <div class="rounded-md bg-green-50 p-4 text-sm text-green-800 ring-1 ring-green-200">
                {{ session('success') }}
            </div>
        </div>
    @endif

    <!-- Page Content -->
    <main class="flex-1">
        {{ $slot }}
    </main>

    @include('partials.lightbox')

    <x-script src="js/masonry.js" />
    <x-script src="js/infinite-scroll.js" />
    <x-script src="js/lightbox.js" />

    @auth
        <!-- Appears while a photo is dragged: drop it here to save it into your folder -->
        <div data-drop-zone hidden data-over="false"
             class="group fixed inset-x-0 bottom-0 z-50 p-4 pb-[calc(1rem+env(safe-area-inset-bottom))]">
            <div class="mx-auto max-w-3xl rounded-xl border-2 border-dashed border-rose-700 bg-white/95 px-6 py-6 text-center shadow-lg transition
                        group-data-[over=true]:bg-rose-50 group-data-[over=true]:scale-[1.02]">
                <p data-drop-message class="font-medium text-stone-900">
                    Drop here to save to {{ Auth::user()->folders()->oldest('id')->value('name') ?? 'your folder' }}
                </p>
            </div>
        </div>

        <x-script src="js/save-photos.js" />
    @endauth

    <footer class="border-t border-stone-200 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 text-sm text-stone-500 flex flex-col sm:flex-row gap-2 justify-between">
            <p>&copy; {{ date('Y') }} Wedspiration</p>
            <div class="flex gap-4">
                <a href="{{ route('about') }}" class="hover:text-stone-800">About</a>
                <a href="{{ route('contact') }}" class="hover:text-stone-800">Contact</a>
            </div>
        </div>
    </footer>
</div>
</body>
</html>
