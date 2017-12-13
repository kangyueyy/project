<?php
/**
 * DooValidator class file.
 *
 * @author Leng Sheng Hong <darkredz@gmail.com>
 * @link http://www.doophp.com/
 * @copyright Copyright &copy; 2009 Leng Sheng Hong
 * @license http://www.doophp.com/license
 */

/**
 * A helper class that helps validating data.
 *
 * <p>DooValidator can be used for form and Model data validation before saving/inserting/deleting a data.</p>
 *
 * <p>To use DooValidator, you have to create an instance of it and defined the validation rules.
 * All the methods start with 'test' can be used as a rule for validating data. Rule names are case insensitive.</p>
 *
 * <p>You can pass in custom error message along with the rules. By default all fields in the rules are <b>required</b></p>
 * <code>
 * $rules = array(
 *              'creattime'=>array(
 *                    array('datetime'),
 *                    array('email'),
 *                    array('optional')   //Optional field
 *              ),
 *              'username'=>array(
 *                    array('username',6,16),
 *                    //Custom error message will be used
 *                    array('lowercase', 'Username must only be lowercase.')
 *               ),
 *
 *               //Only one rule
 *               'pwd'=>array('password'),
 *               'email'=>array('email'),
 *               'age'=>array('between',13,200),
 *               'today'=>array('date','mm/dd/yy'),
 *
 *               //Custom rules, static method required
 *               'a'=>array('custom', 'MainController::isA'),
 *
 *               //Custom Required field message.
 *               'content'=>array('required', 'Content is required!')
 *        );
 * </code>
 *
 * <p>Rules are defined based on the validation method's parameters. For example:</p>
 * <code>
 * //Validation method
 * testBetween($value, $min, $max, $msg=null)
 *
 * $rule = array(
 *     'field'=>array('between', 0, 20)
 *     'field2'=>array('between', 0, 20, 'Custom Err Msg!')
 * );
 * </code>
 *
 * <p>You can get the list of available validation rules by calling Validator::getAvailableRules()</p>
 *
 * <p>To validate the data, create an instance of DooValidator and call validate() in your Controller/Model.</p>
 * <code>
 * $v = new DooValidator();
 *
 * # There are 3 different validation Mode.
 * //$v->checkMode = Validator::CHECK_SKIP;
 * //$v->checkMode = Validator::CHECK_ALL_ONE;
 * //$v->checkMode = Validator::CHECK_ALL;
 *
 * //The $_POST or data pass in need to be an assoc array
 * //$data = array('username'=>'doophp', 'pwd'=>'12345');
 * if($error = $v->validate($_POST, $rules)){
 *      print_r($error);
 * }
 * </code>
 *
 * <p>You can pass in a string to load predefined Rules which located at SITE_PATH/protected/config/forms/</p>
 * <code>
 * <?php
 * //in protected/config/forms/example.php
 * return array(
 *      'username'=>array(
 *                      array('username',4,5,'username invalid'),
 *                      array('maxlength',6,'This is too long'),
 *                      array('minlength',6)
 *                  ),
 *      'pwd'=>array('password',3,5),
 *      'email'=>array('email')
 *  );
 * ?>
 *
 * //in your Controller/Model
 * $error = $v->validate($data, 'example');
 * </code>
 *
 * <p>If nothing is returned from the validate() call, it means that all the data passed the validation rules.</p>
 *
 * <p>The Model validation rules are automatically generated when you used the framework's model generator feature.
 * If your Models are extending DooModel or DooSmartModel, you can validate the data by calling DooModel::validate()</p>
 * <code>
 * $f = new Food;
 * $f->name = 'My Food name for validation';
 * $error = $f->validate();
 *
 * //Or...
 * $error = Food::_validate($f);
 * </code>
 *
 * @author Leng Sheng Hong <darkredz@gmail.com>
 * @version $Id: DooValidator.php 1000 2009-08-30 11:37:22
 * @package doo.helper
 * @since 1.2
 */
class ValidatorClass {
	/**
	 * @example: rule file
	 *  <code>
	 * return array(
	 *	'title' => array(
	 *		array( 'maxlength', 10, 'Title cannot be longer than the 10 characters.' ),
	 *		array( 'notnull' ),
	 *		array( 'notEmpty', 'Title cannot be empty.' ),
	 *	),
	 *	'id'=>array(
	 *		array('integer', 'Invalid Post ID'),
	 *	),
	 *	'content' => array(
	 *		array( 'maxlength', 100, 'Content cannot be longer than the 100 characters.' ),
	 *		array( 'notnull' ),
	 *		array( 'notEmpty', 'Post content cannot be empty!' ),
	 *	),
	 *	);
	 * </code>
	 */
	/**
	 * Checks All and returns All errors
	 * 检查全部并返回每个检查字段的全部错误
	 * @example: 如title与content都超出限制长度
	 * @return: array(
	 *  'title' => array(
	 *		array( 'maxlength', 10, 'Title cannot be longer than the 10 characters.' ),
	 *	),
	 *	 'content' => array(
	 *		array( 'maxlength', 100, 'Content cannot be longer than the 100 characters.' ),
	 *	)
	 *);
	 */
	const CHECK_ALL = 'all';

	/**
	 * Checks All and returns one error for each data field
	 * 检查全部并返回每个检查字段的第一个错误
	 * @example: 如title与content都超出限制长度
	 * @return: array(
	 *  'title' => 'Title cannot be longer than the 10 characters.',
	 *  'content' => 'Content cannot be longer than the 100 characters.'
	 *);
	 */
	const CHECK_ALL_ONE = 'all_one';

	/**
	 * Returns one error once the first is detected
	 * 检查到有一个错误就返回
	 * @example: 如title与content都超出限制长度
	 * @return: 'Title cannot be longer than the 10 characters.'
	 */
	const CHECK_SKIP = 'skip';

	/**
	 * Use PHP empty method to test for required (or optional)
	 */
	const REQ_MODE_NULL_EMPTY = 'nullempty';

	/**
	 * Only ensure required fields are non null / accept not null on required
	 */
	const REQ_MODE_NULL_ONLY = 'null';

	/**
	 * Default require message to display field name "first_name is required."
	 */
	const REQ_MSG_FIELDNAME = 'fieldname';

	/**
	 * Default require message to display "This is required."
	 */
	const REQ_MSG_THIS = 'this';

	/**
	 * Default require message to convert field name with underscore to words. eg(field = first_name). "First name is required."
	 */
	const REQ_MSG_UNDERSCORE_TO_SPACE = 'underscore';

	/**
	 * Default require message to convert field name with camelcase to words. eg(field = firstName). "First name is required."
	 */
	const REQ_MSG_CAMELCASE_TO_SPACE = 'camelcase';

	/**
	 * Validation mode
	 * @var string all/all_one/skip
	 */
	public $checkMode = 'all';

	/**
	 * How should required fields be tested (or be considered left out on optional)
	 * @var string empty/null
	 */
	public $requireMode = 'nullempty';

	/**
	 * Default method to generate error message for a required field.
	 * @var string
	 */
	public $requiredMsgDefaultMethod = 'underscore';

	/**
	 * Default error message suffix for a required field.
	 * @var string
	 */
	public $requiredMsgDefaultSuffix = ' field is required';

	public static $prm_base;
	/**
	 * Trim the data fields. The data will be modified.
	 * @param array $data assoc array to be trimmed
	 * @param int $maxDepth Maximum number of recursive calls. Defaults to a max nesting depth of 5.
	 */
	public function trimValues(&$data, $maxDepth = 5) {
		foreach ($data as $k => &$v) {
			if (is_array($v)) {
				if ($maxDepth > 0) {
					$this->trimValues($v, $maxDepth - 1);
				}
			} else {
				$v = trim($v);
			}
		}
	}

