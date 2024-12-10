<?php
/**
 * PortOne API 통신 클래스
 * @package portOne
 * @version 1.0.0
 * @since 2024.12.03
 * @author NINEFIVE
 * NOTE : static API URL -> 외부 호출 URL
 * NOTE : 아닐 경우 static-api.iamport.kr -> api.iamport.kr
 */
class portOne{

    const DEV_API_KEY = ''; // DEV API KEY
    const DEV_SECRET_KET = ''; // DEV SECRET KEY
    const DEV_IMP_KEY = '';  // DEV IMP KEY
    const DEV_CHANNEL = ''; // DEV CHANNEL

    const PROD_API_KEY = ''; // PROD API KEY
    const PROD_SECRET_KET = ''; // PROD SECRET KEY
    const PROD_IMP_KEY = ''; // PROD IMP KEY
    const PROD_CHANNEL = ''; // PROD CHANNEL
    const PROG_PG = '{PG Provider}.{PG 상점아이디[MID]}'; // PROD PG KEY


    public $PG = "";
    public $CHANNEL = "";
    public $IMP_KEY = "";

    /**
     * URL 을 확인하여 기본 설정값을 변경
     * @return void
     * @throws Exception
     */
    public function __construct() {
        if (self::URLCheck()) {
            $this->IMP_KEY = self::DEV_IMP_KEY;
            $this->CHANNEL = self::DEV_CHANNEL;
        } else {
            $this->IMP_KEY = self::PROD_IMP_KEY;
            $this->CHANNEL = self::PROD_CHANNEL;
            $this->PG = self::PROG_PG;
        }
    }

    /**
     * PortOne API 토큰 발급 요청
     * @return JSON
     * @throws Exception
     */
    function getAccessToken(){
        $url = "https://static-api.iamport.kr/users/getToken";
        $ch = curl_init($url);
        $data = array();
        // 서버 URL 분기 처리
        if (self::URLCheck()) {
            $data = array("imp_key" => self::DEV_API_KEY, "imp_secret" => self::DEV_SECRET_KET);
        } else {
            $data = array("imp_key" => self::PROD_API_KEY, "imp_secret" => self::PROD_SECRET_KET);
        }
        $data = json_encode($data);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($response, true); // JSON 데이터를 Array로 변환
        return $response;
    }

    /**
     * PortOne API 인증 정보 조회
     * @param token 발급된 토큰 데이터
     * @param imp_uid 인증 완료 후 인증 고유 아이디
     * @return JSON 
     */
    function getCertification($token, $imp_uid) {
        $url = "https://static-api.iamport.kr/certifications/$imp_uid";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: '.$token, // 발급된 토큰 데이터
            'Content-Type: application/json'
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($response, true); // JSON 데이터를 Array로 변환
        return $response;
    }

    /**
     * URL 체크
     * @return boolean 
     * @throws Exception
     */
    function URLCheck(){
        $currentUrlHost = $_SERVER['HTTP_HOST'];
        if ($currentUrlHost == '{분기 처리할 URL}') {
            // 개발 서버 일 경우
            return true;
        } else {
            // 운영 서버 일 경우
            return false;
        }
    }


}

?>