<?php

$config = array(

    'routing' => array(
        gettext('iniciar-sesion')               =>  'LoggedOut\\Signin',
        gettext('he-olvidado-mi-contrasena')    =>  'LoggedOut\\Forgot',
        gettext('cerrar-sesion')                =>  'LoggedOut\\LogOut',

        ''                                  => 'Home',

        //list
        gettext('listado').'/{contentID}'   => 'ContentList',

        //form
        gettext('formulario').'/{contentID}'                    => 'ContentForm',
        gettext('formulario').'/{contentID}/{itemID}'           => 'ContentForm',
        gettext('eliminar-archivo').'/{contentID}/{itemID}'     => 'Ajax\\DeleteFile',

    )

);