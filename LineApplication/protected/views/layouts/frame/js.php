<?php 
$cs->registerScriptFile(Yii::app()->request->baseUrl . '/js/jquery/jquery-1.9.1.min.js');
$cs->registerScriptFile(Yii::app()->request->baseUrl . '/js/jquery/jquery-ui-1.10.3.custom/jquery-ui-1.10.3.custom.min.js');
if(isset($this->JS) && is_array($this->JS)){
	foreach($this->JS as $js_path){
		$cs->registerScriptFile(Yii::app()->request->baseUrl . $js_path);
	}
} 