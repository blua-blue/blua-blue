<?php


namespace Neoan3\Frame;


use Dotenv\Dotenv;
use Neoan3\Apps\Db;
use Neoan3\Apps\DbException;
use Neoan3\Apps\Hcapture;
use Neoan3\Apps\Session;
use Neoan3\Apps\SimpleTracker;
use Neoan3\Apps\Stateless;

class Setup
{
    static function db($dbCredentials)
    {
        try {
            Db::setEnvironment($dbCredentials);
        } catch (DbException $e) {
            echo "Warning: Database connection failed.";
        }
    }

    static function tracker()
    {
        $identifier = Session::isLoggedIn() ? Session::userId() : substr(session_id(), 0, 7);
        SimpleTracker::init(dirname(path) . '/blua-blue-data/');
        SimpleTracker::track($identifier);
    }

    static function hCaptcha($hCaptchaCredentials)
    {
        Hcapture::setEnvironment($hCaptchaCredentials);
    }

    static function stateless($credentials)
    {
        Stateless::setSecret($credentials);
    }

    static function envSetup()
    {
        $dotenv = Dotenv::createImmutable(path);
        $dotenv->load();
        return [
            'blua_stateless' => [
                'secret' => getenv('STATELESS_SECRET')
            ],
            'blua_hcaptcha' => [
                'secret' => getenv('HCAPTCHA_SECRET'),
                'siteKey' => getenv('HCAPTCHA_SITEKEY'),
                'apiKey' => getenv('HCAPTCHA_APIKEY')
            ],
            'blua_db' => [
                'name' => getenv('DB_NAME'),
                'assumes_uuid' => getenv('DB_ASSUMES_UUID'),
                'password' => getenv('DB_PASSWORD'),
                'user' => getenv('DB_USER'),
                'host' => getenv('DB_HOST')
            ],
            'blua_mail' => [
                'port' => getenv('MAIL_PORT'),
                'secure' => getenv('MAIL_SECURITY'),
                'fromEmail' => getenv('MAIL_SENDER'),
                'password' => getenv('MAIL_PASSWORD'),
                'username' => getenv('MAIL_USER'),
                'host' => getenv('MAIL_HOST')
            ]
        ];

    }
}