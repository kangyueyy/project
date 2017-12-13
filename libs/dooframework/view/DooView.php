<?php
/**
 * DooView class file.
 *
 * @author Leng Sheng Hong <darkredz@gmail.com>
 * @link http://www.doophp.com/
 * @copyright Copyright &copy; 2009 Leng Sheng Hong
 * @license http://www.doophp.com/license
 */


/**
 * DooView is a class for working with the "view" portion of the model-view-controller pattern.
 *
 * <p>That is, it exists to help keep the view script separate from the model and controller scripts.
 * It provides a system of helpers, output filters, and variable escaping which is known as Template tags where you can defined them in <i>SITE_PATH/protected/plugin/TemplateTag.php</i>.</p>
 *
 * <p>DooView is a compiling template engine. It parses the HTML templates and convert them into PHP scripts which will then be included and rendered.
 * In production mode, DooView will not processed the template but include the compiled PHP file instead. Otherwise,
 * it will compare the last modified time of both template and compiled file, then regenerate the compiled file if the template file is newer.
 * Compiled files are located at <i>SITE_PATH/protected/viewc</i> while HTML templates should be placed at <i>SITE_PATH/protected/view</i></p>
 *
 * <p>Loops, variables, function calls with parameters, include files are supported in DooView. DooView only allows a template to use functions defined in <i>TemplateTag.php</i>.
 * The first parameter needs to be the variable passed in from the template file and it should return a value to be printed out. Functions are case insensitive.
 * </p>
 * <code>
 * //register functions to be used with your template files
 * $template_tags = array('upper', 'toprice');
 *
 * function upper($str){
 *     return strtoupper($str);
 * }
 * function toprice($str, $currency='$'){
 *     return $currency . sprintf("%.2f", $str);
 * }
 *
 * //usage in template
 * Welcome, Mr. {{upper(username)}}
 * Your account has: {{TOPRICE(amount, 'RM')}}
 * </code>
 *
 * <p>Included files in template are automatically generated if it doesn't exist. To include other template files in your template, use:</p>
 * <code>
 * <!-- include "header" -->
 * //Or you can use a variable in the include tag too. say, $data['file']='header'
 * <!-- include "{{file}}" -->
 * </code>
 *
 * <p>Since 1.1, If statement supported. Function used in IF can be controlled.</p>
 * <code>
 * <!-- if -->
 * <!-- elseif -->
 * <!-- else -->
 * <!-- endif -->
 *
 * //Examples
 * <!-- if {{MyVar}}==100 -->
 *
 * //With function
 * <!-- if {{checkVar(MyVar)}} -->
 *
 * //&& || and other operators
 * <!-- if {{isGender(gender, 'female')}}==true && {{age}}>=14 -->
 * <!-- if {{isAdmin(user)}}==true && ({{age}}>=14 || {{age}}<54) -->
 *
 * //Write in one line
 * <!-- if {{isAdmin(username)}} --><h2>Success!</h2><!-- endif -->
 * </code>
 *
 * <p>Since 1.1, Partial caching in view:</p>
 * <code>
 * <!-- cache('mydata', 60) -->
 * <ul>{{userList}}</ul>
 * <!-- endcache -->
 * </code>
 * <p>DooView can be called from the controller: </p>
 * <code>
 * class SampleController extends DooController{
 *      public viewme(){
 *          $data['myname'] = 'My name is Doo';
 *          $this->view()->render('viewmepage', $data);
 *          //viewmepage instead of viewmepage.html
 *          //files in subfolder, use: render('foldername/viewmepage')
 *      }
 * }
 * </code>
 *
 * Since 1.3, you can have comments block in the template which will not be output/processed unless
 * SHOW_TEMPLATE_COMMENT is True in common.conf.php
 * <code>
 * <!-- comment -->
 *    this is a comment
 *    testing {{debug(myVar)}}
 *    <!-- include 'debugger' -->
 * <!-- endcomment -->
 * </code>
 *
 * <p>You can use <b>native PHP</b> as view templates in DooPHP. Use DooView::renderc() instead of render.</p>
 * <p>In your Controller:</p>
 * <code>
 * $data['phone'] = 012432456;
 * $this->view()->abc = 'ABCDE';
 *
 * //pass in true to enable access to controller if you need it.
 * $this->renderc('example', $data, true);
 * </code>
 *
 * <p>In your view scrips located in <b>SITE_PATH/protected/viewc/</b></p>
 * <code>
 * echo $this->data['phone'];     //012432456
 * echo $this->abc;             //ABC
 *
 * //call controller methods if enabled.
 * $this->thisIsFromController();
 * echo $this->thisIsControllerProperty;
 * </code>
 *
 * <p>To include a template script in viewc folder</p>
 * <code>
 * $this->inc('this_is_another_view_php');
 * </code>
 *
 * To write variable's value as static strings to the template, use a plus sign in front of the variable
 * <code>
 * //in controller
 * $this->data['siteurl'] = 'www.doophp.com';
 * $this->render('template', $this->data);
 *
 * //in template
 * <p>{{+siteurl}}</p>
 *
 * //The compiled template will look like:
 * <p>www.doophp.com</p>
 *
 * //Used with function call
 * {{+time(true)}}
 *
 * //compiled source
 * 1262115252
 * </code>
 *
 * Short tags with native PHP is allowed
 * <code>
 * <? echo $data; ?>
 * <?=$data;?>
 *
 * //The code above will be converted to
 * <?php echo $data; ?>
 * </code>
 *
 * To write variable's value as static string (using native PHP), use a plus sign
 * <code>
 * //Example:
 * <p><?+$data;?></p>
 *
 * //result:
 * <p>www.doophp.com</p>
 * </code>
 *
 * @author Leng Sheng Hong <darkredz@gmail.com>
 * @version $Id: DooView.php 1000 2009-07-7 18:27:22
 * @package doo.view
 * @since 1.0
 */
class DooView {
    public $controller;
    public $data;
    protected $tags;
    protected $mainRenderFolder;
    protected $tagClassName;
    protected $tagModuleName;
     /**
     * 模版路径
     * 允许自定义模版路径,如果common.conf里配有 ROOT_VIEW_PATH 模版根路径，则直接从这个根目录下去找模版
     * 否则，保持默认路径
     * by james.ou 2011--4-1
     * modify by dxf 2014-09-28 10:33 增加$rootViewPath_ORI，记录hmvc模式下，根目录下的rootViewPath路径
     * @var string
     */
    public $rootViewPath;
    public $rootViewPathNet;
    protected $rootViewPath_ORI = null;
    public $templateSkin = 'default/';
    public $folder = 'web/';
    public $isBaseTag = false;
    protected $isIndexBase = false;

    protected $components = array();
    protected $isMakeTemplate = false;
    /**
     * 版本，暂用
     */
    protected $version = 1;

    public  function __construct(){
        $this->setRootViewPath();
        // if(isset (Doo::conf()->ROOT_VIEW_PATH) && !empty(Doo::conf()->ROOT_VIEW_PATH)){
        //     $this->rootViewPath=Doo::conf()->ROOT_VIEW_PATH. Doo::conf()->PROTECTED_FOLDER;
        //     //$this->rootViewPath=Doo::conf()->ROOT_VIEW_PATH;
        //     if(isset(Doo::conf()->PROTECTED_FOLDER_ORI)===true){
        //         //$this->rootViewPath=Doo::conf()->ROOT_VIEW_PATH. Doo::conf()->PROTECTED_FOLDER_ORI;
        //         //$this->rootViewPath_ORI=Doo::conf()->ROOT_VIEW_PATH. Doo::conf()->PROTECTED_FOLDER_ORI;
        //         //$this->rootViewPath=Doo::conf()->ROOT_VIEW_PATH. Doo::conf()->PROTECTED_FOLDER;
        //         $this->rootViewPath_ORI=Doo::conf()->ROOT_VIEW_PATH. Doo::conf()->PROTECTED_FOLDER_ORI;
        //     }else{
        //         $this->rootViewPath_ORI=Doo::conf()->ROOT_VIEW_PATH. Doo::conf()->PROTECTED_FOLDER;
        //         //$this->rootViewPath_ORI=Doo::conf()->ROOT_VIEW_PATH;
        //     }
        // }else{
        //     $this->rootViewPath=Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER;
        // }
    }

    private function setRootViewPath(){
        if(isset (Doo::conf()->ROOT_VIEW_PATH) && !empty(Doo::conf()->ROOT_VIEW_PATH)){
            $this->rootViewPath=Doo::conf()->ROOT_VIEW_PATH. Doo::conf()->PROTECTED_FOLDER;
            //$this->rootViewPath=Doo::conf()->ROOT_VIEW_PATH;
            if(isset(Doo::conf()->PROTECTED_FOLDER_ORI)===true){
                if ($this->version === 1) {
                    $this->rootViewPath=Doo::conf()->ROOT_VIEW_PATH. Doo::conf()->PROTECTED_FOLDER_ORI;
                }
                //$this->rootViewPath=Doo::conf()->ROOT_VIEW_PATH. Doo::conf()->PROTECTED_FOLDER_ORI;
                //$this->rootViewPath_ORI=Doo::conf()->ROOT_VIEW_PATH. Doo::conf()->PROTECTED_FOLDER_ORI;
                //$this->rootViewPath=Doo::conf()->ROOT_VIEW_PATH. Doo::conf()->PROTECTED_FOLDER;
                $this->rootViewPath_ORI=Doo::conf()->ROOT_VIEW_PATH. Doo::conf()->PROTECTED_FOLDER_ORI;
            }else{
                $this->rootViewPath_ORI=Doo::conf()->ROOT_VIEW_PATH. Doo::conf()->PROTECTED_FOLDER;
                //$this->rootViewPath_ORI=Doo::conf()->ROOT_VIEW_PATH;
            }
        }else{
            $this->rootViewPath=Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER;
        }
    }

    public function getViewFilePathEnt($entid,$moduleid,$pageformid,$terminal,$filepath){
    	$fullPath = Doo::conf()->ROOT_FILE_PATH_ENT . substr($entid,-3) . PATH_SEP .
    			$entid . PATH_SEP . $moduleid . "/view/$filepath/{$pageformid}_$terminal.html";
    	//LogClass::log_emerg1('getViewFilePathEnt',$fullPath,__FILE__,__LINE__);
    	return $fullPath;
    }

    public function getViewcFilePathEnt($entid,$moduleid,$pageformid,$terminal,$filepath){
    	$fullPath = Doo::conf()->ROOT_FILE_PATH_ENT . substr($entid,-3) . PATH_SEP .
    			$entid . PATH_SEP . $moduleid . "/viewc/$filepath/{$pageformid}_$terminal.php";
    	//LogClass::log_emerg1('getViewFilePathEnt',$fullPath,__FILE__,__LINE__);
    	return $fullPath;
    }

