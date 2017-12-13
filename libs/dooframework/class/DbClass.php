<?php

class DbClass extends BaseClass{

	public static function exec($prm_base, $sql) {
		$ret = Doo::db()->exec($sql);
		//print_r(Doo::db()->show_sql());
		return $ret;
	}

	public static function query($prm_base, $sql, $param = null) {
		$ret = Doo::db()->query($sql, $param);
		//print_r(Doo::db()->show_sql());
		return $ret;
	}

	public static function fetch_one($prm_base, $sql, $param = null) {
		$ret = Doo::db()->fetchRow($sql,$param);
		//print_r(Doo::db()->show_sql());exit();
		return $ret;
	}

	public static function fetch_all($prm_base, $sql, $param = null) {
		$ret = Doo::db()->fetchAll($sql,$param);
		//print_r(Doo::db()->show_sql());exit();
		return $ret;
	}

	/**
	 * 获取指定表的详细信息
	 * @param $tbname 表名
	 * @param $opt 包括查询条件(条件里包含entid则$entid无需传入)及需更新/插入的字段$opt['fields']/除插入外必须存在$opt['param']
	 * @param $entid 企业ID（大于0代表需结合企业号为0的情况查询）
	 * @param $method 方法 get/find/count/del/update/insert
	 * General.20140505
	 */
	public static function get_one($prm_base, $opt = array(),
		$add_state = EnumClass::SQL_FLD_ADD_STATE_NORMAL, $add_ent = EnumClass::SQL_FLD_ADD_ENT_NORMAL) {

		$opt['limit'] = 1;
		$ret = self::_build_sql_query($prm_base, $opt,$add_state,$add_ent);
		if ($ret['ret'] ===  RetClass::SUCCESS) {
			return self::fetch_one($prm_base, $ret['data']['sql'], $ret['data']['param']);
		}else{
			// return $ret;;  格式不一致    update suson 2015-12-03
			return false;
		}
	}

	public static function get_all($prm_base, $opt = array(),
		$add_state = EnumClass::SQL_FLD_ADD_STATE_NORMAL, $add_ent = EnumClass::SQL_FLD_ADD_ENT_NORMAL) {

		$ret = self::_build_sql_query($prm_base, $opt,$add_state,$add_ent);
		if ($ret['ret'] ===  RetClass::SUCCESS) {
			return self::fetch_all($prm_base, $ret['data']['sql'], $ret['data']['param']);
		}else{
			// return $ret;;   格式不一致    update suson 2015-12-03
			return false;
		}
	}

	public static function insert_bat($prm_base, $opts = array(), $check_db = EnumClass::SQL_FLD_CHECK_DB_NORMAL,
		$run_type = EnumClass::SQL_RUN_SINGLE_NONE) {	//由于pdo，Windows的bug，导致一次运行多条SQL后，不能继续做其它数据库操作
		//$run_type = EnumClass::SQL_RUN_SINGLE_NORMAL) {
		$ret['ret'] = RetClass::ERROR;
		$sql = '';
		$param = array();
		foreach ($opts as $opt) {
			$tbname = $opt['table'];
			$ret_sigle = self::insert($prm_base, $opt, $check_db, $run_type);
			if ($ret_sigle['ret'] === RetClass::SUCCESS) {
				if ($ret_sigle['data']['sql'] != '') {
					if ($run_type == EnumClass::SQL_RUN_SINGLE_NONE) {
						$sql .= $ret_sigle['data']['sql'];
						$param = array_merge($param, $ret_sigle['data']['param']);
					}else{
						$ret_db = self::query($prm_base,$ret_sigle['data']['sql'],$ret_sigle['data']['param']);
						$ret['data']['db'][$tbname]['ret'] = $ret_db;
					}
					$ret['data']['db'][$tbname]['fields'] = $ret_sigle['data']['db'][$tbname]['fields'];
				}else{
					$ret['data']['db'][$tbname]['ret'] = $ret_sigle['data']['db'][$tbname]['ret'];
				}
			}
			$ret['ret'] = RetClass::SUCCESS;
		}
		if ($sql != '') {
			$ret_db = self::query($prm_base,$sql,$param);
			$ret['data']['db']['ret'] = $ret_db;
		}

		return $ret;
	}

	public static function insert($prm_base, $opt, $check_db = EnumClass::SQL_FLD_CHECK_DB_NORMAL, $run_type = EnumClass::SQL_RUN_SINGLE_NORMAL) {
		$ret['ret'] = RetClass::ERROR;
		$param = array();
		$valuestr = '';
		$fieldstr = '';

		$tbname = $opt['table'];
		$fields = $opt['fields'];
		$ret['data']['db'][$tbname]['fields'] = array();
		switch ($check_db) {
			case EnumClass::SQL_FLD_CHECK_DB_NORMAL:
				$fields_org = self::get_table_fields($prm_base,$tbname);
				$real_fields = self::get_real_table_fields($prm_base,$tbname);
				foreach ($fields_org as $field_org) {
					$fldname = $field_org['fldname'];
					//表里面实际没这个字段的，不做处理，suson.20160427
					if(array_search($fldname, $real_fields) === false){
						continue;
					}
					if (!isset($fields[$fldname])) {
						$ret_field = self::_set_field_insert_defval($prm_base, $field_org, $fields);
						$fields[$fldname] = $ret_field['value'];
						if ($ret_field['ret_fld'] !== '') {
							$ret['data']['db'][$tbname]['fields'][$fldname] = $fields[$fldname];
						}
					}
					// $fieldstr .= '`'.$fldname .'`,';
					$fieldstr .= Doo::conf()->DB_TAG . $fldname . Doo::conf()->DB_TAG .',';
					$valuestr .= '?,';
					$param[] = $fields[$fldname];
				}
				$valuestr = substr($valuestr, 0, strlen($valuestr)-1);
				$fieldstr = substr($fieldstr, 0, strlen($fieldstr)-1);
				//exit;
				break;

			default:
				foreach ($fields as $fldname => $value) {
					// $fieldstr .= '`'.$fldname .'`,';
					$fieldstr .= Doo::conf()->DB_TAG . $fldname . Doo::conf()->DB_TAG .',';
					$valuestr .= '?,';
					$param[] = $value;
				}
				$valuestr = substr($valuestr, 0, strlen($valuestr)-1);
				$fieldstr = substr($fieldstr, 0, strlen($fieldstr)-1);
				break;
		}

		$sql = "INSERT INTO {$tbname} ({$fieldstr}) VALUES ({$valuestr});";
		$ret['ret'] = RetClass::SUCCESS;

		if ($run_type === EnumClass::SQL_RUN_SINGLE_NORMAL) {
			$ret_db = self::query($prm_base,$sql,$param);
			$ret['data']['db'][$tbname]['ret'] = $ret_db;
			$ret['data']['sql'] = '';
			$ret['data']['param'] = array();
		}else{
			$ret['data']['sql'] = $sql;
			$ret['data']['param'] = $param;
		}
		$ret['ret'] = RetClass::SUCCESS;

		return $ret;
	}

