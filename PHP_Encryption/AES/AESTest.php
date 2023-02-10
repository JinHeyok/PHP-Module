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

//dk암호화 
$aesClass = new AES;

$array = array("test", "test2"); //배열
$obj = new stdClass; //객체 key,value 둘다 암호화
$name = "test"; //문장 

$obj->test = "test1";
$obj->test2 = "test2";
$obj->test3 = "test3";

$text = $obj;

$encode = $aesClass->AESEncode($text);// 암호화 
$decode = $aesClass->AESDecode($encode); // 복호화

echo "암호화 : ";
print_r($encode);

echo "<br>";

echo "복호화 : ";
print_r($decode);

//암호화 테스트



