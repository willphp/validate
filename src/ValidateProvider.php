<?php
/*--------------------------------------------------------------------------
 | Software: [WillPHP framework]
 | Site: www.113344.com
 |--------------------------------------------------------------------------
 | Author: no-mind <24203741@qq.com>
 | WeChat: www113344
 | Copyright (c) 2020-2022, www.113344.com. All Rights Reserved.
 |-------------------------------------------------------------------------*/
namespace willphp\validate;
use willphp\framework\build\Provider;
class ValidateProvider extends Provider {
	public $defer = true; //延迟加载
	public function boot() {		

	}
	public function register() {
		$this->app->single('Validate', function () {
			return Validate::single();
		});
	}
}