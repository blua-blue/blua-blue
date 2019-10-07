<?php
/* Generated by neoan3-cli */

namespace Neoan3\Frame;

use Neoan3\Apps\Db;
use Neoan3\Apps\Cache;
use Neoan3\Apps\DbException;
use Neoan3\Apps\Ops;
use Neoan3\Apps\Session;
use Neoan3\Apps\SimpleTracker;
use Neoan3\Apps\Stateless;
use Neoan3\Core\Serve;
use PHPMailer\PHPMailer\PHPMailer;

class Neoan extends Serve {
    private   $credentials     = [];
    private   $developmentMode = true;
    protected $currentAuth     = false;

    function __construct() {
        // Hybrid: construct session
        new Session();

        // tracking
        $identifier = Session::is_logged_in() ? Session::user_id() : substr(session_id(), 0, 7);
        SimpleTracker::init(dirname(path) . '/blua-blue-data/');
        SimpleTracker::track($identifier);

        if(!$this->developmentMode && !Session::is_logged_in()) {
            Cache::setCaching('+2 hours');
        } else {
            Cache::invalidateAll();
        }
        if($this->developmentMode){
            $this->includeJs(base . 'node_modules/vue/dist/vue.min.js');
        } else {
            $this->includeJs(base . 'node_modules/vue/dist/vue.js');
        }
        // SETUP
        /*
         * Sharing projects oe.g via GitHub? Hide credentials and place them OUTSIDE of your server's web-root.
         * Here we are storing credentials in a JSON file.
         * ['blua_db'=>
         *  ['name'=>'your_database','assumes_uuid'=>true,'password'=>'Password','user'=>'dbUser'],
         * 'blua_stateless'=>['secret'=>'SecretKey']
         * 'blua_mail'=>
         *  ['host'=>'yourSMPThost','username'=>'yourSMTPlogin','password'=>'smtp_password'],
         * ]
         *
         * THE FOLLOWING LINE MIGHT HAVE TO BE ADJUSTED
         * */
        $credentialFile =  '/credentials/credentials.json';
        if(file_exists($credentialFile)) {
            $this->credentials = json_decode(file_get_contents($credentialFile), true);

        } else {
            print('SETUP: No credentials found. Please check README for instructions and/or change '.__FILE__.' starting at line '.(__LINE__-4).' ');
            die();
        }


        // database
        $this->setUpDb();

        // JWT/Stateless auth
        Stateless::setSecret($this->credentials['blua_stateless']['secret']);


        parent::__construct();

        $this->vueComponent('cookieLaw');
        $this->vueComponent('header');
        $this->hook('header', 'header');
        $this->hook('footer', 'footer');

    }

    function vueComponent($element, $params = []) {
        $params['base'] = base;
        $path = path . '/component/' . $element . '/' . $element . '.ce.';
        if(file_exists($path . $this->viewExt)) {
            $this->footer .= '<template id="' . $element . '">' . $this->fileContent($path . $this->viewExt, $params) .
                             '</template>';
        }
        if(file_exists($path . $this->styleExt)) {
            $this->style .= $this->fileContent($path . $this->styleExt, $params);
        }
        if(file_exists($path . 'js')) {
            $this->js .= $this->fileContent($path . 'js', $params);
        }

        return $this;
    }

    function restrict($scope) {
        $this->currentAuth = Stateless::restrict($scope);
        return $this;
    }

    function output($params = []) {
        $this->js .= 'new Vue({el:"#root"});';
        $this->main = '<div id="root" class="main">' . $this->header . $this->main . '</div>';
        $this->header = '';
        parent::output($params);
        if(!$this->developmentMode) {
            Cache::write();
        }
    }

    function newMail() {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = $this->credentials['blua_mail']['host'];
        $mail->SMTPAuth = true;
        $mail->Username = $this->credentials['blua_mail']['username'];
        $mail->Password = $this->credentials['blua_mail']['password'];
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;
        return $mail;
    }

    private function setUpDb() {
        try {
            Db::setEnvironment($this->credentials['blua_db']);
        } catch(DbException $e) {
            echo "Warning: Database connection failed.";
        }
    }


    function constants() {
        return [
            'base'       => [base],
            'link'       => [
                [
                    'sizes' => '32x32',
                    'type'  => 'image/png',
                    'rel'   => 'icon',
                    'href'  => 'asset/img/blua-blue-icon-32x32.png'
                ]
            ],
            'meta'       => [
                ['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1']
            ],
            'js'         => [
                ['src' => path .'/frame/neoan/global.js', 'data' => ['base' => base]],
                ['src' => base . 'asset/tinymce/js/tinymce/tinymce.min.js'],
                ['src' => base . 'node_modules/axios/dist/axios.min.js'],
                ['src' => base . 'node_modules/lodash/lodash.min.js'],
                ['src' => base . 'node_modules/moment/min/moment.min.js'],
                ['src' => path . '/frame/neoan/axios-wrapper.js', 'data' => ['base' => base]],
            ],
            'stylesheet' => [
                '' . base . 'frame/neoan/main.css',
                'https://fonts.googleapis.com/icon?family=Material+Icons'
            ]
        ];
    }
}
