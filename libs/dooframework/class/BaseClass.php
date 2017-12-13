<?php

/**
 * 基础类
 * General.20141202
 */
class BaseClass
{

	//public static $ret = array('ret' => RetClass::ERROR, 'data' => ''); //返回值数组
	public $advanced_auth; //是否高级授权模式 bool suson.20161222

	public function __construct($params = null)
	{
		$this->params = $params;
	}

	/**
	 * 创建目录文件夹
	 * @param string $filePath
	 * @author ljh 2017-9-1
	 */
	private static function mkDir($filePath)
	{
		if (!file_exists($filePath))
		{
			self::mkDir(dirname($filePath));
			mkdir($filePath, 0777);
		}
	}

	/**
	 * 获取文件夹路径
     * @param str $fileName  文件名称
	 * @param string $filePath	文件夹(目录)路径
	 * @param string $mode		模式
	 * @return string
	 * @author ljh 2017-9-1
	 */
	private static function getFilePath($fileName,$filePath, $mode = '')
	{
		if (empty($filePath))
		{
			$filePath = Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER;
			if (isset(Doo::conf()->PROTECTED_FOLDER_ORI) === true)
			{
				if (file_exists($filePath . "$fileName") === false)
				{
					$filePath = Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER_ORI;
					if (file_exists($filePath . "$fileName") === false) $filePath = Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER;
				}
			}
		}

		if (substr($filePath, -1) != '/')           $filePath .= '/';
		if ($mode == 'write' && !is_dir($filePath)) self::mkDir($filePath); //创建目录文件夹

		return $filePath;
	}

	/**
	 * 读取页面配置config
	 * @param $fileName 配置文件名称(不含扩展名.json)
	 * @return mixed json array
	 * @author ljh 2017-8-31
	 */
	public static function readConfig($fileName, $filePath = '')
	{
		$fileName = $fileName . '.json';
		$filePath = self::getFilePath($fileName,$filePath);
		if (!is_dir($filePath))
		{
			LogClass::log_err("BaseClass_readConfig read错误1::{$filePath}{$fileName},error:目录不存在！", __FILE__, __LINE__);
			return false;
		}
		if (file_exists($filePath . "$fileName") === false)
		{
			LogClass::log_err("BaseClass_readConfig read错误2::{$filePath}{$fileName},error:文件不存在！", __FILE__, __LINE__);
			return false;
		}
		$fileContent = CommonClass::get_data_by_file($filePath.$fileName, '', 24*60);
		if (empty($fileContent) || $fileContent === false)
		{
			LogClass::log_err("BaseClass_readConfig read错误3::{$filePath}{$fileName},error:读取配置异常", __FILE__, __LINE__);
			return false;
		}
		$result = json_decode($fileContent, true);
		if (!$result)
		{
			//error handle ,错误处理
			$ret = json_last_error();
			$err = json_last_error_msg();
			//打印为： 4,查错误信息表，可知是语法错误
			LogClass::log_err("BaseClass_readConfig read错误4::{$filePath}{$fileName},error:[{$ret}]{$err}", __FILE__, __LINE__);
			return false;
		}

		return $result;
	}

	/**
	 * 按模块名和配置名读取配置
	 * HCW 2017.10.25
	 */
	public static function readConfigByModuleid($version, $prm_base, $moduleid, $fileName)
	{
		//应该读每个模块的配置
		$mod_install = ApiClass::enterprise_handle_module_cache($version,$prm_base,$moduleid);
		$mod_path    = isset($mod_install['data']['path']) && !empty($mod_install['data']['path']) ? CommonClass::json_decode($mod_install['data']['path']) : array();
		$type        = isset($mod_path['type']) ? $mod_path['type'] : '';
		$name        = isset($mod_path['name']) ? $mod_path['name'] : '';
		if(empty($type) || empty($name)) return false;

		$path     = Doo::conf()->SITE_PATH."protected/module/{$type}/{$name}/";
		$fileName = $path.$fileName;
		if(file_exists($fileName))
		{
			$data = CommonClass::json_decode(file_get_contents($fileName));
			if($data) return $data;
		}

		return false;
	}

	/**
	 * [保存]写入配置config
	 * @param $fileName 配置文件名称(不含扩展名.json)
	 * @param $config	 配置信息
	 * @return bool true/false
	 * @author ljh 2017-8-31
	 */
	public static function writeConfig($fileName, $filePath = '', $config = array())
	{
		$fileName  = $fileName . '.json';
		$config    = json_encode($config);
		$filePath  = self::getFilePath($fileName,$filePath, 'write');
		$OldConfig = self::readConfig($fileName, $filePath);

		try {
			$result = file_put_contents($filePath . $fileName, $config);
		} catch (Exception $ex) {
			if (!empty($OldConfig)) file_put_contents($filePath . $fileName, $OldConfig);
		}

		return $result === false ? false : true;
	}

	/**
	 * 统一返回格式(如第一个参数已为包含ret和data的数组，则无需拆分传入，但若包含以外的键和值将会去掉)
	 * General.20150206
	 * $ret 返回类型
	 * $data 返回数据
	 * $type 返回类型
	 * $title 提示标题
	 * $content 提示内容
	 * $setdata 提示设置 array('btn'=>array('txt'=>'按钮文本','backurl'=>,'posturl'),'delay'=>1000);
	 * $icon 提示图标
	 */
	public static function ret($ret = RetClass::ERROR, $data = '', $type = EnumClass::RET_TYPE_DATA, $title = '', $content = '', $setdata = '', $icon = 0, $code = 0, $url = '', $theme = EnumClass::TIPS_THEME_DEFAULT, $refresh = 'refresh')
	{
		$return['ret']  = RetClass::ERROR;
		$return['data'] = '';
		$return['type'] = empty($type) ? EnumClass::RET_TYPE_DATA : $type; //类型
		$return['attr'] = array(
			'setdata' => $setdata, //按钮btn、跳转url、延时delay(毫秒)
			'title' => $title,
			'content' => $content,
			'icon' => $icon, //根据$type默认图标
			'theme' => $theme, //主题样式
			'url' => $url, //直接跳转地址
			'refresh' => $refresh, //局部刷新js类型
		); //属性
		$return['code'] = $code; //返回代码

		if (empty($ret)) return $return;

		//General.20150605 如果ret为成功，code为0，title和content都为空时
		/* if(empty($title)){
		  $return['attr']['title'] = CommonClass::txt('TIPS_COM_TITLE');//操作提示
		  } */
		//var_dump($ret);exit;
		//如果$ret为数组
		if (is_array($ret))
		{
			if (isset($ret['ret']) && isset($ret['data']))
			{ //ret和data都存在
				$return['ret'] = $ret['ret'];
				$return['data'] = $ret['data'];
			}
			else if (isset($ret['ret']) && !isset($ret['data']))
			{//ret存在且data不存在
				$return['ret'] = $ret['ret'];
			}
			else if ((!isset($ret['ret']) && !isset($ret['data'])) || (!isset($ret['ret']) && isset($ret['data'])))
			{//ret和data都不存在或data存在ret不存在，可能是空数组或是其他结构的数组
				$return['ret'] = RetClass::ERROR;
			}
			$return['code'] = !empty($ret['code']) ? $ret['code'] : 0;

			//返回类型
			if (isset($ret['type']))            $return['type'] = $ret['type'];
			//跳转地址,当type=EnumClass::RET_TYPE_URL
			if (isset($ret['attr']['url']))     $return['attr']['url'] = $ret['attr']['url'];
	    	if (isset($ret['attr']['setdata'])) $return['attr']['setdata'] = $ret['attr']['setdata'];
			if (isset($ret['attr']['icon']))    $return['attr']['icon'] = $ret['attr']['icon'];
			if (isset($ret['attr']['theme']))   $return['attr']['theme'] = $ret['attr']['theme'];
			if (isset($ret['attr']['title']))   $return['attr']['title'] = $ret['attr']['title'];
			if (isset($ret['attr']['content'])) $return['attr']['content'] = $ret['attr']['content'];
			if (isset($ret['attr']['refresh'])) $return['attr']['refresh'] = $ret['attr']['refresh'];

		}
		else if (is_object($ret))
		{//如果是对象
			$return['ret'] = RetClass::ERROR;
		}
		else
		{
			$return['ret'] = $ret;
			//只要存在不管为不为空都返回，控制器输出到手机时才可能要精简掉
			if (isset($data)) $return['data'] = $data;
		}

		if ($return['code'] == 0)
		{
			if (empty($content) && empty($return['attr']['content']))
			{
				switch ($return['ret'])
				{
					case RetClass::SUCCESS:
						$return['attr']['content'] = CommonClass::txt('TIPS_HANDLE_SUCCESS'); //操作成功
						break;
					case RetClass::ERROR:
						$return['attr']['content'] = CommonClass::txt('TIPS_HANDLE_FAILURE'); //操作失败
						break;
				}
			}
		}

		if ($return['ret'] == RetClass::SUCCESS)
		{
			$return['attr']['theme'] = 'default';
		}
		else
		{
			$return['attr']['theme'] = 'error';
		}

		return $return;
	}

	/**
	 * 压缩html : 清除换行符,清除制表符,去掉注释标记
	 * @param $string
	 * @param $escape 是否转义特殊字符
	 * @return 压缩后的$string
	 * */
	public static function compress_html($string, $escape = 0)
	{
		$string = CommonClass::nl2br($string);
		$string = str_replace("\r\n", '', $string); //清除换行符
		$string = str_replace("\n", '', $string); //清除换行符
		$string = str_replace("\t", '', $string); //清除制表符

		if ($escape === 1) $string = str_replace("\\", '\\\\', $string); //赋值给js变量时需要先转义\

		$pattern = array("/> *([^ ]*) *</", "/[\s]+/", "/<!--[^!]*-->/", "/\" /", "/ \"/", "'/\*[^*]*\*/'");
		$replace = array(">\\1<", " ", "", "\"", "\"", "");

		return preg_replace($pattern, $replace, $string);
	}

	/**
	 * 模块初始化(供各模块主类文件重写该方法)
	 * General.20150819
	 */
	public static function api_initial($version, $prm_base, $params = array())
	{
		return self::ret(RetClass::SUCCESS);
	}

	/**
	 * @author suson 2015-08-28
	 * [ngtable_column ngtable列头格式]
	 * @param  [type]  $version    [description]
	 * @param  [type]  $prm_base   [description]
	 * @param  [type]  $listcolumn [description]
	 * @param  integer $isoperate  [description]
	 * @param  integer $moduleid   [description]
	 * @param  string  $path       [description]
	 * @param  integer $reportpage [description]  reportpage==1代表是报表中心进去的列表页  hky20170426
	 * @return [type]              [description]
	 */
	public static function ngtable_column($version, $prm_base, $listcolumn, $isoperate = 0, $moduleid = 0, $path = '', $reportpage = 0)
	{
		//根据模块id调用各自类里面的方法  hky20170426
		$ret = self::get_ngtable_column($version, $prm_base, $listcolumn, $isoperate, $moduleid, $path, $reportpage);

		if ($ret['ret'] == RetClass::SUCCESS)             return $ret;
		if (empty($listcolumn) || !is_array($listcolumn)) return self::ret();

		//操作栏
		$key = count($listcolumn);
		$listcolumn[$key] = array(
			'title' => '操作', //列名
			'field' => 'operate', //列字段名（作为列表每一条数据的键值，来匹配该列显示的内容）
			'visible' => 0, //设置该列是否显示
			'isoperate' => 4, //该列是否为操作栏  0：非操作栏； 1：ngtable.html里默认的操作； 2：可在父控制器写自定义操作方法（operationClick(opt, value)=操作按钮点击事件，operationShow(opt, value)=操作按钮返回是否显示）；3：2的加强版（增加的功能：某个按钮可以设置成下拉菜单），可参考系统管理列表操作栏
			'issortby' => 0, //该列是否设为可排序
			'isclick' => 0, //该列是否为是否设为超链接  0：非超链接；1：超链接；2：自定义点击方法（customClick(column, value)），可参考系统管理列表某个地方？  【列表每条数据相关的键值为，$value['link']=''，可参考内部邮件】
			'isseticon' => 0, //该列是否显示右侧浮动图标，可参考内部邮件列表内容列  【列表每条数据相关的键值为，图标：$value['righticon']=array()，标签：$value['rightlabel']=array()，可参考内部邮件】
			'issetread' => 0, //设置已阅未阅的红点可标记在该列  【列表每条数据相关的键值为，$value['isread']=true/false，可参考内部邮件】
			'width' => '45px'//列宽度，统一为45px
		);
		foreach ($listcolumn as $k1 => &$v1)
		{
			//列头显示名
			if (isset($v1['display'])) $v1['title'] = $v1['display'];

			//列值对应的key
			if (isset($v1['tag']))
			{
				$v1['field'] = $v1['tag'];
			}
			elseif (isset($v1['name']))
			{ //name,tag是旧数据
				$name = explode('.', $v1['name']);
				$name = isset($name[1]) ? $name[1] : $name[0];
				$v1['field'] = $name;
				// $v1['field'] = trim(strrchr($v1['name'],'.'),'.');
			}
			//是否排序
			if (isset($v1['sortable']))
			{ //sortable是旧数据
				$v1['issortby'] = (int) $v1['sortable'];
			}
			//是否可点击
			//0：非超链接；
			//1：超链接；
			//2：自定义点击方法（customClick(column, value)），可参考系统管理列表某个地方？
			//【列表每条数据相关的键值为，$value['link']=''，可参考内部邮件】
			$v1['isclick'] = isset($v1['isclick']) ? (int) $v1['isclick'] : 0;
			//isblank为1表示新窗口打开
			$v1['isblank'] = isset($v1['isblank']) ? (int) $v1['isblank'] : 0;
			//是否可见
			$v1['visible'] = isset($v1['visible']) ? (int) $v1['visible'] : 1;
			//是否操作栏
			//0：非操作栏；
			//1：ngtable.html里默认的操作；
			//2：可在父控制器写自定义操作方法（operationClick(opt, value)=操作按钮点击事件，operationShow(opt, value)=操作按钮返回是否显示）；
			//3：2的加强版（增加的功能：某个按钮可以设置成下拉菜单），可参考系统管理列表操作栏
			// $v1['isoperate'] = isset($v1['isoperate']) ? (int)$v1['isoperate'] : 0;
			if (!empty($v1['field']) && $v1['field'] == 'operate')
			{
				$v1['isoperate'] = $isoperate;
				if ($isoperate > 0) $v1['visible'] = 1;
			}


			//该列是否显示右侧浮动图标，可参考内部邮件列表内容列
			//【列表每条数据相关的键值为，图标：$value['righticon']=array()，标签：$value['rightlabel']=array()，可参考内部邮件】
			$v1['isseticon'] = isset($v1['isseticon']) ? (int) $v1['isseticon'] : 0;
			//设置已阅未阅的红点可标记在该列  【列表每条数据相关的键值为，$value['isread']=true/false，可参考内部邮件】
			$v1['issetread'] = isset($v1['issetread']) ? (int) $v1['issetread'] : 0;

			//设置列宽 默认不给宽度
			if (isset($v1['width']))   $v1['width'] = $v1['width'];
			if (!empty($v1['fldset'])) continue;

			//由itemname生成fldset
			if (!empty($v1['itemname']) && isset($v1['formid']))
			{
				$fldsetret = FormClass::get_formitem_fldset($version, $prm_base, $v1['formid'], $v1['itemname'], $v1['field']);
				if ($fldsetret['ret'] == RetClass::SUCCESS) $v1['fldset'] = $fldsetret['data'];

				continue;
			}
		}

		return self::ret(RetClass::SUCCESS, $listcolumn);
	}

