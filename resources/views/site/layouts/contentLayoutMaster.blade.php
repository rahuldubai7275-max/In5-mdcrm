@isset($pageConfigs)
{!! Helper::updatePageConfig($pageConfigs) !!}
@endisset

<!DOCTYPE html>
<html lang="en" data-textdirection="ltr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title')</title>
    <!--<link rel="shortcut icon" type="image/x-icon" href="/images/vuexy-logo.png">-->

    {{-- Include core + vendor Styles --}}
    @include('site.panels/styles')

</head>

@php
    $configData = Helper::applClasses();
@endphp

<body >

<!-- BEGIN: Content-->
<div class="wrapper">

    {{-- Include Navbar --}}
    @include('site.panels.header')


    {{-- Include Breadcrumb
    @if($configData['pageHeader'] === true && isset($configData['pageHeader']))
        @include('site.panels.breadcrumb')
    @endif--}}


    {{-- Include Page Content --}}
    @yield('content')


</div>

{{-- include footer --}}
@include('site.panels/footer')

{{-- include default scripts --}}
@include('site.panels/scripts')

</body>

</html>
