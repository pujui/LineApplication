<div class="pageArea">
<?php
if($result->currentPage > 1){
	echo sprintf('<span><a href="%s" >head</a></span>', $result->headPageUrl);
}

if($result->prevPage > 0){
	echo sprintf('<span><a href="%s" >prev</a></span>', $result->prevPageUrl);
}

foreach ($result->rangePageUrl as $page=>$url){
	echo sprintf('<span><a href="%s" >%d</a></span>', $url, $page);
}

if($result->nextPage > 0){
	echo sprintf('<span><a href="%s" >next</a></span>', $result->nextPageUrl);
	echo sprintf('<span><a href="%s" >foot</a></span>', $result->footPageUrl);
}
?>
</div>