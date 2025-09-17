<?php

namespace App\Http\Controllers\Application;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\DateDisabled;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function index(Request $request)
    {
        $articles=Article::orderBy('id', 'desc')->get();

        $reviews=[
            'Вікторія' => 'Зверталися з дуже різними проблемами зі здоров’ям дитини. Щоразу нам надавали якісну допомогу без зайвих ліків і, при цьому, з ефективним лікуванням',
            'Наталя' => 'Відмінний лікар, професіонал, приємна у спілкуванні, завжди відповідає на усі запитання, не робить непотрібних призначень. Моя абсолютна довіра, спокій і впевненість.',
            'Марія' => 'Пані Світлана чудовий лікар. Вона завжди коментує все що робить, пояснює «що», «чому», «для чого» і «навіщо». Завжди усміхнена, привітна та на позитиві',
            'Юлія' => 'Прекрасний лікар, яка не тільки є прихильником доказової медицини, але і чудово оглядає маленького пацієнта. Задоволена візитами, оглядом та поясненнями',
            'Галя' => 'Напевно єдиний лікар в Чернівцях, який дотримується принципів доказової медицини. Дуже уважно оглядає дітей, завжди детально відповідає на всі запитання',
            'Марина' => 'Мене хвилювало багато запитань, щодо здоров\'я дитини, на які я отримала всі відповіді і навіть більше. Чуйна, приємна, та обізнана, одним словом – прекрасний лікар',
        ];

        $tel_formatted=preg_replace('/(\d{3})(\d{3})(\d{1})(\d{3})/', '$1 $2 $3 $4', config('business.tel'));

        $close_dates=[];
        foreach (DateDisabled::all() as $date_disabled)
            $close_dates[]=$date_disabled->date->format('d.m.Y');

        $close_dates_json=json_encode($close_dates);

        return view('application/index/index', [
            'articles'         => $articles,
            'reviews'          => $reviews,
            'close_dates_json' => $close_dates_json,
            'address'          => config('business.address'),
            'tel'              => config('business.tel'),
            'tel_formatted'    => $tel_formatted,
            'modal'            => $request->route('modal'),
        ]);
    }
}
