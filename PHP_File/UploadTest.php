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


?>
<!-- form 형시 -->

<form action="" method="POST" enctype="multipart/form-data">
    <input type="file" name="myfile"><!-- 단일업로드 -->
    <input type="file" name="multifile[]" multiple> <!-- 다중업로드 name=[] 배열표시 -->
    <input type="submit" name="action" value="upload">
</form>

<?php

    $uploadSubmit = $_POST['action'];
    $file = $_FILES['myfile']; //파일 namㄷ 값
    $multiFile = $_FILES['multifile'];
    
    echo "<br>";
    print_r("파일 이름 : " . $file['name']); //파일 이름 
    echo "<br>";
    print_r("파일 크기 : " . $file['size'] . "KB");  //파일 크기 
    echo "<br>";
    print_r("파일 타입 : " . $file['type']); //파일 타입
    echo "<br>";
    print_r("경로 : " . $file['full_path']); //파일 경로 
    echo "<br>";
    print_r("tmp : " . $file['tmp_name']); // tmp폴더에 임의의 이름으로 저장되고 사용이 끝나면 삭제된다. 
    echo "<br> 파일 정보 : ";
    print_r($file);

    $uploaddir = "upload/";

    move_uploaded_file($file['tmp_name'] , $uploaddir . $file['name']);
    //단일 파일 업로드 
    //임시 저장된 파일을 해당경로 이름으로 업로드한다.
    
    //다중업로드 
    foreach($multiFile['tmp_name'] as $key => $data){
        move_uploaded_file($data , $uploaddir . $multiFile['name'][$key]);
    }
    //다중업로드


    //압축파일 업로드 
    
    //압축파일 생성
    $zip = new ZipArchive;
    $zipname = "test";

    // if($zip->open($uploaddir . $zipname . ".zip" , ZipArchive::CREATE) == true){//압축파일 생성 
    //     print_r($uploaddir . $zipname . ".zip");
    //     //파일 추가하기 
    //     $zip->addFile($uploaddir . "샘플.pdf" , "샘플.pdf");
    //     //원본파일 , 압축파일내 저장할 파일 이름 
    //     $zip->addFile($uploaddir . "샘플2.pdf", "샘플2.pdf");
    //     //원본파일 , 압축파일내 저장할 파일 이름 

    //     //폴더 추가하기 원본파일 , 폴더명/폴더안에 저장할 파일 이름
    //     $zip->addFile($uploaddir . '샘플.pdf', '폴더명/'. "샘플.pdf");
        
    //     $zip->close(); //압축파일 닫기 
    // }else{
    //     print_r("압축파일 생성 에러");
    // }
    //압축파일 생성

    //압축파일 해제
        //압축파일 오픈 , 해당 폴더가 있는지 확인
    if($zip->open($uploaddir . $zipname . ".zip") == true && file_exists('clear/') == true){
        //해당 디렉토리에 압축파일을 해체한다.
        $zip->extractTo($uploaddir . '/clear');
        $zip->close();
    }
    //압축파일 해제

    //압축파일 다운로드
    $file_name = $uploaddir.$zipname . ".zip"; //파일경로모두 입렵 
    $filesize = filesize($file_name);   //파일 사이즈

    header("Content-type: application/zip");//content사이즈 zip
    header("Content-Disposition: attachment; filename=파일이름.zip"); //파일이름 
    header("Pragma: no-cache");
    header("Expires: 0");
    header("Content-Length: $filesize");
    ob_clean();
	flush();
    readfile($file_name);//파일 읽기
    unlink($file_name); //파일 삭제
    //압축파일 다운로드

    //압축파일 업로드 

?>

<!-- form 형식 -->