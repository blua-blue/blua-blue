<?php
namespace Neoan3\Components;

use SendGrid;
use SendGrid\Mail\EmailAddress;
use SendGrid\Mail\From;
use SendGrid\Mail\Mail;
use SendGrid\Mail\To;

class SendgridTemplate
{
    private $templateId;
    public $instance;
    public function __construct($templateId)
    {
        $this->instance = new SendGrid(getenv('SENDGRID_API_KEY'));
        $this->templateId = $templateId;
    }
    public function sendCustom(array $tos, string $subject, string $content)
    {
        $from = new From(getenv('SENDGRID_FROM_EMAIL'), getenv('SENDGRID_FROM_NAME'));
        $email = new Mail($from, $tos);
        $email->setSubject($subject);
        $email->addContent("text/html", $content);
        try {
            $response = $this->instance->send($email);
            return ['success'=>$response->statusCode()];
        } catch (\Exception $e) {
            return ['success'=>false,'error'=>$e->getMessage()];
        }
    }
    public function send(array $tos ): array
    {
        $from = new From(getenv('SENDGRID_FROM_EMAIL'), getenv('SENDGRID_FROM_NAME'));
        $email = new Mail($from, $tos);
        $email->setTemplateId($this->templateId);
        try {
            $response = $this->instance->send($email);
            return ['success'=>$response->statusCode()];
        } catch (\Exception $e) {
            return ['success'=>false,'error'=>$e->getMessage()];
        }
    }
}