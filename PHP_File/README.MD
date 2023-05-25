### FILE UPLOAD 모듈 사용
#### - ClassAutoload 사용 
#### - Class 생성 후 사용 (공통)
- 공통 적용
    - File Class 공통 생성 후 사용
        - $fileClass = new File;
    - 다중 파일 업로드시 multiple 사용
    - 업로드 경로 유동적 변경 가능
    - move_uploaded_file 함수 사용
        - move_uploaded_file(tmp_name , 경로를포함한 저장할 파일 이름)
    - UUID로 사용여부를 Parameter로 받음
    - 압축 사용 ZipArchive 사용
---
#### HTML
    <form action="" method="POST" enctype="multipart/form-data">
        <input type="file" name="myfile" id+"myfile">
        <input type="file" name="multifile[]" id="multiful" multiple>
        <input type="submit" name="action" value="upload">
    </form>

    <?php
        $file = $_FILES['myfile'];
        $uploadDir = "/";
    ?>

|File Obj|File Content|
|:--:|:--:|
|$file['name']|파일 이름|
|$file['size']|파일 크기|
|$file['type']|파일 타입|
|$file['full_path"]|파일 경로|
|$file['tmp_name']|tmp폴더에 임의의 이름으로 저장되는 파일 이름|
---
## 단일 파일 업로드
    - 예시문
    $fileClass=>singleFileUpload($file, $uploadDir , $fileClass::IMAGE_USE , false);
> 설명
1. singleFileUpload(파일객체, 업로드 할 경로, 파일 타입 확인 , uuid 사용여부)
2. uuid true 시 File이름이 uuid로 변환되어 업로드, false일 경우 그대로 업로드
>업로드 과정
1. File 존재 여부 확인
2. File의 확장자를 저장
3. File의 uuid 사용여부 확인
4. 원본 파일의 이름을 가져옴
5. 파일이 존재하고 , 업도르하려는 파일타입이 맞을 때 file_exists함수 실행 후 업로드
6. 업로드 시 기폰 파일 이름 설정으로 '파일이름_2023_01_01_123010' 저장
---
## 다중 파일 업로드
    - 예시문
    $fileClass->multifulFileUpload($file, $uploadDir , $fileClass::APPLICATION_USE , false);
>설명
1. multifulFileUpload(배열로 이루어진 파일 객체, 업로드 할 경로 , 파일 타입 확인 , uuid 사용여부)
>업로드 과정
1. 파일 파리미터가 배열형태인지 확인
2. 반복문을 통해 singleUpload형식으로 파일 업로드
---
## 파일 압축 후 다운로드
    - 예시문
    $obj = new stdClass;
    $obj->path = "" 

    /** @param Type Class */
    $obj->file = array("test.png" , "test2.jpg"); || $obj->file = array("/upload/test.png" , "/upload/test2.jpg");

    /** @param Type Array */
    $obj = array("/upload/test.png" , "/upload/test2.jpg");
    $fileClass->zipDownload($obj);
> 설명
1. zipDownload(클래스 방식 OR 배열방식(경로 포함) , 필요시 경로 입력);
1. 빈 클래스를 생성 후 경로와 파일 이름을 담아줌
2. 경로가 필요할 경우 경로 삽입 , DB에따라 경로도 같이 INSERT 되는 경우 경로가 포함된 파일까지 유동적으로 다운로드
3. 클래스 나 배열형식으로 넘길 수 있지만 배열로 넘길 경우 경로까지 포함되서 전달
> 업로드 과정
1. parameter 로 넘어온 파일의 형식에 따라 졍보 저장
2. 압축파일의 이름을 생성 (현재 날짜로 생성, 2023_01_01_123010)
3. 알집 파일이 정상적으로 생성되었으면 파일을 삽입 후 파일 닫기
4. header로 해당 압축파일 다운로드 후 압축파일 삭제
---
## 파일 압축 후 업로드
    - 예시문
    $obj = new stdClass;
    $obj->path = "" 

    /** @param Type Class */
    $obj->file = array("test.png" , "test2.jpg"); || $obj->file = array("/upload/test.png" , "/upload/test2.jpg");

    /** @param Type Array */
    $obj = array("/upload/test.png" , "/upload/test2.jpg");
    $fileClass->zipUpload($obj);
    
    $file = $_FILES['multifile'];
    $fileClass = zipUpload($file , uploadDir);

> 설명
1. zipUpload(multiful 파일 , 필요시 경로);
2. multiple 바로 삽입 후 업로드 가능
- JS 비동기 처리 파일 업로드 가능
>업로드 과정
1. parameter 로 넘어온 파일의 형식에 따라 정보 저장
2. 압축파일 생성 (현재 날짜로 생성 , 2023_01_01_123010)
3. tmp폴더에 임시 파일이 있을 경우 알집에 파일 삽입
4. 경로가 포함되어있으면 해당 경로에 알집 업로드 , 직접 넘길 시 넘긴 경로에 생성

---

**FILE STATUS**
|ERROR IDNEX|ERROR CODE|ERROR MESSAGE|
|:--:|:--:|:--:|
|0|UPLOAD_ERR_OK|"업로드가 성공적으로 이루어졌씁니다."|
|1|UPLOAD_ERR_INI_SIZE|"php.ini에 설정된 (upload_max_filesize) 최대 파일크기가 초과되었습니다."|
|2|UPLOAD_ERR_FROM_SIZE|"HTML 폼에 설정된 (max_file_size) 최대 파일크기 초과"|
|3|UPLOAD_ERR_PARTIAL|"업로드 된 파일이 부분적으로만 업르도 되었습니다."|
|4|UPLOAD_ERR_NO_FILE|"업로드 할 피일이 없습니다."|
|6|UPLOAD_ERR_NO_TMP_DIR|"웹서버에 임시폴더가 없습니다."|
|7|UPLOAD_ERR_CANT_WRITE|"파일을 사용할 수 없습니다."|
|8|UPLOAD_ERR_EXTENSION|"PHP 확장기능에 의한 업르도가 중단되었습니다."|
---
<br>

**EXTENSION 항목**
|OPTION|NAME|EXTENSION|
|:--:|:--:|:--:|
|IMAGE_ALL|모든 이미지|"tiff, jfif, bmp, gif, svg, png, jpeg, svgz, jpg, webp, ico, xbm, dib, pip, apng, tif, pjpeg, avif"|
|VIDEO_ALL|모든 비디오|"ogm, wmv, mpg, webm, ogv, mov, asx, mpeg, mp4, m4v, avi"|
|AUDIO_ALL|모든 오디오|"wma, flac, webm, oga, mp3, mp4, ogg, wav, opus, amr, aac"|
|APPLICATION_ALL|모든 애플리케이션|"zip, crt, docx, xlsx, ppt, xul, apk, tar, ai, ps, rss, p7s, woff, p7z, p7c, pptx, pdf, rtf, bin, p7m, swf, xhtm, dot, swl, doc, xls, json, m3u8, epub, gz, com, rdf, js, cer, xhtml, tgz, xht, eps, crx, wasm, exe"|
|IMAGE_USE|자주 사용하는 이미지|"bmp, gif, png, svg, jpeg, jpg"|
|VIDEO_USE|자주 사용하는 비디오|"wmv, mpg, mp4, avi"|
|AUDIO_USE|자주 사용하는 오디오|"mp3, mp4, flac, wav"|
|APPLICATION_USE|자주 사용하는 애플리케이션|"docx, ppt, xlsx, xls, pptx, pdf"|
---
<br>

**ERROR 항목**
|ERROR NAME|ERROR CONTENT|ERROR MESSAGE|
|:--:|:--:|:--:|
|FILE EXTENSTION ERROR|파일이 없거나 파일 형식이 맞지 않을 때|"파일이 없거나 파일 형식이 맞지 않습니다."<br>업로드 가능한 확장 : "" <br> 실제 업로드 된 확장자 : "" "|
|DATA TYPE ERROR|파일 파라미터가 형식에 맞지 않을 때|"파일 형식이 맞지 않습니다.<br>가능한 파라미터 타입 : ""<br>받은 파라미터 타입 : "" "|
|FILE EXITSTS ERROR|알집에 파일 추가중 파일이 존재하지 않을 경우|"알집파일에 파일을 추가하는 도중 오류가 발생했습니다."|
|FILE EXTENSTION CHECK ERROR|설정된 확장자 명이 아닐 경우|"설정된 확장자 명을 넣어주세요"|
|UPLOAD PATH ERROR|업로드 경로가 존재하지 않을 경우|"업로드 경로가 존재하지 않습니다."|
|MULTIFUL FILE TYPE ERROR|"멀티플 업로드할 때 배열로 넘어오지 않을 때|"파일 파라미터가 배열이 아닙니다.<br>파일 타입 : "" "|
|ZIP CREATE ERROR|알집파일을 만들 때 오류가 일어날 때|"알집 파일을 만드는데 오류가 발생했습니다."|
|NOT FILE|파일이 없을 경우|"파일이 없습니다.<br>파일 : "" <br> 파일 타입 : "" "|
|NOT PATH ERROR|파일 경로가 없을 경우|"파일의 경로가 존재하지 않습니다."|
|NONE|설정된 에러 항목이 없을 시|설정된 에러 항목이 없습니다.|

---



