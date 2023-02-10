<?php



class RSA { //인증서 제작 이슈 ....
    
    const PRIVATE_KEY = "private key";
    const PUBLICK_KEY = "public key";

    function RSAencode($result){

        $baseEncode = base64_encode($result);
        $encrypted = '';
        $rsaEncode = openssl_public_encrypt($baseEncode , $encrypted, self::PUBLICK_KEY);
        self::console_log($rsaEncode);
        return $rsaEncode;
        
    }

    function RSAdecode(){
        
    }

    function console_log($result){
        print_r("<script>console.log('" . json_encode($result) .  "')</script>");
    }

}