	/**
	 * Get a list of available rules
	 * @return array
	 */
	public static function getAvailableRules() {
		return array('alpha', 'alphaNumeric', 'between', 'betweenInclusive', 'ccAmericanExpress', 'ccDinersClub', 'ccDiscover', 'ccMasterCard',
					'ccVisa', 'colorHex', 'creditCard', 'custom', 'date', 'dateBetween', 'datetime', 'digit', 'email', 'equal', 'equalAs', 'float',
					'greaterThan', 'greaterThanOrEqual', 'ip', 'integer', 'lessThan', 'lessThanOrEqual', 'lowercase', 'max',
					'maxlength', 'min', 'minlength', 'notEmpty', 'notEqual', 'notNull', 'password', 'passwordComplex', 'price', 'regex',
					'uppercase', 'url', 'username', 'dbExist', 'dbNotExist', 'alphaSpace', 'notInList', 'inList','array',
					'string','input','textarea','user','id'
				);
	}

	/**
	 * Get appropriate rules for a certain DB data type
	 * @param string $dataType
	 * @return string Rule name for the data type
	 */
	public static function dbDataTypeToRules($type){
		$dataType = array(
						//integers
						'tinyint'=>'integer',
						'smallint'=>'integer',
						'mediumint'=>'integer',
						'int'=>'integer',
						'bigint'=>'integer',
						'id'=>'integer',

						//float
						'float'=>'float',
						'double'=>'float',
						'decimal'=>'float',

						//datetime
						'date'=>'date',
						'datetime'=>'datetime',
						'timestamp'=>'datetime',
						'time'=>'datetime'
					);
		if(isset($dataType[$type]))
			return $dataType[$type];
	}

	public static function getDefaultValue($type){
		$dataType = array(
						//string
						'string'=>'',
						'text'=>'',
						'input'=>'',
						'textarea'=>'',
						'user'=>'',
						//array
						'array'=>array(),
						//number
						'integer'=>0,
						'float'=>0,
						//datetime
						'date'=>'1970-1-1',
						'datetime'=>'1970-1-1 00:00:00',
						'timestamp'=>0,
					);
		if(isset($dataType[$type]))
			return $dataType[$type];
	}

	public static function getBaseRules(){
		return array(
			//'uri_enterpriseno' => array(array('integer')),
			//'uno' => array(array('integer')),
			//'eno' => array(array('integer')),
			//'eid' => array(array('integer')),
			//'enterpriseno' => array(array('integer')),
			//'userno' => array(array('integer')),
			//'employeeid' => array(array('integer')),
			);
	}

	/*
	 * 封装检查方法，暂不用
	 * General.20141021
	 */
	public static function chk($data = array(),$rulefile = '',$errtype = 'json',$chktype = 'skip',$url = '',$type = '') {
		if(!is_array($data) || empty($data) || empty($rulefile)){
			return;
		}
		$chktype = strtolower((string) $chktype); //指定为小写的字符串
		$errtype = strtolower((string) $errtype); //指定为小写的字符串
//        $path = strtolower((string) $path); //指定为小写的字符串
//        if(empty($path)){
//            $path = 'forms';
//        }
		$validator = new Validator;
		//$validator->checkMode = Validator::CHECK_SKIP;
		$validator->checkMode = $chktype;
		$error = $validator->validate($data, $rulefile);
		if (empty($url)) {//默认返回首页
			$url = '/home/index';
		}
		if($error){
			switch($errtype){
				case 'json':
					$res['success'] = false;
					$res['message'] = $error;
					echo json_encode($res);
					exit;
					break;
				case 'page':
					//Doo::loadClass('UserCommon');
					return UserCommonClass::showMsg($error, $url,$type);
					/*$data['rootUrl'] = Doo::conf()->APP_URL;
					$data['title'] =  'Error Occured!';
					$data['content'] =  '<p style="color:#ff0000;">'.$error.'</p>';
					$data['content'] .=  '<p>Go <a href="javascript:history.back();">back</a> to edit.</p>';
					$this->render('news/error_', $data);*/
					break;
			}
		}else{
			return;
		}
	}
	/**
	 * Validate the data with the defined rules.
	 *
	 * @param array $data Data to be validate. One dimension assoc array, eg. array('user'=>'leng', 'email'=>'abc@abc.com')
	 * @param string|array $rules Validation rule. Pass in a string to load the predefined rules in SITE_PATH/protected/config/forms
	 * @return array Returns an array of errors if errors exist.
	 */
	public function validate($data=null, $rules=null, &$return,&$oaret=array(), $prm_base = null){
		//$data = array('username'=>'leng s', 'pwd'=>'234231dfasd', 'email'=>'asdb12@#asd.com.my');
		//$rules = array('username'=>array('username'), 'pwd'=>array('password',6,32), 'email'=>array('email'));
		if(is_string($rules)){
			//$rules = include(Doo::conf()->SITE_PATH .  Doo::conf()->PROTECTED_FOLDER . 'config/forms/'.$rules.'.php');
			$rules = Doo::loadRules($rules);
		}
		$rules = array_merge(self::getBaseRules(),$rules);

		self::$prm_base = $prm_base;

		$return = array();
		$optErrorRemove = array();
		$errors = null;
		// foreach($data as $dk=>$dv){
		// 	if($this->requireMode == ValidatorClass::REQ_MODE_NULL_EMPTY && ($dv === null || $dv === '') ||
		// 	   $this->requireMode == ValidatorClass::REQ_MODE_NULL_ONLY  && $dv === null){
		// 		unset($data[$dk]);
		// 	}
		// }
		foreach($rules as $fieldname=>$rule_array){
			$fieldtype = $rule_array[0][0];
			$setdefault = false;
			$iserror = false;
			$oaret = null; //每次验证销毁一下上一次的返回
			if(!isset($data[$fieldname])){
				if( in_array('notNull', $rule_array) ){
					$iserror = true;
					//$errors[$fieldname]['notNull'] = array(RetClass::CHECK_PARAM_NOT_NULL,CommonClass::txt('TIPS_CHECK_PARAM_NOT_NULL'));
					$errors = CommonClass::txt('TIPS_CHECK_PARAM_NOT_NULL');//General.20151010 该字段必填(只提示单个错误)
				}else if( in_array('optional', $rule_array) ){
					//$return[$fieldname] =
				}else{
					$setdefault = true;
				}
			}else{
				$value = $data[$fieldname];
				
				// 增加空数据判断
				$value_IsEmpty = ($value === null || $value === '' || $value === 'undefined' || $value === '[object Object]') ? true : false;
				// update by suson 2015-12-02  空值，但是没设置notEmpty验证的情况
				if(in_array('notEmpty',$rule_array) && $value_IsEmpty === true){
					$setdefault = true;
					$iserror = true;
					$errors = CommonClass::txt('TIPS_CHECK_PARAM_NOT_EMPTY');//General.20151010 该字段内容不能为空(只提示单个错误)

				}else{
					foreach($rule_array as $rule_value){
						if(is_array($rule_value)){
							$test_function_params = array_merge(array($value),array_slice($rule_value, 1));

							$retRule = call_user_func_array(array(&$this, 'test'.$rule_value[0]), $test_function_params);
							//echo "<br>retRule:$rule_value[0]|$value:";var_dump($retRule);

							if (is_array($retRule)){
								$oaret = $retRule[0];
								switch ($retRule[0]) {
									case RetClass::SUCCESS:
										$return[$fieldname] = $retRule[1];
										break;
									case RetClass::NONE:
										break;
									default:
										$iserror = true;
										//如果返回的是数组ret，且ret成功则不提示error
                                        if(is_array($retRule[0]) && $retRule[0]['ret'] == RetClass::SUCCESS){
                                            $iserror = false;
                                        }
										//$errors[$fieldname][$rule_value[0]] = $retRule;
										$errors = $retRule[1];//General.20141027 只返回错误文字
										break;
								}
							}
							if ($iserror && $this->checkMode==EnumClass::CHECK_PARAM_SKIP) {
								return $errors;
							}
						}
					}

				}

				/*
				if ($value_IsEmpty){
					$setdefault = true;
					if( in_array('notEmpty', $rule_array) ){
						$iserror = true;
						//$errors[$fieldname]['notEmpty'] = array(RetClass::CHECK_PARAM_NOT_EMPTY,CommonClass::txt('TIPS_CHECK_PARAM_NOT_EMPTY'));
						$errors = CommonClass::txt('TIPS_CHECK_PARAM_NOT_EMPTY');//General.20151010 该字段内容不能为空(只提示单个错误)
					}
				}else{
					foreach($rule_array as $rule_value){
						if(is_array($rule_value)){
							$test_function_params = array_merge(array($value),array_slice($rule_value, 1));

							$retRule = call_user_func_array(array(&$this, 'test'.$rule_value[0]), $test_function_params);
							//echo "<br>retRule:$rule_value[0]|$value:";var_dump($retRule);

							if (is_array($retRule)){
								$oaret = $retRule[0];
								switch ($retRule[0]) {
									case RetClass::SUCCESS:
										$return[$fieldname] = $retRule[1];
										break;
									case RetClass::NONE:
										break;
									default:
										$iserror = true;
										//$errors[$fieldname][$rule_value[0]] = $retRule;
										$errors = $retRule[1];//General.20141027 只返回错误文字
										break;
								}
							}
						}
					}
				}
				*/
			}

			if ($iserror && $this->checkMode==EnumClass::CHECK_PARAM_SKIP) {
				return $errors;
			}

			if ($setdefault){
				$default = null;
				foreach($rule_array as $innerArrayRules){
				   if($innerArrayRules[0] == 'default'){
					   $default = $innerArrayRules[1];
					   break;
				   }
				}
				if (!is_null($default)){
					$return[$fieldname] = $default;
				}else{
					//公共默认值
					$return[$fieldname] = ValidatorClass::getDefaultValue($fieldtype);
				}
			}
		}

		if (is_array($errors)){
			return $errors;
		}
	}

