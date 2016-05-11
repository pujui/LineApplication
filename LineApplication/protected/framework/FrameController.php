<?php
class FrameController extends Controller
{
    
    public $layout = '//layouts/frame';
    
    public $pageTitle = 'order';
    
    public $CSS = array();    

    public $JS = array();
    
    public $FRAME_DATA = array();
    
    public $BreadCrumbs = array();
    
    public function __construct(){
    }
    
    public function layout($page, $data = array()){
        $this->FRAME_DATA = array_merge($this->FRAME_DATA, $data);
        $this->render($page, $this->FRAME_DATA);
    }
    
    public function setJS($js){
        $this->__setVariable($this->JS, $js);
    }
    
    public function setCSS($css){
        $this->__setVariable($this->CSS, $css);
    }
    
    public function setVariable($variable, $value){
        $this->FRAME_DATA[$variable] = $value;
    }
    
    private function __setVariable(&$variable, $list){
        if(is_array($list)){
            foreach ($list as $value){
                $variable[] = $value;
            }
        }else{
            $variable[] = $list;
        }
    }
    
    public function actionErrorPage(){
        $this->redirect(Yii::app()->request->baseUrl.'/user/login');
    }

}