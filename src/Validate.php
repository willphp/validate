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
use willphp\validate\build\Base;
/**
 * 验证处理
 * Class Validate
 * @package willphp\validate
 */
class Validate {
	const EXISTS_VALIDATE = 1; //有字段时验证
	const VALUE_VALIDATE = 2; //值不为空时验证
	const MUST_VALIDATE = 3; //必须验证
	const VALUE_NULL = 4; //值是空时处理
	const NO_EXISTS_VALIDATE = 5; //不存在字段时处理	
	protected static $link;		
	public static function single()	{
		if (!self::$link) {
			self::$link = new Base();
		}
		return self::$link;
	}
	public function __call($method, $params) {
		return call_user_func_array([self::single(), $method], $params);
	}		
	public static function __callStatic($name, $arguments) {
		return call_user_func_array([self::single(), $name], $arguments);
	}
}