	public static function update_bat($prm_base, $opts = array(), $add_state = EnumClass::SQL_FLD_ADD_STATE_NORMAL,
		$add_ent = EnumClass::SQL_FLD_ADD_ENT_NORMAL, $check_db = EnumClass::SQL_FLD_CHECK_DB_NORMAL,
		$run_type = EnumClass::SQL_RUN_SINGLE_NONE) {	//由于pdo，Windows的bug，导致一次运行多条SQL后，不能继续做其它数据库操作
		//$run_type = EnumClass::SQL_RUN_SINGLE_NORMAL) {
		$ret['ret'] = RetClass::ERROR;
		$sql = '';
		$param = array();
		foreach ($opts as $opt) {
			$tbname = $opt['table'];
			$ret_sigle = self::update($prm_base, $opt, $add_state, $add_ent, $check_db, $run_type);
			if ($ret_sigle['ret'] === RetClass::SUCCESS) {
				if ($ret_sigle['data']['sql'] != '') {
					if ($run_type == EnumClass::SQL_RUN_SINGLE_NONE) {
						$sql .= $ret_sigle['data']['sql'];
						$param = array_merge($param, $ret_sigle['data']['param']);
					}else{
						$ret_db = self::query($prm_base,$ret_sigle['data']['sql'],$ret_sigle['data']['param']);
						$ret['data']['db'][$tbname]['ret'] = $ret_db;
					}
				}else{
					$ret['data']['db'][$tbname]['ret'] = $ret_sigle['data']['db'][$tbname]['ret'];
				}
			}
			$ret['ret'] = RetClass::SUCCESS;
		}
		if ($sql != '') {
			$ret_db = self::query($prm_base,$sql,$param);
			$ret['data']['db']['ret'] = $ret_db;
		}

		return $ret;
	}

	/**
	 * @param  [type] $prm_base  [description]
	 * @param  [type] $opt       [description]
	 * @param  [type] $add_state [description]
	 * @param  [type] $add_ent   [description]
	 * @param  [type] $check_db  [description]
	 * @param  [type] $run_type  [description]
	 * @return [type]            [description]
	 */
	public static function update($prm_base, $opt, $add_state = EnumClass::SQL_FLD_ADD_STATE_NORMAL,
		$add_ent = EnumClass::SQL_FLD_ADD_ENT_NORMAL, $check_db = EnumClass::SQL_FLD_CHECK_DB_NORMAL, $run_type = EnumClass::SQL_RUN_SINGLE_NORMAL) {
		$ret['ret'] = RetClass::ERROR;
		$update_param = array();
		$field_and_value = '';
		$param = array();
		$where = '';
		$order = '';
		$limit = '';
		$canupdate = false;

		$tbname = $opt['table'];
		$fields = $opt['fields'];

		//生成更新条件
		switch ($add_ent) {
			case EnumClass::SQL_FLD_ADD_ENT_NORMAL:
				if ($prm_base->entid == 0) {
					//个人端，无企业ID，不添加
					$where = ' WHERE 1=1 ';
				}else{
					$where = ' WHERE entid = ? ';
					$param[] = $prm_base->entid;
				}
				break;
			default:
				$where = ' WHERE 1=1 ';
				break;
		}
		switch ($add_state) {
			case EnumClass::SQL_FLD_ADD_STATE_NORMAL:
				$where .= ' AND state = ? ';
				$param[] = EnumClass::STATE_TYPE_NORMAL;
				break;
			default:
				break;
		}

		$row = self::get_one($prm_base, $opt,$add_state,$add_ent);
		$real_fields = self::get_real_table_fields($prm_base,$tbname);

		//生成需默认赋值的字段
		switch ($check_db) {
			case EnumClass::SQL_FLD_CHECK_DB_NORMAL:
				$fields_org = self::get_table_fields($prm_base,$tbname);
				//print_r($fields_org);

				break;

			default:
				$fields_org = array(
					array('fldname' => 'uptime'),
					array('fldname' => 'upuserid'),
					array('fldname' => 'updata')
					);
				//这个方法跟update_fields很相似，不知为何写成了两个，原来的update方法不会加上upuserid和updata，现在加上去 HCW 2017.04.11
				foreach ($fields as $fldname => $value) {
					// $field_and_value .= '`'.$fldname .'`=?,';
					$field_and_value .= Doo::conf()->DB_TAG . $fldname . Doo::conf()->DB_TAG .'=?,';
					$update_param[] = $value;
				}
				break;
		}

		foreach ($fields_org as $field_org) {
			$fldname = $field_org['fldname'];
			//表里面实际没这个字段的，不做处理，suson.20160427
			if(array_search($fldname, $real_fields) === false){
				continue;
			}
			if (!isset($fields[$fldname])) {
				//不存在的字段,检查是否要添加默认值
				$field_value = isset($row[$fldname]) ? $row[$fldname] : '';
				$ret_field = self::_set_field_update_defval($prm_base, $field_org, $fields,$field_value);
				if ($ret_field['ret'] === RetClass::SUCCESS) {
					$fields[$fldname] = $ret_field['data'];
					//组合update sql语句
					// $field_and_value .= '`'.$fldname .'`=?,';
					$field_and_value .= Doo::conf()->DB_TAG . $fldname . Doo::conf()->DB_TAG .'=?,';
					$update_param[] = $ret_field['data'];
				}
			}else {
				//组合update sql语句
				// $field_and_value .= '`'.$fldname .'`=?,';
				$field_and_value .= Doo::conf()->DB_TAG . $fldname . Doo::conf()->DB_TAG .'=?,';
				$update_param[] = $fields[$fldname];
			}
		}

		if (isset($opt['where_fields']) && is_array($opt['where_fields'])) {
			foreach ($opt['where_fields'] as $fldname => $value) {
				$where .= " AND $fldname = ? ";
				$param[] = $value;
				$canupdate = true;
			}
		}
		if (isset($opt['where']) && !empty($opt['where'])) {
			$where .= ' AND '. $opt['where'];
			if(isset($opt['param']))
				$param = array_merge($param, $opt['param']);
			$canupdate = true;
		}
		if ($canupdate == false) {
			//如果更新没有足够条件，不能继续
			$ret['ret'] = RetClass::ERROR;
			$ret['code'] = RetClass::DB_WHERE_NONE_ERR;
			return $ret;
		}

		if(isset($opt['order']) && !empty($opt['order'])){
			$order .= ' ORDER BY ' . $opt['order'];
		}

		if(isset($opt['limit']) && !empty($opt['limit'])){
			if (Doo::conf()->DB_TYPE == 'pgsql') {
				$limit .= ' LIMIT ' . strtr($opt['limit'],array(','=>' OFFSET '));
			}else{
				$limit .= ' LIMIT ' . $opt['limit'];
			}
		}

		$param = array_merge($update_param, $param);
		$field_and_value = substr($field_and_value, 0, strlen($field_and_value)-1);

		$sql ="UPDATE {$tbname} SET {$field_and_value} {$where} {$order} {$limit};";

		if ($run_type === EnumClass::SQL_RUN_SINGLE_NORMAL) {
			$ret_db = self::query($prm_base,$sql,$param);
			$ret['data']['db'][$tbname]['ret'] = $ret_db;
			$ret['data']['sql'] = '';
			$ret['data']['param'] = array();
		}else{
			$ret['data'] = array('sql' => $sql, 'param' => $param);
		}
		$ret['ret'] = RetClass::SUCCESS;

		return $ret;
	}

