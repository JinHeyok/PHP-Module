<?php


class MySQL{ // 사용시 클래스 (AUtO) 로드 필요

    const HOST = "localhost"; //host 정보 
    const USER = "root"; // 아이디 정보 
    const PASS = "root"; // 패스워드 정보 
    const DB = "react_project"; // DB명 정보

    private static $servername = self::HOST;
    private static $username = self::USER;
    private static $password = self::PASS;
    private static $dbname = self::DB;

    const MYSQL = "MySQL";
    //session에 넣을 MySQL Object key값

    private static $timezone = "Asia/Seoul"; //시간설정 
    private static $charset = "utf8mb4"; //인코딩 설정 

    private $connection = null;


    function __construct(){//기본생성자 생성
        // session_start(); // 로컬 테스트시 사용
        try {
            // DB연결
            if ($_SESSION[self::MYSQL] == "" || $_SESSION[self::MYSQL] == null) {

                $conn = mysqli_connect(self::$servername, self::$username, self::$password, self::$dbname);
                $conn->set_charset(self::$charset);
                // $conn->query("SET time_zone='" . self::$timezone . "'"); //로컬에서는 주석처리
                if ($conn) {
                    $_SESSION[self::MYSQL]  = $conn;
                    $this->connection = $conn;
                } else {
                    $type = "CONNECTION ERROR";
                    self::error_log($type , mysqli_connect_error());
                }
            } else {
                $conn = $_SESSION[self::MYSQL];
            }

            $this->connection = $conn;
        } catch (mysqli_sql_exception $error) {
            $type = "CONNECTION ERROR";
            self::error_log($type, $error->getMessage());
            return false;
        }
    }

    function __destruct(){ //생성자 제거
        // unset($_SESSION[self::MYSQL]); //로컬 테스트시 사용
        $this->clear();
    }

    function clear(){//DB 해제 
        if ($_SESSION[self::MYSQL] == "" || $_SESSION[self::MYSQL] == null) {
            session_unset(self::MYSQL);
        }
    }

    function bindType(array $type){

        $bind = "";

        foreach ($type as $item) {
            if (gettype($item) === "string") {
                $bind .= "s";
            } else if (gettype($item) === "integer") {
                $bind .= "i";
            } else {
                return false;
            }
        }
        return $bind;
    }

    function getParameter(string $bind){
        $parameter = "";
        for ($index = 0; $index < strlen($bind); $index++) {
            $parameter .= "?";
        }
        $parameter = str_split($parameter, 1);
        $parameter = implode(",", $parameter);
        return $parameter;
    }

    function countCheck($column = array()){

        $text = "";
        $aliasCount = "";

        if(count($column) == 1){
        
            foreach($column as $item){

                $keyName = strtolower(key($item)); //대문자로 alias를 쓸 경우 소문자로 변경
                $value = $item[key($item)];

                $objCount = new stdClass;

                if($keyName != "count"){
                    
                    $countStr = substr($keyName , 6 , 1);//count(뒤부터 시작되는 언어를 가져옴
                    $countSplit = explode($countStr , $keyName);//자른 언어를 기준으로 배열로 자른다.


                    $firstText = $countSplit[0] == "count(" ? "count(" : "";

                    if($countStr == "*"){//전체일시 괄호만
                        $lastText = ")";
                    }else{
                        $lastText = strpos($countSplit[1] , ")") == true ? ")" : "" ;//')'포함시 ')'입력
                    }
        
                    $text =  $firstText . $lastText;

                }else{
                    $aliasCount = true;
                }

            }

            if($text  == "count()" || $aliasCount){//count함수가 있으면 true;
                if($value != 0){
                    $objCount->count = $value;
                }else{
                    $objCount->count = null;
                }
                return $objCount;
            }else{
                return false;
            }

        }else{
            return false;
        }
    }