	/**
	 * Set default settings to display the default error message for required fields
	 * @param type $displayMethod Default error message display method. use: ValidatorClass::REQ_MSG_UNDERSCORE_TO_SPACE, ValidatorClass::REQ_MSG_CAMELCASE_TO_SPACE, ValidatorClass::REQ_MSG_THIS, ValidatorClass::REQ_MSG_FIELDNAME
	 * @param type $suffix suffix for the error message. Default is ' field is required'
	 */
	public function setRequiredFieldDefaults( $displayMethod = ValidatorClass::REQ_MSG_UNDERSCORE_TO_SPACE, $suffix = ' field is required'){
		$this->requiredMsgDefaultMethod = $displayMethod;
		$this->requiredMsgDefaultSuffix = $suffix;
	}

	/**
	 * Get the default error message for required field
	 * @param string $fieldname Name of the field
	 * @return string Error message
	 */
	public function getRequiredFieldDefaultMsg($fieldname){
		if($this->requiredMsgDefaultMethod==ValidatorClass::REQ_MSG_UNDERSCORE_TO_SPACE)
			return ucfirst(str_replace('_', ' ', $fieldname)) . $this->requiredMsgDefaultSuffix;

		if($this->requiredMsgDefaultMethod==ValidatorClass::REQ_MSG_THIS)
			return 'This ' . $this->requiredMsgDefaultSuffix;

		if($this->requiredMsgDefaultMethod==ValidatorClass::REQ_MSG_CAMELCASE_TO_SPACE)
			return ucfirst(strtolower(preg_replace('/([A-Z])/', ' $1', $fieldname))) . $this->requiredMsgDefaultSuffix;

		if($this->requiredMsgDefaultMethod==ValidatorClass::REQ_MSG_FIELDNAME)
			return $fieldname . $this->requiredMsgDefaultSuffix;
	}

	public function testOptional($value){}
	public function testDefault($value){
		//echo "testDefault:"; var_dump($value);
	}
	public function testRequired($value, $msg){
		if ($this->requireMode == ValidatorClass::REQ_MODE_NULL_EMPTY && ($value === null || $value === '') ||
			$this->requireMode == ValidatorClass::REQ_MODE_NULL_ONLY  && $value === null) {

			if($msg!==null) return $msg;
			return 'This field is required!';	}
	}

	/**
	 * Validate data with your own custom rules.
	 *
	 * Usage in Controller:
	 * <code>
	 * public static function isA($value){
	 *      if($value!='a'){
	 *          return 'Value must be A';
	 *      }
	 * }
	 *
	 * public function test(){
	 *     $rules = array(
	 *          'email'=>array('custom', 'TestController::isA')
	 *     );
	 *
	 *     $v = new DooValidator();
	 *     if($error = $v->validate($_POST, $rules)){
	 *          //display error
	 *     }
	 * }
	 * </code>
	 *
	 * @param string $value Value of data to be validated
	 * @param string $function Name of the custom function
	 * @param string $msg Custom error message
	 * @return string
	 * General.20151008 原方法不用
	 */
	public function testCustom2($value, $function, $options=null ,$msg=null){
		if($options==null){
			if($err = call_user_func($function, $value)){
				if($err!==true){
					if($msg!==null) return $msg;
					return $err;
				}
			}
		}else{
			//if array, additional parameters
			if($err = call_user_func_array($function, array_merge(array($value), $options)) ){
				if($err!==true){
					if($msg!==null) return $msg;
					return $err;
				}
			}
		}
	}
	
	//General.20151008 自定义验证方法
	public function testCustom($value, $prm_base, $moduleid, $objid, $function, $options=null ,$msg=null){
		$params = array(
            'moduleid' => $moduleid,
			'objid' => $objid,
			'value' => $value,
			'options' => $options,
			);
		if(!empty($options) && is_array($options)){
			$params = $params + $options;
		}
		$ret = RetClass::CHECK_PARAM_INVALID;
		$retValue = $value;
		//获取模块路径
		$mod = ApiClass::enterprise_get_module_path('3.0.0.0', $prm_base, $moduleid);
		
		if($mod['ret'] == RetClass::SUCCESS && isset($mod['data']['type']) && !empty($mod['data']['type']) && isset($mod['data']['name']) && !empty($mod['data']['name'])){
			$type = $mod['data']['type'];
			$name = $mod['data']['name'];
		}else{
			if($msg!==null){
				$errMsgDefault = $msg;
			}else{
				$errMsgDefault = $ret['attr']['content'];
			}
			return $this->getReturn($ret,$retValue,$errMsgDefault,$msg);
		}
		$ret =  Doo::getModuleClass($type.'/'.$name, ucfirst($name).'Class', $function, '3.0.0.0', $prm_base, $params);
		if($ret['ret'] != RetClass::SUCCESS){
			if($msg!==null){
				$errMsgDefault = $msg;
			}else{
				$errMsgDefault = $ret['attr']['content'];
			}
			
		}else{
			$errMsgDefault = null;
		}
		return $this->getReturn($ret,$retValue,$errMsgDefault,$msg);
	}
	
