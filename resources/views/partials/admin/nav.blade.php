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
