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

//다중 단일 업로드
    $fileClass = new File;
    $fileCount = $_POST["fileCount"];

    // for($i = 0;  $i < $fileCount; $i++){
    //     if(!empty($_FILES["fileList" . $i])){
    //         $fileClass->singleFileUplaod($_FILES["fileList" . $i] , "upload/" , "image");
    //     }; 
    // }
//다중 단일 업로드

    $testFileList = $_POST["testFileList"];
    $testFileList = explode("," , $testFileList);
    
    $test = $fileClass->zipUpload($testFileList);

?>