<?php
if (!defined('Perfect')) exit('Blocking access to this script');
/**
 * 后台首页相关功能控制器类
 */
class indexController extends Controller {

	public function indexAction(){
		echo 'this is index action';
	}

	public function demoAction(){
	    echo 'this is demo action';
    }

}
