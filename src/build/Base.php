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
use willphp\config\Config;
use willphp\request\Request;
use willphp\session\Session;
use willphp\validate\Validate;
/**
 * 验证处理
 * Class Base
 * @package willphp\validate\build;
 * @author  no-mind
 */
class Base extends ValidateRule {	
	protected $validate = []; //扩展验证规则	 
	protected $error = []; //错误信息	
	/**
	 * 数据验证
	 * @param       $validates 验证规则
	 * @param array $data      数据
	 * @return $this
	 */
	public function make($validates, array $data = []) {
		$this->error = [];
		$data = $data ? $data : Request::post();
		foreach ($validates as $validate) {			
			//字段名
			$fieldName = $validate[0];
			//初始字段错误提示
			if (!isset($this->error[$fieldName])) {
				$this->error[$fieldName] = '';
			}
			//验证条件
			$validate[3] = isset($validate[3]) ? $validate[3] : Validate::MUST_VALIDATE;
			if ($validate[3] == Validate::EXISTS_VALIDATE && ! isset($data[$validate[0]])) {
				continue;
			} else if ($validate[3] == Validate::VALUE_VALIDATE && empty($data[$validate[0]])) {
				//不为空时处理
				continue;
			} else if ($validate[3] == Validate::VALUE_NULL && ! empty($data[$validate[0]])) {
				//值为空时处理
				continue;
			} else if ($validate[3] == Validate::NO_EXISTS_VALIDATE && isset($data[$validate[0]])) {
				//值为空时处理
				continue;
			} else if ($validate[3] == Validate::MUST_VALIDATE) {
				//必须处理
			}
			//表单值
			$value = isset($data[$validate[0]]) ? $data[$validate[0]] : '';
			//验证规则
			if ($validate[1] instanceof \Closure) {
				$method = $validate[1];
				//闭包函数
				if ($method($value) !== true) {
					$this->error[$fieldName] = $validate[2].PHP_EOL;
				}
			} else {
				$actions = explode('|', $validate[1]);
				foreach ($actions as $action) {
					$info   = explode(':', $action);
					$method = $info[0];
					$params = isset($info[1]) ? $info[1] : '';
					if (method_exists($this, $method)) {
						//类方法验证
						if ($this->$method($validate[0], $value, $params, $data) !== true) {
							$this->error[$fieldName] .= '<br/>'.$validate[2];
						}
					} else if (isset($this->validate[$method])) {
						$callback = $this->validate[$method];
						if ($callback instanceof \Closure) {
							//闭包函数
							if ($callback($validate[0], $value, $params, $data) !== true) {
								$this->error[$fieldName] = $validate[2];
							}
						}
					}
					$this->error[$fieldName] = trim($this->error[$fieldName],'<br/>');
				}
			}
		}		
		$this->error = array_filter($this->error);		
		//验证返回信息处理
		return $this->respond($this->error);
	}	
	/**
	 * 验证返回信息处理
	 * @param array $errors 错误内容
	 * @return bool
	 */
	public function respond(array $errors) {		
		//验证返回信息处理
		if (count($errors) > 0) {
			if (Request::isAjax()) {				
				$ajax = Config::get('validate.validate_ajax', ['msg'=>'验证失败', 'status'=>0]);
				$ajax['msg'] = implode(';', $errors);
				header('Content-type: application/json;charset=utf-8');
				$res = json_encode($ajax, JSON_UNESCAPED_UNICODE);				
				exit($res);
			} else {				
				//错误信息记录
				Session::flash('errors', $errors);				
				switch (Config::get('validate.dispose')) {
					case 'redirect':
						header("Location:".$_SERVER['HTTP_REFERER']);
						die;
					case 'show':						
						$template = Config::get('validate.template');
						if (!file_exists($template)) {
							$template =  __DIR__.'/../view/validate.php';
						}
						ob_start();
						include $template;
						$res = ob_get_clean();						
						exit($res);
					default:
						return false;
				}				
			}
		}		
		return true;
	}	
	/**
	 * 添加验证闭包
	 * @param $name
	 * @param $callback
	 */
	public function extend($name, $callback) {
		if ($callback instanceof \Closure) {
			$this->validate[$name] = $callback;
		}
	}	
	/**
	 * 验证判断是否失败
	 * @return bool
	 */
	public function fail() {
		return !empty($this->error);
	}	
	/**
	 * 获取错误信息
	 * @return array
	 */
	public function getError() {
		return $this->error;
	}
}