<?php
class CBreadCrumbsView extends CWidget{

	public $BreadCrumbs = array();
	
	public function run()
	{
		$breadCrumbs = array();
		if($this->BreadCrumbs){
			foreach ($this->BreadCrumbs as $url=>$value){
				if($url == 'last'){
					$breadCrumbs[] = sprintf('<span>%s</span>', CHtml::encode($value));
				}else{
					$breadCrumbs[] = sprintf('<a href="%s" >%s</a>', $url, CHtml::encode($value));
				}
			}
		}
		echo implode("&gt;", $breadCrumbs);
	}
}