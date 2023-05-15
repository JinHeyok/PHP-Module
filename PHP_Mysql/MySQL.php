<?php


class MySQL{ // 사용시 클래스 (AUtO) 로드 필요

    const HOST = "localhost"; //host 정보 
    const USER = "root"; // 아이디 정보 
    const PASS = "root"; // 패스워드 정보 
    const DB = "sql_study"; // DB명 정보

    private static $servername = self::HOST;
    private static $username = self::USER;
    private static $password = self::PASS;
    private static $dbname = self::DB;

    const MYSQL = "MySQL";
    //session에 넣을 MySQL Object key값
    
    private static $timezone = "Asia/Seoul"; //시간설정 
    private static $charset = "utf8mb4"; //인코딩 설정 (이모지로 인해 4byte)
    
    private $connection = null; //DB 객체 담기
    private $errorLogStatus = true; //에러로그 사용 여부 true : 사용 , false : 미사용

    private $tranasctionLogStatus = true; //트랜잭션 상태 출력 true : 사용 , false : 미사용
    private $transaction = false; //트랜잭션 사용 시 true 전환 연결 닫지 않기
    private $transactionStatus = false; //트랜잭션 실행 상태
    

    function __construct(){//기본생성자 생성
        session_start();
        try {
            // DB연결
            if (empty($_SESSION[self::MYSQL])) {

                $conn = mysqli_connect(self::$servername, self::$username, self::$password, self::$dbname);
                $conn->set_charset(self::$charset);
                $conn->query("SET GLOBAL time_zone = '" . self::$timezone . "';"); //시간 설정 
                $conn->query("SET names '" . self::$charset . "';");

                /**
                 * @property request character_set_client : MySQL 클라이언트의 기본이 되는 캐릭터셋, 클라이언테엇 서버로 전송하는 SQL문에 대한 인코딩 
                 * @property request character_set_connection : 클라이언트로부터 수신한 Character set introducer가 없는 리터럴에 대한 기본 캐릭터 셋을 의미
                 * @property request character_set_results : Client가 데이터를 조회할 경우, Server는 해당 캐릭터 셋으로 인코딩하여 전송한다.
                 */
                //위 세가지를 UTF8MB4로 설정 SET names 사용시 3개 설정을 동시에 설정 가능 

                if ($conn) {
                    $_SESSION[self::MYSQL]  = $conn;
                    $this->connection = $conn;
                }

            } else {
                $conn = $_SESSION[self::MYSQL];
            }

            $this->connection = $conn;

        } catch (mysqli_sql_exception $error) {
            if(strpos($error->getMessage() , "Unknown or incorrect time zone: 'Asia/Seoul'") !== false){
                $type = "TIME_ZONE ERROR";
                self::error_log($type, $error->getMessage());
                return false;
            }else{
                $type = "CONNECTION ERROR";
                self::error_log($type, $error->getMessage());
                return false;
            }
        }
    }

    function __destruct(){ //생성자 제거, DB 해제
        if (!empty($_SESSION[self::MYSQL])) {
            unset($_SESSION[self::MYSQL]);
        }
    }

