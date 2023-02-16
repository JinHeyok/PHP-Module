<?php

class File{

    const EXTENSION = array(
        "IMAGE" => "/jpg|jpeg|gif|svg|png|bmp/", //이미지만(eng)
        "이미지" => "/jpg|jpeg|gif|svg|png|bmp/", //이미지만(kor)
        "PDF" => "/pdf/",  //PDF파일만
    );
    const FILTER = array("" , null , "undefined"); //빈값 체크 

    function debug_log($result){//콘솔로그 값 확인
        print_r("<script>console.log('" .  json_encode($result). "')</script>");
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

    function error_log($error_type  , $fileName = "" , $filePath = ""){

        $text = "";

        // self::debug_log("fileName : " . $fileName);
        // self::debug_log("filePath : " . $filePath);

        //UPLOAD ERROR  : 파일이 없거나 확장명이 맞지 않을 때 
        //ZIP CREATE ERROR : 알집파일을 만드는 오류가 발생할 때 
        //NOT FILE : 파일이 없을 경우 

        switch ($error_type) {
            case "UPLOAD ERROR":
                $text = "UPLOAD ERROR : 파일이 없거나 파일 형식이 맞지 않습니다.";
                break;
            case "FILE EXTENSTION ERROR":
                $text = "FILE EXTENSTION ERROR : 파일이 없거나 파일 형식이 맞지 않습니다." . 
                $text .= "\n 업로드 할 확장자 : " . $fileName;
                $text .= "\n 실제 업로드 된 확장자 : " . $filePath;
                break;
            case "FILE TYPE ERROR":
                $text = "FILE TYPE ERROR : 파일 형식이 맞지 않습니다." . 
                $text .= "\n 가능한 파일 타입 : " . $fileName;
                $text .= "\n 받은 파라미터 타입 : " . gettype($filePath);
                break;
            case "ZIP CREATE ERROR":
                $text = "ZUP CREATE ERROR : 알집파일을 만드는데 오류가 발생했습니다.";
                break;
            case "NOT FILE":
                $text = "NOT FILE : 파일이 없습니다.";
                $text .= "\n 파일 : " . $fileName;
                $text .= "\n 파일타입 : " . gettype($fileName);
                break;
            default:
                # code...
                break;
        }
        print_r("<script>console.log('" . json_encode($text) . "')</script>");
    }
    
    function singleFileUplaod($file , $path , $type , $uuid = false){//단일 파일

        if($file != "" && $file != null){

            $extension = self::extension($file["name"]); //확장자
            $fileName = $uuid == true ? self::gen_uuid_v4() . "." . $extension :  $file["name"]; //파일 원본 이름 
            // $fileSize = $file["size"]; //파일 크기 
            // $fileType = $file["type"]; //파일 타입 
            $fileTmpName = $file["tmp_name"];
            
            $date = new DateTime("now");
            $date = $date->format("Y_m_d_His");// Y-m-d H:i:s 년월일 시분초
            
            if(!in_array($fileTmpName , self::FILTER) && preg_match(self::EXTENSION[strtoupper($type)], $extension)){
                //파일이 존재하고, 타입에 맞게 확장자가 맞을 경우 업로드 
                if(file_exists($fileTmpName)){
                    move_uploaded_file($fileTmpName , $path . $date . "_" . $fileName);
                    return true;
                }else{
                    self::error_log("NOT FILE" , $fileTmpName);
                    return false;
                }
                
            }else{
                self::error_log("FILE EXTENSTION ERROR" , $type , $extension);
                exit;
                return false;
            }   
            
        }else{
            self::error_log("NOT FILE" , $file);
            return false;
        }
    }

    function multifulFileUpload($file , $path ,  $type  ,  $uuid = false){//다중 업로드 Multiful 사용 

        if(in_array($file , self::FILTER)){
            self::error_log("NOT FILE");
            return false;
        }

        if(gettype($file["tmp_name"]) == "array"){//멀티플 사용 

            foreach($file['tmp_name'] as $key => $data){
                  
                $extension = self::extension($file['name'][$key]);//파일확장자
                $fileName = $uuid == true ? self::gen_uuid_v4() . "." . $extension : $file['name'][$key];// 파일이름 기본이름할지 UUID 쓸지 
                $fileTmpName = $data;

                $date = new DateTime("now");//날짜 생성 
                $date = $date->format("Y_m_d_His");

                if(!in_array($fileTmpName , self::FILTER) && preg_match(self::EXTENSION[strtoupper($type)] , $extension)){//해당 값 아니고, 넘어온 파일타입이 맞을 때 실행
                    
                    if(file_exists($fileTmpName)){  
                        move_uploaded_file($fileTmpName , $path . $date . "_" . $fileName);
                    }else{
                        self::error_log("NOT FILE" , $fileTmpName);
                        return false;
                    }
                        
                }else{
                    self::error_log("FILE EXTENSTION ERROR" , $type , $extension);
                    exit;
                    return false;
                }
            }

            return true;
        }
    }

    function zipDownload($obj , $path = ""){//압축해서 다운로드 

        if(in_array($obj->file, self::FILTER) && empty($obj)){ //파일 유무 검사
            self::error_log("FILE TYPE ERROR" , "object , array" , $obj ); //넘어오는 데이터 타입이 형식에 안맞을 때
            return false;
        }

        $zip = new ZipArchive;
        $file = "";
        
        if(gettype($obj) == "object"){ //오브젝트로 넘어올시 
            $path = $obj->path;
            $files = $obj->file;
        }else if(gettype($obj) == "array"){//배열로 넘어올시 (경로가 포함되어야함)
            $files = $obj;
        }else{
            self::error_log("FILE TYPE ERROR" , "object , array" , $obj ); //넘어오는 데이터 타입이 형식에 안맞을 때
            exit;
            return false;
        }
        
        $date = new DateTime("now");
        $date = $date->format('Y_m_d_His');

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
                    self::error_log("NOT FILE" , $path . $file);
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
                self::error_log("NOT FILE" , $file_name);
                return false;
            }
            
        }else{
            self::error_log("ZIP CREATE ERROR");
            self::debug_log($zip);
            return false;
        }
    }

    function zipUpload($obj , $path = ""){//압축해서 업로드 

        if(in_array($obj->file, self::FILTER) && empty($obj)){ //파일 유무 검사
            self::error_log("FILE TYPE ERROR" , "object , array" , $obj ); //넘어오는 데이터 타입이 형식에 안맞을 때
            return false;
        }

        $zip = new ZipArchive;
        $file = "";
        
        if(gettype($obj) == "object"){ //오브젝트로 넘어올시 
            $path = $obj->path;
            $files = $obj->file;
        }else if(gettype($obj) == "array"){//배열로 넘어올시 (경로가 포함되어야함)
            $files = $obj;
        }else{
            self::error_log("FILE TYPE ERROR" , "object , array" , $obj ); //넘어오는 데이터 타입이 형식에 안맞을 때
        }

        
        $date = new DateTime("now");
        $date = $date->format('Y_m_d_His');
        
        foreach($files as $key => $data){
            
            if($path == null || $path == ""){ //경로가포함되어서 올 때  
                $path = explode("/" , $data);
                $path = array_slice($path , 0 , count($path) - 1);
                $path = implode("/" , $path) . "/";
            }
            
        }

        $zipname = $path . $date . ".zip"; //경로,파일 포함 (오늘날짜)
        
            if($zip->open($zipname , ZipArchive::CREATE) == true){//알집파일 생성
                
            if($files[0]['tmp_name'] != null && $files[0]['tmp_name'] != ""){ //JS 동적 파일 업로드 
                foreach($files as $key => $data){

                    $fileTmpName = $data["tmp_name"];

                    if($fileTmpName != "" && $fileTmpName != null){
                        
                        if(file_exists($fileTmpName)){
                            $zip->addFile($fileTmpName , $data["name"]); //압축파일에 파일 넣기
                        }else{
                            self::error_log("NOT FILE" , $fileTmpName);
                            return false;
                        }
                    }
                    
                }

                return true;

            }else if($files['tmp_name'] != null && $files['tmp_name'] != ""){ //바로 업로드시 멀티플 압축파일로 업로드
                
                foreach($files["tmp_name"] as $key => $data){
                    $fileName = $files["name"][$key];
                    $fileTmpName = $files["tmp_name"][$key];

                    if(file_exists($fileTmpName)){
                        $zip->addFile($fileTmpName , $fileName); //압축파일에 파일 넣기
                    }else{
                        self::error_log("NOT FILE" , $fileTmpName);
                        return false;
                    }
                }
                $zip->close(); //압축파일 닫기 
                return true;
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
                self::error_log("NOT FILE" , $path . $file);
                return false;
            }
        }
        return true;
    }

}