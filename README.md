##验证处理

validate组件能快速实现表单验证处理

###安装组件

使用 composer命令进行安装或下载源代码使用(依赖config,request,db,session组件)。

    composer require willphp/validate

>WillPHP框架已经内置此组件，无需再安装。

###验证常量(必须定义)

	const AT_MUST = 1; //必须
	const AT_NOT_NULL = 2; //有值
	const AT_NULL = 3; //空值
	const AT_SET = 4; //有字段
	const AT_NOT_SET = 5; //无字段

###验证配置

`config/validate.php`配置文件可设置：
	
	//ajax请求时验证失败显示的json数据模板
	'validate_ajax' => [
			'msg' => '验证失败',
			'code' => 400,
			'status' => 0,
			'url' => 'javascript:history.back(-1);'
	],
	//验证响应处理：redirect跳转来源页,show显示show模板,不设置返回false
	'dispose'  => 'show',
	'template' => '', //show模板		

###规则配置

可以在 config/regex.php 配置文件中定义自已的正则验证规则，如： 

	'string' => '/^\w+$/', //数字字母下划线
	'number' => '/^[0-9]*$/', //正数
	'chs' => '/^[\x7f-\xff]+$/', //汉字
	'mobile' => '/^1[3-9]\d{9}$/', //手机号
	'qq' => '/^[1-9][0-9]{4,12}$/', //qq号
	'idcard' => '/^(\d{15}$|^\d{18}$|^\d{17}(\d|X|x))$/', //身份证号
	'bankcard' => '/^[1-9][0-9]{18}$/', //银行卡号

###内置规则

    required    字段必须存在且不能为空
    exists      必须有字段
    notExists   必须无字段
    unique      唯一验证 格式:unique:表名,主键
    captcha     验证码 验证==session('captcha')
    confirm     字段相等 如：confirm:password
    regex:正则	正则验证 如：regex:/^\d{5,20}$/       
    len         长度范围 如：length:5,20
    max         最大长度 如：max:10
    min         最小长度 如：min:5    
    between     数字范围 如：between:1,9
    in          在...里面 如：in:red,blue,green
    notin       不在...里面 如：notin:1,2,3
    url         验证url(filter_var)
    email       验证邮箱(filter_var)
    ip          验证ip(filter_var)
    float       验证浮点数(filter_var)
    int         验证数字(filter_var)
    boolean     验证是否(filter_var)

###规则设置

    $rule = [
        ['表单字段', '验证规则[|...]', '错误提示[|...]', '[条件-验证常量]']
    ];

###验证示例

    //增加规则(可选)
    Validate::extend('checkNum', function($value,$field,$params,$data){         
            return $value > 100; 
    }); 
    $rule = [
        ['username', 'required|/^\w{5,20}$/', '用户必须|用户格式错误', AT_MUST], //必须
        ['email', 'email|unique:users,id', '邮箱格式错误|邮箱已存在', 2], //有值时
        ['iq', 'checkNum', 'iq必须大于100']; //必须   
        ['test', function($value) {return $value==100;}, 'test必须等于100', 4]; //有字段       
    ];
    $data = ['username'=>'admin', 'email'=>'123', 'iq'=>20];    
    Validate::make($rule, $data);
    
###验证响应

当dispose设置为default时，获取验证结果：

    if (!Validate::isFail) {
        echo '验证通过';
    } else {
        dump(Validate::getError());
    }       
