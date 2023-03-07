<?php
$modulePath = dirname(__FILE__);

// ERROR LOG 출력
// error_reporting(E_ALL);
// ini_set( "display_errors", 1 );
// ERROR LOG 출력

//CLASS AUTO LOAD
function classAutoload($className = ""){

    global $modulePath;
    if ($className != "") {
        require_once $modulePath . "/Module" . "/" . $className . ".php";
        //패스 정보 변경
    }
}

spl_autoload_register("classAutoload");
//CLASS AUTO LOAD


$mail = new Mail;
$content = 
<<<EOT
<p>테스트 메일입니다.</p>
EOT;


$mailStatus = $mail->mail("테스트" , "kpjh@ninefive.com" , "테스트 메일 입니다." , $content );
//$mail->mail(메일 보내는 이, 메일을 받을 이메일, 메일 제목, 메일 내용);