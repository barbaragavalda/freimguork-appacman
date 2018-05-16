<?php

$config = array(

    'routing' => array(
        gettext('iniciar-sesion')                   =>  'LoggedOut\\SignIn',
        gettext('he-olvidado-mi-contrasena')        =>  'LoggedOut\\Forgot',
        gettext('cambiar-contrasena') .'/{hash}'    =>  'LoggedOut\\ChangePassword',
        gettext('cerrar-sesion')                    =>  'LoggedOut\\LogOut',

        ''                                  => 'Home',

        //list
        gettext('listado').'/{contentID}'   => 'ContentList',
        gettext('exportar').'/{contentID}'  => 'Export',

        //form
        gettext('formulario').'/{contentID}'                    => 'ContentForm',
        gettext('formulario').'/{contentID}/{itemID}'           => 'ContentForm',
        gettext('eliminar-item').'/{contentID}/{itemID}'        => 'Ajax\\DeleteItem',
        gettext('eliminar-archivo').'/{contentID}/{itemID}'     => 'Ajax\\DeleteFile',
        gettext('bloquear').'/{contentID}/{itemID}'             => 'Ajax\\BlockItem',
        gettext('subir-archivo') .'/{contentID}/{itemID}'       => 'Ajax\\Upload',
        gettext('duplicar').'/{contentID}/{itemID}'             => 'Duplicate',

        // push notifications
        gettext('notificaciones-push').'/{contentID}'               => 'PushNotifications',
        gettext('notificaciones-push').'/{contentID}/{itemID}'      => 'PushNotifications',

        // others
        gettext('informacion')              => 'Info',

    )

);