	/**
	 *  @author suson 2015-08-28
	 * [ngtable_content ngtable列内容处理]
	 * @param  [type]  $version    [description]
	 * @param  [type]  $prm_base   [description]
	 * @param  [type]  $result     [每一页的记录集]
	 * @param  [type]  $columns    [ngtable_columns返回的列头]
	 * @param  string  $path       [description]
	 * @param  integer $page       [description]
	 * @param  string  $pk         [description]
	 * @param  string  $moduleid   [description]
	 * @param  integer $reportpage [description]   reportpage==1代表是报表中心进去的列表页  hky20170426
	 * @return [type]              [description]
	 */
	public static function ngtable_content($version, $prm_base, $result, $columns, $path = '', $page = 1, $pk = '', $moduleid = '', $reportpage = 0)
	{
		//根据模块id调用各自类里面的方法  hky20170426
		$ret = self::get_ngtable_content($version, $prm_base, $result, $columns, $path, $page, $pk, $moduleid, $reportpage);
		if ($ret['ret'] == RetClass::SUCCESS) return $ret;

		if (empty($result) || !is_array($result) || empty($columns) || !is_array($columns)) return self::ret();


		$i = 1;
		$ng_result = array(); //返回给列表控件的数据、尽量不是整个row返回
		// 格式化数据
		foreach ($result as $k => $row)
		{
			$ng_row = array();
			// angularjs tracked by id  列表控件以id做key，必须返回id
			if (isset($row['id']))
			{
				$ng_row['id'] = $row['id'];
			}
			else
			{
				$ng_row['id'] = $i ++;
			}

			//报表专用
			if (isset($row['pkid']) && !isset($row['id']))
			{
				$ng_row['id'] = $row['pkid'];
				$row['id'] = $ng_row['id'];
			}

			if (!empty($pk) && isset($row[$pk]))
			{
				$ng_row[$pk] = $row[$pk];
				$ng_row['id'] = $row[$pk];
			}

			if (isset($row['formtitle'])) $ng_row['formtitle'] = $row['formtitle'];

			//已阅红点
			$red              = ApiClass::user_is_read('3.0.0.0', $prm_base, $moduleid, EnumClass::OP_TYPE_READ, $ng_row['id']);
			$ng_row['isread'] = $red['ret'] == RetClass::SUCCESS ? true : false;

			//遍历列头
			foreach ($columns as $colomn)
			{
				$tag          = $colomn['field'];
				$tag_val_arr  = array();
				$ng_row[$tag] = isset($row[$tag]) ? $row[$tag] : '';
				if (empty($colomn['fldset'])) continue;

				$fldset     = $colomn['fldset'];
				$fields     = isset($fldset['fields']) ? $fldset['fields'] : array();
				$key_format = !empty($fldset['format']) ? $fldset['format'] : '';
				//获取列头对应字段配置
				foreach ($fields as $field)
				{
					$args             = !empty($field['args']) ? $field['args'] : array();
					$fldname          = !empty($field['fldname']) ? $field['fldname'] : '';
					$type             = isset($args['type']) ? $args['type'] : '';
					$format           = isset($args['format']) ? $args['format'] : '';
					$othername        = isset($args['othername']) ? $args['othername'] : '';
					$datasource       = isset($args['datasource']) ? $args['datasource'] : array();
					$dataselectsource = isset($args['dataselectsource']) ? $args['dataselectsource'] : array();
					$defname          = isset($args['defname']) ? $args['defname'] : '';  //查询不到人名或部门名时默认显示的值  hky 20160616
					$temparr         = array(EnumClass::STATE_TYPE_NORMAL);

					//用户id转姓名 不为空显示被删除人员 Xiaogq2017.02.22
					if (isset($args['showdel'])) $temparr = array(EnumClass::STATE_TYPE_NORMAL, EnumClass::STATE_TYPE_DELETE, EnumClass::STATE_TYPE_ABSDELETE);

					$link     = isset($args['link']) && $args['link'] ? 1 : 0;  //是否要超链接（还需要在对应方法上加上format_link）  hky 20160726
					$json_key = isset($args['json_key']) ? $args['json_key'] : '';
					if (empty($type))
					{
						$fldval                = isset($row[$fldname]) ? $row[$fldname] : '';
						$tag_val_arr[$fldname] = $fldval;
						continue;
					}

					switch ($type)
					{
						case 'userid':
							$fldval = isset($row['keydata']) ? $row['keydata'] : '';
							self::format_userid($version, $prm_base, $fldval, $fldname, $tag_val_arr);
							break;
						case 'userid_to_title':
							//先链接
							self::format_link($version, $prm_base, $row, $fldname, $tag_val_arr, $format, $path, $page);
							//再处理userid_to_name
							$fldval = isset($row[$fldname]) ? $row[$fldname] : ''; //add hky 20160722
							$fldval = isset($row['owner']) ? $row['owner'] : $fldval; //暂时写死，目前就绩效管理会用到，HCW 2016.01.25
							self::format_userid_to_name($version, $prm_base, $fldval, $fldname, $tag_val_arr);
							break;
						case 'deptid':
							$fldval = isset($row['keydata']) ? $row['keydata'] : '';
							self::format_deptid($version, $prm_base, $fldval, $fldname, $tag_val_arr);
							break;
						case 'params':
							$fldval = isset($row[$fldname]) ? $row[$fldname] : '';
							self::format_params($version, $prm_base, $fldval, $fldname, $tag_val_arr);
							break;
						case 'enum':
							$fldval = isset($row[$fldname]) ? $row[$fldname] : '';
							self::format_enum($version, $prm_base, $fldval, $fldname, $tag_val_arr, $datasource);
							break;
						case 'datetime':
							$fldval = isset($row[$fldname]) ? $row[$fldname] : '';
							self::format_datetime($version, $prm_base, $fldval, $fldname, $tag_val_arr, $format);
							break;
						case 'link':
							self::format_link($version, $prm_base, $row, $fldname, $tag_val_arr, $format, $path, $page, $args);
							//var_dump($tag_val_arr);exit;
							break;
						case 'venue':
							$fldval = isset($row[$fldname]) ? $row[$fldname] : '';
							self::format_venue($version, $prm_base, $fldval, $fldname, $tag_val_arr);
							break;
						case 'userdata_to_name':
							if ($link) {
								//先链接
								self::format_link($version, $prm_base, $row, $fldname, $tag_val_arr, $format, $path, $page, $args);
							}
							$fldval = isset($row[$fldname]) ? $row[$fldname] : '';
							self::format_userdata_to_name($version, $prm_base, $fldval, $fldname, $tag_val_arr, $defname, 0, $temparr);
							break;
						case 'userdata_all_to_name'://获取人员数据，不去重
							if ($link) {
								//先链接
								self::format_link($version, $prm_base, $row, $fldname, $tag_val_arr, $format, $path, $page, $args);
							}
							$fldval = isset($row[$fldname]) ? $row[$fldname] : '';
							self::format_userdata_to_name($version, $prm_base, $fldval, $fldname, $tag_val_arr, $defname, 1, $temparr);
							break;
						case 'check_title':
							if ($link) {
								//先链接
								self::format_link($version, $prm_base, $row, $fldname, $tag_val_arr, $format, $path, $page, $args);
							}
							$fldval = isset($row[$fldname]) ? $row[$fldname] : '';
							self::format_check_title($version, $prm_base, $fldval, $fldname, $tag_val_arr, $defname, 1, '', $extparams = array('row' => $row));
							break;
						case 'visit_title':
							self::format_link($version, $prm_base, $row, $fldname, $tag_val_arr, $format, $path, $page, $args);
							self::format_visit_title($version, $prm_base, $row, $fldname, $tag_val_arr);
							break;
						case 'visit_sign':
							self::format_visit_sign($version, $prm_base, $row, $tag, $fldname, $tag_val_arr);
							break;
						case 'new_visit_time':
							self::format_new_visit_time($version, $prm_base, $row, $tag, $fldname, $tag_val_arr, $args);
							break;
						case 'new_visit_sign':
							self::format_new_visit_sign($version, $prm_base, $row, $tag, $fldname, $tag_val_arr, $args);
							break;
						case 'process_remark':
							self::format_link($version, $prm_base, $row, $fldname, $tag_val_arr, $format, $path, $page, $args);
							self::format_process_remark($version, $prm_base, $row, $tag_val_arr);
							break;
						case 'issue_remark':
							self::format_link($version, $prm_base, $row, $fldname, $tag_val_arr, $format, $path, $page, $args);
							self::format_issue_remark($version, $prm_base, $row, $tag_val_arr);
							break;
						case 'dataradio':
							$fldval = isset($row[$fldname]) ? $row[$fldname] : '';
							self::format_dataradio($version, $prm_base, $fldval, $fldname, $dataselectsource, $tag_val_arr);
							break;
						case 'time':
							$fldval = isset($row[$fldname]) ? $row[$fldname] : '';
							self::format_time($version, $prm_base, $fldval, $fldname, $tag_val_arr, $format);
							break;
						case 'pm_link':
							$pm_format = isset($args['pm_format']) ? $args['pm_format'] : array();
							self::format_pm_link($version, $prm_base, $row, $fldname, $tag_val_arr, $pm_format, $path, $page);
							break;
						case 'pm_dot':
							self::format_pm_dot($version, $prm_base, $row, $fldname, $tag_val_arr, $format, $path, $page);
							break;
						case 'titletype':
							$fldval = isset($row[$fldname]) ? $row[$fldname] : '';
							self::format_titletype($version, $prm_base, $fldval, $fldname, $tag_val_arr);
							break;
						case 'meeting_chairuser':
							$fldval = isset($row[$fldname]) ? $row[$fldname] : '';
							$chair_out = isset($row['extchairuser']) ? $row['extchairuser'] : '';
							self::format_meeting_chairuser($version, $prm_base, $fldval, $fldname, $tag_val_arr, $defname, $chair_out);
							break;
						case 'custom':
							$fldval = isset($row[$fldname]) ? $row[$fldname] : '';
							$tag_val_arr = self::format_custom($version, $prm_base, $fldval, $fldname, $moduleid, '', $row);
							break;
						case 'json':
							$fldval = isset($row[$fldname]) ? $row[$fldname] : '';
							self::format_json($version, $prm_base, $fldval, $fldname, $tag_val_arr, $json_key);
							break;
						case 'report_type':
							$fldval = isset($row[$fldname]) ? $row[$fldname] : '';
							self::format_report_type($version, $prm_base, $fldval, $fldname, $tag_val_arr);
							break;
						case 'current_time':
							$start_time = isset($row[$fldname]) ? $row[$fldname] : '';
							$end_time = isset($row[$othername]) ? $row[$othername] : '';
							self::format_current_time($version, $prm_base, $start_time, $end_time, $fldname, $othername, $tag_val_arr);
							break;
						default:
							$fldval = isset($row[$fldname]) ? $row[$fldname] : '';
							$tag_val_arr[$fldname] = $fldval;
							//var_dump($fldval);exit;
							$handler = array('BaseClass', 'format_' . $type);
							if (is_callable($handler)) {
								$format_function_params = array($version, $prm_base, $fldval, $fldname, &$tag_val_arr, $format, $path, $page, $extparams = array('row' => $row));
								$format_val = call_user_func_array($handler, $format_function_params);
							}

							break;
					}
				}

				if (!empty($key_format))
				{
					$arraykeys = array_keys($tag_val_arr);
					$arraykeys = array_map(function($n) {
						return $n = '#' . $n . '#';
					}, $arraykeys);
					$arrayvals = array_values($tag_val_arr);

					$new_tag_val_arr = array_combine($arraykeys, $arrayvals);
					$key_format      = strtr($key_format, $new_tag_val_arr);
					// $row[$tag]    = $key_format;
					$ng_row[$tag]    = $key_format;
				}
				else
				{
					if (is_array($tag_val_arr))
					{
						// $row[$tag] = implode('|', $tag_val_arr);
						$ng_row[$tag] = implode('|', $tag_val_arr);
					}
					else
					{
						// $row[$tag] = $tag_val_arr;
						$ng_row[$tag] = $tag_val_arr;
					}
				}
				//var_dump($tag_val_arr);exit;
				//合并附加的key
				// var_dump($ng_row);
				$ng_row = array_merge($ng_row, $tag_val_arr);
			}
			$ng_result[] = $ng_row; //只返回需要的列头
		}

		return self::ret(RetClass::SUCCESS, $ng_result);
	}

	//==================================   列表里面使用到的format(start) ==============================================

	/**
	 * @author ljh 2016-8-11
	 * [format_custom 自定义格式化参数值为文本]
	 * @param  string $version		版本号
	 * @param  object $prm_base
	 * @param  string $fldval		要处理的值
	 * @param  string $field		字段名称
	 * @param  string $moduleid		模块id
	 * @param  string $entid		企业id
	 * @return array
	 */
	public static function format_custom($version, $prm_base, $fldval, $field, $moduleid = '', $entid = '', $row = array())
	{
		$result  = array($field => '');
		$entid   = !empty($entid) ? $entid : $prm_base->entid;
		$modpath = ApiClass::enterprise_get_module_path($version, $prm_base, $moduleid, $path = '', $entid);

		if ($modpath['ret'] == RetClass::SUCCESS && isset($modpath['data']['type']) && isset($modpath['data']['name']))
		{
			$params = array('field' => $field, 'fldval' => $fldval, 'row' => $row);
			$ret    = Doo::getModuleClass($modpath['data']['type'] . '/' . $modpath['data']['name'], ucfirst($modpath['data']['name']) . 'Class', 'format_' . $field, '3.0.0.0', $prm_base, $params);
			if ($ret['ret'] == RetClass::SUCCESS) $result[$field] = $ret['data'];
		}

		return $result;
	}

	/**
	 * @author suson 2015-08-28
	 * [format_userid 格式化当前人userid为username]
	 * @param  [type] $version      [description]
	 * @param  [type] $prm_base     [description]
	 * @param  [type] $fldval       [要处理的值]
	 * @param  [type] $field        []
	 * @param  [type] &$tag_val_arr [description]
	 * @return [type]               [description]
	 */
	public static function format_userid($version, $prm_base, $fldval, $field, &$tag_val_arr = array())
	{
		$keydata_arr         = CommonClass::json_decode($fldval);
		//替换创建人
		$tag_val_arr[$field] = isset($keydata_arr['username']) ? $keydata_arr['username'] : '';

		return $tag_val_arr[$field];
	}

	/**
	 * @param  [type] $version      [description]
	 * @param  [type] $prm_base     [description]
	 * @param  [type] $fldval       [description]
	 * @param  [type] $field        [description]
	 * @param  array  &$tag_val_arr [description]
	 * @return [type]               [description]
	 */
	public static function format_moduleid($version, $prm_base, $fldval, $field, &$tag_val_arr = array())
	{
		$rets                = ApiClass::enterprise_handle_module_cache($version, $prm_base, $fldval);
		$tag_val_arr[$field] = isset($rets['data']['mname']) ? $rets['data']['mname'] : '';
		return $tag_val_arr[$field];
	}

	/**
	 * @param  [type] $version      [description]
	 * @param  [type] $prm_base     [description]
	 * @param  [type] $fldval       [description]
	 * @param  [type] $field        [description]
	 * @param  array  &$tag_val_arr [description]
	 * @return [type]               [description]
	 */
	public static function format_terminal($version, $prm_base, $fldval, $field, &$tag_val_arr = array())
	{
		$terminaltxt         = CommonClass::get_terminal($fldval);
		$tag_val_arr[$field] = $terminaltxt;
		return $tag_val_arr[$field];
	}

	public static function format_json($version, $prm_base, $fldval, $field, &$tag_val_arr, $json_key)
	{
		$keydata_arr         = CommonClass::json_decode($fldval);
		//替换指定key
		$tag_val_arr[$field] = isset($keydata_arr[$json_key]) ? $keydata_arr[$json_key] : '';
		return $tag_val_arr[$field];
	}

	public static function format_report_type($version, $prm_base, $fldval, $field, &$tag_val_arr)
	{
		//$keydata_arr = CommonClass::json_decode($fldval);
		$type_name           = ApiClass::report_type_to_string($version, $prm_base, $fldval);
		//替换指定key
		$tag_val_arr[$field] = isset($type_name['data']) ? $type_name['data'] : '';
		return $tag_val_arr[$field];
	}

	public static function format_userid_to_name($version, $prm_base, $fldval, $field, &$tag_val_arr = array())
	{
		$state     = array(EnumClass::STATE_TYPE_NORMAL, EnumClass::STATE_TYPE_DELETE);
		$user_info = ApiClass::user_get_info($version, $prm_base, $fldval, 'profname', 'get', '', 'desc=profsort', $state);

		$tag_val_arr[$field]  = $user_info['ret'] == RetClass::SUCCESS ? $user_info['data']['profname'] : '';
		$tag_val_arr['title'] = isset($tag_val_arr[$field]) ? $tag_val_arr[$field] : '';
		return $tag_val_arr[$field];
	}

	/**
	 * @author suson 2015-08-28
	 * [format_params 格式化参数表id为文本]
	 * @param  [type] $version      [description]
	 * @param  [type] $prm_base     [description]
	 * @param  [type] $row          [某一笔记录]
	 * @param  [type] $field        [字段]
	 * @param  array  &$tag_val_arr [替换后的值放到改数组]
	 * @return [type]               [description]
	 */
	public static function format_params($version, $prm_base, $fldval, $field, &$tag_val_arr = array())
	{
		$paramid    = $fldval;
		$paramids   = explode(',', $paramid);
		$paramnames = array();
		//每个paramid以后做缓存，分次取效率是否高？
		foreach ($paramids as $v)
		{
			$param_ret    = ApiClass::params_getone_byid($version, $prm_base, $v);
			$paramnames[] = $param_ret['data'];
		}

		$tag_val_arr[$field] = implode(',', $paramnames);
		return $tag_val_arr[$field];
	}

	/**
	 * @author suson 2015-08-28
	 * [format_datetime 格式化日期显示方式]
	 * @param  [type] $version      [description]
	 * @param  [type] $prm_base     [description]
	 * @param  [type] $row          [某一笔记录]
	 * @param  [type] $field        [字段]
	 * @param  array  &$tag_val_arr [替换后的值放到改数组]
	 * @param  [type] $formatType   [description]
	 * @return [type]               [description]
	 */
	public static function format_datetime($version, $prm_base, $fldval, $field, &$tag_val_arr = array(), $formatType = EnumClass::DATETIME_FORMAT_NO_SECOND)
	{
		//需求上，非正确格式的日期都显示为空
		$formatType = empty($formatType) ? EnumClass::DATETIME_FORMAT_NO_SECOND : $formatType;
		if (empty($fldval))
		{
			$tag_val_arr[$field] = '';
		}
		else
		{
			$tag_val_arr[$field] = CommonClass::get_datetime($fldval, $type = EnumClass::DATETIME_TYPE_NONE, $formatType);
			if (strpos($tag_val_arr[$field], '1970') !== false) $tag_val_arr[$field] = '';
		}

		return $tag_val_arr[$field];
	}

	/**
	 * @author ljh 2016-06-12
	 * [format_datetime 格式化日期显示方式]
	 * @param  [type] $version      [description]
	 * @param  [type] $prm_base     [description]
	 * @param  [type] $row          [某一笔记录]
	 * @param  [type] $field        [字段]
	 * @param  array  &$tag_val_arr [替换后的值放到改数组]
	 * @param  [type] $formatType   [description]
	 * @return [type]               [description]
	 */
	public static function format_date1($version, $prm_base, $fldval, $field, &$tag_val_arr = array(), $formatType = EnumClass::DATETIME_FORMAT_DATE_ONLY)
	{
		//需求上，非正确格式的日期都显示为空
		$formatType = empty($formatType) ? EnumClass::DATETIME_FORMAT_DATE_ONLY : $formatType;
		if (empty($fldval))
		{
			$tag_val_arr[$field] = '';
		}
		else
		{
			$tag_val_arr[$field] = CommonClass::get_datetime($fldval, $type = EnumClass::DATETIME_TYPE_NONE, $formatType);
			if (strpos($tag_val_arr[$field], '1970') !== false) $tag_val_arr[$field] = '';
		}

		return $tag_val_arr[$field];
	}

	/**
	 * @author suson 2015-08-28
	 * [format_link 格式化链接地址]
	 * @param  [type] $version      [description]
	 * @param  [type] $prm_base     [description]
	 * @param  [type] &$row         [某一笔记录]
	 * @param  [type] $field        [字段]
	 * @param  array  &$tag_val_arr [替换后的值放到改数组]
	 * @param  string $template     [description]
	 * @return [type]               [description]
	 */
	public static function format_link($version, $prm_base, $row, $field, &$tag_val_arr = array(), $template = '', $path = '', $page = 1, $args = array())
	{
		$baseUrl     = trim($prm_base->url, '/');
		$row['path'] = $path;
		$row['page'] = $page;
		$t = preg_replace_callback('/#(\w+)#/', function($matches) use($row) {
			if (isset($row[$matches[1]])) {
				return $row[$matches[1]];
			}
		}, $template);
		$tag_val_arr['link']  = $baseUrl . $t;
		$tag_val_arr['title'] = isset($row[$field]) ? $row[$field] : '';
		$tag_val_arr[$field]  = isset($row[$field]) ? $row[$field] : ''; //这个需要加  suson 20151124
		if (!empty($args['moduleid']))
		{

			// switch ($args['moduleid']) {
			// 	case '96679686402257752':
			// 		if(isset($row['m96679686402257752_sort']) && $row['m96679686402257752_sort'] == -10000){
			// 			$tag_val_arr[$field] = '<span class="v3-icon ico-settop" title="置顶"></span>'.$tag_val_arr[$field];
			// 		}
			// 		break;
			// 	default:
			// 		# code...
			// 		break;
			// }
			$function = 'module_title_icon';
			$params   = array(
				'row' => $row,
				'value' => $tag_val_arr[$field]
			);
			$ret      = BaseClass::do_module_api($version, $prm_base, $args['moduleid'], $function, $params);
			if ($ret['ret'] == RetClass::SUCCESS && isset($ret['data'])) $tag_val_arr[$field] = $ret['data'];
		}

		return $tag_val_arr[$field];
	}

	/**
	 * @author LL 2016-06-14
	 * [format_pm_dot 格式化项目管理模块-急需跟踪列表的红点]
	 * @param  [type] $version      [description]
	 * @param  [type] $prm_base     [description]
	 * @param  [type] &$row         [某一笔记录]
	 * @param  [type] $field        [字段]
	 * @param  array  &$tag_val_arr [替换后的值放到改数组]
	 * @param  string $template     [description]
	 * @return [type]               [description]
	 */
	public static function format_pm_dot($version, $prm_base, $row, $field, &$tag_val_arr = array(), $template = '', $path = '', $page = 1)
	{
		$style = '';
		$day   = (time() - strtotime($row['track_time'])) / (24 * 60 * 60); // 当前时间距离上次跟踪时间的天数
		switch ($row['cateid'])
		{
			case '1'://在谈项目 45天后进入提醒期
			case '3'://市主要领导会见企业
			case '4'://事项
				if ($day > 90)
				{//距离上次跟踪时间大于90天
					$style = 'style="background-color:red"';
				}
				else
				{
					$style = 'style="background-color:yellow"'; //急需跟踪换成黄色标记
				}
				break;
			case '2'://在跟企业 137天后进入提醒期
				if ($day > (365 / 2))
				{//距离上次跟踪时间大于半年
					$style = 'style="background-color:red"';
				}
				else
				{
					$style = 'style="background-color:yellow"';
				}
				break;
			default:
				# code...
				break;
		}
		$tag_val_arr[$field] = '<span class="ico-new-o" ' . $style . '></span>' . $tag_val_arr[$field];
		return $tag_val_arr[$field];
	}

	/**
	 * @author suson 2015-08-28
	 * [format_enum 格式化枚举值]
	 * @param  [type] $version      [description]
	 * @param  [type] $prm_base     [description]
	 * @param  [type] $row          [某一笔记录]
	 * @param  [type] $field        [字段]
	 * @param  array  &$tag_val_arr [替换后的值放到改数组]
	 * @param  [type] $enums        [菜单表里面配置的datasource]
	 * @return [type]               [description]
	 */
	public static function format_enum($version, $prm_base, $fldval, $field, &$tag_val_arr = array(), $enums)
	{
		if (isset($enums[$fldval]))
		{
			$tag_val_arr[$field] = $enums[$fldval];
		}
		else
		{
			$tag_val_arr[$field] = $fldval;
		}

		return $tag_val_arr[$field];
	}

	/**
	 * @author suson 2015-08-28
	 * [format_venue 格式化会议室id为文本值]
	 * @param  [type] $version      [description]
	 * @param  [type] $prm_base     [description]
	 * @param  [type] $row          [某一笔记录]
	 * @param  [type] $field        [字段]
	 * @param  array  &$tag_val_arr [替换后的值放到改数组]
	 * @return [type]               [description]
	 */
	public static function format_venue($version, $prm_base, $fldval, $field, &$tag_val_arr = array())
	{
		$venue     = ApiClass::get_venue_by_id($version, $prm_base, $fldval, 'title,remark');
		$venue_txt = ($venue['ret'] == RetClass::SUCCESS) ? $venue['data']['title'] : '';

		$tag_val_arr[$field] = $venue_txt;
		return $tag_val_arr[$field];
	}

