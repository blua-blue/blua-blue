<?php


namespace Neoan3\Components;


use Neoan3\Apps\Stateless;
use Neoan3\Frame\Neoan;
use Neoan3\Model\WebhookModel;

/**
 * Class Webhook
 *
 * @package Neoan3\Components
 */
class Webhooks extends Neoan
{
    /**
     * @return mixed
     * @throws \Neoan3\Core\RouteException
     */
    function getWebhooks(){
        $userId = Stateless::restrict();
        return WebhookModel::find(['user_id'=>$userId['jti'],'delete_date'=>'']);
    }

    /**
     * @param $webHook
     *
     * @return mixed
     * @throws \Neoan3\Core\RouteException
     */
    function postWebhooks($webHook){
        $jwt = Stateless::restrict();
        $webHook['user_id'] = $jwt['jti'];
        return WebhookModel::create($webHook);
    }

    /**
     * @param $webHook
     *
     * @return mixed
     * @throws \Neoan3\Core\RouteException
     */
    function deleteWebhooks($webHook){
        $jwt = Stateless::restrict();
        $where = [
            'user_id' => $jwt['jti'],
            'id' => $webHook['id']
        ];
        return WebhookModel::delete($where);
    }

    /**
     * @param $webHook
     *
     * @return mixed
     * @throws \Neoan3\Core\RouteException
     */
    function putWebhooks($webHook){
        Stateless::restrict();
        return WebhookModel::update($webHook);
    }
}