    /**
     * Determine which class to use as template tag.
     *
     * If $module is equal to '/', the main app's template tag class will be used.
     *
     * @param string $class Template tag class name
     * @param string $module Folder name of the module. Define this module name if the tag class is from another module.
     */
    public function setTagClass($class, $module=Null){
        $this->tagClassName = $class;
        $this->tagModuleName = $module;
    }

    /**
     * Includes the native PHP template file to be output.
     *
     * @param string $file PHP template file name without extension .php
     * @param array $data Associative array of the data to be used in the template view.
     * @param object $controller The controller object, pass this in so that in views you can access the controller.
     * @param bool $includeTagClass If true, DooView will determine which Template tag class to include. Else, no files will be loaded
     */
    public function renderc($file, $data=NULL, $controller=NULL, $includeTagClass=TRUE){
        $this->data = $data;
        $this->controller = $controller;
        if($includeTagClass===TRUE)
            $this->loadTagClass();

       //by james.ou 2011-4-1
       // include Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER . "/viewc/$file.php";
        include $this->rootViewPath. "/viewc/$file.php";
    }

    /**
     * Include template view files
     * @param string $file File name without extension (.php)
     */
    public function inc($file){
        //by james.ou 2011-4-1
        //include Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER . "viewc/$file.php";
        include $this->rootViewPath. "viewc/$file.php";
    }

    public function  __call($name,  $arguments) {
        if($this->controller!=NULL){
            return call_user_func_array(array(&$this->controller, $name), $arguments);
        }
    }

    public function  __get($name) {
        if($this->controller!=NULL){
            return $this->controller->{$name};
        }
    }

    //dxf 2015-01-10 16:45:58 判断是否需要编译
    private function isCompile($cfile, $vfile, $process=null, $forceCompile=false){
        if(isset(Doo::conf()->TEMPLATE_COMPILE_ALWAYS) && Doo::conf()->TEMPLATE_COMPILE_ALWAYS==true){
            $process = $forceCompile = true;
        }
        //if process not set, then check the app mode, if production mode, skip the process(false) and just include the compiled files
        else if($process===NULL){
            $process = (Doo::conf()->APP_MODE!='prod');
        }

        if($process!=true){
            //不需要强制编译
            return false;
        }
        else{
            //if file exist and is not older than the html template file, include the compiled php instead and exit the function
            if(!$forceCompile){
                if(file_exists($cfile)){
                    if(filemtime($cfile)>=filemtime($vfile)){
                        return false;
                    }
                }
            }
        }
        return true;
    }
    /**
     * Writes the generated output produced by render() to file.
     * @param string $path Path to save the generated output.
     * @param string $templatefile Template file name (without extension name)
     * @param array $data Associative array of the data to be used in the Template file. eg. <b>$data['username']</b>, you should use <b>{{username}}</b> in the template.
     * @return string|false The file name of the rendered output saved (html).
     */
    public function saveRendered($file, $templatefile, $data=NULL, $isMakeTemplate=false, $process=NULL,
        $templateSkin='default/', $folder='web/', $isBaseTag=false, $version=1){
    	$this->isMakeTemplate = $isMakeTemplate;
        ob_start();
        $this->render($templatefile, $data, null, true, $templateSkin, $folder, $isBaseTag, $version);
        $data = ob_get_contents();
        ob_end_clean();

		$this->setRootViewPath();
        $vfilename= $this->rootViewPath. "view/$file.html";

        if(file_put_contents($vfilename, $data)>0){
            $filename = explode('/',$vfilename);
            return $filename[sizeof($filename)-1];
        }
        return false;
    }


    public function saveRendered_ent($entid,$moduleid,$pageformid,$terminal,
        $filepath, $templatefile, $data=NULL, $isMakeTemplate=false, $process=NULL,
        $templateSkin='default/', $folder='web/', $isBaseTag=false, $version=1){
        $this->isMakeTemplate = $isMakeTemplate;
        ob_start();
        $this->renderex($templatefile, $data, null, true, $templateSkin, $folder, $isBaseTag, $version);
        $data = ob_get_contents();
        ob_end_clean();

        $vfilename = $this->getViewFilePathEnt($entid,$moduleid,$pageformid,$terminal,$filepath);
        // update suson.20160118
        Doo::loadHelper('DooFile');
        $fileManager = new DooFile(0777);
        $ret = $fileManager->create($vfilename, $data, 'w+');
        if($ret === true){
            $filename = explode('/',$vfilename);
            return $filename[sizeof($filename)-1];
        }
        // if(file_put_contents($vfilename, $data)>0){
        //     $filename = explode('/',$vfilename);
        //     return $filename[sizeof($filename)-1];
        // }
        return false;
    }

    /**
     * [saveTmp_ent template表的content直接生成entfiles文件]
     * suson.20160621
     * @param  [type]  $entid          [description]
     * @param  [type]  $moduleid       [description]
     * @param  [type]  $pageformid     [description]
     * @param  [type]  $terminal       [description]
     * @param  [type]  $filepath       [description]
     * @param  string  $templatefile   [description]
     * @param  [type]  $data           [description]
     * @param  boolean $isMakeTemplate [description]
     * @param  [type]  $process        [description]
     * @param  string  $templateSkin   [description]
     * @param  string  $folder         [description]
     * @param  boolean $isBaseTag      [description]
     * @param  integer $version        [description]
     * @return [type]                  [description]
     */
    public function saveTmp_ent($prm_base,$entid,$moduleid,$pageformid,$terminal,
    	$filepath, $templatefile='', $data=NULL, $isMakeTemplate=false, $process=NULL,
        $templateSkin='default/', $folder='web/', $isBaseTag=false, $version=1){
    	$this->isMakeTemplate = $isMakeTemplate;
        $template_ret = ApiClass::pagemake_temp_replace($version,$prm_base,$pageformid,$moduleid);
        $data = !empty($template_ret['data']) ? $template_ret['data'] : '';
		$vfilename = $this->getViewFilePathEnt($entid,$moduleid,$pageformid,$terminal,$filepath);
        Doo::loadHelper('DooFile');
        $fileManager = new DooFile(0777);
        $ret = $fileManager->create($vfilename, $data, 'w+');
        if($ret === true){
            $filename = explode('/',$vfilename);
            return $filename[sizeof($filename)-1];
        }
        return false;
    }

    /**
     * Writes the generated output produced by renderc() to file.
     * @param string $path Path to save the generated output.
     * @param string $templatefile Template file name (without extension name)
     * @param array $data Associative array of the data to be used in the Template file. eg. <b>$data['username']</b>, you should use <b>{{username}}</b> in the template.
     * @param object $controller The controller object, pass this in so that in views you can access the controller.
     * @param bool $includeTagClass If true, DooView will determine which Template tag class to include. Else, no files will be loaded
     * @return string|false The file name of the rendered output saved (html).
     */
    public function saveRenderedC($path, $templatefile, $data=NULL, $controller=NULL, $includeTagClass=TRUE){
        ob_start();
        $this->renderc($templatefile, $data, $controller, $includeTagClass);
        $data = ob_get_contents();
        ob_end_clean();
        if(file_put_contents($path, $data)>0){
            $filename = explode('/',$path);
            return $filename[sizeof($filename)-1];
        }
        return false;
    }

    /**
     * Renders the view file, generates compiled version of the view template if necessary
     * @param string $file Template file name (without extension name)
     * @param array $data Associative array of the data to be used in the Template file. eg. <b>$data['username']</b>, you should use <b>{{username}}</b> in the template.
     * @param bool $process If TRUE, checks the template's last modified time against the compiled version. Regenerates if template is newer.
     * @param bool $forceCompile Ignores last modified time checking and force compile the template everytime it is visited.
     * isBaseTag、version为暂时使用参数
     */
    public function render($file, $data=NULL, $process=NULL, $forceCompile=false,
        $templateSkin='default/', $folder='web/', $isBaseTag=false, $version=1){
        $this->templateSkin = $templateSkin;
        $this->folder = $folder;
        $this->isBaseTag = $isBaseTag;
        $this->version = $version;
        if (substr($file,-10) === 'index_base') {
            $this->isIndexBase = true;
        }
        $this->setRootViewPath();

        if(isset(Doo::conf()->TEMPLATE_COMPILE_ALWAYS) && Doo::conf()->TEMPLATE_COMPILE_ALWAYS==true){
            $process = $forceCompile = true;
        }
        //if process not set, then check the app mode, if production mode, skip the process(false) and just include the compiled files
        else if($process===NULL){
            $process = (Doo::conf()->APP_MODE!='prod');
        }

        //just include the compiled file if process is false
        if($process!=true){
            //includes user defined template tags for template use
            $this->loadTagClass();
            //by james.ou 2011-4-1
            //include Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER . "viewc/$file.php";
            include $this->rootViewPath. "viewc/$file.php";
        }
        else{
            //by james.ou 2011-4-1
            //$cfilename = Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER . "viewc/$file.php";
            //$vfilename = Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER . "view/$file.html";

            $cfilename = $this->rootViewPath. "viewc/$file.php";
            $vfilename= $this->rootViewPath. "view/$file.html";

            //if file exist and is not older than the html template file, include the compiled php instead and exit the function
            if(!$forceCompile){
                if(file_exists($cfilename)){
                    if(filemtime($cfilename)>=filemtime($vfilename)){
                        $this->setTags();
                        include $cfilename;
                        return;
                    }
                }
            }
            // var_dump($cfilename);
            $this->data = $data;
            $this->compile($file, $vfilename, $cfilename);
            include $cfilename;
        }
    }

    public function render_ent($entid,$moduleid,$pageformid,$terminal,
    	$filepath, $templatefile, $data=NULL, $isMakeTemplate=false, $process=NULL,
        $templateSkin='default/', $folder='web/', $isBaseTag=false, $version=1){

        $this->templateSkin = $templateSkin;
        $this->folder = $folder;
        $this->isBaseTag = $isBaseTag;
        $this->version = $version;
        $file = '';
        if (substr($file,-10) === 'index_base') {
            $this->isIndexBase = true;
        }
        $this->setRootViewPath();

        if(isset(Doo::conf()->TEMPLATE_COMPILE_ALWAYS) && Doo::conf()->TEMPLATE_COMPILE_ALWAYS==true){
            $process = $forceCompile = true;
        }
        //if process not set, then check the app mode, if production mode, skip the process(false) and just include the compiled files
        else if($process===NULL){
            $process = (Doo::conf()->APP_MODE!='prod');
        }

        //just include the compiled file if process is false
        if($process!=true){
            //includes user defined template tags for template use
            $this->loadTagClass();
            //by james.ou 2011-4-1
            //include Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER . "viewc/$file.php";
            // include $this->rootViewPath. "viewc/$file.php";
            $cfilename = $this->getViewcFilePathEnt($entid,$moduleid,$pageformid,$terminal,$filepath);

        }
        else{
            //by james.ou 2011-4-1
            //$cfilename = Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER . "viewc/$file.php";
            //$vfilename = Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER . "view/$file.html";

            // $cfilename = $this->rootViewPath. "viewc/$file.php";
            // $vfilename= $this->rootViewPath. "view/$file.html";
            $cfilename = $this->getViewcFilePathEnt($entid,$moduleid,$pageformid,$terminal,$filepath);
            $vfilename = $this->getViewFilePathEnt($entid,$moduleid,$pageformid,$terminal,$filepath);
            // print_r($cfilename);
            // print_r($vfilename);exit();

            //if file exist and is not older than the html template file, include the compiled php instead and exit the function
            if(!$forceCompile){
                if(file_exists($cfilename)){
                    if(filemtime($cfilename)>=filemtime($vfilename)){
                        $this->setTags();
                        include $cfilename;
                        return;
                    }
                }
            }
            // var_dump($cfilename);
            $this->data = $data;
            $this->compile($file, $vfilename, $cfilename);
            include $cfilename;
        }
    }

