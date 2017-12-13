<?php
/**
 * 基础参数定义
 */

class PrmBaseClass {
	public $entid			= 0;	//企业ID
	public $entno			= 0;	//企业号
	public $userid			= 0;	//当前用户ID
	public $oano			= 0;	//OA号
	public $profid			= 0;	//当前用户档案ID
	public $deptid			= 0;	//当前用户主部门ID
	public $deptname		= '';	//当前用户主部门名
	public $posnid			= 0;	//当前用户主职位ID
	public $posnname		= '';	//当前用户主职位名
	public $roles			= '';	//当前用户所有角色，包括主副职位
	public $username		= '';   //当前用户名
	public $entname			= '';   //当前企业名
	public $url				= '';   //基地址
	public $oacnurl			= '';   //oa.cn基地址
	public $fileurl			= '';   //文件基地址
	public $ver				= '';   //版本
	public $channel			= '';   //渠道
	public $terminal		= 0;    //终端类型
	public $puserid			= 0;    //当前用户的平台账号ID
	public $sobpackageid	= 0;	//当前企业套餐ID
    public $args            = array();   //扩展对象数据 add by james.ou 2017-11-22

    public function __construct($entid = 0, $entno = 0, $userid = 0, $oano = 0, $profid = 0, $deptid = 0, $posnid = 0, $roles = '', $username = '', $entname = '', $url = '', $oacnurl = '', $fileurl = '', $ver = '', $channel = '', $terminal = 0, $puserid = 0, $sobpackageid = 0) {
		$this->entid			= $entid;
		$this->entno			= $entno;
		$this->userid			= $userid;
		$this->oano				= $oano;
		$this->profid			= $profid;
		$this->deptid			= $deptid;
		$this->posnid			= $posnid;
		$this->terminal			= $terminal;
		$this->username			= (string) $username;
		$this->entname			= (string) $entname;
		$this->url				= (string) $url;
		$this->oacnurl			= (string) $oacnurl;
		$this->fileurl			= (string) $fileurl;
		$this->ver				= (string) $ver;
		$this->channel			= (string) $channel;
		$this->puserid			= $puserid;
		$this->sobpackageid		= $sobpackageid;
        $this->args             = array();//初始化扩展对象 by james.ou 2017-11-22
        
		Doo::conf()->PRM_BASE	= $this;
	}


    /**
	 * @param string $oacnurl
	 */
	public function setOacnurl($oacnurl)
	{
		$this->oacnurl = (string)$oacnurl;
		Doo::conf()->PRM_BASE = $this;
	}

	/**
	 * @param int $channel
	 */
	public function setChannel($channel)
	{
		$this->channel = $channel;
		Doo::conf()->PRM_BASE = $this;
	}

	/**
	 * @param int $ver
	 */
	public function setVersion($ver)
	{
		$this->ver = $ver;
		Doo::conf()->PRM_BASE = $this;
	}

	/**
	 * @param int $terminal
	 */
	public function setTerminal($terminal)
	{
		$this->terminal = $terminal;
		Doo::conf()->PRM_BASE = $this;
	}

	/**
	 * @param int $entid
	 */
	public function setEntid($entid)
	{
		$this->entid = $entid;
		Doo::conf()->PRM_BASE = $this;
	}

	/**
	 * @param int $entno
	 */
	public function setEntno($entno)
	{
		$this->entno = $entno;
		Doo::conf()->PRM_BASE = $this;
	}

	/**
	 * @param int $userid
	 */
	public function setUserid($userid)
	{
		$this->userid = $userid;
		Doo::conf()->PRM_BASE = $this;
	}

	/**
	 * @param int $oano
	 */
	public function setOAno($oano)
	{
		$this->oano = $oano;
		Doo::conf()->PRM_BASE = $this;
	}

	/**
	 * @param int $profid
	 */
	public function setProfid($profid)
	{
		$this->profid = $profid;
		Doo::conf()->PRM_BASE = $this;
	}

	/**
	 * @param int $deptid
	 */
	public function setUserDeptid($deptid)
	{
		$this->deptid = $deptid;
		Doo::conf()->PRM_BASE = $this;
	}

	/**
	 * @param int $deptname
	 */
	public function setUserDeptname($deptname)
	{
		$this->deptname = $deptname;
		Doo::conf()->PRM_BASE = $this;
	}

	/**
	 * @param int $posnid
	 */
	public function setUserPosnid($posnid)
	{
		$this->posnid = $posnid;
		Doo::conf()->PRM_BASE = $this;
	}

	/**
	 * @param int $posnname
	 */
	public function setUserPosnname($posnname)
	{
		$this->posnname = $posnname;
		Doo::conf()->PRM_BASE = $this;
	}

	/**
	 * @param string $roles
	 */
	public function setUserRoles($roles)
	{
		$this->roles = $roles;
		Doo::conf()->PRM_BASE = $this;
	}
    /**
	 * @param string $username
	 */
	public function setUsername($username)
	{
		$this->username = (string)$username;
		Doo::conf()->PRM_BASE = $this;
	}

	/**
	 * @param string $entname
	 */
	public function setEntname($entname)
	{
		$this->entname = (string)$entname;
		Doo::conf()->PRM_BASE = $this;
	}

	/**
	 * @param string $url
	 */
	public function setUrl($url)
	{
		$this->url = (string)$url;
		Doo::conf()->PRM_BASE = $this;
	}

	/**
	 * @param string $fileurl
	 */
	public function setFileUrl($fileurl)
	{
		$this->fileurl = (string)$fileurl;
		Doo::conf()->PRM_BASE = $this;
	}

	/**
	 * @param int $agentid
	 */
	public function setAgentid($agentid)
	{
		$this->agentid = (int)$agentid;
		Doo::conf()->PRM_BASE = $this;
	}

	/**
	 * @param int $puserid
	 */
	public function setPuserid($puserid)
	{
		$this->puserid = $puserid;
		Doo::conf()->PRM_BASE = $this;
	}

	/**
	 * @param int $sobpackageid
	 */
	public function setSobpackageid($sobpackageid)
	{
		$this->sobpackageid = $sobpackageid;
		Doo::conf()->PRM_BASE = $this;
	}
}

?>