<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Кабінет дитячого лікаря ДітиКвіти, м. Чернівці</title>

    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Lato:400,700,400italic,700italic&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker3.standalone.min.css" rel="stylesheet" crossorigin="anonymous"/>

    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/custom.css') }}?nocache={{ filemtime(public_path('css/custom.css')) }}" rel="stylesheet">

    <link rel="shortcut icon" href="{{ asset('img/favicon.ico') }}" type="image/x-icon">

    <meta property="og:title" content="Кабінет дитячого лікаря ДітиКвіти, м. Чернівці"/>
    <meta property="og:description" content="{{ config('business.address') }}"/>
    <meta property="og:image" content="{{ asset('img/logo.png') }}"/>
</head>

<body id="page-top" style="padding-top:100px;">

@include('partials.application.nav')

<main>
    @yield('content')
</main>

@include('partials.application.footer')

@include('partials.application.modals.appointment')
@include('partials.application.modals.contact_success')
@include('partials.application.modals.terms')
@include('partials.application.modals.webinar_nutrition')
@include('partials.application.modals.webinar_newborn')
@include('partials.application.modals.price')
@include('partials.application.modals.pay')

<script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js') }}"></script>
<script src="{{ asset('js/jqBootstrapValidation.js') }}"></script>
<script src="{{ asset('js/freelancer.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.uk.min.js" integrity="sha512-zj4XeRYWp+L81MSZ3vFuy6onVEgypIi1Ntv1YAA6ThjX4fRhEtW7x+ppVnbugFttWDFe/9qBVdeWRdv9betzqQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script src="{{ asset('js/articles.js') }}?nocache={{ filemtime(public_path('js/articles.js')) }}"></script>
<script src="{{ asset('js/contact_me.js') }}?nocache={{ filemtime(public_path('js/contact_me.js')) }}"></script>
<script src="{{ asset('js/modals.js') }}?nocache={{ filemtime(public_path('js/modals.js')) }}"></script>
<script src="{{ asset('js/quick_search.js') }}"></script>

@if($modal ?? false)
    <script>
        $(function(){
            $('#{{ $modal }}').modal('show');
        });
    </script>
@endif

</body>
</html>