    public function renderex($file, $data=NULL, $process=NULL, $forceCompile=false,
        $templateSkin='default/', $folder='web/', $isBaseTag=false, $version=1){
        $this->templateSkin = $templateSkin;
        $this->folder = $folder;
        $this->isBaseTag = $isBaseTag;
        $this->version = $version;
        if (substr($file,-10) === 'index_base') {
            $this->isIndexBase = true;
        }
      	$this->setRootViewPath();

        if(isset(Doo::conf()->TEMPLATE_COMPILE_ALWAYS) && Doo::conf()->TEMPLATE_COMPILE_ALWAYS==true){
            $process = $forceCompile = true;
        }
        //if process not set, then check the app mode, if production mode, skip the process(false) and just include the compiled files
        else if($process===NULL){
            $process = (Doo::conf()->APP_MODE!='prod');
        }

        //just include the compiled file if process is false
        if($process!=true && 1==2){
            //includes user defined template tags for template use
            $this->loadTagClass();
            //by james.ou 2011-4-1
            //include Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER . "viewc/$file.php";
            // include $this->rootViewPath. "viewc/$file.php";
            include Doo::conf()->ROOT_VIEW_PATH_V3 . "$file.php";
        }
        else{
            //by james.ou 2011-4-1
            //$cfilename = Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER . "viewc/$file.php";
            //$vfilename = Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER . "view/$file.html";

            // $cfilename = $this->rootViewPath. "viewc/$file.php";
            // $vfilename= $this->rootViewPath. "view/$file.html";
            $cfilename = Doo::conf()->ROOT_VIEW_PATH_V3 . "$file.php";
            $vfilename = Doo::conf()->ROOT_VIEW_PATH_V3 . "$file";

            //if file exist and is not older than the html template file, include the compiled php instead and exit the function
            if(!$forceCompile){
                if(file_exists($cfilename)){
                    if(filemtime($cfilename)>=filemtime($vfilename)){
                        $this->setTags();
                        include $cfilename;
                        return;
                    }
                }
            }

            $this->data = $data;
            $this->compile($file, $vfilename, $cfilename);
            include $cfilename;
        }
    }

    /**
     * Renders layouts
     * @param string $layoutName Name of the layout
     * @param string $viewFile View file name (without extension name .html)
     * @param array $data Associative array of the data to be used in the Template file. eg. <b>$data['username']</b>, you should use <b>{{username}}</b> in the template.
     * @param bool $process If TRUE, checks the template's last modified time against the compiled version. Regenerates if template is newer.
     * @param bool $forceCompile Ignores last modified time checking and force compile the template everytime it is visited.
     */
    public function renderLayout($layoutName, $viewFile, $data=NULL, $process=NULL, $forceCompile=false) {

        $compiledViewFile = $layoutName . '/' . $viewFile;

        if(isset(Doo::conf()->TEMPLATE_COMPILE_ALWAYS) && Doo::conf()->TEMPLATE_COMPILE_ALWAYS==true){
            $process = $forceCompile = true;
        }
        //if process not set, then check the app mode, if production mode, skip the process(false) and just include the compiled files
        else if($process===NULL){
            $process = (Doo::conf()->APP_MODE!='prod');
        }

        //just include the compiled file if process is false
        if($process!=true){
            //includes user defined template tags for template use
            $this->loadTagClass();
            //by james.ou 2011-4-1
           // include Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER . "viewc/$compiledViewFile.php";
            include $this->rootViewPath . "viewc/$compiledViewFile.php";
        }
        else{
            //by james.ou 2011-4-11
            //$lfilename = Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER . "layout/$layoutName.html";
            //$vfilename = Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER . "view/$viewFile.html";
            //$cfilename = Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER . "viewc/$compiledViewFile.php";

             $lfilename = $this->rootViewPath . "layout/$layoutName.html";
            $vfilename = $this->rootViewPath .  "view/$viewFile.html";
            $cfilename = $this->rootViewPath .  "viewc/$compiledViewFile.php";

            //if file exist and is not older than the html template file AND layout file, include the compiled php instead and exit the function
            if(!$forceCompile){
                if(file_exists($cfilename)){
                    if(filemtime($cfilename)>=filemtime($vfilename) && filemtime($cfilename)>=filemtime($lfilename)){
                        $this->setTags();
                        include $cfilename;
                        return;
                    }
                }
            }
            $this->data = $data;
            $this->compileLayout($compiledViewFile, $lfilename, $vfilename, $cfilename);
            include $cfilename;
        }

    }

    /**
     * Contains the contents of view blocks used with layouts
     * @var array
     */
    private $viewBlocks = null;

    /**
     * Parses and compiled a view into a layout to fill in placeholders and
     * stores the resulting view file to then be processed as normal by DooView::compile
     * @param string $viewFile The original location of the view without extension .html
     * @param string $lfilename Full path to the layout file
     * @param string $vfilename Full path to the view to be merged into the layout
     * @param string $cfilename Full path of the compiled file to be saved
     */
    protected function compileLayout($viewFile, $lfilename, $vfilename, $cfilename) {

        $layout = file_get_contents($lfilename);
        $view = file_get_contents($vfilename);

        // Identify the blocks within a view file
        // <!-- block:NAME -->CONTENT<!-- endblock -->
        $this->viewBlocks = array();
        // We use \s\S to get ANY character including newlines etc as '.' will not get new lines
        // Also use +? and *? so as to use non greedy matching
        preg_replace_callback('/<!-- block:([^\t\r\n]+?) -->([\s\S]*?)<!-- endblock -->/', array( &$this, 'storeViewBlock'), $view);
        $compiledLayoutView = preg_replace_callback('/<!-- placeholder:([^\t\r\n]+?) -->([\s\S]*?)<!-- endplaceholder -->/', array( &$this, 'replacePlaceholder'), $layout);


        $this->mainRenderFolder = $viewFile;

        //--------------------------- Parsing -----------------------------
        //if no compiled file exist or compiled file is older, generate new one
        $str = $this->compileTags($compiledLayoutView);

        Doo::loadHelper('DooFile');
        $fileManager = new DooFile(0777);
        $fileManager->create($cfilename, $str, 'w+');

    }


    /**
     * Parse and compile the template file. Templates generated in protected/viewc folder
     * @param string $file Template file name without extension .html
     * @param string $vfilename Full path of the template file
     * @param string $cfilename Full path of the compiled file to be saved
     */
    protected function compile($file, $vfilename, $cfilename){
        $this->mainRenderFolder = $file;

        //--------------------------- Parsing -----------------------------
        //if no compiled file exist or compiled file is older, generate new one
        $str = $this->compileTags(file_get_contents($vfilename));

        Doo::loadHelper('DooFile');
        $fileManager = new DooFile(0777);
        $fileManager->create($cfilename, $str, 'w+');
    }

    /**
     * Load the template class and returns the class name.
     * @return string Name of the class that is loaded.
     */
    public function loadTagClass(){
        /* if include tag class is not defined load TemplateTag for main app
         * else if render() is called from a module, load ModulenameTag */

        $tagFile = '';

        if( !isset($this->tagClassName) ){
            if( !isset(Doo::conf()->PROTECTED_FOLDER_ORI) ){
                $tagFile = Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER . 'plugin/TemplateTag.php';
                $tagcls = 'TemplateTag';
            }else{
                $tagcls = explode('/', Doo::conf()->PROTECTED_FOLDER);
                $tagcls = ucfirst($tagcls[sizeof($tagcls)-2]) . 'Tag';
                $tagFile = Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER . 'plugin/' . $tagcls .'.php';
                $this->tagModuleName = $tagcls;
            }
        }else{
            //load the main app's TemplateTag if module is '/'
            if($this->tagModuleName=='/'){
                $tagFile = Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER_ORI . 'plugin/'. $this->tagClassName .'.php';
            }
            else if($this->tagModuleName===Null){
                $tagFile = Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER . 'plugin/'. $this->tagClassName .'.php';
            }
            else{
                if(isset(Doo::conf()->PROTECTED_FOLDER_ORI))
                    $tagFile = Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER_ORI .'module/'. $this->tagModuleName . '/plugin/'. $this->tagClassName .'.php';
                else
                    $tagFile = Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER .'module/'. $this->tagModuleName . '/plugin/'. $this->tagClassName .'.php';
            }
            $tagcls = $this->tagClassName;
        }
        if (file_exists($tagFile)) {
            require_once $tagFile;
            return $tagcls;
        } else {
            return false;
        }
    }

    private function setTags(){
        $tagcls = $this->loadTagClass();

        if ($tagcls === false) {
            $template_tags = array();
        } else {
            $tagMethod = get_class_methods($tagcls);

            if(!empty($tagMethod)){
                if( !empty(Doo::conf()->TEMPLATE_GLOBAL_TAGS) )
                    $template_tags = array_merge(Doo::conf()->TEMPLATE_GLOBAL_TAGS, $tagMethod);
                else
                    $template_tags = $tagMethod;

                $template_tags['_methods'] = $tagMethod;
                $template_tags['_class'] = $tagcls;
            }
            else if( !empty(Doo::conf()->TEMPLATE_GLOBAL_TAGS) ){
                $template_tags = Doo::conf()->TEMPLATE_GLOBAL_TAGS;
            }
            else{
                $template_tags = array();
            }

            foreach($template_tags as $k=>$v ){
                if(is_int($k))
                    $template_tags[$k] = strtolower($v);
                else
                    $template_tags[$k] = $v;
            }
        }
        Doo::conf()->add('TEMPLATE_TAGS', $template_tags);
        return $template_tags;
    }

