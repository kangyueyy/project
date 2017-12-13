<?php
class AES {
    private $hex_iv = '00000000000000000000000000000000'; # converted JAVA byte code in to HEX and placed it here           
    private $key = '01020304050607080910203040506070'; #Same as in JAVA
    function __construct() {
        //$this->key = hash('sha256', $this->key, true);
        //echo $this->key.'<br/>';
    }
    function encode($str) {   
        $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
        mcrypt_generic_init($td, $this->key, $this->hexToStr($this->hex_iv));
        $block = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
        $pad = $block - (strlen($str) % $block);
        $str .= str_repeat(chr($pad), $pad);
        $encrypted = mcrypt_generic($td, $str);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);    
        return base64_encode($encrypted);
    }
    function decode($code) {    
        $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
        mcrypt_generic_init($td, $this->key, $this->hexToStr($this->hex_iv));
        $str = @mdecrypt_generic($td, base64_decode($code));//暂时这样 加@屏蔽警告，避免出现不能解密的情况报错 2016.3.14 LL 
        $block = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);    
        return $this->strippadding($str);           
    }
    /*
      For PKCS7 padding
     */
    private function addpadding($string, $blocksize = 16) {
        $len = strlen($string);
        $pad = $blocksize - ($len % $blocksize);
        $string .= str_repeat(chr($pad), $pad);
        return $string;
    }
    private function strippadding($string) {
        $slast = ord(substr($string, -1));
        $slastc = chr($slast);
        $pcheck = substr($string, -$slast);
        if (@preg_match("/$slastc{" . $slast . "}/", $string)) {
            $string = substr($string, 0, strlen($string) - $slast);
            return $string;
        } else {
            return false;
        }
    }
	function hexToStr($hex)
	{
		$string='';
		for ($i=0; $i < strlen($hex)-1; $i+=2)
		{
			$string .= chr(hexdec($hex[$i].$hex[$i+1]));
		}
		return $string;
	}
}
//$encryption = new MCrypt();
//echo $encryption->encrypt('123456') . "<br/>";
//echo $encryption->decrypt('yDJliPNrDeqUPlpU6CtmNpEit/6ILbSV6Ruoqgrq4WcQ0XCg5gkA57+F0KxGAbmE');
 
?>