	/**
	 * @author suson 2015-08-28
	 * [format_userdata_to_name 格式化选人格式值为人名]
	 * @param  [type] $version      [description]
	 * @param  [type] $prm_base     [description]
	 * @param  [type] $field        [字段]
	 * @param  array  &$tag_val_arr [替换后的值放到改数组]
	 * @param  [type] $defname      [查询不到人名或部门名时默认显示的值]  hky 20160616
	 * @return [type]               [description]
	 */
	public static function format_userdata_to_name($version, $prm_base, $fldval, $field, &$tag_val_arr = array(), $defname = '', $unique = 0, $state = array(EnumClass::STATE_TYPE_NORMAL))
	{
		$userdata     = ApiClass::datasource_userdata_to_name($version, $prm_base, $fldval, 1, 0, 0, $unique, $state);
		$userdata_txt = ($userdata['ret'] == RetClass::SUCCESS) ? $userdata['data'] : '';

		if (empty($userdata_txt))
		{
			//pwj  2017年7月3日 17:01:03  其他类型的也处理(暂时多加一个r类型的)
			//如果为空，则查部门名，暂时这样，待优化
			$userdata = ApiClass::datasource_userdata_change_name($version, $prm_base, $fldval, $key = 'd');
			if ($userdata['ret'] == RetClass::SUCCESS)
			{
				$userdata_txt = implode(',', $userdata['data']);
				$userdata_txt = rtrim($userdata_txt, ',');
			}
			else
			{
				$roledata = ApiClass::datasource_userdata_change_name($version, $prm_base, $fldval, $key = 'r');
				if ($roledata['ret'] == RetClass::SUCCESS) {
					$userdata_txt = implode(',', $roledata['data']);
					$userdata_txt = rtrim($userdata_txt, ',');
				}
			}
		}

		$userdata_txt        = empty($userdata_txt) ? $defname : $userdata_txt;
		$tag_val_arr[$field] = $userdata_txt;

		return $tag_val_arr[$field];
	}

	/**
	 * HCW 2015.09.12
	 * @param  [type] $version      [description]
	 * @param  [type] $prm_base     [description]
	 * @param  [type] $row          [description]
	 * @param  [type] $field        [description]
	 * @param  array  &$tag_val_arr [description]
	 * @return [type]               [description]
	 */
	public static function format_visit_title($version, $prm_base, $row, $field, &$tag_val_arr = array())
	{
		$visit_title = ApiClass::visit_get_title('3.0.0.0', $prm_base, $row);
		$title       = $visit_title['ret'] == RetClass::SUCCESS ? $visit_title['data'] : '';

		$tag_val_arr['title'] = $title;
		return $tag_val_arr['title'];
	}

	/**
	 * HCW 2016.01.20
	 * @param  [type] $version      [description]
	 * @param  [type] $prm_base     [description]
	 * @param  [type] $row          [description]
	 * @param  [type] $tag          [description]
	 * @param  [type] $field        [description]
	 * @param  array  &$tag_val_arr [description]
	 * @return [type]               [description]
	 */
	public static function format_visit_sign($version, $prm_base, $row, $tag, $field, &$tag_val_arr = array())
	{
		$signInfo = isset($row['visitsign_setdata']) ? CommonClass::json_decode($row['visitsign_setdata']) : array();
		//导出选字段时
		if (empty($signInfo) && !empty($row[$tag]) && is_string($row[$tag])) $signInfo = isset($row[$tag]) ? CommonClass::json_decode($row[$tag]) : array();

		$tag_val_arr[$tag] = isset($signInfo[$field]) ? $signInfo[$field] : '';
		return $tag_val_arr[$tag];
	}

	/**
	 * 新客户拜访用
	 * @param  [type] $version      [description]
	 * @param  [type] $prm_base     [description]
	 * @param  [type] $row          [description]
	 * @param  [type] $tag          [description]
	 * @param  [type] $field        [description]
	 * @param  array  &$tag_val_arr [description]
	 * @return [type]               [description]
	 */
	public static function format_new_visit_sign($version, $prm_base, $row, $tag, $field, &$tag_val_arr = array(), $args)
	{
		$signInfo          = isset($row[$field]) ? CommonClass::json_decode($row[$field]) : array();
		$data_field        = isset($args['data_field']) ? $args['data_field'] : '';
		$data_type         = isset($args['data_type']) ? $args['data_type'] : '';
		$tag_val_arr[$tag] = isset($signInfo[$data_field]) ? $signInfo[$data_field] : '';

		if ($data_type == 'signout')
		{
			$other = ApiClass::enterprise_setting_get($version, $prm_base, EnumClass::VISIT_OTHER_CHECK_PERMIT, '96933927880491207', $prm_base->entid, 0, 0, 0, 'valuechar');
			$other = $other['ret'] == RetClass::SUCCESS ? CommonClass::json_decode($other['data']['valuechar']) : array();
			$need_signout = isset($other['need_signout']['isallow']) ? $other['need_signout']['isallow'] : false; //默认不需要签退
			if (!$need_signout) $tag_val_arr[$tag] = '没有要求签退';
		}

		return $tag_val_arr[$tag];
	}

	/**
	 * 新客户拜访用，拜访时长
	 * HCW 2007.03.16
	 * @param  [type] $version      [description]
	 * @param  [type] $prm_base     [description]
	 * @param  [type] $row          [description]
	 * @param  [type] $tag          [description]
	 * @param  [type] $field        [description]
	 * @param  array  &$tag_val_arr [description]
	 * @param  [type] $args         [description]
	 * @return [type]               [description]
	 */
	public static function format_new_visit_time($version, $prm_base, $row, $tag, $field, &$tag_val_arr = array(), $args)
	{
		$signin_time_field  = isset($args['signin_time_field']) ? $args['signin_time_field'] : '';
		$signout_time_field = isset($args['signout_time_field']) ? $args['signout_time_field'] : '';
		$signin_time        = isset($row[$signin_time_field]) ? $row[$signin_time_field] : '';
		$signout_time       = isset($row[$signout_time_field]) ? $row[$signout_time_field] : '';
		$visit_time         = strtotime($signout_time) - strtotime($signin_time);
		$visit_time_str     = '';
		$visit_time_str     = floor($visit_time / (60 * 60 * 24)) > 0 ? floor($visit_time / (60 * 60 * 24)) . '天' : '';

		if (empty($visit_time_str)) $visit_time_str = floor($visit_time / (60 * 60)) > 0 ? floor($visit_time / (60 * 60)) . '小时' : '';
		if (empty($visit_time_str)) $visit_time_str = floor($visit_time / (60)) > 0 ? floor($visit_time / (60)) . '分钟' : '';
		if (empty($visit_time_str)) $visit_time_str = $visit_time > 0 ? $visit_time . '秒' : '';

		$tag_val_arr[$tag] = $visit_time_str;
		return $tag_val_arr[$tag];
	}

	/**
	 * HCW 2015.10.14
	 * @param  [type] $version      [description]
	 * @param  [type] $prm_base     [description]
	 * @param  [type] $row          [description]
	 * @param  array  &$tag_val_arr [description]
	 * @return [type]               [description]
	 */
	public static function format_process_remark($version, $prm_base, $row, &$tag_val_arr = array())
	{
		if (!empty($row['formtitle']))
		{
			$tag_val_arr['title'] = $row['formtitle'];
		}
		elseif (empty($tag_val_arr['remark']) && isset($row['crtime']) && isset($row['title']))
		{
			$username = CommonClass::json_decode($row['keydata']);
			$username = $username['username'];
			$tag_val_arr['title'] = $username . CommonClass::dateFormat($row['crtime'], 'Y-m-d') . '的' . $row['title'];
		}
		else
		{
			$tag_val_arr['title'] = $tag_val_arr['remark'];
		}
	}

	/**
	 * HCW 2015.10.14
	 * @param  [type] $version      [description]
	 * @param  [type] $prm_base     [description]
	 * @param  [type] $row          [description]
	 * @param  array  &$tag_val_arr [description]
	 * @return [type]               [description]
	 */
	public static function format_issue_remark($version, $prm_base, $row, &$tag_val_arr = array())
	{
		if (empty($tag_val_arr['remark']))
		{
			$username             = CommonClass::json_decode($row['keydata']);
			$username             = $username['username'];
			$tag_val_arr['title'] = $username . CommonClass::dateFormat($row['crtime'], 'Y-m-d') . '的' . $row['title'];
		}
		else
		{
			$tag_val_arr['title'] = $tag_val_arr['remark'];
		}
	}

	/**
	 * 格式化列表数据
	 * hky 20160328
	 * @param  [type] $version      [description]
	 * @param  [type] $prm_base     [description]
	 * @param  [type] $fldval       [description]
	 * @param  [type] $field        [description]
	 * @param  array  &$tag_val_arr [description]
	 * @param  [type] $formatType   [description]
	 * @return [type]               [description]
	 */
	public static function format_time($version, $prm_base, $fldval, $field, &$tag_val_arr = array(), $formatType = EnumClass::DATETIME_FORMAT_NO_SECOND)
	{
		//需求上，非正确格式的日期都显示为空
		$formatType = empty($formatType) ? EnumClass::DATETIME_FORMAT_YEAR_MONTH : $formatType;
		if (empty($fldval))
		{
			$tag_val_arr[$field] = '';
		}
		else
		{
			$tag_val_arr[$field] = CommonClass::get_datetime($fldval, $type = EnumClass::DATETIME_TYPE_NONE, $formatType);
			if (strpos($tag_val_arr[$field], '1970') !== false) $tag_val_arr[$field] = '';
		}

		return $tag_val_arr[$field];
	}

	//取创建人名
	public static function format_create_userid($version, $prm_base, $fldval, $field, &$tag_val_arr = array(), $format = '', $path = '', $page = '', $extparams = array())
	{
		$keydata_arr         = CommonClass::json_decode($fldval);
		//替换创建人
		// var_dump($tag_val_arr);
		$tag_val_arr[$field] = '';
		switch ($format)
		{
			case 'userid':
				$tag_val_arr[$field] = isset($keydata_arr['username']) ? $keydata_arr['username'] : '';
				if (empty($tag_val_arr[$field]) && !empty($extparams['row']['userid']))
				{
					//keydata为空再查一下人名
					$state               = array(EnumClass::STATE_TYPE_NORMAL, EnumClass::STATE_TYPE_DELETE);
					$user_info           = ApiClass::user_get_info($version, $prm_base, $extparams['row']['userid'], 'profname', 'get', '', 'desc=profsort', $state);
					$tag_val_arr[$field] = $user_info['ret'] == RetClass::SUCCESS ? $user_info['data']['profname'] : '';
				}
				break;
			case 'applyuserid':
				$tag_val_arr[$field] = isset($keydata_arr['applyusername']) ? $keydata_arr['applyusername'] : '';
				if (empty($tag_val_arr[$field]) && !empty($extparams['row']['applyuserid']))
				{
					//keydata为空再查一下人名
					$state               = array(EnumClass::STATE_TYPE_NORMAL, EnumClass::STATE_TYPE_DELETE);
					$user_info           = ApiClass::user_get_info($version, $prm_base, $extparams['row']['applyuserid'], 'profname', 'get', '', 'desc=profsort', $state);
					$tag_val_arr[$field] = $user_info['ret'] == RetClass::SUCCESS ? $user_info['data']['profname'] : '';
				}
				break;
			default:
				if (!empty($extparams['row'][$format]))
				{
					$state               = array(EnumClass::STATE_TYPE_NORMAL, EnumClass::STATE_TYPE_DELETE);
					$user_info           = ApiClass::user_get_info($version, $prm_base, $extparams['row'][$format], 'profname', 'get', '', 'desc=profsort', $state);
					$tag_val_arr[$field] = $user_info['ret'] == RetClass::SUCCESS ? $user_info['data']['profname'] : '';
				}
				break;
		}

		// 直接用id查
		//如果数值为0，接口方法会自动查出当前人的名字
		// if($fldval>0){
		// 	$user_info = ApiClass::user_get_info($version, $prm_base, $fldval, 'profname');
		// 	$tag_val_arr[$field] = $user_info['ret'] == RetClass::SUCCESS ? $user_info['data']['profname']:'';
		// }else{
		// 	//如果为0，则为空
		// 	$tag_val_arr[$field] = '';
		// }
		return $tag_val_arr[$field];
	}

	//取创建人部门名
	public static function format_create_deptid($version, $prm_base, $fldval, $field, &$tag_val_arr = array(), $format = '', $path = '', $page = '', $extparams = array())
	{
		$keydata_arr         = CommonClass::json_decode($fldval);
		//替换创建人部门
		$tag_val_arr[$field] = isset($keydata_arr['deptname']) ? $keydata_arr['deptname'] : '';
		if (empty($tag_val_arr[$field]) && !empty($extparams['row'][$format]))
		{
			//keydata为空再查一下部门名
			//直接用id查
			$dept_info           = ApiClass::enterprise_get_dept($version, $prm_base, $prm_base->entid, $extparams['row'][$format], 'deptname', 'get');
			$tag_val_arr[$field] = $dept_info['ret'] == RetClass::SUCCESS ? $dept_info['data']['deptname'] : '';
		}

		return $tag_val_arr[$field];
	}

	//取创建人职位名
	public static function format_create_posnid($version, $prm_base, $fldval, $field, &$tag_val_arr = array(), $format = '', $path = '', $page = '', $extparams = array())
	{
		$keydata_arr         = CommonClass::json_decode($fldval);
		//替换创建人职位
		$tag_val_arr[$field] = isset($keydata_arr['posnname']) ? $keydata_arr['posnname'] : '';
		if (empty($tag_val_arr[$field]) && !empty($extparams['row'][$format]))
		{
			//keydata为空再查一下职位名
			//直接用id查
			$posn_info           = ApiClass::enterprise_get_posn($version, $prm_base, $prm_base->entid, $extparams['row'][$format], 'posnname', 'get');
			$tag_val_arr[$field] = $posn_info['ret'] == RetClass::SUCCESS ? $posn_info['data']['posnname'] : '';
		}

		return $tag_val_arr[$field];
	}

	//流程字段
	public static function format_get_wf_info($version, $prm_base, $fldval, $field, &$tag_val_arr = array(), $format = '', $path = '', $page = '', $extparams = array())
	{
		//将流程相关的格式化相关的人员
		$tag_val_arr[$field] = '';
		if (empty($fldval)) return $tag_val_arr[$field];

		$users    = explode(',', $fldval);
		$users_cn = '';
		if (is_array($users))
		{
			foreach ($users as $k => $v)
			{
				$tmp = explode('|', $v);
				foreach ($tmp as $k1 => $v1)
				{
					if (strpos($v1, 'u:') !== false)
					{
						$userid    = ltrim($v1, 'u:');
						$state     = array(EnumClass::STATE_TYPE_NORMAL, EnumClass::STATE_TYPE_DELETE);
						$user_info = ApiClass::user_get_info($version, $prm_base, $userid, 'profname', 'get', '', 'desc=profsort', $state);
						$username  = $user_info['ret'] == RetClass::SUCCESS ? $user_info['data']['profname'] : '';
						if (!empty($username)) $users_cn .= $username . ',';
					}
				}
			}
		}

		$users_cn            = rtrim($users_cn, ',');
		$tag_val_arr[$field] = $users_cn;
		return $tag_val_arr[$field];
	}

	//流程字段
	public static function format_get_wf_node($version, $prm_base, $fldval, $field, &$tag_val_arr = array(), $format = '', $path = '', $page = '', $extparams = array())
	{
		//将流程相关的格式化相关的人员
		$todonode            = CommonClass::json_decode($fldval);
		$todo                = isset($todonode[0]['uname']) ? $todonode[0]['uname'] : '';
		$tag_val_arr[$field] = $todo;

		return $tag_val_arr[$field];
	}

	//流程字段（步骤）
	public static function format_get_wf_node_n($version, $prm_base, $fldval, $field, &$tag_val_arr = array(), $format = '', $path = '', $page = '', $extparams = array())
	{
		//将流程相关的格式化相关的步骤
		$todonode            = CommonClass::json_decode($fldval);
		$todo                = isset($todonode[0]['nname']) ? $todonode[0]['nname'] : '';
		$tag_val_arr[$field] = $todo;

		return $tag_val_arr[$field];
	}

	// public static function format_applyuserid($version,$prm_base,$fldval,$field,&$tag_val_arr=array(),$format='',$path='',$page='',$extparams=array()){
	// 	$keydata_arr = CommonClass::json_decode($fldval);
	// 	//替换申请人职位
	// 	$tag_val_arr[$field] = isset($keydata_arr['applyusername']) ? $keydata_arr['applyusername'] : '';
	// 	if(empty($tag_val_arr[$field]) && !empty($tag_val_arr['applyuserid'])){
	// 		//keydata为空再查一下人名
	// 	}
	// 	return $tag_val_arr[$field];
	// }
	//取地址控件某一个key值
	public static function format_address($version, $prm_base, $fldval, $field, &$tag_val_arr = array(), $format = '', $path = '', $page = '', $extparams = array())
	{
		$keydata_arr         = CommonClass::json_decode($fldval);
		$tag_val_arr[$field] = isset($keydata_arr[$format]) ? $keydata_arr[$format] : '';

		return $tag_val_arr[$field];
	}

	//取业务状态中文名
	public static function format_opstate($version, $prm_base, $fldval, $field, &$tag_val_arr = array(), $format = '', $path = '', $page = '', $extparams = array())
	{
		$tag_val_arr[$field] = $fldval;
		$moduleid            = !empty($format['moduleid']) ? $format['moduleid'] : 0;
		$moduleid            = !empty($extparams['moduleid']) ? $extparams['moduleid'] : $moduleid;

		if ($moduleid == EnumClass::MODULE_NAME_NEWS || $moduleid == EnumClass::MODULE_NAME_MEETING)
		{
			$opstate_data        = ApiClass::module_get_opstate_name($version, $prm_base, $moduleid, $fldval);
			$tag_val_arr[$field] = $opstate_data['ret'] == RetClass::SUCCESS ? $opstate_data['data'] : '';
			return $tag_val_arr[$field];
		}

		$formid   = !empty($format['formid']) ? $format['formid'] : 0;
		$langtype = !empty($format['langtype']) ? $format['langtype'] : EnumClass::LANG_TYPE_ZHCN;
		$ret      = ApiClass::langdata_get($version, $prm_base, $langtype, $value = $fldval, $name = '', $moduleid, $formid, $num = 1);

		$tag_val_arr[$field] = $ret['ret'] == RetClass::SUCCESS ? $ret['data']['title'] : '';

		return $tag_val_arr[$field];
	}

	//取签到控件某key值
	public static function format_locaterecord($version, $prm_base, $fldval, $field, &$tag_val_arr = array(), $format = '', $path = '', $page = '', $extparams = array())
	{
		$keydata_arr         = CommonClass::json_decode($fldval);
		$tag_val_arr[$field] = isset($keydata_arr[$format]) ? $keydata_arr[$format] : '';

		if ($format == 'distance' && !empty($tag_val_arr[$field]))    $tag_val_arr[$field] = trim($tag_val_arr[$field], '() ');
		if ($format == 'tdifference' && !empty($tag_val_arr[$field])) $tag_val_arr[$field] = trim($tag_val_arr[$field], '()');

		return $tag_val_arr[$field];
	}