	/**
	 * Validate against a Regex rule
	 *
	 * @param string $value Value of data to be validated
	 * @param string $regex Regex rule to be tested against
	 * @param string $msg Custom error message
	 * @return string
	 */
	public function testRegex($value, $regex, $msg=null){
		if(!preg_match($regex, $value) ){
			if($msg!==null) return $msg;
			return 'Error in field.';
		}
	}

	/**
	 * Validate Editbox format. 富文本框
	 * General.20150605
	 * @return string
	*/ 
	public function testEditbox($value, $msg=null){
		//对文本框中上传图片标签进行转换
		
		return $this->testText($value, $msg=null);
	}
	
	/**
	 * Validate testId format.针对ID的验证
	 * General.20150914
	 * @param string $value Value of data to be validated
	 * @param string $msg Custom error message
	 * @return string
	 */
	public function testId($value, $msg=null){
		return $this->testString($value, 1, 30, $msg=null);
	}
		
	/**
	 * Validate testTitle format.针对标题的验证
	 * General.20150915
	 * @param string $value Value of data to be validated
	 * @param string $msg Custom error message
	 * @return string
	 */
	public function testTitle($value, $msg=null){
		return $this->testString($value, 1, 100, $msg=null);
	}
	
	/**
	 * Validate testText format.
	 *
	 * @param string $value Value of data to be validated
	 * @param int $minLength Minimum length
	 * @param int $maxLength Maximum length
	 * @param string $msg Custom error message
	 * @return string
	 */
	public function testText($value, $msg=null){
		return $this->testString($value, 0, 8000000, $msg=null);
	}
	
	/**
	 * Validate input format. 文本输入框验证，包括特殊字符的处理
	 * General.20150319
	 * @return string
	*/ 
	public function testInput($value, $minLength=1, $maxLength=12, $msg=null){
		return $this->testString($value, $minLength, $maxLength, $msg=null);
	}
	
	/**
	 * Validate textarea format. 多行文本输入框验证，包括特殊字符的处理、回车换行的转换
	 * General.20150319
	 * @return string
	*/ 
	public function testTextarea($value, $minLength=1, $maxLength=100, $msg=null){
		$value = str_replace("\n\r", "<br />", $value);
		$value = str_replace("\n", "<br />", $value);
		$value = str_replace("\r", "<br />", $value);
		$value = addslashes($value); //转义
		return $this->testString($value, $minLength, $maxLength, $msg);
	}
	
	/**
	 * Validate testString format.
	 *
	 * @param string $value Value of data to be validated
	 * @param int $minLength Minimum length
	 * @param int $maxLength Maximum length
	 * @param string $msg Custom error message
	 * @return string
	 */
	public function testString($value, $minLength=0, $maxLength=5000, $msg=null){
		/* if(!is_string($value) && isset($value) && !empty($value)){
			$errMsgDefault = CommonClass::txt('TIPS_VALIDATE_NOT_STRING');
			return $this->getReturn(RetClass::ERROR,$value,$errMsgDefault,$msg);
		} */
		$ret = RetClass::CHECK_PARAM_INVALID;
        //过滤特殊字符如单引号 (')、双引号 (")、反斜线 backslash (/) 以及空字符NULL,单双引号、反斜线及NULL加上反斜线转义
        //add by james.ou 2017-11-9
        // $retValue = addslashes(trim($value));
        $retValue = trim($value);
		//去掉html标签,统计字数 add by dyn 2016-1-14
		$valuenullhtml = strip_tags(trim($retValue),ENT_QUOTES);
		$length = mb_strlen($valuenullhtml,'UTF-8');
		if( $length < $minLength || $length > $maxLength ){
			$errMsgDefault = CommonClass::txt('TIPS_VALIDATE_MUST_BETWEEN',$value.'@@'.$minLength.'@@'.$maxLength);//General.20150409 必须在指定范围内 "Value length must be between $minLength and $maxLength.";
		}else{
			$ret = RetClass::SUCCESS;
			$errMsgDefault = null;
		}
		return $this->getReturn($ret,$retValue,$errMsgDefault,$msg);
	}
    /**
     * 转义string (html特殊符号（如< > & ’ “）等转化为一个替代的html entity（如：< 对应<）)
     * @author by james.ou 2017-11-15
     * @param type $value
     * @param type $minLength
     * @param type $maxLength
     * @param type $msg
     * @return string
     */
    public function testStringHtml($value, $minLength=0, $maxLength=5000, $msg=null){
        //转义 转换双引号和单引号 html特殊符号（如< > & ’ “）等转化为一个替代的html entity（如：< 对应<）
        $value = htmlspecialchars(addslashes(trim($value)),ENT_QUOTES); 
        return $this->testString($value, $minLength, $maxLength, $msg);
    }

	/**
	 * Validate username format.
	 *
	 * @param string $value Value of data to be validated
	 * @param int $minLength Minimum length
	 * @param int $maxLength Maximum length
	 * @param string $msg Custom error message
	 * @return string
	 */
	public function testUsername($value, $minLength=4, $maxLength=12, $msg=null){
		if(!preg_match('/^[a-zA-Z][a-zA-Z.0-9_-]{'. ($minLength-1) .','.$maxLength.'}$/i', $value)){
			if($msg!==null) return $msg;
			return "User name must be $minLength-$maxLength characters. Only characters, dots, digits, underscore & hyphen are allowed.";
		}
		else if(strpos($value, '..')!==False){
			if($msg!==null) return $msg;
			return "User name cannot consist of 2 continuous dots.";
		}
		else if(strpos($value, '__')!==False){
			if($msg!==null) return $msg;
			return "User name cannot consist of 2 continuous underscore.";
		}
		else if(strpos($value, '--')!==False){
			if($msg!==null) return $msg;
			return "User name cannot consist of 2 continuous dash.";
		}
		else if(strpos($value, '.-')!==False || strpos($value, '-.')!==False ||
				strpos($value, '._')!==False || strpos($value, '_.')!==False ||
				strpos($value, '_-')!==False || strpos($value, '-_')!==False){
			if($msg!==null) return $msg;
			return "User name cannot consist of 2 continuous punctuation.";
		}
		else if(ctype_punct($value[0])){
			if($msg!==null) return $msg;
			return "User name cannot start with a punctuation.";
		}
		else if(ctype_punct( substr($value, strlen($value)-1) )){
			if($msg!==null) return $msg;
			return "User name cannot end with a punctuation.";
		}
	}

	/**
	 * Validate password format
	 *
	 * @param string $value Value of data to be validated
	 * @param int $minLength Minimum length
	 * @param int $maxLength Maximum length
	 * @param string $msg Custom error message
	 * @return string
	 */
	public function testPassword($value, $minLength=6, $maxLength=32, $msg=null){
		$ret = RetClass::CHECK_PARAM_INVALID;
		$retValue = trim($value);
		$length = strlen($retValue);
		if( $length < $minLength || $length > $maxLength ){
			$errMsgDefault = CommonClass::txt('TIPS_VALIDATE_MUST_BETWEEN',$minLength.'@@'.$maxLength);//General.20150409 必须在指定范围内 "Value length must be between $minLength and $maxLength.";
		}else if(!preg_match('/^[a-zA-Z.0-9@#_-]{'.$minLength.','.$maxLength.'}$/i', $value)){
			$errMsgDefault = CommonClass::txt('TIPS_VALIDATE_NOT_PASSWORD',$minLength);//General.20150409 不是密码格式 "Only characters, dots, digits, underscore & hyphen are allowed. Password must be at least $minLength characters long.";
		}else{
			$ret = RetClass::SUCCESS;
			$errMsgDefault = null;
		}
		return $this->getReturn($ret,$retValue,$errMsgDefault,$msg);
	}

