<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DateComment;
use App\Models\DateDisabled;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Http\Request;

class DateCommentsController extends Controller
{
    public function save(Request $request)
    {
        $comment_text=$request->input('comment');
        $date=Carbon::parse($request->input('date'))->startOfDay();

        $comment=DateComment::where('date', $date)->first();

        if (!$comment_text)
        {
            if ($comment)
                $comment->delete();
        }
        else
        {
            if (!$comment)
            {
                $comment = new DateComment;
                $comment->date=$date;
            }

            $comment->comment=$comment_text;

            $comment->save();
        }

        return response()->json(['status' => 'ok']);
    }
}
