<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Кабінет дитячого лікаря ДітиКвіти, м. Чернівці</title>
    <link href="/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="/img/favicon.ico" type="image/x-icon">
    <link rel="icon" href="/img//favicon.ico" type="image/x-icon">

    <style>
        body
        {
            height:100vh;
            display:flex;
            flex-direction:column;
            justify-content:center;
            align-items:center;
            overflow:hidden;
        }

        i.fas
        {
            position:absolute;
            animation:float 6s infinite ease-in-out;
            opacity:0.5;
        }

        i.fas:nth-child(1) { top:10%; left:20%; animation-duration:5s;}
        i.fas:nth-child(2) { top:30%; left:70%; animation-duration:7s; }
        i.fas:nth-child(3) { top:60%; left:15%; animation-duration:8s; }
        i.fas:nth-child(4) { top:80%; left:60%; animation-duration:6s; }
        i.fas:nth-child(5) { top:50%; left:30%; animation-duration:9s; }

        @keyframes float
        {
            0%
            {
                transform:translateY(0) rotate(0deg);
            }
            50%
            {
                transform:translateY(-20px) rotate(15deg);
            }
            100%
            {
                transform:translateY(0) rotate(0deg);
            }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="text-center">
        <h1 class="text-muted" style="font-size:450%;">404</h1>
        <h4 class="text-muted">Сторінку не знайдено <i class="far fa-frown-open"></i></h4>

        <a class="btn btn-success mt-3" href="{{ url('/') }}"><i class="fa fa-home mr-1"></i> На головну</a>
    </div>

    <div>
        <i class="fas fa-3x text-info fa-stethoscope"></i>
        <i class="fas fa-3x text-warning fa-syringe"></i>
        <i class="fas fa-3x text-success fa-heartbeat"></i>
        <i class="fas fa-3x text-danger fa-notes-medical"></i>
        <i class="fas fa-3x text-primary fa-briefcase-medical"></i>
    </div>
</div>

</body>
</html>
