<?php

class Pagination {

	/**
	 * 페이지 네이션 처리 관련 주석
     *
	 * @param count - 페이징 처리할 개수
	 * @param current - 현재 페이지
     * @param perPage - 한 페이지에 리스트 개수
     * @param pageLength - 한 페이지에 나타낼 페이지 링크 개수
     *
	 * @return pageCount - 총 페이지 수
     * @return current - 현재 페이지 수
     * @return limit - 현재 페이지의 시작 게시물 번호
     * @return perPage - 한 페이지에 리스트 개수
     * @return nextPage - 다음 페이지 수
     * @return firstPage - 처음 페이지 수
     * @return prevPage - 이전 페이지 수
     * @return lastPage - 마지막 페이지 수
     *
     * @return currentBigPage - pageLength 단위의 페이지 번호
     * @return pageLength - 한 페이지에 나타낼 페이지 링크 개수
     * @return currentIndex - 링크번호에 default 로 더 할 값
     * @return LastForIndex - 반복문 완료 조건 번호 // 맨 마지막 페이지에서 사용
     *
	 */
    function page($count , $current, $perPage = 10, $pageLength = 5){
        if($count == 0){
          $count = 1;
        }

        $pageCount = ceil($count / $perPage);
        $currentPage = $current > 1 ? $current - 1 : $current = 0;
        $limitPage = $currentPage * $perPage;

        $nextPage = ($currentPage + 1) < $pageCount ? ($currentPage + 2) : $pageCount;
        $firstPage = ceil($current * $perPage) > 1 ? 0 : $currentPage ;
        $prevPage = $currentPage != 0 ? $currentPage : 0;
        $lastPage = $pageCount;

        $result = new stdClass;

        $result->pageCount = $pageCount;
        $result->current = $currentPage;


        $result->limit = $limitPage;
        $result->perPage = $perPage;

        $result->nextPage = $nextPage;
        $result->firstPage = $firstPage;
        $result->prevPage = $prevPage;
        $result->lastPage = $lastPage;

        $result->currentBigPage = ceil(($currentPage+1) / $pageLength)-1;
        $result->pageLength = $pageLength;
        $result->currentIndex = $result->currentBigPage * $result->pageLength;

        $result->t1 = floor($result->pageCount / $pageLength);
        $result->t2 = floor($currentPage / $pageLength);


        if(floor($result->pageCount / $pageLength) == floor($currentPage / $pageLength)){
            $result->LastForIndex = $result->pageCount % $pageLength;
        }else{
            $result->LastForIndex = $pageLength;
        }


        return $result;
    }

}

?>
