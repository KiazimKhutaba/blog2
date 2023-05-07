<?php

namespace MyBlog\Controllers;


use Exception;
use MyBlog\Core\Traits\ToJsonStringTrait;
use MyBlog\Core\Validator\Validator;
use MyBlog\Dtos\PostRequestDto;
use MyBlog\Exceptions\ResourceNotFoundException;
use MyBlog\Repositories\PostRepository;
use MyBlog\ViewModels\PostViewModel;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PostController extends BaseController
{
    use ToJsonStringTrait;

    public function __construct(
        private readonly PostRepository $postRepository
    )
    {
    }

    /**
     * Show the post form and process post adding
     *
     * @throws \ReflectionException
     * @throws Exception
     */
    public function index(Request $request): string|Response
    {
        if ($request->isMethod(Request::METHOD_POST) && $request->request->has('postSubmit')) {

            $post = PostRequestDto::from($request->request->all());
            $errors = Validator::validate($post);

            if (0 === count($errors)) {
                $createdId = $this->postRepository->create($post);

                if(0 !== $createdId) {
                    return $this->redirect('/post/' . $createdId);
                }
                else {
                    throw new Exception("Unknown exception");
                }

            } else {
                $vm = new PostViewModel(message: 'Cant create post', errors: $errors);
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
    public function show(Request $request, int $post_id): string
    {
        $post = $this->postRepository->get($post_id);

        if(!$post)
            throw new ResourceNotFoundException();

        $vm = new PostViewModel(post: $post);
        return $this->render('post/show.html.twig', ['post' => $vm->toArray()]);
    }


    // Todo: when error - form reset happens

    /**
     * @throws \ReflectionException
     * @throws Exception
     */
    public function edit(Request $request, int $id): string|Response
    {
        if ($request->isMethod(Request::METHOD_POST) && $request->request->has('postSubmit')) {

            $post = PostRequestDto::from($request->request->all());
            $errors = Validator::validate($post);

            if (0 === count($errors)) {
                $postUpdated = $this->postRepository->update($id, $post);

                if($postUpdated) {
                    return $this->redirect('/post/' . $id);
                }
                else
                    throw new Exception("Unknown exception");
            }
            else {
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
    public function remove(Request $request, int $post_id): Response
    {
        if($this->postRepository->remove($post_id)) {
            return $this->redirect('/');
        }

        throw new Exception("Can\'t remove post with id $post_id");
    }

    public function getUserPosts(Request $request, int $user_id): string
    {
        $posts = $this->postRepository->getUserPosts($user_id);
        return $this->toJson($posts);
    }

    public function getUsersPosts(): string
    {
        $posts = $this->postRepository->getUsersPosts();
        return $this->toJson($posts);
    }
}