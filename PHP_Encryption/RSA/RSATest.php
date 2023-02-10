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


//RSA 암호화 ssl인증서 이슈....

$rsaClass = new RSA;

$text = "RSA 암호화 될 문자";

$encode = $rsaClass->RSAencode($text);

print_r($encode);


