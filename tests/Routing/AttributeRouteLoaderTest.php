<?php

namespace Appacman\Tests\Routing;

use Core\Routing\Loader\AttributeRouteLoader;
use PHPUnit\Framework\TestCase;

class AttributeRouteLoaderTest extends TestCase
{

    public function testDiscoversTheFullExpectedRouteSet(): void
    {
        $collection = (new AttributeRouteLoader())->load(
            'Appacman',
            __DIR__ . '/../../src/Controller/',
            null
        );

        $routes = array();
        foreach ($collection as $route) {
            $routes[] = $route->path . ' -> ' . $route->controllerClass;
        }
        sort($routes);

        $expected = array(
            '/ -> Appacman\Controller\Home',
            '/anadir-campo/{contentID} -> Appacman\Controller\Ajax\Dynamic\Add',
            '/anadir-campo/{contentID}/{itemID} -> Appacman\Controller\Ajax\Dynamic\Add',
            '/bloquear/{contentID}/{itemID} -> Appacman\Controller\Ajax\BlockItem',
            '/cambiar-contrasena/{hash} -> Appacman\Controller\LoggedOut\ChangePassword',
            '/cerrar-sesion -> Appacman\Controller\LoggedOut\LogOut',
            '/duplicar/{contentID}/{itemID} -> Appacman\Controller\Duplicate',
            '/eliminar-archivo/{contentID}/{itemID} -> Appacman\Controller\Ajax\DeleteFile',
            '/eliminar-campo/{contentID}/{itemID} -> Appacman\Controller\Ajax\Dynamic\Delete',
            '/eliminar-item/{contentID}/{itemID} -> Appacman\Controller\Ajax\DeleteItem',
            '/exportar/{contentID} -> Appacman\Controller\Export',
            '/formulario/{contentID} -> Appacman\Controller\ContentForm',
            '/formulario/{contentID}/{itemID} -> Appacman\Controller\ContentForm',
            '/he-olvidado-mi-contrasena -> Appacman\Controller\LoggedOut\Forgot',
            '/informacion -> Appacman\Controller\Info',
            '/iniciar-sesion -> Appacman\Controller\LoggedOut\SignIn',
            '/listado/{contentID} -> Appacman\Controller\ContentList',
            '/log-out/{contentID}/{itemID} -> Appacman\Controller\ForceLogOut',
            '/notificacion-push/{contentID} -> Appacman\Controller\Push\Form',
            '/notificacion-push/{contentID}/{itemID} -> Appacman\Controller\Push\Form',
            '/notificaciones-push/{contentID} -> Appacman\Controller\Push\PushList',
            '/push-table/{contentID} -> Appacman\Controller\Push\AjaxTablePushList',
            '/push-target/{contentID} -> Appacman\Controller\Push\Target',
            '/subir-archivo/{contentID} -> Appacman\Controller\Ajax\Upload',
            '/subir-archivo/{contentID}/{itemID} -> Appacman\Controller\Ajax\Upload',
            '/table/{contentID} -> Appacman\Controller\Ajax\ContentList',
        );
        sort($expected);

        $this->assertSame($expected, $routes);
    }

    /**
     * every one of these routes used to be reachable by any HTTP method under the old
     * routing.php (path => controller, no method concept at all) - the same single action
     * handles both "show the form" (GET) and "process the submission" (POST), e.g.
     * SignIn::run() branches on isset($_POST['enter']). Route's default methods is ['GET']
     * only, so it's easy to silently break every form/AJAX POST in this app by adding a
     * #[Route] attribute without an explicit methods: [...] - this already happened once
     * (login POST 404ing) and got fixed; this test is here so it can't happen again quietly.
     */
    public function testEveryRouteAllowsBothGetAndPost(): void
    {
        $collection = (new AttributeRouteLoader())->load(
            'Appacman',
            __DIR__ . '/../../src/Controller/',
            null
        );

        foreach ($collection as $route) {
            $methods = $route->methods;
            sort($methods);
            $this->assertSame(
                array('GET', 'POST'),
                $methods,
                "{$route->path} -> {$route->controllerClass} must allow both GET and POST"
            );
        }
    }

}
