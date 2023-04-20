<?php

//base64 
// - 24bit 단위로 나누어서 수행된다, 인코딩 대상 문자열을 3byte씩 나누어서 인코딩
// - 2진데이터를 아스키 코드에 해당하는 문자열로 변경해주는 방식 

//AES 
// - AES-128 , AES-192, AES-256 비트로 암호화, 키를 가지고 있어야 암호화, 복호화 가능

class AES{

    const KEY = "test";
    const iv = "1234567890123456"; // 16자리
    
    function AESEncode($result){

        $ivKey = substr(hash("sha256" , self::iv) , 0, 16);
        
        if(gettype($result) === "array"){ //배열이 넘어왔을 경우
            
            $encodeArray = array();
            
            foreach($result as $key => $item){
                $aesEncode = openssl_encrypt($item , "aes-256-cbc" , self::KEY , true , $ivKey);
                $baseEncode = base64_encode($aesEncode);
                array_push($encodeArray , $baseEncode);
            }
            
            return $encodeArray;
            
        }else if(gettype($result) === "object"){ //객체일 경우 

            $obj = new stdClass;
            $keys = array_keys((array)$result);
            
            foreach($keys as $index => $item){
                //key
                $aesEncodeKey= openssl_encrypt($item , "aes-256-cbc" , self::KEY , true ,  $ivKey);
                $baseEncodeKey = base64_encode($aesEncodeKey);//키에대한 밸류값들 암호화

                //value
                $aesEncodeValue= openssl_encrypt($result->$item , "aes-256-cbc" , self::KEY , true ,  $ivKey);
                $baseEncodeValue = base64_encode($aesEncodeValue);//키에대한 밸류값들 암호화
                $obj->$baseEncodeKey = $baseEncodeValue;

            }

            return $obj;

        }else{
            
            $aesEncode = openssl_encrypt($result , "aes-256-cbc" , self::KEY , true ,  $ivKey);
            $baseEncode = base64_encode($aesEncode);//한글 인코딩 문제로 먼저 base64로 인코딩 해준다
            //base64를 거쳐와서 해당 언어를 AES로 다시 암호화 시켜준다.

            return $baseEncode;
        }
         

    }

    function AESDecode($result){

        $ivKey = substr(hash("sha256" , self::iv) , 0, 16);
               
        if(gettype($result) === "array"){ //배열이 넘어왔을 경우
            
            $encodeArray = array();
            
            foreach($result as $key => $item){

                $baseDeocde = base64_decode($item);
                $aesDecode = openssl_decrypt($baseDeocde , "aes-256-cbc" , self::KEY , true  ,  $ivKey);
                array_push($encodeArray , $aesDecode);
            }
            
            return $encodeArray;

        }else if(gettype($result) === "object"){//객체형일 경우 
            
            $obj = new stdClass;
            $keys = array_keys((array)$result);
            
            foreach($keys as $index => $item){

                //key
                $baseDeocdeKey = base64_decode($item);//키에대한 밸류값들 암호화
                $aesDecodeKey = openssl_decrypt($baseDeocdeKey , "aes-256-cbc" , self::KEY , true ,  $ivKey);

                //value 
                $baseDeocdeValue = base64_decode($result->$item);//키에대한 밸류값들 암호화
                $aesDecodeValue = openssl_decrypt($baseDeocdeValue , "aes-256-cbc" , self::KEY , true ,  $ivKey);
                $obj->$aesDecodeKey = $aesDecodeValue;
                
            }
            return $obj;

        }else{

            $baseDeocde = base64_decode($result);
            //처음에 base64Encode 했던거를 다시 복호화 시켜준다.
            $aesDecode = openssl_decrypt($baseDeocde , "aes-256-cbc" , self::KEY , true  ,  $ivKey);
            //AES로 복호화 
            
            return $aesDecode;
        }
            
    }

    function console_log($result){
        print_r("<script>console.log('". json_encode($result) . "');</script>");
    }


}
