<?php

class CommonClass {

	CONST DECODE_KEY = 'sdsd345kohnbdf345SDF890AABBCCDDE';
	CONST INTSWITCH_KEY = "IJKLMNOPQR0123456789ABCDEFGHSTUVWXYZabcdefghijklmnopqrstuvwxyz";//默认密钥
	public static $key1 = "IJKLMNOPQR0123456789ABCDEFGHSTUVWXYZabcdefghijklmnopqrstuvwxyz";//默认密钥

	/**
	 * 加密参数字符串
	 * General.20150105
	 * @param type $data
	 * @return type
	 */
	public static function encode($data = '', $type = EnumClass::PAGE_RET_MSG_TYPE_PAGE,$change='AES') {
		if(empty($data)){
			return $data;
		}
		$str = '';
		if(!empty($type)){
			switch($type){
				case EnumClass::PAGE_RET_MSG_TYPE_PAGE:
					//$str = json_encode($data);
					//$str = json_encode($data, JSON_UNESCAPED_UNICODE);
					//$str = json_encode($data);
					//var_dump($str);exit;
					if(is_array($data)){
						foreach($data as $k => $v2){
							$str .= $k .'=' . $v2 .'&';
						}
						$str = rtrim($str, '&');
					}else if(is_string($data)){
						$str = $data;
					}else if(is_object($data)){
						$str = self::json_encode($data);
					}
					break;
				case EnumClass::PAGE_RET_MSG_TYPE_STRING: //General.20150910 字符串类型
				case EnumClass::PAGE_RET_MSG_TYPE_MOBILE:
				case EnumClass::PAGE_RET_MSG_TYPE_NONE:
				case EnumClass::PAGE_RET_MSG_TYPE_HTML:
					$str = $data;
					if(!self::is_json($data)){ //json格式
						$str = self::json_encode($data);
					}
					break;
				case EnumClass::PAGE_RET_MSG_TYPE_CONSOLE: //General.20151013 控制台
					$str = $data;
					if(!self::is_json($data)){ //json格式
						$str = json_encode($data);
					}
					break;
				default:
					break;
			}
		}
		//var_dump($str);exit;
		//判断加密类型
		switch ($change) {
			case 'DES':
				$encrypt = new DES();
				break;
			case 'AES':
				$encrypt = new AES();
				break;
			default:
				$encrypt = new AES();
				break;
		}

		//return base64_encode($encrypt->encode($data));//加密参数字符串
		return $encrypt->encode($str);//加密参数字符串
	}

	/**
	 * 解密参数字符串
	 * General.20150105
	 * @param type $data
	 * @return type
	 */
	public static function decode($str = '', $type = EnumClass::PAGE_RET_MSG_TYPE_PAGE,$change='AES'){
		if(!empty($str) && is_string($str)) {
			if(!empty($type)){
				switch($type){
					case EnumClass::PAGE_RET_MSG_TYPE_STRING: //General.20150910 字符串类型
						break;
					case EnumClass::PAGE_RET_MSG_TYPE_PAGE:
						//$str = str_replace(' ', '+', ($str));
						// $str = str_replace('%2F', '/', urlencode($str));
						// $str = str_replace(' ', '+', urlencode($str));
						// $str = str_replace('%3D', '=', urlencode($str));
						// var_dump($str);
						//$data = json_decode($str, true);
						//$data = json_encode($str, JSON_UNESCAPED_UNICODE);
					case EnumClass::PAGE_RET_MSG_TYPE_MOBILE:
					case EnumClass::PAGE_RET_MSG_TYPE_CONSOLE: //General.20151013 控制台
					case EnumClass::PAGE_RET_MSG_TYPE_NONE:
					case EnumClass::PAGE_RET_MSG_TYPE_HTML:
						$str = str_replace(' ', '+', ($str));
						break;
					default:
						break;
				}
			}
			//var_dump($str);echo '<br>';
			LogClass::log_trace1('解密前接收到的字符串',$str,__FILE__,__LINE__);
			//$str = base64_decode(str_replace(" ", "+", $str));
			//判断加密类型
			switch ($change) {
				case 'DES':
				  $encrypt = new DES();
				  break;
				case 'AES':
				  $encrypt = new AES();
				  break;
				default:
				  $encrypt = new AES();
				  break;
			}
			//var_dump($str);
			$new_str = $str;
			$str = $encrypt->decode($str);//解密参数字符串
			// var_dump($str);exit;

			//临时处理字符串末是“+”号被转成空格的问题 hky.20151113
			if (!$str) {
				$str = $new_str . '+';
				$str = $encrypt->decode($str);//解密参数字符串
			}

			switch($type){
				case EnumClass::PAGE_RET_MSG_TYPE_STRING:
					break;
				default:
					if(self::is_json($str)){ //json格式
						LogClass::log_trace1('解密后处理接收到的字符串(json格式)',$str,__FILE__,__LINE__);
						$str = self::json_decode($str);
					}else if($type == EnumClass::PAGE_RET_MSG_TYPE_STRING){ //General.20150910 字符串类型
						LogClass::log_trace1('解密后处理接收到的字符串',$str,__FILE__,__LINE__);
						// $data = $str;
					}else{
						//General.20150910 将url中参数解成数组，格式如：
						//entid=95994514849661102&puserid=95994514849661101&password=e54c282f1911dc6e225cefbf33396c88&authcode=ldhbqocdop25lmk34afu4mrc46
						LogClass::log_trace1('解密后处理接收到的字符串(parse_str)',$str,__FILE__,__LINE__);
						//parse_str($str, $data); General.20160406 处理参数值中存在html标签问题
						$str = self::parse_str($str);
						LogClass::log_trace1('处理HTML后的字符串',$str,__FILE__,__LINE__);
					}
					return $str;
					break;
			}
		}
		return $str;
	}

	/**
	 * 字符串转换关联数组
	 * @author shawnWoo 2016/3/31
	 * @param string $str 需转换的字符串
	 * @return array 返回处理完的数据
	 */
	public static function parse_str($str){
		//检查参数
		if(empty($str)){
			return array();
		}
		if(is_string($str)){
			$strReplac=preg_replace('/&(=)+/','',$str); 	//正则匹配替换&=或&==等
			preg_match('/&[a-z]{4};/',$strReplac,$check);
			$isTrue=0;
			if(!empty($check)){
				$isTrue=1;
			}
			$strHtml=htmlspecialchars_decode($strReplac,ENT_QUOTES); 	//把预定义的 HTML 实体转换为字符。
			$strEntity=html_entity_decode($strHtml,ENT_QUOTES); 		//把 HTML 实体转换为字符：
			$arr=explode('&',$strEntity);	//将字符串转换为数组
			foreach($arr as $k=>$v){	//遍历删除空值
				if(empty($v)){
					unset($arr[$k]);
				}
			}
			$combinationArr=array_merge($arr); //重新组合数组下标
			foreach($combinationArr as $key=>$val){
				if(!strpos($val,'=')){ 	//查找 "=" 在字符串中第一次出现的位置
					$combinationArr[$key-1]=$combinationArr[$key-1].$val;	//拼接错开的值
					unset($combinationArr[$key]);	//删除重复的值
				}
			}
			$strChange=implode('&',$combinationArr);	//重新把数组转换为字符串
			$strReplacs=str_replace('<br/>','br/',$strChange); //字符串替换
			$arrRegroup=explode('&',$strReplacs); //将字符串转换为数组
			foreach($arrRegroup as $k=>$v){	//将字符串转换为数组
				$array[]=explode('=',$v,2);
			}
			foreach($array as $key=>$val){	//重新组合数组的键值
				$data[$val[0]]=$key;
				$data[$val[0]]=$val[1];
			}
			//parse_str($strRepOperator, $data);		//字符串转换关联数组
			foreach($data as $k=>$v){
				if($isTrue==1){
					//把预定义的字符转换为 HTML 实体，再把前面函数不能转换的字符也转换为 HTML 实体.
					$data[$k]=htmlentities(htmlspecialchars($v,ENT_QUOTES),ENT_QUOTES,'UTF-8',FALSE);
				}

			}
			foreach($data as $k=>$v){
				$data[$k]=urldecode(str_replace('br/','<br/>',$v)); //客户端提交的参数必须要urlencode一次，避免存在&符号当做参数处理 suson.20160528
			}
			return $data;
		}else{
			return array();
		}
	}

	/**
	 * 加密串
	 * General.20150423
	 * $params 要加密的数组
	 * $type 加密类型
	 */
	public static function encode_format($prm_base, $params = '', $type = EnumClass::PAGE_RET_MSG_TYPE_PAGE){
		if(empty($params)){
			return $params;
		}
		if(is_array($params)){
			$params['reqtime'] = time();
		}else if(is_string($params)){
			$params .= '&reqtime=' . time();
		}
		$reqdata = '?reqdata=' . self::encode($params, $type);
		return $reqdata;
	}

	/**
	 * 判断是否json
	 * General.20150424
	 * $json_str 字符串
	 */
	public static function is_json($json_str = ''){
		$json_str = str_replace('＼＼', '', $json_str);
		//整一串为json才判断成功，局部是json不当作json处理  modify by hky 20151228
		// $out_arr = array();
		// preg_match('/{.*}/', $json_str, $out_arr);
		// if (!empty($out_arr)){
		// 	$result = json_decode($out_arr[0], TRUE);
		// } else {
		// 	return FALSE;
		// }
		if (!empty($json_str) && is_string($json_str)){
			$result = json_decode($json_str, TRUE);
		} else {
			return FALSE;
		}
		return $result;
	}

	/**
	 * 获取语言包文案
	 * General.20150130
	 * @param type $str
	 * @param string $lang
	 * @return type
	 */
	public static function txt($str = '', $param = '', $lang = 'zh-cn') {
		if (empty($str)) {
			return $str;
		}
		$str = strtoupper((string) $str);
		if (empty($lang)) {
			$lang = 'zh-cn';
		}
		$com_file = '';
		$mod_file = '';
		if(isset(Doo::conf()->PROTECTED_FOLDER_ORI)){
			//公共语言包配置文件
			$com_file = Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER_ORI . 'lang/' . $lang . '.lang.ini';
		}
		if(isset(Doo::conf()->PROTECTED_FOLDER)){
			//模块所在目录下lang文件夹下语言包配置文件
			$mod_file = Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER . 'lang/' . $lang . '.lang.ini';
		}
		//$ini = file_get_contents($file);
		// 获取值
        //modified by james.ou 2017.10.25 增加缓存机制
        $com_values_str = self::get_data_by_file($com_file, '', 10*24*60);
        if(empty($com_values_str)){
            if(!empty($com_file) && file_exists($com_file)){
                self::clear_data_by_file($com_file,false);
               $com_values_str = self::get_data_by_file($com_file,'', 10*24*60);
            }
        }
        if(!empty($com_values_str) && is_string($com_values_str)){
            $com_values = parse_ini_string($com_values_str);
        }

        $mod_values_str = self::get_data_by_file($mod_file, '', 10*24*60);
        if(empty($mod_values_str)){
            if(!empty($mod_file) && file_exists($mod_file)){
                self::clear_data_by_file($mod_file,false);
                $mod_values_str = self::get_data_by_file($mod_file,'', 10*24*60);
            }
        }
        if(!empty($mod_values_str) && is_string($mod_values_str)){
            $mod_values = parse_ini_string($mod_values_str);
        }

        //modified by james.ou 2017.10.25
//        if(!empty($com_file) && file_exists($com_file)){
//                $com_values = parse_ini_file($com_file);
//        }
//		if(!empty($mod_file) && file_exists($mod_file)){
//			$mod_values = parse_ini_file($mod_file);
//		}
		if(!isset($com_values)){
			return $str;
		}else if(isset($com_values)){
			if(!isset($mod_values)){
				$values = $com_values;
			}else{
				$values = array_merge($com_values, $mod_values);
			}
		}

		// 获取键
//		$keys = array_keys($values);
		$ret = '';
		if (isset($values[$str])) {
			/*
			 * 1、若参数的个数少于文案中变量数，则多出的{s}替换为空
			 * 2、若参数的个数多于文案中变量数，则多出的1个参数拼接在文案最后，其余不显示
			 * 3、若存在参数，但文案中不存在插入参数符号{s}，则不会插入参数
			 */
			if (!empty($param) && strpos($values[$str], '{s}') !== false) {
				$array1 = explode('{s}', $values[$str]); //一个或多个{s}
				$array2 = explode('@@', $param); //多个参数以@@间隔
				/*
				 * 1、{s}和$param都一样多
				 * 2、{s}多于$param
				 * 3、{s}少于$param
				 */
				if(count($array1) > count($array2)){//{s}多于$param，要将$param补空值到跟{s}一样多
					for ($i = count($array2); $i <= count($array1); $i++) {
						$array2[] = '';
					}
				}else if(count($array1) < count($array2)){//{s}少于$param，要将{s}补空值到跟$param一样多
					for ($i = count($array1); $i <= count($array2); $i++) {
						$array1[] = '';
					}
				}
				foreach ($array1 as $k1 => $v1) {
					foreach ($array2 as $k2 => $v2) {
						if ($k1 == $k2) {
							$ret .= $v1 . $v2;
						}
					}
				}
			}else{
				//1、若$param为空，则有{s}都会替换为空
				//2、若不存在{s}，则$param不为空，{s}都会替换为空
				$ret = str_replace('{s}', '', $values[$str]);
			}
		}

		return $ret;
	}

	/**
	 * 获取终端类型
	 * General.20150416
	 * @param string $value 终端类型值
	 * @return type
	 */
	public static function get_terminal($value = '') {
		if(empty($value)){
			$value = EnumClass::TERMINAL_TYPE_WEB;
		}
		$txt = '';
		switch($value){
			case EnumClass::TERMINAL_TYPE_WEB:
				$txt = 'TERMINAL_TYPE_WEB';
				break;
			case EnumClass::TERMINAL_TYPE_MOBILE:
				$txt = 'TERMINAL_TYPE_MOBILE';
				break;
			case EnumClass::TERMINAL_TYPE_PAD:
				$txt = 'TERMINAL_TYPE_PAD';
				break;
			case EnumClass::TERMINAL_TYPE_MOBILE_ANDROID:
				$txt = 'TERMINAL_TYPE_MOBILE_ANDROID';
				break;
			case EnumClass::TERMINAL_TYPE_MOBILE_IPHONE:
				$txt = 'TERMINAL_TYPE_MOBILE_IPHONE';
				break;
			case EnumClass::TERMINAL_TYPE_MOBILE_WINDOWS:
				$txt = 'TERMINAL_TYPE_MOBILE_WINDOWS';
				break;
			case EnumClass::TERMINAL_TYPE_PAD_ANDROID:
				$txt = 'TERMINAL_TYPE_PAD_ANDROID';
				break;
			case EnumClass::TERMINAL_TYPE_PAD_IPAD:
				$txt = 'TERMINAL_TYPE_PAD_IPAD';
				break;
			case EnumClass::TERMINAL_TYPE_PAD_WINDOWS:
				$txt = 'TERMINAL_TYPE_PAD_WINDOWS';
				break;
			case EnumClass::TERMINAL_TYPE_TV_ANDROID:
				$txt = 'TERMINAL_TYPE_TV_ANDROID';
				break;
			case EnumClass::TERMINAL_TYPE_TV_IOS:
				$txt = 'TERMINAL_TYPE_TV_IOS';
				break;
			case EnumClass::TERMINAL_TYPE_IM:
				$txt = 'TERMINAL_TYPE_IM';
				break;
		}
		return self::txt('TXT_'.$txt);
	}

	/**
	 * 删除目录及文件
	 * General.20160223
	 * @param string $dir 目录路径
	 * @return type
	 */
	public static function deldir($dir) {
		$flag = 0;
		if(is_dir($dir)){
			if ( $handle = opendir("$dir")) {
				while (false !== ($item = readdir($handle))) {
					if ($item != "." && $item != "..") {
						if (is_dir( "$dir/$item" )) {
							self::deldir("$dir/$item");
						} else {
							if(unlink("$dir/$item")){
								//echo "已删除文件: $dir/$item<br /> ";
								$flag = 1;
							}
						}
					}
				}

				closedir($handle);
				if(rmdir($dir)){
					//echo "已删除目录: $dir<br /> ";
					$flag = 2;
				}
			}
		}
		if($flag > 0){
			return true;
		}else{
			return false;
		}
	}

	public static function encoding_u2utf82gb($c) {
		$str = "";
		if ($c < 0x80) {
			$str.=$c;
		} else if ($c < 0x800) {
			$str.=chr(0xC0 | $c >> 6);
			$str.=chr(0x80 | $c & 0x3F);
		} else if ($c < 0x10000) {
			$str.=chr(0xE0 | $c >> 12);
			$str.=chr(0x80 | $c >> 6 & 0x3F);
			$str.=chr(0x80 | $c & 0x3F);
		} else if ($c < 0x200000) {
			$str.=chr(0xF0 | $c >> 18);
			$str.=chr(0x80 | $c >> 12 & 0x3F);
			$str.=chr(0x80 | $c >> 6 & 0x3F);
			$str.=chr(0x80 | $c & 0x3F);
		}
		return iconv('UTF-8', 'GB2312', $str);
	}

	/**
	 *  检测编码
	 * @param string $str 传入字符串
	 * @return string 编码
	 */
	public static function encoding_detect($str) {
		if (preg_match("/^([" . chr(228) . "-" . chr(233) . "]{1}[" . chr(128) . "-" . chr(191) . "]{1}[" . chr(128) . "-" . chr(191) . "]{1})+$/", $str))
			return 'UTF-8'; //utf8编码
		if (preg_match("/^([" . chr(176) . "-" . chr(247) . "]{1}[" . chr(161) . "-" . chr(254) . "]{1}){1}+$/", $str))
			return 'GBK'; //GBK编码
		else {
			for ($i = 0; $i < strlen($str); $i++) {
				$v = ord($str[$i]);
				if ($v > 127) {
					if (($v >= 228) && ($v <= 233)) {
						if (($i + 2) >= (strlen($str) - 1))
							return 'GB2312';  // not enough characters
						$v1 = ord($str[$i + 1]);
						$v2 = ord($str[$i + 2]);
						if (!($v1 >= 128) && ($v1 <= 191) && ($v2 >= 128) && ($v2 <= 191))
							return 'GB2312';
					}
				}
			}
			return 'OTHER';
		}
	}

	public static function encoding_conv_all2utf($str) {
		switch (self::encoding_detect($str)) {
			case 'UTF-8':
				return $str;
				break;
			case 'GBK':
				return iconv('GBK', 'UTF-8', $str);
				break;
			case 'GB2312':
				return iconv('GB2312', 'UTF-8', $str);
				break;
			default:
				return iconv('GB2312', 'UTF-8', $str);
		}
	}

	/**
	 *
	 * @param string $pass
	 * @param string $salt
	 * @return string
	 */
	public static function encrypt_password($pass, $salt) {
		return md5(md5($pass) . $salt);
	}

	/**
	 * 生成加密后的Token
	 * @param mix $params  加密参数，数组或字符串，数组顺序和验证字段顺序相同
	 * @param string $securitykey 加密验证码
	 * @param string $ip 是否自动获取IP，否则传入IP
	 * @param bool $urlencode 是否需要urlencode
	 * @return string 返回加密后的Token
	 */
	public static function encryptToken($params, $securitykey, $ip = null, $urlencode = false) {
		if (is_array($params))
			$token_source = implode('', $params);
		else
			$token_source = $params;

		//echo($token_source);
		if ($ip === null)
			$token = strtoupper(md5(strtoupper(md5($token_source)) . $securitykey . self::get_ip()));
		else
			$token = strtoupper(md5(strtoupper(md5($token_source)) . $securitykey . $ip));

		if ($urlencode)
			return urlencode($token);
		else
			return $token;
	}

	/**
	 * 生成url，包括加密后的Token
	 * @param mix $params 加密参数，数组或字符串，数组顺序和验证字段顺序相同
	 * @param string $securitykey 加密验证码
	 * @param string $backurl 返回url
	 * @param string $token Token的key名称
	 * @param string $ip 是否自动获取IP，否则传入IP
	 * @param bool $urlencode 是否需要urlencode
	 * @return string 返回完整url，包括加密后的token字段
	 */
	public static function makeURLWithToken($params, $securitykey, $backurl = null, $token = 'token', $ip = null, $urlencode = false) {
		$and = '';
		$token_key = $token;
		$token_source = '';
		if ($backurl !== null) {
			if (strpos($backurl, '?') !== false)
				$and = '&';
			else
				$and = '?';
		}
		foreach ($params as $key => $value) {
			$backurl .= $and . $key . '=' . $value;
			$token_source .= $value;
			$and = '&';
		}

		if ($ip === null)
			$token = strtoupper(md5(strtoupper(md5($token_source)) . $securitykey . self::get_ip()));
		else
			$token = strtoupper(md5(strtoupper(md5($token_source)) . $securitykey . $ip));
		$backurl .= '&' . $token_key . '=' . $token;

		if ($urlencode)
			return urlencode($backurl);
		else
			return $backurl;
	}

	/**
	 * 验证Token
	 * @param mix $params 加密参数，数组或字符串，数组顺序和验证字段顺序相同
	 * @param string $securitykey 加密验证码
	 * @param string $token_value Token值
	 * @param array $params_key 如果$params为数组，此处填写需要验证的key数组或字符串列表，字符串用“,”隔开，按循序填写，null表示所有key均需要验证
	 * @param string $ip 是否自动获取IP，否则传入IP
	 * @param bool $urlencode 是否需要urlencode
	 * @return bool 返回是否验证通过
	 */
	public static function checkToken($params, $securitykey, $token_value, $params_key = null, $ip = null, $urlencode = false) {
		$token_source = '';

		if (is_array($params)) {
			if (!is_array($params_key))
				$params_key = explode(',', $params_key);

			foreach ($params_key as $key => $value)
				$token_source .= $params[$value];
		}else {
			$token_source = $params;
		}

		//echo($token_source);
		//echo('<br/>');
		//echo($securitykey);
		if ($ip === null)
			$token = strtoupper(md5(strtoupper(md5($token_source)) . $securitykey . self::get_ip()));
		else
			$token = strtoupper(md5(strtoupper(md5($token_source)) . $securitykey . $ip));

		if ($token === $token_value)
			return true;
		else
			return false;
	}

	public static function GetToken($params, $securitykey, $params_key) {
		$token_source = '';

		if (is_array($params)) {
			if (!is_array($params_key))
				$params_key = explode(',', $params_key);

			foreach ($params_key as $key => $value)
				$token_source .= $params[$value];
		}else {
			$token_source = $params;
		}
		$token = strtoupper(md5(strtoupper(md5($token_source)) . $securitykey . self::get_ip()));
		return $token;
	}

	/*
	 * @提交数据加密
	 * @参照ucenter数据提交加密方式
	 */

	public static function encode_requestdata($arg) {
		$arg["agent"] = md5($_SERVER['HTTP_USER_AGENT']);
		$arg["time"] = time();

		$s = $sep = '';
		foreach ($arg as $k => $v) {
			$k = urlencode($k);
			if (is_array($v)) {
				$s2 = $sep2 = '';
				foreach ($v as $k2 => $v2) {
					$k2 = urlencode($k2);
					$s2 .= "$sep2{$k}[$k2]=" . urlencode(stripslashes($v2));
					$sep2 = '&';
				}
				$s .= $sep . $s2;
			} else {
				$s .= "$sep$k=" . urlencode(stripslashes($v));
			}
			$sep = '&';
		}

		//Doo::loadClass("UserCommon");
		$s = urlencode(Userself::uc_authcode($s, 'ENCODE', OA_KEY));
		return $s;
	}

	/*
	 * @初始化请求数据
	 * @ 参照了ucenter加密方式
	 */

	public static function dencode_requestdata($data, $getagent = '') {
		//Doo::loadClass("UserCommon");
		$request = null;
		$time = time();
		if ($data) {
			$data = Userself::uc_authcode($data, 'DECODE', OA_KEY);
			parse_str($data, $request);
			$request = self::daddslashes($request, 1, TRUE);
			$agent = $getagent ? $getagent : $request['agent'];

			if (($getagent && $getagent != $request['agent']) || (!$getagent && md5($_SERVER['HTTP_USER_AGENT']) != $agent)) {
				exit('Access denied for agent changed');
			} elseif ($time - $request['time'] > 3600) {
				exit('Authorization has expired');
			}
		}
		if (empty($request)) {
			exit('Invalid request data');
		}

		return $request;
	}

	/*
	 * 该字符串为了数据库查询语句等的需要在某些字符前加上了反斜线。这些字符是单引号（'）、双引号（"）、反斜线（\）与 NUL（NULL 字符）。
	 */

	public static function daddslashes($string, $force = 0, $strip = FALSE) {
		if (!get_magic_quotes_gpc() || $force) {
			if (is_array($string)) {
				foreach ($string as $key => $val) {
					$string[$key] = self::daddslashes($val, $force, $strip);
				}
			} else {
				$string = addslashes($strip ? stripslashes($string) : $string);
			}
		}
		return $string;
	}

	/*
	 * 将用addslashes()函数处理后的字符串返回原样
	 */

	public static function sstripslashes($string) {
		if (is_array($string)) {
			foreach ($string as $key => $val) {
				$string[$key] = self::sstripslashes($val);
			}
		} else {
			$string = stripslashes($string);
		}
		return $string;
	}

	/*
	 * 转换特殊字符为HTML字符编码
	 * & (ampersand) becomes &amp; &amp;表示&(and)
	  " (double quote) becomes &quot;
	  &quot;表示双引号（"）
	  ' (single quote) becomes &#039;
	  &#039;表示单引号（'）
	  < (less than) becomes &lt;
	  &lt;表示小于号（<）
	  > (greater than) becomes &gt;
	  &gt;表示大于号（>）
	 */