    /**
     * Processes a string containing DooPHP Template tags and replaces them with the relevant PHP code required
     * @param string $str This is the html template markup from View files
     * @return string The PHP markedup version of the View file
     */
    private function compileTags($str) {

        //includes user defined template tags and checks for the tag and compile.
        if($this->tags===NULL){
            if(!isset(Doo::conf()->TEMPLATE_TAGS)){
                $this->tags = $this->setTags();
            }else{
                $this->tags = Doo::conf()->TEMPLATE_TAGS;
            }
        }

        if ($this->isMakeTemplate != true) {
	        //convert start getBlock <!--# getBlock({})  -->
	        $str = preg_replace_callback('/{#getBlock\(([\S|\s]*)\)#}/Ui', array( &$this, 'convertGetBlock'), $str);
            $str = preg_replace_callback('/{#getBlock\(([\S|\s]*)\)#}/Ui', array( &$this, 'convertGetBlock'), $str);
	        //convert start getComponent <!--# getComponent({})  -->$str = preg_replace_callback('/<!-- getComponent\(\{([\S|\s]*)\}\) -->/', array( &$this, 'convertGetComponent'), $str);
	        //$str = preg_replace_callback('/<!-- getComponent\(\{([\S|\s]*)\}\) -->/Ui', array( &$this, 'convertGetComponent'), $str);
	        //$str = preg_replace_callback('/{#getComponent\(([\S|\s]*)\)#}/Ui', array( &$this, 'convertGetComponent'), $str);
	        $str = preg_replace_callback('/{#getComponent\(([\S|\s]*)\)#}/Ui', array( &$this, 'convertGetComponent'), $str);
            for($i=0;$i<3;$i++){
                $str = preg_replace_callback('/{#getBlock\(([\S|\s]*)\)#}/Ui', array( &$this, 'convertGetBlock'), $str);
                $str = preg_replace_callback('/{#getComponent\(([\S|\s]*)\)#}/Ui', array( &$this, 'convertGetComponent'), $str);
            }


        }

        if( isset(Doo::conf()->TEMPLATE_ALLOW_PHP) ){
            if( Doo::conf()->TEMPLATE_ALLOW_PHP === False ){
                $str = preg_replace('/<\?(php|\=|\+)?([\S|\s]*)\?>/Ui', '', $str);
            }
        }else{
            $str = preg_replace_callback('/<\?(php|\=|\+)?([\S|\s]*)\?>/Ui', array( &$this, 'convertPhpFunction'), $str);
        }

        //convert variables to static string <p>{{+username}}</p> becomes <p>myusernamevalue</p>
        //$str = preg_replace_callback('/{{\+([^ \t\r\n\(\)\.}]+)}}/', array( &$this, 'writeStaticVar'), $str);
        $str = preg_replace_callback('/{\@\+([^ \t\r\n\(\)\.}]+)\@}/', array( &$this, 'writeStaticVar'), $str);

        //convert variables {@$v0@}
        $str = preg_replace('/{\@\$([^ \@\r\n]*)\@}/', "<?php echo \$$1; ?>", $str);
        // $str = preg_replace_callback('/{\@\$([^ \@\r\n]*)\@}/', array( &$this, 'convertVarEx'), $str);
        // $str = preg_replace_callback('/{:\$([^ \r\n]*):}/', array( &$this, 'convertVarEx1'), $str);
        //convert variables {{username}}
        /*$str = preg_replace('/{{([^ \t\r\n\(\)\.}]+)}}/', "<?php echo \$data['$1']; ?>", $str); */
        $str = preg_replace('/{\@([^ \t\r\n\(\)\.}]+)\@}/', "<?php echo \$data['$1']; ?>", $str);

        //convert non $data key variables {{$user.john}} {{$user.total.male}}
        //$str = preg_replace_callback('/{{\$([^ \t\r\n\(\)\.}]+)\.([^ \t\r\n\(\)}]+)}}/', array( &$this, 'convertNonDataVarKey'), $str);
        $str = preg_replace_callback('/{\@\$([^ \t\r\n\(\)\.}]+)\.([^ \t\r\n\(\)}]+)\@}/', array( &$this, 'convertNonDataVarKey'), $str);

        //convert key variables {{user.john}} {{user.total.male}}
        //$str = preg_replace_callback('/{{([^ \t\r\n\(\)\.}]+)\.([^ \t\r\n\(\)}]+)}}/', array( &$this, 'convertVarKey'), $str);
        $str = preg_replace_callback('/{\@([^ \t\r\n\(\)\.}]+)\.([^ \t\r\n\(\)}]+)\@}/', array( &$this, 'convertVarKey'), $str);

        //convert start getUrl {#getUrl()#}
        $str = preg_replace_callback('/{#getUrl\(([\S|\s]*)\)#}/Ui', array( &$this, 'convertGetUrl'), $str);

        $str = preg_replace_callback('/{#getRes\(([\S|\s]*)\)#}/Ui', array( &$this, 'convertGetRes'), $str);
        $str = preg_replace_callback('/{#getJs\(([\S|\s]*)\)#}/Ui', array( &$this, 'convertGetJs'), $str);
        $str = preg_replace_callback('/{#getCss\(([\S|\s]*)\)#}/Ui', array( &$this, 'convertGetCss'), $str);

        //convert start switch <!--# switch users --> <!--# switch users' value --> <!--# switch users' value' value -->
        //$str = preg_replace_callback('/<!-- switch ([^ \t\r\n\(\)}\']+).* -->/', array( &$this, 'convertSwitch'), $str);
        $str = preg_replace_callback('/{#switch ([^ \t\r\n\(\)}\']+).*#}/', array( &$this, 'convertSwitch'), $str);
        //$str = preg_replace_callback('/<!-- case (.*) -->/', array( &$this, 'convertSwitchCase'), $str);
        $str = preg_replace_callback('/{#case (.*)#}/', array( &$this, 'convertSwitchCase'), $str);
        /*$str = str_replace('<!-- default -->', '<?php break;default: ?>', $str);*/
        $str = str_replace('{#default#}', '<?php break;default: ?>', $str);
        //convert end switch
        /*$str = str_replace('<!-- endswitch -->', '<?php break;endswitch; ?>', $str);*/
        $str = str_replace('{#endswitch#}', '<?php break;endswitch; ?>', $str);
        $str = preg_replace("/ switch_temp\?>(.*\s.*)<\?php break;/", "$1", $str);

        //convert start loop {#loop users @@ value #}
        $str = preg_replace_callback('/{#loop (([^\t\r\n\(\)}\']+).*)@@(([^\t\r\n\(\)}\']+).*)#}/', array( &$this, 'convertLoopEx'), $str);
        // $str = preg_replace_callback('/{#loop1 ([^ \t\r\n\(\)}\']+).*#}/', array( &$this, 'convertLoopEx'), $str);

        //convert start loop <!--# loop users --> <!--# loop users' value --> <!--# loop users' value' value -->
        //$str = preg_replace_callback('/<!-- loop ([^ \t\r\n\(\)}\']+).* -->/', array( &$this, 'convertLoop'), $str);
        $str = preg_replace_callback('/{#loop ([^ \t\r\n\(\)}\']+).*#}/', array( &$this, 'convertLoop'), $str);
        //convert end loop
        /*$str = str_replace('<!-- endloop -->', '<?php endforeach; ?>', $str);*/
        $str = str_replace('{#endloop#}', '<?php endforeach; ?>', $str);

        //convert variable in loop {{user' value}}  {{user' value' value}}
        //$str = preg_replace_callback('/{{([^ \t\r\n\(\)\.}\']+)([^\t\r\n\(\)}{]+)}}/', array( &$this, 'convertVarLoop'), $str);
        $str = preg_replace_callback('/{\@([^ \t\r\n\(\)\.}\']+)([^\t\r\n\(\)}{]+)\@}/', array( &$this, 'convertVarLoop'), $str);

        //转换模版函数
        //$str = preg_replace_callback('/{{([^ \t\r\n\(\)}]+?)\((.*?)\)}}/', array( &$this, 'convertFunction'), $str);
        $str = preg_replace_callback('/{\@([^ \t\r\n\(\)}]+?)\((.*?)\)\@}/', array( &$this, 'convertFunction'), $str);
        $str = preg_replace_callback('/{:([^ \t\r\n\(\)}]+?)\((.*?)\):}/', array( &$this, 'convertFunctionEx'), $str);

        //convert start of for loop
        //$str = preg_replace_callback('/<!-- for ([^\t\r\n\(\)}{]+) -->/', array( &$this, 'convertFor'), $str);
        $str = preg_replace_callback('/{#for ([^\t\r\n\(\)}{]+)#}/', array( &$this, 'convertFor'), $str);
        //convert end for
        /*$str = str_replace('<!-- endfor -->', '<?php endforeach; ?>', $str);*/
        $str = str_replace('{#endfor#}', '<?php endforeach; ?>', $str);

        // convert set
        //$str = preg_replace_callback('/<!-- set ([^ \t\r\n\(\)\.}]+) as (.*?) -->/U', array( &$this, 'convertSet'), $str);
        $str = preg_replace_callback('/{#set ([^ \t\r\n\(\)\.}]+) as (.*?)#}/U', array( &$this, 'convertSet'), $str);

        //convert if and else if condition <!-- if expression --> <!-- elseif expression -->  only functions in template_tags are allowed
        //$str = preg_replace_callback('/<!-- (if|elseif) ([^\t\r\n}]+) -->/U', array( &$this, 'convertCond'), $str);
        $str = preg_replace_callback('/{#(if|elseif) ([^\t\r\n}]+)#}/U', array( &$this, 'convertCond'), $str);
        //convert else, end if
        /*$str = str_replace('<!-- else -->', '<?php else: ?>', $str);*/
        $str = str_replace('{#else#}', '<?php else: ?>', $str);
        /*$str = str_replace('<!-- endif -->', '<?php endif; ?>', $str);*/
        $str = str_replace('{#endif#}', '<?php endif; ?>', $str);

        //convert continue
        /*$str = str_replace('<!-- continue -->', '<?php continue; ?>', $str);*/
        $str = str_replace('{#continue#}', '<?php continue; ?>', $str);

        //convert break
        /*$str = str_replace('<!-- break -->', '<?php break; ?>', $str);*/
        $str = str_replace('{#break#}', '<?php break; ?>', $str);

        //convert cache <!-- cache('partial_id', 60) -->
        /*$str = preg_replace_callback('/<!-- cache\(([^\t\r\n}\)]+)\) -->/', array( &$this, 'convertCache'), $str);*/
        $str = preg_replace_callback('/{#cache\(([^\t\r\n}\)]+)\)#}/', array( &$this, 'convertCache'), $str);
        //convert end cache <!-- endcache -->
        /*$str = str_replace('<!-- endcache -->', "\n<?php Doo::cache('front')->end(); ?>\n<?php endif; ?>", $str);*/
        $str = str_replace('{#endcache#}', "\n<?php Doo::cache('front')->end(); ?>\n<?php endif; ?>", $str);

        //convert include to php include and parse & compile the file, if include file not exist Echo error and exit application
        // <?php echo $data['file']; chars allowed for the grouping
        //$str = preg_replace_callback('/<!-- include [\'\"]{1}([^\t\r\n\"]+).*[\'\"]{1} -->/', array( &$this, 'convertInclude'), $str);
        $str = preg_replace_callback('/{#include [\'\"]{1}([^\t\r\n\"]+).*[\'\"]{1}#}/', array( &$this, 'convertInclude'), $str);

        $str = preg_replace_callback('/{#initPage#}/', array( &$this, 'convertInitPage'), $str);

        //remove comments
        if(!isset(Doo::conf()->TEMPLATE_SHOW_COMMENT) || Doo::conf()->TEMPLATE_SHOW_COMMENT!=true){
            //$str = preg_replace('/<!-- comment -->.+<!-- endcomment -->/s', '', $str);
            //$str = str_replace('<!-- comment -->', '<?php /** ', $str);
            $str = str_replace('{#comment#}', '<?php /** ', $str);
            /*$str = str_replace('<!-- endcomment -->', ' * / ?>', $str);*/
            $str = str_replace('{#endcomment#}', ' */ ?>', $str);
        }

        //$str = str_replace('{-{', '{{', $str);
        //$str = str_replace('}-}', '}}', $str);
        $str = str_replace('{||@', '{@', $str);
        $str = str_replace('@||}', '@}', $str);
        $str = str_replace('{||#', '{#', $str);
        $str = str_replace('#||}', '#}', $str);

        return $str;
    }

