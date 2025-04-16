<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

class Mail {
    private $mail;
    private $to;
    private $subject;
    private $body;

    public function __construct() {
        $this->mail = new PHPMailer(true);
        
        // Server settings
        $this->mail->isSMTP();
        $this->mail->Host = getenv('SMTP_HOST') ?: 'smtp.gmail.com';
        $this->mail->SMTPAuth = true;
        $this->mail->Username = getenv('SMTP_USERNAME') ?: 'your-email@gmail.com';
        $this->mail->Password = getenv('SMTP_PASSWORD') ?: 'your-app-password';
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port = getenv('SMTP_PORT') ?: 587;
        $this->mail->CharSet = 'UTF-8';
        
        // Sender
        $this->mail->setFrom(getenv('SMTP_USERNAME') ?: 'your-email@gmail.com', 'EduAI');
    }

    public function setTo($email) {
        $this->to = $email;
        $this->mail->addAddress($email);
    }

    public function setSubject($subject) {
        $this->subject = $subject;
        $this->mail->Subject = $subject;
    }

    public function setBody($body) {
        $this->body = $body;
        $this->mail->isHTML(true);
        $this->mail->Body = $body;
    }

    public function send() {
        try {
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Mailer Error: " . $this->mail->ErrorInfo);
            return false;
        }
    }
}
?> 