	public static function format_dataradio_and_datacheck($version, $prm_base, $fldval, $field, &$tag_val_arr = array(), $format = '', $path = '', $page = '', $extparams = array())
	{
		$itemname         = 'formtitle'; //回显用，这里写死
		//获得formid,在setdata中
		$formitem_setdata = $format;
		$rela             = isset($formitem_setdata['itemdetail']['rela']) ? $formitem_setdata['itemdetail']['rela'] : array();
		//获取其他需要的字段
		if (!empty($rela) && is_array($rela))
		{
			$itemname .= ',';
			foreach ($rela as $v) $itemname .= (isset($v['itemname']) && !empty($v['itemname'])) ? $v['itemname'] . ',' : '';
			$itemname  = rtrim($itemname, ',');
		}

		$formid = isset($formitem_setdata['itemdetail']['form']['val']) ? $formitem_setdata['itemdetail']['form']['val'] : '';
		$id     = $fldval;
		if (!is_array(CommonClass::json_decode($fldval)))
		{
			if ($formid == 'all')
			{
				//没有formid，就找moduleid
				$moduleid  = isset($formitem_setdata['itemdetail']['moduletype']['val']) ? $formitem_setdata['itemdetail']['moduletype']['val'] : '';
				$datavalue = ApiClass::datasource_dataselect_get_show_val($version, $prm_base, $moduleid, $id);
			}
			else
			{
				$datavalue = ApiClass::listdata_get_show_formdataselect($version, $prm_base, $formid, $itemname, $id);
			}

			if ($datavalue['ret'] == RetClass::SUCCESS)
			{
				$fldval = $datavalue['data'];
				$fldval = self::format_dataselection($fldval);
			} else {
				$fldval = '';
			}
		}

		$tag_val_arr[$field] = $fldval;
		return $tag_val_arr[$field];
	}

	public static function format_locate_record_attid($version, $prm_base, $fldval, $field, &$tag_val_arr = array(), $format = '', $path = '', $page = '', $extparams = array())
	{
		if (strpos($fldval, '{') !== false && strpos($fldval, 'attid') !== false) {
			$fldval = CommonClass::json_decode($fldval);
			$fldval = !empty($fldval['attid']) ? $fldval['attid'] : 0;
		} else {
			$fldval = !empty($fldval) ? $fldval : 0;
		}

		if (empty($fldval))
		{
			$fldval = '';
		}
		else
		{
			$fldval_arr = explode(',', $fldval);
			$fldval_arr = array_unique($fldval_arr);
			$fldval_str = implode(',', $fldval_arr);
			$fldval     = base64_encode($fldval_str);
			$url        = Doo::conf()->APP_URL . 'fileview/view?da=' . $fldval . '&attachmentid=' . $fldval_arr[0];
			$fldval     = '<a href="' . $url . '" target="_blank">查看</a>';
		}

		$tag_val_arr[$field] = $fldval;
		return $tag_val_arr[$field];
	}

	/**
	 * [format_att 格式化附件id为附件详细信息json字符串]
	 * @param  [type] $version      [description]
	 * @param  [type] $prm_base     [description]
	 * @param  [type] $fldval       [description]
	 * @param  [type] $field        [description]
	 * @param  array  &$tag_val_arr [description]
	 * @param  string $format       [description]
	 * @param  string $path         [description]
	 * @param  string $page         [description]
	 * @param  array  $extparams    [description]
	 * @return [type]               [description]
	 */
	public static function format_att($version, $prm_base, $fldval, $field, &$tag_val_arr = array(), $format = '', $path = '', $page = '', $extparams = array())
	{
		if (strpos($fldval, '{') !== false && strpos($fldval, 'attid') !== false)
		{
			$fldval = CommonClass::json_decode($fldval);
			$fldval = !empty($fldval['attid']) ? $fldval['attid'] : '';
		}
		else
		{
			$fldval = !empty($fldval) ? $fldval : '';
		}

		$attvalue = ApiClass::file_get_att_by_ids($version, $prm_base, $prm_base->entid, $fldval);
		if ($attvalue['ret'] == RetClass::SUCCESS) $fldval = CommonClass::json_encode($attvalue['data']);

		$tag_val_arr[$field] = $fldval;
		return $tag_val_arr[$field];
	}

	//==================================   列表里面使用到的format(end) ==============================================

	/**
	 * @author suson 2015.10.21
	 * [do_module_api 调用各个模块的接口方法]
	 * @param  [type] $version  [description]
	 * @param  [type] $prm_base [description]
	 * @param  [type] $moduleid [description]
	 * @param  [type] $function [方法名不带api_]
	 * @param  [type] $params   [description]
	 * @return [type]           [description]
	 */
	public static function do_module_api($version, $prm_base, $moduleid, $function, $params)
	{
		if (empty($moduleid) || empty($function)) return self::ret();

		$mod_path     = array();
		$type = $name = '';
		$mod_install  = ApiClass::enterprise_handle_module_cache($version, $prm_base, $moduleid);

		if ($mod_install['ret'] == RetClass::SUCCESS)
		{
			$moduledata = !empty($mod_install['data']) ? $mod_install['data'] : array();
			$mod_path   = !empty($moduledata['path']) ? CommonClass::json_decode($moduledata['path']) : array();
		}
		if (isset($mod_path['type']) && !empty($mod_path['type']) && isset($mod_path['name']) && !empty($mod_path['name']))
		{
			$type = $mod_path['type'];
			$name = $mod_path['name'];
		}
		else if ($moduleid == EnumClass::MODULE_NAME_NOTES)
		{ //笔记模块在enterprise_module没有，暂时这么处理。HCW 2017.11.13
			$type = 'base';
			$name = 'notes';
		}
		else
		{
			return self::ret();
		}

		$ret = Doo::getModuleClass($type . '/' . $name, ucfirst($name) . 'Class', $function, $version, $prm_base, $params);

		return $ret;
	}

	/**
	 * 点用各个模块的controller的方法
	 * HCW 2017.05.12
	 * @param  [type] $version  [description]
	 * @param  [type] $prm_base [description]
	 * @param  [type] $moduleid [description]
	 * @param  [type] $function [description]
	 * @param  [type] $data     [description]
	 * @return [type]           [description]
	 */
	public static function do_module_controller_api($version, $prm_base, $moduleid, $function, $data)
	{
		$mod_path    = array();
		$type        = $name = '';
		$mod_install = ApiClass::enterprise_handle_module_cache('3.0.0.0', $prm_base, $moduleid);
		if ($mod_install['ret'] == RetClass::SUCCESS)
		{
			$moduledata = !empty($mod_install['data']) ? $mod_install['data'] : array();
			$mod_path = !empty($moduledata['path']) ? CommonClass::json_decode($moduledata['path']) : array();
		}

		$render = '';
		if (isset($mod_path['type']) && !empty($mod_path['type']) && isset($mod_path['name']) && !empty($mod_path['name']))
		{
			$type   = $mod_path['type'];
			$name   = $mod_path['name'];
			$render = Doo::app()->getModule($type . '/' . $name, 'Web' . ucfirst($name) . 'Controller', 'render_tab', '3.0.0.0', $prm_base, $data);
		}
		if (!empty($render)) return self::ret(RetClass::SUCCESS, $render);

		return self::ret();
	}

	//==================================   formclass里面使用到的format(start) ==============================================

	//格式化数据选择控件，返回formtitle
	// $params = array('prm_base'=>$prm_base,'formid'=>'','itemname'=>'')
	public static function format_dataselection($val, $params = array())
	{
		extract($params);
		$retval = '';
		if (is_array($val))
		{
			$valjson = $val;
		}
		else if (!empty($prm_base) && !empty($formid) && !empty($itemname) && strpos($val, '{') === false)
		{ //如果$val的值是ID值(bigint类型的值)update by ljh 2016-10-13
			// 根据formid和itemname，获取form_item表数据选择控件记录
			$formitem_all     = FormClass::get_formitem_page($prm_base, $formid, $itemname, $ver = 0, $pagetype = EnumClass::FORM_PAGE_TYPE_NONE, $terminal = EnumClass::TERMINAL_TYPE_WEB, $itemtype = EnumClass::FORM_ITEM_TYPE_NONE);
			// 获取form_item.setdata字段值
			$formitem_setdata = !empty($formitem_all['setdata']) ? CommonClass::json_decode($formitem_all['setdata']) : array();
			// 获取数据选择控件的moduleid
			$moduleid         = !empty($formitem_setdata['itemdetail']) && !empty($formitem_setdata['itemdetail']['moduletype']) && !empty($formitem_setdata['itemdetail']['moduletype']['val']) ? $formitem_setdata['itemdetail']['moduletype']['val'] : 0;
			// 根据moduleid和val(记录ID)的值，获取对应数据信息
			$ret              = ApiClass::datasource_dataselect_get_show_val('3.0.0.0', $prm_base, $moduleid, $val);
			$valjson          = $ret['ret'] == RetClass::SUCCESS && !empty($ret['data']) ? CommonClass::json_decode($ret['data']) : array();
		}
		else
		{
			$valjson = CommonClass::json_decode($val);
		}

		if (!is_null($valjson))
		{
			if (!empty($valjson['list']))
			{
				$formtitle_arr = array();
				foreach ($valjson['list'] as $v) if (!empty($v['formtitle'])) $formtitle_arr[] = $v['formtitle'];
				$retval        = implode(',', $formtitle_arr);
			}
		}

		return $retval;
	}

	//拜访，返回当前使用者名字
	public static function format_visit_userid($prm_base)
	{
		$state     = array(EnumClass::STATE_TYPE_NORMAL, EnumClass::STATE_TYPE_DELETE);
		$user_info = ApiClass::user_get_info('3.0.0.0', $prm_base, $prm_base->userid, 'profname', 'get', '', 'desc=profsort', $state);
		$username  = $user_info['ret'] == RetClass::SUCCESS ? $user_info['data']['profname'] : '';

		return $username;
	}

	//拜访，返回当前创建时间
	public static function format_visit_crtime($val)
	{
		//返回的是拜访时间才对 suson.20160803  formtitle的配置改成visittime_start而不是crtime
		$crtime = CommonClass::get_datetime($val, EnumClass::DATETIME_TYPE_NONE, EnumClass::DATETIME_FORMAT_NO_SECOND);
		return $crtime;
	}

	//返回日期 ljh 2016-05-24
	public static function format_date($val)
	{
		$crtime = CommonClass::get_datetime($val, EnumClass::DATETIME_TYPE_NONE, EnumClass::DATETIME_FORMAT_DATE_ONLY);
		return $crtime;
	}

	//拜访，返回客户的shorname
	public static function format_visit_customer($val)
	{
		//注意，form_item里面的formtitle一定要在customer前面，否则customer的值会转为id
		if (!is_array($val)) $val = CommonClass::json_decode($val);
		$shorname = isset($val['list'][0]['formtitle']) ? $val['list'][0]['formtitle'] : '';
		return $shorname;
	}

	//==================================   formclass里面使用到的format(end) ==============================================

	/*
	 * 数据库重链接
	 * General.20151223
	 */
	public static function db_reconnect($db = EnumClass::DB_NAME_ENTERPRISE)
	{
		CommonClass::db_reconnect($db);
	}

	/**
	 * [api_before_action 各模块主类重写该方法检查操作权限等]
	 * @param  [type] $version  [description]
	 * @param  [type] $prm_base [description]
	 * @param  array  $params   [description]
	 * @return [type]           [description]
	 */
	public static function api_before_action($version, $prm_base, $params = array())
	{
		return self::ret(RetClass::SUCCESS);
	}

	/**
	 * [api_do_action 各模块主类重写该方法执行操作]
	 * @param  [type] $version  [description]
	 * @param  [type] $prm_base [description]
	 * @param  array  $params   [description]
	 * @return [type]           [description]
	 */
	public static function api_do_action($version, $prm_base, $params = array())
	{
		return self::ret(RetClass::SUCCESS);
	}

	/**
	 * [api_after_action 各模块主类重写该方法执行操作后调用]
	 * @param  [type] $version  [description]
	 * @param  [type] $prm_base [description]
	 * @param  array  $params   [description]
	 * @return [type]           [description]
	 */
	public static function api_after_action($version, $prm_base, $params = array())
	{
		return self::ret(RetClass::SUCCESS);
	}

	/**
	 * [api_before_save 各模块自行重写，保存前操作]
	 * @param  [type] $version  [description]
	 * @param  [type] $prm_base [description]
	 * @param  array  $params   [description]
	 * @return [type]           [description]
	 */
	public static function api_before_save($version, $prm_base, $params = array())
	{
		return self::ret(RetClass::SUCCESS);
	}

	/**
	 * [api_do_save description 各模块自行重写，保存方法]
	 * @param  [type] $version  [description]
	 * @param  [type] $prm_base [description]
	 * @param  array  $params   [description]
	 * @return [type]           [description]
	 */
	public static function api_do_save($version, $prm_base, $params = array())
	{
		extract($params);
		$ret = SaveClass::common_save($version, $prm_base, $formdata, $wfdata, $moduleid, $objid, $formid, $chkobjid, $forminfo, $actnid);
		return self::ret($ret);
	}

	/**
	 * [api_after_save description 各模块自行重写，保存后操作]
	 * @param  [type] $version  [description]
	 * @param  [type] $prm_base [description]
	 * @param  array  $params   [description]
	 * @return [type]           [description]
	 */
	public static function api_after_save($version, $prm_base, $params = array())
	{
		return self::ret(RetClass::SUCCESS);
	}

	/**
	 * [api_choose_return_value description 各模块自行重写，选择返回值]
	 * @param  [type] $version  [description]
	 * @param  [type] $prm_base [description]
	 * @param  array  $params   [description]
	 * @return [type]           [description]
	 */
	public static function api_choose_return_value($version, $prm_base, $params = array())
	{
		extract($params);
		/**
		 * 这里的params参数里面，包含了$ret_before, $ret_do, $ret_after
		 * 分别对应 before,do,after方法的返回值
		 * 这里默认返回do方法的返回值
		 * 各个模块可以根据实际需求自行重写
		 * @author HCW 20170.06.09
		 */
		return self::ret($ret_do);
	}

	/**
	 * [api_obtain_right_list description 各模块自行重写，获取右侧列表]
	 * @param  [type] $version  [description]
	 * @param  [type] $prm_base [description]
	 * @param  array  $params   [description]
	 * @return [type]           [description]
	 */
	public static function api_obtain_right_list($version, $prm_base, $params = array())
	{
		return self::ret(RetClass::ERROR);
	}

	/**
	 * [format_titlebar 后台管理模块标题栏]
	 * author DYN 2016.1.12
	 * @param  [type] $version  [description]
	 * @param  [type] $prm_base [description]
	 * @param  array  $params   [description]
	 * @return [type]           [description]
	 */
	public static function format_titlebar($version, $prm_base, $title, $addate, $lastupdate, $terminal, $opstate, $keydata)
	{
		$keydata = CommonClass::json_decode($keydata);
		if (isset($keydata['deptname']) && !empty($keydata['deptname']))
		{
			$deptname = $keydata['deptname'];
		}
		else
		{
			//获取部门
			$depres = ApiClass::enterprise_get_dept('3.0.0.0', $prm_base, $prm_base->deptid);
			if ($depres['ret'] == RetClass::SUCCESS)
			{
				$deptname = $depres['data']['deptname'];
			}
			else
			{
				$deptname = '';
			}
		}

		$username    = isset($keydata['username']) ? $keydata['username'] : '';
		//精确到秒
		$addsecond   = strtotime($addate) !== false ? strtotime($addate) : '';
		//精确到秒
		$upsecond    = strtotime($lastupdate) !== false ? strtotime($lastupdate) : '';
		//时间转为Y-m-d H:i 省略秒
		$addate      = !empty($addsecond) ? date('Y-m-d H:i', $addsecond) : '';
		//$lastupdate 不能为空用作排列
		$lastupdate  = !empty($upsecond) ? date('Y-m-d H:i', $upsecond) : '';
		$publishtime = !empty($addate) ? $addate : $lastupdate;
		//获取终端
		$terminal    = CommonClass::get_terminal($terminal);

		// $getopstate = $opstate;
		$titres['title']     = $title;
		$titres['defval']    = "{$username} 于 {$publishtime} 通过{$terminal} 提交，";
		$titres['createval'] = "创建人: {$deptname} {$username} 于 {$publishtime} 通过{$terminal} 提交";
		$titres['updateval'] = empty($addsecond) || $upsecond == $addsecond ? '' : "最后修改: {$deptname} {$username} 于 {$lastupdate} 修改";
		//var_dump($titres);exit();
		return CommonClass::json_encode($titres);
	}

	/**
	 * 数据单选控件格式化
	 * HCW 2016.01.29
	 */
	public static function format_dataradio($version, $prm_base, $fldval, $field, $column, &$tag_val_arr = array())
	{
		$itemname            = isset($column['itemname']) ? $column['itemname'] : '';
		$formid              = isset($column['formid']) ? $column['formid'] : '';
		$ret                 = ApiClass::listdata_get_data_from_dataselect($version, $prm_base, $formid, $itemname, $fldval);
		$tag_val_arr[$field] = $ret['ret'] == RetClass::SUCCESS ? $ret['data'][$itemname] : '';

		return $tag_val_arr[$field];
	}

	/**
	 * @author HCW 2016.05.04
	 * [format_link 格式化链接地址]
	 * @param  [type] $version      [description]
	 * @param  [type] $prm_base     [description]
	 * @param  [type] &$row         [某一笔记录]
	 * @param  [type] $field        [字段]
	 * @param  array  &$tag_val_arr [替换后的值放到改数组]
	 * @param  string $template     [description]
	 * @return [type]               [description]
	 */
	public static function format_pm_link($version, $prm_base, $row, $field, &$tag_val_arr = array(), $template = array(), $path = '', $page = 1)
	{
		$baseUrl          = trim($prm_base->url, '/');
		$row['path']      = $path;
		$row['page']      = $page;
		$view_template    = isset($template['view']) ? $template['view'] : '';
		$process_template = isset($template['process']) ? $template['process'] : '';

		if (strpos($path, '96527580236088550') !== false)
		{ //如果是项目审批的话 就要用项目审批的url
			$template = $process_template;
		}
		else
		{
			$template = $view_template;
		}

		$t = preg_replace_callback('/#(\w+)#/', function($matches) use($row) {
			if (isset($row[$matches[1]])) {
				return $row[$matches[1]];
			}
		}, $template);
		$tag_val_arr['link']  = $baseUrl . $t;
		$tag_val_arr['title'] = isset($row[$field]) ? $row[$field] : '';
		$tag_val_arr[$field]  = isset($row[$field]) ? $row[$field] : ''; //这个需要加  suson 20151124

		return $tag_val_arr[$field];
	}

	/**
	 * [check_authority 检查当前人是否符合表单角色权限 crm模块验证创建人，负责人，负责人直接上司]
	 * suson.20160305
	 * @param  [type] $version    [description]
	 * @param  [type] $prm_base   [description]
	 * @param  [type] $moduleid   [description]
	 * @param  [type] $objid      [description]
	 * @param  [type] $checktype  [description]
	 * @param  [type] $managerkey [负责人字段名]
	 * @return [type]             [description]
	 */
	public static function check_authority($version, $prm_base, $moduleid, $objid, $checktype, $managerkey = 'manager')
	{
		//根据moduleid来获取表名
		$opt['table']  = 'form_info';
		$opt['select'] = 'tbname,setdata';
		$opt['where']  = 'moduleid = ? and mainformid=0 and datatype=?';
		$opt['param']  = array($moduleid, EnumClass::FORM_DATA_TYPE_FORM);

		$tbname  = DbClass::get_one($prm_base, $opt);
		$setdata = isset($tbname['setdata']) ? CommonClass::json_decode($tbname['setdata']) : array();
		$tbname  = isset($tbname['tbname']) ? $tbname['tbname'] : '';
		$pkid    = !empty($setdata['fldname']) ? $setdata['fldname'] : 'id';

		if (empty($tbname)) return false;

		unset($opt);
		$opt['table'] = $tbname;
		$opt['where'] = $pkid . ' = ?';
		$opt['param'] = array($objid);
		$ret = DbClass::get_one($prm_base, $opt);

		if (!empty($ret))
		{
			//该记录不符合业务状态，则直接返回无权限
			if ($ret['opstate'] == EnumClass::STATE_TYPE_ARCHIVE) return false;

		    //验证报表管理权限时 只需要验证业务状态条件满足
			if ($checktype == 'listdata') return true;

			$allows       = array($ret['userid']);
			$manager_json = !empty($ret[$managerkey]) ? $ret[$managerkey] : '';
			if (!empty($manager_json))
			{
				$managerArray = CommonClass::json_decode($manager_json);
				$userArray    = $managerArray['user']; //user字段数组
				foreach ((array) $userArray as $key => $value)
				{
					if (!isset($value['u']) || empty($value['u'])) continue;

					$manager = $value['u'];
					$manager = explode(',', $manager);
					foreach ($manager as $manager_key => $manager_value)
					{
						$managerLeaders = ApiClass::user_get_leaders_by_userid($version, $prm_base, $manager_value);
						if ($managerLeaders['ret'] == RetClass::SUCCESS)
						{
							extract($managerLeaders['data']);
							//$directleader,$upperleader,$deptleader  直接上司，间接上司，部门领导
							//放到允许列表
							$allows = array_merge($allows, $directleader);
						}
						array_push($allows, $manager_value);
					}
				}
			}
			if (false !== array_search($prm_base->userid, $allows)) return true;
		}

		return false;
	}