	/**
	 * Validate against a complex password format
	 *
	 * @param string $value Value of data to be validated
	 * @param string $msg Custom error message
	 * @return string
	 */
	public function testPasswordComplex($value, $msg=null){
		if(!preg_match('A(?=[-_a-zA-Z0-9]*?[A-Z])(?=[-_a-zA-Z0-9]*?[a-z])(?=[-_a-zA-Z0-9]*?[0-9])[-_a-zA-Z0-9]{6,32}z', $value)){
			if($msg!==null) return $msg;
			return 'Password must contain at least one upper case letter, one lower case letter and one digit. It must consists of 6 or more letters, digits, underscores and hyphens.';
		}
	}

	/**
	 * Validate email address
	 *
	 * @param string $value Value of data to be validated
	 * @param string $msg Custom error message
	 * @return string
	 */
	public function testEmail($value, $msg=null){
		// Regex based on best solution from here: http://fightingforalostcause.net/misc/2006/compare-email-regex.php
		$retValue = trim($value);
		if(empty($retValue)){
			return $this->getReturn(RetClass::SUCCESS,$retValue,null,$msg);
		}
		$ret = RetClass::ERROR;
		if(!preg_match('/^([\w\!\#$\%\&\'\*\+\-\/\=\?\^\`{\|\}\~]+\.)*[\w\!\#$\%\&\'\*\+\-\/\=\?\^\`{\|\}\~]+@((((([a-z0-9]{1}[a-z0-9\-]{0,62}[a-z0-9]{1})|[a-z])\.)+[a-z]{2,6})|(\d{1,3}\.){3}\d{1,3}(\:\d{1,5})?)$/i', $value) ||
			strpos($value, '--')!==False || strpos($value, '-.')!==False
		){

			$errMsgDefault = CommonClass::txt('TIPS_CHECK_PARAM_EMAIL');
			// if($msg!==null) return $msg;
			// return 'Invalid email format!';
		}else{
			$ret = RetClass::SUCCESS;
			$errMsgDefault = null;
		}
		return $this->getReturn($ret,$value,$errMsgDefault,$msg);
	}


	/**
	 * [testMobile 手机号码格式验证，拿js验证正则]
	 * suson.20160303
	 * @param  [type] $value [description]
	 * @param  [type] $msg   [description]
	 * @return [type]        [description]
	 */
	public function testMobile($value,$msg=null){

		$retValue = trim($value);
		if(empty($retValue)){
			return $this->getReturn(RetClass::SUCCESS,$retValue,null,$msg);
		}
		$ret = RetClass::ERROR;
		$errMsgDefault = null;
		$match = array();
		$match[] = '/^13[3]{1}\d{8}$|15[3]{1}\d{8}$|17[7]{1}\d{8}$|18[019]{1}\d{8}$/';
		$match[] = '/^13[0-2]{1}\d{8}$|15[56]{1}\d{8}$|17[6]{1}\d{8}$|18[56]{1}\d{8}$/';
		$match[] = '/^13[4-9]{1}\d{8}$|14[7]{1}\d{8}$|15[012789]{1}\d{8}$|18[23478]{1}\d{8}$|17[8]{1}\d{8}$/';
		$match[] = '/^170[059]{1}\d{7}$/';
		foreach($match as $v){
			if(preg_match($v, $value)){
				$ret = RetClass::SUCCESS;
				break;
			}else{
				$ret = RetClass::ERROR;
				$errMsgDefault = CommonClass::txt('TIPS_CHECK_PARAM_MOBILE');
			}
		}
		return $this->getReturn($ret,$value,$errMsgDefault,$msg);
	}

	/**
	 * Validate a URL
	 *
	 * @param string $value Value of data to be validated
	 * @param string $msg Custom error message
	 * @return string
	 */
	public function testUrl($value, $msg=null){
		if(!preg_match('/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i', $value)){
			if($msg!==null) return $msg;
			return 'Invalid URL!';
		}
	}

	/**
	 * Validate an IP address (198.168.1.101)
	 *
	 * @param string $value Value of data to be validated
	 * @param string $msg Custom error message
	 * @return string
	 */
	public function testIP($value, $msg=null){
		//198.168.1.101
		if (!preg_match('/^(([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]).){3}([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/',$value)) {
			if($msg!==null) return $msg;
			return 'Invalid IP address!';
		}
	}

	/**
	 * Validate a credit card number
	 *
	 * @param string $value Value of data to be validated
	 * @param string $msg Custom error message
	 * @return string
	 */
	public function testCreditCard($value, $msg=null){
		//568282246310632
		if (!preg_match('/^(?:4[0-9]{12}(?:[0-9]{3})?|5[1-5][0-9]{14}|6011[0-9]{12}|3(?:0[0-5]|[68][0-9])[0-9]{11}|3[47][0-9]{13})$/', $value)) {
			if($msg!==null) return $msg;
			return 'Invalid credit card number!';
		}
	}

	/**
	 * Validate an American Express credit card number
	 *
	 * @param string $value Value of data to be validated
	 * @param string $msg Custom error message
	 * @return string
	 */
	public function testCcAmericanExpress($value, $msg=null){
		if (!preg_match('/^3[47][0-9]{13}$/', $value)) {
			if($msg!==null) return $msg;
			return 'Invalid American Express credit card number!';
		}
	}

	/**
	 * Validate an Discover credit card number
	 *
	 * @param string $value Value of data to be validated
	 * @param string $msg Custom error message
	 * @return string
	 */
	public function testCcDiscover($value, $msg=null){
		if (!preg_match('/^6011[0-9]{12}$/', $value)) {
			if($msg!==null) return $msg;
			return 'Invalid Discover credit card number!';
		}
	}

	/**
	 * Validate an Diners Club credit card number
	 *
	 * @param string $value Value of data to be validated
	 * @param string $msg Custom error message
	 * @return string
	 */
	public function testCcDinersClub($value, $msg=null){
		if (!preg_match('/^3(?:0[0-5]|[68][0-9])[0-9]{11}$/', $value)) {
			if($msg!==null) return $msg;
			return 'Invalid Diners Club credit card number!';
		}
	}

	/**
	 * Validate an Master Card number
	 *
	 * @param string $value Value of data to be validated
	 * @param string $msg Custom error message
	 * @return string
	 */
	public function testCcMasterCard($value, $msg=null){
		if (!preg_match('/^5[1-5][0-9]{14}$/', $value)) {
			if($msg!==null) return $msg;
			return 'Invalid Master Card number!';
		}
	}

	/**
	 * Validate an Visa Card number
	 *
	 * @param string $value Value of data to be validated
	 * @param string $msg Custom error message
	 * @return string
	 */
	public function testCcVisa($value, $msg=null){
		if (!preg_match('/^4[0-9]{12}(?:[0-9]{3})?$/', $value)) {
			if($msg!==null) return $msg;
			return 'Invalid Visa card number!';
		}
	}

