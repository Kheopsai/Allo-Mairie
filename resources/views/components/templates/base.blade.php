<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="soft-scrollbar">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- <meta name="description" content="{{ $description }}">
    <meta name="keywords" content="{{ $seoKeywords }}"> --}}


    @hasSection('title')
        <title>@yield('title') - {{ config('app.name') }}</title>
    @else
        <title>{{ config('app.name') }}</title>
    @endif

    <link rel="shortcut icon" href="{{ url(global_asset('images/svg/logo.svg')) }}">

    <wireui:scripts/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="antialiased tracking-tight">
    <x-dialog z-index="z-100"/>
    <x-notifications z-index="z-100"/>
    @yield('body')
    @livewire('wire-elements-modal')

</body>

</html>