    function data_log($result){
        print(sprintf("<pre style='background-color : 006600; color : white; font-family : fangsong; font-weight : bold; padding : 0.2rem;'>%s</pre>" , print_r($result , true)));
    }

    function error_log($type, $error_message = ""){    

        /** 
        * @param type NO DELETE CHANGED : 삭제된 데이터가 없을 경우 
        * @param type NO UPDATE CHANGED : 변경된 데이터가 없을 경우
        * @param type NO SELECT DATA : 검색된 데이터가 없을 경우 
        * @param type NONE COUNT : 바인드수와 데이터수가 동일하지 않을 경우
        * @param type NONE TABLE : 테이블명이 존재하지 않을 경우 
        * @param type NONE INSERT DATA : 정상실행이지만 추가된 데이터가 없을 경우
        * @param type SQL ERROR : SQL 에러일 경우
        * @param type CONNECTION ERROR : 연결정보 오류일 경유
        * @param type NONE : 설정된 에러항목이 없음
         */

        $text = "";
        $errorType = "";
        
        switch ($type) {
            case "NO DELETE CHANGED":
                $text = "정상적으로 실행되었지만 삭제된 데이터가 없습니다.";
                $errorType = "NO DELETE CHANGED";
                break;
            case "NO UPDATE CHANGED":
                $text = "정상적으로 실행되었지만 변경된 데이터가 없습니다.";
                $errorType = "NO UPDATE CHANGED";
                break;
            case "NO SELECT DATA":
                $text = "정상적으로 실행되었지만 검색된 데이터가 없습니다.";
                $errorType = "NO SELECT DATA";
                break;
            case "NONE COUNT":
                $text = "바인드수와 데이터수가 동일하지 않습니다.";
                $errorType = "NONE COUNT";
                break;
            case "NONE TABLE":
                $text = "테이블명이 존재하지 않습니다.";
                $errorType = "NONE TABLE";
                break;
            case "NONE INSERT DATA":
                $text = "정상적으로 실행되었지만 추가된 데이터가 없습니다.";
                $errorType = "NONE INSERT DATA";
                break;
            case "SQL ERROR":
                $text = $error_message;
                $errorType = "SQL ERROR";
                break;
            case "CONNECTION ERROR":
                $text = $error_message;
                $errorType = "CONNECTION ERROR";
                break;
            default:
                $text = "설정된 에러항목이 없습니다.";
                $errorType  = "NONE";
                break;
        } 
        // $script = '<script>console.log("' . $errorType  . ' : '  . $text . '");</script>';
        // print_r($script);
        print(sprintf("<pre style='background-color : 330000; color : white; font-family : fangsong; font-weight : bold; padding : 0.2rem; white-space : pre-wrap;'>%s</pre>" , print_r($errorType . " : " .  $text , true)));
    }

    function debug_log($message){
        //디버깅용 array, object 출력할 수도 있으므로 JSON으로 출력
        $script = "<script>console.log('Debugging : " . json_encode($message) . "');</script>";
        print_r($script);
    }

    function insert($obj){

        //$obj->컬림명 = 데이터
        //$obj->컬럼명 = 데이터
        //$obj->talbe = table명 꼭 테이블명 삽입 

        $column = array_keys((array)$obj);//키값을 배열로 전환
        $column = str_replace(",table", "", implode(",", $column));//테이블 기값 제거 

        $value = array_values((array)$obj);//value 값들 배열로전환
        $value = array_filter(str_replace($obj->table, "",  $value));//테이블 명 제거 동시에 빈값 제거 

        $bind = self::bindType($value);//bind문장 가져오기
        $parameter = self::getParameter($bind);//?문장 가져오기
        $questionCount = substr_count($parameter , "?");//?수 가져오기

        $query = "INSERT INTO " . $obj->table . " ( " . $column . ") VALUES (" .  $parameter . ");";
        //실행될 쿼리 입력

        if(strlen($bind) != $questionCount){//바인드와 데이터가 맞지 않을경우 
            $type = "NONE COUNT";
            self::error_log($type);
            return false;
        }

        if($obj->table == "" || $obj->table == null){//테이블명을 뺴먹었을 경우 
            $type = "NONE TABLE";
            self::error_log($type);
            return false;
        }

        try {

            $statement = $this->connection->prepare($query);//쿼리 준비
    
            if ($statement) {
    
                if ($bind != "" && sizeof($value) > 0) {
                    $statement->bind_param($bind, ...$value);//bind 삽입
                }
    
                $statement->execute();//실제 쿼리실행
    
                if ($statement->insert_id || $statement->affected_rows) {
                    $statement->close(); //쿼리해제
                    return true;
                }else{
                    self::error_log("NONE INSERT DATA");
                    $statement->close();
                    return false;
                }
                
            }
   
        } catch (mysqli_sql_exception $error) {
            $type = "SQL ERROR";
            self::error_log($type, $error->getMessage());
            $statement->close();
            return false;
        }

    }


