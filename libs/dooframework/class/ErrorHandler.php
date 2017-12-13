<?php
/**
 * 定义Error_Handler 函数
 * 
 * @param $error_level 错误级别
 * @param $error_message 错误信息
 * @param $file 错误所在文件
 * @param $line 错误所在行数
 * @author james.ou
 */
function error_handler ($error_level, $error_message, $file, $line) {

	$EXIT = FALSE;
	switch ($error_level) {
		//提醒级别
		case E_NOTICE:
		case E_USER_NOTICE:
			$error_type = 'Notice';
			//Doo::logger()->notice("<font color='#ff0000'><b>{$error_type}</b></font>: {$error_message} in <b>{$file}</b> on line <b>{$line}</b><br /><br />\n");
			LogClass::log_notice($error_message,$file,$line);
			break;
		//警告级别
		case E_WARNING:
		case E_USER_WARNING:
			$error_type = 'Warning';
			//Doo::logger()->warn("<font color='#ff0000'><b>{$error_type}</b></font>: {$error_message} in <b>{$file}</b> on line <b>{$line}</b><br /><br />\n");
			LogClass::log_warn($error_message,$file,$line);
			break;
		//错误级别
		case E_ERROR:
		case E_USER_ERROR:
			$error_type = 'Fatal Error';
			$EXIT = TRUE;
			//Doo::logger()->err("<font color='#ff0000'><b>{$error_type}</b></font>: {$error_message} in <b>{$file}</b> on line <b>{$line}</b><br /><br />\n");
			LogClass::log_error($error_message,$file,$line);
			break;
		//其他未知错误
		default:
			$error_type = 'Unknown';
			$EXIT = TRUE;
			//Doo::logger()->info("<font color='#ff0000'><b>{$error_type}</b></font>: {$error_message} in <b>{$file}</b> on line <b>{$line}</b><br /><br />\n");
			LogClass::log_crit($error_message,$file,$line);
			break;
	}
	//直接打印错误信息， 也可以写文件， 写数据库， 反正错误信息都在这， 任你发落
	//printf ("<font color='#ff0000'><b>%s</b></font>: %s in <b>%s</b> on line <b>%d</b><br /><br />\n", $error_type, $error_message, $file, $line);
	
	//Doo::logger()->writeLogs(date('Ym') .'_error_log.xml');
	//错误影响到程序的正常执行的话跳转到友好的错误提示页面
	if (TRUE == $EXIT) {
		//友好错误提示
	   
		//echo "<script language='Javascript'>location='err.html'; </script>";
	}
  
}
?>