    private function writeStaticVar($matches){
        return $this->data[$matches[1]];
    }

    private function convertPhpFunction($matches){
        //LogClass::log_trace($matches,__FILE__,__LINE__,EnumClass::LOG_TYPE_VAR_DUMP);
        if(stripos($matches[0], '<?php')!==0 && strpos($matches[0], '<?=')!==0 && strpos($matches[0], '<?+')!==0  && strpos($matches[0], '<? ')!==0 ){
            return $matches[0];
        }

        $str = preg_replace_callback('/([^ \t\r\n\(\)}]+)([\s\t]*?)\(/', array( &$this, 'parseFunc'), $matches[2]);
        if(strpos($str, 'php')===0)
            $str = substr($str, 3);

        //if short tag <?=, convert to <?php echo
        if($matches[2][0]=='='){
            $str = substr($str, 1);
            return '<?php echo ' . $str .' ?>';
        }
        //write the variable value
        else if($matches[2][0]=='+'){
            $str = substr($str, 1);
            return eval('return ' . $str);
        }

        return '<?php ' . $str .' ?>';
    }

    private function parseFunc($matches){
        LogClass::log_trace($matches,__FILE__,__LINE__,EnumClass::LOG_TYPE_VAR_DUMP);
        //matches and check function name against template tag
        if(!empty($matches[1])){
            $funcname = trim(strtolower($matches[1]));
            if($funcname[0]=='+' || $funcname[0]=='=')
                $funcname = substr($funcname, 1);

            $controls = array('if','elseif','else if','while','switch','for','foreach','switch','return','include','require','include_once','require_once','declare','define','defined','die','constant','array');

            //skip checking static method usage: TemplateTag::go(), Doo::conf()
            if(stripos($funcname, $this->tags['_class'] . '::')===False && stripos($funcname, 'Doo')===False){
                $funcname = str_ireplace($this->tags['_class'] . '::', '', $funcname);
                if(!in_array($funcname, $controls)){
                    if(!in_array($funcname, $this->tags)) {
                        if ($this->tagModuleName != null){
                            return $this->tagModuleName .'::'. $matches[1].'(';
                        }else{
                            return 'function_deny(';
                        }
                    }
                }
            }
        }
        if ($this->isBaseTag === true) {
            return 'BaseTag::'. $matches[1].'(';
        }else{
            return $matches[1].'(';
        }
    }

    private function stripCommaStr($matches){
        $str = implode('\/\.\;', explode(',', $matches[0]) );
        $str = substr($str, 1, strlen($str)-2);
        return "'".$str."'";
    }

    private function convertFunction($matches) {

		//LogClass::log_trace($matches,__FILE__,__LINE__,EnumClass::LOG_TYPE_VAR_DUMP);
		if (strpos($matches[2], '(') !== FALSE) {
	        if($matches[1][0]=='+'){
	            $matches[1] = substr($matches[1], 1);
	            $writeStaticValue = true;
	        }
	        $functionName = $matches[1];
	        if(isset($this->tags['_methods']) && in_array($functionName, $this->tags['_methods'])===True){
	            if ($this->isIndexBase === true) {
	                $this->tags['_class'] = 'BaseTag';
	            }
	            //LogClass::log_trace($this->tags['_class'],__FILE__,__LINE__,EnumClass::LOG_TYPE_VAR_DUMP);
	            $functionName = $this->tags['_class'] . '::' . $functionName;
	        }
	        // LogClass::log_trace($matches,__FILE__,__LINE__,EnumClass::LOG_TYPE_VAR_DUMP);
	        // LogClass::log_trace($str,__FILE__,__LINE__,EnumClass::LOG_TYPE_VAR_DUMP);
    		$str = preg_replace_callback('/([^ \t\r\n\(\)}]+?)\((.*?)\)/', array( &$this, 'convertFunctionCore'), $matches[2]);
			// if ($functionName == 'BaseTag::getcomponent_return_value') {
			// 	LogClass::log_trace($str,__FILE__,__LINE__,EnumClass::LOG_TYPE_VAR_DUMP);exit();
			// }
    		$return = "{$functionName}($str)";
    	}else{
			$return = $this->convertFunctionCore($matches);
		}

        return "<?php echo $return; ?>";

    }

    private function convertFunctionEx($matches) {

        //LogClass::log_trace($matches,__FILE__,__LINE__,EnumClass::LOG_TYPE_VAR_DUMP);
        if (strpos($matches[2], '(') !== FALSE) {
            if($matches[1][0]=='+'){
                $matches[1] = substr($matches[1], 1);
                $writeStaticValue = true;
            }
            $functionName = $matches[1];
            if(isset($this->tags['_methods']) && in_array($functionName, $this->tags['_methods'])===True){
                if ($this->isIndexBase === true) {
                    $this->tags['_class'] = 'BaseTag';
                }
                //LogClass::log_trace($this->tags['_class'],__FILE__,__LINE__,EnumClass::LOG_TYPE_VAR_DUMP);
                $functionName = $this->tags['_class'] . '::' . $functionName;
            }
            // LogClass::log_trace($matches,__FILE__,__LINE__,EnumClass::LOG_TYPE_VAR_DUMP);
            // LogClass::log_trace($str,__FILE__,__LINE__,EnumClass::LOG_TYPE_VAR_DUMP);
            $str = $matches[2];
            // var_dump($matches);
            // if ($functionName == 'BaseTag::getcomponent_return_value') {
            //  LogClass::log_trace($str,__FILE__,__LINE__,EnumClass::LOG_TYPE_VAR_DUMP);exit();
            // }
            $return = "{$functionName}($str)";
        }else{
            $return = $this->convertFunctionCore($matches);
        }
        $functionName = $matches[1];
        if(isset($this->tags['_methods']) && in_array($functionName, $this->tags['_methods'])===True){
            if ($this->isIndexBase === true) {
                $this->tags['_class'] = 'BaseTag';
            }
            //LogClass::log_trace($this->tags['_class'],__FILE__,__LINE__,EnumClass::LOG_TYPE_VAR_DUMP);
            $functionName = $this->tags['_class'] . '::' . $functionName;
        }
        $str = $matches[2];
        // var_dump($matches);
        $return = "{$functionName}($str)";

        return "<?php echo $return; ?>";
    }

    private function convertFunctionCore($matches) {
        if($matches[1][0]=='+'){
            $matches[1] = substr($matches[1], 1);
            $writeStaticValue = true;
        }
        // if(!in_array(strtolower($matches[1]), $this->tags)) {
        //     return '<span style="color:#ff0000;">Function '.$matches[1].'() Denied</span>';
        // }

        //LogClass::log_trace($matches,__FILE__,__LINE__,EnumClass::LOG_TYPE_VAR_DUMP);
        //LogClass::log_trace($this->tags,__FILE__,__LINE__,EnumClass::LOG_TYPE_VAR_DUMP);
        $functionName = $matches[1];
        if(isset($this->tags['_methods']) && in_array($functionName, $this->tags['_methods'])===True){
            if ($this->isIndexBase === true) {
                $this->tags['_class'] = 'BaseTag';
            }
            //LogClass::log_trace($this->tags,__FILE__,__LINE__,EnumClass::LOG_TYPE_VAR_DUMP);
            $functionName = $this->tags['_class'] . '::' . $functionName;
        }

		if (strpos($matches[2], "'{") === 0) {
        	//避免{@funa({"a":"123","b":"1234")@}被处理
			$args = $matches[2];
			// LogClass::log_trace($args,__FILE__,__LINE__,EnumClass::LOG_TYPE_VAR_DUMP);
		}else{
	        //replace , to something else if it's in a string parameter
	        if(strpos($matches[2], ',')!==False){
	            $matches[2] = preg_replace_callback('/\"(.+)\"/', array( &$this, 'stripCommaStr'), $matches[2]);
	        }

	        $stmt = str_replace('<?php echo ', '', $matches[2]);
	        $stmt = str_replace('; ?>', '', $stmt);

	        if (preg_match('/^[\'\"].*[\'\"]$/', $stmt)) {
	        	//避免{@funa('31,23,45')@}被拆分为多个参数处理
				$args = $stmt;
			}else{
				//{@funa(aa,bb,cc')@}拆分为多个参数处理
		        $parameters = explode(',', $stmt);

		        $args = '';

		        foreach ($parameters as $param) {
		            $param = trim($param);
		            if (strlen($args) > 0) {
		                $args .= ', ';
		            }

		            // Is a number
		            if (preg_match('/^[0-9]*\\.?[0-9]{0,}$/', $param)) {
		                $args .= $param;
		            }
		            // Is a string 'anything' OR "anything"
		            elseif (preg_match('/^[\'\"].*[\'\"]$/', $param)) {
		                $args .= str_replace('\/\.\;', ',', $param);
		            }
		            elseif (strtolower($param)=='true' || strtolower($param)=='false') {
		                $args .= $param;
		            }
		            // Got parameter values to handle
		            else {
		                $args .= $this->extractObjectVariables($param);
		            }
		        }
		    }
		}

		// if ($functionName == 'BaseTag::getcomponent_return_value') {
    		//LogClass::log_trace($args,__FILE__,__LINE__,EnumClass::LOG_TYPE_VAR_DUMP);exit();
    	// }
        //if + in front, write the value of the function call
        if(!empty($writeStaticValue)){
            return eval("return {$functionName}($args);");
        }

        return "{$functionName}($args)";
    }

	/**
	 * convertFunctionComponet 处理控件的模版方法
	 * 控件的模版使用模版方法（该模版方法在页面运行中调用，不是在生成控件时调用）
	 * 例控件模版为：{|@funa({@component_value@})@|},调用该控件时，
	 * 如果component_value传入如果是变量vara则生成{|@funa({@vara@})@|}，转换为{|@funa(vara)@|}
	 * 如果component_value传入如果是字符串asd,df,sdf则生成{|@funa(asd,df,sdf)@|}，转换为{|@funa('asd,df,sdf')@|}
	 * @param  array $matches 匹配得到的数组
	 * @return String         返回字符串
	 */
	private function convertFunctionComponet($matches) {
        $functionName = $matches[1];

        if(strpos($matches[2], '{@') === 0){
        	$args = str_replace('{@', '', $matches[2]);
        	$args = str_replace('@}', '', $args);
        } else {
			$args = "'{$matches[2]}'";
        }
        return "{|@{$functionName}($args)@|}";
    }

