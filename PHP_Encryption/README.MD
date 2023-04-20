### AES Module 사용
#### - ClassAutoload 사용 
#### - Class 생성 후 사용 (공통)
- 공통 적용
    - KEY 값 동일 , iv(Intialization vector) 쉽게 제 2의 키라고 명칭 동일
    - aes-256-cbc 사용
--- 
## - 문자열 암호화
    - 예시문
    $aesClass = new AES;
    $text = "test";
    $stringEncode = $aesClass->AESEncode($text);
    $stringDecode = $aesClass->AESDecode($stringEncode);

    결과
    암호화 : QIz31aLkS5spVu0Y4N+LFQ==
    복호화 : test
>설명
- 암호화
    1. 파라미터가 넘어오면  base64_encode로 암호화 (한글 인코딩 문제)
    2. base64_encode 된 값을 openssl_encrypt()함수에서 암호화
- 복호화
    1. 암호화 된 데이터를 먼저 openssl_decrypt()로 복호화 진행
    2. 처음 암호화 했을 때 base64로 encode 되있기 때문에 base64_decode 진행
## - 배열 암호화
    - 예시문
    $aesClass = new AES;
    $array = array("test" , "test2");
    $arrayEncode = $aesClass->AESEncode($array);
    $arrayDecode = $aesClass->AESDecode($arrayEncode);

    결과
    암호화 : Array ( [0] => QIz31aLkS5spVu0Y4N+LFQ== [1] => Yg9DJ2j6qWz3kaTNtjbfwQ== )
    복호화 : 복호화 : Array ( [0] => test [1] => test2 )
>설명
- 암호화
    1. 넘어온 파라미터가 배열이면 반복문으로 value값 확인 
    2. 새로운 배열을 생성하고 생성된 배열안에 문자열 암호화 같은 방법으로 배열에 넣어준다.
- 복호화
    1. 암호화 방법에서 복호화식으로만 변경 
## - 객체 암호화
    - 예시문
    $obj = new stdClass;
    $obj->test = "test1";
    $obj->test2 = "test2";
    $obj->test3 = "test3";

    $objEncode = $aesClass->AESEncode($obj);
    $objDecode = $aesClass->AESDecode($objEncode);

    결과 
    암호화 : stdClass Object ( [QIz31aLkS5spVu0Y4N+LFQ==] => AWQAA/nHNo55fwih0mlbyA== [Yg9DJ2j6qWz3kaTNtjbfwQ==] => Yg9DJ2j6qWz3kaTNtjbfwQ== [haMiMJNv//ZZi/dKHl9MlA==] => haMiMJNv//ZZi/dKHl9MlA== )
    복호화 :  stdClass Object ( [test] => test1 [test2] => test2 [test3] => test3 )
>설명 
- 암호화
    1. 넘어온 파라미터가 object이면 key값만 배열형태로 저장
    2. 반복문을 돌려 value값 따로 암호화 key값을 따로 암호화
    3. 새로운 빈 Class를 생성해 Class->암호화된 key값 = 암호화된 value 값으로 저장
- 복호화
    1. 암호화의 방법에서 복호화식으로만 변경

---