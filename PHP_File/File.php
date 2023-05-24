<?php


class File{

    private const EXTENSION = array(
        "IMAGE" => "/tiff|jfif|bmp|gif|svg|png|jpeg|svgz|jpg|webp|ico|xbm|dib|pip|apng|tif|pjpeg|avif/", 
        "VIDEO" => "/ogm|wmv|mpg|webm|ogv|mov|asx|mpeg|mp4|m4v|avi/", 
        "AUDIO" => "/wma|flac|webm|oga|mp3|mp4|ogg|wav|opus|amr|aac/",
        "APPLICATION" => "/zip|crt|docx|xlsx|ppt|xul|apk|tar|ai|ps|rss|p7s|woff|p7z|p7c|pptx|pdf|rtf|bin|p7m|swf|xhtm|dot|swl|doc|xls|json|m3u8|epub|gz|com|rdf|js|cer|xhtml|tgz|xht|eps|crx|wasm|exe/",
        
        "IMAGE_USE" => "/bmp|gif|png|svg|jpeg|jpg/",
        "VIDEO_USE" => "/wmv|mpg|mp4|avi/",
        "AUDIO_USE" => "/mp3|mp4|flac|wav/",
        "APPLICATION_USE" => "/docx|ppt|xlsx|xls|pptx|pdf/",
    );

    /**
    *@param FILE_EXTENSTION_ERROR : 파일이 없거나 파일 형식이 맞지 않을 경우
    *@param DATA_TYPE_ERROR : 파라미터 데이터 타입이 맞지 않을 경우
    *@param ZIP_CREATE_ERROR : 알집파일을 만드는 오류가 발생할 때 
    *@param FILE_EXITSTS_ERROR : 알집에 파일 추가중 파일이 존재하지 않을 때 
    *@param UPLOAD_PATH_ERROR : 업로드 경로가 존재하지 않을 경우 
    *@param FILE_EXTENSTION_CHECK_ERROR : 설정된 확장자 명이 아닐 경우
    *@param MULTIFUL_FILE_TYPE_ERROR : 멀티플 업로드시 데이터 타입이 array가 아닐 경우
    *@param NOT_FILE : 파일이 없을 경우 
    *@param NOT_PATH_ERROR : 경로가 없을 경우
    *@param NONE : 설정된 에러 항목이 없을 경우
    */
    private const ERROR_MESSAGE = array(
        "FILE EXTENSTION ERROR" => "파일이 없거나 파일 형식이 맞지 않습니다.",
        "DATA TYPE ERROR" => "데이터 타입이 맞지 않습니다.",
        "ZIP CREATE ERROR" => "알집파일을 생성하는데 도중 오류가 발생했습니다.",
        "FILE EXITSTS ERROR" => "알집파일에 파일을 추가하는 도중 오류가 발생했습니다.",
        "UPLOAD PATH ERROR" => "업로드 경로가 존재하지 않습니다.",
        "FILE EXTENSTION CHECK ERROR" => "설정된 확장자 명을 넣어주세요.",
        "MULTIFUL FILE TYPE ERROR" => "다중 업로드 데이터 타입이 맞지 않을 경우",
        "NOT FILE" => "파일이 없습니다.",
        "NOT PATH ERROR" => "파일의 경로가 없습니다.",
        "NONE" => "설정된 에러 항목이 없습니다.",
    );

