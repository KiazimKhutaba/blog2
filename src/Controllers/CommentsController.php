<?php

namespace MyBlog\Controllers;

use MyBlog\Core\Routing\Annotation\Route;
use MyBlog\Dtos\NewCommentRequestDto;
use MyBlog\Core\Traits\ToJsonStringTrait;
use MyBlog\Exceptions\ForbiddenException;
use MyBlog\Exceptions\ResourceNotFoundException;
use MyBlog\Middlewares\IsAuthenticated;
use MyBlog\Repositories\CommentsRepository;
use MyBlog\Repositories\PostRepository;
use Symfony\Component\HttpFoundation\Request;

class CommentsController extends BaseController
{
    use ToJsonStringTrait;

    public function __construct
    (
        private readonly CommentsRepository $commentsRepository
    )
    {
    }


    public function create(Request $request): string
    {
        $dto = NewCommentRequestDto::from($request->request->all());
        return $this->toJson($request->request->all());
        //return $this->commentsRepository->create($commentDto->toArray());
    }


    /**
     * @throws ForbiddenException
     * @throws ResourceNotFoundException
     */
    #[Route(
        '/comment/{comment_id}/remove',
        ['DELETE'],
        'comment.remove',
        ['comment_id' => '[\d]+'],
        [IsAuthenticated::class]
        //, IsAuthorized::class => ['object_author_id']
    )]
    public function remove(Request $request, int $comment_id)
    {
        $comment = $this->commentsRepository->get($comment_id);

        if($comment) {
            $this->authorize($comment['user_id']); // authorize comment author
            return $this->commentsRepository->remove($comment_id);
        }

        throw new ResourceNotFoundException();
    }

    #[Route('/post/{id}/comments', ['GET'], 'comments.get', ['id' => '[\d]+'])]
    public function getAll(Request $request, int $post_id): string
    {
        $comments = $this->commentsRepository->getComments($post_id);
        return $this->toJson($comments);
    }
}