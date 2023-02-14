
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
    <input type="file" name="myfile" id="myfile"><!-- 단일업로드 -->
    <input type="file" name="multifile[]" multiple> <!-- 다중업로드 name=[] 배열표시 key값안에 배열이 존재하게 된다.-->
    <input type="submit" name="action" value="upload">
</form>

<!-- form 형식 -->

<?php

    $fileClass = new File;

    // $file = $_FILES['myfile']; //단일 
    $file = $_FILES['multifile'];//다중 (multiful)
    
    $uploaddir = "upload/"; //경로 
    
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
    
    
    //move_uploaded_file(tmp_name, 경로를포함한 파일이름지정)
    //임시 저장된 파일을 해당경로 이름으로 업로드한다.
    

    //단일 파일 업로드 
    // $fileClass->singleFileUplaod($file , $uploaddir ,  false , "image"); 
    // 파일객체 , 경로 ,  uuid 사용여부 , 업로드할 파일 타입(check타입)

    //다중 파일 업로드 (multiful type)
    // $fileClass->multifulFileUpload($file, $uploaddir ,  false , "이미지" );
    // 배열(key이름마다 value는 배열로 이루어져있음) , 경로 , uuid 사용여부, 업로드할 파일 타입(check타입)
    
    //압축해서 파일 다운로드

    $obj = new stdClass;
    $obj->path = "upload/";
    $obj->file = array("2023_02_14_064749_sample_images_03.png" , "2023_02_14_064749_sample_images_04.png");
    // $fileClass->zipDownload($obj); 
    //해당 파일 경로 , 압축해서 다운받을 파일들(배열)

    //압축파일 업로드 
    
    //압축파일 생성 후 다운로드
    // $zip = new ZipArchive;
    // $zipname = "te1st";

    // if($zip->open($uploaddir . $zipname . ".zip" , ZipArchive::CREATE) == true){//압축파일 생성 
    //     // print_r($uploaddir . $zipname . ".zip");
    //     //파일 추가하기 
    //     $zip->addFile($uploaddir . "2023_02_14_064749_sample_images_03.png" , "2023_02_14_064749_sample_images_03.png");
    //     //원본파일(경로포함) , 압축파일내 저장할 파일 이름 
    //     $zip->addFile($uploaddir . "2023_02_14_064749_sample_images_04.png", "2023_02_14_064749_sample_images_04.png");
    //     //원본파일(경로포함) , 압축파일내 저장할 파일 이름 

    //     //폴더 추가하기 원본파일 , 폴더명/폴더안에 저장할 파일 이름
    //     // $zip->addFile($uploaddir . '샘플.pdf', '폴더명/'. "샘플.pdf");
        
    //     $zip->close(); //압축파일 닫기 
    // }else{
    //     print_r("압축파일 생성 에러");
    // }
    //압축파일 생성 후 다운로드

    //압축파일 해제
        //압축파일 오픈 , 해당 폴더가 있는지 확인
    // if($zip->open($uploaddir . $zipname . ".zip") == true && file_exists('clear/') == true){
    //     //해당 디렉토리에 압축파일을 해체한다.
    //     $zip->extractTo($uploaddir . '/clear');
    //     $zip->close();
    // }
    //압축파일 해제


    //압축파일 업로드 

?>

<!-- 단일 다운로드 -->
<a href="<?=$uploaddir . "2023_02_14_064749_sample_images_03.png"?>" download>test</a>
<!-- 단일 다운로드  -->


<!-- 리스트 다운로드  -->

<!-- 리스트 다운로드  -->

