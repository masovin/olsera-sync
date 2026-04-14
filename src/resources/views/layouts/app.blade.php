<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Sync Manager') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

        <!-- Tailwind CSS -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                darkMode: 'class',
                theme: {
                    extend: {
                        fontFamily: {
                            sans: ['Inter', 'sans-serif'],
                        },
                        colors: {
                            brand: {
                                blue: '#0EA5E9',
                                purple: '#A855F7',
                                dark: '#0F172A',
                            }
                        }
                    }
                }
            }
        </script>

        @livewireStyles
        <style>
            [x-cloak] { display: none !important; }
            .glass {
                background: rgba(255, 255, 255, 0.05);
                backdrop-filter: blur(12px);
                border: 1px solid rgba(255, 255, 255, 0.1);
            }
        </style>
    </head>
    <body class="font-sans antialiased bg-brand-dark text-slate-200 min-h-screen">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            {{ $slot }}
        </div>

        @livewireScripts
    </body>
</html>