    private function checkFuncAllowed($matches){
        if(!in_array(strtolower($matches[1]), $this->tags))
            return 'function_deny('. $matches[2] .')';
        return $matches[1].'('. $matches[2] .')';
    }

    private function storeViewBlock($matches){
        // Store blocks as blockName => blockContent
        $this->viewBlocks[$matches[1]] = $matches[2];
        return '';
    }

    private function replacePlaceholder($matches) {
        $blockName = $matches[1];
        // If the block has been defined in the view then use it otherwise
        // use the default from the layout
        if (isset( $this->viewBlocks[$matches[1]] )) {
            return $this->viewBlocks[$matches[1]];
        } else {
            return $matches[2];
        }
    }

    private function convertCache($matches){
        $data = str_replace(array('<?php echo ', '; ?>'), '', $matches[1]);
        $data = explode(',', $data);
        if(sizeof($data)==2){
            $data[1] = intval($data[1]);
            return "<?php if (!Doo::cache('front')->getPart({$data[0]}, {$data[1]})): ?>\n<?php Doo::cache('front')->start({$data[0]}); ?>";
        }else{
            return "<?php if (!Doo::cache('front')->getPart({$data[0]})): ?>\n<?php Doo::cache('front')->start({$data[0]}); ?>";
        }
    }

    private function convertCond($matches){
        //echo '<h1>'.str_replace('>', '&gt;', str_replace('<', '&lt;', $matches[2])).'</h1>';
        $stmt = str_replace('<?php echo ', '', $matches[2]);
        $stmt = str_replace('; ?>', '', $stmt);
        //echo '<h1>'.$stmt.'</h1>';

        //prevent malicious HTML designers to use function with spaces
        //eg. unlink        ( 'allmyfiles.file'  ), php allows this to happen!!
        $stmt = preg_replace_callback('/([a-z0-9\-_]+)[ ]*\([ ]*([^ \t\r\n}]+)\)/i', array( &$this, 'checkFuncAllowed'), $stmt);

        //echo '<h1>'.$stmt.'</h1>';
        switch($matches[1]){
            case 'if':
                return '<?php if( '.$stmt.' ): ?>';
            case 'elseif':
                return '<?php elseif( '.$stmt.' ): ?>';
        }
    }

    private function convertFor($matches) {
        $expr = str_replace('<?php echo ', '', $matches[1]);
        $expr = str_replace('; ?>', '', $expr);

        //for: i from 0 to 10
        if (preg_match('/([a-z0-9\-_]+?) from ([^ \t\r\n\(\)}]+) to ([^ \t\r\n\(\)}]+)( step ([^ \t\r\n\(\)}]+))?/i', $expr)){
            $expr = preg_replace_callback('/([a-z0-9\-_]+?) from ([^ \t\r\n\(\)}]+) to ([^ \t\r\n\(\)}]+)( step ([^ \t\r\n\(\)}]+))?/i', array( &$this, 'buildRangeForLoop'), $expr);
        }
        // for: 'myArray as key=>val'
        else if (preg_match('/([a-z0-9\-_]+?) as ([a-z0-9\-_]+)[ ]?=>[ ]?([a-z0-9\-_]+)/i', $expr)) {
            $expr = preg_replace_callback('/([a-z0-9\-_]+?) as ([a-z0-9\-_]+)[ ]?=>[ ]?([a-z0-9\-_]+)/i', array( &$this, 'buildKeyValForLoop'), $expr);
        }
        // for: 'myArray as val'
        else if (preg_match('/([a-z0-9\-_]+?) as ([a-z0-9\-_]+)/i', $expr)) {
            $expr = preg_replace_callback('/([a-z0-9\-_]+?) as ([a-z0-9\-_]+)/i', array( &$this, 'buildValForLoop'), $expr);
        }
        return $expr;
    }

    private function buildRangeForLoop($matches) {
        $stepBy = isset($matches[5]) ? $matches[5] : 1;
        return '<?php foreach(range(' . $matches[2] . ', ' . $matches[3] . ', ' . $stepBy . ') as $data[\'' . $matches[1] . '\']): ?>';
    }

    private function buildKeyValForLoop($matches) {
        return '<?php foreach($data[\''.$matches[1].'\'] as $'.$matches[2].'=>$'.$matches[3].'): ?>';
    }

    private function buildValForLoop($matches) {
        return '<?php foreach($data[\''.$matches[1].'\'] as $'.$matches[2].'): ?>';
    }

    private function convertLoopEx($matches){
    	// var_dump($matches);exit();
		$loopname = trim($matches[1]);
		if (strpos($matches[3], ",") == FALSE){
			$value = trim($matches[3]);
			$key = $value . '_key';
		}else{
			$param = explode(',', $matches[3]);
			$key = trim($param[0]);
			$value = trim($param[1]);
		}
        return "<?php foreach($loopname as $key => $value): ?>";
    }

    private function convertLoop($matches){
        $looplevel = sizeof(explode('\' ', $matches[0]));
        if(strpos($matches[0], "' ")!==FALSE){
            $strValue = str_repeat("' value", $looplevel-1);
            //$loopStr = "<!-- loop {$matches[1]}$strValue.";
            $loopStr = "{#loop {$matches[1]}$strValue.";
            if( strpos($matches[0], $loopStr)===0){
                $loopStr = substr($matches[0], strlen($loopStr));
                //$loopStr = str_replace(' -->', '', $loopStr);
                $loopStr = str_replace('#}', '', $loopStr);
                $param = explode('.', $loopStr);
                $varBck ='';
                foreach($param as $pa){
                    if(strpos($pa, '@')===0){
                        $varBck .= '->' . substr($pa, 1);
                    }else{
                        $varBck .= "['$pa']";
                    }
                }
                $thislvl = $looplevel-1;
                $loopname = "\$v$thislvl$varBck";
            }else{
                $loopname = ($looplevel<2)? '$data[\''.$matches[1].'\']' : '$v'. ($looplevel-1);
            }
        }
        else if(strpos($matches[1], '.@')!==FALSE){
            $varname = str_replace('.@', '->', $matches[1]);
            $varname = explode('->', $varname);
            $firstname = $varname[0];
            array_splice($varname, 0, 1);
            $loopname =  '$data[\''.$firstname.'\']->' . implode('->', $varname) ;
        }
        else if(strpos($matches[1], '.')!==FALSE){
            $varname = explode('.',$matches[1]);
            $firstname = $varname[0];
            array_splice($varname, 0, 1);
            $loopname =  '$data[\''.$firstname .'\'][\''. implode("']['", $varname) .'\']';
        }
        else{
            $loopname = ($looplevel<2)? '$data[\''.$matches[1].'\']' : '$v'. ($looplevel-1);
        }
        return '<?php foreach('.$loopname.' as $k'.$looplevel.'=>$v'.$looplevel.'): ?>';
    }

    private function convertGetUrl($matches){
        $pattern="/[\'|\"](.*)[\'|\"],[\'|\"](.*)[\'|\"],[\'|\"](.*)[\'|\"]/";
        preg_match_all($pattern,$matches[1],$match);
        //LogClass::log_trace($match,__FILE__,__LINE__,EnumClass::LOG_TYPE_VAR_DUMP);
        $module = $match[1][0];
        $component = $match[2][0];
        $version = $match[3][0];

        $str = '<?php echo $data[\'fileurl\']?>'. $module .'/'. $version .'/'. $component.'?<?php echo $data[\'version\']?>';
        return $str;
    }

    private function convertGetRes($matches, $res = null){
        $pattern="/[\'|\"](.*)[\'|\"],[\'|\"](.*)[\'|\"],[\'|\"](.*)[\'|\"]/";
        preg_match_all($pattern,$matches[1],$match);
        //LogClass::log_trace($match,__FILE__,__LINE__,EnumClass::LOG_TYPE_VAR_DUMP);
        $module = $match[1][0];
        $component = $match[2][0];
        $version = $match[3][0];

        if ($res === null){
            $res = substr($component,strrpos($component,'.') + 1);
        }else{
            $component .= '.'. $res;
        }

        $rootViewPath = $this->rootViewPath_ORI;
        if ($module != '') {
            $rootViewPath .= 'module/' . $module;
        }
        $vfile = $rootViewPath ."/view/{$this->templateSkin}$version/{$this->folder}$component";
        $module = substr($module,strpos($module,'/') + 1);
        $cfile = Doo::conf()->ROOT_RES_PATH . "{$this->templateSkin}$module/$version/{$this->folder}$component";

        if ($this->isCompile($cfile, $vfile, $process=null) === true) {
            Doo::loadHelper('DooFile');
            $file = new DooFile();
            $content = $file->readFileContents($vfile);
            $file->create($cfile, $content);
        }


        /*
        $str = '<?php echo $data[\'fileurl\']?>'. "$res/$module/$version/$component" .'?<?php echo $data[\'version\']?>';
        */

        $str = '<?php echo $data[\'fileurl\']?>'. "$module/$version/{$this->folder}$component" .'?<?php echo $data[\'version\']?>';

        $component_array = explode('.' , $component);
        $suffix = end($component_array);
        switch($suffix){
            case 'js':
                $res_str = '<script type="text/javascript" src="'.$str.'"></script>';
                break;
            case 'css':
                $res_str = '<link rel="stylesheet" type="text/css"  href="'.$str.'"/>';
                break;
            default:
                $res_str = $str;
                break;
        }

        return $res_str;
    }

    private function convertGetJs($matches){
        return $this->convertGetRes($matches, 'js');
    }

    private function convertGetCss($matches){
        return $this->convertGetRes($matches, 'css');
    }

    private function convertGetBlock($matches){
        //LogClass::log_trace($matches,__FILE__,__LINE__,EnumClass::LOG_TYPE_VAR_DUMP);

        $pattern="/[\'|\"](.*)[\'|\"],[\'|\"](.*)[\'|\"],[\'|\"](.*)[\'|\"],([\S|\s]*)/";
        preg_match_all($pattern,$matches[1],$match);
        //LogClass::log_trace($match,__FILE__,__LINE__,EnumClass::LOG_TYPE_VAR_DUMP);
        $module = $match[1][0];
        $component = $match[2][0];
        $version = $match[3][0];
        $params = $match[4][0];

        // $function = 'api_'.$function;
        // if(isset(Doo::conf()->PROTECTED_FOLDER_ORI)===false){
        //  Doo::conf()->PROTECTED_FOLDER_ORI = $tmp = Doo::conf()->PROTECTED_FOLDER;
        //  Doo::conf()->PROTECTED_FOLDER = $tmp . 'module/'.$moduleName.'/';
        // }else{
        //  $tmp = Doo::conf()->PROTECTED_FOLDER;
        //  Doo::conf()->PROTECTED_FOLDER = Doo::conf()->PROTECTED_FOLDER_ORI . 'module/'.$moduleName.'/';
        // }
        // $path = Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER .'/class/';
        $rootViewPath = $this->rootViewPath_ORI;
        if ($module != '') {
            $rootViewPath .= 'module/' . $module;
        }
        //LogClass::log_trace($this->controller,__FILE__,__LINE__,EnumClass::LOG_TYPE_VAR_DUMP);
        $str = file_get_contents($rootViewPath ."/view/{$this->templateSkin}$version/{$this->folder}$component.html");
        return $str;
    }

