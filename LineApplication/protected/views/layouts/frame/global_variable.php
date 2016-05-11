<?php 
$cs = Yii::app()->getClientScript();
if(!empty($this->FRAME_DATA) && is_array($this->FRAME_DATA)){
	foreach ($this->FRAME_DATA as $_variable => $_variableValue ){
		$$_variable = $_variableValue;
	}
}