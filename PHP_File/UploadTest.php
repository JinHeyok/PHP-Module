
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
    <input type="file" name="multifile[]" id="mutifile" multiple> <!-- 다중업로드 name=[] 배열표시 key값안에 배열이 존재하게 된다.-->
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
    // $fileClass->singleFileUplaod($file , $uploaddir ,  "image" , false); 
    // singleFileUpload(파일객체 , 경로  , 업로드할 파일 타입(check타입) ,  uuid 사용여부(default = false));

    //다중 파일 업로드 (multiful type)
    // $fileClass->multifulFileUpload($file, $uploaddir , "pdf", false );
    // multifulUpload(배열(key이름마다 value는 배열로 이루어져있음) , 경로 ,  업로드할 파일 타입(check타입) , uuid 사용여부(defalt = false))
    
    //압축해서 파일 다운로드
    // $obj = new stdClass;
    // $obj->path = "";
    // $obj->file = array("upload/2023_02_14_064749_sample_images_03.png" , "upload/2023_02_14_064749_sample_images_04.png");
    // $obj = array("upload/2023_02_14_064749_sample_images_03.png" , "upload/2023_02_14_064749_sample_images_04.png");
    // $fileClass->zipDownload($obj); 
    //해당 파일 경로 , 압축해서 다운받을 파일들(배열)
    // zipDownload(오브젝트 방식 OR  배열방식(경로가 포함되어야함) , 필요할시 경로입력);

    // 연속 다운로드 (파일명만 있을경우)
    // $fileArray = array(
    //     "upload/2023_02_14_064749_sample_images_03.png",
    //     "upload/2023_02_14_064749_sample_images_04.png",
    //     "upload/2023_02_14_064749_sample_images_05.png",
    // );
    // $fileClass->allDownload($fileArray);
    // allDownload(배열(파일이름) , 경로);

    //압축파일 업로드 

    // //압축해서 기존파일 다운로드 하기
    // $obj = new stdClass;
    // $obj->path = "";
    // $obj->file = array("test/upload/2023_02_14_064749_sample_images_03.png" , "test/upload/2023_02_14_064749_sample_images_04.png");
    // $obj = array("2023_02_14_064749_sample_images_03.png" , "2023_02_14_064749_sample_images_04.png");
    // $fileClass->zipDownload($obj); 
    // zipDownload(오브젝트 OR 배열 (파일이름) , 필요시 경로);

    //압축해서 파일 업로드 하기

    // 압축해서 업로드 다중 (multiful)
    // $file = $_FILES['multifile'];
    // $fileClass->zipUpload($file , $uploaddir);
    // zipUpload(파일 오브젝트(multiful , 필요시 , 경로));

    // 압축해서 업로드 
    
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


<!-- script 리스트 upload -->
<div class="fileList">
</div>
<button type="button">버튼</button>

<!-- script 리스트 upload 테스트 -->

<script>

    var fileInput = document.querySelector('#myfile');
    var uploadButton = document.querySelector("button");
    var uploadFileList = [];

    fileInput.addEventListener("change", function(){

        // console.log(fileInput.files[0]); //파일객체
        uploadFileList.push(fileInput.files[0]);

        var p  = document.createElement("p");
        p.textContent = "업로드 파일 : " + fileInput.files[0]['name'];

        document.querySelector(".fileList").appendChild(p);

    });

    uploadButton.addEventListener("click" , function(){

        var formData = new FormData;

        var test = [
        "2023_02_14_064749_sample_images_03.png",
        "2023_02_14_064749_sample_images_04.png",
        "2023_02_14_064749_sample_images_05.png",
        ];

        var mulitfulFileList = document.querySelector("#mutifile");

        uploadFileList.forEach((data , index) => {
            formData.append("fileList" + index ,  data);
        }); 
        formData.append("fileCount" , uploadFileList.length);
        formData.append("testFileList" , test);
        formData.append("multiFileList" , mulitfulFileList);
        
        const requset = new XMLHttpRequest;

        requset.open("POST" , "./UploadTest2.php");
        requset.addEventListener("readystatechange" , function(){
            if(requset.readyState == XMLHttpRequest.DONE){
                if(requset.status === 200){
                    console.log(requset.response);
                    const response = JSON.parse(requset.response);
                    console.log(response);
                }
            }
        });
        requset.send(formData);
    });

    

</script>

<!-- script 리스트 upload 테스트-->