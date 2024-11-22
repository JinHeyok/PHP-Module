<?php

require 'vendor/autoload.php';  // AWS SDK 로드
/**
 * @api S3 파일 업로드 및 다운로드
 * @package S3Uploader
 * @version 1.0.0
 * @since 2024.11.22
 * 
 * @todo Setting 방법
 * 1. 구성 필요 https://getcomposer.org/download/ 페이지에서 Composer 다운로드
 * 설치 방법
 * php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
 * php -r "if (hash_file('sha384', 'composer-setup.php') === 'dac665fdc30fdd8ec78b38b9800061b4150413ff2e3b6f88543c636f7cd84f6db9189d43a81e5503cda447da73c7e5b6') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
 * php composer-setup.php
 * php -r "unlink('composer-setup.php');"
 * sudo mv composer.phar /usr/local/bin/composer     
 * 
 * 2. Composer 설치 후, 원하는 경로에 composer require aws/aws-sdk-php 명령어로 AWS SDK 설치
 * 
 */

class S3Uploader {

    private $s3Client;
    const BUCKET_NAME = "{}";  // Bucket 이름
    
    // AWS 설정: QA 및 PROD 환경을 구분하여 설정
    private function getAwsConfigs() {
        // 현재 URL의 호스트명 가져오기
        $currentUrlHost = $_SERVER['HTTP_HOST'];

        $DEV_ACCESS_KEY = "{}"; // Dev Access Key
        $DEV_SECRET_KEY = "{}"; // Dev Secret Key
        $PROD_ACCESS_KEY = "{}"; // PROD Access Key
        $PROD_SECRET_KEY = "{}"; // PROD Secret Key

        // QA 서버 URL일 경우
        if (strpos($currentUrlHost, '{URL 분기처리 [Ex. example.com]}') !== false) {
            return [
                'version' => 'latest',
                'region' => 'ap-northeast-2', // Region 설정
                'credentials' => [
                    'key' => $DEV_ACCESS_KEY,   // Dev Access Key
                    'secret' => $DEV_SECRET_KEY,  // Dev Secret Key
                ]
            ];
        } else {
            return [
                'version' => 'latest',
                'region' => 'ap-northeast-2', // Region 설정
                'credentials' => [
                    'key' => $PROD_ACCESS_KEY,  // PROD Access Key
                    'secret' => $PROD_SECRET_KEY,  // PROD Secret Key
                ]
            ];
        }
    }

    public function __construct() {
        // AWS SDK 설정
        $awsConfigs = $this->getAwsConfigs();  // 현재 환경에 맞는 설정 가져오기

        // AWS SDK 인스턴스 생성
        $sdk = new Aws\Sdk([
            'version' => $awsConfigs['version'],
            'region' => $awsConfigs['region'],
            'credentials' => $awsConfigs['credentials'],
        ]);

        $this->s3Client = $sdk->createS3();
    }

    // 현재 URL을 기반으로 업로드할 디렉토리 선택 (prod 또는 dev)
    private function getUploadDirectory() {
        // 현재 URL에서 호스트명 가져오기
        $currentUrlHost = $_SERVER['HTTP_HOST'];

        // URL에 따라 경로를 결정
        if (strpos($currentUrlHost, '{URL 분기처리 [Ex. example.com]}') !== false) {
            return 'dev/';  // dev 서버 URL이면 dev/ 경로 사용
        } else {
            return 'prod/';  // prod 서버 URL이면 prod/ 경로 사용
        }
    }

    /** 
     * @api 단일 파일 업로드
     * @param $file : 업로드할 파일 정보
     * @param $subUrl : 업로드할 파일의 하위 경로 EX['winter_star2024/{folder}/'] 처음 '/'는 필요없음 마지막 '/'는 필요함
     * @param $fileName : 업로드할 파일 이름
     */
    public function uploadSingleFile($file , $subUrl , $fileName) {
        // 경로를 URL에 따라 다르게 설정
        $directory = $this->getUploadDirectory();
        $filePath = $directory . $subUrl . $fileName;  // 업로드할 파일 경로
        $fp = fopen($file['tmp_name'], 'r');  // 파일을 읽기 모드로 열기

        try {
            // 파일 업로드
            $result = $this->s3Client->putObject([
                'Bucket' => self::BUCKET_NAME,
                'Key' => $filePath,
                'Body' => $fp
            ]);
            
            fclose($fp);  // 파일 닫기

            $metadata = $result->get('@metadata');  // @metadata 정보 가져오기
            // $this->dataLog($metadata);  // 로그 출력

            if ($metadata['statusCode'] == 200) {
                return [
                    'state' => true,
                    'message' => '파일 업로드 성공',
                    'url' => $metadata['effectiveUri']
                ];
            } else {
                return ['state' => false, 'message' => '파일 업로드 실패'];
            }
        } catch (Exception $e) {
            return ['state' => false, 'message' => '오류: ' . $e->getMessage()];
        }
    }

     /** 
     * @api 다중 파일 업로드
     * @param $file : 업로드할 파일 정보
     * @param $subUrl : 업로드할 파일의 하위 경로 EX['winter_star2024/{folder}/'] 처음 '/'는 필요없음 마지막 '/'는 필요함
     * @param $fileName : 업로드할 파일 이름
     */
    public function uploadMultipleFiles($files , $subUrl , $fileName) {
        $uploadResults = [];
        
        foreach ($files['tmp_name'] as $key => $tmpName) {
            $fileName = $fileName . "_" . [$key];
            $file = [
                'name' => $fileName,
                'tmp_name' => $tmpName
            ];
            $uploadResults[] = $this->uploadSingleFile($file , $subUrl , $fileName);  // 각 파일을 업로드
        }
        
        return $uploadResults;
    }

    // 데이터 로그 출력 (디버그용)
    private function dataLog($result) {
        print(sprintf("<pre style='background-color : #006600; color : white; font-family : fangsong; font-weight : bold; padding : 0.2rem;  z-index: 1000; position:relative; font-size:18px;'>%s</pre>", print_r($result, true)));
    }

  /** 
     * @api S3에서 파일 다운로드 (브라우저에서 다운로드 가능하도록 처리)
     * @param $fileKey : S3에서 다운로드할 파일의 키 (경로 포함)
     * @return array 다운로드 성공 또는 실패 메시지
     * Get 요청 방식으로 파일 다운로드
     */
    public function downloadFile($fileKey) {
        try {

            // S3에서 파일 다운로드
            $result = $this->s3Client->getObject([
                'Bucket' => self::BUCKET_NAME,
                'Key' => $fileKey,
            ]);


            // 파일의 MIME 타입 설정
            $contentType = $result['ContentType']; // 예: application/pdf, application/octet-stream 등
            $contentLength = $result['ContentLength']; // 파일 크기
            $fileName = basename($fileKey); // 다운로드할 파일 이름

            // 파일 다운로드를 위한 헤더 설정
            header("Content-Type: $contentType");
            header("Content-Disposition: attachment; filename=\"$fileName\"");
            header("Content-Length: $contentLength");

            // 파일 내용을 출력하여 다운로드
            // var_dump($result['Body']);
            echo $result['Body'];
 
            exit;  // 스크립트 종료 (더 이상 진행하지 않도록)
        } catch (Exception $e) {
            // 오류 발생 시 JSON 반환
            header('Content-Type: application/json');
            echo json_encode([
                'state' => false,
                'message' => '오류: ' . $e->getMessage()
            ]);
            exit;
        }
    }

}

?>
