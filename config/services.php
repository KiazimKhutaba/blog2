<?php

use MyBlog\Core\Config;
use MyBlog\Core\Container;
use MyBlog\Core\Db\DatabaseInterface;
use MyBlog\Core\Db\SQLiteDatabase;
use MyBlog\Core\Routing\Router;
use MyBlog\Core\Session\SessionInterface;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;
use function MyBlog\Helpers\env;
use function MyBlog\Helpers\isProd;


return function (Container $container): Container {

    // Database
    $container->set(DatabaseInterface::class, fn() => SQLiteDatabase::connect(Config::PATH_TO_SQLITE_FILE));

    $container->set(SessionInterface::class, fn() => new MyBlog\Core\Session\PhpSession());

    // Router singleton
    $container->set(Router::class, static function() {
        static $instance = null;

        if($instance == null) {
            $instance = new Router();
        }

        return $instance;
    });

    // Twig
    $container->set(Environment::class, function () use ($container) {
        $loader = new FilesystemLoader(sprintf('%s/templates', env('APP_ROOT')));
        $twig = new Environment($loader, [
            'cache' => isProd() ? env('APP_ROOT') . '/files/cache/templates' : false,
        ]);

        $twig->addFunction(new TwigFunction('route',function (string $name, array $values = []) use ($container): string {

            /** @var Router $router */
            $router = $container->get(Router::class);
            return $router->generateUrl($name, $values);
        }));

        $twig->addFunction(new TwigFunction('is_logged', function () use ($container) {
            /** @var SessionInterface $session */
            $session = $container->get(SessionInterface::class);
            return $session->has('user_id');
        }));


        $twig->addFunction(new TwigFunction('is_admin', function () use ($container) {
            /** @var SessionInterface $session */
            $session = $container->get(SessionInterface::class);
            return $session->get('role') === 'admin';
        }));

        return $twig;
    });

    return $container;
};