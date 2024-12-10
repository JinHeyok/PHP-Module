<?php
/**
 * 인증 토큰을 가져오는 API
 * @return array
 * @throws Exception
 */
include './portOne.php';

$portOne = new portOne();
$response = $portOne->getAccessToken();

return print_r($response);
?>