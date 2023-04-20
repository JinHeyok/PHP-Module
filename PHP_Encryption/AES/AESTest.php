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

$text = "test";
$stringEncode  = $aesClass->AESEncode($text);
$stringDecode = $aesClass->AESDecode($stringEncode);

echo "String" . "<br/>";
echo "암호화 : ";
print_r($stringEncode);

echo "<br/>";

echo "복호화 : ";
print_r($stringDecode);

echo "<br/>";

$array = array("test", "test2"); //배열

$arrayEncode = $aesClass->AESEncode($array);
$arrayDecode = $aesClass->AESDecode($arrayEncode);

echo "Array " . "<br/>";
echo "암호화 : ";
print_r($arrayEncode);
echo "<br/>";
echo "복호화 : ";
print_r($arrayDecode);

echo "<br/>";

$obj = new stdClass; //객체 key,value 둘다 암호화

$obj->test = "test1";
$obj->test2 = "test2";
$obj->test3 = "test3";

$objEncode = $aesClass->AESEncode($obj);// 암호화 
$objDecode = $aesClass->AESDecode($objEncode); // 복호화

echo "Class" . "<br/>";
echo "암호화 : ";
print_r($encode);

echo "<br>";

echo "복호화 : ";
print_r($decode);

//암호화 테스트



