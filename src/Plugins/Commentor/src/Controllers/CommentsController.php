<?php

namespace MyBlog\Plugins\Commentor\Controllers;

use Exception;
use MyBlog\Controllers\BaseController;
use MyBlog\Core\Traits\ToJsonStringTrait;
use MyBlog\Core\Validator\Validator;
use MyBlog\Plugins\Commentor\src\Dtos\NewCommentRequestDto;
use MyBlog\Plugins\Commentor\src\Repositories\CommentsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class CommentsController extends BaseController
{
    use ToJsonStringTrait;

    public function __construct
    (
        private readonly CommentsRepository $commentsRepository
    )
    {
    }

    /**
     * @throws Exception
     */
    public function create(Request $request): string|Response
    {
        $comment = NewCommentRequestDto::from($request->request->all());
        $errors = Validator::validate($comment);

        if(0 === count($errors))
        {
            $commentCreated = $this->commentsRepository->create($comment);
            if($commentCreated) {
                return $this->toJson($comment->toArray());
            }
            throw new \Exception("Can't create comment");
        }

        return $this->render('comments/list.html.twig');
    }
}