    private function convertGetComponent($matches){
        $pattern="/[\'|\"](.*)[\'|\"],[\'|\"](.*)[\'|\"],[\'|\"](.*)[\'|\"],{([\S|\s]*)/";
        preg_match_all($pattern,$matches[1],$match);
        //LogClass::log_trace($match,__FILE__,__LINE__,EnumClass::LOG_TYPE_VAR_DUMP);
        $module = $match[1][0];
        $component = $match[2][0];
        $version = $match[3][0];
        //$params = "{\r\n". $match[4][0];
        $params = $match[4][0];

        $fullComponentName = "$module/$component";

        if(isset(Doo::conf()->TEMPLATE_COMPONENT_LIST) && in_array($fullComponentName, Doo::conf()->TEMPLATE_COMPONENT_LIST) ){
            $component_isrepeat = true;
        }else{
            Doo::conf()->TEMPLATE_COMPONENT_LIST[] = "$module/$component";
            $component_isrepeat = false;
        }


        //LogClass::log_trace(Doo::conf()->TEMPLATE_COMPONENT_LIST,__FILE__,__LINE__,EnumClass::LOG_TYPE_VAR_DUMP);

        //$paramArray = json_decode(str_replace('\'','"',str_replace('"','\\"',$params)));
        //LogClass::log_trace($match,__FILE__,__LINE__,EnumClass::LOG_TYPE_VAR_DUMP);
        //公共属性
        $component_siteurl = $this->getComponentParamsValue('siteurl',$params);
        $component_userno = $this->getComponentParamsValue('userno',$params);
        $component_enterpriseno = $this->getComponentParamsValue('enterpriseno',$params);
        if($component_siteurl == ''){
           $params = "\"siteurl\":\"{{baseurl}}\",\r\n".$params;
        }
        else if($component_userno == ''){
           $params = "\"userno\":\"{{prm_base.@userno}}\",\r\n".$params;
        }
        else if($component_enterpriseno == ''){
            $params = "\"enterpriseno\":\"{{prm_base.@enterpriseno}}\",\r\n".$params;
        }
        $params = "{\r\n". $params;

        $component_id = $this->getComponentParamsValue('id',$params);
        $component_name = $this->getComponentParamsValue('name',$params);
        $component_iscolon = $this->getComponentParamsValue('iscolon',$params);
        $component_isrand = $this->getComponentParamsValue('isrand',$params);
        $component_value = $this->getComponentParamsValue('value',$params);
        $component_value1 = $this->getComponentParamsValue('value1',$params);
        $component_value2 = $this->getComponentParamsValue('value2',$params);
        $component_value3 = $this->getComponentParamsValue('value3',$params);
        $component_value4 = $this->getComponentParamsValue('value4',$params);
        $component_value5 = $this->getComponentParamsValue('value5',$params);
        $component_width = $this->getComponentParamsValue('width',$params);
        $component_height = $this->getComponentParamsValue('height',$params);
        $component_usernos = $this->getComponentParamsValue('usernos',$params);
        $component_usernames = $this->getComponentParamsValue('usernames',$params);
        $component_validator = $this->getComponentParamsValue('validator',$params);
        $component_watermark = $this->getComponentParamsValue('watermark',$params);
        $component_quickselect = $this->getComponentParamsValue('quickselect',$params);
        $component_option = $this->getComponentParamsValue('option',$params);
        $component_defval = $this->getComponentParamsValue('defval',$params);
        $component_subdefine = $this->getComponentParamsValue('subdefine',$params);
        $component_serviceid = $this->getComponentParamsValue('serviceid',$params);
        $component_type = $this->getComponentParamsValue('type',$params);
        $component_tpltype = $this->getComponentParamsValue('tpltype',$params);
        $component_withoutres = $this->getComponentParamsValue('withoutres',$params);
        $component_idgroup = $this->getComponentParamsValue('idgroup',$params);
        $component_ruledata = $this->getComponentParamsValue('ruledata',$params);
        $component_isform = $this->getComponentParamsValue('isform',$params);
        $component_showtype = $this->getComponentParamsValue('showtype',$params);
        $component_showtitle = $this->getComponentParamsValue('showtitle',$params);
        $component_issimplify = $this->getComponentParamsValue('issimplify',$params);
        $component_display = $this->getComponentParamsValue('display',$params);
        if(strpos($component_validator, 'optional') === false){
            Doo::conf()->TEMPLATE_COMPONENT_VALIDATOR[] = $component_id;
        }

        $componentParams = array(
            'component' => $component,
            'component_id' => $component_id,//控件唯一ID，标识
            'component_name' => $component_name,//控件名
            'component_iscolon' => $component_iscolon, //控件名是否有冒号 true/false
            'component_isrand' => $component_isrand, //控件名是否有随机数 true/false
            'component_value' => $component_value,//控件的值。数据回显时用
            'component_value1' => $component_value1,//控件的值。数据回显时用
            'component_value2' => $component_value2,//控件的值。数据回显时用
            'component_value3' => $component_value3,//控件的值。数据回显时用
            'component_value4' => $component_value4,//控件的值。数据回显时用
            'component_value5' => $component_value5,//控件的值。数据回显时用
            'component_width' => $component_width,//控件宽度
            'component_height' => $component_height,//控件高度
            'component_usernos' => $component_usernos,//选人控件，默认选择人员userno 逗号分隔
            'component_usernames' => $component_usernames,//选人控件，默认选择人员名 逗号分隔
            'component_isrepeat' => $component_isrepeat,//是否重复 true/false
            'component_validator' => $component_validator,//验证规则 格式："type=textarea,isnotnull=false,min=1,max=30"
            'component_watermark' => $component_watermark,//背景水印，输入框文本框
            'component_quickselect' => $component_quickselect,//选人控件，快捷选择
            'component_option' => $component_option,//单多选下拉选项 格式：name:value竖线分隔 如："abc:1|def:2|ccc:3"
            'component_defval' => $component_defval,//默认值
            'component_params' => $params,//控件参数
            'component_subdefine' => $component_subdefine,//提交按钮定义，[{name:"a",distyp:"a"}{name:"b",,distyp:"button"}]
            'component_serviceid' => $component_serviceid,//业务ID
            'component_type' => $component_type,//控件类型
            'component_tpltype' => $component_tpltype==''?'app':$component_tpltype, //控件的模板类型
            'component_withoutres' => $component_withoutres,//不使用res资源文件
            'component_idgroup' => $component_idgroup,//收件人控件，对应多id
            'component_ruledata' => $component_ruledata,//规则数据(选人)
            'component_isform' => $component_isform,//是否form表单提交
            'component_showtype' => $component_showtype,//展示类型
            'component_showtitle' => $component_showtitle,//公文审批控件是否显示标题
            'component_issimplify' => $component_issimplify,//公文审批控件是否显示标题
            'component_display' => $component_display,//控件显示方式，预览 suson.20161208
            );
		//LogClass::log_trace($componentParams['component_option'],__FILE__,__LINE__,EnumClass::LOG_TYPE_VAR_DUMP);
        $str = Doo::app()->getModule($module,$component.'Controller', 'getComponent',$version,null,$componentParams);

        //LogClass::log_trace($str,__FILE__,__LINE__,EnumClass::LOG_TYPE_VAR_DUMP);
        //转换控件的模版函数
        $str = preg_replace_callback('/{\|\@([^ \t\r\n\(\)}]+?)\((.*?)\)\@\|}/', array( &$this, 'convertFunctionComponet'), $str);

        //LogClass::log_trace($str,__FILE__,__LINE__,EnumClass::LOG_TYPE_VAR_DUMP);
        //$str = str_replace('{|{', '{{', $str);
        $str = str_replace('{|@', '{@', $str);
        //$str = str_replace('}|}', '}}', $str);
        $str = str_replace('@|}', '@}', $str);
        $str = str_replace('{|#', '{#', $str);
        $str = str_replace('#|}', '#}', $str);
        return $str;
    }

    private function getComponentParamsValue($key,$str){
        $pattern="/\{[\S|\s]*[\'|\"]". $key ."[\'|\"]:[\'|\"](.*)[\'|\"]/";
        preg_match_all($pattern,$str,$match);
        //LogClass::log_trace($key,__FILE__,__LINE__);
        //LogClass::log_trace($match,__FILE__,__LINE__,EnumClass::LOG_TYPE_VAR_DUMP);
        if(isset($match[1][0])){
            return $match[1][0];
        }else{
            return '';
        }
    }

    private function convertInitPage($matches){
        //LogClass::log_trace($matches,__FILE__,__LINE__,EnumClass::LOG_TYPE_VAR_DUMP);
        $str = var_export (Doo::conf()->TEMPLATE_COMPONENT_VALIDATOR, TRUE);

        return $str;
    }

    private function convertSwitch($matches){
        //LogClass::log_trace($matches,__FILE__,__LINE__,EnumClass::LOG_TYPE_VAR_DUMP);
        $looplevel = sizeof(explode('\' ', $matches[0]));
        if(strpos($matches[0], "' ")!==FALSE){
            $strValue = str_repeat("' value", $looplevel-1);
            // $loopStr = "<!-- switch {$matches[1]}$strValue.";
            $loopStr = "{#switch {$matches[1]}$strValue.";
            if( strpos($matches[0], $loopStr)===0){
                $loopStr = substr($matches[0], strlen($loopStr));
                // $loopStr = str_replace(' -->', '', $loopStr);
                $loopStr = str_replace(' #}', '', $loopStr);
                $param = explode('.', $loopStr);
                $varBck ='';
                foreach($param as $pa){
                    if(strpos($pa, '@')===0){
                        $varBck .= '->' . substr($pa, 1);
                    }else{
                        $varBck .= "['$pa']";
                    }
                }
                $thislvl = $looplevel-1;
                $loopname = "\$v$thislvl$varBck";
            }else{
                $loopname = ($looplevel<2)? '$data[\''.$matches[1].'\']' : '$v'. ($looplevel-1);
            }
        }
        else if(strpos($matches[1], '.@')!==FALSE){
            $varname = str_replace('.@', '->', $matches[1]);
            $varname = explode('->', $varname);
            $firstname = $varname[0];
            array_splice($varname, 0, 1);
            $loopname =  '$data[\''.$firstname.'\']->' . implode('->', $varname) ;
        }
        else if(strpos($matches[1], '.')!==FALSE){
            $varname = explode('.',$matches[1]);
            $firstname = $varname[0];
            array_splice($varname, 0, 1);
            $loopname =  '$data[\''.$firstname .'\'][\''. implode("']['", $varname) .'\']';
        }
        else{
            $loopname = ($looplevel<2)? '$data[\''.$matches[1].'\']' : '$v'. ($looplevel-1);
        }
        return '<?php switch('.$loopname.'): switch_temp?>';
    }

