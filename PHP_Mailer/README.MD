### Mail Module 사용
#### - ClassAutoload 사용 
#### - Class 생성 후 사용 (공통)
- 공통 적용
    - Class에서 Host, ID, PW, Port 설정 필요
    - Mail Class 생성
    - PHP_Mailer 사용
--- 
## - 메일 전송
     - 예시문
     $mail = new Mail;
     $sender = "보내는 이의 이름";
     $reciver = "받는 이의 메일";
     $title = "메일 제목";
     $content =
     <<<EOT
     <p>테스트 메일입니다.</p>
     EOT 

     $mailStatus = $mail->mail($sender ,  $reciver , $title , $content);

> 설명
1. mail(보내는 이의 이름 , 받는 이의 이메일 , 메일 제목 , 메일 내용);
2. 메일이 성공적으로 보내졌을 시 $mailStatus 는 true 아닐 시 false이다.
- $reciver 는 Array 형태로 담아서 보내면 여러명에게 전송할 수 있다.
> 전송 과정
1. PHP_Mailer폴더에 PHPMailer.php , SMTP.php , Exception.php 클래스 파일 읽기
2. use 각 Class name 지정
3. 보내는 이의 계정정보, 호스트, 포트번호 설정
    - 사용하는 HOST, PORT는 유동적으로 변경
4. 한글이 깨지지 않게 CharSet은 PHPMailer::CHARSET_UTF8로 지정
5. secure 정보 설정 ssl , tls
5. 여러명에게 보낼 수 있어 배열이면 보내는 사람을 담아주고 아닐 시 1명만 담아준다.
6. HTML을 사용한다 isHTML(true) 설정
7. $mail->send(); 시 실제 전송
---