<?php
if (!defined('Perfect')) exit('Blocking access to this script');

$CONFIG['router'] = array(
	'enable_module'=>true,
	'default_module'=>'back',
	'default_controller'=>'Index',
	'default_action'=>'Index',
);

$CONFIG['autoLoadDirs'] = array(
	// 'alias'=>PATH,
);

$CONFIG['viewConfig'] = array(
	'viewExt'=>'php',
	'viewPath'=>VIEW_PATH,
	'layout'=>'main',
);


$CONFIG['systemInfo'] = array(
	'system_name'=>'ZCG',
	'author'=>'Alex.Qiu',
	'email'=>'alex.qiubo@qq.com',
);