	public static function update_del_bat($prm_base, $opts = array(), $add_state = EnumClass::SQL_FLD_ADD_STATE_NORMAL,
		$add_ent = EnumClass::SQL_FLD_ADD_ENT_NORMAL, $check_db = EnumClass::SQL_FLD_CHECK_DB_NORMAL,
		$run_type = EnumClass::SQL_RUN_SINGLE_NONE) {	//由于pdo，Windows的bug，导致一次运行多条SQL后，不能继续做其它数据库操作
		//$run_type = EnumClass::SQL_RUN_SINGLE_NORMAL) {
		$ret['ret'] = RetClass::ERROR;
		$sql = '';
		$param = array();
		foreach ($opts as $opt) {
			$tbname = $opt['table'];
			$opt['fields']['state'] = EnumClass::STATE_TYPE_DELETE;
			$ret_sigle = self::update_fields($prm_base, $opt, $add_state, $add_ent, $check_db, $run_type);
			if ($ret_sigle['ret'] === RetClass::SUCCESS) {
				if ($ret_sigle['data']['sql'] != '') {
					if ($run_type == EnumClass::SQL_RUN_SINGLE_NONE) {
						$sql .= $ret_sigle['data']['sql'];
						$param = array_merge($param, $ret_sigle['data']['param']);
					}else{
						$ret_db = self::query($prm_base,$ret_sigle['data']['sql'],$ret_sigle['data']['param']);
						$ret['data']['db'][$tbname]['ret'] = $ret_db;
					}
				}else{
					$ret['data']['db'][$tbname]['ret'] = $ret_sigle['data']['db'][$tbname]['ret'];
				}
			}
			$ret['ret'] = RetClass::SUCCESS;
		}
		if ($sql != '') {
			$ret_db = self::query($prm_base,$sql,$param);
			$ret['data']['db']['ret'] = $ret_db;
		}

		return $ret;
	}

	public static function update_del($prm_base, $opt, $add_state = EnumClass::SQL_FLD_ADD_STATE_NORMAL,
		$add_ent = EnumClass::SQL_FLD_ADD_ENT_NORMAL, $check_db = EnumClass::SQL_FLD_CHECK_DB_NORMAL,$run_type = EnumClass::SQL_RUN_SINGLE_NORMAL) {
		$opt['fields']['state'] = EnumClass::STATE_TYPE_DELETE;
		return self::update_fields($prm_base, $opt, $add_state, $add_ent, $check_db,$run_type);
	}

	public static function update_state_bat($prm_base, $opts = array(), $state_new = EnumClass::STATE_TYPE_NORMAL, $state_old = EnumClass::STATE_TYPE_INVALID,
		$add_ent = EnumClass::SQL_FLD_ADD_ENT_NORMAL, $check_db = EnumClass::SQL_FLD_CHECK_DB_NORMAL,
		$run_type = EnumClass::SQL_RUN_SINGLE_NONE) {	//由于pdo，Windows的bug，导致一次运行多条SQL后，不能继续做其它数据库操作
		//$run_type = EnumClass::SQL_RUN_SINGLE_NORMAL) {
		$ret['ret'] = RetClass::ERROR;
		$sql = '';
		$param = array();
		foreach ($opts as $opt) {
			$tbname = $opt['table'];
			if (!isset($opt['fields']['state'])) {
				$opt['where_fields']['state'] = $state_old;
			}
			if (!isset($opt['fields']['state'])) {
				$opt['fields']['state'] = $state_new;
			}
			$ret_sigle = self::update_fields($prm_base, $opt, EnumClass::SQL_FLD_ADD_STATE_NONE, $add_ent, $check_db, $run_type);
			if ($ret_sigle['ret'] === RetClass::SUCCESS) {
				if ($ret_sigle['data']['sql'] != '') {
					if ($run_type == EnumClass::SQL_RUN_SINGLE_NONE) {
						$sql .= $ret_sigle['data']['sql'];
						$param = array_merge($param, $ret_sigle['data']['param']);
					}else{
						$ret_db = self::query($prm_base,$ret_sigle['data']['sql'],$ret_sigle['data']['param']);
						$ret['data']['db'][$tbname]['ret'] = $ret_db;
					}
				}else{
					$ret['data']['db'][$tbname]['ret'] = $ret_sigle['data']['db'][$tbname]['ret'];
				}
			}
			$ret['ret'] = RetClass::SUCCESS;
		}
		if ($sql != '') {
			$ret_db = self::query($prm_base,$sql,$param);
			$ret['data']['db']['ret'] = $ret_db;
		}

		return $ret;
	}

