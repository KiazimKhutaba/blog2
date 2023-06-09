<?php

namespace MyBlog\Controllers;


use Exception;
use MyBlog\Core\Routing\Annotation\Route;
use MyBlog\Core\Session\SessionInterface;
use MyBlog\Core\Traits\ToJsonStringTrait;
use MyBlog\Core\Validator\Validator;
use MyBlog\Dtos\NewCommentRequestDto;
use MyBlog\Dtos\PostRequestDto;
use MyBlog\Exceptions\ForbiddenException;
use MyBlog\Exceptions\ResourceNotFoundException;
use MyBlog\Middlewares\IsAdmin;
use MyBlog\Middlewares\IsAuthenticated;
use MyBlog\Repositories\CommentsRepository;
use MyBlog\Repositories\PostRepository;
use MyBlog\Repositories\StatCounterRepository;
use MyBlog\Repositories\UserRepository;
use MyBlog\ViewModels\PostViewModel;
use ReflectionException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PostController extends BaseController
{
    use ToJsonStringTrait;

    public function __construct(
        private readonly PostRepository $postRepository,
        private readonly CommentsRepository $commentsRepository,
        private readonly UserRepository $userRepository,
        private readonly SessionInterface $session
    )
    {
    }

    /**
     * Show the post form and process post adding
     *
     * @throws ReflectionException
     * @throws Exception
     */
    #[Route('/post', ['GET', 'POST'], 'post.index', middlewares: [IsAdmin::class])]
    public function index(Request $request): string|Response
    {
        if ($request->isMethod(Request::METHOD_POST) && $request->request->has('postSubmit')) {

            $post = PostRequestDto::from($request->request->all());
            $errors = Validator::validate($post);

            if (0 === count($errors))
            {
                $user_id = $this->session->get('user_id');
                $createdId = $this->postRepository->add(array_merge($post->toArray(), ['user_id' => $user_id]));

                if(0 !== $createdId) {
                    return $this->redirectToRoute('post.show', ['id' => $createdId]);
                }
                else {
                    throw new Exception("Unknown exception");
                }

            } else {
                $vm = new PostViewModel(message: 'Can\'t create post', errors: $errors);
                return $this->render('post/form.html.twig', ['post' => $vm->toArray()]);
            }

        }

        return $this->render('post/form.html.twig');
    }

    /**
     * Shows concrete post
     *
     * @throws Exception
     */
    #[Route('/post/{id}', ['GET'], 'post.show', ['id' => '[\d]+'])]
    public function show(Request $request, StatCounterRepository $counterRepository, int $id): string
    {
        $post = $this->postRepository->get($id);

        if(!$post)
            throw new ResourceNotFoundException();

        $comments = $this->commentsRepository->getComments($id);

        // count views
        $counterRepository->countViews($request, $id);

        $vm = new PostViewModel(post: $post);
        return $this->render($vm->getViewName(), ['post' => $vm->toArray(), 'comments' => $comments]);
    }


    #[Route('/post/{post_id}/comment', ['POST'], 'post_comment.create', ['post_id' => '[\d]+'], [IsAuthenticated::class]),]
    public function addComment(Request $request, int $post_id): string
    {
        $dto = NewCommentRequestDto::from($request->request->all());
        $errors = Validator::validate($dto);

        if (0 === count($errors)) {

            $user_id = $this->session->get('user_id');
            $comment = $this->commentsRepository->createComment($dto, $post_id, $user_id);

            return $this->toJson(['status' => 'ok', 'comment' => $comment]);
        }

        return $this->toJson(['status' => 'error', 'errors' => $errors]);
    }


    // Todo: when error - form reset happens

    /**
     *
     * @throws ReflectionException
     * @throws Exception
     */
    #[Route('/post/{id}/edit', ['GET', 'POST'], 'post.edit', ['id' => '[\d]+'], [IsAdmin::class])]
    public function edit(Request $request, int $id): string|Response
    {
        if ($request->isMethod(Request::METHOD_POST) && $request->request->has('postSubmit'))
        {
            $post = PostRequestDto::from($request->request->all());
            $errors = Validator::validate($post);

            if (0 === count($errors))
            {
                $postUpdated = $this->postRepository->update($id, $post);

                if($postUpdated) {
                    return $this->redirectToRoute('post.show', ['id' => $id]);
                }
                else
                    throw new Exception("Unknown exception");
            }
            else
            {
                $postViewModel = new PostViewModel(true, 'Cant update post', $errors, $post->toArray());
                return $this->render('post/form.html.twig', $postViewModel->toArray());
            }
        }

        $postViewModel = new PostViewModel(true, post: $this->postRepository->get($id));
        return $this->render('post/form.html.twig', ['post' => $postViewModel->toArray()]);

    }


    /**
     * @throws Exception
     */
    #[Route('/post/{id}/remove', ['GET'], 'post.remove', ['id' => '[\d]+'], [IsAdmin::class])]
    public function remove(int $id): Response
    {
        if($this->postRepository->remove($id)) {
            return $this->redirect('/');
        }

        throw new Exception("Can\'t remove post with id $id");
    }

    #[Route('/users/{id}/posts', ['GET'], 'user.posts', ['id' => '[\d]+'])]
    public function getUserPosts(Request $request, int $user_id): string
    {
        $posts = $this->postRepository->getUserPosts($user_id);
        return $this->toJson($posts);
    }

    #[Route('/users/posts', ['GET'], 'users.posts')]
    public function getUsersPosts(): string
    {
        $posts = $this->postRepository->getUsersPosts();
        return $this->toJson($posts);
    }
}