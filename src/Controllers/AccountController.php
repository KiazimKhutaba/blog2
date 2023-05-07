<?php

namespace MyBlog\Controllers;

use MyBlog\Core\Session\SessionInterface;
use MyBlog\Core\Traits\ToJsonStringTrait;
use MyBlog\Core\Validator\Validator;
use MyBlog\Dtos\LoginRequestDto;
use MyBlog\Dtos\NewAccountRequestDto;
use MyBlog\Repositories\UserRepository;
use MyBlog\ViewModels\LoginViewModel;
use MyBlog\ViewModels\RegistrationViewModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class AccountController extends BaseController
{
    use ToJsonStringTrait;

    public function __construct
    (
        private readonly UserRepository $userRepository,
        private readonly SessionInterface $session
    )
    {
    }


    public function index(): string|Response
    {
        if($this->isUserLogged()) {
            $user = $this->userRepository->get($this->session->get('user_id'));
        }
        else {
            return $this->redirectToRoute('account.login');
        }

        return $this->render('account/profile.html.twig', ['user' => $this->toJson($user)]);
    }


    public function logout(): Response
    {
        if($this->isUserLogged()) {
            $this->session->clear();
        }

        return $this->redirectToRoute('account.login');
    }

    public function registration(Request $request): string|Response
    {
        // Todo: to middleware
        if($this->isUserLogged())
            return $this->redirectToRoute('account.index');

        $vm = new RegistrationViewModel();


        if($request->isMethod(Request::METHOD_POST) && $request->request->has('accountCreateSubmit'))
        {
            $dto = NewAccountRequestDto::from($request->request->all());
            $errors = Validator::validate($dto);

            if(0 === count($errors))
            {
                $user = $this->userRepository->getByEmail($dto->email);

                if($user)
                {
                    $vm->addError('User with this email already registered');
                    return $this->render($vm->getViewName(), ['req' => $vm->toArray()]);
                }
                else
                {
                    $new_user = NewAccountRequestDto::from([
                        'email' => $dto->email,
                        'password' => password_hash($dto->password, PASSWORD_DEFAULT)
                    ]);

                    $createdUserId = $this->userRepository->create($new_user);

                    if($createdUserId) {

                        // Todo: session
                        $this->session->set('user_id', $createdUserId);
                        $this->session->set('role', 'user');

                        return $this->redirectToRoute('account.index');
                    }
                    else {
                        $vm->addError('Cant create user');
                        return $this->render($vm->getViewName(), ['req' => $vm->toArray()]);
                    }
                }
            }
            else {

                $vm->setErrors($errors);
                return $this->render($vm->getViewName(), ['req' => $vm->toArray()]);
            }
        }


        return $this->render($vm->getViewName());
    }

    public function login(Request $request): string|Response
    {
        // Todo: should be moved to middleware
        if($this->isUserLogged())
            return $this->redirectToRoute('account.index');


        $vm = new LoginViewModel();

        if($request->isMethod(Request::METHOD_POST) && $request->request->has('accountLoginSubmit'))
        {
            $requestDto = LoginRequestDto::from($request->request->all());
            $errors = Validator::validate($requestDto);
            $vm->setErrors($errors);

            if(0 === count($errors))
            {
                $user = $this->userRepository->getByEmail($requestDto->email);

                if($user && password_verify($requestDto->password, $user->password))
                {
                    // Todo: session
                    $this->session->set('user_id', $user->id);
                    $this->session->set('role', $user->role);

                    return $this->redirectToRoute('account.index');
                }
                else {
                    $vm->addError("Wrong email/password combination");
                    return $this->render($vm->getViewName(), ['req' => $vm->toArray()]);
                }
            }
            else {
                return $this->render($vm->getViewName(), ['req' => $vm->toArray()]);
            }
        }


        return $this->render($vm->getViewName());
    }


}