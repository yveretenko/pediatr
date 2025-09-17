<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Кабінет дитячого лікаря ДітиКвіти, м. Чернівці</title>

    <link href="/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Lato:400,700,400italic,700italic" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker3.standalone.min.css" integrity="sha512-p4vIrJ1mDmOVghNMM4YsWxm0ELMJ/T0IkdEvrkNHIcgFsSzDi/fV7YxzTzb3mnMvFPawuIyIrHcpxClauEfpQg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/custom.css?nocache=<?=filemtime(public_path().'/css/custom.css') ?>" rel="stylesheet">

    <link rel="shortcut icon" href="/img/favicon.ico" type="image/x-icon">
    <link rel="icon" href="/img//favicon.ico" type="image/x-icon">

    <meta property="og:title" content="Кабінет дитячого лікаря ДітиКвіти, м. Чернівці" />
    <meta property="og:description" content="<?=config('business.address') ?>" />
    <meta property="og:image" content="/img/logo.png" />
</head>

<body id="page-top" style="padding-top:100px;">

<nav class="bg-white border-bottom fixed-top pt-2" style="height:100px;">
    <div class="container px-1">
        <div class="form-row">
            <div class="col-auto">
                <IMG src="img/logo.svg" height="80" class="mx-1 mt-0 mt-lg-1">
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

@yield('content')

<section class="py-3 text-center alert alert-secondary mb-0 mt-4 rounded-0 small" style="text-wrap:balance;">
    <div class="container px-0">
        <div>
            Ліцензія видана наказом Міністерства охорони здоров'я України&nbsp;<u><A target="_blank" title="Витяг відомостей з Ліцензійного реєстру МОЗ з медичної практики" class="text-dark" href="/Витяг відомостей з Ліцензійного реєстру МОЗ з медичної практики.pdf">№1949&nbsp;(від 21.08.2020)</A></u>. ЄДРПОУ&nbsp;3371807241
        </div>

        <div>
            <u><A href="#" data-toggle="modal" data-target="#terms" class="text-dark">Контактна інформація</A></u>
            <u><A href="#" data-toggle="modal" data-target="#terms" class="text-dark mx-2">Правила та умови</A></u>
            <u><A href="#" data-toggle="modal" data-target="#terms" class="text-dark">Правила повернення грошових коштів</A></u>
        </div>
    </div>
</section>

<form name="sentMessage" id="contactForm" novalidate="novalidate">
    <div class="modal fade" id="appointment_modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title font-weight-light text-muted"><i class="fas fa-notes-medical"></i> Запис на прийом</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="control-group">
                        <div class="form-group">
                            <label class="mb-0" for="name">Ім'я та прізвище дитини</label>
                            <input class="form-control" id="name" type="text" required="required" data-validation-required-message="Будь ласка введіть ім'я та прізвище дитини">
                            <p class="help-block text-danger small"></p>
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="form-group">
                            <label class="mb-0" for="phone">Вік дитини</label>
                            <input class="form-control" id="age" type="text">
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="form-group">
                            <label class="mb-0" for="phone">Контактний телефон</label>
                            <input class="form-control" id="phone" type="tel" required="required" data-validation-required-message="Будь ласка введіть телефон">
                            <p class="help-block text-danger small"></p>
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="form-group">
                            <label class="mb-0" for="message">Коротко опишіть причину звернення</label>
                            <textarea class="form-control" id="message" rows="4" required="required" data-validation-required-message="Будь ласка опишіть причину звернення"></textarea>
                            <p class="help-block text-danger small"></p>
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="form-group mb-0">
                            <label class="mb-0" for="date">Оберіть бажану дату</label>
                            <input class="form-control bg-white" type="text" autocomplete="off" readonly id="date" data-date-start-date="<?=date('G')>16 ? date('d.m.Y', strtotime('tomorrow')) : date('d.m.Y') ?>" data-date-end-date="<?=date('d.m.Y', strtotime('last day of next month')) ?>">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <i class="fa fa-spinner fa-spin text-muted fa-lg d-none"></i>

                    <button type="submit" class="btn btn-success btn-xl" id="sendMessageButton">Відправити</button>
                </div>
            </div>
        </div>
    </div>
