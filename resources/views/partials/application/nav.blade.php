<nav class="bg-white border-bottom fixed-top pt-2" style="height:100px;">
    <div class="container px-1">
        <div class="form-row">
            <div class="col-auto">
                <img src="{{ asset('img/logo.svg') }}" height="80" class="mx-1 mt-0 mt-lg-1">
            </div>

            <div class="col">
                <div class="form-row">
                    <div class="col-12 col-lg-auto pt-0 pt-lg-3">
                        <A class="navbar-brand mr-0 js-scroll-trigger text-decoration-none text-uppercase py-0 py-lg-1" href="#page-top">
                            <span class="text-success">Кабінет</span>
                            <span class="text-warning">дитячого</span>
                            <span class="text-info">лікаря</span>

                            <div style="font-size:60%;" class="text-muted">
                                <b>м. Чернівці, <nobr><?=config('business.address') ?></nobr></b>
                            </div>
                        </A>
                    </div>

                    <div class="col-12 col-lg-auto ml-auto pt-1 pt-lg-4" id="top_menu">
                        <A class="py-1 py-lg-2 px-2 px-lg-3 btn btn-success text-white mr-1" data-toggle="modal" data-target="#price_modal">
                            <i class="fa fa-list"></i>
                            Послуги
                        </A>

                        <A class="py-1 py-lg-2 px-2 px-lg-3 btn btn-success text-white mr-1" data-toggle="modal" data-target="#appointment_modal">
                            <i class="fa fa-pen"></i>
                            Запис <span class="d-none d-md-inline">на прийом</span>
                        </A>

                        <A class="py-1 py-lg-2 px-2 px-lg-3 btn btn-info text-white mr-1" data-toggle="modal" data-target="#pay_modal">
                            <i class="fa fa-laptop"></i>
                            Онлайн<span class="d-none d-sm-inline">-консультація</span>
                        </A>

                        <A class="btn btn-outline-secondary rounded-circle d-inline-flex justify-content-center align-items-center ml-1 ml-xl-3" title="Instagram" href="https://www.instagram.com/dr_svitlana/" style="width:2.5em; aspect-ratio:1/1;">
                            <i class="fab fa-fw fa-instagram" style="font-size:150%;"></i>
                        </A>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>