	public static function update_state($prm_base, $opt, $state_new = EnumClass::STATE_TYPE_NORMAL, $state_old = EnumClass::STATE_TYPE_INVALID,
		$add_ent = EnumClass::SQL_FLD_ADD_ENT_NORMAL, $check_db = EnumClass::SQL_FLD_CHECK_DB_NORMAL,$run_type = EnumClass::SQL_RUN_SINGLE_NORMAL) {
		if (!isset($opt['fields']['state'])) {
			$opt['fields']['state'] = $state_new;
		}
		if (!isset($opt['where_fields']['state'])) {
			$opt['where_fields']['state'] = $state_old;
		}
		return self::update_fields($prm_base, $opt, EnumClass::SQL_FLD_ADD_STATE_NONE, $add_ent, $check_db,$run_type);
	}

	public static function update_fields_bat($prm_base, $opts = array(), $add_state = EnumClass::SQL_FLD_ADD_STATE_NORMAL,
		$add_ent = EnumClass::SQL_FLD_ADD_ENT_NORMAL, $check_db = EnumClass::SQL_FLD_CHECK_DB_NORMAL,
		$run_type = EnumClass::SQL_RUN_SINGLE_NONE) {	//由于pdo，Windows的bug，导致一次运行多条SQL后，不能继续做其它数据库操作
		//$run_type = EnumClass::SQL_RUN_SINGLE_NORMAL) {
		$ret['ret'] = RetClass::ERROR;
		$sql = '';
		$param = array();
		foreach ($opts as $opt) {
			$tbname = $opt['table'];
			$ret_sigle = self::update_fields($prm_base, $opt, $add_state, $add_ent, $check_db, $run_type);
			if ($ret_sigle['ret'] === RetClass::SUCCESS) {
				if ($ret_sigle['data']['sql'] != '') {
					if ($run_type == EnumClass::SQL_RUN_SINGLE_NONE) {
						$sql .= $ret_sigle['data']['sql'];
						$param = array_merge($param, $ret_sigle['data']['param']);
					}else{
						$ret_db = self::query($prm_base,$ret_sigle['data']['sql'],$ret_sigle['data']['param']);
						$ret['data']['db'][$tbname]['ret'] = $ret_db;
					}
				}else{
					$ret['data']['db'][$tbname]['ret'] = $ret_sigle['data']['db'][$tbname]['ret'];
				}
			}
			$ret['ret'] = RetClass::SUCCESS;
		}
		if ($sql != '') {
			$ret_db = self::query($prm_base,$sql,$param);
			$ret['data']['db']['ret'] = $ret_db;
		}

		return $ret;
	}

	public static function update_fields($prm_base, $opt, $add_state = EnumClass::SQL_FLD_ADD_STATE_NORMAL,
		$add_ent = EnumClass::SQL_FLD_ADD_ENT_NORMAL, $check_db = EnumClass::SQL_FLD_CHECK_DB_NORMAL,$run_type = EnumClass::SQL_RUN_SINGLE_NORMAL) {
		$ret['ret'] = RetClass::ERROR;
		$field_and_value = '';
		$update_param = array();
		$where = '';
		$order = '';
		$limit = '';
		$param = array();

		$tbname = $opt['table'];
		$fields = $opt['fields'];
		$real_fields = self::get_real_table_fields($prm_base,$tbname);
		switch ($check_db) {
			case EnumClass::SQL_FLD_CHECK_DB_NORMAL:
				$fields_org = self::get_table_fields($prm_base,$tbname);

				break;

			default:
				$fields_org = array(
					array('fldname' => 'uptime'),
					array('fldname' => 'upuserid'),
					array('fldname' => 'updata')
					);
				foreach ($fields as $fldname => $value) {
					// $field_and_value .= '`'.$fldname .'`=?,';
					$field_and_value .= Doo::conf()->DB_TAG . $fldname . Doo::conf()->DB_TAG .'=?,';
					$update_param[] = $value;
				}
				break;
		}

		$row = self::get_one($prm_base, $opt,$add_state,$add_ent);
		//生成需默认赋值的字段
		foreach ($fields_org as $field_org) {
			$fldname = $field_org['fldname'];
			//表里面实际没这个字段的，不做处理，suson.20160427
			if(array_search($fldname, $real_fields) === false){
				continue;
			}
			if (!isset($fields[$fldname])) {
				//不存在的字段,检查是否要添加默认值
				$field_value = isset($row[$fldname]) ? $row[$fldname] : '';
				$ret_field = self::_set_field_update_defval($prm_base, $field_org, $fields,$field_value);
				if ($ret_field['ret'] === RetClass::SUCCESS) {
					// $field_and_value .= ' `'.$fldname .'`=?,';
					$field_and_value .= Doo::conf()->DB_TAG . $fldname . Doo::conf()->DB_TAG .'=?,';
					$update_param[] = $ret_field['data'];
				}
			}else{
				// $field_and_value .= ' `'.$fldname .'`=?,';
				$field_and_value .= Doo::conf()->DB_TAG . $fldname . Doo::conf()->DB_TAG .'=?,';
				$update_param[] = $fields[$fldname];
			}
		}

		if (isset($opt['fields_add'])) {
			foreach ($opt['fields_add'] as $fldname => $value) {
				// $field_and_value .= "`$fldname`=`$fldname` + ?,";
				$field_and_value .= Doo::conf()->DB_TAG . $fldname . Doo::conf()->DB_TAG .'='. Doo::conf()->DB_TAG . $fldname . Doo::conf()->DB_TAG  .' + ?,';
				$update_param[] = $value;
			}
		}

		if (isset($opt['fields_ext'])) {
			foreach ($opt['fields_ext'] as $fldname => $value) {
				// $field_and_value .= "`$fldname`=$value,";
				$field_and_value .= Doo::conf()->DB_TAG . $fldname . Doo::conf()->DB_TAG ."=$value,";
			}
			if (isset($opt['param_ext'])) {
				$update_param = array_merge($update_param,$opt['param_ext']);
			}
		}

		// $field_and_value .= ' `state`=?,';
		// $update_param[] = EnumClass::STATE_TYPE_DELETE;

		//生成更新条件
		switch ($add_ent) {
			case EnumClass::SQL_FLD_ADD_ENT_NORMAL:
				if ($prm_base->entid == 0) {
					//个人端，无企业ID，不添加
					$where = ' WHERE 1=1 ';
				}else{
					$where = ' WHERE entid = ? ';
					$param[] = $prm_base->entid;
				}
				break;
			default:
				$where = ' WHERE 1=1 ';
				break;
		}
		switch ($add_state) {
			case EnumClass::SQL_FLD_ADD_STATE_NORMAL:
				$where .= ' AND state = ? ';
				$param[] = EnumClass::STATE_TYPE_NORMAL;
				break;
			default:
				break;
		}

		if (isset($opt['where_fields']) && !empty($opt['where_fields']) && is_array($opt['where_fields'])) {
			foreach ($opt['where_fields'] as $fldname => $fldval) {
				// $where .= " AND `$fldname` = ? ";
				$where .= ' AND '. Doo::conf()->DB_TAG . $fldname . Doo::conf()->DB_TAG .' = ? ';
				$param[] = $fldval;
			}
		}

		if (isset($opt['where'])) {
			$where .= ' AND '. $opt['where'];
			if(isset($opt['param'])){
				$param = array_merge($param, $opt['param']);
			}
		}

		if(isset($opt['order']) && !empty($opt['order'])){
			$order .= ' ORDER BY ' . $opt['order'];
		}

		if(isset($opt['limit']) && !empty($opt['limit'])){
			if (Doo::conf()->DB_TYPE == 'pgsql') {
				$limit .= ' LIMIT ' . strtr($opt['limit'],array(','=>' OFFSET '));
			}else{
				$limit .= ' LIMIT ' . $opt['limit'];
			}
		}

		$param = array_merge($update_param, $param);
		$field_and_value = substr($field_and_value, 0, strlen($field_and_value)-1);

		$sql ="UPDATE {$tbname} SET {$field_and_value} {$where} {$order} {$limit};";
		if ($run_type === EnumClass::SQL_RUN_SINGLE_NORMAL) {
			$ret_db = self::query($prm_base,$sql,$param);
			$ret['data']['db'][$tbname]['ret'] = $ret_db;
			$ret['data']['sql'] = '';
			$ret['data']['param'] = array();
		}else{
			$ret['data'] = array('sql' => $sql, 'param' => $param);
		}
		$ret['ret'] = RetClass::SUCCESS;

		return $ret;
	}

