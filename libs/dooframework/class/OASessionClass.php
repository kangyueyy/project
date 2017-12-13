<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of OASession
 *
 * @author delll
 */
class OASessionClass {

    private $catch = null;

    protected $_namespace = null;

    protected $_session = array();

    private static $sid = '';

    public function __construct($namespace = 'Default') {
        Doo::conf()->APP_MODE;
        Doo::loadClass('CommonClass');
        $this->_namespace = $namespace;
        $this->catch = CommonClass::cache(Doo::conf()->CACHE_TYPE_SYS);
    }

    /**
     * 设置session
     * @param type $name
     * @param type $value
     */
    function __set($name, $value) {
        $id = self::getId();
        if (empty ($id)) {
            $id = $this->setId();
        } else {
            $this->_session = $this->catch->get($id);
        }
        if (!$this->_session)
            $this->_session = array();
        $this->_session[$this->_namespace][$name] = $value;
        $this->catch->set($id, $this->_session);
    }

    /**
     * 获取session
     * @param type $name
     * @return string
     */
    public function &get($name) {
        $id = self::getId();
        $res = null;
        if (empty ($id))
            return $res;
        $this->_session = $this->catch->get($id);
        if (!$this->_session || !isset($this->_session[$this->_namespace][$name])) {
            return $res;
        } else {
            return $this->_session[$this->_namespace][$name];
        }
    }

    /**
     * 获取session
     * @param type $name
     * @return type
     */
    function &__get($name) {
        return $this->get($name);
    }

    /**
     *
     * @param type $name
     * @return type
     */
    public function namespaceUnset($name = null) {
        $id = self::getId();
        if (empty ($id))
            return true;
        $this->_session = $this->catch->get($id);
        if (!isset($this->_session))
            $this->_session = array();
        if (empty($name)) {
            unset($this->_session[$this->_namespace]);
        } else {
            unset($this->_session[$this->_namespace][$name]);
        }
        $this->catch->set($id, $this->_session);
        return true;
    }

    /**
     *
     * @param type $name
     * @return type
     */
    public function __unset($name) {
        $id = self::getId();
        if (empty ($id))
            return true;
        $this->_session = $this->catch->get($id);
        if (!$this->_session || !isset($this->_session[$this->_namespace][$name])) {
            return true;
        }
        if (isset($this->_session[$this->_namespace][$name])) {
            unset($this->_session[$this->_namespace][$name]);
            return true;
        }
        return false;
    }

    /**
     * 检测属性是否存在
     * @param type $name
     * @return type
     */
    public function __isset($name) {
        $id = self::getId();
        if (empty ($id))
            return false;
        $this->_session = $this->catch->get($id);
        if (!$this->_session || !isset($this->_session[$this->_namespace][$name])) {
            return false;
        }
        if (isset($this->_session[$this->_namespace][$name])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 获取session ID
     * @return type
     */
    public static function getId(){
        $id = isset($_COOKIE['oasid']) ? $_COOKIE['oasid'] : '';
        if(empty ($id)){
            $id = self::$sid;
        }
        return $id;
    }

    /**
     * 设置session ID
     * @return type
     */
    public function setId(){
        $guid = Common::guid();
        $id = md5($guid);
        self::$sid = $id;
        $cookietime = time() + 60 * 60 * 24 * 30;

        header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
        setcookie('oasid', $id, $cookietime, '/');
        return $id;
    }
}

?>
