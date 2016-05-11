<?php
$cs->registerCssFile(Yii::app()->request->baseUrl . '/css/bootstrap.min.css');
$cs->registerCssFile(Yii::app()->request->baseUrl . '/css/justified-nav.css');
$cs->registerCssFile(Yii::app()->request->baseUrl . '/js/jquery/jquery-ui-1.10.3.custom/ui-lightness/jquery-ui-1.10.3.custom.min.css');
$cs->registerCssFile(Yii::app()->request->baseUrl . '/css/frame.css');
if(isset($this->CSS) && is_array($this->CSS)){
	foreach($this->CSS as $cs_path){
		$cs->registerCssFile(Yii::app()->request->baseUrl . $cs_path);
	}
}