    /**
     * @param file->error->UPLOAD_ERR_OK_0 : 업로드 정상완료
     * @param file->error->UPLOAD_ERR_INI_SIZE_1 : php.ini에 설정된 최대 파일크기 초과
     * @param file->error->UPLOAD_ERR_FORM_SIZE_2 : HTML 폼에 설정된 최대 파일크기 초과
     * @param file->error->UPLOAD_ERR_PARTIAL_3 : 파일의 일부만 업로드 됨
     * @param file->error->UPLOAD_ERR_NO_FILE_4 : 업로드 할 파일이 없음
     * @param file->error->UPLOAD_ERR_NO_TMP_DIR_6 : 웹서버에 임시폴더가 없음
     * @param file->error->UPLOAD_ERR_CANT_WRITE_7 : 파일을 쓸 수 없음
     * @param file->error->UPLOAD_ERR_EXTENSION_8 : PHP 확장기능에 의한 업로드 중단  
     */
    private const FILE_ERROR_CODE = array(
        0 => "UPLOAD_ERR_OK_0 : 업로드가 정상적으로 이루어졌습니다.",
        1 => "UPLOAD_ERR_INI_SIZE_1 : php.ini에 설정된 최대 파일크기 초과되었습니다.",
        2 => "UPLOAD_ERR_FORM_SIZE_2 : HTML 폼에 설정된 최대 파일크기 초과되었습니다.",
        3 => "UPLOAD_ERR_PARTIAL_3 : 파일의 일부만 업로드 되었습니다.",
        4 => "UPLOAD_ERR_NO_FILE_4 : 업로드 할 파일이 없습니다.",
        6 => "UPLOAD_ERR_NO_TMP_DIR_6 : 웹서버에 임시폴더가 없습니다.",
        7 => "UPLOAD_ERR_CANT_WRITE_7 : 파일을 사용할 수 없습니다.",
        9 => "UPLOAD_ERR_EXTENSION_8 : PHP 확장기능에 의한 업로드 중단되었습니다.",
    );

    private $errorLogStatus = true; 

    //전체 확장명 종류
    const IMAGE_ALL = "IMAGE";
    const VIDEO_ALL = "VIDEO";
    const AUDIO_ALL = "AUDIO";
    const APPLICATION_ALL = "APPLICATION";
    
    //자주 사용하는 확장명 
    const IMAGE_USE = "IMAGE_USE";
    const VIDEO_USE = "VIDEO_USE";
    const AUDIO_USE = "AUDIO_USE";
    const APPLICATION_USE = "APPLICATION_USE";

    private const FILTER = array("" , null , "undefined" , false); //빈값 체크 
    private const DATA_FILTER = array("string" , "integer" , "object" , "array");
    private $date = "";

    function __construct(){
        $this->date = new DateTime("now" , new DatetimeZone('Asia/Seoul'));
        $this->date = $this->date->format('Y_m_d_His');
    }

    function __destruct(){
        $this->date = "";
    }

