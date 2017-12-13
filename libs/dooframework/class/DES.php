<?php
	/**
	 *@author Boss.20160504
	 *实现3DES 加解密
	 */
	class DES {

		private $key = 'contactsworkcontactswork';//密钥

		//构造函数
		function __construct() {
        //$this->key = hash('sha256', $this->key, true);
        //echo $this->key.'<br/>';
    	}
		
		/**
		 *@author Boss.20160504
		 *进行3DES加密
		 *@param $input 需要加密的字符串
		 *@return 返回经过base64_encode的密文
		 */
	     function encode($input)
	    {
	    	$input = iconv('UTF-8', 'GBK', $input);
	        $size = mcrypt_get_block_size(MCRYPT_3DES,'ecb');
	        $input = $this->pkcs5_pad($input, $size);//填充
	        $td = mcrypt_module_open(MCRYPT_3DES, '', 'ecb', '');
	        //$iv = @mcrypt_create_iv(mcrypt_enc_get_iv_size($td),MCRYPT_RAND);
	        $iv = '00000000';//ECB 模式 忽略初始向量
	        @mcrypt_generic_init($td, $this->key, $iv);
	        $data = mcrypt_generic($td, $input);
	        mcrypt_generic_deinit($td);
	        mcrypt_module_close($td);
	        $data = base64_encode($data);
	        return $data;
	    }


	     /**
		 *@author Boss.20160504
		 *进行3DES加密
		 *@param $encrypted 需要解密的字符串
		 *@return 返回经过base64_decode的明文
		 */
	     function decode($encrypted)
	    {
	        $encrypted = base64_decode($encrypted);
	        $td = mcrypt_module_open(MCRYPT_3DES,'','ecb','');
	        //$iv = @mcrypt_create_iv(mcrypt_enc_get_iv_size($td),MCRYPT_RAND);
	        $ks = mcrypt_enc_get_key_size($td);
	        $iv = '00000000';//ECB 模式 忽略初始向量
	        @mcrypt_generic_init($td, $this->key, $iv);
	        $decrypted = mdecrypt_generic($td, $encrypted);
	        mcrypt_generic_deinit($td);
	        mcrypt_module_close($td);
	        $y = $this->pkcs5_unpad($decrypted);
	        $y = iconv('GBK', 'UTF-8', $y);
	        return $y;
	    }
	     

	     //PKCS5Padding 填充
	    private function pkcs5_pad ($text, $blocksize) 
	    {
	        $pad = $blocksize - (strlen($text) % $blocksize);
	        return $text . str_repeat(chr($pad), $pad);
	    }
	     


	    //PKCS5Padding 反填充
	    private function pkcs5_unpad($text)
	    {
	        $pad = ord($text{strlen($text)-1});
	        if ($pad > strlen($text)) 
	        {
	        return false;
	        }
	        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad)
	        {
	            return false;
	        }
	        return substr($text, 0, -1 * $pad);
	    }
	}

?>