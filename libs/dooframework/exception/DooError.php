<?php
// 环境常量
define('IS_CLI', PHP_SAPI == 'cli' ? true : false);
define('IS_WIN', strpos(PHP_OS, 'WIN') !== false);


class DooError
{
	public static $_exception_title = '';
	public static $_exception = '';

	public function __construct(){
		Doo::loadCore('exception/DooErrorHandle');
		Doo::loadCore('exception/DooException');
		Doo::loadCore('exception/DooErrorException');
		Doo::loadCore('exception/DooThrowableError');
	}
    /**
     * 注册异常处理
     * @return void
     */
    public static function register()
    {
        error_reporting(E_ALL);
        set_error_handler([__CLASS__, 'appError']);
        set_exception_handler([__CLASS__, 'appException']);
        register_shutdown_function([__CLASS__, 'appShutdown']);
    }

    /**
     * Exception Handler
     * @param  \Exception|\Throwable $e
     */
    public static function appException($e)
    {
    	// var_dump($e);exit();
    	self::$_exception_title = 'appException';
        if (!$e instanceof Exception) {
            $e = new DooThrowableError($e);
        }

        self::getExceptionHandler()->report($e);
        if (IS_CLI) {
            // self::getExceptionHandler()->renderForConsole(new ConsoleOutput, $e);
            self::$_exception = self::getExceptionHandler()->renderForConsole(new ConsoleOutput, $e);
        } else {
        	// echo "appException</br>";
            // var_dump(self::getExceptionHandler()->render($e));
            self::$_exception = self::getExceptionHandler()->render($e);
            // var_dump(self::$_exception);
        }

        // 写入日志
        // Log::save();
        self::$_exception_title = microtime(true) . rand(1000,9999);
        self::$_exception['iv_prm_base'] = Doo::conf()->PRM_BASE;
		$errorMessage = '运行时间:'. Doo::benchmark() .' '. self::$_exception_title .':'. var_export(self::$_exception, TRUE);

			// printf("%s <font color='#ff0000'><b>%s</b></font>。<br />%s<br />\n",
			// CommonClass::get_datetime(),'EMERGENCY', $errorMessage);
		echo '运行错误！(code:'. self::$_exception_title .')';

		Doo::crashlogger()->err("{$errorMessage}");
		Doo::crashlogger()->writeLogs(date('Ymd') .'_crash.log',false);
    }

    /**
     * Error Handler
     * @param  integer $errno   错误编号
     * @param  integer $errstr  详细错误信息
     * @param  string  $errfile 出错的文件
     * @param  integer $errline 出错行号
     * @param array    $errcontext
     * @throws ErrorException
     */
    public static function appError($errno, $errstr, $errfile = '', $errline = 0, $errcontext = [])
    {
    	// var_dump([$errno, $errstr, $errfile, $errline, $errcontext]);
    	self::$_exception_title = 'appError';

        $exception = new DooErrorException($errno, $errstr, $errfile, $errline, $errcontext);
        // exit();
        if (error_reporting() & $errno) {
            // 将错误信息托管至 DooErrorException
            // var_dump('appError');
            throw $exception;
        } else {
        	self::appException($exception);
			// var_dump('appError');
			// self::getExceptionHandler()->report($exception);
        }
    }

    /**
     * Shutdown Handler
     */
    public static function appShutdown()
    {
    	// var_dump($dmsg = debug_backtrace());
    	// var_dump('appShutdown');
		// var_dump(self::$_exception_title);
        if (!is_null($error = error_get_last()) && self::isFatal($error['type'])) {
            // 将错误信息托管至think\ErrorException
            $exception = new DooErrorException($error['type'], $error['message'], $error['file'], $error['line']);

            self::appException($exception);
        }
    }

    /**
     * 确定错误类型是否致命
     *
     * @param  int $type
     * @return bool
     */
    protected static function isFatal($type)
    {
        return in_array($type, [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE]);
    }

    /**
     * Get an instance of the exception handler.
     *
     * @return Handle
     */
    public static function getExceptionHandler()
    {
        static $handle;
        if (!$handle) {
            // 异常处理handle
            // $class = Config::get('exception_handle');
            $class = 'DooErrorHandle';
            if ($class && class_exists($class)) {
                $handle = new $class;
            } else {
                $handle = new Handle;
            }
        }
        return $handle;
    }
}