    function gen_uuid_v4() { //file uuid 생성
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
         );
    }   

    function extension($result){ //확장자 구하기
        $extension = explode("." , $result);
        $extension = $extension[count($extension) - 1];         
        return $extension;
    }

    function fileInfo(array $file){

        if($this->errorLogStatus){
            
            empty($file) == false ? self::ERROR_MESSAGE["NOT FILE"]  : exit;
            
            $text = "파일 이름 : " . $file['name'];
            $text .= "<br/>파일 크기 : " . $file["size"] . "KB";
            $text .= "<br/>파일 타입 : " . $file["type"];
            $text .= "<br/>파일 경로 : " . $file["full_path"];
            $text .= "<br/>파일 tmp 경로 : " . $file["tmp_name"];
            $text .= "<br>파일 ERROR 코드(상태) " . (self::FILE_ERROR_CODE[$file["error"]]);
            
            print(sprintf("<pre style='background-color : 666666; color : white; font-family : fangsong; font-weight : bold; padding : 0.2rem; white-space : pre-wrap;'>%s</pre>" , print_r($text , true)));
            
        }
    }

    function debug_log($result){//콘솔로그 값 확인
        print_r("<script>console.log('" .  json_encode($result). "')</script>");
    }

    function data_log($result){
        print(sprintf("<pre style='background-color : 006600; color : white; font-family : fangsong; font-weight : bold; padding : 0.2rem;'>%s</pre>" , print_r($result , true)));
        //print_r 문자로 가져오기 위해 true
    }

    function error_log($error_type  , $fileStatus = "" , $fileExtension = ""){

        $text = "";
        
        if($this->errorLogStatus){
            
            switch ($error_type) {
                case "FILE EXTENSTION ERROR":
                    $text = "FILE EXTENSTION ERROR : " . self::ERROR_MESSAGE["FILE EXTENSTION ERROR"] . 
                    $text .= "\n 업로드 가능한 확장자 : " . $fileStatus;
                    $text .= "\n 실제 업로드 된 확장자 : " . ($fileExtension != "" ? $fileExtension : (empty($fileExtension) == true ? "NULL" : "확인 필요"));
                    break;
                case "DATA TYPE ERROR":
                    $text = "DATA TYPE ERROR : " . self::ERROR_MESSAGE["DATA TYPE ERROR"] . 
                    $text .= "\n 가능한 파라미터 타입 : " . $fileStatus;
                    $text .= "\n 받은 파라미터 타입 : " . gettype($fileExtension);
                    break;
                case "UPLOAD PATH ERROR":
                    $text = "UPLOAD PATH ERROR : " . self::ERROR_MESSAGE["UPLOAD PATH ERROR"];
                    break;
                case "FILE EXTENSTION CHECK ERROR":
                    $text = "FILE EXTENSTION CHECK ERROR : " . self::ERROR_MESSAGE["FILE EXTENSTION CHECK ERROR"];
                    break;
                case "ZIP CREATE ERROR":
                    $text = "ZIP CREATE ERROR : " . self::ERROR_MESSAGE["ZIP CREATE ERROR"];
                    break;
                case "FILE EXITSTS ERROR":
                    $text = "FILE EXITSTS ERROR : " . self::ERROR_MESSAGE["FILE EXITSTS ERROR"];
                    $text .= "\nFILE URL : " . $fileStatus;
                    break;
                case "MULTIFUL FILE TYPE ERROR":
                    $text = "MULTIFUL FILE TYPE ERROR : " . self::ERROR_MESSAGE["MULTIFUL FILE TYPE ERROR"];
                    break;
                case "NOT PATH ERROR":
                    $text = "NOT PATH ERROR : " . self::ERROR_MESSAGE["NOT PATH ERROR"];
                    break;
                case "NOT FILE":
                    $text = "NOT FILE : " . self::ERROR_MESSAGE["NOT FILE"];
                    $text .= "\n 파일 : " . (gettype($fileStatus) != "array" ? self::ERROR_MESSAGE["FILE EXTENSTION ERROR"] : ($fileStatus["error"] == 4 || gettype($fileStatus["error"]) == "integer" ? gettype($fileStatus) : $fileStatus));
                    $text .= "\n 파일 타입 : " . gettype($fileStatus);
                    $text .= "\n FILE ERRPR CODE : " . (gettype($fileStatus) != "array" ? self::ERROR_MESSAGE["FILE EXTENSTION ERROR"] : (gettype($fileStatus["error"]) != "array" ? self::FILE_ERROR_CODE[$fileStatus["error"]] : self::FILE_ERROR_CODE[$fileStatus["error"][0]]));
                    break;
                default:
                    $text = "NONE : 설정된 에러 항목이 없습니다.";
                    break;
            }
            print(sprintf("<pre style='background-color : FF0000; color : white; font-family : fangsong; font-weight : bold; padding : 0.2rem; white-space : pre-wrap;'>%s</pre>" , print_r($text , true)));
        }
    }
    
    function singleFileUplaod($file , $path , $type , $uuid = false){//단일 파일

        if((in_array($path , self::FILTER) && gettype($path) != "String") || !is_dir($path)){
            self::error_log("UPLOAD PATH ERROR");
            return false;
        }
        
        if(!in_array($type , array_keys(self::EXTENSION))){
            self::error_log("FILE EXTENSTION CHECK ERROR");
            return false;
        }

        if(!in_array($file, self::FILTER) && $file["error"] == 0){ //파일이 없을경우 error코드 '4'

            $extension = self::extension($file["name"]); //확장자
            $fileName = $uuid == true ? self::gen_uuid_v4() . "." . $extension :  $file["name"]; //파일 원본 이름 
            $fileTmpName = $file["tmp_name"];
            $date = $this->date;

            if(!in_array($fileTmpName , self::FILTER) && preg_match(self::EXTENSION[$type], $extension)){
                //파일이 존재하고, 타입에 맞게 확장자가 맞을 경우 업로드 
                $fileObj = new stdClass;
                $fileObj->status = false;
                if(file_exists($fileTmpName)){
                    if(move_uploaded_file($fileTmpName , $path . $date . "_" . $fileName)){
                        $fileObj->status = true;
                        $fileObj->fileName = $date . "_" . $fileName;
                        return $fileObj;
                    };
                }else{
                    self::error_log("NOT FILE" , $file);
                    return false;
                }
                
            }else{
                self::error_log("FILE EXTENSTION ERROR" , self::EXTENSION[$type] , $extension);
                return false;
            }   

        }else{
            self::error_log("NOT FILE" , $file);
            return false;
        }
    }

    function multifulFileUpload($file , $path ,  $type  ,  $uuid = false){//다중 업로드 Multiful 사용 

        if(empty($file["tmp_name"]) || gettype($file) != "array"){
            self::error_log("NOT FILE" , $file);
            return false;
        }

        if(!in_array($type , array_keys(self::EXTENSION))){
            self::error_log("FILE EXTENSTION CHECK ERROR");
            return false;
        }

        if((in_array($path , self::FILTER) && gettype($path) != "String") || !is_dir($path)){
            self::error_log("UPLOAD PATH ERROR");
            return false;
        }

        if(!empty($file["tmp_name"]) && gettype($file["tmp_name"]) == "array"){//멀티플 사용 

            $fileObj = new stdClass;
            $fileObj->status = false;
            $fileObj->fileName = array();

            foreach($file['tmp_name'] as $key => $data){
                  
                $extension = self::extension($file['name'][$key]);//파일확장자
                $fileName = $uuid == true ? self::gen_uuid_v4() . "." . $extension : $file['name'][$key];// 파일이름 기본이름할지 UUID 쓸지 
                $fileTmpName = $data;

                $date = $this->date;//날짜 생성 

                if(!in_array($fileTmpName , self::FILTER) && preg_match(self::EXTENSION[$type] , $extension)){//해당 값 아니고, 넘어온 파일타입이 맞을 때 실행
                    if(file_exists($fileTmpName)){  
                        if(move_uploaded_file($fileTmpName , $path . $date . "_" . $fileName)){
                            $fileObj->status = true;
                            array_push($fileObj->fileName , $date . "_" . $fileName);
                        };
                    }else{
                        self::error_log("NOT FILE" , $file);
                        return false;
                    }
                        
                }else{
                    self::error_log("FILE EXTENSTION ERROR" , self::EXTENSION[$type] , $extension);
                    return false;
                }
            }
            return $fileObj;
        }else{
            self::error_log("MULTIFUL FILE TYPE ERROR" , $file);
            return false;
        }
    }

    function zipDownload($obj , $path = ""){//압축해서 다운로드 

        if(empty($obj) && in_array($obj->file, self::FILTER)){ //파일 유무 검사
            self::error_log("DATA TYPE ERROR" , "object , array" , $obj ); //넘어오는 데이터 타입이 형식에 안맞을 때
            return false;
        }

        $zip = new ZipArchive;
        $file = "";
        
        if(gettype($obj) == "object"){ //오브젝트로 넘어올시 
            $path = empty($obj->path) ? self::error_log("NOT PATH ERROR") : $obj->path;
            $files = empty($obj->file) ? self::error_log("FILE EXTENSTION ERROR") : $obj->file;
        }else if(gettype($obj) == "array"){//배열로 넘어올시 (경로가 포함되어야함)
            $files = $obj;
        }else{
            self::error_log("DATA TYPE ERROR" , "object , array" , $obj ); //넘어오는 데이터 타입이 형식에 안맞을 때
            exit;
            return false;
        }
        
        $date = $this->date;

        foreach($files as $key => $data){

            if($path == null || $path == ""){ //경로가포함되어서 올 때  
                $path = explode("/" , $data);
                $path = array_slice($path , 0 , count($path) - 1);
                $path = implode("/" , $path) . "/";
            }

        }

        $zipname = $path . $date . ".zip"; //경로,파일 포함 (오늘날짜)

        if($zip->open($zipname , ZipArchive::CREATE) == true){//알집파일 생성
            
            foreach($files as $key => $data){
                $file = explode("/" , $data); //경로가 포함되서 올 경우를 대비해서
                $file = array_slice($file , count($file) - 1 , 1);
                $file = $file[0];
                if(file_exists($path . $file)){
                    $zip->addFile($path . $file , $file); //압축파일에 파일 넣기
                }else{
                    self::error_log("FILE EXITSTS ERROR" , $path . $file);
                    return false;
                }
            }
            
            $zip->close(); //압축파일 닫기 
            
            $file_name = $zipname; //파일경로모두 입렵 
            $filesize = filesize($file_name);   //파일 사이즈
            
            if(file_exists($file_name)){

                header("Content-type: application/zip");//content사이즈 zip
                header("Content-Disposition: attachment; filename=" . $date . ".zip"); //파일이름 
                header("Pragma: no-cache");
                header("Expires: 0");
                header("Content-Length: $filesize");
                ob_clean();
                flush();
                readfile($file_name);//파일 읽기
                unlink($file_name); //파일 삭제

            }else{
                self::error_log("FILE EXITSTS ERROR" , $file_name);
                return false;
            }
            
        }else{
            self::error_log("ZIP CREATE ERROR");
            self::debug_log($zip);
            return false;
        }
    }

    function zipUpload($obj , $path = ""){//압축해서 업로드 

        if(empty($obj) && in_array($obj->file, self::FILTER)){ //파일 유무 검사
            self::error_log("DATA TYPE ERROR" , "object , array" , $obj ); //넘어오는 데이터 타입이 형식에 안맞을 때
            return false;
        }

        $zip = new ZipArchive;
        $fileObj = new stdClass;
        $fileObj->status = false;
        
        if(gettype($obj) == "object"){ //오브젝트로 넘어올시 
            $path = $obj->path;
            $files = $obj->file;
        }else if(gettype($obj) == "array"){//배열로 넘어올시 (경로가 포함되어야함)
            $files = $obj;
        }else{
            self::error_log("DATA TYPE ERROR" , "object , array" , $obj ); //넘어오는 데이터 타입이 형식에 안맞을 때
        }

        $date = $this->date;
        
        foreach($files as $key => $data){
            
            if($path == null || $path == ""){ //경로가포함되어서 올 때  
                $path = explode("/" , $data);
                $path = array_slice($path , 0 , count($path) - 1);
                $path = implode("/" , $path) . "/";
            }
            
        }

        $zipname = $path . $date . ".zip"; //경로,파일 포함 (오늘날짜)
        
            if($zip->open($zipname , ZipArchive::CREATE) == true){//알집파일 생성
                
            if(!empty($files[0]['tmp_name'])){ //JS 동적 파일 업로드 
                foreach($files as $key => $data){

                    $fileTmpName = $data["tmp_name"];

                    if($fileTmpName != "" && $fileTmpName != null){
                        
                        if(file_exists($fileTmpName)){
                            $zip->addFile($fileTmpName , $data["name"]); //압축파일에 파일 넣기
                            $fileObj->status = true;
                            $fileObj->fileName = $date . ".zip";
                        }else{
                            self::error_log("NOT FILE" , $fileTmpName);
                            return false;
                        }
                    }
                    
                }

                return $fileObj;

            }else if(!empty($files['tmp_name'])){ //바로 업로드시 멀티플 압축파일로 업로드
                
                foreach($files["tmp_name"] as $key => $data){
                    $fileName = $files["name"][$key];
                    $fileTmpName = $files["tmp_name"][$key];

                    if(file_exists($fileTmpName)){
                        $zip->addFile($fileTmpName , $fileName); //압축파일에 파일 넣기
                        $fileObj->status = true;
                        $fileObj->fileName = $date . ".zip";
                    }else{
                        self::error_log("NOT FILE" , $fileTmpName);
                        return false;
                    }
                }
                $zip->close(); //압축파일 닫기 
                return $fileObj;
            }else{
                self::error_log("NOT FILE" , $files);
                return false;
            }
        }else{
            self::error_log("ZIP CREATE ERROR");
        }
    }

    function allDownload($result, $path = ""){// 연속다운로드 

        if(in_array($result , self::FILTER)){
            self::error_log("NOT FILE" , $result);
            return false;
        }

        foreach($result as $key => $file){
            if(file_exists($path . $file)){
                $fileDownLoad = $path . $file;
                $fileName = $file;
                print_r(
                "<script>
                function download(){
                    var file =  '" . $fileDownLoad . "';
                    var fileName = '" . $fileName . "';
                    var a = document.createElement('a');
                    a.href = file;
                    a.download = fileName;
                    a.click();
                }
                download();
            </script>");
            //....이렇게 해도 되는걸까..?

            }else{
                self::error_log("FILE EXITSTS ERROR" , $path . $file);
                return false;
            }
        }
        return true;
    }

}