	public static function delete_bat($prm_base, $opts = array(), $add_state = EnumClass::SQL_FLD_ADD_STATE_NORMAL,
		$add_ent = EnumClass::SQL_FLD_ADD_ENT_NORMAL,
		$run_type = EnumClass::SQL_RUN_SINGLE_NONE) {	//由于pdo，Windows的bug，导致一次运行多条SQL后，不能继续做其它数据库操作
		//$run_type = EnumClass::SQL_RUN_SINGLE_NORMAL) {
		$ret['ret'] = RetClass::ERROR;
		$tbname = $opt['table'];
		foreach ($opts as $opt) {
			$ret_sigle = self::delete($prm_base, $opt, $add_state, $add_ent, $run_type);
			if ($ret_sigle['ret'] === RetClass::SUCCESS) {
				if ($ret_sigle['data']['sql'] != '') {
					$ret_db = self::query($prm_base,$ret_sigle['data']['sql'],$ret_sigle['data']['param']);
					$ret['data']['db'][$tbname]['ret'] = $ret_db;
				}else{
					$ret['data']['db'][$tbname]['ret'] = $ret_sigle['data']['db'][$tbname]['ret'];
				}
			}
			$ret['ret'] = RetClass::SUCCESS;
		}

		return $ret;
	}

	public static function delete($prm_base, $opt, $add_state = EnumClass::SQL_FLD_ADD_STATE_NORMAL,
		$add_ent = EnumClass::SQL_FLD_ADD_ENT_NORMAL, $run_type = EnumClass::SQL_RUN_SINGLE_NORMAL) {
		$tbname = $opt['table'];
		$ret = self::_build_sql_delete($prm_base, $opt,$add_state,$add_ent);
		if ($run_type === EnumClass::SQL_RUN_SINGLE_NORMAL) {
			if ($ret['ret'] ===  RetClass::SUCCESS) {
				$ret_db = self::query($prm_base, $ret['data']['sql'], $ret['data']['param']);
				$ret['data'] = array('ret' => $ret_db, 'sql' => '', 'param' => array());
				$ret['data']['db'][$tbname]['ret'] = $ret_db;
				$ret['data']['sql'] = '';
				$ret['data']['param'] = array();
			}
		}

		return $ret;
	}

