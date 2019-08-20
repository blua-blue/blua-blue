<?php
/* Generated by neoan3-cli */

namespace Neoan3\Components;

use Neoan3\Apps\Db;
use Neoan3\Apps\DbException;
use Neoan3\Apps\Ops;
use Neoan3\Apps\Session;
use Neoan3\Apps\Stateless;
use Neoan3\Core\RouteException;
use Neoan3\Core\Unicore;
use Neoan3\Frame\Neoan;
use Neoan3\Model\UserModel;

class Register extends Unicore
{
    private $vueElements = ['register'];

    function __construct()
    {
        new Neoan();
    }

    function init()
    {

        $this->uni('neoan')
             ->callback($this, 'vueComponents')
             ->hook('main', 'register')
             ->output();
    }

    /**
     * @param Neoan $uni
     */
    function vueComponents($uni)
    {
        foreach ($this->vueElements as $vueElement) {
            $uni->vueComponent($vueElement);
        }

    }

    /**
     * @return array
     * @throws DbException
     * @throws RouteException
     */
    function getRegister()
    {
        $jwt = Stateless::validate();
        $hasSession = Session::is_logged_in();
        return ['user' => UserModel::byId($jwt['jti']), 'phpSession' => $hasSession];

    }

    /**
     * @param $credentials
     *
     * @return array
     * @throws DbException
     * @throws RouteException
     */
    function postRegister($credentials)
    {
        // check uniqueness
        $userNameExists = UserModel::find(['user_name' => trim($credentials['username'])]);
        $emailExists = UserModel::find(['user_name' => trim($credentials['email'])]);
        if (!empty($userNameExists) || !empty($emailExists)) {
            throw new RouteException('Duplicate entry', 400);
        }
        $newUser = UserModel::register(trim($credentials['email']), $credentials['password'], true);
        if (!isset($newUser['model']) || empty($newUser['model'])) {
            throw new RouteException('Unresolved error', 500);
        }
        Db::ask('user', [
            'user_name' => trim($credentials['username'])
        ], ['id' => '$' . $newUser['model']['id']]);
        $verify = new Verify();
        $verify->confirmEmail(trim($credentials['email']), $newUser['confirm_code']);
        $jwt = Stateless::assign($newUser['model']['id'], 'user', ['exp' => time() + (2 * 60 * 60)]);
        return ['token' => $jwt];
    }

}