	/**
	 * pm模块数据格式化
	 */
	public static function format_pm_data($version, $prm_base, &$data)
	{
		// var_dump($data);die;
		$arr           = CommonClass::json_decode($data['jsondata']);
		$opt['where']  = 'state = ?';
		$opt['param']  = array(EnumClass::STATE_TYPE_NORMAL);
		$opt['select'] = 'id,title';
		$temp_options  = CommonClass::do_table($prm_base, 'PmParameter', $opt, 0, 'find');
		$options       = array();
		if (!empty($temp_options) && is_array($temp_options))
		{
			foreach ($temp_options as $k => $v) $options[$v['id']] = $v['title'];
		}
		else
		{
			return false;
		}

		if (is_array($arr['result']) && !empty($arr['result']))
		{
			foreach ($arr['result'] as $k => $fields)
			{
				$opt2['where'] = 'id = ?';
				$id            = isset($fields['pm_list_id']) ? $fields['pm_list_id'] : $fields['id'];
				$opt2['param'] = array($id);
				$pmdata        = CommonClass::do_table($prm_base, 'PmList', $opt2, 0, 'get');

				foreach ($fields as $k1 => $field_value)
				{
					// var_dump($fields);die;
					// if (strpos($k1, 'pm_list') !== false) {
					$temp_key = str_replace('pm_list_', '', $k1);
					switch ($temp_key)
					{
						case 'pm_from'://项目来源
						case 'invest_type'://投资方式
						case 'industry'://行业
						case 'pm_type'://项目方性质
						case 'scale'://规模
							if ($field_value == 0) $field_value = '';
							$arr['result'][$k][$k1] = isset($options[$field_value]) ? $options[$field_value] : $field_value;
							break;
						case 'currency'://货币类型
							$arr['result'][$k][$k1] = $field_value == 1 ? CommonClass::txt('CURRENCY_RMB') : CommonClass::txt('CURRENCY_DOLLAR');
							break;
						case 'is_capital'://是否增资
							$arr['result'][$k][$k1] = $field_value == 1 ? CommonClass::txt('IS_CAPITAL') : CommonClass::txt('NO_CAPITAL');
							break;
						case 'pm_leader'://项目负责人
							$arr['result'][$k][$k1] = isset($pmdata['pm_leader_text']) ? $pmdata['pm_leader_text'] : '';
							break;
						case 'department'://管理部门
							$arr['result'][$k][$k1] = isset($pmdata['department_text']) ? $pmdata['department_text'] : '';
							break;
						case 'leader'://分管领导
							$arr['result'][$k][$k1] = isset($pmdata['leader_text']) ? $pmdata['leader_text'] : '';
							break;
						case 'other_leader'://其他负责人
							$arr['result'][$k][$k1] = isset($pmdata['other_leader_text']) ? $pmdata['other_leader_text'] : '';
							break;
						case 'cateid':
							$arr['result'][$k][$k1] = isset($pmdata['cate_name']) ? $pmdata['cate_name'] : '';
							break;
						case 'userid'://创建人
							$keydata = CommonClass::json_decode($pmdata['keydata']);
							$arr['result'][$k][$k1] = isset($keydata['username']) ? $keydata['username'] : '';
							break;
						case 'crtime'://创建时间
						case 'uptime'://修改时间
						case 'contact_time'://首次接触时间
							$arr['result'][$k][$k1] = substr($arr['result'][$k][$k1], 0, 10); //只显示年月日
							break;
						case 'pm_date'://落户时间
							$arr['result'][$k][$k1] = substr($arr['result'][$k][$k1], 0, 10); //只显示年月日
							break;
						default:
							# code...
							break;
					}
					// }
				}
			}
		}
		$data['jsondata'] = CommonClass::json_encode($arr);
		// die;
	}

	/**
	 * 模块升级(供各模块主类文件重写该方法)
	 * suson.20160513
	 */
	public static function api_upgrade($version, $prm_base, $params = array())
	{
		// params = array(ver=>8.0)
		return self::ret(RetClass::SUCCESS);
	}

	/**
	 * 将post过来的值转换成文本
	 * HCW 2016.06.19
	 * @param  [type] $version  [description]
	 * @param  [type] $prm_base [description]
	 * @param  [type] $formval  [description]
	 * @param  [type] $cmptname [description]
	 * @return [type]           [description]
	 */
	public static function format_postval_to_text($version, $prm_base, $format_val, $cmptname)
	{
		switch ($cmptname) {
			case 'category':
				//暂时
				$formtype               = 'params';
				$format_function_params = array($version, $prm_base, $format_val, 'self');
				$format_val             = call_user_func_array(array('BaseClass', 'format_' . $formtype), $format_function_params);
				break;
			case 'dataselection':
			case 'datacheck':
			case 'dataradio':
				$formtype               = 'dataselection';
				$format_function_params = array($format_val);
				$format_val             = call_user_func_array(array('BaseClass', 'format_' . $formtype), $format_function_params);
				break;
			default:
				break;
		}
		return $format_val;
	}

	/**
	 * 补全fldset数据
	 * hky 20160620
	 * @param  [type] $version  [description]
	 * @param  [type] $prm_base [description]
	 * @param  [type] $fldtype  [description]
	 * @param  [type] $itemname [description]
	 * @param  [type] $change 	[description] 	//为true则把fldtype赋值给type
	 * @return [type]           [description]
	 */
	public static function change_fldset($prm_base, $fldtype, $itemname, $change = false)
	{
		$arr = array();
		switch ($fldtype)
		{
			case 'Titletype':
				$type = 'titletype';
				break;
			case 'Selectmember':
				$type = 'userdata_to_name';
				break;
			default:
				$type = $change ? $fldtype : ''; //change为true则把fldtype赋值给type
				break;
		}

		if (!empty($type))
		{
			$arr = array(
				'fields' => array(
					array(
						'fldname' => $itemname,
						'args' => array(
							'format' => '',
							'type' => $type
						)
					)
				)
			);
		}

		return $arr;
	}

	/**
	 * 将type转换
	 * @return [type] [description]
	 */
	public static function format_titletype($version, $prm_base, $fldval, $field, &$tag_val_arr = array())
	{
		switch ($fldval)
		{
			case 'day':
				$type = '日报';
				break;
			case 'week':
				$type = '周报';
				break;
			case 'month':
				$type = '月报';
				break;
			case 'quarter':
				$type = '季报';
				break;
			case 'year':
				$type = '年报';
				break;
			case 'another':
				$type = '其他';
				break;
			default:
				$type = '';
				break;
		}
		$tag_val_arr[$field] = $type;
		return $tag_val_arr[$field];
	}

	/**
	 * 格式化会议主持人
	 * HCW 2016.07.01
	 * @param  [type] $version      [description]
	 * @param  [type] $prm_base     [description]
	 * @param  [type] $fldval       [description]
	 * @param  [type] $field        [description]
	 * @param  array  &$tag_val_arr [description]
	 * @param  string $defname      [description]
	 * @param  [type] $chair_out    [description]
	 * @return [type]               [description]
	 */
	public static function format_meeting_chairuser($version, $prm_base, $fldval, $field, &$tag_val_arr = array(), $defname = '', $chair_out)
	{
		self::format_userdata_to_name($version, $prm_base, $fldval, $field, $tag_val_arr, $defname);
		if (empty($tag_val_arr[$field]) && !empty($chair_out)) $tag_val_arr[$field] = $chair_out;

		return $tag_val_arr[$field];
	}

	/**
	 * [get_comset 获取设置控件值]
	 * @param  [type] $version  [description]
	 * @param  [type] $prm_base [description]
	 * @param  [type] $tbdata   [description]
	 * @return [type]           [description]
	 */
	public static function get_comset($version, $prm_base, $tbdata)
	{
		$comsetjsonstr = '{"topendtime":"","commentattr":"","publicattr":"","issuettime":"","archivetime":""}';
		$comsetdef     = CommonClass::json_decode($comsetjsonstr);
		$comset        = !empty($tbdata['comset']) ? CommonClass::json_decode($tbdata['comset']) : $comsetdef;
		// commentattr 1000 允许评论
		// publicattr 100 公开受众
		if (!empty($tbdata['userid']) && $tbdata['userid'] == $prm_base->userid) $comset['publicattr'] = '100';

		return $comset;
	}

	/**
	 * [api_check_comset 设置控件验证]
	 * @param  [type] $version  [description]
	 * @param  [type] $prm_base [description]
	 * @param  [type] $params   [description]
	 * @return [type]           [description]
	 */
	public static function api_check_comset($version, $prm_base, $params)
	{
		extract($params);
		//{"topendtime":"","commentattr":"","publicattr":"","issuettime":"2016-07-20 00:00","archivetime":""}
		$comset = CommonClass::json_decode($value);
		if (!empty($comset['topendtime']) && strtotime($comset['topendtime']) <= strtotime(CommonClass::get_datetime()))
		{
			$msg = '设置置顶时间小于当前时间';
			return self::ret(RetClass::ERROR, '', '', '', $msg);
		}

		if (!empty($comset['issuettime']) && strtotime($comset['issuettime']) <= strtotime(CommonClass::get_datetime()))
		{
			$msg = '设置定时发布时间小于当前时间';
			return self::ret(RetClass::ERROR, '', '', '', $msg);
		}

		if (!empty($comset['archivetime']) && strtotime($comset['archivetime']) <= strtotime(CommonClass::get_datetime()))
		{
			$msg = '设置自动归档时间小于当前时间';
			return self::ret(RetClass::ERROR, '', '', '', $msg);
		}

		return self::ret(RetClass::SUCCESS);
	}

	/**
	 * [api_check_custom_permit 报表特殊权限验证方法，各类重写]
	 * suson.20160808
	 * @param  [type] $version  [description]
	 * @param  [type] $prm_base [description]
	 * @param  [type] $params   [$params['formitem'] = $v1;
	  $params['chkobjid'] = $chkobjid;
	  $params['validationtype'] = $validationtype;]
	 * @return [type]           [description]
	 */
	public static function api_check_custom_permit($version, $prm_base, $params)
	{
		return self::ret(RetClass::SUCCESS);
	}

	/**
	 * [api_get_listdata_keys 获取各个模块手机列表配置]
	 * suson.20160822
	 * @param  [type] $version  [description]
	 * @param  [type] $prm_base [description]
	 * @param  [type] $params   [$params['pid'] = $pid;$params['moduleid'] = $moduleid;]
	 * @return [type]           [description]
	 */
	public static function api_get_listdata_keys($version, $prm_base, $params)
	{
		if (is_array($params)) extract($params);
		//返回 标题
		$data = array(
			array(
				'tag' => 'objid',
				'fields' => array(array('fldname' => 'id', 'args' => array()))
			),
			array(
				'tag' => 'isread',
				'fields' => array('fldname' => 'id', 'args' => array('format' => '', 'type' => 'isread'))
			),
			array(
				'tag' => 'opstate',
				'fields' => array(array('fldname' => 'opstate', 'args' => array()))
			),
			array(
				'tag' => 'title',
				'fields' => array(array('fldname' => 'formtitle', 'args' => array()))
			),
			array(
				'tag' => 'dataformid',
				'fields' => array(array('fldname' => 'formid', 'args' => array()))
			),
			array(
				'tag' => 'formid',
				'fields' => array(array('fldname' => 'formid', 'args' => array()))
			),
			array(
				'tag' => 'formname',
				'fields' => array(array('fldname' => 'formid', 'args' => array('format' => '', 'type' => 'formid_formname')))
			),
			array(
				'tag' => 'oraltime',
				'fields' => array(array('fldname' => 'uptime', 'args' => array('format' => '', 'type' => 'oral_time')))
			),
			array(
				'tag' => 'crtime',
				'fields' => array(array('fldname' => 'crtime', 'args' => array('format' => '', 'type' => 'timestamp')))
			),
			array(
				'tag' => 'userid',
				'fields' => array(array('fldname' => 'userid', 'args' => array()))
			),
			array(
				'tag' => 'commentcount',
				'fields' => array(array('fldname' => 'id', 'args' => array('format' => '', 'type' => 'commentcount')))
			)
		);
		switch ($prm_base->terminal)
		{
			case EnumClass::TERMINAL_TYPE_H5: //H5
			case EnumClass::TERMINAL_TYPE_MOBILE: //手机客户端
				//拼接url的Link
				$ret_path = ApiClass::enterprise_get_module_info($version, $prm_base, $moduleid, $select = 'path', $method = 'get');
				$ret_path = $ret_path['ret'] == RetClass::SUCCESS ? $ret_path['data']['path'] : '';
				$ret_path = CommonClass::json_decode($ret_path);
				$pathname = isset($ret_path['name']) ? $ret_path['name'] : '';
				//加上url
				$data[]   = array(
					'tag' => 'url',
					'fields' => array(array('fldname' => 'url', 'args' => array('format' => '', 'type' => 'url', 'link' => 'html/' . $pathname . '/page/pagedetail')))
				);
				//添加头像logo字段 jimswoo 20161114
				$data[]   = array(
					'tag' => 'logo',
					'fields' => array(array('fldname' => 'userid', 'args' => array('format' => '', 'type' => 'avatar')))
				);
				break;
			default:
				break;
		}

		return self::ret(RetClass::SUCCESS, $data);
	}

	/**
	 * [api_reply 评论控件发送后回调，各个类重写]
	 * suson.20160831
	 * @param  [type] $version  [description]
	 * @param  [type] $prm_base [description]
	 * @param  [type] $params   [description]
	 * @return [type]           [description]
	 */
	public static function api_reply($version, $prm_base, $params)
	{
		return self::ret(RetClass::SUCCESS);
	}

	/**
	 * [api_check_alarm_condition 提醒控件发提醒的条件验证,如任务开始提醒，需要判断是不是未执行状态]
	 * suson.20160902
	 * @param  [type] $version  [description]
	 * @param  [type] $prm_base [description]
	 * @param  [type] $params   [description]
	 * @return [type]           [description]
	 */
	public static function api_check_alarm_condition($version, $prm_base, $params)
	{
		return self::ret(RetClass::SUCCESS);
	}

	/**
	 * 获取返回按钮
	 * HCW 2017.03.28
	 * @param  [type] $version  [description]
	 * @param  [type] $prm_base [description]
	 * @param  [type] $moduleid [description]
	 * @param  [type] $params   [description]
	 * @return [type]           [description]
	 */
	public static function api_get_returnlist($version, $prm_base, $moduleid, $params)
	{
		$ret = ApiClass::enterprise_get_module_info($version, $prm_base, $moduleid, $select = 'url');
		$url = $ret['ret'] == RetClass::SUCCESS ? $ret['data']['url'] : '';
		if (empty($url))
		{
			LogClass::log_trace1('BaseClass::api_get_returnlist_url_empty_module', $moduleid, __FILE__, __LINE__);
			return self::ret(RetClass::ERROR, '', '', '', 'module url is empty');
		}

		$path     = isset($params['path']) ? $params['path'] : '';
		$p        = isset($params['p']) ? $params['p'] : '';
		$k        = isset($params['k']) ? $params['k'] : '';
		$r        = isset($params['r']) ? $params['r'] : '';
		$v        = isset($params['v']) ? $params['v'] : '';
		$result   = array();
		$data     = compact('path', 'p', 'k', 'r', 'v');
		$url      = rtrim($prm_base->url, '/') . $url . CommonClass::encode_format($prm_base, $data);
		$result[] = array('name' => '返回', 'url' => $url);

		return self::ret(RetClass::SUCCESS, CommonClass::json_encode($result));
	}

	//查岗提醒填写异常说明按钮
	public static function format_check_explain($version, $prm_base, $fldval, $field, &$tag_val_arr = array(), $format = '', $path = '', $page = '', $extparams = array())
	{
		if (strpos($fldval, '{') !== false && strpos($fldval, 'attid') !== false)
		{
			$fldval = CommonClass::json_decode($fldval);
			$fldval = !empty($fldval['attid']) ? $fldval['attid'] : '';
		}
		else
		{
			$fldval = !empty($fldval) ? $fldval : '';
		}

		if (empty($fldval))
		{
			$fldval = '';
		}
		else
		{
			if ($fldval == '未填写') $fldval = '<a oa-linkurl id="' . $extparams['row']['pkid'] . '" href="/web/m96685493600651804/ajax/ajaxerrorexplain?objid=' . $extparams['row']['pkid'] . '">提醒填写异常说明</a>';
		}

		$tag_val_arr[$field] = $fldval;
		return $tag_val_arr[$field];
	}

	//查岗取业务状态中文名
	public static function format_check_opstate($version, $prm_base, $fldval, $field, &$tag_val_arr = array(), $format = '', $path = '', $page = '', $extparams = array())
	{
		$tag_val_arr[$field] = $fldval;
		$moduleid            = !empty($format['moduleid']) ? $format['moduleid'] : 0;
		$moduleid            = !empty($extparams['moduleid']) ? $extparams['moduleid'] : $moduleid;

		if ($moduleid == EnumClass::MODULE_NAME_NEWS || $moduleid == EnumClass::MODULE_NAME_MEETING)
		{
			$opstate_data        = ApiClass::module_get_opstate_name($version, $prm_base, $moduleid, $fldval);
			$tag_val_arr[$field] = $opstate_data['ret'] == RetClass::SUCCESS ? $opstate_data['data'] : '';
			return $tag_val_arr[$field];
		}

		$formid   = !empty($format['formid']) ? $format['formid'] : 0;
		$langtype = !empty($format['langtype']) ? $format['langtype'] : EnumClass::LANG_TYPE_ZHCN;
		$ret      = ApiClass::langdata_get($version, $prm_base, $langtype, $value = $fldval, $name = '', $moduleid, $formid, $num = 1);
		if ($ret['ret'] == RetClass::SUCCESS)
		{
			$tag_val_arr[$field] = isset($ret['data']['title']) ? $ret['data']['title'] : '';
			switch ($tag_val_arr[$field])
			{
				case '在岗':
					$tag_val_arr[$field] = '';
					break;
			}
		}
		$tag_val_arr[$field] = !empty($tag_val_arr[$field]) ? '[' . $tag_val_arr[$field] . ']' : '';
		return $tag_val_arr[$field];
	}

	//查岗手机端报表内容拼接
	public static function format_check_content($version, $prm_base, $fldval, $field, &$tag_val_arr = array(), $format = '', $path = '', $page = '', $extparams = array())
	{
		// echo "<pre>";var_dump($extparams);exit();
		if (isset($extparams['row']['oa1469001538403_790']))
		{
			switch ($extparams['row']['oa1469001538403_790'])
			{
				case '2001400':
					$tag_val_arr[$field] = '[在岗]' . $tag_val_arr[$field];
					break;
			}
			if (isset($extparams['row']['oa1469001550409_706']) && $extparams['row']['oa1469001550409_706'] != '未填写')
			{
				$tag_val_arr[$field] = '[已填写异常说明]' . $tag_val_arr[$field];
			}
		}
		return $tag_val_arr[$field];
	}

	//查岗二级菜单查岗记录
	public static function format_check_title($version, $prm_base, $fldval, $field, &$tag_val_arr = array(), $format = '', $path = '', $page = '', $extparams = array())
	{
		// echo "<pre>"; var_dump($extparams);exit();
		if (isset($extparams['row']['ent_95994514849661102_96697120899209488_oa1469001423182_178'], $extparams['row']['ent_95994514849661102_96697120899209488_oa1469001331800_107']))
		{
			$time = $extparams['row']['ent_95994514849661102_96697120899209488_oa1469001331800_107'];
			$user = $extparams['row']['ent_95994514849661102_96697120899209488_oa1469001423182_178'];
			if (!empty($user))
			{
				$data = ApiClass::datasource_userdata_change_name('3.0.0.0', $prm_base, $user, 'u', 'string'); //获取可查岗人
				$username = $data['data'];
			}
			else
			{
				$username = '系统';
			}
			$tag_val_arr[$field] = $username . '于' . $time . '的查岗记录';
		}
		return $tag_val_arr[$field];
	}

