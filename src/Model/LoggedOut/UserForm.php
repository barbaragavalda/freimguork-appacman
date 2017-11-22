<?php

namespace Appacman\Model\LoggedOut;


use Appacman\Model\User;
use Core\Model\Encryptor\OneWay;
use Core\Model\Encryptor\TwoWay;
use Core\Model\Form;

class UserForm extends Form {

    /**
     * login into appacman
     */
    public function signin(){
        $this->form = array(
            'user' => $_POST['user'],
            'password' => $_POST['password']
        );

        if( !empty($this->form['user']) && !empty($this->form['password']) ){
            if( filter_var($this->form['user'], FILTER_VALIDATE_EMAIL) ){
                if( ($userInfo = $this->checkLogin()) !== false ){
                    $this->send = true;
                    $key = $userInfo['id_appacman_user'] . '_' . $userInfo['created'];
                    $username = TwoWay::decrypy($userInfo['name'], $key.'_name');

                    $user = User::getInstance();
                    $user->signin($userInfo['id_appacman_user'], $username);
                }
            }else{
                $this->error = gettext('Comprueba el formato del email.');
            }
        }else{
            $this->error = gettext('Debes llenar todos los campos obligatorios.');
        }
    }

    /**
     * check user in database for login
     * @return bool
     */
    private function checkLogin(){
        $sql = '
            SELECT id_appacman_user, name, email, password, created
            FROM appacman_user
        ';
        $users = $this->mysql->query($sql);

        $found = false;
        foreach($users as $user){
            $key = $user['id_appacman_user'] . '_' . $user['created'];
            $decryptedEmail = TwoWay::decrypy($user['email'], $key.'_email');
            if( $decryptedEmail == $this->form['user'] ){
                $found = true;

                if( OneWay::check($user['password'], $this->form['password'], $key.'_password') ){
                    return $user;
                }else{
                    $this->error = gettext('Contraseña incorrecta.');
                }
                break;
            }
        }

        if( !$found ) $this->error = gettext('No existe ningún usuario con este email.');
        return false;
    }

    public function remember(){
        $this->form = array(
            'user' => $_POST['user']
        );

        if( !empty($this->form['user']) ){
            if( filter_var($this->form['user'], FILTER_VALIDATE_EMAIL) ){

            }else{
                $this->error = gettext('Comprueba el formato del email.');
            }
        }else{
            $this->error = gettext('Debes llenar todos los campos obligatorios.');
        }
    }

    private function sendMailChangePassword(){

    }

}