	public static function shtmlspecialchars($string) {
		if (is_array($string)) {
			foreach ($string as $key => $val) {
				$string[$key] = self::shtmlspecialchars($val);
			}
		} else {
			$string = preg_replace('/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4})|[a-zA-Z][a-z0-9]{2,5});)/', '&\\1', str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $string));
		}
		return $string;
	}

	/**
	 * 获取在线IP
	 * @param int $format
	 * @return string
	 */
	public static function get_ip($format = 0) {
		//初始化变量
		$returnIp = '';

		//获取IP
		if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
			$onlineip = getenv('HTTP_CLIENT_IP');
		} elseif (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
			$onlineip = getenv('HTTP_X_FORWARDED_FOR');
		} elseif (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
			$onlineip = getenv('REMOTE_ADDR');
		} elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
			$onlineip = $_SERVER['REMOTE_ADDR'];
		}
		preg_match("/[\d\.]{7,15}/", $onlineip, $onlineipmatches);
		$returnIp = $onlineipmatches[0] ? $onlineipmatches[0] : 'unknown';

		//格式化IP %03d表示 空格用0代替最小三位整数转成十进位
		if ($format) {
			$ips = explode('.', $_SGLOBAL['onlineip']);
			for ($i = 0; $i < 3; $i++) {
				$ips[$i] = intval($ips[$i]);
			}
			return sprintf('%03d%03d%03d', $ips[0], $ips[1], $ips[2]);
		} else {
			return $returnIp;
		}
	}

	/**
	 * 转换为日期格式，如2010-01-10 23:23:02
	 * @param time $time 传入时间,null表示获取当前时间
	 * @param string $format 时间格式，默认为 Y-m-d H:i:s
	 * @return string
	 */
	public static function format_date($time = null, $format = 'Y-m-d H:i:s') {
		if ($time === null)
			$time = time();
		return date($format, $time);
	}

	/**
	 * Generates a random string.
	 * @param int $length Length of the generated string
	 * @return string
	 */
	public static function randomName($length = 6) {
		$allchar = 'abcdefghijklmnopqrstuvwxyz01234567890';
		$str = "";
		mt_srand((double) microtime() * 1000000);
		for ($i = 0; $i < $length; $i++)
			$str .= substr($allchar, mt_rand(0, 36), 1);
		//return date("YmdHis").rand(1000,9999).$str ;
		return $str;
	}

	/**
	 * 获得所有模块的名称以及链接地址
	 *
	 * @access      public
	 * @param       string      $directory      插件存放的目录
	 * @return      array
	 */
	public static function readModules($directory = '.') {
		//global $_LANG;

		$dir = @opendir($directory);
		$set_modules = true;
		$modules = array();

		while (false !== ($file = @readdir($dir))) {
			if (preg_match("/^.*?\.php$/", $file)) {
				include_once($directory . '/' . $file);
			}
		}
		@closedir($dir);
		unset($set_modules);

		foreach ($modules AS $key => $value) {
			ksort($modules[$key]);
		}
		ksort($modules);

		return $modules;
	}

	/**
	 * 重定向
	 * @param string $url 跳转URL
	 */
	public static function redirect2($url) {
		/* echo "<script language=\"javascript\">location.replace(\"$url\");</script>"; */
		header("location: $url");
		//echo ($url);
		exit;
	}

	/**
	 * 跳转到指定地址
	 * @param string $url url 相对于conf()->APP_URL路径，无/，如game/all/list
	 * @param string $type 类型，无表示普通跳转，internal 表示内部跳转
	 */
	public static function redirect($url, $type = 'internal') {
		echo '<script language="javascript" type="text/javascript">window.location.href="'.$url.'";</script>';
		/*if ($type === 'internal')
			return array('/' . $url, 'internal');
		else {
			if (strpos($url, 'http://') !== false)
				header("location: $url");
			else
				header('location: ' . Doo::conf()->APP_URL . $url);

			exit;
		}*/
	}

	/**
	 * 跳转到指定地址
	 * @param string $url url 相对于conf()->APP_URL路径，无/，如game/all/list
	 * @param string $subfolder 子项目目录，字符串后需带上'/'，如"admin/"，$typr=internal 有效
	 * @param string $type 类型，无表示普通跳转，internal 表示内部跳转
	 */
	public static function goURL($url, $subfolder = '', $type = 'internal') {
		if ($type === 'internal')
			return array('/' . $subfolder . $url, 'internal');
		else {
			if (strpos($url, 'http://') !== false)
				header("location: $url");
			else
				header('location: ' . Doo::conf()->APP_URL . $url);
		}
	}

	/**
	 * 显示提示信息
	 * @param string $message 内容
	 * @param string $type 类型，无表示普通，admin 表示管理站点提示
	 */
	public static function showMsg($message, $type = '') {
		return array('/showmsg/' . urlencode($message), 'internal');
	}

	/**
	 * 显示提示信息
	 * General.20150611
	 * @param string $return 返回格式
	 */
	public static function show_msg($return, $type = '') {
		if(empty($type)){
			$type = EnumClass::PAGE_RET_MSG_TYPE_NONE;
		}
		//var_dump($return);exit;
		return array('/tips/page/showmsg?reqdata=' . self::encode($return, $type));
	}

	/**
	 * 显示提示信息
	 * @param string $message 内容
	 */
	/*public static function show_msg($message, $url = '',$type = '') {
		if(!empty($url))$url=  urlencode(base64_encode($url));

		if (empty($url) && empty($type))
			return array('/showmsg/' . urlencode($message), 'internal');
		if (!empty($type)) {
			return array('/showmsg/' . urlencode($message) . '/' . $url . '/' . $type, 'internal');
		}
		return array('/showmsg/' . urlencode($message) . '/' . $url, 'internal');
	}*/

	/**
	* 写入session
	* General.20150325
	*/
	public static function session($namespace = '') {
		if(empty($namespace)){
			$namespace = Doo::conf()->SESSION_NAME;
		}
		//$namespace = "user";
		return Doo::session($namespace);
	}

	/**
	* 销毁session
	* General.20150325
	*/
	/*public static function session_unset($namespace = '') {
		if(empty($namespace)){
			$namespace = Doo::conf()->SESSION_NAME;
		}
		//$namespace = "user";
		return Doo::session($namespace)->namespaceUnset();
	}*/

	/**
	 * 获取或设置session
	 * @param string $namespace
	 * @return session 对象
	 */
	public static function session2($namespace = 'user') {
		if (isset(Doo::conf()->SESSION_TYPE) && Doo::conf()->SESSION_TYPE == 'cache') {
			Doo::loadClass('OASessionClass');
			return new OASessionClass($namespace);
		} else {
			return Doo::session($namespace);
		}
	}

	/**
	 * 缓存对象
	 * @param $cacheType (file,php,front,apc,xcache,eaccelerator,memcache) 等
	 * @return
	 */
	public static function cache($cacheType = 'file') {
		// $cacheType = isset(Doo::conf()->CACHE_TYPE) ? Doo::conf()->CACHE_TYPE : "file";
		return Doo::cache($cacheType);
	}

	/**
	 * 缓存操作(由方法决定选用哪种缓存技术)
	 * General.20151028
	 * @param $op 操作，包括set,get,del
	 * @param $key1 常量，如：ENTERPRISE=>企业信息缓存
	 * @param $key2 常量，如：MODULE=>模块信息缓存
	 * @param $key3 对应$key1的值，如：96145119354292138
	 * @param $key4 对应$key2的值，如：2001
	 * @param $value 需要缓存的值，只存有用的值或数组
	 * @param $expired 过期时间，默认10分钟
	 * @return
	 */
	public static function cache_key($prm_base, $optype = 0, $moduleid = 0,  $formid = 0, $setid = '', $entid = 0, $userid = 0){
		/**
		 * 1、写入企业所有模块信息
		 * 	a)$key1=EnumClass::CACHE_KEY_ENTERPRISE=ENTERPRISE
		 * 	b)$key2=EnumClass::CACHE_KEY_MODULE=MODULE
		 * 	c)$key3=entid=96145119354292138
		 * 	d)$key4=''
		 *  e)$expired=0
		 * 存储key：ENTERPRISE_96145119354292138_MODULE
		 * 存储value：
		 * Array
		 * 	(
		 * 		[2001] => Array
		 * 			(
		 * 				[mname] => 公告
		 * 				[msort] => 1
		 * 			)
		 *
		 * 		[2002] => Array
		 * 			(
		 * 				[mname] => 审批
		 * 				[msort] => 2
		 * 			)
		 * 	)
		 * 2、写入退出后返回地址
		 * 	a)$key1=EnumClass::CACHE_KEY_PERSONAL=PERSONAL
		 * 	b)$key2=EnumClass::CACHE_KEY_LOGINURL=LOGINURL
		 * 	c)$key3=sessionid=a34oas54t7fmrfq6p2kn5jv786
		 *  d)$key4=''
		 *  e)$expired=10
		 * 存储key：PERSONAL_BACKURL_a34oas54t7fmrfq6p2kn5jv786
		 * 存储value：http://test2.oa.cn:8409/web/task/page/index?path=96168075249844741%2C96168075249844743
		 */
		// $key = self::cache_key($key1, $key2, $key3, $key4);//组装缓存key
		if(empty($optype) || empty($entid)){
			LogClass::log_error1('参数不完整，cache_op::op='. $op,array('key1'=>$key1,'key2'=>$key2,'key3'=>$key3,'key4'=>$key4),__FILE__,__LINE__);
			return false;
		}
		$model->upuserid = isset($prm_base->userid) ? $prm_base->userid : 0;
		$key = $key1 . '_' . $key2 . '_' . $key3 . '_' . $key4;	//组装缓存key
		//LogClass::log_trace1('cache_op::op='. $op .',key='. $key .',value=',$value,__FILE__,__LINE__);
		LogClass::log_trace1('cache_op::op='. $op .',key',$key,__FILE__,__LINE__);
		return $key;
	}

	/**
	 * 缓存操作(由方法决定选用哪种缓存技术)
	 * General.20151028
	 * @param $op 操作，包括set,get,del
	 * @param $key1 常量，如：ENTERPRISE=>企业信息缓存
	 * @param $key2 常量，如：MODULE=>模块信息缓存
	 * @param $key3 对应$key1的值，如：96145119354292138
	 * @param $key4 对应$key2的值，如：2001
	 * @param $value 需要缓存的值，只存有用的值或数组
	 * @param $expired 过期时间，默认10分钟
	 * @return
	 */
	public static function cache_op($op = 'get', $key1 = '' ,$key2 = '' , $key3 = '', $key4 = '',$value = '', $expired = 10){
		/**
		 * 1、写入企业所有模块信息
		 * 	a)$key1=EnumClass::CACHE_KEY_ENTERPRISE=ENTERPRISE
		 * 	b)$key2=EnumClass::CACHE_KEY_MODULE=MODULE
		 * 	c)$key3=entid=96145119354292138
		 * 	d)$key4=''
		 *  e)$expired=0
		 * 存储key：ENTERPRISE_96145119354292138_MODULE
		 * 存储value：
		 * Array
		 * 	(
		 * 		[2001] => Array
		 * 			(
		 * 				[mname] => 公告
		 * 				[msort] => 1
		 * 			)
		 *
		 * 		[2002] => Array
		 * 			(
		 * 				[mname] => 审批
		 * 				[msort] => 2
		 * 			)
		 * 	)
		 * 2、写入退出后返回地址
		 * 	a)$key1=EnumClass::CACHE_KEY_PERSONAL=PERSONAL
		 * 	b)$key2=EnumClass::CACHE_KEY_LOGINURL=LOGINURL
		 * 	c)$key3=sessionid=a34oas54t7fmrfq6p2kn5jv786
		 *  d)$key4=''
		 *  e)$expired=10
		 * 存储key：PERSONAL_BACKURL_a34oas54t7fmrfq6p2kn5jv786
		 * 存储value：http://test2.oa.cn:8409/web/task/page/index?path=96168075249844741%2C96168075249844743
		 */
		// $key = self::cache_key($key1, $key2, $key3, $key4);//组装缓存key
		if(empty($key1) || empty($op)){
			LogClass::log_error1('参数不完整，cache_op::op='. $op,array('key1'=>$key1,'key2'=>$key2,'key3'=>$key3,'key4'=>$key4),__FILE__,__LINE__);
			return false;
		}
		$key = $key1 . '_' . $key2 . '_' . $key3 . '_' . $key4;	//组装缓存key
		//LogClass::log_trace1('cache_op::op='. $op .',key='. $key .',value=',$value,__FILE__,__LINE__);
		LogClass::log_trace1('cache_op::op='. $op .',key',$key,__FILE__,__LINE__);

		switch($op){
			case 'get'://获取
				return self::cache(Doo::conf()->CACHE_TYPE_SYS)->get($key);
				break;
			case 'set'://写入
				$time = 0;
				if(is_numeric($expired) && $expired > 0){
					$time = $expired * 60;
				}
				return self::cache(Doo::conf()->CACHE_TYPE_SYS)->set($key, $value, $time);
				break;
			case 'del'://删除
				return self::cache(Doo::conf()->CACHE_TYPE_SYS)->flush($key);
				break;
			case 'dellike'://模糊删除
				if (Doo::conf()->CACHE_TYPE_SYS == 'redis'){
					$key = rtrim($key,'_') . '*';
					return self::cache_redis()->delete(self::cache_redis()->keys($key));
				}else{
					return self::cache(Doo::conf()->CACHE_TYPE_SYS)->flushAll();
				}
				break;
			case 'delall'://删除所有
				return self::cache(Doo::conf()->CACHE_TYPE_SYS)->flushAll();
				break;
			default:
				break;
		}
		return false;
	}

	/**
	 * redis缓存对象
	 * @return
	 */
	public static function cache_redis() {
		// $cacheType = isset(Doo::conf()->CACHE_TYPE) ? Doo::conf()->CACHE_TYPE : "file";
		return Doo::cache('redis')->redis();
	}

	/**
	 * 缓存选用及操作
	 * General.20151030
	 * @param $key 缓存key
	 * @param $value 缓存value
	 * @param $expired 过期时间，默认10分钟
	 * @return string
	 */
	// public static function cache_route($op = 'get', $key = '', $value = '', $expired = 10){
	// 	if(empty($key) || empty($op)){
	// 		return false;
	// 	}
	// 	switch($op){
	// 		case 'get'://获取
	// 			return self::cache(Doo::conf()->CACHE_TYPE_SYS)->get($key);
	// 			break;
	// 		case 'set'://写入
	// 			$time = 0;
	// 			if(is_numeric($expired) && $expired > 0){
	// 				$time = $expired * 60;
	// 			}
	// 			return self::cache(Doo::conf()->CACHE_TYPE_SYS)->set($key, $value, $time);
	// 			break;
	// 		case 'del'://删除
	// 			return self::cache(Doo::conf()->CACHE_TYPE_SYS)->flush($key);
	// 			break;
	// 		case 'delall'://删除所有
	// 			return self::cache(Doo::conf()->CACHE_TYPE_SYS)->flushAll();
	// 			break;
	// 		default:
	// 			break;
	// 	}
	// 	return false;
	// }

	/**
	 * [str_rnTobr 转换回车换行为<br /> lyj 2016-07-14]
	 * @param  [type] $str [description]
	 * @return [type]      [description]
	 */
	public static function str_rnTobr($str) {
		return str_replace(array("\r\n", "\r", "\n"), "<br />", $str);
	}

	/**
	 * 转换换行为<br/>
	 * @param <string> $str
	 * @return <string>
	 */
	public static function str_rn2br($str) {
		return str_replace('\n', '<br />', $str);
	}

	/**
	 * 转换<br/>为换行
	 * @param <string> $str
	 * @return <string>
	 */
	public static function str_br2rn($str) {
		return str_replace('<br />', '\n', str_replace('<br/>', '\n', str_replace('<br>', '\n', $str)));
	}

	/**
	 * 显示xml信息
	 * @param  $arr
	 * @return <type>
	 */
	public static function showXml($arr) {
		$str = '<result>';
		foreach ($arr as $k => $v) {
			$str .='<record>';
			foreach ($v as $k2 => $v2) {
				$str .= "<$k2>$v2</$k2>";
			}
			$str .='</record>';
		}
		$str .= '</result>';
		echo($str);
		return '';
	}

	/**
	 * 自定义分页函数
	 * @param int $num 数据总条数
	 * @param int $perpage 每页显示条数
	 * @param int $curpage 当前页
	 * @param string $url 连接
	 * @param int $maxpages 最大的页数值
	 * @param int $page 要显示的页数个数
	 */
	public static function pageOutput($num, $perpage, $curpage, $url, $first = "&lt;", $last = "&gt;", $prev = "&lt;&lt;", $next = "&gt;&gt;", $maxpages = 0, $page = 10) {

		$multipage = '';
		$realpages = 1;

		if ($num > $perpage) {
			$offset = 2;

			$realpages = @ceil($num / $perpage);
			$pages = $maxpages && $maxpages < $realpages ? $maxpages : $realpages;

			if ($page > $pages) {
				$from = 1;
				$to = $pages;
			} else {
				$from = $curpage - $offset;
				$to = $from + $page - 1;
				if ($from < 1) {
					$to = $curpage + 1 - $from;
					$from = 1;
					if ($to - $from < $page) {
						$to = $page;
					}
				} elseif ($to > $pages) {
					$from = $pages - $page + 1;
					$to = $pages;
				}
			}

			$multipage .= $curpage - $offset > 1 && $pages > $page ? "<li><a href=\"$url/1\">$first</a></li>" : "";

			$multipage .= $curpage > 1 ? "<li><a href=\"$url/" . ($curpage - 1) . "\">$prev</a></li> " : "";

			for ($i = $from; $i <= $to; $i++) {
				$multipage .= $i == $curpage ? "<li class=\"now\"><a title=\"本页\" href=\"javascript:void(0);\">$i</a></li>" :
						"<li><a href=\"$url/$i\">$i</a></li>";
			}

			$multipage .= $to < $pages ? "<li><a href=\"$url/$pages\">$last</a></li>" : "";

			$multipage .= $curpage < $pages ? "<li><a href=\"$url/" . ($curpage + 1) . "\">$next</a></li> " : "";

			$multipage .= "<li class=\"afont\">共{$pages}页</li>";
		}

		return $multipage;
	}

	/**
	 * iApp显示Html方法 add by dxf 2013-09-03 11:57:40
	 * @param int $num 数据总条数
	 * @param int $perpage 每页显示条数
	 * @param int $curpage 当前页
	 * @param string $url 连接
	 * @param int $maxpages 最大的页数值
	 * @param int $page 要显示的页数个数
	 */
	public static function showHtml($arrTitle, $arrTitleEnd, $arrTitle2, $arrData) {

		$html = $arrTitle;

		foreach ($arrData as $key => $record) {
			$temp = $arrTitle2;
			foreach ($record as $fieldname => $field) {
				$temp = str_replace('{{' . $fieldname . '}}', $field, $temp);
				//echo("<br>{{".$fieldname."}}:$field---$arrTitle2:=>$temp<br>");
			}
			$html .= $temp;
		}
		$html = str_replace('{{appid}}', 1, $html);
		$html = str_replace('{{appformid}}', 2, $html);
		return $html . $arrTitleEnd;
	}

	/**
	 * 读文件内容
	 */
	public static function ReadFromFile($filename) {
		$content = @file_get_contents($filename);
		return $content;
	}

	/**
	 * 下载文件
	 */
	public static function downloadFile($content, $filename) {
		header("Cache-Control:public");
		header("Pragma:public");
		header("Content-type: application/octet-stream");
		header("Accept-Ranges: bytes");
		header("Content-Disposition:attachment; filename=" . $filename);
		header("Content-length:" . strlen($content));
		echo($content);
		exit;
	}

	/**
	 * 将json数据转换相对应的array数据
	 */
	public static function json_toarray($obj) {
		if ($obj == null)
			return array();

		$arr = array();
		foreach ($obj as $k => $w) {
			$arr[$k] = $w;
		}

		return $arr;
	}

	/**
	 * 自定义分页函数,异步
	 * @param string container div的id
	 * @param int $num 数据总条数
	 * @param int $perpage 每页显示条数
	 * @param int $curpage 当前页
	 * @param string $url 连接
	 * @param int $maxpages 最大的页数值
	 * @param int $page 要显示的页数个数
	 */
	public static function pageOutputAjax($container, $num, $perpage, $curpage, $url, $first = "&lt;&lt;", $last = "&gt;&gt;", $prev = "&lt;", $next = "&gt;", $maxpages = 0, $page = 10, $canback = false) {

		$multipage = '';
		$realpages = 1;

		if ($num > $perpage) {
			$offset = 2;

			$realpages = @ceil($num / $perpage);
			$pages = $maxpages && $maxpages < $realpages ? $maxpages : $realpages;

			if ($page > $pages) {
				$from = 1;
				$to = $pages;
			} else {
				$from = $curpage - $offset;
				$to = $from + $page - 1;
				if ($from < 1) {
					$to = $curpage + 1 - $from;
					$from = 1;
					if ($to - $from < $page) {
						$to = $page;
					}
				} elseif ($to > $pages) {
					$from = $pages - $page + 1;
					$to = $pages;
				}
			}
			$parm = '';
			$parms = explode('?', $url);
			if (count($parms) > 1) {
				$url = $parms[0];
				$parm = '?' . $parms[1];
			}

			//by james.ou 2011-9-20
			if (!$canback) {
				//不支持回退
				$multipage .= $curpage - $offset > 1 && $pages > $page ? "<li><a href=\"javascript:void(0);\" onclick=\"getPage('$container', '$url/1$parm');return false;\">$first</a></li>" : "";
				$multipage .= $curpage > 1 ? "<li><a href=\"javascript:void(0);\" onclick=\"getPage('$container', '$url/" . ($curpage - 1) . $parm . "');return false;\">$prev</a></li> " : "";
				for ($i = $from; $i <= $to; $i++) {
					$multipage .= $i == $curpage ? "<li class=\"now\"><a title=\"本页\" href=\"javascript:void(0);\">$i</a></li>" :
							"<li><a href=\"javascript:void(0);\" onclick=\"getPage('$container', '$url/$i$parm');return false;\">$i</a></li>";
				}
				//jeffli 2012-2-22 注释，移到下面604行
				//$multipage .= $to < $pages ? "<li><a href=\"javascript:void(0);\" onclick=\"getPage('$container', '$url/$pages$parm')\">$last</a></li>" : "";
				$multipage .= $curpage < $pages ? "<li><a href=\"javascript:void(0);\" onclick=\"getPage('$container', '$url/" . ($curpage + 1) . $parm . "');return false;\">$next</a></li> " : "";
				$multipage .= $to < $pages ? "<li><a href=\"javascript:void(0);\" onclick=\"getPage('$container', '$url/$pages$parm');return false;\">$last</a></li>" : "";
			} else {
				//支持回退url
				$fisturl = $url;
				if (empty($parm)) {
					$fisturl = $url . '?pageindex=1';
				} else {
					$fisturl = $url . $parm . '&pageindex=1';
				}
				$multipage .= $curpage - $offset > 1 && $pages > $page ? "<li><a href=\"javascript:void(0);\" onclick=\"getPage('$container', '$fisturl','" . $container . "_first');return false;\">$first</a></li>" : "";

				$prevurl = $url;
				if (empty($parm)) {
					$prevurl = $url . '?pageindex=' . ($curpage - 1);
				} else {
					$prevurl = $url . $parm . '&pageindex=' . ($curpage - 1);
				}
				$multipage .= $curpage > 1 ? "<li><a href=\"javascript:void(0);\" onclick=\"getPage('$container', '$prevurl','$container" . ($curpage - 1) . "');return false;\">$prev</a></li> " : "";
				for ($i = $from; $i <= $to; $i++) {

					$pageurl = $url;
					if (empty($parm)) {
						$pageurl = $url . '?pageindex=' . $i;
					} else {
						$pageurl = $url . $parm . '&pageindex=' . $i;
					}

					$multipage .= $i == $curpage ? "<li class=\"now\"><a title=\"本页\" href=\"javascript:void(0);\">$i</a></li>" :
							"<li><a href=\"javascript:void(0);\" onclick=\"getPage('$container', '$pageurl','$container$i');return false;\">$i</a></li>";
				}

				$lasturl = $url;
				if (empty($parm)) {
					$lasturl = $url . '?pageindex=' . $pages;
				} else {
					$lasturl = $url . $parm . '&pageindex=' . $pages;
				}
				//jeffli 2012-2-22注释，移到下面650行
				//$multipage .= $to < $pages ? "<li><a href=\"javascript:void(0);\" onclick=\"getPage('$container', '$lasturl','$container$pages')\">$last</a></li>" : "";

				$nexturl = $url;
				if (empty($parm)) {
					$nexturl = $url . '?pageindex=' . ($curpage + 1);
				} else {
					$nexturl = $url . $parm . '&pageindex=' . ($curpage + 1);
				}

				$multipage .= $curpage < $pages ? "<li><a href=\"javascript:void(0);\" onclick=\"getPage('$container', '$nexturl','$container" . ($curpage + 1) . "');return false;\">$next</a></li> " : "";
				$multipage .= $to < $pages ? "<li><a href=\"javascript:void(0);\" onclick=\"getPage('$container', '$lasturl','$container$pages');return false;\">$last</a></li>" : "";
			}
			$multipage .= "<li class=\"afont\">共{$pages}页</li>";
		}

		return $multipage;
	}

	/**
	 * 汉字转拼音
	 * 陆伟强 2015-10-15
	 * 非中文字符将自动忽略
	 * @param string $str 待转换的字符串
	 * @param string $charset 字符串编码
	 * @return array 返回拼音和首字母
	 例如：
		输入：
		  lhm liuhaomiao
		返回：
		  刘_제浩_淼のね
		  刘浩淼
	 */
	public static function pinyin($str,$charset="utf-8") {
		$data=array();
		$py='';
		$first='';
		$str = trim($str);
		if($charset=="utf-8"){
			$str=iconv("utf-8","gb2312//IGNORE",$str);
		}
		$slen = strlen($str);
		$pinyins=array();
		// if ($slen < 2) {
		// 	return $str;
		// }

		$fp = fopen(Doo::conf()->SITE_PATH.'t/res/default/js/pinyin.dat','r');
		while (!feof($fp)) {
			$line = trim(fgets($fp));
			$pinyins[$line[0] . $line[1]] = substr($line, 3, strlen($line) - 3);
		}
		fclose($fp);

		for ($i = 0; $i < $slen; $i++) {
			if (ord($str[$i]) > 0x80) {
				$c = $str[$i] . $str[$i + 1];
				$i++;
				if (isset($pinyins[$c])) {

						$py .= $pinyins[$c];
						$first .= $pinyins[$c][0];
				}
				else {
					$py .= '';
					$first .= '';
				}
			}
			else if (preg_match("/[a-z0-9]/i", $str[$i])) {
				$py .= $str[$i];
				$first .= $str[$i][0];
			}
			else {
				$py .= $str[$i];
				// $py .= '';
				$first .= '';
			}
		}
		//非中文字符按原输入入库  modify by hky 20151207
		/*$pin_str = '';
		for($i=0;$i<strlen($py);$i++){

			if(preg_match('/^[a-z]*$/', $py{$i})){

				$pin_str .= $py{$i};
			}
		}*/
		$first_str = '';
		for($i=0;$i<strlen($first);$i++){

			// if(preg_match('/^[a-z]*$/', $first{$i})){	//非中文字符按原输入入库  modify by hky 20160729

				$first_str .= $first{$i};
			// }
		}
		$data['py']=$py;
		// $data['py']=$pin_str;
		$data['first']=$first_str;
		return $data;

	}

	/**
	 * 将中文字符转为拼音
	 * */
	public static function pinyin2($_String, $_Code = 'utf-8') {

		$_DataKey = "a|ai|an|ang|ao|ba|bai|ban|bang|bao|bei|ben|beng|bi|bian|biao|bie|bin|bing|bo|bu|ca|cai|can|cang|cao|ce|ceng|cha" .
				"|chai|chan|chang|chao|che|chen|cheng|chi|chong|chou|chu|chuai|chuan|chuang|chui|chun|chuo|ci|cong|cou|cu|" .
				"cuan|cui|cun|cuo|da|dai|dan|dang|dao|de|deng|di|dian|diao|die|ding|diu|dong|dou|du|duan|dui|dun|duo|e|en|er" .
				"|fa|fan|fang|fei|fen|feng|fo|fou|fu|ga|gai|gan|gang|gao|ge|gei|gen|geng|gong|gou|gu|gua|guai|guan|guang|gui" .
				"|gun|guo|ha|hai|han|hang|hao|he|hei|hen|heng|hong|hou|hu|hua|huai|huan|huang|hui|hun|huo|ji|jia|jian|jiang" .
				"|jiao|jie|jin|jing|jiong|jiu|ju|juan|jue|jun|ka|kai|kan|kang|kao|ke|ken|keng|kong|kou|ku|kua|kuai|kuan|kuang" .
				"|kui|kun|kuo|la|lai|lan|lang|lao|le|lei|leng|li|lia|lian|liang|liao|lie|lin|ling|liu|long|lou|lu|lv|luan|lue" .
				"|lun|luo|ma|mai|man|mang|mao|me|mei|men|meng|mi|mian|miao|mie|min|ming|miu|mo|mou|mu|na|nai|nan|nang|nao|ne" .
				"|nei|nen|neng|ni|nian|niang|niao|nie|nin|ning|niu|nong|nu|nv|nuan|nue|nuo|o|ou|pa|pai|pan|pang|pao|pei|pen" .
				"|peng|pi|pian|piao|pie|pin|ping|po|pu|qi|qia|qian|qiang|qiao|qie|qin|qing|qiong|qiu|qu|quan|que|qun|ran|rang" .
				"|rao|re|ren|reng|ri|rong|rou|ru|ruan|rui|run|ruo|sa|sai|san|sang|sao|se|sen|seng|sha|shai|shan|shang|shao|" .
				"she|shen|sheng|shi|shou|shu|shua|shuai|shuan|shuang|shui|shun|shuo|si|song|sou|su|suan|sui|sun|suo|ta|tai|" .
				"tan|tang|tao|te|teng|ti|tian|tiao|tie|ting|tong|tou|tu|tuan|tui|tun|tuo|wa|wai|wan|wang|wei|wen|weng|wo|wu" .
				"|xi|xia|xian|xiang|xiao|xie|xin|xing|xiong|xiu|xu|xuan|xue|xun|ya|yan|yang|yao|ye|yi|yin|ying|yo|yong|you" .
				"|yu|yuan|yue|yun|za|zai|zan|zang|zao|ze|zei|zen|zeng|zha|zhai|zhan|zhang|zhao|zhe|zhen|zheng|zhi|zhong|" .
				"zhou|zhu|zhua|zhuai|zhuan|zhuang|zhui|zhun|zhuo|zi|zong|zou|zu|zuan|zui|zun|zuo";

		$_DataValue = "-20319|-20317|-20304|-20295|-20292|-20283|-20265|-20257|-20242|-20230|-20051|-20036|-20032|-20026|-20002|-19990" .
				"|-19986|-19982|-19976|-19805|-19784|-19775|-19774|-19763|-19756|-19751|-19746|-19741|-19739|-19728|-19725" .
				"|-19715|-19540|-19531|-19525|-19515|-19500|-19484|-19479|-19467|-19289|-19288|-19281|-19275|-19270|-19263" .
				"|-19261|-19249|-19243|-19242|-19238|-19235|-19227|-19224|-19218|-19212|-19038|-19023|-19018|-19006|-19003" .
				"|-18996|-18977|-18961|-18952|-18783|-18774|-18773|-18763|-18756|-18741|-18735|-18731|-18722|-18710|-18697" .
				"|-18696|-18526|-18518|-18501|-18490|-18478|-18463|-18448|-18447|-18446|-18239|-18237|-18231|-18220|-18211" .
				"|-18201|-18184|-18183|-18181|-18012|-17997|-17988|-17970|-17964|-17961|-17950|-17947|-17931|-17928|-17922" .
				"|-17759|-17752|-17733|-17730|-17721|-17703|-17701|-17697|-17692|-17683|-17676|-17496|-17487|-17482|-17468" .
				"|-17454|-17433|-17427|-17417|-17202|-17185|-16983|-16970|-16942|-16915|-16733|-16708|-16706|-16689|-16664" .
				"|-16657|-16647|-16474|-16470|-16465|-16459|-16452|-16448|-16433|-16429|-16427|-16423|-16419|-16412|-16407" .
				"|-16403|-16401|-16393|-16220|-16216|-16212|-16205|-16202|-16187|-16180|-16171|-16169|-16158|-16155|-15959" .
				"|-15958|-15944|-15933|-15920|-15915|-15903|-15889|-15878|-15707|-15701|-15681|-15667|-15661|-15659|-15652" .
				"|-15640|-15631|-15625|-15454|-15448|-15436|-15435|-15419|-15416|-15408|-15394|-15385|-15377|-15375|-15369" .
				"|-15363|-15362|-15183|-15180|-15165|-15158|-15153|-15150|-15149|-15144|-15143|-15141|-15140|-15139|-15128" .
				"|-15121|-15119|-15117|-15110|-15109|-14941|-14937|-14933|-14930|-14929|-14928|-14926|-14922|-14921|-14914" .
				"|-14908|-14902|-14894|-14889|-14882|-14873|-14871|-14857|-14678|-14674|-14670|-14668|-14663|-14654|-14645" .
				"|-14630|-14594|-14429|-14407|-14399|-14384|-14379|-14368|-14355|-14353|-14345|-14170|-14159|-14151|-14149" .
				"|-14145|-14140|-14137|-14135|-14125|-14123|-14122|-14112|-14109|-14099|-14097|-14094|-14092|-14090|-14087" .
				"|-14083|-13917|-13914|-13910|-13907|-13906|-13905|-13896|-13894|-13878|-13870|-13859|-13847|-13831|-13658" .
				"|-13611|-13601|-13406|-13404|-13400|-13398|-13395|-13391|-13387|-13383|-13367|-13359|-13356|-13343|-13340" .
				"|-13329|-13326|-13318|-13147|-13138|-13120|-13107|-13096|-13095|-13091|-13076|-13068|-13063|-13060|-12888" .
				"|-12875|-12871|-12860|-12858|-12852|-12849|-12838|-12831|-12829|-12812|-12802|-12607|-12597|-12594|-12585" .
				"|-12556|-12359|-12346|-12320|-12300|-12120|-12099|-12089|-12074|-12067|-12058|-12039|-11867|-11861|-11847" .
				"|-11831|-11798|-11781|-11604|-11589|-11536|-11358|-11340|-11339|-11324|-11303|-11097|-11077|-11067|-11055" .
				"|-11052|-11045|-11041|-11038|-11024|-11020|-11019|-11018|-11014|-10838|-10832|-10815|-10800|-10790|-10780" .
				"|-10764|-10587|-10544|-10533|-10519|-10331|-10329|-10328|-10322|-10315|-10309|-10307|-10296|-10281|-10274" .
				"|-10270|-10262|-10260|-10256|-10254";

		$_TDataKey = explode('|', $_DataKey);

		$_TDataValue = explode('|', $_DataValue);

		$_Data = array_combine($_TDataKey, $_TDataValue);

		arsort($_Data);

		reset($_Data);

		if ($_Code != 'gb2312')
			$_String = self::u2_utf8_gb($_String);

		$_Res = '';

		for ($i = 0; $i < strlen($_String); $i++) {

			$_P = ord(substr($_String, $i, 1));

			if ($_P > 160) {

				$_Q = ord(substr($_String, ++$i, 1));
				$_P = $_P * 256 + $_Q - 65536;
			}

			$_Res .= self::pin_yin($_P, $_Data);
		}

		return preg_replace("/[^a-z0-9]*/", '', $_Res);
	}

	private static function pin_yin($_Num, $_Data) {

		if ($_Num > 0 && $_Num < 160) {

			return chr($_Num);
		} elseif ($_Num < -20319 || $_Num > -10247) {

			return '';
		} else {

			foreach ($_Data as $k => $v) {
				if ($v <= $_Num)
					break;
			}

			return $k;
		}
	}

	private static function u2_utf8_gb($_C) {

		$_String = '';

		if ($_C < 0x80) {

			$_String .= $_C;
		} elseif ($_C < 0x800) {

			$_String .= chr(0xC0 | $_C >> 6);

			$_String .= chr(0x80 | $_C & 0x3F);
		} elseif ($_C < 0x10000) {

			$_String .= chr(0xE0 | $_C >> 12);

			$_String .= chr(0x80 | $_C >> 6 & 0x3F);

			$_String .= chr(0x80 | $_C & 0x3F);
		} elseif ($_C < 0x200000) {

			$_String .= chr(0xF0 | $_C >> 18);

			$_String .= chr(0x80 | $_C >> 12 & 0x3F);

			$_String .= chr(0x80 | $_C >> 6 & 0x3F);

			$_String .= chr(0x80 | $_C & 0x3F);
		}

		return self::charset_encode($_String, 'gbk');
	}

	//实现多种字符编码方式
	public static function charset_encode($input, $_output_charset, $_input_charset = "utf-8") {
		$output = "";
		if (!isset($_output_charset))
			$_output_charset = $GLOBALS['charset'];
		if ($_input_charset == $_output_charset || $input == null) {
			$output = $input;
		} elseif (function_exists("mb_convert_encoding")) {
			$output = mb_convert_encoding($input, $_output_charset, $_input_charset);
		} elseif (function_exists("iconv")) {
			$output = iconv($_input_charset, $_output_charset, $input);
		}
		else
			die("sorry, you have no libs support for charset change.");
		return $output;
	}

	//实现多种字符解码方式
	public static function charset_decode($input, $_input_charset, $_output_charset = "utf-8") {
		$output = "";
		if (!isset($_input_charset))
			$_input_charset = $GLOBALS['charset'];
		if ($_input_charset == $_output_charset || $input == null) {
			$output = $input;
		} elseif (function_exists("mb_convert_encoding")) {
			$output = mb_convert_encoding($input, $_output_charset, $_input_charset);
		} elseif (function_exists("iconv")) {
			$output = iconv($_input_charset, $_output_charset, $input);
		}
		else
			die("sorry, you have no libs support for charset changes.");
		return $output;
	}

	/*
	 * 去除字符串首尾的连续的$sub
	 */

	public static function trim_str($str, $sub = ' ') {
		$b = TRUE;
		while ($b) {
			if (substr($str, 0, strlen($sub)) == $sub)
				$str = substr($str, strlen($sub));
			else
				$b = FALSE;
		}
		$b = TRUE;
		while ($b) {
			if (substr($str, strlen($str) - strlen($sub)) == $sub)
				$str = substr($str, 0, strlen($str) - strlen($sub));
			else
				$b = FALSE;
		}
		return $str;
	}

	/*
	 * @时间比较函数，返回两个日期相差几秒、几分钟、几小时或几天
	 * @param date2：默认当前时间
	 * @param unit：s（秒） i(分) h(小时) d（天）
	 */

	public static function date_contrast($date1, $date2 = '', $unit = '') {
		switch ($unit) {
			case 's':
				$dividend = 1;
				break;
			case 'i':
				$dividend = 60;
				break;
			case 'h':
				$dividend = 3600;
				break;
			case 'd':
				$dividend = 86400;
				break;
			default:
				$dividend = 86400;
		}

		//date2默认为当前时间
		if ($date2 == '') {
			$date2 = date('Y-m-d H:i:s');
		}

		$time1 = strtotime($date1);
		$time2 = strtotime($date2);
		if ($time1 && $time2)
			return (float) ($time1 - $time2) / $dividend;
		return false;
	}

	/**
	 * 截取字符串
	 * @param string $string 要截取的字符串
	 * @param int $length 截取的长度
	 * @param string $dot 截取后显示的字符
	 */
	public static function cutstr($string, $length, $dot = '...') {

		if (strlen($string) <= $length) {
			return $string;
		}

		$string = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array('&', '"', '<', '>'), $string);

		$strcut = '';
		$n = $tn = $noc = 0;
		while ($n < strlen($string)) {

			$t = ord($string[$n]);
			if ($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
				$tn = 1;
				$n++;
				$noc++;
			} elseif (194 <= $t && $t <= 223) {
				$tn = 2;
				$n += 2;
				$noc += 2;
			} elseif (224 <= $t && $t <= 239) {
				$tn = 3;
				$n += 3;
				$noc += 2;
			} elseif (240 <= $t && $t <= 247) {
				$tn = 4;
				$n += 4;
				$noc += 2;
			} elseif (248 <= $t && $t <= 251) {
				$tn = 5;
				$n += 5;
				$noc += 2;
			} elseif ($t == 252 || $t == 253) {
				$tn = 6;
				$n += 6;
				$noc += 2;
			} else {
				$n++;
			}

			if ($noc >= $length) {
				break;
			}
		}
		if ($noc > $length) {
			$n -= $tn;
		}

		$strcut = substr($string, 0, $n);

		$strcut = str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $strcut);

		return $strcut . $dot;
	}

	/**
	 * 检查$value是否在以$split分割的$string中(aa在aa,bb中)
	 * @param  $value
	 * @param  $split
	 * @param  $string
	 * @return <type>
	 */
	public static function is_in_splitstring($value, $split, $string) {
		$list = explode($split, $string);
		return in_array($value, $list);
		/* by jame 2011-7-1 comment
		  foreach ($list as $s) {
		  if($s == $value)
		  return true;
		  }
		  return false; */
	}

	/**
	 * 数组1中是否存在数组2的值
	 * @param <array> $list1
	 * @param <array> $list2
	 * @return <bool>
	 */
	public static function array_one_is_in($list1, $list2) {
		foreach ($list1 as $v1) {
			foreach ($list2 as $v2) {
				if ($v1 == $v2) {
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * 以$split分割的字符串$string1是否存在$string2的值
	 * @param  $string1
	 * @param  $string2
	 * @param  $split
	 * @return <type>
	 */
	public static function is_splitstring_in_string($string1, $string2, $split) {
		$list1 = explode($split, $string1);
		$list2 = explode($split, $string2);

		return self::array_one_is_in($list1, $list2);
	}

	/**
	 * 获取用户头像缩略图
	 * @param  $url
	 * @param  $flag
	 * @param  $gender
	 * @return <type>
	 */
	public static function getImageThumb($userno, $flag, $url = '') {
		//$imgurl = Doo::conf()->DOCS_URL . 'uploads/userfiles/logo/' . $userno . '/thumb' . $flag . '_' . $userno . '.jpg';
		//General.20131227 增加头像指定路径url和随机数rand
		$url = empty($url) ? Doo::conf()->DOCS_URL : $url;
		$imgurl = $url . 'uploads/userfiles/logo/' . $userno . '/thumb' . $flag . '_' . $userno . '.jpg?rand=' . time();
		//General.20131227 end
		return $imgurl;
	}

	/**
	 * 删除$split分割的字符串$string中的字符串$value
	 * @param  $value
	 * @param  $string
	 * @param  $split
	 * @return <type>
	 */
	public static function del_splitstring($value, $string, $split = ',') {
		$values = explode($split, $string);
		$newstring = '';
		foreach ($values as $v) {
			if ($v != $value)
				$newstring .= $split . $v;
		}
		return self::trim_str($newstring, $split);
	}

	/**
	 * 日期格式
	 * @param  $date
	 * @param  $format
	 * @return <type>
	 */
	public static function dateFormat($date, $format = 'Y-m-d H:i:s') {
		if (is_numeric($date)) {
			return date($format, $date);
		} else {
			return date($format, strtotime($date));
		}
	}

	/**
	 * 查找字符串是否在另一个字符串中
	 * @param  $search要查找的字符串
	 * @param  $subject目标字符串
	 * @return <type>找到返回TRUE 否则返回FALSE
	 */
	public static function is_exit_subject($search, $subject) {
		$sub_array = explode(",", $subject);
		if (in_array($search, $sub_array)) {
			return true;
		}
		else
			return false;
	}

	/**
	 *
	 * @param  $search要查找删除的字符串(aa)
	 * @param  $subject目标值(aa,ee,bb,cc)
	 * @param  $type 返回类型 1为字符链(以逗号分开)ee,bb,cc
	 */
	public static function unset_value($search, $subject) {
		if ($subject == "") {
			return "";
		} else {
			$array = explode(",", $subject);
			if ($search == "") {
				return implode(",", $array);
			} else {
				foreach ($array as $k => $value) {
					if ($search == $value) {
						unset($array[$k]);
					}
				}
				return implode(",", $array);
			}
		}
	}

	/*
	 * 查看该记录的最近几条记录
	 * 数组方式 $now = array('title'=>'','url'=>'','isread'=>''); 如果有已读未读，已读值为true,未读值为false
	 */

	public static function view_recent($now, $up, $down, $toptitle = '浏览附近', $isDisplayReadStatus = true, $width = '330px') {
		$upstr = '';
		$downstr = '';
		$starthtml = '<div class="list_a"><div class="clear"></div><dl class="fleft  tleft pdbtm10" style="width:' . $width . '"><dt class="fb  h30 w100p">' . $toptitle . '：</dt><dt class="fb h10 overflow w100p" style="background-color:#f2f7fb">&nbsp;</dt>';
		$endhtml = '</dl></div>';
		$readhtml = '<em class="h25 fleft ann w20 mleft10" style=" background-position:right -485px;"></em>';
		$unreadhtml = '<em class="h25 fleft ann_new w20 mleft10" style=" background-position:right -445px;"></em>';
		$itemhtml = '<dd class="h25 lh25 w100p" style="background-color:#f2f7fb ;">{readstatushtml}<span class="fleft mleft5 "><a href="{url}" class="gray6h" title="{title}">{title}</a></span></dd>';
		if ($isDisplayReadStatus)
			$read_status_html = $readhtml;
		else
			$read_status_html = '';

		$item_now_html = '<dd class="h25 lh25 w100p"  style="background-color:#f2f7fb ;">' . $read_status_html . '<span class="fleft mleft5 "><span class="gray6 fb" alt="' . $now['title'] . '">' . self::csubstr($now['title'], 0, 20) . '</span></span></dd>';
		//var_dump($down);echo '<br/>';var_dump($up);exit;
		if ($up != '') {
			foreach ($up as $v) {
				foreach ($v as $key => $value) {
					if ($key == 'isread' && $value == true && $isDisplayReadStatus) {
						$searchs_up[] = '{readstatushtml}';
						$replaces_up[] = $readhtml;
					} else if ($key == 'isread' && $value == false && $isDisplayReadStatus) {
						$searchs_up[] = '{readstatushtml}';
						$replaces_up[] = $unreadhtml;
					} else if (!$isDisplayReadStatus && $key == 'isread') {
						$searchs_up[] = '{readstatushtml}';
						$replaces_up[] = '';
					} else {
						$searchs_up[] = '{' . $key . '}';
						if ($key == 'title')
							$replaces_up[] = self::csubstr($value, 0, 20);
						else
							$replaces_up[] = $value;
					}
				}
				$new_up_item_html = str_replace($searchs_up, $replaces_up, $itemhtml);
				$upstr .= $new_up_item_html;
				$searchs_up = null;
				$replaces_up = null;
			}
		}
		else
			$upstr = '';

		if ($down != '') {
			foreach ($down as $key_top => $v) {
				foreach ($v as $key => $value) {
					if ($key == 'isread' && $value == true && $isDisplayReadStatus) {
						$searchs_down[] = '{readstatushtml}';
						$replaces_down[] = $readhtml;
					} else if ($key == 'isread' && $value == false && $isDisplayReadStatus) {
						$searchs_down[] = '{readstatushtml}';
						$replaces_down[] = $unreadhtml;
					} else if (!$isDisplayReadStatus && $key == 'isread') {
						$searchs_down[] = '{readstatushtml}';
						$replaces_down[] = '';
					} else {
						$searchs_down[] = '{' . $key . '}';
						if ($key == 'title')
							$replaces_down[] = self::csubstr($value, 0, 20);
						else
							$replaces_down[] = $value;
					}
				}
				$new_down_item_html = str_replace($searchs_down, $replaces_down, $itemhtml);
				$downstr .= $new_down_item_html;
				$searchs_down = null;
				$replaces_down = null;
			}
		}
		else
			$downstr = '';

		$res_html = $starthtml . $upstr . $item_now_html . $downstr . $endhtml;
		return $res_html;
	}

	/**
	 * 截取字符串长度(支持中文)
	 *
	 */
	public static function csubstr($string, $start, $sublength, $encoding = 'utf-8') {
		$len = mb_strlen($string, $encoding);
		if ($len > $sublength) {
			$str = mb_substr($string, $start, $sublength, $encoding);
			return $str . '...';
		}
		else
			return $string;

		/* $string_s = substr($string, 0, $start);
		  $parity_s = 0;
		  for ($i = 0; $i < $start; $i++) {
		  $temp_str = substr($string_s, $i, 1);
		  if (Ord($temp_str) > 127)
		  $parity_s+=1;
		  }
		  if ($parity_s % 2 == 1) {
		  $start += 1;
		  }

		  $len = strlen($string);
		  if ($len <= $sublength) {
		  $string = substr($string, $start, $sublength);
		  } else {
		  $string = substr($string, $start, $sublength);
		  $parity = 0;
		  for ($j = 0; $j < $sublength; $j++) {
		  $temp_str = substr($string, $j, 1);
		  if (Ord($temp_str) > 127)
		  $parity+=1;
		  }
		  if ($parity % 2 == 1) {
		  $string = substr($string, 0, ($sublength - 1)) . "...";
		  } else {
		  $string = substr($string, 0, $sublength) . "...";
		  }
		  }
		  return $string; */
	}

	/**
	 * 将html内容转换为image图片
	 * @param $htmlcontent
	 * @param $toimagepath
	 * @author james.ou 2011-11-1
	 */
	public static function html2image($htmlcontent, $toimagepath, $toimagewidth = '400', $toimageheight = '300', $toimagetype = 'png') {
		$str = $htmlcontent;
		$str = strtolower($str);
		//$str = mb_convert_encoding($str, "html-entities", "utf-8");
		//Get the original HTML string
		//Declare <h1> and </h1> arrays
		$h1_start = array();
		$h1_end = array();
		//Clear <h1> and </h1> attributes
		$str = preg_replace("/<h1[^>]*>/", "<h1>", $str);
		$str = preg_replace("/<\/h1[^>]*>/", "</h1>", $str);
		$str = preg_replace("/<h1>\s*<\/h1>/", "", $str);

		//Declare <img> arrays
		$img_pos = array();
		$imgs = array();
		//If we have images in the HTML
		if (preg_match_all("/<img[^>]*src=\"([^\"]*)\"[^>]*>/", $str, $m)) {
			//Delete the <img> tag from the text
			//since this is not plain text
			//and save the position of the image
			$nstr = $str;
			$nstr = str_replace("\r\n", "", $nstr);
			$nstr = str_replace("<h1>", "", $nstr);
			$nstr = str_replace("</h1>", "", $nstr);
			$nstr = preg_replace("/<br[^>]*>/", str_repeat(chr(1), 2), $nstr);
			$nstr = preg_replace("/<div[^>]*>/", str_repeat(chr(1), 2), $nstr);
			$nstr = preg_replace("/<\/div[^>]*>/", str_repeat(chr(1), 2), $nstr);
			$nstr = preg_replace("/<p[^>]*>/", str_repeat(chr(1), 4), $nstr);
			$nstr = preg_replace("/<\/p[^>]*>/", str_repeat(chr(1), 4), $nstr);
			$nstr = preg_replace("/<hr[^>]*>/", str_repeat(chr(1), 8), $nstr);

			foreach ($m[0] as $i => $full) {
				$img_pos[] = strpos($nstr, $full);
				$str = str_replace($full, chr(1), $str);
			}
			//Save the sources of the images
			foreach ($m[1] as $i => $src) {
				$imgs[] = $src;
			}
			//Get image resource of the source
			//according to its extension and save it in array
			foreach ($imgs as $i => $image) {
				$ext = end(explode(".", $image));
				$im = null;
				switch ($ext) {
					case "gif":
						$im = imagecreatefromgif($image);
						break;
					case "png":
						$im = imagecreatefrompng($image);
						break;
					case "jpeg":
						$im = imagecreatefromjpeg($image);
						break;
				}
				$imgs[$i] = $im;
			}
		}
		//If there is <h1> or </h1>s
		while (strpos($str, "<h1>") != false || strpos($str, "</h1>") != false) {
			while (strpos($str, "<h1>") !== false) {
				$p = strpos($str, "<h1>");
				$h1_start[] = $p;
				$str = substr($str, 0, $p) . substr($str, $p + strlen("<h1>"));
			}
			while (strpos($str, "</h1>") !== false) {
				$p = strpos($str, "</h1>");
				$h1_end[] = $p;
				$str = substr($str, 0, $p) . substr($str, $p + strlen("</h1>"));
			}
		}
		//去除一些库不支持的html标签
		/*
		 * painty库只支持HTML代码
		 * H1
		 * STRONG, B
		 * IMG tags
		 * HR
		 * BR, P
		 */
		$str = preg_replace("/\<(?!img|br|p|br|div|hr|strong|h1).(.*?)\>/is", "", $str);
		/*
		 * 替换html空格
		 */
		$str = str_replace("&nbsp;", " ", $str);
		//Remove plain line breaks
		$str = str_replace("\r\n", "", $str);
		//Replace <br>s to line breaks
		$str = preg_replace("/<br[^>]*>/", "\r\n", $str);
		//Take care of <div>s
		$str = preg_replace("/<div[^>]*>/", "\r\n", $str);
		$str = preg_replace("/<\/div[^>]*>/", "\r\n", $str);
		//Take care of <p>s
		$str = preg_replace("/<p[^>]*>/", "  \r\n", $str);
		$str = preg_replace("/<\/p[^>]*>/", "\r\n ", $str);
		//Take care of <hr>s
		$str = preg_replace("/<hr[^>]*>/", "\r\n<hr> \r\n", $str);
		//print_r($str);exit;
		$i = 0;
		$h = 0;
		$width = $toimagewidth;
		$height = $toimageheight;
		$rows = explode("\r\n", $str);
		$im = imagecreatetruecolor($width, $height);
		$black = imagecolorallocate($im, 0, 0, 0);
		$white = imagecolorallocate($im, 255, 255, 255);
		imagefill($im, 0, 0, $white);
		//Location vars
		$x = 0;
		$y = 18;
		//<b>,<strong> vars
		$bold = false;
		$bolds = 0;
		$strong = false;
		$strongs = 0;
		//<h1> vars
		$h = false;
		$hs = 0;
		//Size
		$size = 11;
		//Add vertical offset after row with image
		$add_at_end = 0;
		//Runs over all the characters in the string
		if ($str == "")
			$str .= chr(1);
		//echo $str;

		$prev_bold = false;
		$prev_strong = false;
		$prev_bold = false;
		$prev_strong = false;
		$len = mb_strlen($str, "utf8");

		for ($i = 0; $i < $len; $i++) {
			$jump = false;
			$add_to_i = 0;
			// $tmpchar = mb_substr($str,$i,1,"utf8");
			//Saves the current character
			$char = mb_substr($str, $i, 1, "utf8");
			//If this is line break
			if (mb_substr($str, $i, 2, "utf8") == "\r\n") {
				//Vertical offset is font size plus some padding and
				//the height of the image (if there was one or more in the past row)
				$y += 12 + 4 + $add_at_end;
				//Nothing to add to the next row
				$add_at_end = 0;
				//Horizonal offset in 0
				$x = 0;
				//Ignore \n
				$add_to_i += 1;
				//Dont print \r
				$jump = true;
				//If this is <b>
			} else if (mb_substr($str, $i, 4, "utf8") == "<hr>") {
				imageline($im, $x, $y, $width, $y, $black);
				$add_to_i += 3;
				$jump = true;
			} else if (mb_substr($str, $i, 3, "utf8") == "<b>") {
				//<b> and <strong> counter incremented
				$bolds++;
				//If it more than 0
				if ($bolds > 0) {
					//We are in bold text
					$bold = true;
				}
				//Ignore b>
				$add_to_i += 2;
				//Dont print <
				$jump = true;
				//If this is </b>
			} else if (mb_substr($str, $i, 4, "utf8") == "</b>") {
				//<b> and <strong> counter decremented
				$bolds--;
				//If the counter is 0
				if ($bolds == 0) {
					//We are not in bold text
					$bold = false;
				}
				//If there is open tag to close
				if ($bolds >= 0) {
					//Ignore /b>
					$add_to_i += 3;
					//Dont print <
					$jump = true;
				}
				//<strong> handling similar to <b> handling
			} else if (substr($str, $i, 8) == "<strong>") {
				$strongs++;
				if ($strongs > 0) {
					$strong = true;
				}
				$add_to_i += 7;
				$jump = true;
			} else if (mb_substr($str, $i, 9, "utf8") == "</strong>") {
				$strongs--;
				if ($strongs == 0) {
					$strong = false;
				}
				if ($strongs >= 0) {
					$add_to_i += 8;
					$jump = true;
				}
			}
			//We started <h1>
			if (in_array($i, $h1_start)) {
				//Increment <h1>s counter
				$hs++;
				//We are in <h1>
				if ($hs > 0)
					$h = true;
				//We ended <h1>
			} else if (in_array($i, $h1_end)) {
				//Decrement it
				$hs--;
				//And we are not in <h1> tag anymore
				if ($hs == 0)
					$h = false;
			}
			//We have to insert image
			/*
			  foreach($img_pos as $k=>$pos) {
			  $img_pos[$k] -= $add_to_i + ($jump ? +1 : 0);
			  } */
			if (in_array($i, $img_pos)) {
				//Get the first image from the images array
				//AND remove it
				$nim = array_shift($imgs);
				//Save the image dimensions
				$nwidth = imagesx($nim);
				$nheight = imagesy($nim);
				//Copy the image to our output image
				imagecopymerge($im, $nim, $x, $y, 0, 0, $nwidth, $nheight, 100);
				//The next character will be after the image
				$x += $nwidth;
				//And the next row will start below out row
				//If this is the only image
				if ($add_at_end == 0)
				//After the image's height
					$add_at_end = $nheight;
				//If there are more than 1 images
				else
				//After the height of the highest image
					$add_at_end = max($add_at_end, $nheight);
			}
			if ($add_to_i > 0)
				$i += $add_to_i;
			if ($jump)
				continue;
			//Change font according to $bold
			if ($bold || $strong) {
				$font = Doo::conf()->SITE_PATH . 'protected/script/font/arial_bold.ttf';
			} else {
				$kill_bold = $prev_bold;
				$kill_strong = $prev_strong;
				$font = Doo::conf()->SITE_PATH . 'protected/script/font/arial.ttf';
			}
			//$font = "C://WINDOWS//Fonts//SimHei.ttf";
			$font = Doo::conf()->SITE_PATH . 'protected/script/font/Simhei.ttf';
			//Change font size if we are in <h1>
			if ($h)
				$size = 18;
			else
				$size = 11;
			if ($x + (($prev_strong && $prev_bold) ? 1 : 0) + $size > 400) {
				$x = 0;
				$y += 12 + 4 + $add_at_end;
			}
			//Add the character
			$pos = imagettftext($im, $size, 0, $x + (($prev_strong && $prev_bold) ? 1 : 0), $y, $black, $font, $char);

			//Save the x-position of the character end
			$x = $pos[2];
			//Stuff to add padding to bolded area
			if ($bold) {
				$prev_bold = true;
			}
			if ($strong) {
				$prev_strong = true;
			}
			if ($kill_bold)
				$prev_bold = false;
			if ($kill_strong)
				$prev_strong = false;
		}

		$re = false;
		switch ($toimagetype) {
			case "gif":
				$re = imagegif($im, $toimagepath);
				break;
			case "png":
				$re = imagepng($im, $toimagepath);
				break;
			case "jpeg":
				$re = imagejpeg($im, $toimagepath);
				break;
		}

		imagedestroy($im);
		return $re;
	}

	/**
	 * 将tiff文件转成pdf
	 * @param  $tiffpath
	 * @param  $pdfpath
	 * @author james.ou 2011-11-1
	 */
	public static function tiff2pdf($tiffpath, $pdfpath) {
		$pdf = pdf_new();
		pdf_set_parameter($pdf, "SearchPath", "./");
		pdf_set_parameter($pdf, "hypertextencoding", "winansi");
		pdf_set_parameter($pdf, "imagewarning", "false");

		pdf_open_file($pdf, $pdfpath);

		// Convert and add pages until you reach end of the tiff
		for ($page = 1; $image = @pdf_open_image_file($pdf, "tiff", $tiffpath, "page", $page); $page++) {
			// Set page scale to 72 dpi using the width of the image (72 * 8.5 inches = 612 dpi)
			$scale = 612 / pdf_get_value($pdf, "imagewidth", $image);

			// Create a new page using the scaled height and width of the image
			pdf_begin_page($pdf, $scale * pdf_get_value($pdf, "imagewidth", $image), $scale * pdf_get_value($pdf, "imageheight", $image));

			// Place the scaled image in the top left corner of the page and end this page of the pdf
			pdf_place_image($pdf, $image, 0, 0, $scale);
			pdf_end_page($pdf);
		}

		// Finish the pdf File
		pdf_close($pdf);
	}

	/**
	 * 将pdf文件转成swf文件
	 * @param $pdfpath
	 * @param $swfpath
	 * @author james.ou 2011-11-1
	 */
	public static function pdf2swf($pdfpath, $swfpath) {
		if (PATH_SEPARATOR == ':') {
			$command = 'pdf2swf -o ' . $swfpath . ' -T -z -t -f ' . $pdfpath . ' -s languagedir=/usr/share/xpdf/xpdf-chinese-simplified -s flashversion=9';
		} else {
			$command = Doo::conf()->SITE_PATH . "/protected/script/swftools/pdf2swf.exe  " . $pdfpath . "  -o  " . $swfpath . "  -T 9";
		}
		exec($command);
	}

	/**
	 * 等比例生成缩略图
	 * @param $imgSrc
	 * @param $resize_width
	 * @param $resize_height
	 * @param $isCut
	 * @author james.ou 2011-11-1
	 */
	public function reSizeImg($imgSrc, $resize_width, $resize_height, $isCut = false) {
		//图片的类型
		$type = substr(strrchr($imgSrc, "."), 1);
		//初始化图象
		if ($type == "jpg") {
			$im = imagecreatefromjpeg($imgSrc);
		}
		if ($type == "gif") {
			$im = imagecreatefromgif($imgSrc);
		}
		if ($type == "png") {
			$im = imagecreatefrompng($imgSrc);
		}
		//目标图象地址
		$full_length = strlen($imgSrc);
		$type_length = strlen($type);
		$name_length = $full_length - $type_length;
		$name = substr($imgSrc, 0, $name_length - 1);
		$dstimg = $name . "_s." . $type;

		$width = imagesx($im);
		$height = imagesy($im);

		//生成图象
		//改变后的图象的比例
		$resize_ratio = ($resize_width) / ($resize_height);
		//实际图象的比例
		$ratio = ($width) / ($height);
		if (($isCut) == 1) { //裁图
			if ($ratio >= $resize_ratio) { //高度优先
				$newimg = imagecreatetruecolor($resize_width, $resize_height);
				imagecopyresampled($newimg, $im, 0, 0, 0, 0, $resize_width, $resize_height, (($height) * $resize_ratio), $height);
				ImageJpeg($newimg, $dstimg);
			}
			if ($ratio < $resize_ratio) { //宽度优先
				$newimg = imagecreatetruecolor($resize_width, $resize_height);
				imagecopyresampled($newimg, $im, 0, 0, 0, 0, $resize_width, $resize_height, $width, (($width) / $resize_ratio));
				ImageJpeg($newimg, $dstimg);
			}
		} else { //不裁图
			if ($ratio >= $resize_ratio) {
				$newimg = imagecreatetruecolor($resize_width, ($resize_width) / $ratio);
				imagecopyresampled($newimg, $im, 0, 0, 0, 0, $resize_width, ($resize_width) / $ratio, $width, $height);
				ImageJpeg($newimg, $dstimg);
			}
			if ($ratio < $resize_ratio) {
				$newimg = imagecreatetruecolor(($resize_height) * $ratio, $resize_height);
				imagecopyresampled($newimg, $im, 0, 0, 0, 0, ($resize_height) * $ratio, $resize_height, $width, $height);
				ImageJpeg($newimg, $dstimg);
			}
		}
		ImageDestroy($im);
	}

	/**
	 * 添加值到目标值末尾
	 * @param $var 要添加的值qqq
	 * @param $subject 被添加的目标值aaa,sss
	 * @return 新的值aaa,sss,qqq
	 */
	public static function append_to_subject($var, $subject) {
		if ($subject == "") {
			return $var;
		} else {
			//$subject = $this->trim_str($subject, ",");
			$array = explode(",", $subject);
			if (!in_array($var, $array)) {
				array_push($array, $var);
			}
			$new_str = implode(',', $array);
			return $new_str;
		}
	}

	/**
	 * 将json转为xml
	 * @param  $source json参数
	 * @param $charset 编码方式
	 * @return xml
	 */
	public static function json2xml($source, $charset = 'utf8') {
		if (empty($source)) {
			return false;
		}
		$array = json_decode($source);  //php5，以及以上，如果是更早版本，請下載JSON.php
		$xml = '<!--l version="1.0" encoding="' . $charset . '-->';
		$xml .= '<root>' . self::json2xmlchange($array) . '</root>';
		return $xml;
	}

	private static function json2xmlchange($source) {
		$string = "";
		foreach ($source as $k => $v) {
			$string .="<" . $k . ">";
			if (is_array($v) || is_object($v)) {       //判断是否是数组，或者，对像
				$string .= self::json2xmlchange($v);        //是数组或者对像就的递归调用
			} else {
				$string .=$v;                        //取得标签数据
			}
			$string .="</" . $k . ">";
		}
		return $string;
	}

	/**
	 * 将xml转为json
	 * @param  $source xml文件或字符串
	 * @return json
	 */
	public static function xml2json($source) {
		if (is_file($source)) {             //传的是文件，还是xml的string的判断
			$xml_array = simplexml_load_file($source);
		} else {
			$xml_array = simplexml_load_string($source);
		}
		$json = json_encode($xml_array,JSON_UNESCAPED_UNICODE);  //php5，以及以上，如果是更早版本，請下載JSON.php
		return $json;
	}

	//合并2个数字的值，返回新的数组
	public static function array_merger_value($a1, $a2) {
		$a = array();
		foreach ($a1 as $v) {
			array_push($a, $v);
		}
		foreach ($a2 as $v) {
			array_push($a, $v);
		}
		return $a;
	}

	//添加时间
	public static function addtime($datetime, $count, $format, $type = 'd') {
		$timestamp = 0;
		switch ($type) {
			case 'd':
				$timestamp = $count * 24 * 60 * 60;
				break;
			case 'h':
				$timestamp = $count * 60 * 60;
				break;
			case 'm':
				$timestamp = $count * 60;
				break;
			case 's':
				$timestamp = $count;
				break;
			default :
				$timestamp = $count * 24 * 60 * 60;
				break;
		}
		return date($format, strtotime($datetime) + $timestamp);
	}

	/*
	 * 生成guid
	 */

	public static function guid() {
		if (function_exists('com_create_guid')) {
			return com_create_guid();
		} else {
			mt_srand((double) microtime() * 10000); //optional for php 4.2.0 and up.
			$charid = strtoupper(md5(uniqid(rand(), true)));
			$hyphen = chr(45); // "-"
			$uuid = chr(123)// "{"
					. substr($charid, 0, 8) . $hyphen
					. substr($charid, 8, 4) . $hyphen
					. substr($charid, 12, 4) . $hyphen
					. substr($charid, 16, 4) . $hyphen
					. substr($charid, 20, 12)
					. chr(125); // "}"
			return $uuid;
		}
	}

	/**
	 * Creates an example PDF TEST document using TCPDF
	 * HTML转PDF
	 * @package com.tecnick.tcpdf
	 * @abstract TCPDF - Example: Default Header and Footer
	 * @author Nicola Asuni
	 * @since 20121121
	 */
	public static function tcpdf($html, $filename, $path) {
		require_once(Doo::conf()->SITE_PATH . 'protected/script/tcpdf/config/lang/eng.php');
		require_once(Doo::conf()->SITE_PATH . 'protected/script/tcpdf/tcpdf.php');

		// create new PDF document
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		//$pdf->SetAuthor('Nicola Asuni');
		//$pdf->SetTitle('TCPDF Example 001');
		$pdf->SetSubject('TCPDF Tutorial');
		$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

		// set default header data
		//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 001', PDF_HEADER_STRING, array(0, 64, 255), array(0, 64, 128));
		//$pdf->setFooterData($tc=array(0, 64, 0), $lc=array(0, 64, 128));
		// set header and footer fonts
		//$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		//$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		//set margins
		//$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		//$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		//set auto page breaks
		//$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		//set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		//set some language-dependent strings
		$pdf->setLanguageArray($l);

		// ---------------------------------------------------------
		// set default font subsetting mode
		//$pdf->setFontSubsetting(true);
		// Set font
		// dejavusans is a UTF-8 Unicode font, if you only need to
		// print standard ASCII chars, you can use core fonts like
		// helvetica or times to reduce file size.
		//$pdf->SetFont('dejavusans', '', 14, '', true);
		//这个字体支持中文
		$pdf->SetFont('droidsansfallback', '', 10, '', true);

		// Add a page
		// This method has several options, check the source code documentation for more information.
		$pdf->AddPage();

		// set text shadow effect
		$pdf->setTextShadow(array('enabled' => true, 'depth_w' => 0.2, 'depth_h' => 0.2, 'color' => array(196, 196, 196), 'opacity' => 0, 'blend_mode' => 'Normal'));

		// Set some content to print
		/* $html = '
		  <h1>Welcome to <a href="http://www.tcpdf.org" style="text-decoration:none;background-color:#CC0000;color:black;">&nbsp;<span style="color:black;">TC</span><span style="color:white;">PDF</span>&nbsp;</a>!</h1>
		  <i>This is the first example of TCPDF library.</i>
		  <p>This text is printed using the <i>writeHTMLCell()</i> method but you can also use: <i>Multicell(), writeHTML(), Write(), Cell() and Text()</i>.</p>
		  <p>Please check the source code documentation and other examples for further information.</p>
		  <p style="color:#CC0000;">TO IMPROVE AND EXPAND TCPDF I NEED YOUR SUPPORT, PLEASE <a href="http://sourceforge.net/donate/index.php?group_id=128076">MAKE A DONATION!</a></p>
		  '; */

		// Print text using writeHTMLCell()
		$pdf->writeHTMLCell($w = 0, $h = 0, $x = '', $y = '', $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);

		// ---------------------------------------------------------
		// Close and output PDF document
		// This method has several options, check the source code documentation for more information.

		$pdf->Output($path . $filename, 'F');
	}

	/*
	 * 生成csv
	 * add by suson 20140228
	 */

	public static function export_csv($newarr, $filename, $page = 1, $nowcount = 0, $allcount = 0, $enterpriseno = 0, $userno = 0) {

		if (-1 === $allcount) { //一次导出
			$cnt = 0;
			$limit = 100000;
			header("Content-Type: text/csv");
			if (strpos($_SERVER['HTTP_USER_AGENT'], "MSIE")) {
				header("Content-Disposition:filename=" . urlencode($filename) . ".csv");
			} else {
				header("Content-Disposition:filename=" . $filename . ".csv");
			}
			$fp = fopen('php://output', 'a');
			$count = count($newarr);
			for ($t = 0; $t < $count; $t++) {
				$cnt++;
				if ($limit == $cnt) { //刷新一下输出buffer，防止由于数据过多造成问题
					ob_flush();
					flush();
					$cnt = 0;
				}
				$row = $newarr[$t];
				foreach ($row as $i => $v) {
					$row[$i] = iconv('utf-8', 'gbk//IGNORE', $v);
				}
				fputcsv($fp, $row);
				unset($row);
			}
		} else {
			$filename_p = md5($filename . $enterpriseno . $userno);
			$filepath = Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER . "userfiles/temp/" . $filename_p . ".csv";
			$count = count($newarr);
			if ($page == 1 && file_exists($filepath)) {
				chmod($filepath, 0666);
				unlink($filepath);
			}

			if ($page > 1 && $nowcount == 0) {
				Doo::loadClass("Download");
				$down = new Download();
				$down->is_attachment = true;
				$down->Download($filepath, $filename . '.csv');
				if (file_exists($filepath)) {
					//unlink($filepath);
				}
			} else {
				$fp = fopen($filepath, 'a');
				for ($t = 0; $t < $count; $t++) {
					$row = $newarr[$t];
					foreach ($row as $i => $v) {
						$row[$i] = iconv('utf-8', 'gbk//IGNORE', $v);
					}
					fputcsv($fp, $row);
					unset($row);
				}
				if ($allcount < 1) {
					$process_width = "100%";
				} else {
					$process_width = round($nowcount / $allcount * 100, 2) . "%";
				}
				return array('num' => $allcount - $nowcount, 'width' => $process_width, 'process' => $nowcount . "/" . $allcount);
			}
		}
	}

	public static function ajax_export_excel($arr, $filename = 'excel', $img = null, $mergecells = '', $vertical = '', $horizontal = '', $broder = '', $bold = array(), $width = array(), $font_color = array(), $height = array(), $wrap_text = array(), $fontSize = array(), $page = 1, $nowcount = 0, $allcount = 0, $enterpriseno = 0, $userno = 0) {
		error_reporting(E_ALL);
		date_default_timezone_set('Europe/London');
		Doo::loadClass('phpexcel/PHPExcel');
		Doo::loadClass('UserCommon');

		//#james.ou 2014-7-3 #加上缓存,php://temp 或 memocache 缓存，
		$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
		$cacheSettings = array('memoryCacheSize' => '16MB');

		/*
		  $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_memcache;
		  $cacheSettings = array( 'memcacheServer'  => '192.168.1.2',
		  'memcachePort'    => 11211,
		  'cacheTime'       => 600  );
		 */
		PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

		//Userself::writeLog(date('H:i:s'));
//        $AZarr = range('A', 'Z');
//        array_push($AZarr, 'AA', 'AB','AC', 'AD','AE', 'AF','AG','AH', 'AI','AJ', 'AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ','BA','BB','BC','BD','BE','BF','BG','BH','BI','BJ','BK','BL');
		$AZ = range('A', 'Z');
		$AG = range('A', 'G');
		$AZarr = $AZ;
		foreach ($AG as $a) {
			foreach ($AZ as $b) {
				array_push($AZarr, $a . $b);
			}
		}
		$filename_p = md5($filename . $enterpriseno . $userno);
		$filepath = Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER . "userfiles/temp/" . $filename_p . ".xls";
		if ($page == 1 && file_exists($filepath)) {
			chmod($filepath, 0666);
			unlink($filepath);
		}
//        if($page > 1){
//            var_dump($page);
//            var_dump($nowcount);
//            var_dump($allcount);
//        }
		Doo::loadClass('UserCommon');
		if ($page > 1 && $nowcount == 0) {

			Doo::loadClass("Download");
			$down = new Download();
			$down->is_attachment = true;
			$down->Download($filepath, $filename . '.xls');
			if (file_exists($filepath)) {
				//unlink($filepath);
			}
			exit;
		} else {
			try {
				$objPHPExcel = file_exists($filepath) ? PHPExcel_IOFactory::load($filepath) : new PHPExcel();
				$objPHPExcel->setActiveSheetIndex(0);
				$row = $page > 1 ? intval($objPHPExcel->getActiveSheet()->getHighestRow()) : 0;  //上一次最后一条记录
				foreach ($arr as $k => $z) {
					if (is_array($z)) {
						$strphp = '$objPHPExcel->setActiveSheetIndex(0)';
						foreach ($z as $k1 => $z1) {
							$z1 = addslashes(str_replace("$", "", $z1));
							if ($img != null) {
								$strphp = $strphp . ' ->setCellValue("' . $AZarr[$k1] . ($k + 16 + 1 + $row) . '","' . $z1 . '")';
								//设置居中
								$objPHPExcel->getActiveSheet()->getStyle($AZarr[$k1] . ($k + 16 + 1))->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
							} else {
								$strphp = $strphp . ' ->setCellValue("' . $AZarr[$k1] . ($k + 1 + $row) . '","' . $z1 . '")';
								//$objPHPExcel->getActiveSheet()->getStyle($AZarr[$k1] . ($k + 16 + 1))->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
							}
						}
					}

					eval($strphp . ';');
					$strphp = '';
				}
				if (!empty($mergecells)) {
					foreach ($mergecells as $z) {
						//$objActSheet->mergeCells('B1:C22');
//                $objPHPExcel->getActiveSheet()->mergeCells($z[0] . ':' . $z[1]);
						$objPHPExcel->getActiveSheet()->mergeCells($z);
					}
				}
				//对其方式
				//垂直
				if ($vertical) {
					foreach ($vertical as $z) {
						$objPHPExcel->getActiveSheet()->getStyle($z)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					}
				}
				//水平
				if ($horizontal) {
					foreach ($horizontal as $z) {
						$objPHPExcel->getActiveSheet()->getStyle($z)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					}
				}
				//边框
				if (!empty($broder)) {
					foreach ($broder as $z) {
						$objPHPExcel->getActiveSheet()->getStyle($z)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objPHPExcel->getActiveSheet()->getStyle($z)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objPHPExcel->getActiveSheet()->getStyle($z)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objPHPExcel->getActiveSheet()->getStyle($z)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					}
				}

				//字体加粗
				if (!empty($bold)) {
					foreach ($bold as $z) {
						$objPHPExcel->getActiveSheet()->getStyle($z)->getFont()->setBold(true);
					}
				}

				//设置字体大小
				if (!empty($fontSize)) {
					foreach ($fontSize as $z) {
						$objPHPExcel->getActiveSheet()->getStyle($z)->getFont()->setSize(18);
					}
				}

				//设置列宽
				if (!empty($width)) {
					foreach ($width as $k => $z) {
						$objPHPExcel->getActiveSheet()->getColumnDimension($k)->setWidth($z);
					}
				}

				//设置字体颜色
				if (!empty($font_color)) {
					foreach ($font_color as $z) {
						$objPHPExcel->getActiveSheet()->getStyle($z)->getFont()->getColor()->setRGB('FF255000');
					}
				}

				//设置行高(索引$k为行高,值为行数)
				if (!empty($height)) {
					foreach ($height as $k => $z) {
						$objPHPExcel->getActiveSheet()->getRowDimension($z)->setRowHeight($k);
					}
				}

				// 单元格换行
				if (!empty($wrap_text)) {
					foreach ($wrap_text as $z) {
						$objPHPExcel->getActiveSheet()->getStyle($z)->getAlignment()->setWrapText(true);
					}
				}

				////设置当前活动sheet的名称
				$objPHPExcel->getActiveSheet()->setTitle($filename);

				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
				$objWriter->save($filepath);
			} catch (Exception $e) {
				echo 'Caught exception: ', $e->getMessage(), "\n";
			}
			if ($allcount < 1) {
				$process_width = "100%";
			} else {
				$process_width = round($nowcount / $allcount * 100, 2) . "%";
			}
			return array('num' => $allcount - $nowcount, 'width' => $process_width, 'process' => $nowcount . "/" . $allcount, 'row' => $row);
		}
		exit;
	}

	/*
	 * 生成excel
	 * add by james.ou 5-20
	 */

	public static function export_excel($arr, $filename = 'excel', $img = null, $mergecells = '', $vertical = '', $horizontal = '', $broder = '', $bold = array(), $width = array(), $font_color = array(), $height = array(), $wrap_text = array(), $fontSize = array(), $page = 1, $size = 0, $enterpriseno = 0, $userno = 0) {
		error_reporting(E_ALL);
		date_default_timezone_set('Europe/London');
		Doo::loadClass('phpexcel/PHPExcel');
		Doo::loadClass('UserCommon');

		//#james.ou 2014-7-3 #加上缓存,php://temp 或 memocache 缓存，
		$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
		$cacheSettings = array('memoryCacheSize' => '16MB');

		/*
		  $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_memcache;
		  $cacheSettings = array( 'memcacheServer'  => '192.168.1.2',
		  'memcachePort'    => 11211,
		  'cacheTime'       => 600  );
		 */
		PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

		//Userself::writeLog(date('H:i:s'));
//        $AZarr = range('A', 'Z');
//        array_push($AZarr, 'AA', 'AB','AC', 'AD','AE', 'AF','AG','AH', 'AI','AJ', 'AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ','BA','BB','BC','BD','BE','BF','BG','BH','BI','BJ','BK','BL');
		$AZ = range('A', 'Z');
		$AG = range('A', 'G');
		$AZarr = $AZ;
		foreach ($AG as $a) {
			foreach ($AZ as $b) {
				array_push($AZarr, $a . $b);
			}
		}
//        echo "<pre>";
//        var_dump($AZarr);
//        echo "<pre>";exit;
		//实例化对象
		if ($size > 0) {
			$filename_p = md5($filename . $enterpriseno . $userno);
			$filepath = Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER . "userfiles/temp/" . $filename_p . ".xls";
			$allcount = count($arr);
			$start = ($page - 1) * $size;
			$arr = array_slice($arr, $start, $size);
			if ($page == 1 && file_exists($filepath)) {
				unlink($filepath);
			}
			$count = count($arr);
			$nowcount = $count + $start;
			if ($count < 1) {
				Doo::loadClass("Download");
				$down = new Download();
				$down->is_attachment = true;
				$down->Download($filepath, $filename . '.xls');
				if (file_exists($filepath)) {
					//unlink($filepath);
				}
			} else {
				$objPHPExcel = file_exists($filepath) ? PHPExcel_IOFactory::load($filepath) : new PHPExcel();
				$objPHPExcel->setActiveSheetIndex(0);
				$row = file_exists($filepath) ? $objPHPExcel->getActiveSheet()->getHighestRow() : 0;  //上一次最后一条记录
				foreach ($arr as $k => $z) {
					if (is_array($z)) {
						$strphp = '$objPHPExcel->setActiveSheetIndex(0)';
						foreach ($z as $k1 => $z1) {
							$z1 = addslashes(str_replace("$", "", $z1));
							if ($img != null) {
								$strphp = $strphp . ' ->setCellValue("' . $AZarr[$k1] . ($k + 16 + 1 + $row) . '","' . $z1 . '")';
								//设置居中
								$objPHPExcel->getActiveSheet()->getStyle($AZarr[$k1] . ($k + 16 + 1))->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
							} else {
								$strphp = $strphp . ' ->setCellValue("' . $AZarr[$k1] . ($k + 1 + $row) . '","' . $z1 . '")';
								//$objPHPExcel->getActiveSheet()->getStyle($AZarr[$k1] . ($k + 16 + 1))->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
							}
						}
					}

					eval($strphp . ';');
					$strphp = '';
				}
				if ($allcount - $nowcount < 1) { //最后一次设置格式
					if (!empty($mergecells)) {
						foreach ($mergecells as $z) {
							//$objActSheet->mergeCells('B1:C22');
//                $objPHPExcel->getActiveSheet()->mergeCells($z[0] . ':' . $z[1]);
							$objPHPExcel->getActiveSheet()->mergeCells($z);
						}
					}
					//对其方式
					//垂直
					if ($vertical) {
						foreach ($vertical as $z) {
							$objPHPExcel->getActiveSheet()->getStyle($z)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
						}
					}
					//水平
					if ($horizontal) {
						foreach ($horizontal as $z) {
							$objPHPExcel->getActiveSheet()->getStyle($z)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						}
					}
					//边框
					if (!empty($broder)) {
						foreach ($broder as $z) {
							$objPHPExcel->getActiveSheet()->getStyle($z)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
							$objPHPExcel->getActiveSheet()->getStyle($z)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
							$objPHPExcel->getActiveSheet()->getStyle($z)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
							$objPHPExcel->getActiveSheet()->getStyle($z)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						}
					}

					//字体加粗
					if (!empty($bold)) {
						foreach ($bold as $z) {
							$objPHPExcel->getActiveSheet()->getStyle($z)->getFont()->setBold(true);
						}
					}

					//设置字体大小
					if (!empty($fontSize)) {
						foreach ($fontSize as $z) {
							$objPHPExcel->getActiveSheet()->getStyle($z)->getFont()->setSize(18);
						}
					}

					//设置列宽
					if (!empty($width)) {
						foreach ($width as $k => $z) {
							$objPHPExcel->getActiveSheet()->getColumnDimension($k)->setWidth($z);
						}
					}

					//设置字体颜色
					if (!empty($font_color)) {
						foreach ($font_color as $z) {
							$objPHPExcel->getActiveSheet()->getStyle($z)->getFont()->getColor()->setRGB('FF255000');
						}
					}

					//设置行高(索引$k为行高,值为行数)
					if (!empty($height)) {
						foreach ($height as $k => $z) {
							$objPHPExcel->getActiveSheet()->getRowDimension($z)->setRowHeight($k);
						}
					}

					// 单元格换行
					if (!empty($wrap_text)) {
						foreach ($wrap_text as $z) {
							$objPHPExcel->getActiveSheet()->getStyle($z)->getAlignment()->setWrapText(true);
						}
					}

					////设置当前活动sheet的名称
					$objPHPExcel->getActiveSheet()->setTitle($filename);
				}

				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
				$objWriter->save($filepath);

				return array('num' => $allcount - $nowcount, 'width' => round($nowcount / $allcount * 100, 2) . "%", 'process' => $nowcount . "/" . $allcount, 'row' => $row);
			}
			exit;
		} else {
			$objPHPExcel = new PHPExcel();

			//设置文档属性
			$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
					->setLastModifiedBy("Maarten Balliauw")
					->setTitle("Office 2007 XLSX Test Document")
					->setSubject("Office 2007 XLSX Test Document")
					->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
					->setKeywords("office 2007 openxml php")
					->setCategory("Test result file");

			//添加图片
			if ($img != null) {
				$objPHPExcel->getActiveSheet()->mergeCells('A1:H16');
				if (is_array($img)) {
					//为excel加图片
					$objDrawing = new PHPExcel_Worksheet_Drawing();
					$objDrawing->setName($img['name']);
					$objDrawing->setDescription($img['Description']);
					$objDrawing->setPath($img['path']);
					$objDrawing->setHeight($img['width']);
					$objDrawing->setWidth($img['height']);
					$objDrawing->setCoordinates('A1');  //坐表
					$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
				}
			}

			//循环列表 拼接表格
			foreach ($arr as $k => $z) {
				if (is_array($z)) {
					$strphp = '$objPHPExcel->setActiveSheetIndex(0)';
					foreach ($z as $k1 => $z1) {
						$z1 = addslashes(str_replace("$", "", $z1));
						if ($img != null) {
							$strphp = $strphp . ' ->setCellValue("' . $AZarr[$k1] . ($k + 16 + 1) . '","' . $z1 . '")';
							//设置居中
							$objPHPExcel->getActiveSheet()->getStyle($AZarr[$k1] . ($k + 16 + 1))->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						} else {
							$strphp = $strphp . ' ->setCellValue("' . $AZarr[$k1] . ($k + 1) . '","' . $z1 . '")';
							$objPHPExcel->getActiveSheet()->getStyle($AZarr[$k1] . ($k + 16 + 1))->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						}
					}
				}

				eval($strphp . ';');
				$strphp = '';
			}
			//合并单元格
			if (!empty($mergecells)) {
				foreach ($mergecells as $z) {
					//$objActSheet->mergeCells('B1:C22');
//                $objPHPExcel->getActiveSheet()->mergeCells($z[0] . ':' . $z[1]);
					$objPHPExcel->getActiveSheet()->mergeCells($z);
				}
			}
			//对其方式
			//垂直
			if ($vertical) {
				foreach ($vertical as $z) {
					$objPHPExcel->getActiveSheet()->getStyle($z)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
				}
			}
			//水平
			if ($horizontal) {
				foreach ($horizontal as $z) {
					$objPHPExcel->getActiveSheet()->getStyle($z)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				}
			}
			//边框
			if (!empty($broder)) {
				foreach ($broder as $z) {
					$objPHPExcel->getActiveSheet()->getStyle($z)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					$objPHPExcel->getActiveSheet()->getStyle($z)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					$objPHPExcel->getActiveSheet()->getStyle($z)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					$objPHPExcel->getActiveSheet()->getStyle($z)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				}
			}

			//字体加粗
			if (!empty($bold)) {
				foreach ($bold as $z) {
					$objPHPExcel->getActiveSheet()->getStyle($z)->getFont()->setBold(true);
				}
			}

			//设置字体大小
			if (!empty($fontSize)) {
				foreach ($fontSize as $z) {
					$objPHPExcel->getActiveSheet()->getStyle($z)->getFont()->setSize(18);
				}
			}

			//设置列宽
			if (!empty($width)) {
				foreach ($width as $k => $z) {
					$objPHPExcel->getActiveSheet()->getColumnDimension($k)->setWidth($z);
				}
			}

			//设置字体颜色
			if (!empty($font_color)) {
				foreach ($font_color as $z) {
					$objPHPExcel->getActiveSheet()->getStyle($z)->getFont()->getColor()->setRGB('FF255000');
				}
			}

			//设置行高(索引$k为行高,值为行数)
			if (!empty($height)) {
				foreach ($height as $k => $z) {
					$objPHPExcel->getActiveSheet()->getRowDimension($z)->setRowHeight($k);
				}
			}

			// 单元格换行
			if (!empty($wrap_text)) {
				foreach ($wrap_text as $z) {
					$objPHPExcel->getActiveSheet()->getStyle($z)->getAlignment()->setWrapText(true);
				}
			}

			////设置当前活动sheet的名称
			$objPHPExcel->getActiveSheet()->setTitle($filename);



			//输出头信息

			header('Content-Type: application/vnd.ms-excel');
			if (strpos($_SERVER['HTTP_USER_AGENT'], "MSIE") || strpos($_SERVER['HTTP_USER_AGENT'], "like"))
				header('Content-Disposition: attachment; filename="' . urlencode($filename) . '.xls'); //如果是ie存为的名字要urlencode
			else
				header('Content-Disposition: attachment; filename="' . $filename . '.xls'); //存为的名字


			header('Cache-Control: max-age=0');

//        header("Content-Type: application/force-download");
//        header("Content-Type: application/octet-stream");
//        header("Content-Type: application/download");
//        header('Content-Disposition:inline;filename="'.$filename.'"');
//        header("Content-Transfer-Encoding: binary");
//        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
//        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
//        header("Pragma: no-cache");


			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save('php://output');
			//Userself::writeLog(date('H:i:s'));
			//$objWriter->save('test.xls');
			//删除临时图片文件
			if ($img != null) {
				@unlink($img['path']);
			}
		}


		exit;
	}

	/**
	 * 生成目录 如 123456 生成 1/23/456
	 * @param  $str 字符串
	 * @return string 处理后的字符串
	 */
	public static function splitstr($str) {
		if (empty($str))
			return"";
		$arr = str_split($str);
		$i = 1;
		$last = -1;
		while ($i > 0) {
			$str = '';
			for ($ii = 0; $ii < $i; $ii++) {
				$last++;
				if ($last + 1 > count($arr)) {
					$i = -1;
					break;
				} else {
					$str.=$arr[$last];
				}
			}
			if ($str != '') {
				$newarr[] = $str;
			}
			if ($i == -1)
				break;
			$i++;
		}

		return implode($newarr, '/');
	}

	/**
	 * 获取用户头像缩略图
	 * @param  $enterpriseno
	 * @return <type>
	 */
	public static function getCompanyLogoUrl($enterpriseno) {
		$imgurl = '/uploads/userfiles/elogo/' . self::splitstr($enterpriseno) . '/logo_' . $enterpriseno . '.jpg';
		return $imgurl;
	}

	/**
	 * 提示框
	 * @param  $success true(成功)\false(失败)
	 * @param  $message 提示内容
	 * @return <type>
	 */
	public static function jsonTips($message, $success = false) {
		$json = array(
			"success" => false,
			"message" => "请求失败",
			"data" => array()
		);
		$json["success"] = $success;
		$json["message"] = $message;
		return json_encode($json,JSON_UNESCAPED_UNICODE);
	}

	/* 去除数组值前后的空格
	 * create by General 201308
	 */

	public static function TrimArray($Input) {
		if (!is_array($Input))
			return trim($Input);
		return array_map('self::TrimArray', $Input);
	}

	/* 二维数组去掉重复值
	 * create by General 201308
	 */

	public static function unique_array2D($array2D, $stkeep = false, $ndformat = true) {
		// 判断是否保留一级数组键 (一级数组键可以为非数字)
		if ($stkeep)
			$stArr = array_keys($array2D);

		// 判断是否保留二级数组键 (所有二级数组键必须相同)
		if ($ndformat)
			$ndArr = array_keys(end($array2D));

		//降维,也可以用implode,将一维数组转换为用逗号连接的字符串
		foreach ($array2D as $v) {
			$v = join(",", $v);
			$temp[] = $v;
		}

		//去掉重复的字符串,也就是重复的一维数组
		$temp = array_unique($temp);

		//再将拆开的数组重新组装
		foreach ($temp as $k => $v) {
			if ($stkeep)
				$k = $stArr[$k];
			if ($ndformat) {
				$tempArr = explode(",", $v);
				foreach ($tempArr as $ndkey => $ndval)
					$output[$k][$ndArr[$ndkey]] = $ndval;
			}
			else
				$output[$k] = explode(",", $v);
		}

		return $output;
	}

	/**
	 * 获取数组value add by dxf 2013-09-03 11:57:40
	 * @param  $enterpriseno
	 * @return <type>
	 */
	public static function getArrayValue($array, $index, $return = NULL) {
		if (isset($array[$index])) {
			return $array[$index];
		} else {
			return $return;
		}
	}

	/**
	 * 获取value add by dxf 2013-09-03 11:57:40
	 * @param $value
	 * @param $return
	 * @return <type>
	 */
	public static function getValue($value, $return = NULL) {
		isset($value) ? $value : $return;
	}

	/**
	 * 获取value add by dxf 2013-09-03 11:57:40
	 * @param $value
	 * @param $return
	 * @return <type>
	 */
	public static function getValueInt($value) {
		isset($value) ? $value : 0;
	}

	/**
	 * 获取value add by dxf 2013-09-03 11:57:40
	 * @param $value
	 * @param $return
	 * @return <type>
	 */
	public static function getValueString($value) {
		isset($value) ? $value : '';
	}


	/**
	 * 获取二维数组中，某个field的值
	 * add by dxf 2016-12-24 14:42:48
	 * @param  [type] $array  [description]
	 * @param  [type] $field  [description]
	 * @param  [type] $value  [description]
	 * @param  [type] $return [description]
	 * @return [type]         [description]
	 */
	public static function getArray2D($array, $field, $value, $return = NULL){
		foreach ($array as $v) {
			if ($v[$field] == $value) {
				return $v;
			}
		}
		return $return;
	}

	/*
	 * 判断是否存在于二维数组中
	 * $str 要查找的字符串
	 * $array 二维数组
	 * $field 二维数组中指定字段
	 * return true
	 * created by General 20131204
	 */
	public static function inArray2D($array, $field, $value, $return = false){
		foreach ($array as $v) {
			if ($v[$field] == $value)
				return true;
		}
		return $return;
	}
//	/* 检查接收字段
//	 * $field 要检测的字段，多个用逗号隔开
//	 * $type 接收种类 post\get\request
//	 * $default 如果不存在且为空所指定的默认值 null\0
//	 * created by General 20131219
//	 */
//
//	public static function checkRequest($type, $field = 'post', $default = null) {
//		$json['success'] = false;
//		$json['message'] = '对不起，参数错误，请刷新！';
//		if (empty($type) || empty($field)) {
//			echo json_encode($json,JSON_UNESCAPED_UNICODE);
//			exit;
//		}
//
//		$field_arr = explode(',', $field);
//		foreach ($field_arr as $k => $v) {
//			switch ($type) {
//				case 'request':
//					if (!isset($_REQUEST[$v])) {
//						echo json_encode($json,JSON_UNESCAPED_UNICODE);
//						exit;
//					}
//					break;
//				case 'get':
//					if (!isset($_GET[$v])) {
//						echo json_encode($json,JSON_UNESCAPED_UNICODE);
//						exit;
//					}
//					break;
//				default:
//					if (!isset($_POST[$v])) {
//						echo json_encode($json,JSON_UNESCAPED_UNICODE);
//						exit;
//					}
//					break;
//			}
//		}
//		if (!isset($_REQUEST[$field])) {
//			echo json_encode($json,JSON_UNESCAPED_UNICODE);
//			exit;
//		} else {
//			$field = $_REQUEST[$field];
//			return $field;
//		}
//	}

	/*
	 * @Purpose: 检查post/get/request传过来的键是否存在或者他的值是否为空  add by qax 2013-12-25
	 * @Method name: checkRequestVal();
	 * @Parameters:
	  说明: $original_type: 传值方式;post/get/request
	  $string: 字符串形式，格式是‘key|value|tips,key2|value2|tips2’,键值对内部用‘|’分割，键值对与键值对之间用逗号‘,’分割，
	  第一个key是所要检查的键，第二个value是当key不存在时给key指定一个值，第三个tips是当键不存在时提示信息tips，value和tips可以缺省。
	  因为可以调用统一的默认值$default_value和统一的错误提示$noexist_tips;
	  $noexist_value_set ：当键不存在时，$string没有指定默认值，通过判断这里是否设置默认值。
	  $noexist_tips_set : 见参数$noexist_tips;
	  $empty_tips_set : 见参数$empty_tips;
	  $empty_value_set ：当键存在但为空时，$empty_value_set为真时，则将$default_value赋给键key；
	  $default_value : 默认值,当键key不存在时，$string没有传入指定的值，$noexist_value_set为真时,将$default_value 赋给键key;
	  键key存在但为空，$empty_value_set为真时，将$default_value 赋给键key;

	  $noexist_tips ：当键key不存在时，$string也没有提示信息，$noexist_tips_set为真时，即将$noexist_tips输出；
	  $empty_tips ：当键key存在为空时，$empty_tips_set为真时，即将$empty_tips输出;
	  $return_noexist_array ：为真时，返回键不存在组成的数组；
	  $return_empty_array ：为真时，返回键存在但为空组成的数组。

	  @return: $return_noexist_array ：为真时，返回键不存在组成的数组；
	  $return_empty_array ：为真时，返回键存在但为空组成的数组。
	  两者都为假，不返回任何内容。

	  @方法调用：
	  先引入//Doo::loadClass('Common');
	  调用 : self::checkRequestVal('post', 'id|1|tips,pid|2,fid',$noexist_value_set = true,
	  $empty_value_set = false, $noexist_tips_set = true, $empty_tips_set = true,
	  $default_value = 0, $noexist_tips = '参数错误', $empty_tips = '值为空', $return_noexist_array = false,
	  $return_empty_array = false);
	  说明 ：'id|1|找不到'表示当键id不存在时，将1赋给键id，同时会输出错误提示'找不到';
	  当键存在但为空时，可以通过$empty_value_set来控制是否设定默认值，通过$empty_tips_set来控制
	  是否输出为空错误提示。若$empty_value_set为真时，则将默认值$default_value赋给键id，
	  若$empty_tips_set为真，则输出默认的为空错误提示$empty_tips
	  'pid|2'表示当键不存在时，将值2赋给键pid,由于后面没有带错误提示，需不需要输出错误提示，
	  可用$noexist_tips_set来控制，$noexist_tips_set为真，则输出，反之则不输出。键pid存在
	  但为空时，操作同上。

	  'fid'不带指定值和错误提示，当不存在时，可以通过$noexist_value_set来控制是否设定默认值，
	  通过$noexist_tips_set来控制是否输出错误提示，操作同上，键fid存在但为空的操作也同上。
	 *
	 */

	/* public static function checkRequestVal($original_type = 'post', $string = 'id|1|tips,caseid|1,tag', $noexist_value_set = true, $empty_value_set = false, $noexist_tips_set = false, $empty_tips_set = false, $default_value = 0, $noexist_tips = '参数错误', $empty_tips = '值为空', $return_noexist_array = false, $return_empty_array = false) {

		//$default_value = trim($default_value);
		$type = strtoupper($original_type);
		$get_type = '$_' . $type;
		if (false == is_string($string) && false == is_numeric($string)) {
			self::$json['message'] = '抱歉！参数错误，请刷新！';
			echo json_encode(self::$json,JSON_UNESCAPED_UNICODE);
			exit;
		}
		if (false == is_string($noexist_tips) && false == is_int($noexist_tips)) {
			self::$json['message'] = '抱歉！参数错误，请刷新！';
			echo json_encode(self::$json,JSON_UNESCAPED_UNICODE);
			exit;
		}
		if (false == is_string($empty_tips) && false == is_numeric($empty_tips)) {
			self::$json['message'] = '抱歉！参数错误，请刷新！';
			echo json_encode(self::$json,JSON_UNESCAPED_UNICODE);
			exit;
		}
		$key_value_tips = explode(',', $string);
		$return_array = array();
		$noexist_array = array();
		$empty_array = array();
		foreach ($key_value_tips as $value) {
			$value = trim($value);
			$exist_bar = strpos($value, '|');
			if (false !== $exist_bar) { //输入竖杠
				$k_v_t = explode('|', $value);
				$k_v_t[0] = trim($k_v_t[0]);
				eval('$isset_value = isset(' . $get_type . "['" . $k_v_t[0] . "']);");
				if (false != $isset_value) { //所检查的键存在
					eval('$isempty_value = (trim(' . $get_type . "['" . $k_v_t[0] . "']) == '');");
					if (false != $isempty_value) { //所检查键的值为空
						//将值为空的键收集
						$empty_array[] = $k_v_t[0];
						if (false != $empty_value_set) { //所检查的键的值为空，调用没有传入指定值时，判断$empty_value_set为真，
							//则将默认值$default_value赋给这个键
							eval($get_type . "['" . $k_v_t[0] . "'] = '" . $default_value . "';");
						}
						if (false != $empty_tips_set) {
							self::$json['message'] = $k_v_t[0] . ' ' . $empty_tips;
							echo json_encode(self::$json,JSON_UNESCAPED_UNICODE);
							exit;
							$k_v_t[0] . ' ' . $empty_tips;
						}
					}
				} else { //所检查的键不存在
					$noexist_array[] = $k_v_t[0];

					if (false != isset($k_v_t[1])) { //竖杠后面有值
						//将这个值赋给对应的键名
						$k_v_t[1] = trim($k_v_t[1]);
						if ('' != $k_v_t[1]) {
							eval($get_type . "['" . $k_v_t[0] . "'] = '" . $k_v_t[1] . "';");
						} else {
							if (false != $noexist_value_set) { //没有传入默认值参数，若$noexist_value_set 为真,
								//则设置默认的值$default_value;
								eval($get_type . "['" . $k_v_t[0] . "'] = '" . $default_value . "';");
							}
						}

						if (false != isset($k_v_t[2])) { //竖杠后面有提示错误提示信息
							$k_v_t[2] = trim($k_v_t[2]);
							if ('' != $k_v_t[2]) {
								self::$json['message'] = $k_v_t[0] . ' ' . $k_v_t[2];
								echo json_encode(self::$json,JSON_UNESCAPED_UNICODE);
								exit;
								$k_v_t[0] . ' ' . $empty_tips;
							} else {

								if (false != $noexist_tips_set) {
									self::$json['message'] = $k_v_t[0] . ' ' . $default_tips;
									echo json_encode(self::$json,JSON_UNESCAPED_UNICODE);
									exit;
									$k_v_t[0] . ' ' . $empty_tips;
								}
							}
						} else {

							if (false != $noexist_tips_set) {
								self::$json['message'] = $k_v_t[0] . ' ' . $noexist_tips;
								echo json_encode(self::$json,JSON_UNESCAPED_UNICODE);
								exit;
								$k_v_t[0] . ' ' . $empty_tips;
							}
						}
					} else { //竖杠后面没有值
						////没有传入默认值参数，若$noexist_value_set 为真,则设置默认的值$default_value;
						if (false != $noexist_value_set)
							eval($get_type . "['" . $k_v_t[0] . "'] = '" . $default_value . "';");

						//参数$noexist_tips_set 为真，则提示默认错误$default_tips
						if (false != $noexist_tips_set) {
							self::$json['message'] = $k_v_t[0] . ' ' . $noexist_tips;
							echo json_encode(self::$json,JSON_UNESCAPED_UNICODE);
							exit;
							$k_v_t[0] . ' ' . $empty_tips;
						}
						echo '<meta http-equiv="content-type" content="text/html; charset=UTF-8" /><br />' . $k_v_t[0] . ' ' . $noexist_tips;
					}
				}
			} else { //调用不输入竖杠
				//$isset_value = isset($_GET[$value]);
				eval('$isset_value = isset(' . $get_type . "['" . $value . "']);");

				if (false != $isset_value) { //所检查的键存在
					eval('$no_bar_empty =  trim(' . $get_type . "['" . $value . "']) == '';");
					//var_dump($no_bar_empty);
					if (false != $no_bar_empty) { //所检查键的值为空
						//收集值为空的键
						$empty_array[] = $value;
						//所检查键的值为空，调用没有传入默认值参数，若$empty_value_set 为真，则设置默认的值$default_value;
						if (false != $empty_value_set)
							eval($get_type . "['" . $value . "'] = '" . $default_value . "';");

						//所检查键的值为空，$empty_tips_set为真，则提示默认错误$default_tips
						if (false != $empty_tips_set) {
							self::$json['message'] = $value . ' ' . $empty_tips;
							echo json_encode(self::$json,JSON_UNESCAPED_UNICODE);
							exit;
							$k_v_t[0] . ' ' . $empty_tips;
						}
					}
				} else { //所检查的键不存在
					$noexist_array[] = $value;

					//所检查键不存在时，调用没有传入默认值参数，若$noexist_value_set 为真,则设置默认的值$default_value;
					if (false != $noexist_value_set)
						eval($get_type . "['" . $value . "'] = '" . $default_value . "';");

					//参数$noexist_tips_set 为真，则提示默认错误$default_tips
					if (false != $noexist_tips_set) {
						self::$json['message'] = $value . ' ' . $noexist_tips;
						echo json_encode(self::$json,JSON_UNESCAPED_UNICODE);
						exit;
						$k_v_t[0] . ' ' . $empty_tips;
					}
				}
			}
		}
		if (false != $return_noexist_array && false != $return_empty_array) {
			$return_array['noexist_key'] = $noexist_array;
			$return_array['empty_key'] = $empty_array;
			return $return_array;
		} elseif (false != $return_noexist_array) {
			return $noexist_array;
		} elseif (false != $return_empty_array) {
			return $empty_array;
		}
	} */

	/**
	 * 生成8位不重复的SN字符串
	 * @param array $opt 要md5的字符串
	 * @param int $start 开始位
	 * @param int $len 长度
	 * @return string
	 * @author james.ou
	 */
	public static function builderSN($opt, $start = 8, $len = 16) {
		if (!is_array($opt))
			$opt = array($opt);

		return substr(md5(implode('', $opt)), $start, $len);
	}

	/*     * *********************
	 * *功能:将多维数组合并为一位数组
	 * *$array:需要合并的数组
	 * *$clearRepeated:是否清除并后的数组中得重复值
	 * General.20131227
	 * ********************* */

	public static function array_multiToSingle($array, $clearRepeated = false) {
		if (!isset($array) || !is_array($array) || empty($array)) {
			return false;
		}
		if (!in_array($clearRepeated, array('true', 'false', ''))) {
			return false;
		}
		static $result_array = array(); //属性为静态，当多次调用时，数组$result_array会累加

		foreach ($array as $value) {
			if (is_array($value)) {
				self::array_multiToSingle($value);
			} else {
				$result_array[] = $value;
			}
		}
		if ($clearRepeated) {
			$result_array = array_unique($result_array);
		}
		return $result_array;
	}

	/**
	 * 生成唯一key
	 * @param array $opt array(8000098,5000040,name,...)
	 * @return string
	 * 字段长度为 bigint 字符串形式返回
	 * @author james.ou
	 */
	public static function builder_primary_id($opt = array()) {
		if (Doo::conf()->DB_TYPE == 'pgsql') {
			Doo::db()->reconnect('iv_id');
			$row = Doo::db()->fetchRow('select nextval(\'tb_iv_id_seq\') as uuid ');
			Doo::db()->reconnect();
		}else{
			$row = Doo::db()->fetchRow('SELECT uuid_short() as uuid ');
		}
		if ($row === false)
			throw new Exception('get uuiid failed', '99', '');
		return $row['uuid'];
	}

	/**
	 * 获取数组中的列
	 * @param  [type] $array  数组 array(array('userno' => '', 'enterpriseno' => ''))
	 * @param  [type] $column 列 array('userno')
	 * @return [type]         array(array('userno' => ''));
	 */
	public static function get_array_column($array = array(), $column = array()) {
		$new_array = array();
		$col_count = count($column);
		foreach ($array as $arr) {
			if (is_object($arr)) {
				$arr = (array) $arr;
			}

			$temp = array();
			foreach ($column as $col) {
				if (isset($arr[$col])) {
					if ($col_count == 1) {
						$temp = $arr[$col];
					} else {
						$temp[$col] = $arr[$col];
					}
				}
			}
			if ($temp) {
				$new_array[] = $temp;
			}
		}
		return $new_array;
	}

	/**
	 * 输出json
	 * @param array||int $data
	 */
	public static function json($data, $msg = '') {
		if (!is_array($data)) {
			if ($msg === '')
				$msg = isset(self::$error[$data]) ? self::$error[$data] : '';
			$status = array(
				'status' => array(
					'success' => 0,
					'error_code' => $data,
					'error_desc' => '', //self::$error[$data]
					'error_msg' => $msg
				)
			);
			die(self::json_encode($status));
		}
		if (isset($data['data'])) {
			$data = $data['data'];
		}
		$data = array_merge(array('data' => $data), array('status' => array('success' => 1)));
		die(self::json_encode($data));
	}

	/**
	 * 生成json,utf8转成中文
	 * @param array $data
	 */
	public static function json_encode($data = array()) {
		if(!empty($data) && is_array($data)){
			return json_encode($data,JSON_UNESCAPED_UNICODE);
		}else{
			return '';
		}
	}

	/**
	 * json解析
	 * @param json $data
	 */
	public static function json_decode($data,$param=true) {
        if(empty($data)) return '';
        if(is_array($data)) return $data;

		return json_decode($data,$param);
	}

	/**
	 * @name 获取URL参数,支持POST,GET等
	 * @param type $key
	 * @param type $defValue
	 */
	public static function get($key, $defValue = '') {
		return isset($_REQUEST[$key]) ? $_REQUEST[$key] : $defValue;
	}

//	/**
//	 * 判断是否频繁请求
//	 * @params $funcname 请求方法名
//	 * @params $time 两次请求的时间间隔
//	 * @params $iswait 是否等待
//	 * @params $waittime	等待时间
//	 * General 20140818
//	 */
//	public static function request_wait($funcname, $userno = 0, $interval = 3, $iswait = true, $waittime = 3) {
//		$nowtime = time();
//		if (intval($interval) <= 0) {
//			$interval = 3;
//		}
//		if (intval($waittime) <= 0) {
//			$waittime = 3;
//		}
//		$bool = false;
//		if (!isset(Userself::session()->requesttimelimit)) {
//			Userself::session()->requesttimelimit = array($funcname . '_' . $userno => $nowtime);
//		} else {
//			if (!isset(Userself::session()->requesttimelimit[$funcname . '_' . $userno])) {
//				Userself::session()->requesttimelimit[$funcname . '_' . $userno] = $nowtime;
//			} else {
//				if (!empty(Userself::session()->requesttimelimit[$funcname . '_' . $userno])) {
//					if (($nowtime - Userself::session()->requesttimelimit[$funcname . '_' . $userno]) < $interval) {
//						$bool = true;
//					} else {
//						Userself::session()->requesttimelimit[$funcname . '_' . $userno] = $nowtime;
//					}
//				}
//			}
//		}
//		if ($iswait && $bool) {
//			sleep($waittime);
//		}
//		return $bool;
//	}

	/**
	 * 去掉HTML内容，保留文本
	 */
	public static function no_HTML($content) {
		$content = htmlspecialchars_decode($content);
		$content = preg_replace("/<a[^>]*>/i", '', $content);
		$content = preg_replace("/<\/a>/i", '', $content);
		$content = preg_replace("/<div[^>]*>/i", '', $content);
		$content = preg_replace("/<\/div>/i", '', $content);
		$content = preg_replace("/<font[^>]*>/i", '', $content);
		$content = preg_replace("/<\/font>/i", '', $content);
		$content = preg_replace("/<p[^>]*>/i", '', $content);
		$content = preg_replace("/<P[^>]*>/i", '', $content);
		$content = preg_replace("/<\/p>/i", '', $content);
		$content = preg_replace("/<\/P>/i", '', $content);
		$content = preg_replace("/<span[^>]*>/i", '', $content);
		$content = preg_replace("/<\/span>/i", '', $content);
		$content = preg_replace("/<\?xml[^>]*>/i", '', $content);
		$content = preg_replace("/<\/\?xml>/i", '', $content);
		$content = preg_replace("/<o:p[^>]*>/i", '', $content);
		$content = preg_replace("/<\/o:p>/i", '', $content);
		$content = preg_replace("/<u[^>]*>/i", '', $content);
		$content = preg_replace("/<\/u>/i", '', $content);
		$content = preg_replace("/<b[^>]*>/i", '', $content);
		$content = preg_replace("/<\/b>/i", '', $content);
		$content = preg_replace("/<meta[^>]*>/i", '', $content);
		$content = preg_replace("/<\/meta>/i", '', $content);
		$content = preg_replace("/<!--[^>]*-->/i", '', $content); //注释内容
		$content = preg_replace("/<p[^>]*-->/i", '', $content); //注释内容
		$content = preg_replace("/style=.+?['|\"]/i", '', $content); //去除样式
		$content = preg_replace("/class=.+?['|\"]/i", '', $content); //去除样式
		$content = preg_replace("/id=.+?['|\"]/i", '', $content); //去除样式
		$content = preg_replace("/lang=.+?['|\"]/i", '', $content); //去除样式
		$content = preg_replace("/width=.+?['|\"]/i", '', $content); //去除样式
		$content = preg_replace("/height=.+?['|\"]/i", '', $content); //去除样式
		$content = preg_replace("/border=.+?['|\"]/i", '', $content); //去除样式
		$content = preg_replace("/face=.+?['|\"]/i", '', $content); //去除样式
		$content = preg_replace("/face=.+?['|\"]/", '', $content);
		$content = preg_replace("/face=.+?['|\"]/", '', $content);
		$content = str_replace("&nbsp;", "", $content);
		return $content;
	}

	/**
	 * 检测手机号码
	 */
	public static function check_mobile($mobilephone) {
		if (self::check_telcom_mobile($mobilephone)) {
			return true;
		}
		if (self::check_unicom_mobile($mobilephone)) {
			return true;
		}
		if (self::check_china_mobile($mobilephone)) {
			return true;
		}
		if (self::check_virtual_mobile($mobilephone)) {
			return true;
		}
		return false;
	}

	/*
	 * 移动的号段：134(0-8)、135、136、137、138、139、147（预计用于TD上网卡）、150、151、152、157（TD专用）、158、159、187（未启用）、188（TD专用）、183
	  联通的号段：130、131、132、155、156（世界风专用）、185（未启用）、186（3g）
	  电信的号段：133、153、180（未启用）、189,181
	 */

	//是否为电信手机
	public static function check_telcom_mobile($mobilephone) {
		$mobilephone = trim($mobilephone);
		if (preg_match("/^13[3]{1}[0-9]{8}$|15[3]{1}[0-9]{8}$|18[0139]{1}[0-9]{8}$/", $mobilephone)) {
			return $mobilephone;
		} else {
			return false;
		}
	}

	//联通手机
	public static function check_unicom_mobile($mobilephone) {
		$mobilephone = trim($mobilephone);
		if (preg_match("/^13[0-2]{1}[0-9]{8}$|15[56]{1}[0-9]{8}$|18[256]{1}[0-9]{8}$/", $mobilephone)) {
			return $mobilephone;
		} else {
			return false;
		}
	}

	//移动
	public static function check_china_mobile($mobilephone) {
		$mobilephone = trim($mobilephone);
		if (preg_match("/^13[4-9]{1}[0-9]{8}$|14[7]{1}[0-9]{8}$|15[012789]{1}[0-9]{8}$|18[478]{1}[0-9]{8}$/", $mobilephone)) {
			return $mobilephone;
		} else {
			return false;
		}
	}

	//虚拟运营商
	public static function check_virtual_mobile($mobilephone) {
		$mobilephone = trim($mobilephone);
		if (preg_match("/^17[0-9]{1}[0-9]{8}$/", $mobilephone)) {
			return $mobilephone;
		} else {
			return false;
		}
	}

	/*
	 * 某个字符串是否在以某个分隔的字符串里面
	 * @param string $find
	 * @param string $str
	 * @param string $delimiter 分隔符默认为逗号
	 * @return boolean
	 */

	public static function is_in_str($find, $str, $delimiter = ',') {
		if ($find == '') {
			return false;
		} else {
			if (strpos($find, $delimiter) == -1) {
				return false;
			} else {
				$array = explode($delimiter, $find); //var_dump($str);
				$k = array_search($str, $array);
				if ($k === false) {
					return false;
				} else {
					return true;
				}
			}
		}
	}

	/**
	 * 检查表中是否存在指定字段
	 * @param $table 表名
	 * @param $field 字段
	 * General.20150408
	 */
	public static function isset_table_field($table = '', $field = '') {
		if(empty($table) || empty($field)){
			return false;
		}
		$fields = self::get_table_items($table, 'fields');
		if(in_array($field, $fields)){
			return true;
		}
		return false;
	}

	/**
	 * 获取指定表的项目
	 * @param $table 表名
	 * @param $item 项目
	 * General.20150312
	 */
	public static function get_table_items($table = '', $item = 'pk') {
		if(empty($table)){
			return false;
		}
		Doo::loadModel($table);
		$model = new $table;
		switch($item){
			case 'pk':
			case 'primarykey':
				$item = '_primarykey';
				break;
			case 'field':
			case 'fields':
				$item = '_fields';
				break;
		}
		return $model->$item;
	}

	/**
	 * 获取指定表的详细信息
	 * @param $table 表名
	 * @param $opt 包括查询条件(条件里包含entid则$entid无需传入)及需更新/插入的字段$opt['fields']/除插入外必须存在$opt['param']
	 * @param $entid 企业ID（大于0代表需结合企业号为0的情况查询）
	 * @param $method 方法 get/find/count/del/update/insert
	 * General.20140505
	 */
	public static function do_table($prm_base, $table, $opt = array(), $entid = 0, $method = 'get', $setkeydata = true) {
		if (!isset($table) || empty($table) || empty($opt)) {
			return false;
		}
		$method = strtolower((string) $method); //指定为小写的字符串
		//General.20140902 一定要使用传参形式，不能直接拼接sql语句
		if ($method != 'insert' && (!isset($opt['param']) || empty($opt['param']))) {
			if(strpos(strtolower($opt['select']), 'MAX(') === false){
				return false;
			}
		}
		//General.20160127 将小写表名转换为驼峰
		$tbarr = explode('_', $table);
		$table = '';
		if(is_array($tbarr) && !empty($tbarr)){
			foreach($tbarr as $v){
				$table .= ucfirst($v);
			}
		}
		unset($tbarr);
		if(empty($table)){
			return false;
		}
		Doo::loadModel($table);
		$model = new $table;
		$opt['asArray'] = true;

		if (isset($opt['where']) && !empty($opt['where'])) {
			//若$entid大于0，则需把企业号为0即公共数据也列为查询条件
			if($entid > 0){
				$opt['where'] .= ' AND (entid = ? OR entid = 0)';
				$opt['param'][] = $entid;
			}
			//General.20150408 若没有指定记录状态，则默认为正常状态
			if (isset($opt['where']) && !empty($opt['where']) && strpos($opt['where'], ' state') === false) {
				$opt['where'] .= ' AND state = ?';
				$opt['param'][] = EnumClass::STATE_TYPE_NORMAL;
			}
		}

		//General.20140801 更新或插入时的处理 BEGIN
		if ($method == 'update' || $method == 'insert') {
			switch (strtolower($table)) {
				case 'wfworkflow':
				case 'wfnode':
					$fields['uptime'] = self::get_datetime();
					break;
			}

			//需插入或更新的字段
			if (isset($opt['fields']) && !empty($opt['fields']) && is_array($opt['fields'])) {
				$fields = $opt['fields'];
				unset($opt['fields']);
				//非批量操作
				if(!isset($fields[0])){
					// 在开发环境下输出
					//if (Doo::conf()->APP_MODE == 'dev'){
					//	$diff = array_diff(array_keys($fields),$model->_fields);
					//	if (!empty($diff)){
					//		echo 'Field not used found in '.$model->_table.": ".implode(',',$diff);
					//		if (function_exists('xdebug_get_function_stack')){
					//			print_r(xdebug_get_function_stack());
					//		}
					//	}
					//}
					foreach ($fields as $key => $v) {
						$model->$key = $v;
					}
				}
			} else {
				return false;
			}
		}
		//General.20140801 更新或插入时的处理 END
		//General.20140801 查找时的处理 BEGIN
		else if ($method == 'get' || $method == 'find' || $method == 'count') {

			if (strtolower($table) == 'wfstepaction')
			{//对操作类型表的处理,
				if ($entid > 0) {
					$stepopt['where'] = 'entid = ?';
					$stepopt['param'] = array($entid);
					$list = $model->getOne($stepopt);
					if (!$list) {
						$stepopt['where'] = 'entid = ? AND opstate = ?';
						$stepopt['param'] = array(0, EnumClass::STATE_TYPE_NORMAL);
						$stepopt['asArray'] = true;
						$stepinfo = $model->find($stepopt);
						if ($stepinfo) {
							foreach ($stepinfo as $val) {
								$model->pkid = self::builder_primary_id();
								$model->stepactionid = $val['stepactionid'];
								$model->buttontext = $val['buttontext'];
								$model->entid = $entid;
								$model->upuserid = $val['upuserid'];
								$model->opstate = $val['opstate'];
								$model->isapproval = $val['isapproval'];
								$model->fileaddress = $val['fileaddress'];
								$model->isfilechange = $val['isfilechange'];
								$model->insert();
							}
						}
					}
					if (isset($opt['where']) && strpos($opt['where'], 'entid') === false) {
						$opt['where'] .= ' AND (entid = ?)';
						$opt['param'][] = $entid;
					}
				}
			}

		}

		//General.20140801 查找时的处理 END
		$info = array();
		switch ($method) {
			case 'update':
				//General.20150417 所有记录在有字段修改的情况下都修改更新时间
				if(empty($fields['uptime'])){
					$model->uptime = self::get_datetime();
				}
				//null的情况不修改字段 suson.20170105
				if(array_key_exists('uptime', $fields)){
					if(is_null($fields['uptime'])){
						$model->uptime = null;
					}
				}

				//General.20150417 若条件中存在upuserid才修改更新人、更新人信息
				if(isset($fields['upuserid'])){
					//General.20150327 更新人信息
					$model->upuserid = isset($prm_base->userid) ? $prm_base->userid : 0;

					//General.20150402 存在该字段才更新
					if(self::isset_table_field($table, 'updata')){
						$updata = array(
							'username'=>$prm_base->username,
							'posnid'=>$prm_base->posnid,
							'deptid'=>$prm_base->deptid
						);
						$model->updata = self::json_encode($updata);
					}
				}
				return $model->update($opt);
				break;
			case 'insert':
				//General.20150402 字段$opt['fields']为2维数组时，超出2维会出错
				if(isset($fields[0]) && !isset($fields[0][0])){
					//General.20150417 改为组装sql语句批量插入
                    LogClass::log_trace('do_table_db_batch 开始' . Doo::benchmark(), __LINE__,__LINE__);
					return self::db_batch($prm_base, $table, $model->_primarykey, $fields, $method);
					/*foreach($fields as $v){
						$opt['fields'] = $v;
						self::do_table($prm_base, $table, $opt, $entid, $method);//循环执行
					}
					die;*/
				}else if(!isset($fields[0])){//单条操作
					$fields = self::db_default_handle($prm_base, $table, $fields, $method, $setkeydata);
					//主键ID 为空为零时是取UUID short
					if (null === $model->{$model->_primarykey} || $model->{$model->_primarykey} == 0) {
						LogClass::log_trace('do_table_builder_primary_id 1 ' . Doo::benchmark(), __LINE__,__LINE__);
						$model->{$model->_primarykey} = self::builder_primary_id();
						LogClass::log_trace('do_table_builder_primary_id 2 ' . Doo::benchmark(), __LINE__,__LINE__);
					}
					foreach ($fields as $key => $v) {
						$model->$key = $v;
					}
				}
				LogClass::log_trace('do_table_insert0' . Doo::benchmark(), __LINE__,__LINE__);
				$info = $model->insert();
				LogClass::log_trace('do_table_insert1' . Doo::benchmark(), __LINE__,__LINE__);
				break;
			case 'get':
				$info = $model->getOne($opt);
				break;
			case 'find':
				$info = $model->find($opt);
				//print_r(Doo::db()->show_sql());die;
				break;
			case 'count':
				$info = $model->count($opt);
				break;
			case 'del':
				return $model->delete($opt);
				break;
		}
		//General.20140711 数据查询后处理
		//if ($info){
			//$info = self::db_handle_data($prm_base, $table, $info);
		//}

		return $info;
	}

	/*
	 * 默认处理字段
	 * General.20150417
	*/
	public static function db_default_handle($prm_base = '', $table = '', $fields = array(), $method = '', $setkeydata = true){
		LogClass::log_trace('db_default_handle-1 开始' . Doo::benchmark(), __LINE__,__LINE__);
		if(empty($table)){
			return false;
		}
		$defields = array();
		$useapi = true;
		/*$db = self::db_detect();
		if($db == EnumClass::DB_NAME_PERSONAL){
			$useapi = false;
		}*/
		switch($method){
			case 'insert':
				//General.20150408 写入默认值处理，表中存在字段且没有指定则写入默认值
				if(self::isset_table_field($table, 'entid') && !isset($fields['entid'])){
					$defields['entid'] = isset($prm_base->entid) ? $prm_base->entid : 0;	//企业ID
				}
				if(self::isset_table_field($table, 'userid') && !isset($fields['userid'])){
					$defields['userid'] = isset($prm_base->userid) ? $prm_base->userid : 0; //用户ID
				}
				if(self::isset_table_field($table, 'deptid') && !isset($fields['deptid'])){
					$defields['deptid'] = isset($prm_base->deptid) ? $prm_base->deptid : 0;//主部门ID
				}
				if(self::isset_table_field($table, 'posnid') && !isset($fields['posnid'])){
					$defields['posnid'] = isset($prm_base->posnid) ? $prm_base->posnid : 0;//主职位ID
				}
				if(self::isset_table_field($table, 'terminal') && !isset($fields['terminal'])){
					$defields['terminal'] = isset($prm_base->terminal) ? $prm_base->terminal : EnumClass::TERMINAL_TYPE_WEB;//终端类型 默认web终端
				}
				//General.20150603 插入时默认写入更新时间
				if(self::isset_table_field($table, 'uptime') && !isset($fields['uptime'])){
					$defields['uptime'] = self::get_datetime();
				}
				if(self::isset_table_field($table, 'keydata') && $setkeydata === true){//搜索信息
					$keydata = '';
					if(!isset($fields['keydata'])){
						if(isset($prm_base->username) && !empty($prm_base->username) && isset($prm_base->deptname) && !empty($prm_base->deptname) && isset($prm_base->posnname) && !empty($prm_base->posnname)){
							$keydata['username'] = $prm_base->username;
							$keydata['deptname'] = $prm_base->deptname;
							$keydata['posnname'] = $prm_base->posnname;
						}else if(!empty($prm_base->userid)){
							if($useapi){ //企业端调用接口查询个人信息
								$user = ApiClass::user_get_info('3.0.0.0', $prm_base, $prm_base->userid, 'profname,deptname,posnname');
								if($user['ret'] == RetClass::SUCCESS){
									$keydata['username'] = isset($user['data']['profname']) ? $user['data']['profname'] : '';
									$keydata['deptname'] = isset($user['data']['deptname']) ? $user['data']['deptname'] : '';
									$keydata['posnname'] = isset($user['data']['posnname']) ? $user['data']['posnname'] : '';
								}
							}else{ //个人端端调用接口查询个人信息

							}
						}
					}else{
						$keydata = self::json_decode($fields['keydata']);
						if(!isset($keydata['username']) && isset($prm_base->username) && !empty($prm_base->username) && !isset($keydata['deptname']) && isset($prm_base->deptname) && !empty($prm_base->deptname) && !isset($keydata['posnname']) && isset($prm_base->posnname) && !empty($prm_base->posnname)){
							$keydata['username'] = $prm_base->username;
							$keydata['deptname'] = $prm_base->deptname;
							$keydata['posnname'] = $prm_base->posnname;
						}else if(!empty($fields['userid'])){
							if($useapi){ //企业端调用接口查询个人信息
								$user = ApiClass::user_get_info('3.0.0.0', $prm_base, $fields['userid'], 'profname,deptname,posnname');
								if($user['ret'] == RetClass::SUCCESS){
									$keydata['username'] = isset($user['data']['profname']) ? $user['data']['profname'] : '';
									$keydata['deptname'] = isset($user['data']['deptname']) ? $user['data']['deptname'] : '';
									$keydata['posnname'] = isset($user['data']['posnname']) ? $user['data']['posnname'] : '';
								}
							}else{ //个人端端调用接口查询个人信息

							}
						}
					}
					//General.20150727 申请人信息
					if(isset($fields['applyuserid']) && !empty($fields['applyuserid'])){
						$keydata['applyusername'] = '';
						$keydata['applydeptname'] = '';
						$keydata['applyposnname'] = '';
						if($fields['applyuserid'] == $prm_base->userid){//如果申请人是当前用户
							$keydata['applyusername'] = $prm_base->username;
							if(isset($prm_base->roles) && !empty($prm_base->roles)){
								$roles = CommonClass::json_decode($prm_base->roles);
								foreach($roles as $k => $v){
									if(isset($fields['applydeptid']) && $v['deptid'] == $fields['applydeptid']){
										$keydata['applydeptname'] = $v['deptname'];
									}
									if(isset($fields['applyposnid']) && $v['deptid'] == $fields['applyposnid']){
										$keydata['applyposnname'] = $v['posnname'];
									}
								}
							}
						}else if($useapi){//代别人提交申请
							if($useapi){ //企业端调用接口查询个人信息
								$applyuser = ApiClass::user_get_info('3.0.0.0', $prm_base, $fields['applyuserid'], 'profname,deptname,posnname');
								if($applyuser['ret'] == RetClass::SUCCESS){
									$keydata['applyusername'] = isset($applyuser['data']['profname']) ? $applyuser['data']['profname'] : '';
									$keydata['applydeptname'] = isset($applyuser['data']['deptname']) ? $applyuser['data']['deptname'] : '';
									$keydata['applyposnname'] = isset($applyuser['data']['posnname']) ? $applyuser['data']['posnname'] : '';
								}
							}else{ //个人端端调用接口查询个人信息

							}
						}
					}
					$defields['keydata'] = self::json_encode($keydata);
				}
				if(isset($fields[0]) && !isset($fields[0][0])){//批量操作
					if(is_array($defields) && !empty($defields)){
						foreach($fields as $k=>$v){
							// $fields[$k] = array_merge($fields[$k], $defields);
							$fields[$k] = array_merge($defields, $fields[$k]);//2016-04-19 lyj
						}
					}
				}else if(!isset($fields[0])){//单条操作
					if(self::isset_table_field($table, 'state') && !isset($fields['state'])){
						$defields['state'] = EnumClass::STATE_TYPE_NORMAL;//默认正常状态
					}
					$fields = array_merge($fields, $defields);
				}
				break;
			default:
				break;
		}
		LogClass::log_trace('db_default_handle-2 结束' . Doo::benchmark(), __LINE__,__LINE__);
		return $fields;
	}
	/**
	 * [cc_format 大写字母 转 下划线+小写] add by suson 20150608
	 * @param  [type] $name [description]
	 * @return [type]       [description]
	 */
	public static function cc_format($name){
		$temp_array = array();
		$num_flag = 0;//标识当遇到第一位数字时
		for($i=0;$i<strlen($name);$i++){
			$ascii_code = ord($name[$i]);
			if($ascii_code >= 65 && $ascii_code <= 90){
				if($i == 0){
					$temp_array[] = chr($ascii_code + 32);
				}else{
					$temp_array[] = '_'.chr($ascii_code + 32);
				}
				$num_flag = 0;// 遇到大写字母，标识值重新为0 //////////////start @bos.20160506
			}else if($ascii_code >= 48 && $ascii_code <= 57){
				 if($num_flag == 0 && $i != 0){
					$temp_array[] = '_' . chr($ascii_code);
					$num_flag = 1;//标识符值为1 直至遇到大写字母
				}else{
					$temp_array[] = chr($ascii_code);
				}////////////////end
			}else{
				$temp_array[] = $name[$i];
			}
		}
		return implode('',$temp_array);
	}


	/*
	 * 批量插入方法
	 * $pk 主键字段名
	 * $fields 需要插入的字段和值，只支持3维数组，且按第一层的字段来更新
	 * General.20150417
	*/
	public static function db_batch($prm_base = '', $table = '', $pk = '', $fields = array(), $method = 'insert') {
		if($method != 'insert' || empty($table) || empty($fields) || !is_array($fields) || empty($pk)){
			return false;
		}
		$fields = self::db_default_handle($prm_base, $table, $fields, $method);
		$format_table = self::cc_format($table);
		switch($method){
			case 'insert':
				$sql = 'INSERT INTO '.$format_table;

				$sql_fields = '';
				$sql_values = '';
				$pkid_str = '';
				if(isset($fields[0]) && !isset($fields[0][0])){
					foreach($fields as $k=>$v){
						$key = '';
						$val = '';
						foreach($v as $k1=>$v1){
							$v1 = self::addslashes_str($v1);//特殊字符转义 防报错 lyj 2016-05-20
							$key .= $k1.',';
							$val .= '\''.$v1.'\',';
						}
						$key = rtrim($key,',');
						$val = rtrim($val,',');
						$sql_fields = $pk.','.$key;

						//注释以下代码 lyj 2016-05-20
						$fields[$k]['pkid'] = self::builder_primary_id();
						$sql_values .= ' ('.$fields[$k]['pkid'].','.$val.'), ';
						$pkid_str .= $fields[$k]['pkid'].',';

						// $sql_values .= ' (UUID_SHORT(),'.$val.'), ';//在sql里写UUID lyj 2016-05-20
					}
				}
				$sql_values = rtrim($sql_values,', ');
				$sql .= ' ('.$sql_fields.') VALUES '.$sql_values;
				//var_dump($sql);exit;
				$query = Doo::db()->query($sql);

				//注释以下代码 lyj 2016-05-20
				//默认插入无效状态，全部插入完成再更新为正常状态
				if(self::isset_table_field($table, 'state')){
					$state = EnumClass::STATE_TYPE_NORMAL;//默认正常状态
					$pkid_str = rtrim($pkid_str,',');
					$sql = 'UPDATE '.$format_table.' SET state = '.$state.' WHERE '.$pk.' IN ('.$pkid_str.')';
					$query = Doo::db()->query($sql);
					if($query){
						$query = $pkid_str;
					}
				}
				break;
			default:
				break;
		}
		return $query;//返回影响行数
	}

	/**
	 * [insert_batch 批量插入 lyj 2016-05-20]
	 * @param  string $prm_base [description]
	 * @param  string $table    [description]
	 * @param  string $pk       [description]
	 * @param  array  $fields   [description]
	 * @return [type]           [description]
	 */
	public static function insert_batch($prm_base = '', $table = '', $pk = '', $optArr = array()) {
		if (empty($table) || empty($pk) || empty($optArr[0]['fields'])) {
			return false;
		}

		$sql_fields = "";
		$sql_values = "";

		foreach ($optArr as $k => $v) {
			$fieldnameStr = "";
			$valueStr = "";
			foreach ($v['fields'] as $fieldname => $value) {
				$value = self::addslashes_str($value);//特殊字符转义 防报错 lyj 2016-05-20
				$fieldnameStr .= "`{$fieldname}`,";
				$valueStr .= "'{$value}',";
			}
			$fieldnameStr = rtrim($fieldnameStr, ',');
			$valueStr = rtrim($valueStr, ',');
			$sql_fields = "`{$pk}`,$fieldnameStr";
			$sql_values .= "(UUID_SHORT(),{$valueStr}),";//在sql里写UUID lyj 2016-05-20
		}

		$sql_values = rtrim($sql_values, ',');
		$sql = "INSERT INTO {$table} ({$sql_fields}) VALUES {$sql_values}";
		// return $sql;

		$query = Doo::db()->query($sql);
		return $query;
	}

	/**
	 * [update_batch 批量更新 lyj 2016-05-20]
	 * @param  string $prm_base [description]
	 * @param  string $table    [description]
	 * @param  string $pk       [description]
	 * @param  array  $optArr   [description]
	 * @return [type]           [description]
	 */
	public static function update_batch($prm_base = '', $table = '', $pk = '', $optArr = array()) {
		if (empty($table) || empty($pk) || empty($optArr[0]['id']) || empty($optArr[0]['fields'])) {
			return false;
		}

		// $table = self::cc_format($table);
		$idArr = array();
		$fieldArr = array();
		foreach ($optArr as $k => $v) {
			if (empty($v['id'])) {
				continue;
			}
			$v['id'] = self::addslashes_str($v['id']);
			$idArr[] = "'{$v['id']}'";
			foreach ($v['fields'] as $fieldname => $value) {
				$value = self::addslashes_str($value);
				$fieldArr[$fieldname][$v['id']] = $value;
			}
		}

		$setArr = array();
		foreach ($fieldArr as $fieldname => $list) {
			$str = "";
			foreach ($list as $id => $value) {
				$str .= "WHEN `{$pk}` = '{$id}' THEN '{$value}' ";
			}
			$setArr[] = "`{$fieldname}` = (CASE {$str} END)";
		}
		$setStr = implode(', ', $setArr);

		$whereStr = "WHERE `{$pk}` IN (".implode(',', $idArr).")";
		$sql = "UPDATE {$table} SET {$setStr} {$whereStr}";
		// return $sql;

		$query = Doo::db()->query($sql);
		return $query;
	}

	/*
	 * 各表的数据处理
	 * General.20140711
	 */
	public static function db_handle_data($prm_base = 0, $table = '', $data = '') {
		if (empty($table) || empty($data)) {
			return $data;
		}
		switch (strtolower($table)) {
			case 'wfcase':
			case 'wfnode':
			case 'wfstep':
			case 'wfcasenode':
			case 'wfworkflow':
			case 'wfversionworkflow':
			case 'wfversionnode':
			case 'wfcaseuser':
			case 'wfcaserecord':
			case 'wfcasetransmit':
			case 'wfstepaction':
				Doo::loadClass('WorkflowClass');
				$data = WorkflowClass::handle_workflow_data($prm_base, $table, $data);
				break;
		}
		return $data;
	}

	/**
	 * 获取日期时间格式
	 * General.20151009
	 * @param type $dateTime 指定日期时间
	 * @param type $type 日期时间类型常量
	 * @param type $formatType 日期时间格式类型常量
	 * @return type 具体的日期时间
	 * @example:
	 * 1)当前日期时间的后一小时 self::getDateTime(1,EnumClass::DATETIME_TYPE_DIFF_HOUR)
	 * 2)指定日期的后一天 self::getDateTime('2014-11-22 +1',EnumClass::DATETIME_TYPE_DIFF_DAY,EnumClass::DATETIME_FORMAT_ZERO_HOUR)
	 */
	public static function get_datetime($dateTime = '', $type = EnumClass::DATETIME_TYPE_NONE,
										$formatType = EnumClass::DATETIME_FORMAT_ZERO_NONE) {
		switch ($formatType) {
			case EnumClass::DATETIME_FORMAT_ZERO_SECOND:
				$format = 'Y-m-d H:i:0';
				break;
			case EnumClass::DATETIME_FORMAT_ZERO_MINUTE:
				$format = 'Y-m-d H:0:0';
				break;
			case EnumClass::DATETIME_FORMAT_ZERO_HOUR:
			case EnumClass::DATETIME_FORMAT_DAY_FIRST:
				$format = 'Y-m-d 0:0:0';
				break;
			case EnumClass::DATETIME_FORMAT_DATE_ONLY:
				$format = 'Y-m-d';
				break;
			case EnumClass::DATETIME_FORMAT_MONTH_DAY_1:
			case EnumClass::DATETIME_FORMAT_FIRST_DAY:
				$format = 'Y-m-1 0:0:0';
				break;
			case EnumClass::DATETIME_FORMAT_DAY_END:
				$format = 'Y-m-d 23:59:59';
				break;
			case EnumClass::DATETIME_FORMAT_NO_SECOND:
				$format = 'Y-m-d H:i';
				break;
			case EnumClass::DATETIME_FORMAT_YEAR_FIRST_DAY:
				$format = 'Y-01-01 00:00:00';
				break;
			case EnumClass::DATETIME_FORMAT_YEAR_LAST_DAY:
				$format = 'Y-12-31 23:59:59';
				break;
			case EnumClass::DATETIME_FORMAT_YEAR_MONTH:
				$format = 'Y-m';
				break;
			default:
				$format = 'Y-m-d H:i:s';
				break;
		}
		switch ($type) {
			case EnumClass::DATETIME_TYPE_NONE:
				if ($dateTime === '') {
					return date($format);
				}elseif (is_integer($dateTime) === true) {
					$time = $dateTime;
				}else {
					$time = strtotime($dateTime);
				}
				break;
			case EnumClass::DATETIME_TYPE_DATE:
				$time = strtotime($dateTime);
				break;
			case EnumClass::DATETIME_TYPE_TIMESTAMP:
				$time = $dateTime;
				break;
			case EnumClass::DATETIME_TYPE_DIFF_SECOND:
				$time = strtotime($dateTime . ' second');
				break;
			case EnumClass::DATETIME_TYPE_DIFF_MINUTE:
				$time = strtotime($dateTime . ' minute');
				break;
			case EnumClass::DATETIME_TYPE_DIFF_HOUR:
				$time = strtotime($dateTime . ' hour');
				break;
			case EnumClass::DATETIME_TYPE_DIFF_DAY:
				$time = strtotime($dateTime . ' day');
				break;
			default:
				$time = strtotime('now');
				break;
		}
		$date = date($format,$time);
		//需求上，非正确格式的日期都显示为空 suson.20160708
		return strpos($date, '1970') !== false  || strpos($date, '0001') !== false ? '' : $date;
	}

	/**
	 * 获取时间戳
	 * @param type $dateTime
	 * @param type $type
	 * @return type
	 */
	public static function get_timestamp($dateTime = '', $type = EnumClass::DATETIME_TYPE_NONE) {
		switch ($type) {
			case EnumClass::DATETIME_TYPE_NONE:
				if ($dateTime === '') {
					$time = strtotime('now');
				}elseif (is_integer($dateTime) === true) {
					$time = $dateTime;
				}else {
					$time = strtotime($dateTime);
				}
				break;
			case EnumClass::DATETIME_TYPE_DATE:
				$time = strtotime($dateTime);
				break;
			case EnumClass::DATETIME_TYPE_TIMESTAMP:
				$time = $dateTime;
				break;
			case EnumClass::DATETIME_TYPE_DIFF_SECOND:
				$time = strtotime($dateTime . ' second');
				break;
			case EnumClass::DATETIME_TYPE_DIFF_MINUTE:
				$time = strtotime($dateTime . ' minute');
				break;
			case EnumClass::DATETIME_TYPE_DIFF_HOUR:
				$time = strtotime($dateTime . ' hour');
				break;
			case EnumClass::DATETIME_TYPE_DIFF_DAY:
				$time = strtotime($dateTime . ' day');
				break;
			default:
				$time = strtotime('now');
				break;
		}
		return (int)$time;
	}

	/*
	 * 获取工作日
	 * $startdate 开始日期
	 * $num 后多少日
	 * General.20140708
	 */
	public static function get_work_days($startdate, $num = 1) {
		if(empty($startdate)){
			return ;
		}
		$time = $startdate;
		$startdate = date('w', strtotime($startdate));
		$enddate = $startdate + $num;
		//如果结束日是周六日
		if ($enddate >= 6) {
			$num = $num + 2;
		}
		$enddate = date("Y-m-d H:i:s", strtotime($time) + 86400 * $num);
		return $enddate;
	}

	/**
	 * 个人和企业底部版权字段
	 * 写成公共字段方便修改
	 * @return string
	 */
	public static function footer_copr() {
		$footer = "©{date('Y')} oa.cn All rights reserved.";
		return $footer;
	}

	/**
	 * 获取用户头像
	 * General.20150126
	 * $userno 指定用户ID
	 * $size 尺寸 1=小/2=中/3=大
	 */
	public static function get_avatar($prm_base, $userno = '', $size = 1) {
		Doo::loadClass('Userself');
		if(empty($userno)){
			$userno = $prm_base->uno;
		}
		if(defined('OPEN_FILE_SERVER')){
			$basepath = Userself::gen_save_filepath($prm_base->eno,$userno,3);
			$url = $basepath.'thumb' . $size . '_' . $userno . '.jpg?rand=' . time();
		}else{
			$url = 'uploads/userfiles/logo/' . $userno . '/thumb' . $size . '_' . $userno . '.jpg?rand=' . time();
		}

		return $url;
	}

	/**
	 * 获取模块名(等企业定制语言包完善后改用语言包)
	 * @param  [type] $module_type [description]
	 * @return [type]              [description]
	 */
	/*public static function get_module_info($prm_base, $module_type) {
		$module_info = array(
			'module_name' => '',
			'module_icon' => '',
		);
		$ret = ApiClass::enterprise_get_enterprise_modules('3.0.0.1', $prm_base);

		$modules = array();
		if (RetClass::SUCCESS == $ret['ret']) {
			foreach ($ret['data'] as $value) {
				$modules[$value->modulecode] = $value;
			}
		}
		switch ($module_type) {
			case EnumClass::MODULE_NAME_NEWS:
				$module_code = 'News';
				$module_info['module_name'] = '公告';
				$module_info['module_icon'] = 'i_app16';
				break;
			case EnumClass::MODULE_NAME_PROCESS:
				$module_code = 'Process';
				$module_info['module_name'] = '申请审批';
				$module_info['module_icon'] = 'i_app13';
				break;
			case EnumClass::MODULE_NAME_TASK:
				$module_code = 'Task';
				$module_info['module_name'] = '任务';
				$module_info['module_icon'] = 'i_app03';
				break;
			case EnumClass::MODULE_NAME_MEETING:
				$module_code = 'AdminMeeting';
				$module_info['module_name'] = '开会';
				$module_info['module_icon'] = 'i_app15';
				break;
			case EnumClass::MODULE_NAME_VISIT:
				$module_code = 'Visit';
				$module_info['module_name'] = '服务拜访';
				$module_info['module_icon'] = 'i_app37';
				break;
			case EnumClass::MODULE_NAME_ACTIVITY:
				$module_code = 'Activity';
				$module_info['module_name'] = '活动';
				$module_info['module_icon'] = 'i_app24';
				break;
			case EnumClass::MODULE_NAME_APPROVE:
				$module_code = 'Approve';
				$module_info['module_name'] = '行政审批';
				$module_info['module_icon'] = 'i_app51';
				break;
			case EnumClass::MODULE_NAME_EFAX:
				$module_code = 'efax';
				$module_info['module_name'] = '网络传真';
				$module_info['module_icon'] = 'i_app25';
				break;
			case EnumClass::MODULE_NAME_SYSMSG:
				$module_code = '';
				$module_info['module_name'] = '系统消息';
				$module_info['module_icon'] = 'i_app08';
				break;
			case EnumClass::MODULE_NAME_PLAN:
				$module_code = 'Plan';
				$module_info['module_name'] = '计划';
				$module_info['module_icon'] = 'i_app21';
				break;
			case EnumClass::MODULE_NAME_REPORT:
				$module_code = 'Report';
				$module_info['module_name'] = '汇报';
				$module_info['module_icon'] = 'i_app20';
				break;
			case EnumClass::MODULE_NAME_SYS:
				$module_code = 'AdminSystem';
				$module_info['module_name'] = '系统管理';
				$module_info['module_icon'] = 'i_app04';
				break;
			case EnumClass::MODULE_NAME_PMS:
				$module_code = 'Pms';
				$module_info['module_name'] = '内部邮件';
				$module_info['module_icon'] = 'i_app28';
				break;
			case EnumClass::MODULE_NAME_WEIXIN:
				$module_code = '';
				$module_info['module_name'] = '微信';
				$module_info['module_icon'] = '';
				break;
			case EnumClass::MODULE_NAME_WAGES:
				$module_code = 'Wages';
				$module_info['module_name'] = '工资';
				$module_info['module_icon'] = 'i_app53';
				break;
			case EnumClass::MODULE_NAME_TRIAL:
				$module_code = '';
				$module_info['module_name'] = '试用';
				$module_info['module_icon'] = '';
				break;
			case EnumClass::MODULE_NAME_FAQ:
				$module_code = '';
				$module_info['module_name'] = '常见问题';
				$module_info['module_icon'] = '';
				break;
		}

		if (isset($modules[$module_code])) {
			$module_info['module_name'] = $modules[$module_code]->modulename;
			$module_info['module_icon'] = $modules[$module_code]->moduleicon;
		}
		return $module_info;
	}*/

	/**
	 *  清空缓冲区
	 *  General.20150310
	 */
	public static function ob_clean() {
		/* if (ob_get_length())
			ob_end_clean();//清空（擦除）缓冲区并关闭输出缓冲
		if (function_exists('ob_gzhandler')) {
			ob_start('ob_gzhandler');
		} else {
			ob_start();
		} */
	}

	/**
	 * CURL  get_remote_data 从跨域获取数据的方法，类文件内部调用
	 * @param  [string] $url   [PAI地址]
	 * @param  [array] $post_data [提交的数据]
	*/
	public static function get_remote_data($url, $post_data = '', $header = '', $timeout=0, $file=0){

		//curl 复用 suson.20170323
		//if(isset(Doo::conf()->CURL_INIT)){
		//	$ch = Doo::conf()->CURL_INIT;
		//}else{
		$ch = curl_init();
		//	Doo::conf()->CURL_INIT = $ch;
		//}
		if($timeout){
			curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		}
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_URL, $url);
		if( is_array($post_data) && empty($file)){ // 非文件上传时，最好序列号，文件上传时用数组 suson.20160422
			$post_data = http_build_query($post_data);
		}

		if(!empty($file)){
			//文件上传需要 suson.20160422
			curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
			if (class_exists('\CURLFile')) {
				curl_setopt($ch, CURLOPT_SAFE_UPLOAD, true);
			} else {
				if (defined('CURLOPT_SAFE_UPLOAD')) {
					curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false);
				}
			}
		}

		if( !empty($post_data))
		{
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		}


		if(isset($header{0})){
			curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		}
		if(stripos($url, 'https') !== false){
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 对认证证书来源的检查
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // 从证书中检查SSL加密算法是否存在
		}
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec($ch);
		//if (curl_errno($ch)) {
		//	$result = false; //2017-04-05 lyj
		//}
		curl_close($ch);
		return $result;
	}

	/**
	 * 获取当前站点的渠道
	 * @return string
	 */
	public static function get_channel() {
		$url = $_SERVER['HTTP_HOST'];
		$url = explode(':', $url);
		$url = $url[0];
		$temp = explode('.', $url);
		$len = count($temp);
		if($len > 2)
			$url = $temp[$len - 2] . '.' . $temp[$len - 1];

		return $url;
	}

	/**
	 * 更新state
	 * @param  [type] $version  [description]
	 * @param  [type] $tb_name  [description]
	 * @param  [type] $pk       array(主键key => value, ...)
	 * @param  [type] $state    [description]
	 * @return [type]           [description]
	 */
	public static function update_state($prm_base, $tb_name, $pk, $state) {
		$opt = array();
		foreach ($pk as $key => $value) {
			$opt['where'][] = "{$key} = ?";
			$opt['param'][] = $value;
		}
		$opt['where'][] = 'state = ?';
		$opt['param'][] = EnumClass::STATE_TYPE_INVALID;

		$opt['where'] = implode(' AND ', $opt['where']);
		$opt['fields']['state'] = $state;
		$result = self::do_table($prm_base, $tb_name, $opt, 0, 'update');

		return $result;
	}

	/**
	 * oa.cn平台数据请求
	 * @param  [type] $version  [description]
	 * @param  [type] $prm_base [description]
	 * @param  [type] $apiurl   [description]
	 * @param  [type] $params   [description]
	 * @return [type]           [description]
	 */
	public static function get_oa_platform_data($version, $prm_base, $apiurl, $params, $header='', $timeout=0, $file=0) {
		$params['version'] = $version;
		//将对象转换成json
		// $prm_base = json_encode($prm_base);
		$params['prm_base'] = $prm_base;
		$reqdata	= self::encode($params, EnumClass::PAGE_RET_MSG_TYPE_MOBILE);
		//echo($apiurl.'?reqdata='.$reqdata);exit;
		// $reqdata	= self::encode($params, EnumClass::PAGE_RET_MSG_TYPE_PAGE);
		$result		= self::get_remote_data($apiurl, array('reqdata' => $reqdata),$header, $timeout, $file);
		$data		= self::decode($result, EnumClass::PAGE_RET_MSG_TYPE_MOBILE);
		$logs = array();
		$logs['url'] = $apiurl;
		$logs['params'] = $params;
		$logs['result'] = $result;
		$logs['result_decode'] = $data;
		$logs['reqdata'] = $reqdata;
		// LogClass::log_trace1('get_oa_platform_data',[$logs,debug_backtrace()],__FILE__,__LINE__);
		LogClass::log_trace1('get_oa_platform_data',$logs,__FILE__,__LINE__);
		//General.20160317 如返回的不是指定格式，则返回失败
		if(empty($data) || !isset($data['ret']) || empty($data['ret'])){
			Doo::loadClass('BaseClass');
			return BaseClass::ret(RetClass::ERROR,'','','','请求数据失败！');
		}
		return $data;
	}

	/**
	 * 长ID数字、字母ID互转，临时
	 *
	 * @param mixed   $in      String or long input to translate
	 * @param boolean $to_num  Reverses translation when true
	 * @param mixed   $pad_up  Number or boolean padds the result up to a specified length
	 * @param string  $passKey Supplying a password makes it harder to calculate the original ID
	 *
	 * @return mixed string or long
	 */
	public static function alphaID($in, $to_num = false, $pad_up = false){
		$index = "abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";

		$base  = strlen($index);

		if ($to_num) {
			// Digital number  <<--  alphabet letter code
			$in  = strrev($in);
			$out = 0;
			$len = strlen($in) - 1;
			for ($t = 0; $t <= $len; $t++) {
				$bcpow = bcpow($base, $len - $t);
				$out   = $out + strpos($index, substr($in, $t, 1)) * $bcpow;
			}

			if (is_numeric($pad_up)) {
				$pad_up--;
				if ($pad_up > 0) {
					$out -= pow($base, $pad_up);
				}
			}
			$out = sprintf('%F', $out);
			$out = substr($out, 0, strpos($out, '.'));
		} else {
			// Digital number  -->>  alphabet letter code
			if (is_numeric($pad_up)) {
				$pad_up--;
				if ($pad_up > 0) {
					$in += pow($base, $pad_up);
				}
			}

			$out = "";
			for ($t = floor(log($in, $base)); $t >= 0; $t--) {
				$bcp = bcpow($base, $t);
				$a   = floor($in / $bcp) % $base;
				$out = $out . substr($index, $a, 1);
				$in  = $in - ($a * $bcp);
			}
			$out = strrev($out); // reverse
		}

	  return $out;
	}


	//长ID数字转换为字母ID
	public static function changeBigInt($raw, $ary = 62){
		//变量初始化
		bcscale(0);           //设置没有小数位。
		$result = "";         //结果
		$variable = 1;        //临时变量
		$residue = 1;         //余数
		while($raw != "0"){
			$variable = bcdiv($raw,$ary);
			$residue = bcmod($raw,$ary);
			$result = self::$key1[$residue] . $result;
			$raw = $variable;
		}
		return $result;
	}

	//字母ID转换为长ID数字
	public static function revertBigInt($value, $ary = 62){
		//变量初始化
		bcscale(0);         //设置没有小数位。
		$result = "";
		$median = strlen($value);
		$character = "";
		for ($i=1;$i<=$median;$i++){
			$character = $value[$i-1];
			$result = bcadd(bcmul(strpos(self::INTSWITCH_KEY,$character),bcpow($ary,$median - $i)),$result);
		}
		return $result;
	}

	//二维数组选择某一键名去除重复项
	public static function assoc_unique2D($arr, $key)
	{
		$tmp_arr = array();
		foreach($arr as $k => $v){
			if(in_array($v[$key], $tmp_arr)){//搜索$v[$key]是否在$tmp_arr数组中存在，若存在返回true
			   unset($arr[$k]);
			}
			else {
				$tmp_arr[] = $v[$key];
			}
		}
		sort($arr); //sort函数对数组进行排序
		return $arr;
	}

	public static function resolveUsers($users){
		if($users){
			$users = json_decode($users,true);
			if(!is_array($users) || !isset($users['user']) || !isset($users['user']['0'])  || !isset($users['user']['0']['u'])){
				return array();
			}
			return explode(',', $users['user']['0']['u']);
		}
		return array();
	}

	public static function get_split_string($str,$split,$index){
		if ($index == -1) {
			//strrpos($str,)
		}
	}

	public static function array_change_key($array_org,$key){
		$ret = array();
		foreach((array)$array_org as $v1){
			$ret[$v1[$key]] = $v1;
		}
		return $ret;
	}

	public static function check_hashkey($key){
		$ret['ret'] = RetClass::ERROR;
		$ret['attr']['content'] = CommonClass::txt('TIPS_HASHKEY_ERROR');
		if(!empty($key)){
			$hashkey_val = CommonClass::get_hashkey_val($key);
			// var_dump($hashkey_val);
			if(!empty($hashkey_val) && !empty($hashkey_val['val']) && $hashkey_val['val'] == RetClass::SUCCESS){
				return $ret;
			}else{
				return array('ret' => RetClass::SUCCESS);
			}
		}else{
			return $ret;
		}
		// self::set_hashkey_val($version='3.0.0.0',$prm_base,$key,'hasadd');
		//return array('ret' => RetClass::SUCCESS);
	}

	public static function get_hashkey_val($key){
		if(empty($key)){
			return '';
		}
		return CommonClass::cache(Doo::conf()->CACHE_TYPE_SYS)->get($key);
	}

	public static function set_hashkey_val($key,$val){
		if(empty($key)){
			return false;
		}
		return CommonClass::cache(Doo::conf()->CACHE_TYPE_SYS)->set($key,$val,20*60); //20分钟失效
	}

	//各种上传文件存放路径
	public static function gen_save_filepath($entid, $userid, $type = 1) {
		$fullpath = '';
		$y = date('Y');
		$m = date('m');
		$d = date('j');
		$e_entid = substr($entid, -3);  //末三位
		$e_userid = substr($userid, -2); //末两位
		switch ($type) {
			case 1 : //普通附件目录
				$tempFilePath = "$e_entid/$entid/$e_userid/$userid/" . $y . $m . "/" . $d . "/";
				$fullpath = 'userfiles/' . $tempFilePath;
				break;
			case 2 : //临时目录
				$fullpath = 'userfiles/logo/temp/' . $y . '/' . $m . '/' . $d . '/';
				break;
			case 3 : //个人头像目录
				$fullpath = "userfiles/$e_entid/$entid/$e_userid/$userid/logo/";
				break;
			case 4 : //档案头像目录
				$fullpath = "userfiles/$e_entid/$entid/$userid/eelogo/";
				break;
			case 5 : //上传导入模板目录
				$fullpath = "userfiles/exceltemp/$entid/$userid/". $y . $m . "/" . $d . "/";
				break;
			case 6 : //正文，表单打印生成的文件临时存放目录
				$fullpath = "userfiles/content_temp/$entid/$userid/". $y . $m . "/" . $d . "/";
				break;
			case 7 : //智媒体，文件保存路径
				$fullpath = "userfiles/zimeiti/$entid/$userid/" . $y . $m . "/" . $d . "/";
				break;
		}

		return $fullpath;
	}

	/**
	 * @param string $pass
	 * @param string $salt
	 * @return string
	 */
	public static function encryptPassword($pass, $salt) {
		return md5(md5($pass).$salt);
	}

	/*
	 * 数据库重链接
	 * General.20151223
	 */
	public static function db_reconnect($db = EnumClass::DB_NAME_ENTERPRISE) {
		if(empty($db)){
			$db = EnumClass::DB_NAME_ENTERPRISE;
		}
		$db = strtolower($db);
		switch($db){
			case EnumClass::DB_NAME_PERSONAL:
				$db = 'db_oa';
				break;
			case EnumClass::DB_NAME_ENTERPRISE:
				$db = 'dev';
				break;
			case EnumClass::DB_NAME_UPDATE:
				$db = 'iv_update';
				break;
			default:
				return false;
				break;
		}
		Doo::db()->reconnect($db);
	}

	/*
	 * 检测当前数据库(临时使用表名来判断个人端还是企业端)
	 * General.20160111
	 */
	public static function db_detect() {
		$dbconfig = Doo::db()->getDefaultDbConfig();
		$db = '';
		if(isset($dbconfig[1]) && !empty($dbconfig[1])){
			switch(strtolower($dbconfig[1])){
				case 'db_oa_main':
					$db = EnumClass::DB_NAME_PERSONAL;
					break;
				case 'db_oa_enterprise_main':
					$db = EnumClass::DB_NAME_ENTERPRISE;
					break;
			}
		}
		return $db;
	}

	/**
	 * [nl2br 换行转br  suson 20160107]
	 * @param  [type] $str [description]
	 * @return [type]      [description]
	 */
	public static function nl2br($str){

		return str_replace(array("\r\n", "\r", "\n"), "<br />", $str);
	}



	/**
	 * [list_to_tree 生成数组树]
	 * @param  [type]  $list  [description]
	 * @param  string  $pk    [description]
	 * @param  string  $pid   [description]
	 * @param  string  $child [description]
	 * @param  integer $root  [description]
	 * @return [type]         [description]
	 */
	public static function list_to_tree($list, $pk='formitemid', $pid = 'parentitemid', $child = 'listarea', $root = 0) {
		//创建Tree
		$tree = array();

		if (is_array($list)) {
		//创建基于主键的数组引用
			$refer = array();

			foreach ($list as $key => $data) {
				$refer[$data[$pk]] = &$list[$key];
			}

			foreach ($list as $key => $data) {
				//判断是否存在parent
				$parantId = $data[$pid];

				if ($root == $parantId) {
					$tree[] = &$list[$key];
				} else {
				if (isset($refer[$parantId])) {
					$parent = &$refer[$parantId];
					$parent[$child][] = &$list[$key];
					}
				}
			}
		}

		return $tree;
	}

	/**
	 * [oral_time 口语化时间]
	 * suson.20160408
	 * @param  [type] $date [description]
	 * @return [type]       [description]
	 */
	public static function oral_time($date) {
		if(strpos($date, '1970') !== false  || strpos($date, '0001') !== false || strpos($date, '0000') !== false){
			return '';
		}
		$dateTime = strtotime($date); //发布新鲜事的日期的时间戳
		$dateforymd = date('Y-m-d', strtotime($date)); //发布新鲜事的日期 y-m-d 形式
		$dateforyear = date('Y', strtotime($date)); //发布新鲜事的日期的年份
		$nowtime = time(); //当前时间的时间戳
		$nowtimeforyear = date('Y'); //当前时间的年份
		$nowdate = date('Y-m-d');
		$yesterday = date("Y-m-d", strtotime("-1 day")); //当前时间的昨天的日期
		$beforeyesterday = date('Y-m-d', strtotime("-2 day")); //当前时间的前天的日期
		$threedayago = date('Y-m-d', strtotime("-3 day")); //当前时间的3天前的日期
		$beforeyear = date("Y", strtotime("-1 year")); //当前时间的上一年的年份

		$timeDifference = $nowtime - $dateTime;
		if ($timeDifference < 60) {
			return '刚刚';
		} else if ($timeDifference > 60 && $timeDifference < 3600) {
			$i = floor($timeDifference / 60);
			return $i . '分钟前';
		} else if ($timeDifference > 3600 && $nowdate == $dateforymd) {
			return date('今天 H:i', strtotime($date));
		} else if ($dateforymd == $yesterday) {
			return date('昨天 H:i', strtotime($date));
		} else if ($dateforymd == $beforeyesterday) {
			return date('前天 H:i', strtotime($date));
		} else if ($dateforymd == $threedayago) {
			return date('n月j日 H:i', strtotime($date));
		} else if ($dateforyear < $nowtimeforyear) {
			return date('Y年m月d日 H:i', strtotime($date));
		} else {
			return date('n月j日 H:i', strtotime($date));
		}
	}

	/**
	 * get_pdf_file_path 生成pdf文件，并返回路径
	 * @param  string $entid  description
	 * @param  string $userid description
	 * @param  string $pdfName   要生成的pdf文件名
	 * @param  string $html   要生成的html字符串
	 * @param  array $mpdf   mpdf的配置项
	 * @param  string $pdfHeader 页头
	 * @param  string $pdfFooter 页尾
	 * @return string            生成pdf文件的绝对路径
	 */
	public static function get_pdf_file_path($entid,$userid,$pdfName='',$html,$mpdf='',$pdfHeader='',$pdfFooter=''){
		// Doo::loadClass('CommonClass');
		Doo::loadClass('mpdf/mPDF');
		$pdfPath = CommonClass::gen_save_filepath($entid,$userid,6);//得到文件应该存放的位置
		//参数过滤
		if ($pdfName == '' || !is_string($pdfName)) {
			# pdf默认名
			$pdfName = 'test.pdf';
		}
		if (!is_string($html)) {
			$html = '';
		}
		if (!is_array($mpdf) || !isset($mpdf['mode'])  || !isset($mpdf['format'])  || !isset($mpdf['default_font_size'])  || !isset($mpdf['default_font']) || !isset($mpdf['mgl']) || !isset($mpdf['mgr']) || !isset($mpdf['mgt']) || !isset($mpdf['mgb']) || !isset($mpdf['mgh']) || !isset($mpdf['mgf']) || !isset($mpdf['orientation'])) {
			# 参数过滤,防止参数丢失
			$mpdf = array(
				'mode' => 'UTF-8',
				'format' => 'A4',
				'default_font_size' => 0,
				'default_font' => '',
				'mgl' => 15,
				'mgr' => 15,
				'mgt' => 45,
				'mgb' => 15,
				'mgh' => 9,
				'mgf' => 9,
				'orientation' => 'P'
				);
		}
		$path = Doo::conf()->SITE_PATH.$pdfPath;//文件的绝对路径
		//创建临时文件夹
		if (!file_exists($path)){
			mkdir($path, 0777, true);//给予权限生成文件夹
		}
/*     	var_dump($mpdf);die;*/
		//生成pdf文件
		$pdfPath = $path.$pdfName.'.pdf';//最终文件的绝对路径
		$pdfMPDF = new mPDF($mpdf['mode'],$mpdf['format'],$mpdf['default_font_size'],$mpdf['default_font'],$mpdf['mgl'],$mpdf['mgr'],$mpdf['mgt'],$mpdf['mgb'],$mpdf['mgh'],$mpdf['mgf'],$mpdf['orientation']);
		$pdfMPDF->useAdobeCJK = true;//防止乱码的关键一步
		$pdfMPDF->SetAutoFont(AUTOFONT_ALL);
		$pdfMPDF->SetDisplayMode('fullpage');
		$pdfMPDF->watermark_font = 'GB';
		//设置页头页尾
		if ($pdfHeader != '' && is_string($pdfHeader)) {
			# 页头
			$pdfMPDF->SetHTMLHeader($pdfHeader);
		}
		if ($pdfFooter != '' && is_string($pdfFooter)) {
			# 页尾
			$pdfMPDF->SetHTMLFooter($pdfFooter);
		}
		$pdfMPDF->WriteHTML($html);
		$pdfMPDF->Output($pdfPath);
		unset($pdfMPDF);
		//返回pdf文件路径
		return $pdfPath;
		exit();
	}

	/**
	 * output_qrcode 输出二维码
	 * @param  [type]  $text                 二维码内容,如 http://www.oa.cn/download/123
	 * @param  boolean $outfile              输出文件，false为不输出
	 * @param  string  $errorCorrectionLevel 容错级别，默认为 L
	 * @param  integer $matrixPointSize      生成图片大小
	 * @param  integer $margin
	 * @param  boolean $saveandprint
	 * @return [type]                        无返回
	 */
	public static function output_qrcode($text, $outfile = false, $errorCorrectionLevel = 'L', $matrixPointSize = 3, $margin = 2, $saveandprint = false){
		Doo::loadClass('phpqrcode/phpqrcode');
		// 二维码数据
		// $value = $this->data['oacnurl'].'web/platform/page/download?entno='.$entno; //二维码内容
		// $value = $this->data['oacnurl'].'web/platform/page/download?entno='.$entno.'&pagePhone=true'; //隐藏智信 edit lys 2016.03.31
		QRcode::png($text, $outfile, $errorCorrectionLevel, $matrixPointSize, $margin, $saveandprint);
		exit();
	}

	/**
	 * [addslashes_str 单引号（'）、双引号（"）、反斜线（\）与NUL（NULL 字符）转义 lyj 2016-05-18]
	 * @param  [type] $str [description]
	 * @return [type]      [description]
	 */
	public static function addslashes_str($str) {
		if (is_string($str) && !empty($str)) {
			return addslashes($str);
		} else {
			return $str;
		}
	}



	/**
	 * [list_dir 列出某目录的文件与目录]
	 * suson.20160511
	 * @param  [type] $dir [description]
	 * @return [type]      [description]
	 */
	public static function list_dir($dir){
		$result = array();
		if (is_dir($dir)){
			$file_dir = scandir($dir);
			foreach($file_dir as $file){
				if ($file == '.' || $file == '..'){
					continue;
				}
				elseif (is_dir($dir.$file)){
					$result = array_merge($result, self::list_dir($dir.$file.'/'));
				}
				else{
					array_push($result, $dir.$file);
				}
			}
		}
		return $result;
	}

	public static function dir_chmod($path = null, $role = '777'){
		if ($path === null) {
			$path = Doo::conf()->SITE_PATH;
		}
		Doo::loadClass("HttpClientClass");
		$ret = HttpClientClass::quickGet('http://127.0.0.1:7890/cmd?cmd=dir_chmod&param=' . $path);
		return $ret;
	}




	public static function validation_filter_id_card($id_card){
		if(strlen($id_card)==18){
			return self::idcard_checksum18($id_card);
		}elseif((strlen($id_card)==15)){
			$id_card=self::idcard_15to18($id_card);
			return self::idcard_checksum18($id_card);
		}else{
			return false;
		}
	}
	// 计算身份证校验码，根据国家标准GB 11643-1999
	public static function idcard_verify_number($idcard_base){
		if(strlen($idcard_base)!=17){
			return false;
		}
		//加权因子
		$factor=array(7,9,10,5,8,4,2,1,6,3,7,9,10,5,8,4,2);
		//校验码对应值
		$verify_number_list=array('1','0','X','9','8','7','6','5','4','3','2');
		$checksum=0;
		for($i=0;$i<strlen($idcard_base);$i++){
			$checksum += substr($idcard_base,$i,1) * $factor[$i];
		}
		$mod=$checksum % 11;
		$verify_number=$verify_number_list[$mod];
		return $verify_number;
	}
	// 将15位身份证升级到18位
	public static function idcard_15to18($idcard){
		if(strlen($idcard)!=15){
			return false;
		}else{
			// 如果身份证顺序码是996 997 998 999，这些是为百岁以上老人的特殊编码
			if(array_search(substr($idcard,12,3),array('996','997','998','999')) !== false){
				$idcard=substr($idcard,0,6).'18'.substr($idcard,6,9);
			}else{
				$idcard=substr($idcard,0,6).'19'.substr($idcard,6,9);
			}
		}
		$idcard=$idcard.self::idcard_verify_number($idcard);
		return $idcard;
	}
	// 18位身份证校验码有效性检查
	public static function idcard_checksum18($idcard){
		if(strlen($idcard)!=18){
			return false;
		}
		$idcard_base=substr($idcard,0,17);
		if(self::idcard_verify_number($idcard_base)!=strtoupper(substr($idcard,17,1))){
			return false;
		}else{
			return true;
		}
	}

	// 获取浏览器类型
	public static function getBrowserType() {
		$agent = $_SERVER['HTTP_USER_AGENT'];
		if (empty($agent)){//当浏览器没有发送访问者的信息的时候
			return 'unknow';
		};
		if (strpos($agent,'MSIE') !== false || (strpos($agent, 'rv:') && strpos($agent, 'Gecko'))) //ie判断
			return "ie";
		else if(strpos($agent,'Firefox') !== false)
			return "firefox";
		else if(strpos($agent,'Chrome') !== false)
			return "chrome";
		else if(strpos($agent,'Opera') !== false)
			return 'opera';
		else if((strpos($agent,'Chrome') === false) && strpos($agent, 'Safari') !== false)
			return 'safari';
		else
			return 'unknown';
	}

	// 获取浏览器版本
	public static function getBrowserVer() {
		$agent = $_SERVER['HTTP_USER_AGENT'];
		if (empty($agent)){//当浏览器没有发送访问者的信息的时候
			return 'unknow';
		};
		if (preg_match('/MSIE\s(\d+)\..*/i', $agent, $regs))
			return $regs[1];
		elseif (preg_match('/rv:(\d+)\..*/i', $agent, $regs))
			return $regs[1];
		elseif (preg_match('/FireFox\/(\d+)\..*/i', $agent, $regs))
			return $regs[1];
		elseif (preg_match('/Opera[\s|\/](\d+)\..*/i', $agent, $regs))
			return $regs[1];
		elseif (preg_match('/Chrome\/(\d+)\..*/i', $agent, $regs))
			return $regs[1];
		elseif ((strpos($agent,'Chrome')==false)&&preg_match('/Safari\/(\d+)\..*$/i', $agent, $regs))
			return $regs[1];
		else
			return $agent;
	}

	/**
	 * 获取客户端浏览器信息 添加win10 edge浏览器判断
	 * @return string
	 */
	public static function getBrowserInfo($getInt = true) {
		$sys = $_SERVER['HTTP_USER_AGENT'];  //获取用户代理字符串
		if (stripos($sys, "Firefox/") > 0) {
			preg_match("/Firefox\/([^;)]+)+/i", $sys, $b);
			$exp[0] = "Firefox";
			$exp[1] = $b[1];  //获取火狐浏览器的版本号
		} elseif (stripos($sys, "Maxthon") > 0) {
			preg_match("/Maxthon\/([\d\.]+)/", $sys, $aoyou);
			$exp[0] = "傲游";
			$exp[1] = $aoyou[1];
		} elseif (stripos($sys, "MSIE") > 0) {
			preg_match("/MSIE\s+([^;)]+)+/i", $sys, $ie);
			$exp[0] = "IE";
			$exp[1] = $ie[1];  //获取IE的版本号
		} elseif (stripos($sys, "OPR") > 0) {
			preg_match("/OPR\/([\d\.]+)/", $sys, $opera);
			$exp[0] = "Opera";
			$exp[1] = $opera[1];
		} elseif(stripos($sys, "Edge") > 0) {
			//win10 Edge浏览器 添加了chrome内核标记 在判断Chrome之前匹配
			preg_match("/Edge\/([\d\.]+)/", $sys, $Edge);
			$exp[0] = "Edge";
			$exp[1] = $Edge[1];
		} elseif (stripos($sys, "Chrome") > 0) {
			preg_match("/Chrome\/([\d\.]+)/", $sys, $google);
			$exp[0] = "Chrome";
			$exp[1] = $google[1];  //获取google chrome的版本号
		} elseif(stripos($sys,'rv:')>0 && stripos($sys,'Gecko')>0){
			preg_match("/rv:([\d\.]+)/", $sys, $IE);
			$exp[0] = "IE";
			$exp[1] = $IE[1];
		}else {
			$exp[0] = "未知浏览器";
			$exp[1] = "";
		};
		return array(
			'type' => $exp[0],
			'version' => $getInt ? intval($exp[1]) : $exp[1]
		);
	}

	/**
	 * 求sql  limit的两个参数
	 * @param  [type] $list_num   [description]
	 * @param  string $list_index [description]
	 * @param  string $page_num   [description]
	 * @return [type]             [description]
	 */
	public static function getPageNum($list_num,$list_index='',$page_num=''){
		if(empty($list_index)&&!empty($page_num)){
			return $list_index=$list_num*($page_num-1);
		}elseif($list_index>=0&&empty($page_num)){
			return $page_num=($list_index/$list_num)+1;
		}
	}

	/**
	 * zip_go_tool_relative    调用go工具解压或者压缩文件、文件夹,相对于enterprise上级目录
	 * liujia 2016-12-14 16:53:12
	 * @param  string $filepath 解压时为压缩文件路径，压缩时为文件夹路径
	 * @param  string $outpath   解压时输出文件夹，压缩时为输出文件全名
	 * @param  string $oper     unzip解压缩 || zip压缩
	 * @return [type]           [description]
	 * 注意:全部为相对enterprise文件夹上级目录
	 * 示例:
	 * - 解压 $filepath = './enterprise/test.zip' $outpath = './enterprise/'(请带上最后一个/) $oper='unzip'
	 * - 压缩 $filepath = './enterprise/test'(这里千万不要最后一个/) $outpath = './enterprise' $oper='zip'
	 */
	public static function zip_go_tool_relative($filepath, $outpath, $oper='unzip') {
		$oper='unzip';   //相对路径不明确
		Doo::loadClass("HttpClientClass");
		// 1.相对enterprise上一级，以脚本位置为基准
		$path = Doo::conf()->SITE_PATH.'../';
		$filepath 	= $path.$filepath;
		$outpath 	= $path.$outpath;
		$param    	= urlencode(' '.$path.' '.$filepath.' '.$outpath.' '.$oper);
		$ret 		= HttpClientClass::quickGet('http://127.0.0.1:7890/cmd?cmd=simplezip&param='.$param);
		return $ret;
	}

	/**
	 * zip_go_tool_absolute    调用go工具解压或者压缩文件、文件夹,带盘符的绝对路径(或者复制文件到指定目录绝对路径)
	 * @param  [type] $filepath [description]
	 * @param  [type] $outpath  [description]
	 * @param  string $oper     (unzip解压) (zip压缩) (cp复制)
	 * @return [type]           [description]
	 */
	public static function zip_go_tool_absolute($filepath, $outpath, $oper='unzip') {
		Doo::loadClass("HttpClientClass");
		$path = Doo::conf()->SITE_PATH.'../';
		$param    	= urlencode(' '.$path.' '.$filepath.' '.$outpath.' '.$oper);
		$ret 		= HttpClientClass::quickGet('http://127.0.0.1:7890/cmd?cmd=simplezip&param='.$param);
		return $ret;
	}

	/**
	 * [array_multisort 二维数组排序 lyj]
	 * @param  [type] $arrays     [description]
	 * @param  [type] $sort_key   [description]
	 * @param  [type] $sort_order [description]
	 * @param  [type] $sort_type  [description]
	 * @return [type]             [description]
	 */
	public static function array_multisort($arrays, $sort_key, $sort_order = SORT_ASC, $sort_type = SORT_NUMERIC) {
		$key_arrays = array();
		if (is_array($arrays)) {
			foreach ($arrays as $array){
				if (is_array($array)) {
					$key_arrays[] = $array[$sort_key];
				} else {
					return false;
				};
			};
		} else {
			return false;
		};
		array_multisort($key_arrays, $sort_order, $sort_type, $arrays);
		return $arrays;
	}


	/**
	 * set_tkdata  生成tkdata
	 * @param  [type] $prm_base [description]
	 * @return [type]           [description]
	 */
	public static function tkdata_set($prm_base, $token_keep = true, $session_make = false) {
		$tkdata = array(
			'userid' => $prm_base->userid,
			'token' => CommonClass::randomName(10),
			'token_keep' => $token_keep,
			'timetamp' => time(),
			);
		$ret = CommonClass::encode($tkdata);
		$tkdata['prm_base'] = $prm_base;
		$tkdata['session_make'] = $session_make;
		$tkdata['check_time'] = true;
		CommonClass::cache_op('set',EnumClass::CACHE_KEY_PERSONAL,0,0,$tkdata['token'],$tkdata);
		return $ret;
	}

	/**
	 * 检查是否使用新版的系统管理代码
	 * @param  [type] $version  [description]
	 * @param  [type] $prm_base [description]
	 * @return [type]           [description]
	 */
	public static function check_sys_code_version($version, $prm_base, $entid = 0) {
		return true;
		//EnumClass::PARAM_TYPE_APP_CODE	代码类型
		$entid = !empty($entid) ? $entid : $prm_base->entid;
		$moduleid = EnumClass::MODULE_NAME_SYS;
		$newversion = false;
		$ret = ApiClass::enterprise_setting_get($version, $prm_base, EnumClass::PARAM_TYPE_APP_CODE, $moduleid,  $entid);
		if ($ret['ret'] == RetClass::SUCCESS) {
			if ($ret['data']['valuechar']) {
				$newversion = true;
			}
		}
		return $newversion;
	}

	/**
	 * 验证邮箱合法性
	 * @param  [type] $email    [description]
	 * @return [type]           [description]
	 */
	public static function check_email($email) {
		if (empty($email)) {
			return false;
		}
		$pattern = "/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/";
		if (preg_match($pattern, $email)) {
			return true;
		}
		return false;
	}

	/**
	 * shell_script_to_package_files    调用shell脚本打包文件到指定目录，打包方式为tar
	 * @param $output 输出路径
	 * @param $files array 无下标的需要打包的路径数组
	 */
	public static function shell_script_to_package_files($output, $files=array()) {
		Doo::loadClass("HttpClientClass");
		$path       = Doo::conf()->SITE_PATH.'../';
		$param      = $path.' zcvf '.$output.' ';

		if(empty($files)) {
			return false;
		}

		foreach ($files as $value) {
			$param .= $value.' ';
		}
		$param      = urlencode($param);

		$ret        = HttpClientClass::quickGet('http://127.0.0.1:7890/cmd?cmd=oatar&param='.$param);
		return $ret;
	}

	/**
	 * [get_qrcode_base64 获取一个base64二维码 lyj 2017-04-10]
	 * @param  [type]  $text                 [description]
	 * @param  string  $errorCorrectionLevel [description]
	 * @param  integer $matrixPointSize      [description]
	 * @param  integer $margin               [description]
	 * @param  boolean $saveandprint         [description]
	 * @return [type]                        [description]
	 */
	public static function get_qrcode_base64($text, $errorCorrectionLevel = 'L', $matrixPointSize = 3, $margin = 4) {
		Doo::loadClass('phpqrcode/phpqrcode');
		$tmpfname = tempnam('./userfiles', 'TMP'); //临时目录下创建临时文件
		if (empty($tmpfname)) {
			return '';
		};
		QRcode::png($text, $tmpfname, $errorCorrectionLevel, $matrixPointSize, $margin);
		$result = base64_encode(file_get_contents($tmpfname));
		unlink($tmpfname); //删除临时文件
		return $result;
	}

	/**
	 * [check_is_https 判断是否https协议 lyj 2017-04-13]
	 * @return boolean [description]
	 */
	public static function check_is_https() {
		if (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') {
			return true;
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
			return true;
		} elseif (!empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off') {
			return true;
		};
		return false;
	}

	/**
	 * [array_column 返回数组中指定的一列 lyj 2017-04-20]
	 * @param  [type] $input     [需要取出数组列的多维数组（或结果集）]
	 * @param  [type] $columnKey [需要返回值的列，它可以是索引数组的列索引，或者是关联数组的列的键。也可以是NULL，此时将返回整个数组（配合index_key参数来重置数组键的时候，非常管用）]
	 * @param  [type] $indexKey  [作为返回数组的索引/键的列，它可以是该列的整数索引，或者字符串键值。]
	 * @return [type]            [返回值]
	 */
	public static function array_column($input, $columnKey, $indexKey = null) {
		if (!function_exists('array_column')) {
			$columnKeyIsNumber = (is_numeric($columnKey)) ? true : false;
			$indexKeyIsNull = (is_null($indexKey)) ? true : false;
			$indexKeyIsNumber = (is_numeric($indexKey)) ? true : false;
			$result = array();
			foreach ((array)$input as $key => $row) {
				if ($columnKeyIsNumber) {
					$tmp = array_slice($row, $columnKey, 1);
					$tmp = (is_array($tmp) && !empty($tmp)) ? current($tmp) : null;
				} else {
					$tmp = isset($row[$columnKey]) ? $row[$columnKey] : null;
				};
				if (!$indexKeyIsNull) {
					if ($indexKeyIsNumber) {
						$key = array_slice($row, $indexKey, 1);
						$key = (is_array($key) && !empty($key)) ? current($key) : null;
						$key = is_null($key) ? 0 : $key;
					} else {
						$key = isset($row[$indexKey]) ? $row[$indexKey] : 0;
					};
				};
				$result[$key] = $tmp;
			};
			return $result;
		} else {
			return array_column($input, $columnKey, $indexKey); //PHP5.5以上
		};
	}

	/**
	 * [strim1 按字符切分，中文算一个字符 lyj 2017-04-20]
	 * @param  [type] $str    [原字符串]
	 * @param  [type] $offset [起始位置]
	 * @param  [type] $length [截取长度]
	 * @param  string $point  [打点？]
	 * @return [type]         [description]
	 */
	public static function strim1($str, $offset, $length, $point = '') {
		$charset = 'utf-8';
		$str_len = mb_strlen($str, $charset);
		$new_str = mb_substr($str, $offset, $length, $charset);
		$new_len = mb_strlen($new_str, $charset);
		$point_len = mb_strlen($point, $charset);
		if ($new_len == $length && $new_len < $str_len && $point_len > 0) {
			if ($new_len > $point_len) {
				$new_str = mb_substr($new_str, 0, $new_len - $point_len, $charset) . $point;
			} else {
				$new_str = $point;
			};
		};
		return $new_str;
	}

	/**
	 * [strim2 按字节切分，中文算两个字节 lyj 2017-04-25]
	 * @param  [type] $str    [原字符串]
	 * @param  [type] $offset [起始位置]
	 * @param  [type] $length [截取长度]
	 * @param  string $point  [打点？]
	 * @return [type]         [description]
	 */
	public static function strim2($str, $offset, $length, $point = '') {
		$str_len = mb_strwidth($str, 'utf8');
		if ($offset > 0) {
			$str = mb_strimwidth($str, $offset, $str_len, '', 'utf8');
			$str_len = mb_strwidth($str, 'utf8');
		};
		if ($str_len > $length) {
			$str = mb_strimwidth($str, 0, $length, $point, 'utf8');
		};
		return $str;
	}

	/**
	 * 过滤页面传进来的某些关键字和符号
	 * @param  [type] $params [description]
	 * @return [type]         [description]
	 */
	public static function handle_params($params) {
		if ($params) {
			// $params = array(array('201'=>'{"}'),array('20'=>0),array('21'=>2),array('211'=>'5444}}888'));
			if (is_array($params)) {
				foreach ($params as &$param) {
					$param = self::handle_params($param);
				}
			} else if(is_string($params)) {
				$params = str_replace('scope.', 'ｓｃｏｐｅ.', $params);

				if(is_null(json_decode($params)) === false) {		//json格式字符串把单引号替换成中文符号
					$params = str_replace('\'', '＇', $params);
				} else {										//非json格式字符串把单双引号都替换成中文符号
					$params = str_replace('\'', '＇', $params);
					// $params = str_replace('"', '＂', $params);
				}
                //过滤特殊字符如单引号 (')、双引号 (")、反斜线 backslash (/) 以及空字符NULL,单双引号、反斜线及NULL加上反斜线转义
                //add by james.ou 2017-11-9
                //暂时注释 hky 2017.11.16
                //$params = htmlspecialchars(addslashes($params),ENT_QUOTES);
			}

			/*$params_json = CommonClass::json_encode($params);
			// preg_match('/\:\".*?[^\\]\"|[^\\](\\)/', $params_json, $matches);
			$params_json = str_replace('scope.', 'ｓｃｏｐｅ.', $params_json);	//替换scope.关键字为全角字符
			// $params_json = preg_replace('/\{(?!\s*\n*\r*\"|\s*\n*\r*\[)/', '｛', $params_json);
			$params_json = preg_replace('/\{(?!\"(?!\})|\s*\n*\r*\[)/', '｛', $params_json); //替换"{"符号为全角符号
			$params_json = preg_replace('/(?<!\d|(?<!\:|\\\)\"|\]|false|true)\}/', '&', $params_json);	//替换"}"符号为全角符号
			print_r($params_json);
			$params = CommonClass::json_decode($params_json);
			print_r($params);*/
		}
		return $params;
	}

	/**
	 * 验证用户预设的免打扰时间
	 * @param  [type] $prm_base [description]
	 * @param  [type] $moduleid [description]
	 * @param  [type] $presetid [其实就是actnid]
	 * @return [type]           [description]
	 */
	public static function presetDisturbTime($prm_base,$moduleid,$preset,$params){
		$entid = $params['entid'] != 0 ? $params['entid'] : $prm_base->entid;
		$userid = $params['userid'] != 0 ? $params['userid'] : $prm_base->userid;
		//获取接收系统消息的用户
		$receUsers = array();
		foreach ($params['extdata'] as $receKey => $receValue) {
			$receUsers = ApiClass::datasource_userdata_to_array('3.0.0.0', $prm_base, $receValue['targetid'], $type = 'array');
			break;
		}
		//获取所有需要接收系统消息的用户
		$receUsers = $receUsers['data'];
		//过滤掉里面的空值
		$receUsers = array_filter( $receUsers );
		//操作行为、用户预设规则、用户免打扰时间、可接收系统消息用户、可接收手机推送用户、可接收短信用户
		$actionData = $userRule = $userTime = $sysUsers = $pushUsers = $smsUsers = array();
		$cacheid = '';
		// array是队列接口调用的 ， string是模块调用
		if(is_array($preset)){
			$presetid = $preset['presetid'];
			$cacheid = $preset['cacheid']; //缓存表id
		}else{
			$presetid = $preset;
		}

		//免打扰用户
		$notUsers = array();
		$parentid = self::builder_primary_id();
		foreach ($receUsers as $verKey => $verValue) {
			//获取个人用户的信息
			$user_opt['table'] = 'employee_full';
			$user_opt['where'] = 'userid=? AND entid=? AND state=?';
			$user_opt['param'] = array($verValue,$entid,EnumClass::STATE_TYPE_NORMAL);
			$userInfo = DbClass::get_one($prm_base,$user_opt,EnumClass::SQL_FLD_ADD_STATE_NONE,EnumClass::SQL_FLD_ADD_ENT_NONE);
			unset($user_opt); //手动释放资源，因为在循环中操作太多

			//记录每个用户,用于提醒用户未阅的信息
			$parset['table'] = 'recordusermsg';
			$parset['fields'] = array(
				'id' => self::builder_primary_id(),
				'parentid' => $parentid,
				'userid' => $verValue,
				'entid' => $entid,
				'username' => !empty( $userInfo ) ? $userInfo['profname'] : '',
				'state' => EnumClass::STATE_TYPE_NORMAL,
				'opstate' => EnumClass::STATE_TYPE_NORMAL,
				'params' => CommonClass::json_encode($params),
				'crtime' => CommonClass::get_datetime()
			);
			DbClass::insert($prm_base,$parset,EnumClass::SQL_FLD_CHECK_DB_NONE);

			//验证操作行为是否正确
			// $actOpt['table'] = 'action';
			// $actOpt['where'] = 'actnid=? AND moduleid=? AND actiontype=? AND state=?';
			// $actOpt['param'] = array($presetid,$moduleid,EnumClass::ACTION_TYPE_REMIND_PRESET,EnumClass::STATE_TYPE_NORMAL);
			// $actret = DbClass::get_one($prm_base,$actOpt,EnumClass::SQL_FLD_ADD_STATE_NONE,EnumClass::SQL_FLD_ADD_ENT_NONE);

			$other_params['actiontype'] = EnumClass::ACTION_TYPE_REMIND_PRESET;
			$actret = ActionClass::get_config_action_table_json($prm_base, $moduleid, $presetid, $other_params, 'get');

			if($actret === false){
				return true; //为了用户能正常接收，不在考虑预设规则
			}
			$actionData = $actret; //操作行为


			$presetModuleId = '97149456587161603'; //消息提醒预设moduleid
			//获取用户预设规则
			$opt['table'] = 'common_setting';
			$opt['where'] = 'optype=? AND moduleid=? AND entid=? AND userid=? AND state=? AND valuechar like \'%'.$moduleid.'%\'';
			$opt['param'] = array(EnumClass::PARAM_TYPE_REMIND_PRESET,$presetModuleId,$entid,$verValue,EnumClass::STATE_TYPE_NORMAL);
			$useret = DbClass::get_one($prm_base,$opt,EnumClass::SQL_FLD_ADD_STATE_NONE,EnumClass::SQL_FLD_ADD_ENT_NONE);
			if($useret === false){
				//没有个人的配置,就获取企业的
				$opt['param'] = array(EnumClass::PARAM_TYPE_REMIND_PRESET,$presetModuleId,$entid,0,EnumClass::STATE_TYPE_NORMAL);
				$entret = DbClass::get_one($prm_base,$opt,EnumClass::SQL_FLD_ADD_STATE_NONE,EnumClass::SQL_FLD_ADD_ENT_NONE);
				if($entret === false){
					//也不存在企业数据,则使用系统默认配置
					$opt['param'] = array(EnumClass::PARAM_TYPE_REMIND_PRESET,$presetModuleId,0,0,EnumClass::STATE_TYPE_NORMAL);
					$sysret = DbClass::get_one($prm_base,$opt,EnumClass::SQL_FLD_ADD_STATE_NONE,EnumClass::SQL_FLD_ADD_ENT_NONE);
					if($sysret === false){
						//如果系统也没有则有问题了
						return true; //为了用户能正常接收，不在考虑预设规则
					}
					$userRule = $sysret;
					unset($sysret);
				}else{
					$userRule = $entret;
					unset($entret);
				}
			}else{
				$userRule = $useret;
				unset($useret);
			}

			//获取用户免打扰时间
			$opt['where'] = 'optype=? AND moduleid=? AND entid=? AND userid=? AND state=?';
			$opt['param'] = array(0,$presetModuleId,$entid,$verValue,EnumClass::STATE_TYPE_NORMAL);
			$useret = DbClass::get_one($prm_base,$opt,EnumClass::SQL_FLD_ADD_STATE_NONE,EnumClass::SQL_FLD_ADD_ENT_NONE);
			if($useret === false){
				// 没个人规则，获取企业规则
				$opt['param'] = array(0,$presetModuleId,$entid,0,EnumClass::STATE_TYPE_NORMAL);
				$entret = DbClass::get_one($prm_base,$opt,EnumClass::SQL_FLD_ADD_STATE_NONE,EnumClass::SQL_FLD_ADD_ENT_NONE);
				if($entret === false){
					// 没企业规则，获取系统规则
					$opt['param'] = array(0,$presetModuleId,0,0,EnumClass::STATE_TYPE_NORMAL);
					$sysret = DbClass::get_one($prm_base,$opt,EnumClass::SQL_FLD_ADD_STATE_NONE,EnumClass::SQL_FLD_ADD_ENT_NONE);
					if($sysret === false){
						return true; //为了用户能正常接收，不在考虑预设规则
					}else{
						$userTime = $sysret;
						unset($sysret,$opt);
					}
				}else{
					$userTime = $entret;
					unset($entret,$opt);
				}
			}else{
				$userTime = $useret;
				unset($useret,$opt);
			}
			//以上数据已经获取全，解析分解数据
			/*
				步骤：1.解析时间是否符合当前时间
						不符合：把所有数据存进缓存表，返回不发送系统消息 （判断$cacheid是否有值，有值就不必在入库了，因为库已经有了）
					2.符合当前时间可以发系统消息就把模块预设配置解析（判断$cacheid是否有值，有值证明这数据属于接口调用，把此数据状态改成1001）
					3.匹配‘操作行为’，分析短信、系统消息、手机推送
					4.整合好数据。返回并发送

				Ps:$presetid是字符串的时候，是模块发送系统消息。  $presetid是数组的时候，是队列接口调用
			 */

			$userTime_valuechar = CommonClass::json_decode($userTime['valuechar']);
			$nowMin = strtotime(date("H:i")); //当前时分

			foreach ($userTime_valuechar as $timeKey => $timeValue) {

				//时间判断模式
				if(strtotime($timeValue['startime']) > strtotime($timeValue['endtime'])){
					// ||  符合条件是免打扰时间。 数据写进缓存表 不发送系统消息
					if(strtotime($timeValue['startime']) < $nowMin || $nowMin < strtotime($timeValue['endtime'])){
						$notUser = self::enterLibraryCache($prm_base,$verValue,$entid,$params,$cacheid,$actionData['actnid']);
						if($notUser != false){
							array_push($notUsers, $notUser);
							// 写进可发系统消息数组里面,由于需求是：当用户处于免打扰状态也会收到系统消息
							// array_push($sysUsers, $notUser);
						}
						break;
					}
				}else{
					// &&  符合条件是免打扰时间。  return 数据
					if(strtotime($timeValue['startime']) < $nowMin && $nowMin < strtotime($timeValue['endtime'])){
						$notUser = self::enterLibraryCache($prm_base,$verValue,$entid,$params,$cacheid,$actionData['actnid']);
						if($notUser != false){
							array_push($notUsers, $notUser);
							// 写进可发系统消息数组里面,由于需求是：当用户处于免打扰状态也会收到系统消息
							// array_push($sysUsers, $notUser);
						}
						break;
					}
				}
			}

			// 不在免打扰里面则是可接受消息用户,由于需求是：当用户处于免打扰状态也会收到系统西消息。
			// 所以暂时去掉,不去掉的话，当全部用户都处于免打扰状态就有问题了。
			// if(in_array($verValue,$notUsers)){
			// 	continue;
			// }

			$rule_valuechar = CommonClass::json_decode($userRule['valuechar']); //解析valuechar字段json数据
			$ruleData = array(); //消息规则数据
			foreach ($rule_valuechar['list'] as $ruleKey => $ruleValue) {
				if($ruleValue['id'] == $actionData['actnid']){
					$ruleData = $ruleValue;
					break;
				}
			}

			//缓存表有数据,将这条数据修改成1001
			if(!empty($cacheid) && $cacheid != 'SMS'){ //SMS是额外生成的数据，不在表里
				$opt['table'] = 'm97149456587161603';
				$opt['where'] = 'id=? AND state=?';
				$opt['param'] = array($cacheid,EnumClass::STATE_TYPE_NORMAL);
				$opt['fields'] = array(
					'state' => EnumClass::STATE_TYPE_DELETE
				);
				$ret = DbClass::update($prm_base,$opt,'','',EnumClass::SQL_FLD_CHECK_DB_NONE);
				//如果修改失败，不发消息
				if($ret['ret'] == RetClass::ERROR){
					return false;
				}
			}

			//系统消息    由于需求，免打扰用户也能收到系统消息
			if($ruleData['sys'] != 0){
				array_push($sysUsers, $verValue);
			}
			//手机推送     并且不再免打扰用户中
			if($ruleData['push'] != 0 && !in_array($verValue, $notUsers) ){
				array_push($pushUsers, $verValue);
			}
			//短信    并且不再免打扰用户中
			if($ruleData['sms'] != 0 && $cacheid != 'SMS' && !in_array($verValue, $notUsers) ){
				array_push($smsUsers, $verValue);
			}

		}


		foreach ($params['extdata'] as $closeKey => &$closeVal) {
			if($closeVal['msgtype'] == EnumClass::MSG_SEND_MESSAGE || $closeKey == EnumClass::MSG_SEND_MESSAGE){
				$closeVal['targetid'] = '{"user":[{"u":"'.implode(',', $sysUsers).'"}]}';
			}

			if($closeVal['msgtype'] == EnumClass::MSG_SEND_PUSH || $closeKey == EnumClass::MSG_SEND_PUSH){
				$closeVal['targetid'] = '{"user":[{"u":"'.implode(',', $pushUsers).'"}]}';
			}

			if($closeVal['msgtype'] == EnumClass::MSG_SEND_SMS || $closeKey == EnumClass::MSG_SEND_SMS){
				$closeVal['targetid'] = '{"user":[{"u":"'.implode(',', $smsUsers).'"}]}';
			}
		}

		return $params;
	}

	/**
	 * 处于免打扰时段，把数据存进缓存表中
	 * @param  [type] $prm_base [description]
	 * @param  [type] $params   [description]
	 * @return [type]           [description]
	 */
	private static function enterLibraryCache($prm_base,$userid,$entid,$params,$cacheid,$actnid){
		//如果有cacheid证明这条数据表里面已经有了，无需在插入
		if(!empty($cacheid)){
			return $userid;
		}
		//存储个人用户的数据
		$params['userid'] = $userid;
		$params['entid'] = $entid;

		//把短信、系统消息、手机推送的里面用户更换成自己
		foreach ($params['extdata'] as $key => &$value) {
			if( $value['msgtype'] == EnumClass::MSG_SEND_MESSAGE ){
				array_splice($params['extdata'], $key, 1);
			}else{
				$value['targetid'] = '{"user":[{"u":"'.$userid.'"}]}';
			}
		}
		// 处于免打扰时间段，系统消息要发送，需把$params的系统消息去掉，把每个用户的id记下来返回出去
		// 因为发了系统消息之后就不需要在发送了所以$params里面的系统消息记录去掉

		$opt['table'] = 'm97149456587161603';
		$opt['fields'] = array(
			'id' => self::builder_primary_id(),
			'userid' => $userid,
			'entid' => $entid,
			'actnid' => $actnid,
			'state' => EnumClass::STATE_TYPE_NORMAL,
			'opstate' => EnumClass::STATE_TYPE_NORMAL,
			'params' => self::json_encode($params)
		);

		$ret = DbClass::insert($prm_base,$opt,EnumClass::SQL_FLD_CHECK_DB_NONE);

		return $userid;
	}

	/**
	 * shell_tar_update_package   调用shell脚本打包全站文件
	 * @param $output 输出路径
	 * @param $files array 无下标的需要打包的路径数组
	 */
	public static function shell_tar_update_package() {
		Doo::loadClass("HttpClientClass");
		$path       = Doo::conf()->SITE_PATH.'../';
		$param      = $path;
		$param      = urlencode($param);
		$ret        = HttpClientClass::quickGet('http://127.0.0.1:7890/cmd?cmd=tar_ent_for_down&param='.$param);
		return $ret;
	}



	/**
	 * shell_wget_update_all
	 * 调用shell脚本,下载整站覆盖更新包
	 * @param      string  $host        下载路径前缀  http://t31.oa.cn/down/
	 * @param      string  $filename    下载更新包名  update_enterprise.tar.gz
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function shell_wget_update_all($host, $filename) {
		Doo::loadClass("HttpClientClass");
		$path       = Doo::conf()->SITE_PATH.'../';
		$param      = $path.' '.$host.' '.$filename;
		$param      = urlencode($param);
		$ret        = HttpClientClass::quickGet('http://127.0.0.1:7890/cmd?cmd=update&param='.$param);
		return $ret;
	}

	/**
	 * [str_encode 字符串加密 lyj 2017-06-22]
	 * @param  string $str  [description]
	 * @param  string $type [description]
	 * @return [type]       [description]
	 */
	public static function str_encode($str, $type = 'AES') {
		if (empty($str) || !is_string($str)) {
			return '';
		};
		switch ($type) {
			case 'DES':
				$encrypt = new DES();
				break;
			case 'AES':
			default:
				$encrypt = new AES();
				break;
		};
		return $encrypt->encode($str);
	}

	/**
	 * [str_decode 字符串解密 lyj 2017-06-22]
	 * @param  string $str  [description]
	 * @param  string $type [description]
	 * @return [type]       [description]
	 */
	public static function str_decode($str, $type = 'AES') {
		if (empty($str) || !is_string($str)) {
			return '';
		};
		switch ($type) {
			case 'DES':
				$encrypt = new DES();
				break;
			case 'AES':
				$encrypt = new AES();
				break;
		};
		return $encrypt->decode($str);
	}

	/**
	 * 检查密码合法性
	 * @param  [type]  $password  [description]
	 * @param  string  $filter    [description]
	 * @param  integer $minlenght [description]
	 * @param  integer $maxlenght [description]
	 * @return [type]             [description]
	 */
	public static function check_password($password, $mode = EnumClass::PARAM_PASSWORD_MODE_NORMAL, $filter = '', $minlenght = 8, $maxlenght = 16) {
		if (empty($password) || !is_string($password) || !is_string($filter)) {
			return BaseClass::ret();
		}
		$password = trim($password);
		$password = (string)$password;
		if (!self::check_chinese($password)) {
			if (strlen($password)>=$minlenght && strlen($password)<=$maxlenght) {
				switch ($mode) {
					case EnumClass::PARAM_PASSWORD_MODE_COMPLEX:	//复杂模式
						$i = 0;
						if (preg_match('/[a-z]/',$password)) {
							$i++;
						}
						if (preg_match('/[A-Z]/', $password)) {
							$i++;
						}
						if (preg_match('/[0-9]/', $password)) {
							$i++;
						}
						if (!preg_match("/^[A-Za-z0-9]+$/",$password)) {
							$i++;
						}
						if ($i>=3) {
							if (!empty($filter)) {
								if (strpos($password, (string)$filter) !== false) {
									$content = CommonClass::txt('TIPS_PWD_NOT_INCLUDE_USERNAME');
								} else {
									return BaseClass::ret(RetClass::SUCCESS);
								}
							} else {
								return BaseClass::ret(RetClass::SUCCESS);
							}
						} else {
							$content = CommonClass::txt('TIPS_PWD_INCLUDE_CONTENT');
						}
						break;
					case  EnumClass::PARAM_PASSWORD_MODE_NORMAL:	//普通模式
					default:
						return BaseClass::ret(RetClass::SUCCESS);
						break;
				}
			} else {
				// $content = '密码长度至少需要8位';
				$content = CommonClass::txt('TIPS_PWD_LIMIT_LENGHT', $minlenght.'@@'.$maxlenght);
			}
		} else {
			$content = CommonClass::txt('TIPS_PWD_NOT_INCLUDE_STRING');
		}
		return BaseClass::ret(RetClass::ERROR, '', '', '', $content);
	}

	/**
	 * 判断字符串是否包含中文
	 * @param  string $str [description]
	 * @return bool        [description]
	 */
	public static function check_chinese($str) {
		if (preg_match("/[\x7f-\xff]/", $str)) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * 生成图形验证码
	 * @param  string  $version  [description]
	 * @param  object  $prm_base [description]
	 * @param  integer $num      [description]	验证码字符数
	 * @param  integer $width    [description]	图片宽度
	 * @param  integer $height   [description]	图片高度
	 * @return [type]            [description]
	 */
	public static function get_graph_code($version, $prm_base, $num = 4, $width = 100, $height = 30) {
		$str = "qwertyuiopasdfghjklzxcvbnm1234567890";  //验证码字符取值范围[a-z0-9]
		// $str = "1234567890";  //验证码字符取值范围[0-9]
		// $dotNum = 300;      //干扰点个数
		// $lineNum = rand(3, 5);         //干扰线条数
		$image = imagecreatetruecolor($width, $height);    //1>设置验证码图片大小的函数
		//5>设置验证码颜色 imagecolorallocate(int im, int red, int green, int blue);
		$bgcolor = imagecolorallocate($image,255,255,255); //#ffffff
		//6>区域填充 int imagefill(int im, int x, int y, int col) (x,y) 所在的区域着色,col 表示欲涂上的颜色
		imagefill($image, 0, 0, $bgcolor);
		//10>设置变量
		$captcha_code = "";
		//7>生成随机数字
		for($i=0;$i<$num;$i++){
			//设置字体大小
			$fontsize = 6;
			//设置字体颜色，随机颜色
			$fontcolor = imagecolorallocate($image, rand(0,120),rand(0,120), rand(0,120));      //0-120深颜色
			//设置数字
			$fontcontent = substr($str, rand(0, strlen($str)), 1);      //随机取字符集中的值
			//10>.=连续定义变量
			$captcha_code .= $fontcontent;
			//设置坐标
			$x = ($i*100/4)+rand(5,10);
			$y = rand(5,10);

			imagestring($image,$fontsize,$x,$y,$fontcontent,$fontcolor);
		}
		//10>存到session
		// $_SESSION['graphcode'] = $captcha_code;
		CommonClass::session()->graphcode = $captcha_code;
		CommonClass::session()->graphcodetime = CommonClass::get_timestamp();
		//8>增加干扰元素，设置雪花点
		for($i=0;$i<200;$i++){
		//设置点的颜色，50-200颜色比数字浅，不干扰阅读
		$pointcolor = imagecolorallocate($image,rand(50,200), rand(50,200), rand(50,200));
		//imagesetpixel — 画一个单一像素
		imagesetpixel($image, rand(1,99), rand(1,29), $pointcolor);
		}
		//9>增加干扰元素，设置横线
		for($i=0;$i<4;$i++){
		//设置线的颜色
		$linecolor = imagecolorallocate($image,rand(80,220), rand(80,220),rand(80,220));
		//设置线，两点一线
		imageline($image,rand(1,99), rand(1,29),rand(1,99), rand(1,29),$linecolor);
		}

		//2>设置头部，image/png
		header('Content-Type: image/png');
		//3>imagepng() 建立png图形函数
		imagepng($image);
		//4>imagedestroy() 结束图形函数 销毁$image
		imagedestroy($image);
	}

	/**
	 * 获取csrftoken的方法
	 * hky 20170820
	 * @return [type] [description]
	 */
	public static function get_csrftoken() {
		if (isset(CommonClass::session()->csrftoken) && !empty(CommonClass::session()->csrftoken)) {
			return CommonClass::session()->csrftoken;
		}
		return '';
	}

	/**
	 * url参数加密
	 * hky20170822
	 * @param  [type] $url    [description]
	 * @param  array  $params [description]	一维数组，例如 array('a'=>111, 'b'=>222)  会拆分成a=111&b=222
	 * @return [type]         [description]
	 */
	public static function url_encrypt($url, $params = array(), $type = '') {
		// 判断是否传入URL地址
		if (empty($url)) {
			return '';
		}
		// 默认：EnumClass::PAGE_RET_MSG_TYPE_PAGE
		$type = !empty($type) ? $type : EnumClass::PAGE_RET_MSG_TYPE_PAGE;
		// 获取待加密的参数，并转换为数组格式
		$urlarr = parse_url($url);
		$enc_params = array();
		if (!empty($urlarr['query'])) {
			parse_str($urlarr['query'], $enc_params);
		}
		// 判断URL是否已加密，参数包含了reqdata的，当做已加密的
		$keys = array_keys($enc_params);
		if (in_array('reqdata', $keys)) {
			return $url;
		}
		if (is_array($params) && !empty($params)) {
			$enc_params = array_merge($enc_params, $params);
		}
		// 加密
		$data = CommonClass::encode_format(null, $enc_params, $type);
		// 支持外部邮件这种带固定站点地址的 http://e40.oa.cn/webmail/ suson.20160928
		$url = '';
		if (!empty($urlarr['scheme']) && !empty($urlarr['host'])) {
			$url .= $urlarr['scheme'] . '://' . $urlarr['host'];
		}
		if (!empty($urlarr['port'])) {
			$url .= ':' . $urlarr['port'];
		}
		$path = !empty($urlarr['path']) ? $urlarr['path'] : '';
		$url .= $path . $data;
		LogClass::log_trace1('url_encrypt',$urlarr,__FILE__,__LINE__);
		return $url;
	}

	/**
	 * 验证来源网站，因为不同站点域名不一样，暂时验证oa.cn和127.0.0.1，后面获取各站点域名进行改进
	 * hky20170822
	 * @param  string  $version  [description]
	 * @param  object  $prm_base [description]
	 * @param  boolean $check    [description] true则必须检查；false则referer不为空时才检查，主要针对上传附件的请求，在IE浏览器获取不到referer，但是不能不过验证
	 * @return [type]            [description]
	*/
	public static function verify_referer($version, $prm_base, $check = true) {
		$result = false;
    	$oasites = array(
    			'oa.cn','igov.cn','zhibg.com','hnnxdbgs.com','investguangzhou.gov.cn','gdsyzx.edu.cn','127.0.0.1'
    		);
    	$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
    	LogClass::log_trace1('verify_referer::', $referer, __FILE__, __LINE__);
    	if ($referer) {
	    	$urlarr = parse_url($referer);
	    	$host = isset($urlarr['host']) ? $urlarr['host'] : '';
	    	LogClass::log_trace1('verify_referer_host::', $host, __FILE__, __LINE__);
	    	foreach ($oasites as $oasite) {
	    		if (strpos($host, $oasite) !== false) {
					$result = true;
					break;
				}
	    	}
    		// if (strpos($host, $oaurl) !== false || strpos($host, $localurl) !== false) {
    		// 	$result = true;
    		// }
    	} else {
    		if (!$check) {  //referer为空，不是必须检查则验证通过
	    		$result = true;
    		}
    	}
    	LogClass::log_trace1('verify_referer_result::', $result, __FILE__, __LINE__);
    	return $result;
    }

    /**
     * 从文件里面获取数据(优先从缓存里面读取，如果缓存无或时间过期或文件已修改，再从文件读取后，并存入缓存)
     * @param str $filename
     * @param arr $defvalue
     * @param int $expired 过期(单位:分钟)
     * @author james.ou 2017-9-18
     */
    public static function get_data_by_file($filename,$defvalue='',$expired = 1440){
        if(!file_exists($filename)) return $defvalue;

        $filetime = filemtime($filename);
        $cache_key = md5($filename);
        $lastfiletime = CommonClass::cache_op('get',$cache_key,'filetime',0,0);
        $fileContent = CommonClass::cache_op('get',$cache_key,'filecontent',0,0);
        if($filetime > $lastfiletime || empty($fileContent)){
            if(file_exists($filename)){
               $fileContent = file_get_contents($filename);
            }
            if(empty($fileContent)) $fileContent = $defvalue;
            if(!empty($fileContent)){
                CommonClass::cache_op('set',$cache_key,'filetime',0,0,$filetime,$expired*60);
                CommonClass::cache_op('set',$cache_key,'filecontent',0,0,$fileContent,$expired*60);
            }
            LogClass::log_info("CommonClass::get_data_by_file setCache::{$filename},{date('Y-m-d H:i:s',$filetime)}-{date('Y-m-d H:i:s',$lastfiletime)}", __FILE__, __LINE__);
        }
        return $fileContent;
    }

    /**
     * 清空缓存数据及数据、配置文件
     * @param str $filename
     * @param boolean $isdeleted
     * @return boolean
     * @author james.ou 2017-9-18
     */
    public static function clear_data_by_file($filename,$isdeleted = true){
        if(empty($filename)) return false;

        $cache_key = md5($filename);
        CommonClass::cache_op('del',$cache_key,'filetime',0,0);
        CommonClass::cache_op('del',$cache_key,'filecontent',0,0);

        $res = false;
        if(file_exists($filename) && $isdeleted){
            Doo::loadHelper('DooFile');
            $doofile = new DooFile();
            $res = $doofile->delete($filename);
        }
        return $res;
    }

    /**
     * 获取企业微信的数据
     * @param $code
     */
    public static function getCode($code){
    	$res = CommonClass::cache_op('get', $code, EnumClass::PASSAGE_TYPE_WECHAT, 0, 0);
    	if($code){
    		return $res;
    	}else{
    		return false;
    	}
    }

    /**
	 * [arr_to_str 一维数组转字符串，每个元素转义并带引号，元素之间逗号分隔，如：['a', 'b', 'c'] 转换 "'a', 'b', 'c'"]
	 * @param  array  $arr [description]
	 * @return [type]      [description]
	 */
	public static function arr_to_str($arr = array()) {
		if (is_array($arr)) {
			$str = '';
			foreach ($arr as $v) {
				$v1 = '\'' . CommonClass::addslashes_str($v) . '\'';
				if (empty($str)) {
					$str = $v1;
				} else {
					$str .= ',' . $v1;
				};
			};
		} else {
			$str = $arr;
		};
		return $str;
	}

	/**
	 * [arr_column_as_key 二维数组某个字段作为key返回，如：array(array('id'=>123),array('id'=>456)) 转换 array('123'=>array('id'=>'123'),'456'=>array('id'=>'456'))]
	 * @param  array  $list   [description]
	 * @param  string $column [description]
	 * @return [type]         [description]
	 */
	public static function arr_column_as_key($list = array(), $column = '') {
		$new_list = array();
		if (is_array($list) && is_string($column) && !empty($column)) {
			foreach ($list as $v) {
				if (isset($v[$column])) {
					$new_list[$v[$column]] = $v;
				};
			};
		};
		return $new_list;
	}

	/**
	 * 选人入库格式转数组
	 * @param  [type] $version  [description]
	 * @param  [type] $prm_base [description]
	 * @param  [type] $users    [description]
	 * @return [type]           [description]
	 */
	public static function userdata_to_array($version, $prm_base, $users) {
		if (empty($users)) return array();
		$ret = ApiClass::datasource_userdata_to_array($version, $prm_base, $users);
		if ($ret['ret'] == RetClass::SUCCESS) {
			return $ret['data'];
		}
		return array();
	}

	/**
	 * 数组转入库格式
	 * @param  [type] $version  [description]
	 * @param  [type] $prm_base [description]
	 * @param  [type] $users    [description]
	 * @return [type]           [description]
	 */
	public static function array_to_userdata($version, $prm_base, $users) {
		if (empty($users)) return '';
		if (!is_array($users)) {
			return CommonClass::json_encode(array('user'=>array(array('u'=>$users))));
		} else {
			return CommonClass::json_encode(array('user'=>array(array('u'=>implode(',', $users)))));
		}
	}
}
?>
