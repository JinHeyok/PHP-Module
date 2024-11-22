<?php 
/**
 * @api S3 파일 다운로드 Proxy
 * @version 1.0.0
 * @since 2024.11.22
 * @param path: 다운로드할 파일 경로 [Ex. dev/test/example.jpg] Key값은 S3에 저장된 파일 경로
 * location.href GET 페이지 요청으로 다운로드 가능
 */
	include($_SERVER['DOCUMENT_ROOT'].'S3Uploader.php'); // S3Uploader.php 경로

    $path = $_GET['path']; // 다운로드할 파일 경로
    $S3 = new S3Uploader(); // S3Uploader 인스턴스 생성
    $S3->downloadFile($path); // 파일 다운로드
?>