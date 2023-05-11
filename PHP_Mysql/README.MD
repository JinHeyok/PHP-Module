### DB Module 사용
#### - ClassAutoload 사용 
#### - Class 생성 후 사용 (공통)
- 공통 적용
    - prepare사용
    - Bind 될 Data의 갯수에 따라 '?' 와 DataType 자동 삽입
    - Bind 될 Data가 없어도 정상 실행

--- 
## - SELECT
    - 예시문
    $db = new MySQL;
    $selectQuery = "SELECT * FROM TABLE;";
    $selectData = array();
    $db->select($selectQuery , $selectData);
> 설명 
1. 원하는 정보를 불러올 Query문을 변수에 담는다.
2. Bind 할 데이터를 배열에 넣어준다.
3. DB Class에 select함수를 호출해 Query , Data(array) 을 넣어준다.
- 하나의 단일 데이터일 경우 OPP(객체) 형태로 return 
- 여러개의 데이터일 경우 OPP(객체) 형태로 배열로 return
- COUNT()함수를 사용하여 갯수를 구하는 Query문일 경우 갯수만 Return

--- 
## - INSERT
    - 예시문

    * object 
    $db = new MySQL;
    $insertObj = new stdClass;
    $insertObj->column1 = data1;
    $insertObj->column2 = data2;
    $insertObj->column3 = data3;
    $insertObj->table = tableName;
    $insertStatus = $db->insert($insertObj);

    * String
    $insertString = "INSERT INTO emp (column1 , column2 , column3 ) VALUES (? , ? , ?);";
    $insertData = array(data1 , 0 , data2);
    $insertStatus = $db->insert($insertString , $insertData);

> 설명
- Object
    1. 빈 클래스를 생성한다. 
    2. 객체형식으로 해당 Column 이름으로 INSERT할 Data를 넣어준다.
    3. 마지막에는 table 이름으로 INSERT할 테이블명을 적어준다.
    4. DB Class에 insert함수를 호출해 클래스를 넣어준다.
    5. INSERT가 정상적으로 이루어질 시 ture return
--- 
- String
    1. Query를 문장형으로 생성
    2. prepare 사용으로 삽입할 데이터를 배열로 만듬 
---
- 객체명이 테이블명이 된다.
- Query형으로 데이터를 문장과 같이 사용할 경우 실행 가능
---
## - UPDATE
    - 예시문
    $db = new MySQL;
    $updateQuery = "UPDATE table SET column = data WHERE column = ?";
    $updateData = array( 1 );
    $updateStatus = db->update($updateQuery , $updateData);
>설명
1. UPDATE 할 Query문을 작성해준다.
2. Bind 할 데이터를 배열에 넣어준다.
3. DB Class에 update를 호출해 Query, Data(array)를 넣어준다.
- SELECT 방식과 동일
---
## - DELETE
    - 예시문
    $db = new MySQL;
    $deleteQuery = "DELETE FROM table WHERE Column = ?;";
    $deleteData = array( 1 );
    $deleteStatus = $db->delete($deleteQuery , $deleteData);
> 설명
1. DELETE 할 Query문을 작성해준다.
2. Bind 할 데이터를 배열에 넣어준다.
3. DB Class에 delete를 호출해 Query , Data(array)를 넣어준다.
---
**ERROR 항목**
|ERROR NAME|ERROR CONTENT|ERROR MESSAGE|
|:--:|:--:|:--:|
|CONNECTION ERROR|DB 연결 오류시 연결 오류 출력|mysqli_sql_exception Error 출력|
|SQL ERROR|실제 SQL Error Message 출력|mysqli_sql_exception Error 출력|
|TIME_ZONE ERROR|시간 설정 오류일 경우 (데이터가 없을 경우)|mysqli_sql_exception Error 출력|
|NONE COUNT|바인드 수와 데이터 수가 동일하지 않을 경우|"바인드 수와 데이터 수가 동일하지 않습니다."|
|NONE TABLE|테이블 명이 존재하지 않을 경우|"테이블명이 존재하지 않습니다."|
|NONE DATA TYPE|설정된 데이터 타입이 아닐 경우|"설정된 데이터 타입이 아닙니다."|
|NONE INSERT DATA|정상 실행되었지만 추가된 데이터가 없을 경우|"정상적으로 실행되었지만 추가된 데이터가 없습니다."|
|NO SELECT DATA|검색된 데이터가 없을 경우|"정상적으로 실행되었지만 검색된 데이터가 없습니다"|
|NO UPDATE CHANGED|변경된 데이터가 없을 경우|"정상적으로 실행되었지만 변경된 데이터가 없습니다."|
|NO DELETE CHANGED|삭제된 데이터가 없을 경우|"정상적으로 실행되었지만 삭제된 데이터가 없습니다."|
|NONE|설정된 에러항목이 없을 경우 출력|"설정된 에러항목이 없습니다."|