	/**
	 * Validate Color hex #ff0000
	 *
	 * @param string $value Value of data to be validated
	 * @param string $msg Custom error message
	 * @return string
	 */
	public function testColorHex($value, $msg=null){
		//#ff0000
		if (!preg_match('/^#([0-9a-f]{1,2}){3}$/i', $value)) {
			if($msg!==null) return $msg;
			return 'Invalid color code!';
		}
	}

	//------------------- Common data validation ---------------------

	/**
	 * Validate Date Time
	 *
	 * @param string $value Value of data to be validated
	 * @param string $msg Custom error message
	 * @return string
	 */
	public function testDateTime($value, $msg=null){
		$ret = RetClass::CHECK_PARAM_INVALID;
		$retValue = trim($value);
		$rs = strtotime($retValue);

		if ($rs===false || $rs===-1){
			$errMsgDefault = CommonClass::txt('TIPS_VALIDATE_NOT_DATETIME',$value);//General.20150409 不是有效日期时间格式 'Invalid date time format!';
		}else{
			$ret = RetClass::SUCCESS;
			$errMsgDefault = null;
		}
		return $this->getReturn($ret,$retValue,$errMsgDefault,$msg);
	}

	/**
	 * Validate Date format. Default yyyy/mm/dd.
	 *
	 * <p>Date format: yyyy-mm-dd, yyyy/mm/dd, yyyy.mm.dd
	 * Date valid from 1900-01-01 through 2099-12-31</p>
	 *
	 * @param string $value Value of data to be validated
	 * @param string $dateFormat Date format
	 * @param string $msg Custom error message
	 * @return string
	 */
	public function testDate($value, $format='yyyy-mm-dd', $msg=null, $forceYearLength=false){
		//Date yyyy-mm-dd, yyyy/mm/dd, yyyy.mm.dd
		//1900-01-01 through 2099-12-31

		$ret = RetClass::CHECK_PARAM_INVALID;
		$retValue = trim($value);
		$yearFormat = "(19|20)?[0-9]{2}";
		if ($forceYearLength == true) {
			if (strpos($format, 'yyyy') !== false) {
				$yearFormat = "(19|20)[0-9]{2}";
			} else {
				$yearFormat = "[0-9]{2}";
			}
		}

		switch($format){
			case 'dd/mm/yy':
				$format = "/^\b(0?[1-9]|[12][0-9]|3[01])[- \/.](0?[1-9]|1[012])[- \/.]{$yearFormat}\b$/";
				break;
			case 'mm/dd/yy':
				$format = "/^\b(0?[1-9]|1[012])[- \/.](0?[1-9]|[12][0-9]|3[01])[- \/.]{$yearFormat}\b$/";
				break;
			case 'mm/dd/yyyy':
				$format = "/^(0[1-9]|1[012])[- \/.](0[1-9]|[12][0-9]|3[01])[- \/.]{$yearFormat}$/";
				break;
			case 'dd/mm/yyyy':
				$format = "/^(0[1-9]|[12][0-9]|3[01])[- \/.](0[1-9]|1[012])[- \/.]{$yearFormat}$/";
				break;
			case 'yy/mm/dd':
				$format = "/^\b{$yearFormat}[- \/.](0?[1-9]|1[012])[- \/.](0?[1-9]|[12][0-9]|3[01])\b$/";
				break;
			case 'yyyy/mm/dd':
			default:
				$format = "/^\b{$yearFormat}[- \/.](0?[1-9]|1[012])[- \/.](0?[1-9]|[12][0-9]|3[01])\b$/";
		}

		if (!preg_match($format, $retValue)) {
			$errMsgDefault = CommonClass::txt('TIPS_VALIDATE_NOT_DATE',$value);//General.20150409 不是日期格式 'Invalid date format!';
		}else{
			$ret = RetClass::SUCCESS;
			$errMsgDefault = null;
		}
		return $this->getReturn($ret,$retValue,$errMsgDefault,$msg);
	}

	/**
	 * Validate if given date is between 2 dates.
	 *
	 * @param string $value Value of data to be validated
	 * @param string $dateStart Starting date
	 * @param string $dateEnd Ending date
	 * @param string $msg Custom error message
	 * @return string
	 */
	public function testDateBetween($value, $dateStart, $dateEnd, $msg=null){
		$ret = RetClass::CHECK_PARAM_INVALID;
		$retValue = trim($value);
		$value = strtotime($retValue);
		if(!( $value > strtotime($dateStart) && $value < strtotime($dateEnd) ) ) {
			$errMsgDefault = CommonClass::txt('TIPS_VALIDATE_MUST_BETWEEN',$value.'@@'.$dateStart.'@@'.$dateEnd);//General.20150409 必须在指定范围内 "Date must be between $dateStart and $dateEnd";
		}else{
			$ret = RetClass::SUCCESS;
			$errMsgDefault = null;
		}
		return $this->getReturn($ret,$retValue,$errMsgDefault,$msg);
	}

	/**
	 * Validate integer
	 *
	 * @param string $value Value of data to be validated
	 * @param int $min Minimum value
	 * @param int $max Maximum value
	 * @param string $msg Custom error message
	 * @return string
	 */
	public function testInteger($value, $min=0, $max=2147483647, $msg=null){
		$ret = RetClass::CHECK_PARAM_INVALID;
		$retValue = (int)$value;
		if($retValue!=$value || strlen($retValue)!=strlen($value)){
			$errMsgDefault = CommonClass::txt('TIPS_VALIDATE_NOT_INTEGER',$value);//General.20150409 输入的不是整数
		}else if( $value < $min || $value > $max ){
			$errMsgDefault = CommonClass::txt('TIPS_VALIDATE_MUST_BETWEEN',$value.'@@'.$min.'@@'.$max);//General.20150409 必须在指定范围内 "Value must be between $min and $max."
		}else{
			$ret = RetClass::SUCCESS;
			$errMsgDefault = null;
		}
		return $this->getReturn($ret,$retValue,$errMsgDefault,$msg);
	}

	/**
	 * Validate price. 2 decimal points only
	 *
	 * @param string $value Value of data to be validated
	 * @param string $msg Custom error message
	 * @return string
	 */
	public function testPrice($value, $msg=null){
		// 2 decimal
		$ret = RetClass::CHECK_PARAM_INVALID;
		if (!preg_match('/^[0-9]*\\.?[0-9]{0,2}$/', $value)){
			$errMsgDefault = CommonClass::txt('TIPS_VALIDATE_NOT_PRICE',$value);//General.20150409 输入不是有效的价格金额
		}else{
			$ret = RetClass::SUCCESS;
			$errMsgDefault = null;
		}
		return $this->getReturn($ret,$value,$errMsgDefault,$msg);
	}

	/**
	 * Validate float value.
	 *
	 * @param string $value Value of data to be validated
	 * @param int $decimal Number of Decimal points
	 * @param string $msg Custom error message
	 * @return string
	 */
	public function testFloat($value, $decimal='', $msg=null){
		// any amount of decimal
		$ret = RetClass::CHECK_PARAM_INVALID;
		if (!preg_match('/^[0-9]*\\.?[0-9]{0,'.$decimal.'}$/', $value)){
			$errMsgDefault = CommonClass::txt('TIPS_VALIDATE_NOT_FLOAT',$value);//General.20150409 输入的不是一个有效的浮点值
		}else{
			$ret = RetClass::SUCCESS;
			$errMsgDefault = null;
		}
		return $this->getReturn($ret,$value,$errMsgDefault,$msg);
	}