	/**
	 * [api_check_fileview_permit 各个模块检查文件在线浏览权限]
	 * suson.20161013
	 * @param  [type] $version  [description]
	 * @param  [type] $prm_base [description]
	 * @param  [type] $params   [description]
	 * @return [type]           [description]
	 */
	public static function api_check_fileview_permit($version, $prm_base, $params)
	{
		//params参数 objid,attid,other_param
		return self::ret(RetClass::SUCCESS);
	}

	/**
	 * 两个时间段整合成一个时间格式
	 * LJH 2016-11-2 16:23
	 * @param  [type] $version    [description]
	 * @param  [type] $prm_base   [description]
	 * @param  [type] $start_time [description]
	 * @param  [type] $end_time   [description]
	 * @param  [type] $fldname    [description]
	 * @param  [type] $othername  [description]
	 * @return [type]             [description]
	 */
	public static function format_current_time($version, $prm_base, $start_time, $end_time, $fldname, $othername, &$tag_val_arr = array())
	{
		if (empty($start_time)) return self::ret(RetClass::ERROR, '', '', '', '主时间不能为空!');

		$start_time          = explode(' ', $start_time); //分割开始时间
		$filter_start_second = explode(':', $start_time[1]); //主时间过滤秒
		if (empty($end_time) || $end_time == '0000-00-00 00:00:00')
		{//只有一个时间
			$tag_val_arr[$fldname] = $start_time[0] . ' ' . $filter_start_second[0] . ':' . $filter_start_second[1];
			return $tag_val_arr[$fldname];
		}

		$end_time          = explode(' ', $end_time); //结束时间
		$filter_end_second = explode(':', $end_time[1]); //副时间过滤秒
		if ($start_time[0] == $end_time[0])
		{//同日期，不同时间
			$tag_val_arr[$fldname] = $start_time[0] . ' ' . $filter_start_second[0] . ':' . $filter_start_second[1] . ' 至 ' . $filter_end_second[0] . ':' . $filter_end_second[1];
			return $tag_val_arr[$fldname];
		}

		$tag_val_arr[$fldname]  = $start_time[0] . ' ' . $filter_start_second[0] . ':' . $filter_start_second[1];
		$tag_val_arr[$fldname] .= ' 至 ' . $end_time[0] . ' ' . $filter_end_second[0] . ':' . $filter_end_second[1];
		return $tag_val_arr[$fldname];
	}

	/**
	 * @author clf 2016-11-08
	 * [format_userid 格式化当前人deptid为deptname]
	 * @param  [type] $version      [description]
	 * @param  [type] $prm_base     [description]
	 * @param  [type] $fldval       [description]
	 * @param  [type] $field        [description]
	 * @param  array  &$tag_val_arr [description]
	 * @return [type]               [description]
	 */
	public static function format_deptid($version, $prm_base, $fldval, $field, &$tag_val_arr = array())
	{
		$keydata_arr         = CommonClass::json_decode($fldval);
		//替换创建人部门
		$tag_val_arr[$field] = isset($keydata_arr['deptname']) ? $keydata_arr['deptname'] : '';
		return $tag_val_arr[$field];
	}

	//取业务状态中文名
	public static function format_signstate($version, $prm_base, $fldval, $field, &$tag_val_arr = array(), $format = '', $path = '', $page = '', $extparams = array())
	{
		$tag_val_arr[$field] = $fldval;
		$ret                 = ApiClass::exchange_get_info($version, $prm_base, $fldval);
		$tag_val_arr[$field] = $ret['ret'] == RetClass::SUCCESS ? $ret['data'] : '';

		return $tag_val_arr[$field];
	}

	/**
	 * [api_module_title_icon 各自模块返回标题链接html]
	 * @param  [type] $version  [description]
	 * @param  [type] $prm_base [description]
	 * @param  [type] $params   [description]
	 * @return [type]           [description]
	 */
	public static function api_module_title_icon($version, $prm_base, $params)
	{
		//params参数 objid,attid,other_param
		return self::ret(RetClass::ERROR);
	}

	/**
	 * [api_empty_list_tips 各个模块某报表无数据文案提示]
	 * suson.20161128
	 * @param  [type] $version  [description]
	 * @param  [type] $prm_base [description]
	 * @param  [type] $params   [description]
	 * @return [type]           [description]
	 */
	public static function api_empty_list_tips($version, $prm_base, $params)
	{
		if (is_array($params)) extract($params);

		$data = CommonClass::txt('TIPS_EMPTY_DATA');
		//我收到的
		// if(!empty($path) && strpos($path, '96691291168968319') !== false){
		//     $data = CommonClass::txt('PARAMS_EMPTYTIP_RECEIVED');
		// }

		return self::ret(RetClass::SUCCESS, $data);
	}

	/**
	 * 自定义各个模块二级菜单的内容
	 * LJH 2016-11-28
	 * @param  [type] $version  [description]
	 * @param  [type] $prm_base [description]
	 * @param  [type] $params   [description]
	 * @return [type]           [description]
	 */
	public static function api_custom_second_level_val($version, $prm_base, $params)
	{
		return self::ret(RetClass::SUCCESS);
	}

	/**
	 * [api_del_comment_callback 删除评论回调]
	 * suson.20161129
	 * @param  [type] $version  [description]
	 * @param  [type] $prm_base [description]
	 * @param  [type] $params   [description]
	 * @return [type]           [description]
	 */
	public static function api_del_comment_callback($version, $prm_base, $params)
	{
		return self::ret(RetClass::SUCCESS);
	}

	/**
	 * [api_module_com_save_rules 保存提交，定制验证规则]
	 * suson.20161212
	 * @param  [type] $version  [description]
	 * @param  [type] $prm_base [description]
	 * @param  [type] $params   [description]
	 * @return [type]           [description]
	 */
	public static function api_module_com_save_rules($version, $prm_base, $params)
	{
		return self::ret(RetClass::SUCCESS);
	}

	/**
	 * 格式部门id为部门名称
	 * hky 20161214
	 * @param  [type] $version      [description]
	 * @param  [type] $prm_base     [description]
	 * @param  [type] $fldval       [description]
	 * @param  [type] $field        [description]
	 * @param  array  &$tag_val_arr [description]
	 * @return [type]               [description]
	 */
	public static function format_deptid_to_name($version, $prm_base, $fldval, $field, &$tag_val_arr = array())
	{
		$ret                 = ApiClass::enterprise_get_dept($version, $prm_base, $prm_base->entid, $fldval, 'deptname');
		$tag_val_arr[$field] = $ret['ret'] == RetClass::SUCCESS ? $ret['data']['deptname'] : '';

		return $tag_val_arr[$field];
	}

	/**
	 * 格式
	 * @param  [type] $version  [description]
	 * @param  [type] $prm_base [description]
	 * @param  [type] &$data    [description]  需要显示的数据
	 * @param  [type] $list     [description]  全部数据
	 * @param  [type] $fields   [description]
	 * @return [type]           [description]
	 */
	public static function format_sys_data($version, $prm_base, &$data, $list, $fields)
	{
		if (!empty($data))
		{
			if (isset($data['jsondata']))
			{
				$jsondata = CommonClass::json_decode($data['jsondata']);
				$arr      = array();
				if (isset($jsondata['result']) && !empty($jsondata['result']))
				{
					$ids = array_map(function($value) {
						return $value['profid'];
					}, $list);
					if (!empty($ids))
					{
						//不是导出当前列表
						if ($fields != 'local')
						{
							//查人员档案信息表   hky20170110
							$pro_ret = ApiClass::user_get_proinfo_by_fields($version, $prm_base, $prm_base->entid, array('profid' => implode(',', $ids)), 'find', true, '', 'profid,jobnbr,type,entrydate');
							if ($pro_ret['ret'] == RetClass::SUCCESS) $pro_info = array_reduce($pro_ret['data'], create_function('$result, $v', '$result[$v["profid"]] = $v;return $result;'));

							//查在离职变动表   hky20170110
							$turn_ret = ApiClass::user_get_turnover_record_by_profid($version, $prm_base, implode(',', $ids), 'profid,workingstate,contractdate,dimissiondate,dimissiontype,dimissionrsn,description', 'find', true);
							if ($turn_ret['ret'] == RetClass::SUCCESS) $turn_info = array_reduce($turn_ret['data'], create_function('$result, $v', '$result[$v["profid"]] = $v;return $result;'));
						}
					}

					foreach ($jsondata['result'] as $k => &$v)
					{
						$id = $list[$k]['profid'];  //用profid来作为key
						//不是导出当前列表
						if ($fields != 'local')
						{
							if (isset($pro_info[$id]))  $v = array_merge($v, $pro_info[$id]);
							if (isset($turn_info[$id])) $v = array_merge($v, $turn_info[$id]);
						}

						if (isset($v['opstate']))
						{
							switch ($list[$k]['state'])
							{
								case EnumClass::STATE_TYPE_NORMAL:
									if ($v['opstate'] == EnumClass::SYS_EMP_STATE_NORMAL) $jsondata['result'][$k]['opstate'] = CommonClass::txt("TXT_PRO_NORMAL");
									break;
								case EnumClass::STATE_TYPE_DELETE:
									if ($v['opstate'] == EnumClass::SYS_EMP_STATE_DELETE) $jsondata['result'][$k]['opstate'] = CommonClass::txt("TXT_PRO_DEL");
									break;
								//删除不会显示，无需讨论
								default:
									break;
							}
							//替换状态
							switch ($list[$k]['opstate'])
							{
								case EnumClass::SYS_ACCOUNT_STATE_BAN:
									$jsondata['result'][$k]['opstate'] = CommonClass::txt("TXT_BAN_LOGIN");
									break;
								case EnumClass::SYS_EMP_STATE_LEAVE:
									$jsondata['result'][$k]['opstate'] = CommonClass::txt("TXT_PRO_LEAVE");
									break;
								case EnumClass::SYS_ACCOUNT_STATE_DEL:
									$jsondata['result'][$k]['opstate'] = CommonClass::txt("TXT_NOT_ACNUM");
									break;
								case EnumClass::SYS_EMP_STATE_NORMAL:
									$jsondata['result'][$k]['opstate'] = CommonClass::txt("TXT_PRO_NORMAL");
									//未登录过
									if ($list[$k]['logintime'] == '0000-00-00 00:00:00') $jsondata['result'][$k]['opstate'] = CommonClass::txt('TXT_NEVER_LOGIN');
									//无账号
									if ($list[$k]['puserid'] == 0) $jsondata['result'][$k]['opstate'] = CommonClass::txt("TXT_NOT_ACNUM");
									break;
								case EnumClass::SYS_ACCOUNT_STATE_INVITE: //未加入
									$jsondata['result'][$k]['opstate'] = CommonClass::txt('TXT_NO_JOIN');
									break;
								case EnumClass::SYS_ACCOUNT_STATE_REJECT: //已拒绝
									$jsondata['result'][$k]['opstate'] = CommonClass::txt('TXT_REJECT_JOIN');
									break;
								default:
									//                $resultArray[$k1]['state'] = CommonClass::txt("TXT_PRO_NORMAL");
									break;
							}
						}

						//兼职
						if (isset($list[$k]['deptname']) && isset($list[$k]['posnname']) && isset($list[$k]['leadername']) && isset($list[$k]['parttime']) && isset($list[$k]['ismain']))
						{
							$part_time = self::change_part_time($prm_base, $list[$k]['deptname'], $list[$k]['posnname'], $list[$k]['leadername'], $list[$k]['parttime'], $list[$k]['ismain']);

							$jsondata['result'][$k]['deptname']   = $part_time['deptname'];
							$jsondata['result'][$k]['posnname']   = $part_time['posnname'];
							$jsondata['result'][$k]['leader']     = $part_time['leadername'];
							$jsondata['result'][$k]['leadername'] = $part_time['leadername'];
						}

						//性别
						if (isset($v['gender']))
						{
							switch ($v['gender'])
							{
								case 0:
									$jsondata['result'][$k]['gender'] = '女';
									break;
								case 1:
									$jsondata['result'][$k]['gender'] = '男';
									break;
								default:
									$jsondata['result'][$k]['gender'] = '未知';
									break;
							}
						}

						//员工类型
						if (isset($v['type']))
						{
							switch ($v['type'])
							{
								case 0:
									$jsondata['result'][$k]['type'] = '正式员工';
									break;
								case 1:
									$jsondata['result'][$k]['type'] = '合同工';
									break;
								case 2:
									$jsondata['result'][$k]['type'] = '临时工';
									break;
								default:
									$jsondata['result'][$k]['type'] = '实习生';
									break;
							}
						}

						//离职类型
						if (isset($v['dimissiontype']))
						{
							switch ($v['dimissiontype'])
							{
								case 0:
									$jsondata['result'][$k]['dimissiontype'] = '主动离职';
									break;
								case 1:
									$jsondata['result'][$k]['dimissiontype'] = '被动离职';
									break;
								default:
									$jsondata['result'][$k]['dimissiontype'] = '';
									break;
							}
						}

						//离职日期
						if (isset($v['dimissiondate']))
						{
							if ($v['dimissiondate'] == '0000-00-00 00:00:00') $jsondata['result'][$k]['dimissiondate'] = '';
						}

						if (isset($v['dimissiontype']))
						{
							$dimissiontype_info                      = Apiclass::params_getone_byid($version, $prm_base, $v['dimissiontype']);
							$jsondata['result'][$k]['dimissiontype'] = $dimissiontype_info['ret'] == RetClass::SUCCESS ? $dimissiontype_info['data'] : '';
						}

						//在职状态
						if (isset($v['workingstate']))
						{
							switch ($v['workingstate'])
							{
								case EnumClass::SYS_EMP_STATE_NORMAL:
									$jsondata['result'][$k]['workingstate']  = '在职';
									$jsondata['result'][$k]['dimissiontype'] = '';
									$jsondata['result'][$k]['dimissiondate'] = '';
									$jsondata['result'][$k]['dimissionrsn']  = '';
									$jsondata['result'][$k]['description']   = '';
									break;
								case EnumClass::SYS_EMP_STATE_LEAVE:
									$jsondata['result'][$k]['workingstate']   = '离职';
									break;
								case EnumClass::SYS_EMP_STATE_HONORABLE:
									$jsondata['result'][$k]['workingstate']   = '退休';
									break;
								case EnumClass::SYS_EMP_STATE_RETIRE:
									$jsondata['result'][$k]['workingstate']   = '离休';
									break;
								default:
									$jsondata['result'][$k]['workingstate']   = '在职';
									$jsondata['result'][$k]['dimissiontype']  = '';
									$jsondata['result'][$k]['dimissiondate']  = '';
									$jsondata['result'][$k]['dimissionrsn']   = '';
									$jsondata['result'][$k]['description']    = '';
									break;
							}
						}

						//合同到期
						if (isset($v['contractdate']))
						{
							if ($v['contractdate'] == '0000-00-00 00:00:00') $jsondata['result'][$k]['contractdate'] = '';
						}

						if ($fields != 'local')
						{
							$formid = '96369933629261135';
							$objid  = isset($list[$k]['profid']) ? $list[$k]['profid'] : 0;
							$ret    = ApiClass::iweform_get_formitems_all($version, $prm_base, $formid, $objid, $req = null, $withsubmit = false, $type = 'add', $pagetype = EnumClass::FORM_PAGE_TYPE_EDIT, $terminal = EnumClass::TERMINAL_TYPE_WEB, $pageformsetdata = array());

							if ($ret['ret'] == RetClass::SUCCESS)
							{
								foreach ($ret['data']['item_com_data']['item'] as $key => $value)
								{
									//转换控件的值
									if (isset($ret['data']['item_setdata'][$key]))
									{
										switch ($ret['data']['item_setdata'][$key]['type'])
										{
											case 'Select': //下拉
												if (isset($ret['data']['item_com_data']['item'][$key . '_option']))
												{
													$select_option = explode('|', $ret['data']['item_com_data']['item'][$key . '_option']);
												}
												else
												{
													$select_option = array();
												}
												//取选项的值
												$select_value_option = array_map(function($item) {
													$item_arr = explode(':', $item);
													return $item_arr['0'];
												}, $select_option);
												//取选项的键
												$select_key_option = array_map(function($item) {
													$item_arr = explode(':', $item);
													return $item_arr['1'];
												}, $select_option);
												//合并
												$select_option = array_combine($select_key_option, $select_value_option);
												/* $new_select_option = array();
												  foreach ($select_option as $k_st => $v_st) {
												  $item_arr = explode(':', $v_st);
												  $new_select_option[$item_arr['1']] = $item_arr['0'];
												  } */
												if (isset($jsondata['result'][$k][$key])) $jsondata['result'][$k][$key] = isset($select_option[$value]) ? $select_option[$value] : '';
												break;
											case 'Area': //地点
												if (isset($ret['data']['item_com_data']['item'][$key]))
												{
													$area_option = CommonClass::json_decode($ret['data']['item_com_data']['item'][$key]);
												}
												else
												{
													$area_option = array();
												}

												if (!empty($area_option))
												{
													$area  = $area_option['province'];
													$area .=!empty($area_option['city']) ? ',' . $area_option['city'] : '';
													$area .=!empty($area_option['district']) ? ',' . $area_option['district'] : '';
													if (isset($jsondata['result'][$k][$key])) $jsondata['result'][$k][$key] = $area;
												}
												break;
											default:
												$jsondata['result'][$k][$key] = $ret['data']['item_com_data']['item'][$key];
												break;
										}
									}
								}
							}
						}
						$arr[$id] = $jsondata['result'][$k]; //相同的人员只保存一个
					}

					$arr                = array_values($arr); //重组key
					$jsondata['result'] = $arr;
					$data['jsondata']   = CommonClass::json_encode($jsondata);
				}
			}
		}
	}

	/**
	 * 转为列表需要显示的部门、职务、上司格式
	 * hky 20160419
	 * @param  [type] $prm_base   [description]
	 * @param  [type] $deptname   [description]
	 * @param  [type] $posnname   [description]
	 * @param  [type] $leadername [description]
	 * @param  [type] $parttime   [description]
	 * @param  [type] $sym   	  [description] 	//需要分割的符号
	 * @return [type]             [description]
	 */
	private static function change_part_time($prm_base, $deptname, $posnname, $leadername, $parttime, $ismain = 1, $sym = '、')
	{
		// $job['deptname'] = $deptname;
		// $job['posnname'] = $posnname;
		// $job['leadername'] = $leadername;
		//将主部门也保存到了parttime字段，故只读取parrtime字段数据  hky20101126
		$job['deptname']    = '';
		$job['posnname']    = '';
		$job['leadername']  = '';
		if (!empty($parttime)/* && $ismain == 1 */)
		{
			$parttime_array = CommonClass::json_decode($parttime);
			if ($parttime_array)
			{
				foreach ($parttime_array as $k => $v)
				{
					$job['deptname'] .=!empty($job['deptname']) && !empty($v['deptname']) ? $sym . $v['deptname'] : $v['deptname'];
					$job['posnname'] .=!empty($job['posnname']) && !empty($v['posnname']) ? $sym . $v['posnname'] : $v['posnname'];
					if ($v['leadername'] != '' && $v['leadername'] != ' ') $job['leadername'] .=!empty($job['leadername']) && !empty($v['leadername']) ? $sym . $v['leadername'] : $v['leadername'];
				}
			}
		}

		return $job;
	}

	/**
	 * [check_advanced_auth 检查模块高级授权配置]
	 * @param  [type] $version  [description]
	 * @param  [type] $prm_base [description]
	 * @param  [type] $moduleid [description]
	 * @param  [type] $params   [description]
	 * @return [type]           [description]
	 */
	public function check_advanced_auth($version, $prm_base, $moduleid, $params = array())
	{
		$this->advanced_auth = false;
		if (is_array($params)) extract($params);

		$formid = !empty($formid) ? $formid : 0;
		$setid  = !empty($setid) ? $setid : 0;
		$ret    = ApiClass::setting_get($version, $prm_base, EnumClass::OP_TYPE_SETTING, $moduleid, $formid, $setid);
		//返回的value:
		// EnumClass::SETTING_TYPE_NORMAL 普通模式
		// EnumClass::SETTING_TYPE_ADVANNCE 高级模式
		// var_dump($ret,Doo::db()->show_sql());
		if ($ret['ret'] == RetClass::SUCCESS && !empty($ret['data']['valuechar']) && $ret['data']['valuechar'] == EnumClass::SETTING_TYPE_ADVANNCE) $this->advanced_auth = true;

		return $this->advanced_auth;
	}

