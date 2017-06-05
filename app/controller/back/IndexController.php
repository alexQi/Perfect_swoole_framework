<?php
if (!defined('Perfect')) exit('Blocking access to this script');
/**
 * 后台首页相关功能控制器类
 */
class indexController extends Controller {

	public function indexAction(){
		$data = array();
		echo 1111111;
//		$this->display('index',$data);
	}

	/**
	 * demo
	 */
	public function logoutAction() {
	}

}
