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
        'table/{contentID}'                 => 'Ajax\\ContentList',

        //form
        gettext('formulario').'/{contentID}'                    => 'ContentForm',
        gettext('formulario').'/{contentID}/{itemID}'           => 'ContentForm',
        gettext('eliminar-item').'/{contentID}/{itemID}'        => 'Ajax\\DeleteItem',
        gettext('eliminar-archivo').'/{contentID}/{itemID}'     => 'Ajax\\DeleteFile',
        gettext('bloquear').'/{contentID}/{itemID}'             => 'Ajax\\BlockItem',
        gettext('subir-archivo') .'/{contentID}'                => 'Ajax\\Upload',
        gettext('subir-archivo') .'/{contentID}/{itemID}'       => 'Ajax\\Upload',
        gettext('anadir-campo') .'/{contentID}'                 => 'Ajax\\Dynamic\\Add',
        gettext('anadir-campo') .'/{contentID}/{itemID}'        => 'Ajax\\Dynamic\\Add',
        gettext('eliminar-campo') .'/{contentID}/{itemID}'      => 'Ajax\\Dynamic\\Delete',
        gettext('duplicar').'/{contentID}/{itemID}'             => 'Duplicate',

        // push notifications
        gettext('notificaciones-push').'/{contentID}'           => 'Push\\PushList',
        gettext('notificacion-push').'/{contentID}'             => 'Push\\Form',
        gettext('notificacion-push').'/{contentID}/{itemID}'    => 'Push\\Form',
        'push-target/{contentID}'                               => 'Push\\Target',

        // others
        gettext('informacion')              => 'Info',

    )

);