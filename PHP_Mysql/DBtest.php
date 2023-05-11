<?php
$modulePath = dirname(__FILE__);

// ERROR LOG 출력
error_reporting(E_ALL);
ini_set( "display_errors", 1 );
// ERROR LOG 출력

//CLASS AUTO LOAD
function classAutoload($className = "")
{
    global $modulePath;
    if ($className != "") {
        require_once $modulePath . "/Module" . "/" . $className . ".php";
        //패스 정보 변경
    }
}

spl_autoload_register("classAutoload");
//CLASS AUTO LOAD

//DB CLASS 생성
$db = new MySQL;
//DB CLASS 생성

//셀렉트 쿼리형 타입
$selectQuery = "SELECT  empno  FROM emp WHERE sal = 800 AND deptno = 20;";
$selectData = array(800 , 20);
$selectList = $db->select($selectQuery);

$db->data_log($selectList);

foreach($selectList as $key => $obj){
    print_r($obj);
    echo '<br>';
}
//셀렉트 쿼리형 타입

//인서트 객체 타입

$insertObj = new stdClass; 

$insertObj->empno = 8000;
$insertObj->ename = "CHIRS";
$insertObj->mgr = 7902;
$insertObj->job = "CLWEK";
$insertObj->sal = 6000.00;
$insertObj->table = "emp";
$insertObj->comm = 1000.00;
$insertObj->hiredate = "1990-02-03";
$insertObj->deptno = 30;

//transaction
$db->transaction(); //트랜잭션 시작

//인서트 쿼리형 타입 
$insertString = "INSERT INTO emp ( empno , ename , mgr , job , sal ,comm , hiredate , deptno) VALUES (? , ? , ? , ? , ? , ? , ? ,? );";
$insertData = array(8002 , "CHIRS" , 7902 , "CLWEK" , 6000.00  , 1000 , "1990-02-03" , 30);

$insertStatus =  $db->insert($insertObj , $insertData); //객체 
$insertStatus =  $db->insert($insertString , $insertData); //문장

$db->rollback(); //롤백
$db->commit(); // 커밋 

print_r($insertStatus);

//인서트 객체, 문장 타입 

//업데이트 쿼리형 타입

$updateQuery = "UPDATE store_user SET su_id = ? , su_pw = ? WHERE su_index = ?;";
$updateData = array(
    "test12",
    "test123",
    3
);
$updateStatus = $db->update($updateQuery, $updateData);
print_r($updateStatus);

//업데이트 쿼리형 타입 

//삭제 쿼리형 타입

$deleteQuery = "DELETE FROM store_user WHERE su_index IN (?, ? , ? , ? , ?, ?, ?);"; //쿼리 입력 
$deleteData = array(
    12 , 13 , 14 , 15, 16 , 17 , 18
);
$deleteStatus = $db->delete($deleteQuery , $deleteData);
print_r($deleteStatus);

//삭제 쿼리형 타입


//삭제 객체형 타입

$deleteObj = new stdClass;

$deleteObj->table = "store_user"; //테이블 입력 
$deleteObj->whereColumn = array("su_index","su_index" , "su_index" , "su_index" , "su_index");//조건 컬럼 Data수와 같아야함
$deleteObj->whereData = array(13 , 12, 11 , 10, 8); //삭제될 데이터 컬럼명의 수와 같야함
$deleteObj->whereType = array("or", "or" , "or" , "or");  //맨앞 where 제외 하나 적게 
$deleteObjStatus = $db->delete($deleteObj);
print_r($deleteObjStatus);

//삭제 객체형 타입

// 테이블 없으실 print 화면에 문구 출력
// 바인드와 Data수가 안맞을 경우 print로  테이블 없음 표시
// 쿼리 실행은 정상이지만 삭제된게 없을시 print로 없음 표시