    function select(string $query, $data = array()){//쿼리와 삽입할 데이터(배열)

        $bind = self::bindType($data);//data 타입에 따라 bind문장 생성
        $questionCount = substr_count($query, "?");//?수를 구함

        if(strlen($bind) != $questionCount){ //바인드와 데이터수가 맞지 않을 때 
            $type = "NONE COUNT";
            self::error_log($type);
            return false;
        }

        try {

            $statement = $this->connection->prepare($query);// 해당 쿼리를 입력 
    
            if ($statement) {
    
                if ($bind != "" && sizeof($data) > 0) { //바인드(데이터 타입) 설정이 존재하고 , 넣을 data가 있을 시
                    $statement->bind_param($bind, ...$data);// bind_param()에 데이터 타입과 데이터를 넣어준다.
                }
    
                $statement->execute();// Query 실행
                $statement = $statement->get_result();// SELECT한 값을 담아준다. 
    
            } else {
                print_r($statement->error_log());
            }
    
            $list = array();//데이터를 담아줄 배열 생성
    
            while ($result = $statement->fetch_assoc()) {// 배열 형식의 데이터를 새로운 배열에 담아준다. 
                array_push($list, $result);
            }

            $statement->close(); //실행 쿼리 지워주기

        } catch (mysqli_sql_exception $error) {
            $type = "SQL ERROR";
            self::error_log($type , $error->getMessage());
            $statement->close();
            return false;
        }
        
        $objList = array();//객체 리스트를 담을 배열 생성
        $listCount = self::countCheck($list); //count를 구하는지 체크

        if(count($list) == 0 && $listCount->count == null){ //0개여도 return
            $type = "NO SELECT DATA";
            self::error_log($type);
            return false;
        }


        if (count($list) > 1) { //배열의 길이가 1보다 낮으면 객체로 리턴 아닐 시 배열로 리턴
            //리스트
            foreach ($list as $key => $item) {
                $obj = new stdClass;
                $column = array_keys($item);//key이름만 배열 형태로 전환
                
                foreach ($column as $columnName) {
                    $obj->$columnName = htmlspecialchars($item[$columnName]);
                    //객체->키이름 = value값을 담아준다. htmlspecialchars : XSS 방지
                }
                $objList[$key] = $obj;
                //객체 리스트를 만들어준다.
            }
            return $objList;

        } else {
            //단일
            foreach ($list as $key => $item) {
                $obj = new stdClass;
                $column = array_keys($item);//key이름만 배열 형태로 전환
                
                $countCheck = self::countCheck($list);//컬럼이 count인지 아닌지 확인

                foreach ($column as $columnName) {
                    if($countCheck->count > 0){//갯수를 구할 시 갯수만 리턴
                         $count = htmlspecialchars($item[$columnName]);
                         return $count; //실제 갯수만 리턴
                    }else{
                        $obj->$columnName = htmlspecialchars($item[$columnName]);
                        //객체->키이름 = value값을 담아준다. htmlspecialchars : XSS 방지
                    }
                }
                return $obj;
            }
        }
    }