	private static function _build_sql_query($prm_base, $opt = array(),
		$add_state = EnumClass::SQL_FLD_ADD_STATE_NORMAL, $add_ent = EnumClass::SQL_FLD_ADD_ENT_NORMAL) {
		if (!isset($opt['table']) || empty($opt['table']) || empty($opt)) {
			return self::ret(RetClass::ERROR);
		}

		$tbname = $opt['table'];
		$select = '*';
		$where = '';
		$groupby = '';
		$order = '';
		$limit = '';
		$param = array();

		if(isset($opt['select']) && !empty($opt['select'])){
			$select = $opt['select'];
		}

		switch ($add_ent) {
			case EnumClass::SQL_FLD_ADD_ENT_NORMAL:
				if (isset($opt['where_fields']['entid'])) {
					$where .= ' WHERE entid = ? ';
					$param[] = $opt['where_fields']['entid'];
					unset($opt['where_fields']['entid']);
				}else if ($prm_base->entid == 0) {
					//个人端，无企业ID，不添加
					$where = ' WHERE 1=1 ';
				}else{
					$where = ' WHERE (entid = ? OR entid = \'0\') ';
					$param[] = $prm_base->entid;
				}
				break;
			default:
				$where = ' WHERE 1=1 ';
				break;
		}
		switch ($add_state) {
			case EnumClass::SQL_FLD_ADD_STATE_NORMAL:
				if (isset($opt['where_fields']['state'])) {
					$where .= ' AND state = ? ';
					$param[] = $opt['where_fields']['state'];
					unset($opt['where_fields']['state']);
				}else{
					$where .= ' AND state = ? ';
					$param[] = EnumClass::STATE_TYPE_NORMAL;
				}
				break;
			default:
				break;
		}

		if (isset($opt['where_fields']) && !empty($opt['where_fields']) && is_array($opt['where_fields'])) {
			foreach ($opt['where_fields'] as $fld => $fldval) {
				// $where .= " AND `$fld` = ? ";
				$where .= ' AND '. Doo::conf()->DB_TAG . $fld . Doo::conf()->DB_TAG .' = ? ';
				$param[] = $fldval;
			}
		}

		if(isset($opt['where']) && !empty($opt['where'])){
			$where .= ' AND ' . $opt['where'];
			if (isset($opt['param'])) {
				$param = array_merge($param,$opt['param']);
			}

		}

		if(isset($opt['group']) && !empty($opt['group'])){
			$groupby .= ' GROUP BY ' . $opt['group'];
		}

		if(isset($opt['order']) && !empty($opt['order'])){
			$order .= ' ORDER BY ' . $opt['order'];
		}

		if(isset($opt['limit']) && !empty($opt['limit'])){
			if (Doo::conf()->DB_TYPE == 'pgsql') {
				$limit .= ' LIMIT ' . strtr($opt['limit'],array(','=>' OFFSET '));
			}else{
				$limit .= ' LIMIT ' . $opt['limit'];
			}
		}

		$data['sql'] = "SELECT {$select} FROM {$tbname} {$where} {$groupby} {$order} {$limit}";
		$data['param'] = $param;
		// LogClass::log_emerg($data,__FILE__,__LINE__,EnumClass::LOG_TYPE_VAR_DUMP);

		return  self::ret(RetClass::SUCCESS,$data);
	}

	private static function _build_sql_delete($prm_base, $opt = array(),
		$add_state = EnumClass::SQL_FLD_ADD_STATE_NORMAL, $add_ent = EnumClass::SQL_FLD_ADD_ENT_NORMAL) {
		if (!isset($opt['table']) || empty($opt['table']) || empty($opt)) {
			return self::ret(RetClass::ERROR);
		}

		$tbname = $opt['table'];
		$where = '';
		$order = '';
		$limit = '';
		$param = array();

		switch ($add_ent) {
			case EnumClass::SQL_FLD_ADD_ENT_NORMAL:
				if ($prm_base->entid == 0) {
					//个人端，无企业ID，不添加
					$where = ' WHERE 1=1 ';
				}else{
					$where = ' WHERE entid = ? ';
					$param[] = $prm_base->entid;
				}
				break;
			default:
				$where = ' WHERE 1=1 ';
				break;
		}
		switch ($add_state) {
			case EnumClass::SQL_FLD_ADD_STATE_NORMAL:
				$where .= ' AND state = ? ';
				$param[] = EnumClass::STATE_TYPE_NORMAL;
				break;
			default:
				break;
		}

		if (isset($opt['where_fields']) && !empty($opt['where_fields']) && is_array($opt['where_fields'])) {
			foreach ($opt['where_fields'] as $fldname => $fldval) {
				// $where .= " AND `$fldname` = ? ";
				$where .= ' AND '. Doo::conf()->DB_TAG . $fldname . Doo::conf()->DB_TAG ." = ? ";
				$param[] = $fldval;
			}
		}

		if(isset($opt['where']) && !empty($opt['where'])){
			$where .= ' AND ' . $opt['where'];
			$param = array_merge($param,$opt['param']);
		}

		if(isset($opt['order']) && !empty($opt['order'])){
			$order .= ' ORDER BY ' . $opt['order'];
		}

		if(isset($opt['limit']) && !empty($opt['limit'])){
			if (Doo::conf()->DB_TYPE == 'pgsql') {
				$limit .= ' LIMIT ' . strtr($opt['limit'],array(','=>' OFFSET '));
			}else{
				$limit .= ' LIMIT ' . $opt['limit'];
			}
		}

		$data['sql'] = "DELETE FROM {$tbname} {$where} {$order} {$limit}";
		$data['param'] = $param;

		return  self::ret(RetClass::SUCCESS,$data);
	}

	//设置插入默认值
	private static function _set_field_insert_defval($prm_base, $field_org, $field_org_value = array(), $field_value = null) {
		$ret_fld = '';
		switch ($field_org['fldname']) {
			case 'userid':
				$field_value = isset($prm_base->userid) ? $prm_base->userid : 0; //用户ID
				//$fields_org = self::get_table_fields($prm_base,$tbname);
				break;
			case 'entid':
				$field_value = isset($prm_base->entid) ? $prm_base->entid : 0; //企业ID
				break;
			case 'deptid':
				$field_value = isset($prm_base->deptid) ? $prm_base->deptid : 0; //主部门ID
				break;
			case 'posnid':
				$field_value = isset($prm_base->posnid) ? $prm_base->posnid : 0; //主职位ID
				break;
			case 'terminal':
				$field_value = isset($prm_base->terminal) ? $prm_base->terminal : EnumClass::TERMINAL_TYPE_WEB; //终端类型 默认web终端
				break;
			case 'uptime':
				$field_value = CommonClass::get_datetime();	//插入时默认写入更新时间
				break;
			case 'crtime':
				$field_value = CommonClass::get_datetime();	//插入时默认写入创建时间
				break;
			case 'applytime':
				$field_value = CommonClass::get_datetime();	//插入时默认写入申请时间
				break;
			case 'keydata':	//搜索字段
				//General.20151222 字段默认值处理
				$field_value = self::db_defval_handle($prm_base, $field_org_value, $field_value, 'keydata');
				break;
			default:
				if ($field_org['defval'] == 'build_primary_id') {
					$field_value = CommonClass::builder_primary_id();
				}else{
					// $field_value = $field_org['defval'];
					// 去掉多余的引号，update suson 2015.9.15
					$field_value = trim($field_org['defval'],'\'');
				}
				if ($field_org['rettype'] == EnumClass::SQL_FLD_RET_NORMAL) {
					$ret_fld = $field_org['fldname'];
				}
				break;
		}
		//var_dump($field_value);exit;
		return array('value' => $field_value, 'ret_fld' => $ret_fld);
	}