	/**
	 * Validate digits.
	 *
	 * @param string $value Value of data to be validated
	 * @param string $msg Custom error message
	 * @return string
	 */
	public function testDigit($value, $msg=null){
		$ret = RetClass::CHECK_PARAM_INVALID;
		if(!ctype_digit($value)){
			$errMsgDefault = CommonClass::txt('TIPS_VALIDATE_NOT_DIGIT',$value);//General.20150409 不是数字
		}else{
			$ret = RetClass::SUCCESS;
			$errMsgDefault = null;
		}
		return $this->getReturn($ret,$value,$errMsgDefault,$msg);
	}

	/**
	 * Validate Alpha numeric values.
	 *
	 * Input string can only consist of only Letters or Digits.
	 *
	 * @param string $value Value of data to be validated
	 * @param string $msg Custom error message
	 * @return string
	 */
	public function testAlphaNumeric($value, $msg=null){
		$ret = RetClass::CHECK_PARAM_INVALID;
		if(!ctype_alnum($value)){
			if($msg!==null) return $msg;
			$errMsgDefault = 'Input can only consist of letters or digits.';
		}else{
			$ret = RetClass::SUCCESS;
			$errMsgDefault = null;
		}
		return $this->getReturn($ret,$value,$errMsgDefault,$msg);
	}

	/**
	 * Validate Alpha values.
	 *
	 * Input string can only consist of only Letters.
	 *
	 * @param string $value Value of data to be validated
	 * @param string $msg Custom error message
	 * @return string
	 */
	public function testAlpha($value, $msg=null){
		$ret = RetClass::CHECK_PARAM_INVALID;
		if(!ctype_alpha($value)){
			$errMsgDefault = 'Input can only consist of letters.';
		}else{
			$ret = RetClass::SUCCESS;
			$errMsgDefault = null;
		}
		return $this->getReturn($ret,$value,$errMsgDefault,$msg);
	}

	/**
	 * Validate if string only consist of letters and spaces
	 *
	 * Input string can only consist of only Letters and spaces.
	 *
	 * @param string $value Value of data to be validated
	 * @param string $msg Custom error message
	 * @return string
	 */
	public function testAlphaSpace($value, $msg=null){
		if(!ctype_alpha(str_replace(' ','',$value))){
			if($msg!==null) return $msg;
			return 'Input can only consist of letters and spaces.';
		}
	}


	/**
	 * Validate lowercase string.
	 *
	 * Input string can only be lowercase letters.
	 *
	 * @param string $value Value of data to be validated
	 * @param string $msg Custom error message
	 * @return string
	 */
	public function testLowercase($value, $msg=null){
		if(!ctype_lower($value)){
			if($msg!==null) return $msg;
			return 'Input can only consists of lowercase letters.';
		}
	}

	/**
	 * Validate uppercase string.
	 *
	 * Input string can only be uppercase letters.
	 *
	 * @param string $value Value of data to be validated
	 * @param string $msg Custom error message
	 * @return string
	 */
	public function testUppercase($value, $msg=null){
		if(!ctype_upper($value)){
			if($msg!==null) return $msg;
			return 'Input can only consists of uppercase letters.';
		}
	}

	/**
	 * Validate Not Empty. Input cannot be empty.
	 *
	 * @param string $value Value of data to be validated
	 * @param string $msg Custom error message
	 * @return string
	 */
	public function testNotEmpty($value, $msg=null){
		// if(empty($value)){
		// 	if($msg!==null) return $msg;
		// 	return 'Value cannot be empty!';
		// }


		$ret = RetClass::CHECK_PARAM_INVALID;
		if(is_string($value)){

			$value = trim($value);
		}

		$value_IsEmpty = ($value === null || $value === '' || $value === 'undefined' || $value === '[object Object]') ? true : false;
		
		if($value_IsEmpty === true){
			$errMsgDefault = CommonClass::txt('TIPS_CHECK_PARAM_NOT_EMPTY');
		}else{
			$ret = RetClass::SUCCESS;
			$errMsgDefault = null;
		}
		return $this->getReturn($ret,$value,$errMsgDefault,$msg);
	}

	/**
	 * Validate Max length of a string.
	 *
	 * @param string $value Value of data to be validated
	 * @param int $length Maximum length of the string
	 * @param string $msg Custom error message
	 * @return string
	 */
	public function testMaxLength($value, $length=0, $msg=null){
		if(mb_strlen($value) > $length){
			if($msg!==null) return $msg;
			return "Input cannot be longer than the $length characters.";
		}
	}

	/**
	 * Validate Minimum length of a string.
	 *
	 * @param string $value Value of data to be validated
	 * @param int $length Minimum length of the string
	 * @param string $msg Custom error message
	 * @return string
	 */
	public function testMinLength($value, $length=0, $msg=null){
		if(strlen($value) < $length){
			if($msg!==null) return $msg;
			return "Input cannot be shorter than the $length characters.";
		}
	}

	/**
	 * Validate Not Null. Value cannot be null.
	 *
	 * @param string $value Value of data to be validated
	 * @param string $msg Custom error message
	 * @return string
	 */
	public function testNotNull($value, $msg=null){
		if(is_null($value)){
			if($msg!==null) return $msg;
			return 'Value cannot be null.';
		}
	}

	/**
	 * Validate Minimum value of a number.
	 *
	 * @param string $value Value of data to be validated
	 * @param int $min Minimum value
	 * @param string $msg Custom error message
	 * @return string
	 */
	public function testMin($value, $min, $msg=null){
		if( $value < $min){
			if($msg!==null) return $msg;
			return "Value cannot be less than $min";
		}
	}

	/**
	 * Validate Maximum value of a number.
	 *
	 * @param string $value Value of data to be validated
	 * @param int $max Maximum value
	 * @param string $msg Custom error message
	 * @return string
	 */
	public function testMax($value, $max, $msg=null){
		if( $value > $max){
			if($msg!==null) return $msg;
			return "Value cannot be more than $max";
		}
	}

	/**
	 * Validate if a value is Between 2 values (inclusive)
	 *
	 * @param string $value Value of data to be validated
	 * @param int $min Minimum value
	 * @param int $max Maximum value
	 * @param string $msg Custom error message
	 * @return string
	 */
	public function testBetweenInclusive($value, $min, $max, $msg=null){
		if( $value < $min || $value > $max ){
			if($msg!==null) return $msg;
			return "Value must be between $min and $max inclusively.";
		}
	}

	/**
	 * Validate if a value is Between 2 values
	 *
	 * @param string $value Value of data to be validated
	 * @param int $min Minimum value
	 * @param int $max Maximum value
	 * @param string $msg Custom error message
	 * @return string
	 */
	public function testBetween($value, $min, $max, $msg=null){
		if( $value < $min+1 || $value > $max-1 ){
			if($msg!==null) return $msg;
			return "Value must be between $min and $max.";
		}
	}

	/**
	 * Validate if a value is greater than a number
	 *
	 * @param string $value Value of data to be validated
	 * @param int $number Number to be compared
	 * @param string $msg Custom error message
	 * @return string
	 */
	public function testGreaterThan($value, $number, $msg=null){
		if( !($value > $number)){
			if($msg!==null) return $msg;
			return "Value must be greater than $number.";
		}
	}

	/**
	 * Validate if a value is greater than or equal to a number
	 *
	 * @param string $value Value of data to be validated
	 * @param int $number Number to be compared
	 * @param string $msg Custom error message
	 * @return string
	 */
	public function testGreaterThanOrEqual($value, $number, $msg=null){
		if( !($value >= $number)){
			if($msg!==null) return $msg;
			return "Value must be greater than or equal to $number.";
		}
	}

