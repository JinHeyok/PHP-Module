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
        //경로 정보 변경
    }
}

spl_autoload_register("classAutoload");

//다중 단일 업로드
    // $fileClass = new File;
    // $fileCount = $_POST["fileCount"];

    // for($i = 0;  $i < $fileCount; $i++){
    //     if(!empty($_FILES["fileList" . $i])){
    //         $test = $fileClass->singleFileUplaod($_FILES["fileList" . $i] , "upload/" , "image");
    //     }; 
    // }

//다중 단일 업로드

//압축 파일 이름으로 업로드
    // $testFileList = $_POST["testFileList"];
    // $testFileList = explode("," , $testFileList);

    // $test = $fileClass->zipUpload($testFileList);
//압축 파일 이름으로 업로드

//압축 다중 업로드
    // $fileListArray = array();
    // for($i = 0; $i < $fileCount; $i++){
    //     if(!empty($_FILES["fileList" . $i])){
    //         array_push($fileListArray , $_FILES['fileList' . $i]);
    //     }
    // }

    // $test = $fileClass->zipUpload($fileListArray , "upload/");
    // return print_r(json_encode($test));

//압추 다중 업로드

?>