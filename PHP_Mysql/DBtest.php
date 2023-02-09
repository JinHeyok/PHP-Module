<?php
$modulePath = dirname(__FILE__);

// ERROR LOG 출력
// error_reporting(E_ALL);
// ini_set( "display_errors", 1 );
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
$selectQuery = "SELECT * FROM store_user WHERE su_index = ? OR su_index = ? OR su_index = ?;";
$selectData = array(1, 200, 300);
$selectList = $db->select($selectQuery, $selectData);

print_r($selectList);
echo "<br>";

foreach($selectList as $key => $obj){
    print_r($obj);
    echo '<br>';
}
//셀렉트 쿼리형 타입

//인서트 객체 타입

//$obj->컬림명 = 데이터
//$obj->컬럼명 = 데이터
//$obj->talbe = table명 꼭 테이블명 삽입 

// $insertObj = new stdClass; 

// $insertObj->su_id = "test4"; 
// $insertObj->su_pw = "test4";
// $insertObj->table = "store_user";
// $insertStatus = $db->insert($insertObj);

// print_r($insertStatus);

//인서트 객체 타입 

//업데이트 쿼리형 타입

// $updateQuery = "UPDATE store_user SET su_id = ? , su_pw = ? WHERE su_index = ?;";
// $updateData = array(
//     "test12",
//     "test123",
//     3
// );
// $updateStatus = $db->update($updateQuery, $updateData);
// print_r($updateStatus);

//업데이트 쿼리형 타입 



//삭제 쿼리형 타입

// $deleteQuery = "DELETE FROM store_user WHERE su_index = ?;"; //쿼리 입력 
// $deleteData = array(
//     7
// );
// $deleteStatus = $db->delete($deleteQuery , $deleteData);
// print_r($deleteStatus);

//삭제 쿼리형 타입


//삭제 객체형 타입

// $deleteObj = new stdClass;

// $deleteObj->table = "store_user"; //테이블 입력 
// $deleteObj->whereColumn = array("su_index","su_index" , "su_index" , "su_index" , "su_index");//조건 컬럼 Data수와 같아야함
// $deleteObj->whereData = array(13 , 12, 11 , 10, 8); //삭제될 데이터 컬럼명의 수와 같야함
// $deleteObj->whereType = array("or", "or" , "or" , "or");  //맨앞 where 제외 하나 적게 
// $deleteObjStatus = $db->delete($deleteObj);
// print_r($deleteObjStatus);

//삭제 객체형 타입



// 테이블 없으실 console.log에 테이블 없음 표시
// 바인드와 Data수가 안맞을 경우 console.log에 테이블 없음 표시
// 쿼리 실행은 정상이지만 삭제된게 없을시 console.log9에 표시