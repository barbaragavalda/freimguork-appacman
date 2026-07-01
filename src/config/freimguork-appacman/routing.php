<?php

$config = array(

    'routing' => array(
        _('iniciar-sesion')                 => 'LoggedOut\\SignIn',
        _('he-olvidado-mi-contrasena')      => 'LoggedOut\\Forgot',
        _('cambiar-contrasena') . '/{hash}' => 'LoggedOut\\ChangePassword',
        _('cerrar-sesion')                  => 'LoggedOut\\LogOut',

        ''                                                     => 'Home',

        //list
        _('listado') . '/{contentID}'                    => 'ContentList',
        _('exportar') . '/{contentID}'                   => 'Export',
        'table/{contentID}'                                    => 'Ajax\\ContentList',

        //form
        _('formulario') . '/{contentID}'                 => 'ContentForm',
        _('formulario') . '/{contentID}/{itemID}'        => 'ContentForm',
        _('eliminar-item') . '/{contentID}/{itemID}'     => 'Ajax\\DeleteItem',
        _('eliminar-archivo') . '/{contentID}/{itemID}'  => 'Ajax\\DeleteFile',
        _('bloquear') . '/{contentID}/{itemID}'          => 'Ajax\\BlockItem',
        _('subir-archivo') . '/{contentID}'              => 'Ajax\\Upload',
        _('subir-archivo') . '/{contentID}/{itemID}'     => 'Ajax\\Upload',
        _('anadir-campo') . '/{contentID}'               => 'Ajax\\Dynamic\\Add',
        _('anadir-campo') . '/{contentID}/{itemID}'      => 'Ajax\\Dynamic\\Add',
        _('eliminar-campo') . '/{contentID}/{itemID}'    => 'Ajax\\Dynamic\\Delete',
        _('duplicar') . '/{contentID}/{itemID}'          => 'Duplicate',
        'log-out/{contentID}/{itemID}'                         => 'ForceLogOut',

        // push notifications
        _('notificaciones-push') . '/{contentID}'        => 'Push\\PushList',
        _('notificacion-push') . '/{contentID}'          => 'Push\\Form',
        _('notificacion-push') . '/{contentID}/{itemID}' => 'Push\\Form',
        'push-table/{contentID}'                               => 'Push\\AjaxTablePushList',
        'push-target/{contentID}'                              => 'Push\\Target',

        // others
        _('informacion')                                 => 'Info',

    )

);