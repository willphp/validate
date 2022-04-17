<?php
/*--------------------------------------------------------------------------
 | Software: [WillPHP framework]
 | Site: www.113344.com
 |--------------------------------------------------------------------------
 | Author: no-mind <24203741@qq.com>
 | WeChat: www113344
 | Copyright (c) 2020-2022, www.113344.com. All Rights Reserved.
 |-------------------------------------------------------------------------*/
namespace willphp\validate\build;
use willphp\db\Db;
use willphp\session\Session;
//验证规则
class ValidateRule {	
	//字段必须存在且不能为空
	public function required($field, $value, $params, $data) {
		if (!isset($data[$field]) || empty($data[$field])) {
			return false;
		}		
		return true;
	}
	//验证是否存在字段
	public function isset($field, $value, $params, $data) {
		if (!isset($data[$field])) {
			return false;
		}		
		return true;
	}
	//存在字段时验证失败
	public function exists($field, $value, $params, $data) {
		return isset($data[$field]) ? false : true;
	}
	//验证验证码
	public function captcha($field, $value, $params, $data) {
		return isset($data[$field]) && strtoupper($data[$field]) == Session::get('captcha');
	}	
	//验证字段值唯一
	public function unique($field, $value, $params, $data) {
		$args = explode(',', $params);
		$map = [];
		$map[] = [$field, $value];
		if (isset($data[$args[1]])) {
			$map[] = [$args[1], '<>', $data[$args[1]]];
		}
		$isFind = Db::table($args[0])->where($map)->find();
		return empty($value) || !$isFind ? true : false;
	}
	//验证用户名及长度
	public function user($field, $value, $params, $data) {
		$params = explode(',', $params);	
		$preg = '/^[\x{4e00}-\x{9fa5}a-z0-9]{'.$params[0].','.$params[1].'}$/ui';
		return preg_match($preg, $value) ? true : false;
	}
	//验证密码格式及长度
	public function pwd($field, $value, $params, $data) {
		$params = explode(',', $params);
		$preg = '/^[a-z0-9]{'.$params[0].','.$params[1].'}$/ui';
		return preg_match($preg, $value) ? true : false;
	}	
	//验证字符串长度范围
	public function length($name, $value, $params) {
		$len = mb_strlen($value, 'utf-8');
		$params = explode(',', $params);
		if ($len >= $params[0] && $len <= $params[1]) {
			return true;
		}
		return false;
	}	
	//最大长度验证
	public function max($name, $value, $params) {
		$len = mb_strlen(trim($value), 'utf-8');
		return ($len <= $params)? true : false;
	}
	//最小长度验证
	public function min($name, $value, $params) {
		$len = mb_strlen(trim($value), 'utf-8');
		return ($len >= $params)? true : false;
	}
	//网址验证
	public function url($name, $value, $params) {
		$preg = '/^https?:\/\/(\w+\.)?[\w\-\.]+(\.\w+)+/';
		return preg_match($preg, $value) ? true : false;
	}
	//验证身份证号
	public function idcard($name, $value, $params) {
		$preg = '/^(\d{15}$|^\d{18}$|^\d{17}(\d|X|x))$/';
		return preg_match($preg, $value) ? true : false;
	}
	//验证银行卡号
	public function bankcard($name, $value, $params) {
		$preg = '/^[1-9][0-9]{18}$/';
		return preg_match($preg, $value) ? true : false;
	}	
	//邮箱验证
	public function email($name, $value, $params) {
		$preg = "/([a-z0-9]*[-_.]?[a-z0-9]+)*@([a-z0-9]*[-_]?[a-z0-9]+)+[.][a-z]{2,3}([.][a-z]{2})?/i";
		return preg_match($preg, $value) ? true : false;
	}
	//QQ验证
	public function qq($name, $value, $params) {
		return preg_match('/^[1-9][0-9]{5,15}$/', $value)? true : false;
	}	
	//数字范围
	public function between($name, $value, $params)	{
		$params = explode(',', $params);
		if (intval($value) >= $params[0] && intval($value) <= $params[1]) {
			return true;
		}
		return false;
	}	
	//验证正则表达式
	public function regex($name, $value, $preg) {
		return preg_match($preg, $value) ? true : false;
	}	
	//表单字段比对
	public function confirm($name, $value, $params, $data) {
		return ($value == $data[$params]) ? true : false;
	}
	//验证手机号
	public function phone($name, $value, $params) {
		$preg = '/^1[3456789]\d{9}$/';
		return preg_match($preg, $value) ? true : false;
	}
	//验证可包含[中文，字母，数字，-_]
	public function alpha($name, $value, $params) {
		$preg = '/[a-zA-Z\x7f-\xff0-9-_]+/';
		return preg_match($preg, $value) ? true : false;
	}
	//是否为纯数字，不包含负数和小数点
	public function number($name, $value, $params) {
		return ctype_digit($value)? true : false;
	}	
	//是否为浮点数字
	public function float($name, $value, $params) {
		return filter_var($value, FILTER_VALIDATE_FLOAT)? true : false;
	}
}