	/**
	 * Validate if a value is less than a number
	 *
	 * @param string $value Value of data to be validated
	 * @param int $number Number to be compared
	 * @param string $msg Custom error message
	 * @return string
	 */
	public function testLessThan($value, $number, $msg=null){
		if( !($value < $number)){
			if($msg!==null) return $msg;
			return "Value must be less than $number.";
		}
	}

	/**
	 * Validate if a value is less than or equal to a number
	 *
	 * @param string $value Value of data to be validated
	 * @param int $number Number to be compared
	 * @param string $msg Custom error message
	 * @return string
	 */
	public function testLessThanOrEqual($value, $number, $msg=null){
		if( !($value <= $number)){
			if($msg!==null) return $msg;
			return "Value must be less than $number.";
		}
	}

	/**
	 * Validate if a value is equal to a number
	 *
	 * @param string $value Value of data to be validated
	 * @param int $equalValue Number to be compared
	 * @param string $msg Custom error message
	 * @return string
	 */
	public function testEqual($value, $equalValue, $msg=null){
		if(!($value==$equalValue && strlen($value)==strlen($equalValue))){
			if($msg!==null) return $msg;
			return 'Both values must be the same.';
		}
	}

	/**
	 * Validate if a value is Not equal to a number
	 *
	 * @param string $value Value of data to be validated
	 * @param int $equalValue Number to be compared
	 * @param string $msg Custom error message
	 * @return string
	 */
	public function testNotEqual($value, $equalValue, $msg=null){
		if( $value==$equalValue && strlen($value)==strlen($equalValue) ){
			if($msg!==null) return $msg;
			return 'Both values must be different.';
		}
	}

   /**
	* Validate if value Exists in database
	*
	* @param string $value Value of data to be validated
	* @param string $table Name of the table in DB
	* @param string $field Name of field you want to check
	* @return string
	*/
	public function testDbExist($value, $table, $field, $msg=null) {
		$result = Doo::db()->fetchRow("SELECT COUNT($field) AS count FROM " . $table . ' WHERE '.$field.' = ? LIMIT 1', array($value));
		if ((!isset($result['count'])) || ($result['count'] < 1)) {
			if($msg!==null) return $msg;
			return 'Value does not exist in database.';
		}
	}

   /**
	* Validate if value does Not Exist in database
	*
	* @param string $value Value of data to be validated
	* @param string $table Name of the table in DB
	* @param string $field Name of field you want to check
	* @return string
	*/
	public function testDbNotExist($value, $table, $field, $msg=null) {
		$result = Doo::db()->fetchRow("SELECT COUNT($field) AS count FROM " . $table . ' WHERE '.$field.' = ? LIMIT 1', array($value));
		if ((isset($result['count'])) && ($result['count'] > 0)) {
			if($msg!==null) return $msg;
			return 'Same value exists in database.';
		}
	}

	/**
	 * 验证是否数组
	 * General.20141119
	 * @param type $value
	 * @param type $msg
	 * @return type
	 */
	public function testArray($value, $msg=null){
		$ret = RetClass::CHECK_PARAM_INVALID;
		$retValue = $value;
		if(!is_array($retValue)){
			$errMsgDefault = CommonClass::txt('TIPS_VALIDATE_NOT_ARRAY',$value);//General.20150409 不是数组
		}else{
			$ret = RetClass::SUCCESS;
			$errMsgDefault = null;
			//$retValue = array_filter(array_unique($retValue));
		}
		return $this->getReturn($ret,$retValue,$errMsgDefault,$msg);
	}

	/**
	 * Validate if a value is in a list of values
	 *
	 * @param string $value Value of data to be validated
	 * @param int $equalValue List of values to be checked
	 * @param string $msg Custom error message
	 * @return string
	 */
	public function testInList($value, $valueList, $msg=null){
		if(!(in_array($value, $valueList))){
			if($msg!==null) return $msg;
			return 'Unmatched value.';
		}
	}

	/**
	 * Validate if a value is NOT in a list of values
	 *
	 * @param string $value Value of data to be validated
	 * @param int $equalValue List of values to be checked
	 * @param string $msg Custom error message
	 * @return string
	 */
	public function testNotInList($value, $valueList, $msg=null){
		if(in_array($value, $valueList)){
			if($msg!==null) return $msg;
			return 'Unmatched value.';
		}
	}

	/**
	* Validate field if it is equal with some other field from $_GET or $_POST method
	* This method is used for validating form
	*
	* @param string $value Value of data to be validated
	* @param string $method Method (get or post), default $_POST
	* @param string $field Name of field that you want to check
	* @return string
	*/

	public function testEqualAs($value, $method, $field, $msg=null) {
		if ($method == "get") {
		  $method = $_GET;
		} else if ($method == "post") {
		  $method = $_POST;
		} else {
		  $method = $_POST;
		}
		if (!isset($method[$field]) || $value != $method[$field]) {
			if($msg!==null) return $msg;
			return 'Value '.$value.' is not equal with "'.$field.'".';
		}
	}
	
	/**
	 * 验证选人控件（包括人员、部门、职位混合）
	 * General.20150504
	 * $value格式 [{'id':'999999001','pingyin':'renyuan1','name':'人员1','title':'人员1','type':'user'}, {'id':'999999002','pingyin':'renyuan2','name':'人员2','title':'人员2','type':'user'}]
	 * $type 人员=EnumClass::DATASOURCE_USERTYPE_USER, 部门=EnumClass::DATASOURCE_USERTYPE_DEPT, 职位=EnumClass::DATASOURCE_USERTYPE_POSN 
	 * $sum 人员个数
	 * @return json
	 */
	public function testUser($value = '', $prm_base=null, $type = 0, $sum = '', $msg=null){
		// $prm_base = self::$prm_base != null ? self::$prm_base : $prm_base;
		$retValue = '';
		$ret = RetClass::ERROR;
		if(!empty($value)){
            if (is_array($value)) {     //选人格式才进行转换  hky20171101
    			$result = ApiClass::datasource_user_select_to_field('3.0.0.0',self::$prm_base,$value, $type, $sum);
    			if($result['ret']== RetClass::SUCCESS){
    				$retValue = $result['data'];
    			}
            } else {
                $retValue = $value;
            }
		}
		return $this->testString($retValue, 0, 16770000, $msg);//General.20160504 增加长度
		//return $this->testText($retValue, $msg);
	}

	/**
	 * Validate getReturn
	 *
	 * @param enum $ret
	 * @param var $retValue
	 * @param var $errMsgDefault
	 * @param var $errMsg
	 * @return array
	 */
	public function getReturn($ret=RetClass::NONE,$retValue=null,$errMsgDefault='',$errMsg=null){
		//echo "<br>getReturn1:"; var_dump($retValue);
		switch ($ret) {
			case RetClass::SUCCESS:
			case RetClass::NONE:
				return array($ret, $retValue);
				break;
			default:
				if($errMsg==null){
					$errMsg = $errMsgDefault;
				}
				//General.20150409 必须使用语言标签找出相应的文案
				//$errMsg = CommonClass::txt($errMsg);
				if(strpos($errMsg,'_') !== false){
					$errMsg = CommonClass::txt($errMsg);
				}
				$errMsg = htmlspecialchars($errMsg);	//转义html特殊字符，该内容会直接返回到html上，转义防止XSS跨站脚本攻击 hky2017020
				return array($ret, $errMsg);
				break;
		}
	}
}
?>