	/**
	 * [api_get_module_objdata 返回评论需要的业务表标题与url，各个模块重写]
	 * suson.20170106
	 * @param  [type] $version  [description]
	 * @param  [type] $prm_base [description]
	 * @param  [type] $params   [description]
	 * @return [type]           [description]
	 */
	public static function api_get_module_objdata($version, $prm_base, $params)
	{
		return self::ret();
	}

	/**
	 * [api_get_comment_obj_set 评论是否不公开]
	 * suson.20170106
	 * @param  [type] $version  [description]
	 * @param  [type] $prm_base [description]
	 * @param  [type] $params   [description]
	 * @return [type]           [description]
	 */
	public static function api_get_comment_obj_set($version, $prm_base, $params)
	{
		return self::ret(RetClass::SUCCESS);
	}

	/**
	 * [api_permit_check_base_logic 检查业务基础权限，如详情页然后是草稿状态时怎么处理，各个类重写]
	 * suson.20170109
	 * @param  [type] $version  [description]
	 * @param  [type] $prm_base [description]
	 * @param  [type] $params   [array(5) { ["objid"]=> string(17) "96819672808161502" ["chkobjid"]=> string(35) "96049481958555696,96197057806606628" ["actionid"]=> int(20434301) ["validatetype"]=> string(6) "action" ["data"]=> array(2) { ["objid"]=> string(17) "96819672808161502" ["actiontype"]=> int(20434301) } }]
	 * @return [type]           [description]
	 */
	public static function api_permit_check_base_logic($version, $prm_base, $params)
	{
		return self::ret(RetClass::SUCCESS);
	}

	/**
	 * 通用获取tab模板所需数据,可在各个模块重写
	 * HCW 2017.05.10
	 * @param  [type] $version  [description]
	 * @param  [type] $prm_base [description]
	 * @param  [type] $params   [description]
	 * @return [type]           [description]
	 */
	public static function api_get_tab($version, $prm_base, $params)
	{
		extract($params);
		//获取所有的tab的form_item
		$appmenulist = CustomappClass::get_module_menus($prm_base, $moduleid, 0);

		//是否为设计模式
		$isdesign    = isset($design) && $design == 1 ? true : false;

		//分级重组一二三级菜单，返回fisrt_tab, second_tab, third_tab, new_path，重组到$params中
		$level       = isset($level) ? intval($level) : 999999;
		$ret         = CustomappClass::graded_tab($version, $prm_base, $appmenulist, $level, $path, $moduleid, $isdesign);
		$params      = array_merge($params, $ret);

		//重组操作按钮，如新建等，返回 actiontab, actionwidth
		$ret         = CustomappClass::graded_actiontab($version, $prm_base, $appmenulist, $moduleid, $isdesign);
		$params      = array_merge($params, $ret);

		//获取模块图标等信息
		$ret    = CustomappClass::get_module_display_info($version, $prm_base, $moduleid, $params['first_tab'], $isdesign);
		$params = array_merge($params, $ret);

		return $params;
	}

	/**
	 * remove_invalid_module_group 去除模块列表中，没有模块的模块分组
	 * liujia.2017-1-10 16:47:55
	 * @param  [type] $version  [description]
	 * @param  [type] $prm_base [description]
	 * @param  [type] $module_list   模块列表
	 * @return [type]           [description]
	 */
	public static function remove_invalid_module_group($version, $prm_base, $module_list)
	{

		// 1.将模块区分出两个数组，一个数组分类的mid，一个数组模块的父类id
		// 2.判断两个数组的差集，unset掉原来数组的那个分类
		if (empty($module_list)) return $module_list;
		$group_mid_arr = $module_parentid_arr = array();

		//取出需要的数组
		foreach ($module_list as $value)
		{
			// 处理应用分组
			if ($value['moduletype'] == EnumClass::APP_TYPE_GROUP)
			{
				$group_mid_arr[] = $value['mid'];
			}
			else
			{
				$module_parentid_arr[] = $value['parentid'];
			}
		}
		$module_parentid_arr = array_unique($module_parentid_arr);

		foreach ($group_mid_arr as $value) if (!empty($value) && !in_array($value, $module_parentid_arr)) unset($module_list[$value]);

		return $module_list;
	}

	/**
	 * h5置顶的时候返回的状态
	 * @param  [type] $version  [description]
	 * @param  [type] $prm_base [description]
	 * @param  [type] $fldval   [description]
	 * @param  [type] $field    [description]
	 * @param  string $entid    [description]
	 * @return [type]           [description]
	 */
	public static function format_is_sort_top($version, $prm_base, $fldval, $field, $entid = '')
	{

		$is_top = 0;
		if ($field == 'sort')
		{
			if ($fldval == '-10000') $is_top = 1;
		}
		else
		{
			return $is_top;
		}

		return $is_top;
	}

	/**
	 * [oral_time 口语化时间]
	 * ljh2017-1-10 16:24
	 * @param  [type] $date [description]
	 * @return [type]       [description]
	 */
	public static function format_meeting_oral_time($version, $prm_base, $date, $field)
	{
		$dateTime        = strtotime($date); //发布新鲜事的日期的时间戳
		$dateforymd      = date('Y-m-d', strtotime($date)); //发布新鲜事的日期 y-m-d 形式
		$dateforyear     = date('Y', strtotime($date)); //发布新鲜事的日期的年份
		$nowtime         = time(); //当前时间的时间戳
		$nowtimeforyear  = date('Y'); //当前时间的年份
		$nowdate         = date('Y-m-d');
		$yesterday       = date("Y-m-d", strtotime("-1 day")); //当前时间的昨天的日期
		$beforeyesterday = date('Y-m-d', strtotime("-2 day")); //当前时间的前天的日期
		$threedayago     = date('Y-m-d', strtotime("-3 day")); //当前时间的3天前的日期
		$beforeyear      = date("Y", strtotime("-1 year")); //当前时间的上一年的年份

		$timeDifference  = $nowtime - $dateTime;
		if ($timeDifference < 60)
		{
			return date('H:i', strtotime($date));
		}
		else if ($timeDifference > 60 && $timeDifference < 3600)
		{
			$i = floor($timeDifference / 60);
			return date('H:i', strtotime($date));
		}
		else if ($timeDifference > 3600 && $nowdate == $dateforymd)
		{
			return date('H:i', strtotime($date));
		}
		else if ($dateforymd == $yesterday)
		{
			return date('昨天', strtotime($date));
		}
		else if ($dateforymd == $beforeyesterday)
		{
			return date('前天', strtotime($date));
		}
		else if ($dateforymd == $threedayago)
		{
			return date('n月j日', strtotime($date));
		}
		else if ($dateforyear < $nowtimeforyear)
		{
			return date('Y年m月d日', strtotime($date));
		}
		else
		{
			return date('n月j日', strtotime($date));
		}
	}

	/**
	 * [get_submit_backurl 获取h5提交按钮后跳转到的列表，各个类重写]
	 * Xiaogq 2017-1-13 14:00
	 * @param  [type] $moduleid [description]
	 * @return [string]  'path=XX&id=XX'
	 */
	public static function api_get_submit_backurl($version, $prm_base, $moduleid)
	{
		return self::ret(RetClass::SUCCESS);
	}

	public static function format_activitylist($version, $prm_base, $activityid)
	{
		if (empty($activityid)) return self::ret();

		$data         = '';
		$opt['table'] = 'activityday';
		$opt['where'] = 'id=? AND state=?';
		$opt['param'] = array($activityid, EnumClass::STATE_TYPE_NORMAL);
		$activity_ret = DbClass::get_one($prm_base, $opt, EnumClass::SQL_FLD_ADD_STATE_NONE, EnumClass::SQL_FLD_ADD_ENT_NORMAL);

		if (empty($activity_ret) || $activity_ret == false)
		{
			return self::ret();
		}
		else
		{
			extract($activity_ret);
		}

		if (!empty($posterid) && $posterid != false)
		{
			//获取原图
			$download_params = 'download=1&files=' . $posterid . '&entid=' . $prm_base->entid;
			$download_params = base64_encode($download_params);
			$downloadurl     = Doo::conf()->PRM_BASE->url . 'upload/ajax/gen_file?params=' . $download_params;
			$data           .= '<div class="activity-detail--img">
							<img src="' . $downloadurl . '" alt="">
						 </div>';
		}

		//活动开始日期
		$start_time = date('Y-m-d', strtotime($activitytime_start));
		//星期
		$week_time  = date('w', strtotime($activitytime_start));
		$week       = array(
			0 => '星期天',
			1 => '星期一',
			2 => '星期二',
			3 => '星期三',
			4 => '星期四',
			5 => '星期五',
			6 => '星期六'
		);

		//截止报名时间
		$other         = CommonClass::json_decode($otherset);
		$nowtime       = date('Y-m-d H:i', time());
		//otherset字段的截止时间优先处理 , 截止时间并没有启用的话，则使用活动开始时间作为截止时间
		$choose_time   = $other['otherset']['deline'] == true && !empty($other['otherset']['deline_val']) ? $other['otherset']['deline_val'] : $activitytime_start;
		$sign_end_time = ( strtotime($nowtime) - strtotime(date('Y-m-d H:i', strtotime($choose_time))) ) / 3600 / 24;
		$sign_end_time = str_replace('-', '', $sign_end_time); //去掉-号
		// 截止时间少于一天的话，按小时来计算
		if (round($sign_end_time) < 1)
		{
			$hour_time     = ( strtotime($nowtime) - strtotime(date('Y-m-d H:i', strtotime($choose_time))) ) / 3600;
			$hour_time     = str_replace('-', '', $hour_time); //去掉-号
			$sign_end_time = strtotime($choose_time) < time() ? '报名已截止' : round($hour_time) . '小时后截止报名';
		}
		else
		{
			$sign_end_time = strtotime($choose_time) < time() ? '报名已截止' : round($sign_end_time) . '天后截止报名';
		}

		$data .= '<div class="activity-detail-con">
					<ul>
						<li>开始时间：' . $start_time . ' ' . $week[$week_time] . '</li>
						<li>活动地点：' . $actiaddress . '</li>
						<li>报名时间：' . $sign_end_time . '</li>
					</ul>
				</div>';
		// var_dump($sign_end_time,$activitytime_start,$nowtime);exit();
		return $data;
	}

	/**
	 * [api_formitem_defset 各个控件获取模块本身定义的默认配置]
	 * suson.20170115
	 * @param  [type] $version  [description]
	 * @param  [type] $prm_base [description]
	 * @param  [type] $params   [description]
	 * @return [type]           [description]
	 */
	public static function api_formitem_defset($version, $prm_base, $params)
	{
		return self::ret();
	}

	/**
	 * [api_screenfld_action 各模块筛选(高级、快捷)搜索的搜索字段(移除不显示的字段)]
	 * @param  [type] $version  [description]
	 * @param  [type] $prm_base [description]
	 * @param  array  $params   [description]
	 * @return [type]           [description]
	 */
	public static function api_screen_searchfld($version, $prm_base, $params = array())
	{
		return self::ret();
	}

	/**
	 * 各模块自行排序form_item
	 * HCW 2017.04.27
	 * @param  [type] $version  [description]
	 * @param  [type] $prm_base [description]
	 * @param  [type] $params   [description]
	 * @return [type]           [description]
	 */
	public static function api_sort_form_items($version, $prm_base, $params)
	{
		return self::ret();
	}

	/**
	 * 根据模块id调用各自类里面的api_ngtable_column方法，类里面没有写该方法则继续走通用的方法
	 * hky 20170426
	 * @param  [type] $version    [description]
	 * @param  [type] $prm_base   [description]
	 * @param  [type] $listcolumn [description]
	 * @param  [type] $isoperate  [description]
	 * @param  [type] $moduleid   [description]
	 * @param  [type] $path       [description]
	 * @return [type]             [description]
	 */
	private static function get_ngtable_column($version, $prm_base, $listcolumn, $isoperate, $moduleid, $path, $reportpage)
	{
		$params = array(
			'listcolumn' => $listcolumn,
			'isoperate' => $isoperate,
			'moduleid' => $moduleid,
			'path' => $path,
			'reportpage' => $reportpage
		);
		if (!empty($moduleid))
		{
			$ret = Apiclass::enterprise_get_module_info($version, $prm_base, $moduleid, 'path');
			if ($ret['ret'] == RetClass::SUCCESS)
			{
				if (!empty($ret['data']['path']))
				{
					$path = CommonClass::json_decode($ret['data']['path']);
					$type = isset($path['type']) ? $path['type'] : '';
					$name = isset($path['name']) ? $path['name'] : '';
					if (!empty($type) && !empty($name))
					{
						$classname = ucfirst($name);
						return Doo::getModuleClass($type . '/' . $name, $classname . 'Class', 'ngtable_column', $version, $prm_base, $params);
					}
				}
			}
		}
		return self::ret();
	}

	/**
	 * 根据模块id调用各自类里面的api_ngtable_content方法，类里面没有写该方法则继续走通用的方法
	 * hky 20170426
	 * @param  [type] $version  [description]
	 * @param  [type] $prm_base [description]
	 * @param  [type] $result   [description]
	 * @param  [type] $columns  [description]
	 * @param  [type] $path     [description]
	 * @param  [type] $page     [description]
	 * @param  [type] $pk       [description]
	 * @param  [type] $moduleid [description]
	 * @param  string $reportpage     [description]
	 * @return [type]           [description]
	 */
	private static function get_ngtable_content($version, $prm_base, $result, $columns, $path, $page, $pk, $moduleid, $reportpage)
	{
		$params = array(
			'result' => $result,
			'columns' => $columns,
			'path' => $path,
			'page' => $page,
			'pk' => $pk,
			'moduleid' => $moduleid,
			'reportpage' => $reportpage
		);
		if (!empty($moduleid))
		{
			$ret = Apiclass::enterprise_get_module_info($version, $prm_base, $moduleid, 'path');
			if ($ret['ret'] == RetClass::SUCCESS)
			{
				if (!empty($ret['data']['path']))
				{
					$path = CommonClass::json_decode($ret['data']['path']);
					$type = isset($path['type']) ? $path['type'] : '';
					$name = isset($path['name']) ? $path['name'] : '';
					if (!empty($type) && !empty($name))
					{
						$classname = ucfirst($name);
						return Doo::getModuleClass($type . '/' . $name, $classname . 'Class', 'ngtable_content', $version, $prm_base, $params);
					}
				}
			}
		}
		return self::ret();
	}

	/**
	 * 获取详情页的地址
	 * pengxj 2017-1-13 14:00
	 *
	 */
	public static function format_getdetailurl($version, $prm_base, $fldval, $field, &$tag_val_arr = array(), $format, $path, $page, $extparams)
	{
		//查询选项的名称
		$ret = ApiClass::params_getone_byid($version, $prm_base, $fldval, $showkey = 'paramname');
		if ($ret['ret'] == RetClass::SUCCESS)
		{
			$tag_val_arr[$field] = $ret['data'];
			//获取参数名称
			$keyname             = substr($format, strpos($format, '#') + 1, strrpos($format, '#') - strpos($format, '#') - 1);
			$url                 = substr($format, 0, strpos($format, '#'));
			$tag_val_arr['link'] = $url . $extparams['row'][$keyname];
		}
		return $tag_val_arr;
	}

	/**
	 * 统计各模块未阅的记录数,不一定适合所有模块，有特殊定制时各模块重写该方法
	 * hky 20170510
	 * @param  [type] $version  [description]
	 * @param  [type] $prm_base [description]
	 * @param  array  $params   [description]
	 * @return [type]           [description]
	 */
	public static function api_count_unread($version, $prm_base, $params = array())
	{
		extract($params);
		return FormClass::get_unread($version, $prm_base, $moduleid, $userid);
	}

	/**
	 * 未阅提醒的自定义权限的判断 ljh 2017-6-8
	 * @param  [type] $version  [description]
	 * @param  [type] $prm_base [description]
	 * @param  array  $params   [description]
	 * @return [type]           [description]
	 */
	public static function api_custpermit_remind_unread($version, $prm_base, $params = array())
	{
		return self::ret(RetClass::SUCCESS);
	}

	/**
	 * 获取高级搜索需要的选项
	 * hky20170516
	 * @param  [type] $version  [description]
	 * @param  [type] $prm_base [description]
	 * @param  array  $params   [description]
	 * @return [type]           [description]
	 */
	public static function api_get_search_formitems($version, $prm_base, $params = array())
	{
		return self::ret();
	}

	/**
	 * 格式高级搜索的条件
	 * hky20170516
	 * @param  [type] $version  [description]
	 * @param  [type] $prm_base [description]
	 * @param  array  $params   [description]
	 * @return [type]           [description]
	 */
	public static function api_format_search_condition($version, $prm_base, $params = array())
	{
		return self::ret();
	}

	/**
	 * 审核操作自定义权限验证
	 * @param  [type] $version  [description]
	 * @param  [type] $prm_base [description]
	 * @param  array  $params   [description]
	 * @return [type]           [description]
	 */
	public static function api_audit_custcheck($version, $prm_base, $params = array())
	{
		return self::ret(RetClass::SUCCESS);
	}

	/**
	 * 检查验证参数，通过后将验证后结果赋值$this->params，否则按错误类型返回
	 * @param type $data 需要检查的参数数组
	 * @param type $rules 规则文件名
	 * @param type $page_type 错误类型
	 * @param string $url 错误返回路径
	 * @param type $type 错误页面返回类型
	 * @param type $exclude_rule_keys 不包括的规则字段
	 * @param type $necessary_rule_keys 必须的规则字段
	 * @return type
	 */
	public static function check_params($version, $prm_base, $data = array(), $rules = '', &$paramsarr, $page_type = null, $url = '', $type = '', $exclude_rule_keys = null, $necessary_rule_keys = null)
	{
		$default = array(
			'version' => $version,
			'prm_base' => $prm_base,
			'data' => $data,
			'rules' => $rules,
			'paramsarr' => $paramsarr,
			'page_type' => $page_type,
			'url' => $url,
			'type' => $type,
			'exclude_rule_keys' => $exclude_rule_keys,
			'necessary_rule_keys' => $necessary_rule_keys
		);
		/**
		 * 直接第二个是对象的时候
		 * 成立:直接用该参数
		 * 否则:所有参数向前走2位
		 */
		if (is_string($version) && is_object($prm_base))
		{
		}
		else
		{
			$data                = $default['version'];
			$rules               = $default['prm_base'];
			$paramsarr           = $default['data'];
			$page_type           = $default['rules'];
			$url                 = $default['paramsarr'];
			$type                = $default['page_type'];
			$exclude_rule_keys   = $default['url'];
			$necessary_rule_keys = $default['type'];
		}

		$data      = CommonClass::handle_params($data);  //过滤参数中的关键字   hky20170608
		//get defined rules and add show some error messages
		$validator = new ValidatorClass;
		$validator->checkMode = EnumClass::CHECK_PARAM_SKIP;

		if (is_null($page_type) || empty($page_type)) $page_type = EnumClass::PAGE_RET_MSG_TYPE_AJAX;

		//General.20150310 增加$data与$this->params是否数组的判断
		if (is_array($data) && is_array($paramsarr))
		{
			$data = array_merge($data, $paramsarr);
		}
		else if (!is_array($data) && is_array($paramsarr))
		{
			$data = $this->params;
		}
		else if (!is_array($data) && !is_array($paramsarr))
		{
			return self::ret();
			//$this->show_err(false,'TIPS_HANDLE_DATALOST',$page_type,$url,$type);
		}

		$oaret     = array();
		$result    = $validator->validate($data, $rules, $params, $oaret, $prm_base);
		$paramsarr = $params;

		if ($result) return self::ret();
			//$this->show_err(false,$result,$page_type,$url,$type);
	}

	/**
     * [导入]
     * hky 20170714
     * @param  [type] $version  [description]
     * @param  [type] $prm_base [description]
     * @param  [type] $params   [description]
     * @return [type]           [description]
     */
    public static function api_import_data_action($version, $prm_base, $params)
    {
    	return self::ret(RetClass::ERROR, 'FUNCTION_UNDEFINED');
    }