	/*
	 * 默认处理字段
	 * General.20150417
	*/
	private static function db_defval_handle($prm_base, $field_org_value, $field_value, $fldname = 'keydata'){
		if(empty($fldname)){
			return '';
		}
		switch ($fldname) {
			case 'keydata':	//搜索字段
				$keydata = '';
				if(is_null($field_value) || $field_value == ''){//初始写入搜索信息
					if(isset($prm_base->username) && !empty($prm_base->username) && isset($prm_base->deptname) && !empty($prm_base->deptname) && isset($prm_base->posnname) && !empty($prm_base->posnname)){
						$keydata['username'] = $prm_base->username;
						$keydata['deptname'] = $prm_base->deptname;
						$keydata['posnname'] = $prm_base->posnname;
					}else if(!empty($prm_base->userid)){
						$user = ApiClass::user_get_info('3.0.0.0', $prm_base, $prm_base->userid, 'profname,deptname,posnname');
						if($user['ret'] == RetClass::SUCCESS){
							$keydata['username'] = isset($user['data']['profname']) ? $user['data']['profname'] : '';
							$keydata['deptname'] = isset($user['data']['deptname']) ? $user['data']['deptname'] : '';
							$keydata['posnname'] = isset($user['data']['posnname']) ? $user['data']['posnname'] : '';
						}
					}
				}else{//补充原有搜索信息
					$keydata = CommonClass::json_decode($field_value);
					if(!isset($keydata['username']) && isset($prm_base->username) && !empty($prm_base->username) && !isset($keydata['deptname']) && isset($prm_base->deptname) && !empty($prm_base->deptname) && !isset($keydata['posnname']) && isset($prm_base->posnname) && !empty($prm_base->posnname)){
						$keydata['username'] = $prm_base->username;
						$keydata['deptname'] = $prm_base->deptname;
						$keydata['posnname'] = $prm_base->posnname;
					}else if(!empty($field_org_value['applyuserid'])){
						$user = ApiClass::user_get_info('3.0.0.0', $prm_base, $field_org_value['applyuserid'], 'profname,deptname,posnname');
						if($user['ret'] == RetClass::SUCCESS){
							$keydata['username'] = isset($user['data']['profname']) ? $user['data']['profname'] : '';
							$keydata['deptname'] = isset($user['data']['deptname']) ? $user['data']['deptname'] : '';
							$keydata['posnname'] = isset($user['data']['posnname']) ? $user['data']['posnname'] : '';
						}
					}
				}
				//General.20150727 申请人信息
				if(isset($field_org_value['applyuserid']) && !empty($field_org_value['applyuserid'])){
					$keydata['applyusername'] = '';
					$keydata['applydeptname'] = '';
					$keydata['applyposnname'] = '';
					if($field_org_value['applyuserid'] == $prm_base->userid){//如果申请人是当前用户
						$keydata['applyusername'] = $prm_base->username;
						if(isset($prm_base->roles) && !empty($prm_base->roles)){
							$roles = CommonClass::json_decode($prm_base->roles);
							foreach($roles as $k => $v){
								if(isset($field_org_value['applydeptid']) && $v['deptid'] == $field_org_value['applydeptid']){
									$keydata['applydeptname'] = $v['deptname'];
								}
								if(isset($field_org_value['applyposnid']) && $v['deptid'] == $field_org_value['applyposnid']){
									$keydata['applyposnname'] = $v['posnname'];
								}
							}
						}
					}else{//代别人提交申请
						$applyuser = ApiClass::user_get_info('3.0.0.0', $prm_base, $field_org_value['applyuserid'], 'profname,deptname,posnname');
						if($applyuser['ret'] == RetClass::SUCCESS){
							$keydata['applyusername'] = isset($applyuser['data']['profname']) ? $applyuser['data']['profname'] : '';
							$keydata['applydeptname'] = isset($applyuser['data']['deptname']) ? $applyuser['data']['deptname'] : '';
							$keydata['applyposnname'] = isset($applyuser['data']['posnname']) ? $applyuser['data']['posnname'] : '';
						}
					}
				}
				//var_dump($keydata);
				$field_value = CommonClass::json_encode($keydata);
				break;
		}
		return $field_value;
	}

	//设置更新默认值
	private static function _set_field_update_defval($prm_base, $field_org, $field_org_value = array(), $field_value = null) {
		$ret['ret'] = RetClass::SUCCESS;
		$ret_fld = '';
		switch ($field_org['fldname']) {
			case 'uptime'://更新时间
				/*
				if(is_null($field_value) || $field_value == ''){
					$field_value = CommonClass::get_datetime();	//插入时默认写入更新时间
				}*/
				//默认值这里，有更新操作的直接修改uptime，无需判断是否传值进来，有涉及要业务的用其它字段代替 suson.20170118
				$field_value = CommonClass::get_datetime();
				break;
			case 'upuserid'://更新人
				$field_value = isset($prm_base->userid) ? $prm_base->userid : 0; //用户ID
				break;
			case 'updata':	//更新人信息
				$updata = '';
				if(isset($prm_base->username)){
					$updata['username'] = $prm_base->username;
				}
				if(isset($prm_base->deptid)){
					$updata['deptid'] = $prm_base->deptid;
				}
				if(isset($prm_base->posnid)){
					$updata['posnid'] = $prm_base->posnid;
				}
				$field_value = CommonClass::json_encode($updata);
				break;
			//case 'applytime':
				//$field_value = CommonClass::get_datetime();	//修改申请时间
				break;
			case 'keydata':	//搜索字段
				//General.20151222 字段默认值处理
				$field_value = self::db_defval_handle($prm_base, $field_org_value, $field_value, 'keydata',$field_value);
				break;
			default:
				$ret['ret'] = RetClass::ERROR;
				break;
		}
		$ret['data'] = $field_value;
		return $ret;
	}

