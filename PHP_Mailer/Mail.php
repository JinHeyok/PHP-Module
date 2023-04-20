<?php

//사용시 경로지정
require_once "./lib/PHPMailer/PHPMailer.php";
require_once "./lib/PHPMailer/SMTP.php";
require_once "./lib/PHPMailer/Exception.php";

//namespace
use PHPmailer\PHPMailer\PHPmailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/** CONFIGURE 
* @param SMTP_PORT_DEFAULT 기본 포트 
* @param SMPTP_PORT_SMTPS SSL용 포트
* @param SMPTP_PROT_STARTTLS TLS/STARTTLS 포트

* @param SMTP_HOST HOST 서버 
* @param SMTP_USER 보내는 Eamil 아이디 입력
* @param SMTP_ENCRYPT_PASSWORD 보내는 Email 비밀번호 입력 (base64_encode()) 

* @param isSMTP SMTP를 사용하여 메일을 보내도록 설정
* @param HOST email을 보낼 때 사용할 서버를 지정
* @param Port email을 보낼 때 사용할 포트를 지정 
* @param Username 보내는 email의 아이디
* @param Password 보내는 email의 비밀번호 
* @param SMTPAuth SMTP 인증 사용여부 
* @param CharSet 인코딩 설정 (한글 깨짐 방지)
* @param setFrom 보내는 사람의 이메일 , 표시될 이름 설정 가능
* @param addAddresss 받는 사람의 이메일 , 표시될 이름 설정 가능
* @param isHTML HTML 메일형식 사용
* @param Subject 메일의 제목을 입력
* @param Body 메읿 본문 내용 입력
* @param send 메일 전송
* @param ErrorInfo 메일 에러
*/

class Mail {
	const SMTP_PORT_DEFAULT = 25;
	const SMTP_PORT_SMTPS = 465; //ssl
	const SMTP_PORT_STARTTLS = 587; //tls
	

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