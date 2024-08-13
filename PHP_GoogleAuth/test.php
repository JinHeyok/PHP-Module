<?php
	
	include_once("./GoogleAuthenticator.php");

	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	
	
	$ga = new PHPGangsta_GoogleAuthenticator();
	
	// 시크릿 키는 DB에 저장해야한다.
	// OTP를 생성할 고유 시크릿 키
	// $secret = $ga->createSecret(); // 시크릿키 생성

	// 16자리의 시크릿 키 지정
	$secret = 'OQB6ZZGYHCPSX4AK'; //테스트를 위한 고정 시크릿키
	
	// 회원,유저의 아이디와 시크릿 키를 이용하여 QR코드 생성
	$qrCodeUrl = $ga->getQRCodeGoogleUrl($user, $secret);
	
	// 시키리킷를 가져와 비교할 OTP 코드를 가져온다.
	$oneCode = $ga->getCode($secret);

?>

2단계 OTP인증 모듈 작업을 위한 테스트 입니다.<br>

Google OTP 앱을 받으신 다음 아래 QR코드를 스캔하세요.<br>

만약, QR코드가 스캔되지 않으시면 제공키에 <?php echo $secret; ?>를 입력 하세요.

<hr>

<?php
	
	echo "비교하실 OTP 코드: <b style='color:#ff0000'>".$oneCode."</b><hr>"; // 구글에서 보내주는 OTP 번호

?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<input type="text" name="userCode" value="" class="">

<input type="button" name="btnCheck" value="확인" class="">

<!-- QR코드 이미지를 불러온다. -->
<img src="<?php echo $qrCodeUrl; ?>" alt="" />

<script type="text/javascript">
	
	$(document).ready(function(){
		
		$('input[name="btnCheck"]').click(function(){
			
			var inputVal = $('input[name="userCode"]').val(); // 사용자가 입력한 OTP Number
			
			var optCode  = '<?=$oneCode?>'; // OTP 앱에서 보이는 OTP Number
			
			if(inputVal != optCode){
				
				alert('OTP 번호를 확인해주세요.');
				
				location.reload();
				
			}else{
				
				alert('로그인 성공');
				
			}
			
		});
		
	})

</script>