    function bindType(array $type){

        $bind = "";

        foreach ($type as $item) {
            if (gettype($item) === "string") {
                $bind .= "s";
            } else if (gettype($item) === "integer" || gettype($item) === "double") {
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

        if(count($column) == 1 && count($column[0]) == 1){

            foreach($column as $item){

                $keyName = strtolower(key($item)); //대문자로 alias를 쓸 경우 소문자로 변경
                $value = $item[key($item)];

                $objCount = new stdClass;

                if($keyName == "count(*)"){
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
                if(gettype($value) == "integer"){
                    $objCount->count = $value;
                    $objCount->status = true;
                }else{
                    $objCount->status = false;
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
        * @param error  NO DELETE CHANGED : 삭제된 데이터가 없을 경우 
        * @param error  NO UPDATE CHANGED : 변경된 데이터가 없을 경우
        * @param error  NO SELECT DATA : 검색된 데이터가 없을 경우 
        * @param error  NONE COUNT : 바인드수와 데이터수가 동일하지 않을 경우
        * @param error  NONE BIND DATA : 바인드 할 데이터가 없을 경우
        * @param error  NONE TABLE : 테이블명이 존재하지 않을 경우 
        * @param error  NONE INSERT DATA : 정상실행이지만 추가된 데이터가 없을 경우
        
        * @param error  SQL ERROR : SQL 에러일 경우
        * @param error  CONNECTION ERROR : 연결정보 오류일 경유
        * @param error  TIME_ZONE ERROR : 시간 설정 오류일 경우 (없을 경우)
        * @param error  NONE DATA TYPE : 설정된 데이터 타입이 아닙니다.
        * @param error  TRANSACTION ERROR : 트랜잭션 실행 오류
        * @param error  TRANSACTION START ERROR : 트랜잭션 미실행 오류
        * @param error  TRANSACTION STATUS ERROR : 트랜잭션 Default 상태 변경 필요
        * @param error  COMMIT ERROR : 커밋 에러
        * @param error  ROLLBACK ERROR : 롤백 에러
        * @param error  NONE : 설정된 에러항목이 없음
        */

        if($this->errorLogStatus == true){

            $text = "";
            $errorType = "";
            
            switch ($type) {
                case "NO DELETE CHANGED":
                    $errorType = "NO DELETE CHANGED";
                    $text = $errorType . " : " . "정상적으로 실행되었지만 삭제된 데이터가 없습니다.";
                    break;
                case "NO UPDATE CHANGED":
                    $errorType = "NO UPDATE CHANGED";
                    $text = $errorType . " : " . "정상적으로 실행되었지만 변경된 데이터가 없습니다.";
                    break;
                case "NO SELECT DATA":
                    $errorType = "NO SELECT DATA";
                    $text = $errorType . " : " . "정상적으로 실행되었지만 검색된 데이터가 없습니다.";
                    break;
                case "NONE COUNT":
                    $errorType = "NONE COUNT";
                    $text = $errorType . " : " . "바인드수와 데이터수가 동일하지 않습니다.";
                    break;
                case "NONE BIND DATA":
                    $errorType = "NONE BIND DATA";
                    $text = $errorType . " : " . "바인드할 데이터가 존재하지 않습니다.";
                    break;
                case "NONE TABLE":
                    $errorType = "NONE TABLE";
                    $text = $errorType . " : " . "테이블명이 존재하지 않습니다.";
                    break;
                case "NONE DATA TYPE":
                    $errorType = "NONE DATA TYPE";
                    $text = $errorType . " : " . "설정된 데이터 타입이 아닙니다.";
                    break;
                case "TRANSACTION STATUS ERROR":
                    $errorType = "TRANSACTION STATUS ERROR";
                    $text = $errorType . " : " . "트랜잭션 Default값을 false로 변갱해주세요.";
                    break;
                case "TRANSACTION START ERROR":
                    $errorType = "TRANSACTION START ERROR";
                    $text = $errorType . " : " . "트랜잭션을 먼저 실행해주세요.";
                    break;
                case "COMMIT ERROR":
                    $errorType = "COMMIT ERROR";
                    $text = $errorType . " : " . $error_message;
                    break;
                case "ROLLBACK ERROR":
                    $errorType = "ROLLBACK ERROR";
                    $text = $errorType . " : " . $error_message;
                    break;
                case "TRANSACTION ERROR":
                    $errorType = "TRANSACTION ERROR";
                    $text = $errorType . " : " . $error_message;
                    break;
                case "NONE INSERT DATA":
                    $errorType = "NONE INSERT DATA";
                    $text = $errorType . " : " . $error_message;
                    break;
                case "SQL ERROR":
                    $errorType = "SQL ERROR";
                    $text = $errorType . " : " . $error_message;
                    break;
                case "TIME_ZONE ERROR":
                    $errorType = "TIME_ZONE ERROR";
                    $text = $errorType . " : " . $error_message . " (time_zone 데이터가 없습니다.)";
                    break;
                case "CONNECTION ERROR":
                    $errorType = "CONNECTION ERROR";
                    $text = $errorType . " : " .  $error_message;
                    break;
                default:
                    $errorType  = "NONE";
                    $text = $errorType . " : " . "설정된 에러항목이 없습니다.";
                    break;
            } 
            // $script = '<script>console.log("' . $errorType  . ' : '  . $text . '");</script>';
            // print_r($script);
            print(sprintf("<pre style='background-color : 330000; color : white; font-family : fangsong; font-weight : bold; padding : 0.2rem; white-space : pre-wrap;'>%s</pre>" , print_r($text , true)));
        }
    }

    function debug_log($message , $status = ""){
        //디버깅용 array, object 출력할 수도 있으므로 JSON으로 출력
        if($status == "transaction"){
            $script = "<script>console.log('Transaction : " . json_encode($message) . "');</script>";
        }else{
            $script = "<script>console.log('Debugging : " . json_encode($message) . "');</script>";
        }
        print_r($script);
    }

    function removeElement($class){
        $removeElement = "<script>document.querySelector('." . $class ."').remove();</script>";
        echo $removeElement;
    }

    //트랜잭션
    function transaction(){
        $query = "START TRANSACTION;";
        try{
            if(!$this->transaction){
                $this->transaction = true;
                $this->transactionStatus = true;
                if(mysqli_query($this->connection , $query)){
                    if($this->tranasctionLogStatus){
                        self::debug_log("TRANSACTION START" , "transaction");
                        $result = "COMMIT , ROLLBACK 실행 필요";
                        print(sprintf("<pre class='transaction' style='background-color : 000000; color : white; font-family : fangsong; font-weight : bold; padding : 0.2rem;'>%s</pre>" , print_r($result , true)));
                    }
                    return true;
                };
            }else{
                $type = "TRANSACTION STATUS ERROR";
                self::error_log($type);
                return false;
            }
        }catch(mysqli_sql_exception $error){
            $type = "TRANSACTION ERROR";
            self::error_log($type , $error->getMessage());
            return false;
        }
    }
    
    function commit(){
        $query = "COMMIT;";
        try{
            if($this->transaction && $this->transactionStatus){
                $this->transaction = false;
                if(mysqli_query($this->connection , $query)){
                    if($this->tranasctionLogStatus){
                        self::debug_log("COMMIT SUCCESS" , "transaction");
                        self::removeElement("transaction");
                    }
                    return true;
                };
            }else{
                $type = "TRANSACTION START ERROR";
                self::error_log($type);
                return false;
            }
        }catch(mysqli_sql_exception $error){
            $type = "COMMIT ERROR";
            self::error_log($type , $error->getMessage());
            return false;
        }
    }
    
    function rollback(){
        $query = "ROLLBACK;";
        try{
            if($this->transaction && $this->transactionStatus){
                $this->transaction = false;
                if(mysqli_query($this->connection , $query)){
                    if($this->tranasctionLogStatus){
                        self::debug_log("ROLLBACK SUCCESS" , "transaction");
                        self::removeElement("transaction");
                    }
                    return true;
                };
            }else{
                $type = "TRANSACTION START ERROR";
                self::error_log($type);
                return false;
            }
        }catch(mysqli_sql_exception $error){
            $type = "ROLLBACK ERROR";
            self::error_log($type , $error->getMessage());
            return false;
        }
    }
    //트랜잭션
    
    function insert($obj , $data = array() , String $table = ""){

        /**
         * @param object 오브젝트 형식
         * @param String Query 문자형 형식 
         */

        if(gettype($obj) !== "object" && gettype($obj) !== "string"){
            $type = "NONE DATA TYPE";
            self::error_log($type);
            return false;
        }

        $dataValue = array();
        $query = "";
        $bind = "";
        $parameter = "";
        $questionCount = 0;
        $questionString = "";

        if(gettype($obj) === "object"){

            if(empty($obj->table)){//테이블명을 뺴먹었을 경우 
                $type = "NONE TABLE";
                self::error_log($type);
                return false;
            }   
            
            $column = array_keys((array)$obj);//키값을 배열로 전환
            $column = str_replace(",table", "", implode(",", $column));//테이블 기값 제거 
            
            $value = array_values((array)$obj);//value 값들 배열로전환
            $value = array_filter(str_replace($obj->table, "",  $value));//테이블 명 제거 동시에 빈값 제거

            foreach($value as $key => $item){
                array_push($dataValue , $item);
            }

            $bind = self::bindType($dataValue);//bind문장 가져오기
            $parameter = self::getParameter($bind);//?문장 가져오기
            $questionCount = substr_count($parameter , "?");//?수 가져오기
            
            $query = "INSERT INTO " . $obj->table . " ( " . $column . ") VALUES (" .  $parameter . ");";
            //실행될 쿼리 입력


        }else if(gettype($obj) === "string"){ 

            foreach($data as $key => $item){
                array_push($dataValue , $item);
            }
            $bind = self::bindType($dataValue);

            if(strpos($obj , strtoupper("values")) || strpos($obj , strtolower("VALUES"))){//완전한 Query 형 문장으로 들어올 떄

                $questionCount = substr_count($obj , "?");//?수 가져오기
                $query = $obj;
                
            }else if(strpos($obj , strtoupper("into")) || strpos($obj , strtolower("INTO"))){//VALUES 부터 나머지 문장 자동 생성

                $questionString = explode("(" , $obj);
                $questionString = explode(")" , $questionString[1])[0];
                $questionCount = count(explode( "," ,$questionString));
                $questionString = "";
                
                for ($i=0; $i < $questionCount; $i++) { 
                    if($i == 0){
                        $questionString .= "?";
                    }else{
                        $questionString .= " , ?";
                    }
                }
                
                $questionCount = substr_count($questionString , "?");
                $query = $obj . " VALUES ( " . $questionString . " );";

            }else{//컬럼명만 문장으로 넘어올 때 

                if($table == ""){//테이블이 없을 때
                    $type = "NONE TABLE";
                    self::error_log($type);
                    return false;
                }

                $questionArray = explode("," , $obj);

                for ($i=0; $i < count($questionArray); $i++) { 
                    if($i == 0){
                        $questionString .= "?";
                    }else{
                        $questionString .= " , ?";
                    }
                }

                $questionCount = substr_count($questionString , "?");
                $query = "INSERT INTO " . $table . " ( " . $obj . " ) VALUES ( " . $questionString . ");";

            } 
            
        }

        if(sizeof($dataValue) == 0){//바인드할 데이터가 없을 경우
            $type = "NONE BIND DATA";
            self::error_log($type);
            return false;
        }

        if(strlen($bind) != $questionCount){//바인드와 데이터가 맞지 않을경우 
            $type = "NONE COUNT";
            self::error_log($type);
            return false;
        }

        try {

            $statement = $this->connection->prepare($query);//쿼리 준비

            if ($statement) {
    
                if ($bind != "" && sizeof($dataValue) > 0) {
                    $statement->bind_param($bind, ...$dataValue);//bind 삽입
                }
    
                $statement->execute();//실제 쿼리실행
    
                if ($statement->insert_id || $statement->affected_rows) {
                    $statement->close(); //쿼리해제
                    return true;
                }else{
                    self::error_log("NONE INSERT DATA" , "실행하였지만 추가된 Data가 없습니다.");
                    $statement->close();
                    return false;
                }
                
            }
   
        } catch (mysqli_sql_exception $error) {
            $type = "SQL ERROR";
            self::error_log($type, $error->getMessage());
            if($this->transaction != true){
                $this->connection->close();
            }
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

                if($statement->num_rows == 0){
                    $type = "NO SELECT DATA";
                    self::error_log($type);
                    return false;
                }
    
            }
    
            $list = array();//데이터를 담아줄 배열 생성
    
            while ($result = $statement->fetch_assoc()) {// 배열 형식의 데이터를 새로운 배열에 담아준다. 
                array_push($list, $result);
            }

            $statement->close(); //실행 쿼리 지워주기

        } catch (mysqli_sql_exception $error) {
            $type = "SQL ERROR";
            self::error_log($type , $error->getMessage());
            $this->connection->close();
            return false;
        }
        
        $objList = array();//객체 리스트를 담을 배열 생성
        $countCheck = self::countCheck($list); //count를 구하는지 체크

        if (!empty($list) && count($list) > 1) { //배열의 길이가 1보다 낮으면 객체로 리턴 아닐 시 배열로 리턴
            //리스트
            foreach ($list as $key => $item) {
                $obj = new stdClass;
                $column = array_keys($item);//key이름만 배열 형태로 전환
                
                foreach ($column as $columnName) {
                    $obj->$columnName = $item[$columnName];
                    //객체->키이름 = value값을 담아준다.
                }
                $objList[$key] = $obj;
                //객체 리스트를 만들어준다.
            }
            return $objList;

        } else if(!empty($list) && count($list) == 1){
            //단일

            foreach ($list as $key => $item) {
                $obj = new stdClass;
                $column = array_keys($item);//key이름만 배열 형태로 전환

                foreach ($column as $columnName) {
                    if(!empty($countCheck->status)){//갯수를 구할 시 갯수만 리턴
                        if($countCheck->status == true){
                            $count = $item[$columnName];
                            return $count; //실제 갯수만 리턴
                        }
                    }else{
                        $obj->$columnName = $item[$columnName];
                        //객체->키이름 = value값을 담아준다.
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
            if($this->transaction != true){
                $this->connection->close();
            }
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

                print_r($statement);
    
                if ($statement->affected_rows) {
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
            if($this->transaction != true){
                $this->connection->close();
            }
            return false;
        }
    }
}