	//获取对应表的字段
	public static function get_table_fields($prm_base, $tbname, $getprimarykey = false){
		$opt['table'] = 'table_field';
		//$opt['fields']['tbname'] = $tbname;
		$opt['where'] = '(tbname = ? OR tableid = ?)';
		$opt['param'] = array($tbname,0);
		// if ($getprimarykey === true) {
		// 	$opt['where'] = ' AND pktype = ? ';
		// 	$opt['param'][] = EnumClass::SQL_FLD_PK_NORMAL;
		// }

		return DbClass::get_all($prm_base,$opt);
	}


	// // 在开发环境下输出
	// if (Doo::conf()->APP_MODE == 'dev'){
	// 	$diff = array_diff(array_keys($fields),$model->_fields);
	// 	if (!empty($diff)){
	// 		echo 'Field not used found in '.$model->_table.": ".implode(',',$diff);
	// 		if (function_exists('xdebug_get_function_stack')){
	// 			print_r(xdebug_get_function_stack());
	// 		}
	// 	}
	// }

	/*
	 * 各表的数据处理
	 * General.20140711
	 */
	public static function db_handle_data($prm_base = 0, $tbname = '', $data = '') {
		if (empty($tbname) || empty($data)) {
			return $data;
		}
		switch (strtolower($tbname)) {
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
				$data = WorkflowClass::handle_workflow_data($prm_base, $tbname, $data);
				break;
		}
		return $data;
	}

	/**
	 * [get_real_table_fields 获取表的实际字段]
	 * @param  [type] $prm_base [description]
	 * @param  [type] $tbname   [description]
	 * @return [type]           [description]
	 */
	public static function get_real_table_fields($prm_base,$tbname){
		$has_tb_fields_sql = 'desc '.$tbname;
		$has_tb_fields = Doo::db()->fetchAll($has_tb_fields_sql);
		$fields = array();
		foreach($has_tb_fields as $v){
			if(!empty($v['Field'])){
				$fields[] = $v['Field'];
			}
		}
		return $fields;
	}

	/**
	 * 获取指定库的所有实际表
	 * @param  string $dbname [description]
	 * @return [type]         [description]
	 */
	public static function get_db_table($dbname = '', $tbname = '') {
		if (Doo::conf()->DB_TYPE == 'mysql') {
			$table = Doo::db()->fetchRow("SELECT TABLE_NAME,TABLE_COMMENT FROM `information_schema`.`TABLES` where TABLE_SCHEMA='$dbname' AND table_name='$tbname' LIMIT 1;");
		}elseif (Doo::conf()->DB_TYPE == 'pgsql') {
			$table = Doo::db()->fetchRow("SELECT table_name FROM INFORMATION_SCHEMA.TABLES where table_catalog='$dbname' AND table_name='$tbname' LIMIT 1;");
		}
		return $table;
	}


	/**
	 * 获取指定库的所有实际表
	 * @param  string $dbname [description]
	 * @return [type]         [description]
	 */
	public static function get_db_tables($dbname = '', $count = 10000) {
		if (Doo::conf()->DB_TYPE == 'mysql') {
			$tables = Doo::db()->fetchAll("SELECT TABLE_NAME,TABLE_COMMENT FROM `information_schema`.`TABLES` where TABLE_SCHEMA='$dbname' LIMIT $count;");
		}elseif (Doo::conf()->DB_TYPE == 'pgsql') {
			$tables = array();
		}
		return $tables;
	}

	/**
	 * 获取指定库、指定表的所有实际字段
	 * @param  [type] $dbname [description]
	 * @param  [type] $tbname [description]
	 * @return [type]         [description]
	 */
	public static function get_db_table_fields($dbname, $tbname) {
		if (Doo::conf()->DB_TYPE == 'mysql') {
			$fields = Doo::db()->fetchAll("SELECT `COLUMN_NAME` as `Field`,`COLUMN_DEFAULT`,`DATA_TYPE`,`IS_NULLABLE`,`COLUMN_TYPE`,`COLUMN_KEY` as `Key`,`CHARACTER_MAXIMUM_LENGTH`,`COLUMN_COMMENT` as `Comment`,CHARACTER_SET_NAME,COLLATION_NAME,COLUMN_COMMENT,ORDINAL_POSITION FROM information_schema.`columns` where TABLE_SCHEMA='$dbname' AND table_name='$tbname' order by ORDINAL_POSITION;");
		}elseif (Doo::conf()->DB_TYPE == 'pgsql') {
			$fields = array();
		}
		return $fields;
	}


	/**
	 * 生成查询条件的方法
	 * dxf 兼容pgsql 2016-12-29 16:39:38
	 * @param  [type] $key  字段名
	 * @param  [type] $cond 包含条件、value等
	 * @return [type]       标准ret格式
	 */
	public static function gen_condition($key, $cond){
		if (empty($key)) {
			$ret['ret'] = RetClass::ERROR;
			return $ret;
		}
		$ret['ret'] = RetClass::SUCCESS;
		$opt['param'] = array();
		if (!is_array($cond)) {
			$opt['where'] = ' AND ' . $key .'= ? ';
			$opt['param'][] = $cond;
		}else{
			$join = isset($cond['join']) && ($cond['join'] == 'OR' || $cond['join'] == 'AND') ? $cond['join'] : 'AND';
			$value = isset($cond['value']) ? $cond['value'] : '';
			$symbol = isset($cond['symbol']) ? strtoupper($cond['symbol']) : '=';
			$lbracket = isset($cond['lbracket']) && $cond['lbracket'] == true ? ' ( ' : '';
			$rbracket = isset($cond['rbracket']) && $cond['rbracket'] == true  ? ' ) ' : '';

			switch ($symbol) {
				case 'IN':
				case 'FIND_IN_SET':
					$opt['where'] = " $join $lbracket $key IN ($value) $rbracket";
					break;
				case 'NOT IN':
				case 'NOT FIND_IN_SET':
					$opt['where'] = " $join $lbracket $key NOT IN ($value) $rbracket";
					break;
				case 'LIKE':
					$opt['where'] = " $join $lbracket $key $symbol ? $rbracket";
					if(!strstr($value,"%")){
						$value = '%'. $value .'%';
					}
					$opt['param'][] = $value;
					break;
				default:
					$opt['where'] = " $join $lbracket $key $symbol ? $rbracket";
					$opt['param'][] = $value;
					break;
			}
		}
		$ret['data'] = $opt;
		return $ret;
	}
}
?>