<?php

use App\Entity\Articles;

function getAction()
{
    global $em;

    /** @var Articles $article */
    $article=$em->getRepository(Articles::class)->find($_POST['id']);

    if (!$article)
    {
        http_response_code(404);
        die;
    }

    die(json_encode([
        'id'    => $article->getId(),
        'title' => $article->getTitle(),
        'text'  => $article->getText(),
    ]));
}