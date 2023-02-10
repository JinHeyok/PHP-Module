<?php

//base64 
// - 24bit 단위로 나누어서 수행된다, 인코딩 대상 문자열을 3byte씩 나누어서 인코딩
// - 2진데이터를 아스키 코드에 해당하는 문자열로 변경해주는 방식 

//AES 
// - AES-128 , AES-192, AES-256 비트로 암호화, 키를 가지고 있어야 암호화, 복호화 가능

class AES{

    const KEY = "test";
    
    function AESEncode($result){
        
        if(gettype($result) === "array"){ //배열이 넘어왔을 경우
            
            $encodeArray = array();
            
            foreach($result as $key => $item){
                $baseEncode = base64_encode($item);
                $aesEncode = openssl_encrypt($baseEncode , "aes-256-cbc" , self::KEY , true ,str_repeat(chr(0) , 16));
                array_push($encodeArray , $aesEncode);
            }
            
            return $encodeArray;
            
        }else if(gettype($result) === "object"){ //객체일 경우 

            $obj = new stdClass;
            $keys = array_keys((array)$result);
            
            foreach($keys as $index => $item){
                //key
                $baseEncodeKey = base64_encode($item);//키에대한 밸류값들 암호화
                $aesEncodeKey= openssl_encrypt($baseEncodeKey , "aes-256-cbc" , self::KEY , true , str_repeat(chr(0) , 16));

                //value
                $baseEncodeValue = base64_encode($result->$item);//키에대한 밸류값들 암호화
                $aesEncodeValue= openssl_encrypt($baseEncodeValue , "aes-256-cbc" , self::KEY , true , str_repeat(chr(0) , 16));
                $obj->$aesEncodeKey = $aesEncodeValue;

            }

            return $obj;

        }else{
            
            $baseEncode = base64_encode($result);//한글 인코딩 문제로 먼저 base64로 인코딩 해준다
            $aesEncode = openssl_encrypt($baseEncode , "aes-256-cbc" , self::KEY , true , str_repeat(chr(0) , 16));
            //base64를 거쳐와서 해당 언어를 AES로 다시 암호화 시켜준다.

            return $aesEncode;
        }
         

    }

    function AESDecode($result){

               
        if(gettype($result) === "array"){ //배열이 넘어왔을 경우
            
            $encodeArray = array();
            
            foreach($result as $key => $item){

                $aesDecode = openssl_decrypt($item , "aes-256-cbc" , self::KEY , true  , str_repeat(chr(0) , 16));
                $baseDeocde = base64_decode($aesDecode);
                array_push($encodeArray , $baseDeocde);
            }
            
            return $encodeArray;

        }else if(gettype($result) === "object"){//객체형일 경우 
            
            $obj = new stdClass;
            $keys = array_keys((array)$result);
            
            foreach($keys as $index => $item){

                //key
                $aesDecodeKey = openssl_decrypt($item , "aes-256-cbc" , self::KEY , true , str_repeat(chr(0) , 16));
                $baseDeocdeKey = base64_decode($aesDecodeKey);//키에대한 밸류값들 암호화

                //value 
                $aesDecodeValue = openssl_decrypt($result->$item , "aes-256-cbc" , self::KEY , true , str_repeat(chr(0) , 16));
                $baseDeocdeValue = base64_decode($aesDecodeValue);//키에대한 밸류값들 암호화
                $obj->$baseDeocdeKey = $baseDeocdeValue;
                
            }
            return $obj;

        }else{

            $aesDecode = openssl_decrypt($result , "aes-256-cbc" , self::KEY , true  , str_repeat(chr(0) , 16));
            //AES로 복호화 
            $baseDeocde = base64_decode($aesDecode);
            //처음에 base64Encode 했던거를 다시 복호화 시켜준다.
            
            return $baseDeocde;
        }
            
    }

    function console_log($result){
        print_r("<script>console.log('". json_encode($result) . "');</script>");
    }


}
