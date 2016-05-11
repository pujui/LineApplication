<?php
class CPageView extends CWidget{
	
	public $total = 0;
	
	public $limit = 10;
	
	public $page = 1;
	
	public $pageVO;
	
	public $pageUrl = '';
	
	public $pageVariableName = 'p';
	
	public $params = array();
	
	public $template = 'template.php';
	
	public $pageSeveral = 5;
	
	public $pathDir = '';
	
	public function init()
	{
		if(!empty($this->pageVO)){
			$this->page = $this->pageVO->page;
			$this->total = $this->pageVO->total;
			$this->limit = $this->pageVO->limit;
			$this->params = $this->pageVO->params;
		}
		
		if($this->pathDir == ''){
			$this->pathDir = dirname(__FILE__).'/../views/widgets/CPageView/';
		}
		
		return $this;
	}
	
	public function run()
	{
		$this->init();
		
		$pageResultVo = $this->createPageList();
		
		if(is_object($pageResultVo)){
			$this->display($pageResultVo);
		}
	}
	
	public function createPageList()
	{
		//總數量
		$total			= $this->total;
		if($total < 1){
			return false;
		}
		
		//一頁筆數
		$limit			= $this->limit;
		if($limit < 1){
			return false;
		}
		
		//頁面參數
		$params	= $this->params;
		unset($params[$this->pageVariableName]);
		
		//選擇頁
		$currentPage	= $this->page;
		
		//頁面連結
		$pageUrl		= $this->pageUrl;
		
		//顯示頁籤數
		$pageSeveral	= $this->pageSeveral;
		
		//總頁數
		$totalPage		= ceil($total/$limit);
		
		//預設分頁參數
		$defPageParam	= 'p';
		
		$PageResultVo	= new stdClass;
		
		$result			= array(
								'currentPage'		=> 0,		//current page number
								'currentPageUrl'	=> '',		//current page url
								'prevPage'			=> 0,		//prev page number
								'nextPage'			=> 0,		//next page number
								'prevPageUrl'		=> '',		//prev page url
								'nextPageUrl'		=> '',		//next page url
								'headPage'			=> 0,		//head page number
								'footPage'			=> 0,		//foot page number
								'headPageUrl'		=> '',		//head page url
								'footPageUrl'		=> '',		//foot page url
								'startPageSeveral'	=> 0,		//start page number
								'endPageSeveral'	=> 0,		//end page number
								'rangePageNumber'	=> array(),	//range page list
								'rangePageUrl'		=> '',		//range page url list
								'pageParamString'	=> '',		//get params string
								
							);
		
		//set default value
		$temp = $this->getDefaultPage($pageUrl, $currentPage, $totalPage);
		$pageUrl = $temp['pageUrl'];
		$currentPage = $temp['currentPage'];
		
		//set prev and next page number
		$temp = $this->getPrevAndNextNumber($currentPage, $totalPage);
		$PageResultVo->prevPage = $temp['prevPage'];
		$PageResultVo->nextPage = $temp['nextPage'];
		
		//set 頁籤 start and end page number
		$temp = $this->getStartAndEndNumber($currentPage, $pageSeveral, $totalPage);
		$PageResultVo->startPageSeveral = $temp['startPageSeveral'];
		$PageResultVo->endPageSeveral = $temp['endPageSeveral'];
		
		//set display 頁籤 data
		$PageResultVo->rangePageNumber = range($PageResultVo->startPageSeveral, $PageResultVo->endPageSeveral);
		
		//get get params
		$PageResultVo->pageParamString = $this->getUrlEncodeParamString($params);
		
		//get range page url
		$PageResultVo->rangePageUrl = $this->getRangePageUrl($PageResultVo->rangePageNumber, $pageUrl, $defPageParam, $PageResultVo->pageParamString);
		
		//set prev page url
		if($PageResultVo->prevPage > 0){
			$PageResultVo->prevPageUrl = sprintf('%s?%s=%d%s', $pageUrl, $defPageParam, $PageResultVo->prevPage, $PageResultVo->pageParamString);
		}
		
		//set next page url
		if($PageResultVo->nextPage > 0){
			$PageResultVo->nextPageUrl = sprintf('%s?%s=%d%s', $pageUrl, $defPageParam, $PageResultVo->nextPage, $PageResultVo->pageParamString);
		}
		
		$PageResultVo->total = $total;
		
		//set current page number
		$PageResultVo->currentPage = $currentPage;
		
		//set current page url
		$PageResultVo->currentPageUrl = sprintf('%s?%s=%d%s', $pageUrl, $defPageParam, $currentPage, $PageResultVo->pageParamString);;
		
		//set head page number
		$PageResultVo->headPage = 1;
		
		//set head page url
		$PageResultVo->headPageUrl = sprintf('%s?%s=%d%s', $pageUrl, $defPageParam, 1, $PageResultVo->pageParamString);
		
		//set foot page number
		$PageResultVo->footPage = $totalPage;
		
		//set foot page url
		$PageResultVo->footPageUrl = sprintf('%s?%s=%d%s', $pageUrl, $defPageParam, $totalPage, $PageResultVo->pageParamString);;
		
		return $PageResultVo;
	}
	
