<?php

//사용시 경로지정
require_once "./lib/PHPMailer/PHPMailer.php";
require_once "./lib/PHPMailer/SMTP.php";
require_once "./lib/PHPMailer/Exception.php";

//namespace
use PHPmailer\PHPMailer\PHPmailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Mail {
	const SMTP_PORT_DEFAULT = 25;
	const SMTP_PORT_SMTPS = 465; //ssl
	const SMTP_PORT_STARTTLS = 587; //tls
	
	/* CONFIGURE */
	const SMTP_HOST = "smtp.naver.com";
	const SMTP_USER = "네이버 아이디 입력"; 
	const SMTP_ENCRYPT_PASSWORD = "네이버 비밀번호 입력"; 
	const SMTP_PORT = self::SMTP_PORT_SMTPS;

	static function mail(string $sender = "", string|array $receiverList = [], string $title = "", string $content = "") {
		$mail = new PHPMailer;

		try {
			$mail->isSMTP();
            $mail->Host = self::SMTP_HOST;
            $mail->Username = self::SMTP_USER;
            $mail->Password = base64_decode(self::SMTP_ENCRYPT_PASSWORD);
            $mail->SMTPAuth = true;
            $mail->CharSet = PHPMailer::CHARSET_UTF8;
            $mail->Port = self::SMTP_PORT;
			$mail->SMTPSecure = PHPmailer::ENCRYPTION_SMTPS;
			
			// $mail->SMTPDebug = SMTP::DEBUG_SERVER;	//메일 전송시 기본 내용
			// $mail->SMTPDebug = SMTP::DEBUG_CONNECTION; //메일 전송시 세부 내용
			
			if ($sender == "") {
				$sender = self::SMTP_USER;
			}
            $mail->setFrom(self::SMTP_USER , $sender);
			if (is_array($receiverList)) {
				foreach ($receiverList as $receiver) {
					$mail->addAddress($receiver);
				}
			} else {
				$mail->addAddress($receiverList);
			}
            $mail->isHTML(true);
            $mail->Subject = $title;
            $mail->Body = $content;
			
            $mail->send();

            if (isset($mail->ErrorInfo) && $mail->ErrorInfo != "") {
				return false;
            }
		} catch (Exception $e) {
			return $e;
		}

		return true;
	}
}