    function update(string $query, $data = array()){

        $bind = self::bindType($data);
        $questionCount = substr_count($query , "?");//?수를 가져옴 

        if(strlen($bind) != $questionCount){ //바인드수와 데이터수가 동일하지 않을 경우 
            $type = "NONE COUNT";
            self::error_log($type);
            return false;
        }

        try {

            $statement = $this->connection->prepare($query);
    
            if ($statement) {
    
                if ($bind != "" && sizeof($data) > 0) {
                    $statement->bind_param($bind, ...$data);
                }

                $statement->execute();

                if($statement->affected_rows == 0 ){ //변경된 데이터가 없을 때 
                    $type = "NO UPDATE CHANGED";
                    self::error_log($type);
                    return true;
                };

                $statement->close();
                return true;

            } 
            
        } catch (mysqli_sql_exception $error) {
            $type = "SQL ERROR";
            self::error_log($type, $error->getMessage());
            $statement->close();
            return false;
        }

    }

    function delete($query, $data = array()){

        //obj형일 경우 
        //obj->whereData // 삭제될 데이터 array()형식
        //obj->whereColumn //조건 컬럼 array()형식
        //obj->whereType //and, or 리스트 array()형식
        //obj->table // 테이블명

        $bind = ""; //바인드를 담을 변수
        $deleteQuery = ""; //실행될 진짜 쿼리
        $deleteData = ""; //반이드할 데이터를 담을 변수
        $questionCount = 0; // 컬럼에 총수 
        $obj = $query; // 오브젝트이름 변경

        if(gettype($query) === "string"){ //문장형으로 실행
            $bind = self::bindType($data);
            $deleteQuery = $query; 
            $questionCount = substr_count($deleteQuery , "?"); //? 문장 갯수 
            $deleteData = $data;
        }
        
        if(gettype($obj) === "object"){//객체형으로 실행

            $deleteData = $obj->whereData; //삭제될 데이터
            $objColumn = $obj->whereColumn; //WHERE 뒤 컬럼명들
            $objType = $obj->whereType; // and 또는 or 
            $bind = self::bindType($deleteData); //바인딩 삽입
            $questionCount = count($obj->whereColumn); // 컬럼의 수 입력 
            
            $deleteQuery = "DELETE FROM " . $obj->table . " WHERE " . $objColumn[0] . " = ? ";
            //테이블명과 처음 컬럼명만 입력 
            
            for($j=0; $j < count($objType);  $j++){
               $deleteQuery .= $objType[$j] . " " . $objColumn[$j] . " = ? ";
               //맨앞은 where 컬럼은 이미 들어가 있으므로 type의 갯수에 맞춰서 삽입
            }

            if($obj->table == "" || $obj->table == null){ //테이블명을 빼먹을 경우 console에 출력 
                $type = "NONE TABLE";
                self::error_log($type);
                return false;
            }

        }
        
        if(strlen($bind) != $questionCount){ //바인드와 데이터수가 맞지 않을경우 consoled에 출력 
            $type = "NONE COUNT";
            self::error_log($type);
            return false;
        }

        try {

            $statement = $this->connection->prepare($deleteQuery);

            if ($statement) {
    
                if ($bind != "" && sizeof($deleteData) > 0) {
                    $statement->bind_param($bind, ...$deleteData);
                }
    
                $statement->execute(); //쿼리실행
    
                if ($statement->insert_id || $statement->affected_rows) {
                    $statement->close();
                    return true;
                }else{
                    $type = "NO DELETE CHANGED"; //정상정으로 실행되었지만 삭제된게 없을 경우 
                    self::error_log($type);
                    return true;
                }
            } 
            
        } catch (mysqli_sql_exception $error){//SQL exception 발동
            $type = "SQL ERROR";
            self::error_log($type, $error->getMessage());
            $this->connection->close();
            return false;
        }
    }
}