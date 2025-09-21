<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>pediatr.cv.ua admin - @yield('title')</title>

    <link href="/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Lato:400,700,400italic,700italic" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker3.standalone.min.css" integrity="sha512-p4vIrJ1mDmOVghNMM4YsWxm0ELMJ/T0IkdEvrkNHIcgFsSzDi/fV7YxzTzb3mnMvFPawuIyIrHcpxClauEfpQg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="//cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/responsive/1.0.0/css/dataTables.responsive.css">
    <link rel="stylesheet" href="/vendor/jquery-multiselect/css/bootstrap-multiselect.css">

    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/custom.css?nocache=<?=filemtime(public_path().'/css/custom.css') ?>" rel="stylesheet">

    <link rel="shortcut icon" href="/img/favicon.ico" type="image/x-icon">
    <link rel="icon" href="/img//favicon.ico" type="image/x-icon">
</head>
<body>
<nav class="navbar navbar-expand-lg alert-dark p-0">
    <div class="navbar-brand pl-3">
        <A href="/" class="text-decoration-none text-dark">
            pediatr.cv.ua
        </A>
    </div>

    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar_content" aria-controls="navbar_content" aria-expanded="false">
        <span><i class="fas fa-bars fa-lg text-muted"></i></span>
    </button>

    <div class="collapse navbar-collapse" id="navbar_content">
        @auth
        <ul class="navbar-nav">
            <li class="nav-item px-3 px-md-1">
                <A class="nav-link text-dark" href="/admin/appointments/">
                    <i class="fa fa-list mr-1" aria-hidden="true"></i>
                    Записи
                </A>
            </li>
            <li class="nav-item px-3 px-md-1">
                <A class="nav-link text-dark" href="/admin/appointments/graph/">
                    <i class="fa fa-chart-bar mr-1" aria-hidden="true"></i>
                    Статистика
                </A>
            </li>
            <li class="nav-item px-3 px-md-1">
                <A class="nav-link text-dark" href="/admin/appointments/files/">
                    <i class="far fa-file-alt mr-1" aria-hidden="true"></i>
                    Файли
                </A>
            </li>
            <li class="nav-item px-3 px-md-1">
                <A class="nav-link text-dark" href="/admin/vaccines/">
                    <i class="fa fa-vial mr-1" aria-hidden="true"></i>
                    Вакцини
                </A>
            </li>
            <li class="nav-item px-3 px-md-1">
                <A class="nav-link text-dark" href="/admin/dates-disabled/">
                    <i class="far fa-calendar-times mr-1" aria-hidden="true"></i>
                    Закриті дати
                </A>
            </li>
        </ul>

        <ul class="navbar-nav ml-auto p-0">
            <li class="nav-item px-3 px-md-1">
                <A class="nav-link text-dark" href="/admin/index/logout/">
                    <i class="fa fa-sign-out-alt" aria-hidden="true"></i>
                    Вийти
                </A>
            </li>
        </ul>
        @endguest
    </div>
</nav>

<div class="col-12 col-xl-10 container-xl pb-5 px-1 px-md-4">
    <h5 class="ml-2 ml-md-0 my-4"><i class="@yield('title_icon')"></i> @yield('title')</h5>

    @yield('content')
</div>

@stack('scripts')

<script src="/vendor/jquery/jquery.min.js"></script>
<script src="/js/jquery.mark.min.js"></script>
<script src="/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="/vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="/js/jqBootstrapValidation.js"></script>
<script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script src="//cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.js"></script>
<script src="/js/file_upload/vendor/jquery.ui.widget.js"></script>
<script src="/js/file_upload/jquery.fileupload.js"></script>
<script src="/vendor/jquery-multiselect/js/bootstrap-multiselect.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.uk.min.js" integrity="sha512-zj4XeRYWp+L81MSZ3vFuy6onVEgypIi1Ntv1YAA6ThjX4fRhEtW7x+ppVnbugFttWDFe/9qBVdeWRdv9betzqQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</body>
</html>