    private function convertSwitchCase($matches){
        return '<?php break;case '.$matches[1].' : ?>';
    }

    private function convertInclude($matches){
        $file = $matches[1];

        /*include file is a Variable <!-- include "{{file}}" -->,
         *modify the converted string <?php echo $data['file']; ?> to $data['file']; and return it to be written to file
         * <!-- include "<?php echo $data['file']; ?>" --> after convert to var */
        $includeVarPos = strpos($file, '<?php echo $data');
        if($includeVarPos===0){
            $file = str_replace('<?php echo ', '', $file);
            $file = str_replace('; ?>', '', $file);
            $dynamicFilename = '<?php include "{'.$file.'}.php"; ?>';

            //get the real template file name from $data passed in by users
            $file = $this->data[str_replace('\']', '', str_replace('$data[\'', '', $file) )];
        }

        //if first char is '/' then load the files in view root 'view' folder, <!-- '/admin/index' --> view/admin/index.html
        if(substr($file, 0,1)=='/'){
            $file = substr($file, 1);
            //by james.ou 2011-4-1
            //$cfilename = str_replace('\\', '/', Doo::conf()->SITE_PATH) . Doo::conf()->PROTECTED_FOLDER . "viewc/$file.php";
            //$vfilename = str_replace('\\', '/', Doo::conf()->SITE_PATH) . Doo::conf()->PROTECTED_FOLDER . "view/$file.html";
            //modify by dxf 2014-09-28 10:41:59 模版的include文件中，根目录修改为站点根目录view、viewc路径，不是module中的根目录
            $cfilename = str_replace('\\', '/', $this->rootViewPath_ORI) . "viewc/$file.php";
            $vfilename = str_replace('\\', '/', $this->rootViewPath_ORI ) . "view/$file.html";
        }
        else{
            $folders = explode('/', $this->mainRenderFolder);
            $file = implode('/', array_splice($folders, 0, -1)).'/'.$file;
            //by james.ou 2011-4-1
            //$cfilename = str_replace('\\', '/', Doo::conf()->SITE_PATH) . Doo::conf()->PROTECTED_FOLDER . "viewc/$file.php";
            //$vfilename = str_replace('\\', '/', Doo::conf()->SITE_PATH) . Doo::conf()->PROTECTED_FOLDER . "view/$file.html";
            $cfilename = str_replace('\\', '/',  $this->rootViewPath) . "viewc/$file.php";
            $vfilename = str_replace('\\', '/',  $this->rootViewPath) ."view/$file.html";
        }

        if(!file_exists($vfilename)){
            echo "<span style=\"color:#ff0000\">Include view file <strong>$file.html</strong> not found,path=$vfilename</span>";
            exit;
        }else{
            if(file_exists($cfilename)){
                if(filemtime($vfilename)>filemtime($cfilename)){
                    $this->compile($file, $vfilename, $cfilename);
                }
            }else{
                $this->compile($file, $vfilename, $cfilename);
            }
        }

        if(isset ($dynamicFilename) )
            return $dynamicFilename;

          // by james.ou 2011-4-1
        /*return '<?php include Doo::conf()->SITE_PATH .  Doo::conf()->PROTECTED_FOLDER . "viewc/'.$file.'.php"; ?>';*/
        //modify by dxf 2014-09-28 10:41:59 模版的include文件中，根目录修改为站点根目录view、viewc路径，不是module中的根目录
        if(substr($file, 0,1)=='/'){
            return '<?php include $this->rootViewPath . "viewc/'.$file.'.php"; ?>';
        }else{
            return '<?php include $this->rootViewPath_ORI . "viewc/'.$file.'.php"; ?>';
        }
    }

    private function convertSet($matches) {

        $expr = str_replace('<?php echo ', '', $matches[2]);
        $expr = str_replace('; ?>', '', $expr);
        $expr = preg_replace_callback('/([a-z0-9\-_]+)[ ]*\([ ]*([^ \t\r\n}]+)\)/i', array( &$this, 'checkFuncAllowed'), $expr);

        return '<?php $data[\'' . $matches[1] . '\'] = ' . $expr . '; ?>';
    }

    private function extractObjectVariables($str) {

        $varname = '';
        $args = '';

        //剔除，控件模版添加的模版方法，避免重复处理
        if (strpos($str, '$data')!==FALSE) {
        	return $str;
        }

        if(strpos($str, '.@')!==FALSE){
            $properties = explode('.@', $str);

            if(strpos($properties[0], "' ")!==FALSE){
                $looplevel = sizeof(explode('\' ', $properties[0]));

                //if ' key found that it's a key $k1
                if(strpos($properties[0],"' key")!==FALSE || strpos($properties[0],"' k")!==FALSE){
                    $varname = '$k' . ($looplevel-1);
                }else{
                    $varname = '$v' . ($looplevel-1);

                    //remove the variable part with the ' key or  ' value
                    array_splice($properties, 0, 1);

                    //join it up as array $v1['attachment']['pdf']   from  {{upper(msgdetails' value.attachment.pdf)}}
                    $varname .= "->". implode("->", $properties);
                }
            }else{
                $objname = $properties[0];
                array_splice($properties, 0, 1);
                $varname .= "\$data['$objname']->". implode("->", $properties);
            }

        } else if(strpos($str, '.')!==FALSE){
            $properties = explode('.', $str);
            if(strpos($properties[0], "' ")!==FALSE){
                $looplevel = sizeof(explode('\' ', $properties[0]));

                //if ' key found that it's a key $k1
                if(strpos($properties[0],"' key")!==FALSE || strpos($properties[0],"' k")!==FALSE){
                    $varname = '$k' . ($looplevel-1);
                }else{
                    $varname = '$v' . ($looplevel-1);

                    //remove the variable part with the ' key or  ' value
                    array_splice($properties, 0, 1);

                    //join it up as array $v1['attachment']['pdf']   from  {{upper(msgdetails' value.attachment.pdf)}}
                    $varname .= "['". implode("']['", $properties) ."']";
                }
            }else{
                $varname .= "\$data['". implode("']['", $properties) ."']";
            }
        } else {
            //if the function found used with a key or value in a loop, then use $k1,$k2 or $v1,$v2 instead of $data
            if(strpos($str, "' ")!==FALSE){
                $looplevel = sizeof(explode('\' ', $str));

                //if ' key found that it's a key $k1
                if(strpos($str,"' key")!==FALSE || strpos($str,"' k")!==FALSE){
                    $varname = '$k' . ($looplevel-1);
                }else{
                    $varname = '$v' . ($looplevel-1);
                }
            }else{
                $varname = "\$data['".$str."']";
            }

        }

        $varname = str_replace("\$data[''", "'", $varname);
        $varname = str_replace("'']", "'", $varname);

        return $varname;
    }

    private function convertNonDataVarKey($matches) {

        $varname = '';
        //if more than 1 dots, eg. users.total.pdf
        if(strpos($matches[2], '@')!==FALSE){
            $varname = str_replace('@', '->', $matches[2]);
            $varname = str_replace('.', '', $varname);
        }
        else if(strpos($matches[2], '.')!==FALSE){
            $properties = explode('.', $matches[2]);
            $varname .= "['". implode("']['", $properties) ."']";
        }
        //only 1 dot, users.john
        else{
            $varname = "['".$matches[2]."']";
        }
        return "<?php echo \${$matches[1]}{$varname}; ?>";

    }

    private function convertVarKey($matches){
        $varname = '';
        //if more than 1 dots, eg. users.total.pdf
        if(strpos($matches[2], '@')!==FALSE){
            $varname = str_replace('@', '->', $matches[2]);
            $varname = str_replace('.', '', $varname);
        }
        else if(strpos($matches[2], '.')!==FALSE){
            $properties = explode('.', $matches[2]);
            $varname .= "['". implode("']['", $properties) ."']";
        }
        //only 1 dot, users.john
        else{
            $varname = "['".$matches[2]."']";
        }
        return "<?php echo \$data['{$matches[1]}']$varname; ?>";
    }

    private function convertVarLoop($matches){
        $looplevel = sizeof(explode('\' ', $matches[0]));

        //if ' key found that it's a key $k1
        if(strpos($matches[0],"' key")!==FALSE || strpos($matches[0],"' k")!==FALSE)
            $varname = 'k' . ($looplevel-1);
        else{
            $varname = 'v' . ($looplevel-1);
            // This lets us use $data['key'] values as element indexes
            $matches[2] = str_replace('<?php echo ', '', $matches[2]);
            $matches[2] = str_replace('; ?>', '', $matches[2]);
            //remove the first variable if the ' is found, we dunwan the loop name
            if(strpos($matches[2], "' ")!==FALSE){
                $matches[2] = explode("' ", $matches[2]);
                //array_splice($matches[2], 0, 1);
                //modofy by dxf 2010-03-02 15:52:52
                //指定相应级别循环的有效value，原仅取出第一个' value之后所有字符串
                //修复loop嵌套时，内循环如{{model.@pay_config' value.range' value}}提取key为“range' value”，应该无key
                array_splice($matches[2], 0, $looplevel-1);
                $matches[2] = "' ".implode("' ", $matches[2] );
            }

            //users' value.uname  becomes  $v1['uname']
            //users' value.posts.latest  becomes  $v1['posts']['latest']
            //users' value.@uname  becomes  $v1->uname
            //users' value.@posts.@latest  becomes  $v1->posts->latest
            if(strpos($matches[2], '.@')!==FALSE){
                $varname .= str_replace('.@', '->', $matches[2]);
                $varname = str_replace("' value",'', $varname);
                $varname = str_replace("' v",'', $varname);
            }
            else if(strpos($matches[2], '.')!==FALSE){
                $properties = explode('.', $matches[2]);
                if(sizeof($properties)===2)
                    $varname .= "['".$properties[1]."']";
                else{
                    array_splice($properties, 0, 1);
                    $varname .= "['". implode("']['", $properties) ."']";
                }
            }
        }
        return '<?php echo $'.$varname.'; ?>';
    }

	private function convertVarEx($matches){
		var_dump($matches);
		$varname = trim($matches[1]);
		return '<?php echo $'.$varname.'; ?>';
	}

    private function convertVarEx1($matches){
        var_dump($matches);
        $varname = trim($matches[1]);
        return '<?php echo $'.$varname.'; ?>';
    }

}
