<?php
/* Generated by neoan3-cli */

namespace Neoan3\Components;

use Neoan3\Apps\Db;
use Neoan3\Apps\Session;
use Neoan3\Apps\Stateless;
use Neoan3\Frame\Neoan;
use Neoan3\Model\IndexModel;
use Neoan3\Model\UserModel;
use PHPMailer\PHPMailer\Exception;
use SendGrid;
use SendGrid\Mail\From;
use SendGrid\Mail\To;
use SendGrid\Mail\Mail;

class Verify extends Neoan {
    function init(){
        if(!sub(1) || !isset($_GET['email'])){
            exit();
        }

        $user = IndexModel::first(UserModel::findEmails(['email'=>$_GET['email']]));
        if(empty($user)){
            exit();
        }
        $unconfirmedEmail = [];
        foreach($user['emails'] as $email){
            if($email['email'] == $_GET['email'] && empty($email['confirm_date']) && $email['confirm_code'] == sub(1)){
                $unconfirmedEmail = $email;
            }
        }
        if(!empty($unconfirmedEmail) && $unconfirmedEmail['confirm_code'] == sub(1) ){
            Db::user_email(['confirm_date'=>'.'],['id'=>'$'.$unconfirmedEmail['id']]);
            Session::logout();
            redirect('login');
        } else {
            echo "Invalid url";

        }
        exit();
    }
    function confirmEmail($to, $hash, $userName) {
        require_once path . '/component/sendGridTemplate/SendgridTemplate.ctrl.php';
        $link = base . 'verify/'.$hash.'/?email='.$to;

        $content = [
            'verify_link'=>$link,
            'Sender_Name' => 'Blue.Blue',
        ];
        $to = new To($to, $userName,$content);
        $email = new SendgridTemplate(getenv('SENDGRID_VERIFICATION_TEMPLATE'));
        return $email->send([$to]);
    }


}
