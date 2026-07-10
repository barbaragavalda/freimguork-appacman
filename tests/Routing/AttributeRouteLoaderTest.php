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

}
