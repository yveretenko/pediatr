<?php

use App\Entity\DateComments;

function getByDateAction()
{
    global $em;

    $comment=$em->getRepository(DateComments::class)->findOneBy(['date' => new DateTime($_POST['date'])])?->getComment();

    die(json_encode([
        'comment' => $comment,
    ]));
}

function saveAction()
{
    global $em;

    $comment_text=$_POST['comment'];
    $date = new DateTime($_POST['date']);

    $comment=$em->getRepository(DateComments::class)->findOneBy(['date' => $date]);

    if (!$comment_text)
    {
        if ($comment)
            $em->remove($comment);
    }
    else
    {
        if (!$comment)
        {
            $comment = new DateComments;
            $comment->setDate($date);
        }

        $comment->setComment($comment_text);

        $em->persist($comment);
    }

    $em->flush();
}