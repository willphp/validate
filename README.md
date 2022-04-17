# 验证处理

validate组件能快速实现表单验证处理

#开始使用

####安装组件
使用 composer 命令进行安装或下载源代码使用(依赖willphp/config组件)。

    composer require willphp/validate

> WillPHP 框架已经内置此组件，无需再安装。

####使用示例

    \willphp\validate\Validate::make($rule, $data); //根据规则$rule验证数据$data(默认为$_POST)

####验证配置

`config/validate.php`配置文件可设置：
	
	//ajax请求时验证失败显示的json数据模板
	'validate_ajax' => [
			'msg' => '验证失败',
			'code' => 400,
			'status' => 0,
			'url' => 'javascript:history.back(-1);'
	],
	/*
	 |--------------------------------------------------------------------------
	 | 验证失败处理方式
	 |--------------------------------------------------------------------------
	 | redirect 直接跳转,会分配$errors到前台
	 | show 直接显示错误信息,使用以下配置的模板文件显示错误处理
	 | default 返回false
	 */
	'dispose'  => 'show',
	//当dispose为show时使用以下定义的模板显示错误信息
	'template' => __DIR__.'/../system/view/validate.php',		

####验证规则

已设置的规则：

	required	字段必须存在且不能为空
	isset 		不存在字段时验证失败
	exists		存在字段时验证失败
	captcha		验证码
	unique		唯一验证 如:unique:user,id (id为表主键）
	user		用户名及长度 如：user:5,20
	pwd		密码格式及长度 如：pwd:5,20
	length		长度范围(位数) 如 :length:5,20
	max		最大长度 如：max:10
	min		最小长度 如：min:10
	between		数字范围 如：between:1,9
	regex		正则 如：regex:/^\d{5,20}$/ 
	confirm		字段值比对 如：confirm:repassword
	alpha		包含[中文，字母，数字，-_]
	number		纯数字(不包含负数和小数点)
	float		浮点数
	url		网址
	email		电子邮箱
	phone		手机号
	qq		QQ号
	idcard		身份证号
	bankcard	银行卡号


####规则设置

	$rule = []; //格式：[字段名,验证方法,错误信息,验证条件]
	$rule[] = ['username', 'user:5,20', '用户名5到20个字符', 3];
	$rule[] = ['email', 'email', '电子邮箱格式错误', 3];
	$rule[] = [ 'email', 'unique:users,id', 'E-mail已经存在', 3 ];
	$rule[] = ['test', function($value) {return $value >100}, 'test数字必须大于100', 3];

验证条件 (可选)： 

	1	有字段时 
	2	值不为空时
	3	必须处理 (默认)
	4	值为空时
	5       不存在字段时处理

####增加规则

	Validate::extend('checkNum', function($field, $value, $params){			
			return $value > 100; 
	});

####验证结果

当设置'dispose'  => 'default', 可自定义验证结果处理：

	if  (!Validate::fail) {
		echo '验证通过';
	} else {
		dump(Validate::getError());
	}
