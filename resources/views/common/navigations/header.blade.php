@php
    $currentPlatform = session('platform', 'perfectlum');
    $bodyThemeClass = $currentPlatform === 'perfectlum' 
        ? 'bg-[#F9FAFB] text-gray-900 theme-lum' 
        : 'bg-[#0A0A0C] text-[#E2E1E6] theme-chroma';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'PerfectLum' }} | PerfectLum</title>
    <!-- Tailwind & Lucide -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak] { display: none !important; }</style>
</head>

<body x-cloak x-data="adminApp()" 
      @resize.window="isMobile = window.innerWidth < 1024; if(isMobile) sidebarCollapsed = true"
      class="h-screen w-screen overflow-hidden flex transition-colors duration-500 font-sans {{ $bodyThemeClass }}"
      :class="theme === 'perfectlum' ? 'bg-[#F9FAFB] text-gray-900 theme-lum' : 'bg-[#0A0A0C] text-[#E2E1E6] theme-chroma'">

    @include('admin.partials.sidebar')

    <main class="flex-1 flex flex-col h-full overflow-hidden transition-colors duration-500 relative">
        @include('admin.partials.header')

        {{-- SCROLL AREA FOR PAGE CONTENT --}}
        <div class="flex-1 overflow-y-auto px-6 lg:px-12 pb-16 pt-8">
            <div class="max-w-[1600px] mx-auto w-full h-full">
