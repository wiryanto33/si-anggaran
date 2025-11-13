<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
    <style>
        /* Background carousel with exactly two slides (absolute, no layout shift) */
        .auth-carousel {
            position: absolute;
            inset: 0;
            overflow: hidden;
            z-index: 0;
        }

        .auth-carousel .carousel-slide {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            display: block;
        }

        .auth-carousel .overlay-mask {
            position: absolute;
            inset: 0;
            pointer-events: none;
        }

        .auth-carousel .slide-1 {
            opacity: 1;
            animation: auth-fade-1 12s infinite ease-in-out;
        }

        .auth-carousel .slide-2 {
            opacity: 0;
            animation: auth-fade-2 12s infinite ease-in-out;
        }

        @keyframes auth-fade-1 {

            0%,
            45% {
                opacity: 1
            }

            50%,
            95% {
                opacity: 0
            }

            100% {
                opacity: 1
            }
        }

        @keyframes auth-fade-2 {

            0%,
            45% {
                opacity: 0
            }

            50%,
            95% {
                opacity: 1
            }

            100% {
                opacity: 0
            }
        }
    </style>
</head>
@php
    $__loginImages = json_decode((string) \App\Models\Setting::get('login_carousel_images', '[]'), true) ?: [];
    // Map to URLs; prefer uploaded images, fallback to defaults
    if (count($__loginImages) === 0) {
        $__slide1 = asset('img/koarmada2.jpg');
        $__slide2 = asset('img/kri.jpg');
    } else {
        $toUrl = function ($p) {
            return asset('storage/' . ltrim((string) $p, '/'));
        };
        $__slide1 = isset($__loginImages[0]) ? $toUrl($__loginImages[0]) : asset('img/koarmada2.jpg');
        $__slide2 = isset($__loginImages[1]) ? $toUrl($__loginImages[1]) : asset('img/kri.jpg');
    }
    $__loginOverlay = (int) \App\Models\Setting::get('login_carousel_overlay', 50);
    $__loginAlpha = max(0, min(0.8, $__loginOverlay / 100));
@endphp

<body class="min-h-screen bg-white antialiased dark:bg-linear-to-b dark:from-neutral-950 dark:to-neutral-900">
    <div
        class="relative grid min-h-screen h-screen flex-col items-center justify-center px-8 sm:px-0 lg:max-w-none lg:grid-cols-2 lg:px-0">
        <div class="bg-muted relative hidden h-full flex-col p-10 text-white lg:flex dark:border-e dark:border-neutral-800"
            style="min-height:100vh">
            <!-- Background carousel - z-index: 0 -->
            <div class="auth-carousel" style="height:100vh">
                <img class="carousel-slide slide-1" src="{{ $__slide1 }}" alt="Slide 1" />
                <img class="carousel-slide slide-2" src="{{ $__slide2 }}" alt="Slide 2" />
                <div class="overlay-mask" style="background-color: rgba(0,0,0, {{ $__loginAlpha }});"></div>
            </div>

            <!-- Logo - z-index: 10 (di atas gambar) -->
            <a href="{{ route('home') }}" class="relative z-10 flex items-center" wire:navigate>
                <span class="flex h-[64px] w-auto items-center justify-center rounded-md">
                    <x-app-logo-icon class="h-[100px] w-auto" />
                </span>
            </a>

            @php
                [$message, $author] = str(Illuminate\Foundation\Inspiring::quotes()->random())->explode('-');
            @endphp

            <!-- Quote - z-index: 10 (di atas gambar) -->
            <div class="relative z-10 mt-auto">
                <blockquote class="space-y-2">
                    <flux:heading size="lg">&ldquo;{{ trim($message) }}&rdquo;</flux:heading>
                    <footer>
                        <flux:heading>{{ trim($author) }}</flux:heading>
                    </footer>
                </blockquote>
            </div>
        </div>
        <div class="w-full lg:p-8">
            <div class="mx-auto flex w-full flex-col justify-center space-y-6 sm:w-[350px]">
                <a href="{{ route('home') }}" class="z-20 flex flex-col items-center gap-2 lg:hidden" wire:navigate>
                    <span class="flex h-[64px] w-auto items-center justify-center rounded-md">
                        <x-app-logo-icon class="h-[64px] w-auto" />
                    </span>
                    <span class="sr-only">{{ config('app.name', 'Laravel') }}</span>
                </a>
                {{ $slot }}
            </div>
        </div>
    </div>
    @fluxScripts
</body>

</html>
