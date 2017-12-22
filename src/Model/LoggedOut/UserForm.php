<?php

namespace Appacman\Model\LoggedOut;


use Appacman\Model\Business;
use Appacman\Model\User;
use Core\Model\Encryptor\OneWay;
use Core\Model\Encryptor\TwoWay;
use Core\Model\Form;
use Core\Model\Utils\Mail;
use Core\Model\Utils\StringUtils;
use Core\Utils\Config;

class UserForm extends Form {

    private $user = array();

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
                if( $this->checkLogin() ){
                    $this->send = true;
                    $this->key = $this->user['id_appacman_user'] . '_' . $this->user['created'];
                    $username = TwoWay::decrypy($this->user['name'], $this->key.'_name');

                    $user = User::getInstance();
                    $user->signin($this->user['id_appacman_user'], $username);
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
        $found = false;
        if( $this->foundUser() ){
            $found = true;
            if( OneWay::check($this->user['password'], $this->form['password'], $this->key.'_password') ){
                return true;
            }else{
                $this->error = gettext('Contraseña incorrecta.');
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
                if( $this->setupChangePassword() ){
                    $this->send = true;
                }else{
                    $this->error = gettext('Se ha producido un error al enviar el email. Por favor, inténtalo más tarde.');
                }
            }else{
                $this->error = gettext('Comprueba el formato del email.');
            }
        }else{
            $this->error = gettext('Debes llenar todos los campos obligatorios.');
        }
    }

    /**
     * prepare user on database
     * @return bool
     */
    private function setupChangePassword(){
        $this->mysql->beginTransaction();

        if( $this->foundUser() ){
            // set hash
            $key = $this->key .'_changing_password';
            $hash = OneWay::encrypt($this->user['created'].'_'.date('Y-m-d H:i:s'), $key);
            $hash = str_replace('.', '', StringUtils::removeSpecialCharacters($hash));
            $sql = '
                UPDATE appacman_user
                SET changing_password = :hash
                WHERE id_appacman_user = :id
            ';
            $params = array(
                'hash'  => array('value'=>$hash,                        'type'=>\PDO::PARAM_STR),
                'id'    => array('value'=>$this->user['id_appacman_user'],    'type'=>\PDO::PARAM_INT),
            );
            $this->mysql->query($sql, $params);
            if( $this->mysql->rowCount() ){
                // send email
                if( $this->sendMailChangePassword($hash) ){
                    $this->mysql->commit();
                    $this->form = array();
                    return true;
                }else{
                    $this->error = gettext('Se ha producido un error al enviar el email. Por favor, inténtalo más tarde.');
                }
            }else{
                $this->error = gettext('Se ha producido un error al enviar el email. Por favor, inténtalo más tarde.');
            }
        }else{
            $this->error = gettext('No existe ningún usuario con este email.');
        }

        $this->mysql->rollback();
        return false;
    }

    /**
     * send mail to user
     * @param $hash
     * @return bool
     */
    private function sendMailChangePassword($hash){
        $business = new Business();
        $businessInfo = $business->getInfo();

        $config = Config::getInstance();
        $domain = $config->getDomain();
        $mailConfig = $config->get('mail');

        $userName = TwoWay::decrypy($this->user['name'], $this->key.'_name');
        $userEmail = TwoWay::decrypy($this->user['email'], $this->key.'_email');

        $mail = new Mail();
        return $mail->send(
            array('email'=>$mailConfig['username'], 'name'=>$mailConfig['name']),
            array(array('email'=>$userEmail, 'name'=>$userName)),
            $businessInfo['name'] . ' - ' . gettext('Recordar contraseña'),
            '
                <img src="' . $businessInfo['logo'] . '" alt="' . $businessInfo['name'] . '" title="' . $businessInfo['name'] . '" style="max-width: 200px" />   
                <div>
                    <a href="'. $domain . gettext('cambiar-contrasena') .'/' . $hash . '" style="display:inline-block; text-decoration:none; text-transform: uppercase; text-size: 16px; padding:6px 30px; background:#39CCCC; color:#ffffff; border: 1px solid #36a8a9;">' . gettext('Haz click para cambiar tu contraseña') . '</a>
                </div>
            '
        );
    }

    /**
     * found user by email on database
     * @return bool
     */
    private function foundUser(){
        $sql = '
            SELECT id_appacman_user, name, email, password, created
            FROM appacman_user
        ';
        $users = $this->mysql->query($sql);

        foreach($users as $user){
            $this->key = $user['id_appacman_user'] . '_' . $user['created'];
            $decryptedEmail = TwoWay::decrypy($user['email'], $this->key.'_email');
            if( $decryptedEmail == $this->form['user'] ){
                $this->user = $user;
                return true;
            }
        }
        return false;
    }

    /**
     * check if hash is correct
     * @param $hash
     * @return bool
     */
    public function canChange($hash){
        $sql = '
            SELECT id_appacman_user, created
            FROM appacman_user
            WHERE changing_password = :hash
        ';
        $params = array(
            'hash' => array('value'=>$hash, 'type'=>\PDO::PARAM_STR)
        );
        $user = $this->mysql->query($sql, $params);

        if( count($user) ){
            $this->user = $user[0];
            return true;
        }
        return false;
    }

    public function change(){
        $this->form = array(
            'password1' => $_POST['password1'],
            'password2' => $_POST['password2'],
        );

        if( !empty($this->form['password1']) && !empty($this->form['password2']) ){
            if( $this->form['password1'] === $this->form['password2'] ){
                $this->key = $this->user['id_appacman_user'] . '_' . $this->user['created'];
                $password = OneWay::encrypt($this->form['password1'], $this->key .'_password');
                $sql = '
                    UPDATE appacman_user
                    SET password = :password, changing_password = NULL
                    WHERE id_appacman_user = :id
                ';
                $params = array(
                    'password'  => array('value'=>$password,                        'type'=>\PDO::PARAM_STR),
                    'id'        => array('value'=>$this->user['id_appacman_user'],  'type'=>\PDO::PARAM_INT),
                );
                $this->mysql->query($sql, $params);
                if( $this->mysql->rowCount() ){
                    $this->form = array();
                    $this->send = true;
                }else{
                    $this->error = gettext('Se ha producido un error en el servidor. Por favor, inténtalo más tarde.');
                }
            }else{
                $this->error = gettext('Las contraseñas no coinciden.');
            }
        }else{
            $this->error = gettext('Debes llenar todos los campos obligatorios.');
        }
    }

}