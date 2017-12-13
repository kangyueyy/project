<?php
/**
 * iv错误异常
 * 主要用于封装 set_error_handler 和 register_shutdown_function 得到的错误
 * 除开从 think\Exception 继承的功能
 * 其他和PHP系统\ErrorException功能基本一样
 */
class DooErrorException extends Exception
{
    /**
     * 用于保存错误级别
     * @var integer
     */
    protected $severity;

   /**
     * 保存异常页面显示的额外Debug数据
     * @var array
     */
    protected $data = [];

    /**
     * 错误异常构造函数
     * @param integer $severity 错误级别
     * @param string  $message  错误详细信息
     * @param string  $file     出错文件路径
     * @param integer $line     出错行号
     * @param array   $context  错误上下文，会包含错误触发处作用域内所有变量的数组
     */
    public function __construct($severity, $message, $file, $line, array $context = [])
    {
        $this->severity = $severity;
        $this->message  = $message;
        $this->file     = $file;
        $this->line     = $line;
        $this->code     = 0;

        empty($context) || $this->setData('Error Context', $context);
    }

    /**
     * 设置异常额外的Debug数据
     * 数据将会显示为下面的格式
     *
     * Exception Data
     * --------------------------------------------------
     * Label 1
     *   key1      value1
     *   key2      value2
     * Label 2
     *   key1      value1
     *   key2      value2
     *
     * @param string $label 数据分类，用于异常页面显示
     * @param array  $data  需要显示的数据，必须为关联数组
     */
    final protected function setData($label, array $data)
    {
        $this->data[$label] = $data;
    }

    /**
     * 获取异常额外Debug数据
     * 主要用于输出到异常页面便于调试
     * @return array 由setData设置的Debug数据
     */
    final public function getData()
    {
        return $this->data;
    }



    /**
     * 获取错误级别
     * @return integer 错误级别
     */
    final public function getSeverity()
    {
        return $this->severity;
    }
}
