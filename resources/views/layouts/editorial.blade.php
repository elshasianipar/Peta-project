<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Lembar — uang, ditulis pelan-pelan.')</title>
    <meta name="description" content="@yield('meta_description', 'Artikel keuangan untuk anak muda Indonesia: anggaran, pasar, rupiah, dan investasi tanpa jargon.')">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="min-h-screen bg-ledger text-press font-sans antialiased">
    @include('editorial.navbar')

    <main>
        @yield('content')
    </main>

    @include('editorial.footer')

    @livewireScripts
    @stack('scripts')
</body>
</html>