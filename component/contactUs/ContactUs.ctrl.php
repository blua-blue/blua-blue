<?php
/* Generated by neoan3-cli */

namespace Neoan3\Components;

use Neoan3\Apps\Hcapture;
use Neoan3\Apps\Ops;
use Neoan3\Apps\Session;
use Neoan3\Apps\Stateless;
use Neoan3\Core\RouteException;
use Neoan3\Core\Unicore;
use Neoan3\Frame\Neoan;
use PHPMailer\PHPMailer\Exception;

class ContactUs extends Neoan {
    private $uniCore;

    function init() {
        $this->uniCore = new Unicore();
        $this->uniCore->uni('neoan')
                      ->callback($this, 'vue')
                      ->callback($this, 'session')
                      ->hook('main', 'contactUs')
                      ->output();
    }

    /**
     * @param Neoan $uni
     */
    function vue($uni){
        $uni->vueComponent('contactUs');
    }

    /**
     * @param Neoan $uni
     *
     * @throws \Exception
     */
    function session($uni){
        $hash = Ops::randomString(5);
        Session::add_session(['contact_hash'=>$hash]);
        $uni->js .= 'let contactHash = "' . $hash .'";';
    }

    function postContactUs($info) {
       /* if(!isset($_SESSION['contact_hash']) || !isset($info['contactHash']) || $info['contactHash'] != $_SESSION['contact_hash'] || !Hcapture::isHuman($info)){
            throw new RouteException('unauthorized', 401);
        }*/
        $mail = new Email('Contact form: '. $info['topic'],'From: '.$info['email'],$info['body']);
        try {
            $mail->mailer->addAddress($mail->mailer->From);
            $mail->mailer->send();
        } catch (Exception $e) {
            return ['success'=>false,'error'=>$e->getMessage()];
        }
        return ['success'=>true];
    }

}
