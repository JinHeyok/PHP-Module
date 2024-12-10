<!--
PortOne index page 해당 PHP 파일을 include 하실 본인 인증 로직을 추가한다.
*************************** Code ***************************
<?php 
//  include $_SERVER['DOCUMENT_ROOT'] . '/portOne/index.php';
?>
<script>
    document.getElementById({Element}).onclick = function(){getAuthcication();}
</script>
*************************** Code ***************************

getAuthcication() 호출 시 본인인증이 추가된다.
<input type="text" id="name" certification-name> 
<input type="text" id="birthDay" certification-birth> 
<input type="text" id="phone" certification-phone> 

태그에 certification-name 와 같은 사용자 정의 속성을 추가하여 값을 넣을 수 있다.
value , textContent 로만 추가 된다.

certification-name : 이름을 넣을 태그의 id
certification-birth : 생년월일을 넣을 태그의 id
certification-phone : 전화번호를 넣을 태그의 id

-->
<?php 
include $_SERVER['DOCUMENT_ROOT'] . '/portOne/portOne.php';
$portOne = new portOne();

$impkey = $portOne->IMP_KEY;  // IMP KEY
$channel = $portOne->CHANNEL; // CHANNEL KEY
$pg = $portOne->PG; // PG KEY

?>
<!-- PortOne SDK Import -->
<script src="https://cdn.iamport.kr/v1/iamport.js"></script>
<script>


let nameId = document.querySelector("[certification-name]");
let birthId = document.querySelector("[certification-birth]");
let phoneId = document.querySelector("[certification-phone]");

/**
 * 모바일일 경우 URL 파라미터에서 조회 아이디 가져오기
 * @returns {object} searchParam
 * @example
 */
const searchParam = new URLSearchParams(window.location.search);
const imp_uid = searchParam.get('imp_uid');
if(imp_uid){
    console.log("모바일 환경에서 인증 후 리다이렉트 됨");
    const response = getCertification(imp_uid);
    if(response.code === 0){
        const data = response.response;
        dataInit(data);
    } else {
        console.log(response.message);
        alert("본인인증 통신 중 오류가 발생했습니다.");
    }
}

/**
 * 본인인증
 * @param {string} nameId 이름을 넣을 input 태그의 id
 * @param {string} birthId 생년월일을 넣을 input 태그의 id
 * @param {string} phoneId 전화번호를 넣을 input 태그의 id
 * @param {string} redirectUrl 인증 후 리다이렉트 될 URL [모바일환경에서 popup:false(기본값) 인 경우 필수]
 */
function getAuthcication(){ 
    IMP.init('<?= $impkey ?>'); // 아임포트 관리자 페이지의 "내정보" > "계정" > "API 키" 에서 확인 가능
    // IMP.certification(param, callback) 호출
    IMP.certification(
        {
            // param
            channelKey:'<?= $channel ?>', // 발급받은 채널 키 
            m_redirect_url: window.location.href , // 모바일환경에서 popup:false(기본값) 인 경우 필수, 예: https://www.myservice.com/payments/complete/mobile
            pg : '<?= $pg ?>', // PG사
            popup: false, // PC환경에서는 popup 파라미터가 무시되고 항상 true 로 적용됨
        },
        function (rsp) {
            // callback
            if (rsp.success) {
            // 인증 성공 시 로직
                const imp_uid = rsp.imp_uid;
                const merchant_uid = rsp.merchant_uid; 
                const certificationResponse = getCertification(imp_uid);
                console.log(certificationResponse);
                if(certificationResponse.code === 0){
                    console.log(certificationResponse.response);
                    const data = certificationResponse.response;
                    // 초기화
                    dataInit(data);
                } else {
                    console.log(response.message);
                    alert("본인인증 통신 중 오류가 발생했습니다.");
                }
            } else {
            // 인증 실패 시 로직
                console.log(rsp);
                alert("본인인증에 실패했습니다.");
            }
        },
    );
}


/**
 * 인증 정보 조회
 * @param {string} imp_uid
 * @returns {object} response
 */
function getCertification(imp_uid){
    const url = "/portOne/getCertification.php";
    const method = "POST";
    const data = {'imp_uid' : imp_uid};
    const response = ajax(url, method, data);
    return response;
}

/**
 * 데이터 초기화
 * @param {object} data
 */
function dataInit(data){
    nameId.value = data.name;
    nameId.textContent = data.name;

    birthId.value = data.birthday;
    birthId.textContent = data.birthday;

    phoneId.value = data.phone;
    phoneId.textContent = data.phone;
}


/**
 * AJAX 요청
 * @param {string} url
 * @param {string} method
 * @param {object} data
 * @returns {object} response
 */
function ajax(url, method, data) { // note API 비동기 처리 Method
    let formData = new FormData();
    const request = new XMLHttpRequest();
    let response = "";

    if (typeof data !== 'undefined') {
        let keys = Object.keys(data); // JSON 객체 Key값 추출하기
        if (method === "GET") { // note GET 요청일 시 
            let queryString = keys.map(key => `${encodeURIComponent(key)}=${encodeURIComponent(data[key])}`).join('&');
            url += `?${queryString}`;
        } else if (method === "POST") { // note POST 요청일 시
            keys.map(key => formData.append(key, data[key])); // 데이터가 있을 경우 formData에 담아준다.
        }
    }

    request.open(method, url, false);
    request.addEventListener("readystatechange", (e) => {
        if (request.readyState == XMLHttpRequest.DONE) {
            if (request.status === 200) {
                response = JSON.parse(request.response);
            }
        }
    })
    request.send(formData);
    return response;
}



</script>