	/**
	 * 設定分頁預設值
	 * @param string $pageUrl
	 * 	連結
	 * @param integer $currentPage
	 * 	選擇頁
	 * @param integer $totalPage
	 * 	總頁數
	 */
	private function getDefaultPage($pageUrl, $currentPage, $totalPage){
		//如果頁面未填寫則帶入預設值
		if($pageUrl == ''){
			//$pageUrl = Yii::app()->baseUrl . $_SERVER['PATH_INFO'];
			$pageUrl = $_SERVER['REDIRECT_URL'];
		}
		//如果[選擇頁]大於[總頁數], 則更新為 [總頁數]
		if($currentPage > $totalPage){
			$currentPage = $totalPage;
		
		//如果[選擇頁]小於1, 則更新為 1
		}else if($currentPage < 1){
			$currentPage = 1;
		}
		return array('pageUrl' => $pageUrl, 'currentPage' => $currentPage);
	}
	
	/**
	 * 給予頁籤開始頁與結束頁
	 * @param integer $currentPage
	 * 	選擇頁
	 * @param integer $pageSeveral
	 * 	顯示頁籤數
	 * @param integer $totalPage
	 * 	總數
	 */
	private function getStartAndEndNumber($currentPage, $pageSeveral, $totalPage){
		$startPageSeveral = 0;	//頁籤開始頁
		$endPageSeveral = 0;	//頁籤結束頁
		
		//如果[選擇頁]減[顯示頁籤數]小於1, 則[頁籤開始頁]為1 否則為相減結果
		if($currentPage-$pageSeveral < 1){
			$startPageSeveral = 1;
		}else{
			$startPageSeveral = $currentPage-$pageSeveral;
		}
		
		//如果[選擇頁]加[顯示頁籤數]大於[總頁數], 則[頁籤結束頁]為[總頁數] 否則為相加結果
		if($currentPage+$pageSeveral > $totalPage){
			$endPageSeveral = $totalPage;
		}else{
			$endPageSeveral = $currentPage+$pageSeveral;
		}
		return array('startPageSeveral' => $startPageSeveral, 'endPageSeveral' => $endPageSeveral);
	}
	
	/**
	 * 給予上一頁與下一頁
	 * @param integer $currentPage
	 * 	選擇頁
	 * @param integer $totalPage
	 * 	總頁數
	 */
	private function getPrevAndNextNumber($currentPage, $totalPage){
		$prevPage = 0;	//上一頁
		$nextPage = 0;	//下一頁
		
		//如果[選擇頁]大於1, 則記錄[上一頁]
		if($currentPage > 1){
			$prevPage = $currentPage - 1;
		}
		
		//如果[選擇頁]小於[總頁數], 則記錄[下一頁]
		if($currentPage < $totalPage){
			$nextPage = $currentPage + 1;
		}
		return array('prevPage' => $prevPage, 'nextPage' => $nextPage);
	}
	
	/**
	 * 產生範圍連結
	 * @param integer[] $rangePageNumber
	 * @param string $pageUrl
	 * @param string $defPageParam
	 * @param string $pageParamString
	 */
	private function getRangePageUrl($rangePageNumber, $pageUrl, $defPageParam, $pageParamString){
		$rangePageUrl = array();
		foreach ($rangePageNumber as $page){
			if($page == 1 && $pageParamString==''){
				$rangePageUrl[$page] = $pageUrl;
			}else{
				$rangePageUrl[$page] = $pageUrl . sprintf('?%s=%d%s', $defPageParam, $page, $pageParamString);
			}
		}
		return $rangePageUrl;
	}
	
	/**
	 * 對參數進行url encode轉換成get params
	 * @param array $params
	 */
	private function getUrlEncodeParamString($params){
		
		if(empty($params)){
			return '';
		}
		
		$pageParamList = array();
		
		foreach ($params as $field=>$value){
			if(is_array($value)){
				foreach ($value as $v){
					$pageParamList[] = sprintf('%s[]=%s', $field, urlencode($v));
				}
			}else{
				$pageParamList[] = sprintf('%s=%s', $field, urlencode($value));
			}
		}
		
		return '&amp;'.implode('&amp;', $pageParamList);
	}
	
	public function display($result){
		include $this->pathDir . $this->template;
	}
}