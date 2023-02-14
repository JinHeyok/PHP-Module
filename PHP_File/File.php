<?php

class File{

    const EXTENSION = array(
        "image" => "/jpg|jpeg|gif|svg|png|bmp/", //이미지만(eng)
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
        $extenstion = explode("." , $result)[1];         
        return $extenstion;
    }

    function error_log($error_type  , $type = "" , $extension = ""){

        $text = "";
        $lineText = "";

        //UPLOAD ERROR  : 파일이 없거나 확장명이 맞지 않을 때 
        //ZIP CREATE ERROR : 알집파일을 만드는 오류가 발생할 때 
        //NOT FILE : 파일이 없을 경우 

        switch ($error_type) {
            case "UPLOAD ERROR":
                $text = "파일이 없거나 파일 형식이 맞지 않습니다.";
                self::debug_log("파일 타입 : " . $type  . " 확장명 : " . $extension);
                break;
            case "ZIP CREATE ERROR":
                $text = "알집파일을 만드는데 오류가 발생했습니다.";
                break;
            case "NOT FILE":
                $text = "파일이 없습니다.";
                break;
            default:
                # code...
                break;
        }
        print_r("<script>alert('" . $text . "')</script>");
    }
    
    function singleFileUplaod($file, $path ,  $uuid = false ,$type){//단일 파일

        if($file != "" && $file != null){

            $extension = self::extension($file["name"]); //확장자
            $fileName = $uuid == true ? self::gen_uuid_v4() . "." . $extension :  $file["name"]; //파일 원본 이름 
            $fileSize = $file["size"]; //파일 크기 
            $fileType = $file["type"]; //파일 타입 
            $fileTmpName = $file["tmp_name"];
            
            $date = new DateTime("now");
            $date = $date->format("Y_m_d_His");// Y-m-d H:i:s 년월일 시분초
            
            if(!in_array($fileTmpName , self::FILTER) && preg_match(self::EXTENSION[$type], $extension)){
                //파일이 존재하고, 타입에 맞게 확장자가 맞을 경우 업로드 
                
                move_uploaded_file($fileTmpName , $path . $date . "_" . $fileName);
                
                return true;
                
            }else{
                self::error_log("UPLOAD ERROR" , $type , $extension);
                return false;
            }   
        }else{
            self::debug_log("FILE : " . $file);
            // self::error_log("NOT FILE");
            return false;
        }
    }

    function multifulFileUpload($file , $path ,  $uuid = false , $type ){

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

                if(!in_array($fileTmpName , self::FILTER) && preg_match(self::EXTENSION[$type] , $extension)){//해당 값 아니고, 넘어온 파일타입이 맞을 때 실행

                    move_uploaded_file($fileTmpName , $path . $date . "_" . $fileName);
                    
                }else{
                    self::error_log("UPLOAD ERROR" , $type , $extension);
                    return false;
                }
            }
        }
    }

    function zipDownload($obj){//압축해서 다운로드 
        if(in_array($obj->file, self::FILTER)){
            self::error_log("NOT FILE");
            return false;
        }
        $zip = new ZipArchive;
        $zipName = "";
        $path = $obj->path;
        $files = $obj->file;
       
        $date = new DateTime("now");
        $date = $date->format('Y_m_d_His');
        $zipname = $path . $date . ".zip"; //경로,파일 포함 (오늘날짜)

        if($zip->open($zipname , ZipArchive::CREATE) == true){//알집파일 생성
            
            foreach($files as $key => $data){
                $zip->addFile($path . $data , $data); //압추파일에 파일 넣기
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
                self::error_log("NOT FILE");
                self::debug_log("FILE ERROR : 파일이 없습니다.");
                return false;
            }
            
        }else{
            self::error_log("ZIP CREATE ERROR");
            self::debug_log($zip);
            return false;
        }
    }



}