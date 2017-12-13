<?php
/**
 * DooRedisCache class file.
 *
 * @author Leng Sheng Hong <darkredz@gmail.com>
 * @link http://www.doophp.com/
 * @copyright Copyright &copy; 2009 Leng Sheng Hong
 * @license http://www.doophp.com/license
 */


/**
 * DooRedisCache provides caching methods utilizing the Memcache extension.
 *
 * If you have multiple servers for memcache, you would have to set it up in common.conf.php
 * <code>
 * // host, port, persistent, weight
 * $config['REDIS'] = array('192.168.1.31', '6379', 300);
 * </code>
 *
 * @author Leng Sheng Hong <darkredz@gmail.com>
 * @version $Id: Do`.php 1000 2009-08-22 19:36:10
 * @package doo.cache
 * @since 1.1
 */

class DooRedisCache{
    /**
     * Memcached connection
     * @var Memcache
     */
    protected $_redis;

    /**
     * Configurations of the connections
     * @var array
     */
    protected $_config;

    public function  __construct($conf=Null) {
        $this->_redis = new Redis();
        $this->_config = $conf;

        // host, port, persistent, weight
        if($conf!==Null){
        	$this->_redis->connect($conf[0], $conf[1], $conf[2]);
        }
        else{
            $this->_redis->connect('localhost', 6379);
        }
        $this->_redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_PHP);
    }

    /**
     * Deletes all data cache
     * @return bool True if success
     */
    public function redis(){
        return $this->_redis;
    }

    /**
     * Adds a cache with an unique Id.
     *
     * @param string $id Cache Id
     * @param mixed $data Data to be stored
     * @param int $expire Seconds to expired
     * @param int $compressed To store the data in Zlib compressed format
     * @return bool True if success
     */
    public function set($id, $data, $expire=0, $compressed=false){
    	if ($expire === 0) {
    		return $this->_redis->set($id, $data);
    	}else{
    		return $this->_redis->setex($id, $expire, $data);
    	}

    }

    public function mset($arr_data){
        return $this->_redis->mset($arr_data);
    }

    public function mget($arr_data){
        return $this->_redis->mget($arr_data);
    }

    public function multi(){
        return $this->_redis->multi(Redis::PIPELINE);
    }

    /**
     * Retrieves a value from cache with an Id.
     *
     * @param string $id A unique key identifying the cache
     * @return mixed The value stored in cache. Return false if no cache found or already expired.
     */
    public function get($id){
        return $this->_redis->get($id);
    }

    public function keys($id){
        return $this->_redis->keys($id);
    }

    public function delete($id=''){
        return $this->_redis->delete($id);
    }

    /**
     * Deletes an APC data cache with an identifying Id
     *
     * @param string $id Id of the cache
     * @return bool True if success
     */
    public function flush($id=''){
    	if(empty($id)){
    		return $this->flushAll();
    	}
        return $this->_redis->delete($id);
    }

    /**
     * Deletes all data cache
     * @return bool True if success
     */
    public function flushAll(){
        return $this->_redis->flushAll();
    }

}

