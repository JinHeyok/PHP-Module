<?php
/**
 * 클라이언트의 인증 정보를 가져오는 API
 * @param imp_uid : 아임포트 결제 고유번호
 * @return 인증 정보 
 */
include './portOne.php';
$portOne = new portOne();

$tokenResponse = $portOne->getAccessToken();
$imp_uid = $_POST['imp_uid'];
$token = "";
if($tokenResponse['code'] == 0){
    $token = $tokenResponse['response']['access_token'];
}

$response = $portOne->getCertification($token, $imp_uid);
return print_r(json_encode($response));

?>