</form>

<div class="modal" id="contact_success_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title font-weight-light text-muted"><i class="fa fa-clipboard-check"></i> Дякуємо!</h3>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-success my-0 py-2 text-center">
                    Ми зв'яжемося з Вами для підтвердження запису.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрити</button>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="terms" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title font-weight-light text-muted">Правила та умови</h3>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h4>Варіанти оплати</h4>

                <p>
                    Оплата платіжними картами Visa та MasterCard за допомогою сервісу WayForPay.
                </p>

                <h4>Правила повернення грошових коштів</h4>

                <h5 class="mt-3">Медіаматеріали (відео, вебінари, прямі трансляції тощо)</h5>

                <p>
                    Посилання на медіаматеріали доставляються на електронну пошту покупця.
                    <br>
                    Повернення коштів можливе у випадках, коли посилання на медіа неможливо доставити або подія (вебінар, пряма трансляція тощо) не відбулася.
                </p>

                <h5>Онлайн-консультації</h5>

                <p>
                    У разі, якщо консультація не відбулася з будь-яких причин, клієнт має право на повернення коштів.
                    <br>
                    Клієнт також має право на повернення коштів до початку консультації за умови подання відповідного запиту.
                </p>

                <h4>Контактна інформація</h4>

                Фізична особа-підприємець Веретенко Світлана Степанівна
                <br>
                ЄДРПОУ 3371807241
                <br>
                Ліцензія видана наказом Міністерства охорони здоров'я України&nbsp;<u><A target="_blank" title="Витяг відомостей з Ліцензійного реєстру МОЗ з медичної практики" class="text-dark" href="/Витяг відомостей з Ліцензійного реєстру МОЗ з медичної практики.pdf">№1949&nbsp;(від 21.08.2020)</A></u>
                <br>
                Юр. адреса: 58005, м. Чернівці, вул. Небесної Сотні, 4
                <br>
                <u><A href="mailto:veretenkosvitlana@gmail.com" class="text-dark">veretenkosvitlana@gmail.com</A></u>
                <br>
                тел: <u><A href="tel:<?=config('business.tel') ?>" class="text-dark"><?=preg_replace('/(\d{3})(\d{3})(\d{1})(\d{3})/', '$1 $2 $3 $4', config('business.tel')) ?></A></u>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрити</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="nutrition_webinar_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="my-0">Вебінар про прикорм</h4>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <p>
                    Теми, які розкриваються на вебінарі:
                </p>

                <div class="alert alert-info small">
                    <ul class="pl-0 my-0">
                        <li>У якому віці краще починати пропонувати дитині прикорм і як зрозуміти що дитина готова їсти іншу їжу, окрім грудного молока чи молочної суміші
                        <li>Який стільчик для годування обрати?
                        <li>Шматочки чи пюре? Переваги та недоліки різних видів прикорму
                        <li>Які продукти можна пропонувати? Яка порція має бути у різному віці?
                        <li>А якщо дитина вдавиться - що робити?
                        <li>А якщо буде алергія?
                        <li>Які можливі реакції? Як уникнути залізодефіцитної анемії? Що таке алергічний проктоколіт?
                        <li>Напої, спеції, солодощі - коли можна?
                        <li>Що таке чуйне годування?
                        <li>Як на початку прикорму не наробити помилок та сформувати здорову харчову поведінку, гарний апетит у дитини?
                    </ul>
                </div>

                <p>
                    <i class="fa fa-fw fa-laptop text-muted mr-1"></i> Запис вебінару буде відправлено на вашу пошту
                </p>

                <p>
                    <i class="far fa-fw fa-clock text-muted mr-1"></i> Тривалість вебінару – 1 година 15 хвилин
                </p>

                <p>
                    <i class="fa fa-fw fa-credit-card text-muted mr-1"></i> Запис можна придбати за 490 грн
                </p>

                <A href="https://secure.wayforpay.com/button/bcdd0830a34fc" class="btn btn-block btn-success">Придбати запис вебінару</A>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="newborn_webinar_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="my-0">Вебінар "Дитина народилася"</h4>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <p>
                    Теми, які розкриваються на вебінарі:
                </p>

                <div class="alert alert-info small">
                    <ul class="pl-0 my-0">
                        <li>Як може виглядати дитина після народження? Що таке фізіологічні стани - де межа між нормою та патологією?
                        <li>Що таке жовтяниця новонароджених?
                        <li>Які маніпуляції в пологовому вам запропонують і чи варто їх робити?
                        <li>Післяпологовий блюз та післяпологова депресія - що це, як розпізнати та як подбати про свій емоційний стан?
                        <li>Що варто зробити для успішного старту грудного вигодовування?
                        <li>Дієта мами, яка годує грудьми - яка вона? І чи можна мамі приймати ліки, якщо раптом вона захворіє?
                        <li>Догляд: як піклуватися про шкіру, пупковий залишок, нігті та волосся?
                        <li>Коли та як купати?
                        <li>Яку дитячу косметику обрати?
                        <li>Які в нормі мають бути випорожнення малюка?
                        <li>Які особливості сну новонароджених?
                        <li>Чому дитина плаче? Як навчитися розпізнавати потреби малюка?
                        <li>Аптечка для новонароджених - які ліки потрібні?
                        <li>Які обстеження та огляди лікарів потрібні в перший рік життя?
                    </ul>
                </div>

                <p>
                    <i class="fa fa-fw fa-laptop text-muted mr-1"></i> Запис вебінару буде відправлено на вашу пошту
                </p>

                <p>
                    <i class="far fa-fw fa-clock text-muted mr-1"></i> Тривалість вебінару – 1 година 45 хвилин
                </p>

                <p>
                    <i class="fa fa-fw fa-credit-card text-muted mr-1"></i> Запис можна придбати за 600 грн
                </p>

                <A href="https://secure.wayforpay.com/button/b6fa52ce3c223" class="btn btn-block btn-success">Придбати запис вебінару</A>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="price_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title font-weight-light text-muted"><i class="fa fa-notes-medical"></i> Послуги</h3>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <h5>Консультація</h5>

                <ul class="list-group my-3">
                    @foreach($services as $name => $price)
                    <li class="list-group-item d-flex justify-content-between">
                        <span>{{ $name }}</span>
                        <span class="text-nowrap pl-3">{{ $price }} грн</span>
                    </li>
                    @endforeach
                </ul>

                <h5>Вакцинація</h5>

                <p class="small text-muted">
                    У вартість послуги входить огляд лікаря, послуга вакцинації.
                    <br>
                    Уточнюйте наявність вакцин перед записом.
                </p>

                <ul class="list-group mt-3">
                    @foreach($vaccines as $vaccine)
                    <li class="list-group-item d-flex justify-content-between">
                        <span>{{ $vaccine->name }}</span>
                        <span class="text-nowrap">{{ $vaccine->price }} грн</span>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="pay_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title font-weight-light text-muted"><i class="fa fa-laptop"></i> Послуги онлайн</h3>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <h5>Онлайн-рекомендації</h5>

                <ul class="list-group my-3">
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Онлайн-рекомендації педіатра</span>
                        <span class="text-nowrap pl-3">
                            700 грн
                            <A href="https://secure.wayforpay.com/button/b8ce6fcfb45c8" class="btn btn-sm btn-success ml-1">придбати</A>
                        </span>
                    </li>

                    <li class="list-group-item d-flex justify-content-between">
                        <span>Онлайн-рекомендації дитячого невролога</span>
                        <span class="text-nowrap pl-3">
                            900 грн
                            <A href="https://secure.wayforpay.com/button/b102c10ac6830" class="btn btn-sm btn-success ml-1">придбати</A>
                        </span>
                    </li>

                    <li class="list-group-item d-flex justify-content-between">
                        <span>Онлайн-рекомендації з грудного вигодовування</span>
                        <span class="text-nowrap pl-3">
                            800 грн
                            <A href="https://secure.wayforpay.com/button/bb0d4bf755171" class="btn btn-sm btn-success ml-1">придбати</A>
                        </span>
                    </li>

                    <li class="list-group-item d-flex justify-content-between">
                        <span>Онлайн-рекомендації з дитячого сну</span>
                        <span class="text-nowrap pl-3">
                            980 грн
                            <A href="https://secure.wayforpay.com/button/ba56a7de2dc91" class="btn btn-sm btn-success ml-1">придбати</A>
                        </span>
                    </li>
                </ul>

                <h5>Супровід</h5>

                <ul class="list-group my-3">
                    <li class="list-group-item d-flex justify-content-between">
                        <span>
                            Онлайн-супровід впродовж місяця

                            <br>

                            <span class="small">
                                передбачає формат "питання-відповідь" в листуванні в Телеграмі (текстом або голосовими повідомленнями)
                            </span>
                        </span>
                        <span class="text-nowrap pl-3">
                            3000 грн
                            <A href="https://secure.wayforpay.com/button/bfbf89432f01e" class="btn btn-sm btn-success ml-1">придбати</A>
                        </span>
                    </li>
                </ul>

                <h5>Вебінари</h5>

                <ul class="list-group my-3">
                    <li class="list-group-item d-flex justify-content-between">
                        <span>
                            Дитина народилася

                            <br>

                            <span class="small">
                                маніпуляції в пологовому, післяпологовий стан, жовтяниця, догляд за немовлям, сон, гігієна, грудне вигодовування, аптечка, огляди
                                <br>
                                <i class="far fa-clock"></i> 1 год 45 хв
                            </span>
                        </span>
                        <span class="text-nowrap pl-3">
                            600 грн
                            <A href="#" data-toggle="modal" data-dismiss="modal" data-target="#newborn_webinar_modal" class="btn btn-sm btn-success ml-1">придбати</A>
                        </span>
                    </li>

                    <li class="list-group-item d-flex justify-content-between">
                        <span>
                            Вебінар про прикорм

                            <br>

                            <span class="small" style="line-height:15px !important;">
                                коли і з чого починати, пюре чи шматочки, безпека та алергія, розмір порцій, напої, спеції, солодощі, апетит, харчові звички
                                <br>
                                <i class="far fa-clock"></i> 1 год 15 хв
                            </span>
                        </span>
                        <span class="text-nowrap pl-3">
                            490 грн
                            <A href="#" data-toggle="modal" data-dismiss="modal" data-target="#nutrition_webinar_modal" class="btn btn-sm btn-success ml-1">придбати</A>
                        </span>
                    </li>
                </ul>

                <p class="text-muted small">
                    Оплата здійснюється на рахунок ФОП платіжними картами Visa та MasterCard за допомогою сервісу WayForPay
                </p>
            </div>
        </div>
    </div>
</div>

<script src="/vendor/jquery/jquery.min.js"></script>
<script src="/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="/vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="/js/jqBootstrapValidation.js"></script>
<script src="/js/freelancer.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.uk.min.js" integrity="sha512-zj4XeRYWp+L81MSZ3vFuy6onVEgypIi1Ntv1YAA6ThjX4fRhEtW7x+ppVnbugFttWDFe/9qBVdeWRdv9betzqQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script src="/js/articles.js?nocache=<?=filemtime(public_path().'/js/articles.js') ?>"></script>
<script src="/js/contact_me.js?nocache=<?=filemtime(public_path().'/js/contact_me.js') ?>"></script>
<script src="/js/modals.js?nocache=<?=filemtime(public_path().'/js/modals.js') ?>"></script>
<script src="/js/quick_search.js"></script>

@if($modal)
    <script>
        $(function(){
            $('#{{ $modal }}').modal('show');
        });
    </script>
@endif

</body>
</html>