    /**
     * 导入后的操作
     * hky 20170714
     * @param  [type] $version  [description]
     * @param  [type] $prm_base [description]
     * @param  [type] $params   [description]
     * @return [type]           [description]
     */
    public static function api_import_data_after($version, $prm_base, $params)
    {
    	return self::ret();
    }

    /**
     * 获取各自定义的excel导入列
     * hky20170824
     * @param  [type] $version  [description]
     * @param  [type] $prm_base [description]
     * @param  [type] $params   [description]
     * @return [type]           [description]
     */
    public static function api_get_custom_columns($version, $prm_base, $params)
    {
    	return self::ret();
    }

	/**
	 * 页面获取标题 （含副标题）
	 * @param $version
	 * @param $prm_base
	 * @param $tbdata
	 * @param array $param 其他参数 如模块url ['moduleid'] = 'm96679686402257752';
	 * @author james.ou 2017-8-9 从新公告模块抽离出来
	 */
	public static function _get_titlebar($version, $prm_base, $tbdata, $param = array())
	{
		extract($param);
		if (isset($tbdata['keydata'])) $tbdata['keydata'] = CommonClass::json_decode($tbdata['keydata']);
		//获取部门
		if (isset($tbdata['keydata']['deptname']) && !empty($tbdata['keydata']['deptname']))
		{
			$deptname = $tbdata['keydata']['deptname'];
		}
		else
		{
			$depres   = ApiClass::enterprise_get_dept($version, $prm_base, 0, $tbdata['deptid']);
			$deptname = $depres['ret'] == RetClass::SUCCESS ? $depres['data']['deptname'] : '';
		}
		// 获取创建人
		if (isset($tbdata['keydata']['username']) && !empty($tbdata['keydata']['username']))
		{
			$username = $tbdata['keydata']['username'];
		}
		else
		{
			$ret      = ApiClass::user_get_info($version, $prm_base, $tbdata['userid'], 'profname');
			$username = $ret['ret'] == RetClass::SUCCESS && !empty($ret['data']['profname']) ? $ret['data']['profname'] : '';
		}

		$tbdata['updata'] = isset($tbdata['updata']) ? CommonClass::json_decode($tbdata['updata']) : array();
		// 获取修改人
		if (!empty($tbdata['updata']['username']))
		{
			$upusername = $tbdata['updata']['username'];
		}
		else if ($tbdata['userid'] == $tbdata['upuserid'])
		{
			$upusername = $username;
		}
		else
		{
			$ret        = ApiClass::user_get_info($version, $prm_base, $tbdata['upuserid'], 'profname');
			$upusername = $ret['ret'] == RetClass::SUCCESS && !empty($ret['data']['profname']) ? $ret['data']['profname'] : '';
		}

		// 获取修改人部门
		$updeptid    = isset($tbdata['updata']['deptid']) ? $tbdata['updata']['deptid'] : 0;
		$depres      = ApiClass::enterprise_get_dept($version, $prm_base, 0, $updeptid);
		$updeptname  = $depres['ret'] == RetClass::SUCCESS ? $depres['data']['deptname'] : '';

		$formtitle   = isset($tbdata['formtitle']) ? $tbdata['formtitle'] : '';
		$addate      = isset($tbdata['crtime']) ? $tbdata['crtime'] : '';
		$lastupdate  = isset($tbdata['uptime']) ? $tbdata['uptime'] : '';
		$terminal    = isset($tbdata['terminal']) ? $tbdata['terminal'] : '';
		$opstate     = isset($tbdata['opstate']) ? $tbdata['opstate'] : '';
		//精确到秒
		$addsecond   = strtotime($addate) !== false ? strtotime($addate) : '';
		//精确到秒
		$upsecond    = strtotime($lastupdate) !== false ? strtotime($lastupdate) : '';
		//时间转为Y-m-d H:i 省略秒
		$addate      = !empty($addsecond) ? date('Y-m-d H:i', $addsecond) : '';
		$lastupdate  = !empty($upsecond) ? date('Y-m-d H:i', $upsecond) : '';
		$publishtime = !empty($addate) ? $addate : $lastupdate;
		//获取终端
		$terminal    = CommonClass::get_terminal($terminal);

		$titres['title']     = $formtitle;
		$titres['defval']    = "{$username} 于 {$publishtime} 通过{$terminal}提交";
		$titres['createval'] = "创建人: {$deptname} {$username} 于 {$publishtime} 通过{$terminal} 提交";
		$titres['updateval'] = empty($addsecond) || $upsecond == $addsecond ? '' : "最后修改: {$updeptname} {$upusername} 于 {$lastupdate} 修改";
		$temparr             = array();
		$statustxt           = BaseClass::format_opstate($version, $prm_base, $opstate, $field = 'opstate', $temparr, $format = array('moduleid' => $moduleid));
		$titres['statusval'] = " 状态：" . $statustxt;

		if ($prm_base->terminal == EnumClass::TERMINAL_TYPE_H5)
		{
			$h5_res['subtitle']   = "{$username}创建于{$publishtime}";
			$h5_res['thirdtitle'] = $titres['statusval'];
			$h5_res['terminal']   = $prm_base->terminal;
			$h5_res['count']      = 0;
			return CommonClass::json_encode($h5_res);
		}

		return CommonClass::json_encode($titres);
	}

	/**
	 * [_get_commentdata 获取讨论区配置]
	 * @param  [type] $version  版本信息
	 * @param  [type] $prm_base 基本信息
	 * @param  [type] $moduleid 模块信息
	 * @param  [type] $tbdata   表单数据
	 * @param  [array] $param  其他参数 如模块url ['appurl'] = 'web/m96679686402257752/';
	 * @author james.ou 2017-8-9 从新公告模块抽离出来
	 */
	public static function get_commentdata($version, $prm_base, $moduleid, $tbdata, $param = array())
	{
		if (empty($moduleid) || empty($tbdata)) return '';

		extract($param);
		$objid     = !empty($tbdata['id']) ? $tbdata['id'] : 0;
		$formtitle = isset($tbdata['formtitle']) ? $tbdata['formtitle'] : '';
		$formid    = isset($tbdata['formid']) ? $tbdata['formid'] : '';
		$comsett   = isset($param['comsett']) ? $param['comsett'] : array();

		if (isset($tbdata['keydata'])) $tbdata['keydata'] = CommonClass::json_decode($tbdata['keydata']);
		// 获取创建人
		if (isset($tbdata['keydata']['username']) && !empty($tbdata['keydata']['username']))
		{
			$username = $tbdata['keydata']['username'];
		}
		else
		{
			$ret      = ApiClass::user_get_info($version, $prm_base, $tbdata['userid'], 'profname');
			$username = isset($ret['data']['profname']) ? $ret['data']['profname'] : '';
		}

		$objid_userid   = !empty($tbdata['userid']) ? $tbdata['userid'] : 0;
		$crtitle        = !empty($crtitle) ? $crtitle : '创建人';
		$objid_username = $username;
		extract($comsett);
		if (!isset($canseeall))
		{
			$comset    = isset($tbdata['comset']) ? CommonClass::json_decode($tbdata['comset']) : array(); //设置控件值
			$canseeall = !empty($comset['commentattr']) && $comset['commentattr'] == '1001' ? true : false; //公开评论
		}
		if (!empty($tbdata['userid']) && $prm_base->userid == $tbdata['userid'])
		{
			$canseeall = true; //创建人可以看到其他人评论 suson.20160829
		}
		$objurl                    = $prm_base->url . $appurl . 'page/pagedetail?formid=' . $formid . '&id=' . $objid;
		//评论的基本信息
		$comment['formid']         = $formid;
		$comment['title']          = !empty($title) ? $title : '讨论区';  //评论分割线标题
		$comment['userid']         = $prm_base->userid;	  //当前用户号
		$comment['username']       = $prm_base->username;	  //当前用户名
		$comment['moduleid']       = $moduleid;		//模块ID
		$comment['objid']          = $objid;		 //事务ID
		$comment['objtitle']       = $formtitle;		//事务标题
		$comment['objid_userid']   = $objid_userid;	   //事务创建人ID
		$comment['objid_username'] = $crtitle . $objid_username;	 //事务创建人姓名
		$comment['objurl']         = $objurl;		 //事务路径
		$comment['callbackurl']    = '';		  //添加成功后的回调
		$comment['islevel']        = isset($islevel) ? $islevel : 1;   //是否层级显示
		$comment['watermark']      = isset($watermark) ? $watermark : 0;  //水印
		$comment['candelete']      = isset($can_delete) ? $can_delete : 1;  //是否能删除评论
		$comment['canface']        = isset($canface) ? $canface : 1;   //是否支持表情
		$comment['canweibo']       = isset($canweibo) ? $canweibo : 1;   //是否支持@
		$comment['can_attr']       = isset($can_attr) ? $can_attr : 1;   //是否支持附件
		$comment['canseeall']      = isset($canseeall) ? $canseeall : 1;  //能看所有评论
		$comment['maxlength']      = isset($maxlength) ? $maxlength : 50000; //评论的最大长度
		$comment['base_url']       = isset($base_url) ? $base_url : '/';  //基地址
		$comment['commenttype']    = isset($commenttype) ? $commenttype : 0; //类型(是回复还是评论)
		$comment['autoreadonly']   = isset($autoreadonly) ? $autoreadonly : 0; //主回复是否默认勾上仅XX可见

		//这里获得回复控件的附件上传
		$uploadres            = ApiClass::file_get_upload_url($version, $prm_base, $moduleid, $objid, 0, 'web');
		$comment['uploadurl'] = $uploadres['ret'] == RetClass::SUCCESS ? $uploadres['data'] : '';
		//这里获得评论的内容
		$content              = ApiClass::comment_get($version, $prm_base, $objid, $moduleid, $comment['canseeall'], $comment['islevel']);
		$comment['data']      = !empty($content['data']['data']) ? $content['data']['data'] : '';

		return CommonClass::json_encode($comment);
	}

	/**
	 * [get_main_tbdata_by_objid 获取主表数据]
	 * 从超级推送class移动过来的 by james.ou 2017-8-14
	 * suson.20160711
	 * @param  string $version
	 * @param  obj $prm_base
	 * @param  string $moduleid
	 * @param  string $objid
	 * @return ret
	 */
	public static function get_main_tbdata_by_objid($version, $prm_base, $moduleid, $id, $tbname)
	{

		$tbdata = TableClass::get_table_data($prm_base, $tbname, $id);
		if (empty($tbdata[$tbname])) return self::ret();

		//焚毁状态不显示 hky20170113
		if (isset($tbdata[$tbname]['burnstate']) && $tbdata[$tbname]['burnstate'] == EnumClass::SALARY_TYPE_BURN_YES) return self::ret();

		/* $formid = isset($tbdata[$tbname]['formid']) ? $tbdata[$tbname]['formid'] : 0;
		  if(empty($formid)){
		  return self::ret();
		  } */
		return self::ret(RetClass::SUCCESS, $tbdata[$tbname]);
	}

    /**
	 * 检测引导页(有发布权限才显示) by james.ou 2017-9-12 迁移
	 */
	public static function web_check_guide_setting($version, $prm_base, $moduleid, $formid, $path='')
	{
		//是否拥有发布权限，发布人才有引导页
		$check_add_ret = ApiClass::permit_check_add($version, $prm_base, $moduleid, $formid, $moduleid);
		if ($check_add_ret['ret'] == RetClass::SUCCESS)
		{
			//检测引导页
			$guide_ret = ApiClass::guide_check_setting($version, $prm_base, $moduleid, EnumClass::PUBLIC_TYPE_GUIDE_SETTING, $path, 0);
			return $guide_ret;
		}
		return self::ret();
	}
	/**
     * 获取各自定义的h5第一级tab
     * HCW 2017.09.28
     */
    public static function api_get_h5_tab($version, $prm_base, $params)
    {
    	return self::ret();
    }

    /**
     * 获取各自定义的搜索条件
     * HCW 2017.09.28
     */
    public static function api_get_h5_condition($version, $prm_base, $params)
    {
    	return self::ret();
    }

    /**
     * 格式化各自模块的h5列表
     * HCW 2017.09.30
     */
    public static function api_format_h5_list($version, $prm_base, $params)
    {
    	return self::ret();
    }

    /**
     * 格式化各自模块的h5列表
     * HCW 2017.10.13
     */
    public static function api_format_h5_detail($version, $prm_base, $params)
    {
    	return self::ret();
    }

    /**
     * 格式化详情页的tabs
     */
    public static function api_format_h5_detail_tabs($version, $prm_base, $params)
    {
    	return self::ret();
    }

    /**
     * 获取新建页模块额外数据extdata
     */
    public static function api_get_h5_add_page_extdata($version, $prm_base, $params)
    {
    	return self::ret();
    }

    /**
     * 新建预览页的信息
     */
    public static function api_get_h5_add_preview_info($version, $prm_base, $params)
    {
    	return self::ret();
    }

   	/**
   	 * 获取阅读情况和确认情况
   	 */
    public static function api_get_h5_read_confirm_list($version, $prm_base, $params)
    {
    	return self::ret();
    }

    /**
     * 处理设置控件里的定时队列
     * HCW 2017.10.21
     */
    public static function api_handle_queue($version, $prm_base, $params)
    {
    	return self::ret();
    }

    /**
     * 修改插入前的form_tb_fields
     * HCW 2017.10.21
     */
    public static function api_change_form_tb_fields($version, $prm_base, $params)
    {
    	return self::ret();
    }

    /**
     * 获取角标
     * hky 20171025
     * @param  [type] $version  [description]
     * @param  [type] $prm_base [description]
     * @param  [type] $params   [description]
     * @return [type]           [description]
     */
    public static function api_get_corner($version, $prm_base, $params)
    {
    	return self::ret();
    }

    /**
     * 设置已阅
     * hky 20171028
     * @param  [type] $version  [description]
     * @param  [type] $prm_base [description]
     * @param  [type] $params   [description]
     * @return [type]           [description]
     */
    public static function api_set_read($version, $prm_base, $params)
    {
    	return self::ret();
    }

    /**
     * 循环推送系统消息
     * HCW 2017.11.02
     */
    public static function api_loop_push_send_msg($version, $prm_base, $params)
    {
    	return self::ret();
    }

    /**
     * 循环推送系统消息获取title和reminduser
     * HCW 2017.11.06
     */
    public static function api_loop_push_get_info($version, $prm_base, $params)
    {
    	return self::ret();
    }

    /**
     * 获取阅读列表
     */
    public static function api_read_list($version, $prm_base, $params)
    {
    	extract($params);
    	$total    = $unread_total = $read_total = 0;
		$all_data = $unread_data = $read_data = $userids = array();
		$readuser = $unreaduser = '';

		if($prm_base->terminal == EnumClass::TERMINAL_TYPE_WEB) $page++; //web前端阅读控件 0算第一页  客户端就1算第一页 suson.20160719

		$page    = $page<1 ? 1 : intval($page);
		$all_ret = ApiClass::iweform_user_op_search($version,$prm_base,$moduleid,$objid,$itemname,$items=array('read'=>'*'),$page,$pagesize);
		if($moduleid == '96781208775557283')
		{//计划汇报特殊处理
			$all_ret = ApiClass::report_get_read_data_h5($version, $prm_base, $objid, 'all', $page, $pagesize);
		}
		//array(5) { ["ret"]=> int(1000) ["data"]=> string(87) "[{"ids":["{\"read\": \"0\", \"userid\": \"95994514849661104\"}"],"read":"*","total":1}]" ["type"]=> int(3001) ["attr"]=> array(7) { ["setdata"]=> string(0) "" ["title"]=> string(0) "" ["content"]=> string(7) "success" ["icon"]=> string(0) "" ["theme"]=> string(7) "default" ["url"]=> string(0) "" ["refresh"]=> string(7) "refresh" } ["code"]=> int(1000) }
		if(!empty($all_ret['data']))
		{
			$all_data = CommonClass::json_decode($all_ret['data']);
			$total    = !empty($all_data[0]['total']) ? $all_data[0]['total'] : $total;
			// var_dump($all_data);
		}

		$unread_ret = ApiClass::iweform_user_op_search($version,$prm_base,$moduleid,$objid,$itemname,$items=array('read'=>'0'),$page,$pagesize);
		if($moduleid == '96781208775557283')
		{//计划汇报特殊处理
			$unread_ret = ApiClass::report_get_read_data_h5($version, $prm_base, $objid, 'unreader', $page, $pagesize);
		}
		if(!empty($unread_ret['data']))
		{
			$unread_data  = CommonClass::json_decode($unread_ret['data']);
			$unread_total = !empty($unread_data[0]['total']) ? $unread_data[0]['total'] : $unread_total;
			// var_dump($unread_data);
		}

		$read_ret = ApiClass::iweform_user_op_search($version,$prm_base,$moduleid,$objid,$itemname,$items=array('read'=>'1'),$page,$pagesize);
		 if($moduleid == '96781208775557283')
		 {//计划汇报特殊处理
			$read_ret = ApiClass::report_get_read_data_h5($version, $prm_base, $objid, 'reader', $page, $pagesize);
		}
		if(!empty($read_ret['data']))
		{
			$read_data  = CommonClass::json_decode($read_ret['data']);
			$read_total = !empty($read_data[0]['total']) ? $read_data[0]['total'] : $read_total;
			// var_dump($read_data);
		}

		//客户端提交的查询类型
		switch ($type)
		{
			case 'unreader':
			case EnumClass::OP_TYPE_UNREAD:
				$userids = !empty($unread_data[0]['ids']) ? $unread_data[0]['ids'] : array();
				$total   = $unread_total;
				break;
			case 'reader':
			case EnumClass::OP_TYPE_READ:
				$userids = !empty($read_data[0]['ids']) ? $read_data[0]['ids'] : array();
				$total   = $read_total;
				break;
			case 'all':
			default:
				$userids = !empty($all_data[0]['ids']) ? $all_data[0]['ids'] : array();
				break;
		}

		foreach($userids as $k=>$user)
		{
			$user_set = CommonClass::json_decode($user);
			if(empty($user_set['userid'])) continue;

			if(isset($user_set['read']) && $user_set['read'] == 0) $unreaduser .= $user_set['userid'].',';
			if(isset($user_set['read']) && $user_set['read'] == 1) $readuser .= $user_set['userid'].',';

		}

		$result          = array();
		$user_case       = ApiClass::datasource_user_read_newscase($version, $prm_base, trim($readuser, ','), trim($unreaduser,','), $page, $pagesize);
		$result['total'] = $total;
		$result['list']  = !empty($user_case['data']['userdata']) ? $user_case['data']['userdata'] : array();
		if($prm_base->terminal == EnumClass::TERMINAL_TYPE_WEB)
		{
			$path      = '/web/engine/ajax/readlist?itemname='.$itemname.'&moduleid='.$moduleid;
			$remindurl = 'web/engine/ajax/comremindunreaduser?itemname='.$itemname.'&moduleid='.$moduleid.'&id='.$objid;

			if($user_case['ret'] == RetClass::SUCCESS)
			{
				$user_case['data']['readercount']   = $read_total;
				$user_case['data']['unreadercount'] = $unread_total;
				$result = array(
					'title'=>"阅读情况",
					'remind'=>$remindurl,
					'listdata'=>$user_case['data'],
					'path'=>$path,
					'objid'=>$objid
				);
			}
			else
			{
				$result = array(
					'title'=>"阅读情况",
					'remind'=>'',
					'listdata'=>array('readercount' => 0, 'unreadercount' => 0,'readerdata' => array(), 'unreaderdata' => array()),
					'path'=>$path,
					'objid'=>$objid
				);
			}
		}

		//获取该记录的创建者用户ID ljh 2017-3-2
		$tbdata_ret               = FormClass::get_main_tbdata_by_objid($version,$prm_base,$moduleid,$objid);
		$result['userid']         = $tbdata_ret['ret'] == RetClass::SUCCESS && !empty($tbdata_ret['data']['userid']) ? $tbdata_ret['data']['userid'] : 0;
		$result['current_userid'] = $prm_base->userid;	//当前登录人，从前端用模板变量{@BaseTag::get_prm_base_val('userid')@}获取会导致userid固定为第一次访问页面的人员的userid，故从后台传参  hky20170427

		return self::ret(RetClass::SUCCESS, $result);
    }

    /**
     * h5详情页检测权限
     */
    public static function api_h5_detail_check_permit($version, $prm_base, $params){
    	return self::ret(RetClass::SUCCESS);
    }
}

?>