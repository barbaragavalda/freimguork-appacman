<?php

$config = array(

    'routing' => array(
        gettext('iniciar-sesion')               =>  'LoggedOut\\Signin',
        gettext('he-olvidado-mi-contrasena')    =>  'LoggedOut\\Forgot',
        gettext('cerrar-sesion')                =>  'LoggedOut\\LogOut',

        ''                                  => 'Home',
        gettext('listado').'/{contentID}'   => 'ContentList'

    )

);