<?php

namespace App\Http\Controllers\Application;

use App\Models\Article;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function get(Request $request)
    {
        $id=$request->input('id');
        $article=Article::find($id);

        if (!$article)
            return response()->json(['error' => 'Not Found'], 404);

        return response()->json([
            'id'       => $article->id,
            'title'    => $article->title,
            'text'     => $article->text,
            'is_video' => $article->isVideo(),
        ]);
    }
}
