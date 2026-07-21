@php
    $viteDevServerUrl = rtrim(config('app.vite_dev_server_url', 'http://127.0.0.1:5173'), '/');
@endphp

@if (app()->environment('local'))
    <script type="module" src="{{ $viteDevServerUrl }}/@vite/client"></script>
    <link rel="stylesheet" href="{{ $viteDevServerUrl }}/resources/css/app.css">
    <script type="module" src="{{ $viteDevServerUrl }}/resources/js/app.js"></script>
@else
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@endif