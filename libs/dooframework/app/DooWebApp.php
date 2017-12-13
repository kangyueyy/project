<?php
/**
 * DooWebApp class file.
 *
 * @author Leng Sheng Hong <darkredz@gmail.com>
 * @link http://www.doophp.com/
 * @copyright Copyright &copy; 2009 Leng Sheng Hong
 * @license http://www.doophp.com/license
 */


/**
 * DooWebApp is the global context that processed user's requests.
 *
 * <p>It manages the controllers in MVC pattern, handling URI requests 404 not found, redirection, etc.</p>
 *
 * <p>This class is tightly coupled with DooUriRouter.</p>
 *
 * @author Leng Sheng Hong <darkredz@gmail.com>
 * @version $Id: DooWebApp.php 1000 2009-07-7 18:27:22
 * @package doo.app
 * @since 1.0
 */
class DooWebApp{
	/**
	 * @var array routes defined in <i>routes.conf.php</i>
	 */
	public $route;

	/**
	 * Main function to run the web application
	 */
	public function run(){
		$this->throwHeader( $this->routeToEx() );
	}

	 /**
	 * Handles the routing process.
	 * Auto routing, sub folder, subdomain, sub folder on subdomain are supported.
	 * It can be used with or without the <i>index.php</i> in the URI
	 * @return mixed HTTP status code such as 404 or URL for redirection
	 */
	public function routeTo(){
		Doo::loadCore('uri/DooUriRouter');
		$router = new DooUriRouter;
		$routeRs = $router->execute($this->route,Doo::conf()->SUBFOLDER);

		if($routeRs[0]!==null && $routeRs[1]!==null){
			//dispatch, call Controller class
			//require_once Doo::conf()->BASE_PATH ."controller/DooController.php";
			Doo::loadCore('controller/DooController');

			if($routeRs[0][0]!=='['){
				if(strpos($routeRs[0], '\\')!==false){
					$nsClassFile = str_replace('\\','/',$routeRs[0]);
					$nsClassFile = explode(Doo::conf()->APP_NAMESPACE_ID.'/', $nsClassFile, 2);
					$nsClassFile = $nsClassFile[1];
					require_once Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER . $nsClassFile .'.php';
				}else{
					require_once Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER . "controller/{$routeRs[0]}.php";
				}
			}else{
				$moduleParts = explode(']', $routeRs[0]);
				$moduleName = substr($moduleParts[0],1);

				if(isset(Doo::conf()->PROTECTED_FOLDER_ORI)===true){
					require_once Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER_ORI . 'module/'. $moduleName .'/controller/'.$moduleParts[1].'.php';
				}else{
					require_once Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER . 'module/'. $moduleName .'/controller/'.$moduleParts[1].'.php';
					Doo::conf()->PROTECTED_FOLDER_ORI = Doo::conf()->PROTECTED_FOLDER;
				}

				//set class name
				$routeRs[0] = $moduleParts[1];
				Doo::conf()->PROTECTED_FOLDER = Doo::conf()->PROTECTED_FOLDER_ORI . 'module/'.$moduleName.'/';
			}

			if(strpos($routeRs[0], '/')!==false){
				$clsname = explode('/', $routeRs[0]);
				$routeRs[0] = $clsname[ sizeof($clsname)-1 ];
			}
			//if defined class name, use the class name to create the Controller object
			$clsnameDefined = (sizeof($routeRs)===4);
			//modify by dxf 2014-10-11 16:59:36 获取url中第一个参数的企业号，如果无该参数返回0，在params中返回
			if($clsnameDefined)
				//$controller = new $routeRs[3];
				$controller = new $routeRs[3]($routeRs[2]);
			else
				//$controller = new $routeRs[0]
				$controller = new $routeRs[0]($routeRs[2]);

			$controller->params = $routeRs[2];

			if(isset($controller->params['__extension'])===true){
				$controller->extension = $controller->params['__extension'];
				unset($controller->params['__extension']);
			}
			if(isset($controller->params['__routematch'])===true){
				$controller->routematch = $controller->params['__routematch'];
				unset($controller->params['__routematch']);
			}

			if($_SERVER['REQUEST_METHOD']==='PUT')
				$controller->init_put_vars();

			//before run, normally used for ACL auth
			if($clsnameDefined){
				if($rs = $controller->beforeRun($routeRs[3], $routeRs[1])){
					return $rs;
				}
			}else{
				if($rs = $controller->beforeRun($routeRs[0], $routeRs[1])){
					return $rs;
				}
			}

			$routeRs = $controller->{$routeRs[1]}();
			$controller->afterRun($routeRs);
			return $routeRs;
		}
		//if auto route is on, then auto search Controller->method if route not defined by user
		else if(Doo::conf()->AUTOROUTE){

			list($controller_name, $method_name, $method_name_ori, $params, $moduleName )= $router->auto_connect(Doo::conf()->SUBFOLDER, (isset($this->route['autoroute_alias'])===true)?$this->route['autoroute_alias']:null );

			if(empty($this->route['autoroute_force_dash'])===false){
				if($method_name!=='index' && $method_name===$method_name_ori && ctype_lower($method_name_ori)===false){
					$this->throwHeader(404);
					return;
				}
			}

			if(isset($moduleName)===true){
				Doo::conf()->PROTECTED_FOLDER_ORI = Doo::conf()->PROTECTED_FOLDER;
				Doo::conf()->PROTECTED_FOLDER = Doo::conf()->PROTECTED_FOLDER_ORI . 'module/'.$moduleName.'/';
			}

			$controller_file = Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER . "controller/{$controller_name}.php";

			if(file_exists($controller_file)){
				require_once Doo::conf()->BASE_PATH ."controller/DooController.php";
				require_once $controller_file;

				$methodsArray = get_class_methods($controller_name);

				//if the method not in controller class, check for a namespaced class with the same file name.
				if($methodsArray===null && isset(Doo::conf()->APP_NAMESPACE_ID)===true){
					if(isset($moduleName)===true){
						$controller_name = Doo::conf()->APP_NAMESPACE_ID . '\\module\\'. $moduleName .'\\controller\\' . $controller_name;
					}else{
						$controller_name = Doo::conf()->APP_NAMESPACE_ID . '\\controller\\' . $controller_name;
					}
					$methodsArray = get_class_methods($controller_name);
				}

				//if method not found in both both controller and namespaced controller, 404 error
				if($methodsArray===null){
					if(isset(Doo::conf()->PROTECTED_FOLDER_ORI)===true)
						Doo::conf()->PROTECTED_FOLDER = Doo::conf()->PROTECTED_FOLDER_ORI;
					$this->throwHeader(404);
					return;
				}
			}
			else if(isset($moduleName)===true && isset(Doo::conf()->APP_NAMESPACE_ID)===true){
				if(isset(Doo::conf()->PROTECTED_FOLDER_ORI)===true)
					Doo::conf()->PROTECTED_FOLDER = Doo::conf()->PROTECTED_FOLDER_ORI;

				$controller_file = Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER . '/controller/'.$moduleName.'/'.$controller_name .'.php';

				if(file_exists($controller_file)===false){
					$this->throwHeader(404);
					return;
				}
				$controller_name = Doo::conf()->APP_NAMESPACE_ID .'\\controller\\'.$moduleName.'\\'.$controller_name;
				#echo 'module = '.$moduleName.'<br>';
				#echo $controller_file.'<br>';
				#echo $controller_name.'<br>';
				$methodsArray = get_class_methods($controller_name);
			}
			else{
				if(isset(Doo::conf()->PROTECTED_FOLDER_ORI)===true)
					Doo::conf()->PROTECTED_FOLDER = Doo::conf()->PROTECTED_FOLDER_ORI;
				$this->throwHeader(404);
				return;
			}

			//check for REST request as well, utilized method_GET(), method_PUT(), method_POST, method_DELETE()
			$restMethod = $method_name .'_'. strtolower($_SERVER['REQUEST_METHOD']);
			$inRestMethod = in_array($restMethod, $methodsArray);

			//check if method() and method_GET() etc. doesn't exist in the controller, 404 error
			if( in_array($method_name, $methodsArray)===false && $inRestMethod===false ){
				if(isset(Doo::conf()->PROTECTED_FOLDER_ORI)===true)
					Doo::conf()->PROTECTED_FOLDER = Doo::conf()->PROTECTED_FOLDER_ORI;
				$this->throwHeader(404);
				return;
			}

			//use method_GET() etc. if available
			if( $inRestMethod===true ){
				$method_name = $restMethod;
			}

			$controller = new $controller_name;

			//if autoroute in this controller is disabled, 404 error
			if($controller->autoroute===false){
				if(isset(Doo::conf()->PROTECTED_FOLDER_ORI)===true)
					Doo::conf()->PROTECTED_FOLDER = Doo::conf()->PROTECTED_FOLDER_ORI;
				$this->throwHeader(404);
			}

			if($params!=null)
				$controller->params = $params;

			if($_SERVER['REQUEST_METHOD']==='PUT')
				$controller->initPutVars();

			//before run, normally used for ACL auth
			if($rs = $controller->beforeRun($controller_name, $method_name)){
				return $rs;
			}

			$routeRs = $controller->$method_name();
			$controller->afterRun($routeRs);
			return $routeRs;
		}
		else{
			$this->throwHeader(404);
		}
	}

	 /**
	 * Handles the routing process.
	 * Auto routing, sub folder, subdomain, sub folder on subdomain are supported.
	 * It can be used with or without the <i>index.php</i> in the URI
	 * @return mixed HTTP status code such as 404 or URL for redirection
	 */
	public function routeToEx(){
		Doo::loadCore('uri/DooUriRouter');
		$router = new DooUriRouter;
		$routeRs = $router->execute($this->route,Doo::conf()->SUBFOLDER);

		if($routeRs[0]!==null && $routeRs[1]!==null){
			//dispatch, call Controller class
			//require_once Doo::conf()->BASE_PATH ."controller/DooController.php";
			Doo::loadCore('controller/DooController');

			if($routeRs[0][0]!=='['){
				if(strpos($routeRs[0], '\\')!==false){
					$nsClassFile = str_replace('\\','/',$routeRs[0]);
					$nsClassFile = explode(Doo::conf()->APP_NAMESPACE_ID.'/', $nsClassFile, 2);
					$nsClassFile = $nsClassFile[1];
					require_once Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER . $nsClassFile .'.php';
				}else{
					require_once Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER . "controller/{$routeRs[0]}.php";
				}
			}else{
				$moduleParts = explode(']', $routeRs[0]);
				$moduleName = substr($moduleParts[0],1);

				if(isset(Doo::conf()->PROTECTED_FOLDER_ORI)===true){
					require_once Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER_ORI . 'module/'. $moduleName .'/controller/'.$moduleParts[1].'.php';
				}else{
					require_once Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER . 'module/'. $moduleName .'/controller/'.$moduleParts[1].'.php';
					Doo::conf()->PROTECTED_FOLDER_ORI = Doo::conf()->PROTECTED_FOLDER;
				}

				//set class name
				$routeRs[0] = $moduleParts[1];
				Doo::conf()->PROTECTED_FOLDER = Doo::conf()->PROTECTED_FOLDER_ORI . 'module/'.$moduleName.'/';
			}

			if(strpos($routeRs[0], '/')!==false){
				$clsname = explode('/', $routeRs[0]);
				$routeRs[0] = $clsname[ sizeof($clsname)-1 ];
			}
			//if defined class name, use the class name to create the Controller object
			$clsnameDefined = (sizeof($routeRs)===4);
			//modify by dxf 2014-10-11 16:59:36 获取url中第一个参数的企业号，如果无该参数返回0，在params中返回
			if($clsnameDefined)
				//$controller = new $routeRs[3];
				$controller = new $routeRs[3]($routeRs[2]);
			else
				//$controller = new $routeRs[0]
				$controller = new $routeRs[0]($routeRs[2]);

			$controller->params = $routeRs[2];

			if(isset($controller->params['__extension'])===true){
				$controller->extension = $controller->params['__extension'];
				unset($controller->params['__extension']);
			}
			if(isset($controller->params['__routematch'])===true){
				$controller->routematch = $controller->params['__routematch'];
				unset($controller->params['__routematch']);
			}

			if($_SERVER['REQUEST_METHOD']==='PUT')
				$controller->init_put_vars();

			//before run, normally used for ACL auth
			if($clsnameDefined){
				if($rs = $controller->beforeRun($routeRs[3], $routeRs[1])){
					return $rs;
				}
			}else{
				if($rs = $controller->beforeRun($routeRs[0], $routeRs[1])){
					return $rs;
				}
			}

			if ($controller->pageType === EnumClass::PAGE_RET_MSG_TYPE_PAGE || $controller->pageType === EnumClass::PAGE_RET_MSG_TYPE_HTML || $controller->pageType === EnumClass::PAGE_RET_MSG_TYPE_WHOLEPAGE) {
				ob_start();
				$routeRs = $controller->{$routeRs[1]}();
				//echo $routeRs;
				$data = ob_get_contents();
				ob_end_clean();

				if ($controller->pageType === EnumClass::PAGE_RET_MSG_TYPE_PAGE || $controller->pageType === EnumClass::PAGE_RET_MSG_TYPE_HTML || $controller->pageType === EnumClass::PAGE_RET_MSG_TYPE_WHOLEPAGE) {
					return $controller->afterRunEx($routeRs,$data);
				}else{
					echo $data;
					$controller->afterRun($routeRs);
					return $routeRs;
				}
			}else{
				$routeRs = $controller->{$routeRs[1]}();
				$controller->afterRun($routeRs);
				return $routeRs;
			}
		}
		else{
			$this->throwHeader(404);
		}
	}

	/**
	 * Reroute the URI to an internal route
	 * @param string $routeuri route uri to redirect to
	 * @param bool $is404 send a 404 status in header
	 */
	public function reroute($routeuri, $is404=false){

		if(Doo::conf()->SUBFOLDER!='/')
			$_SERVER['REQUEST_URI'] = substr(Doo::conf()->SUBFOLDER, 0, strlen(Doo::conf()->SUBFOLDER)-1) . $routeuri;
		else
			$_SERVER['REQUEST_URI'] = $routeuri;

		if(isset(Doo::conf()->PROTECTED_FOLDER_ORI)===true){
			Doo::conf()->PROTECTED_FOLDER = Doo::conf()->PROTECTED_FOLDER_ORI;
			unset( Doo::conf()->PROTECTED_FOLDER_ORI );
		}

		if($is404===true)
			header('HTTP/1.1 404 Not Found');
		$this->routeTo();
	}

	/**
	 * Process a module from the main application.
	 *
	 * <p>This is similar to rerouting to a Controller. The framework offer 3 ways to process and render a module.</p>
	 *
	 * <p>Based on a predefined route:</p>
	 * <code>
	 * # The route is predefined in routes.conf.php
	 * # $route['*']['/top/:nav'] = array('MyController', 'renderTop');
	 * $data['top'] = Doo::app()->module('/top/banner');
	 * </code>
	 *
	 * <p>Based on Controller name and Action method:</p>
	 * <code>
	 * Doo::app()->module('MyController', 'renderTop');
	 *
	 * # If controller is in sub folder
	 * Doo::app()->module('folder/MyController', 'renderTop');
	 *
	 * # Passed in parameter if controller is using $this->param['var']
	 * Doo::app()->module('MyController', 'renderTop', array('nav'=>'banner'));
	 * </code>
	 *
	 * <p>If class name is different from controller filename:</p>
	 * <code>
	 * # filename is index.php, class name is Admin
	 * Doo::app()->module(array('index', 'Admin'), 'renderTop');
	 *
	 * # in a sub folder
	 * Doo::app()->module(array('admin/index', 'Admin'), 'renderTop');
	 *
	 * # with parameters
	 * Doo::app()->module(array('admin/index', 'Admin'), 'renderTop', array('nav'=>'banner'));
	 * </code>
	 *
	 * @param string|array $moduleUri URI or Controller name of the module
	 * @param string $action Action to be called
	 * @param array $params Parameters to be passed in to the Module
	 * @param boolen $fullMode 是否为完整模式，完整表示运行afterRunEx
	 * @return string Output of the module
	 */
	public function module($moduleUri, $action=null, $params=null, $moduleVersion='1.0.0.0', $prm_base=null, $fullMode = false){
		if($moduleUri[0]=='/'){
//			echo "sdfsdf";
//			var_dump($moduleUri);
			if(Doo::conf()->SUBFOLDER!='/')
				$_SERVER['REQUEST_URI'] = substr(Doo::conf()->SUBFOLDER, 0, strlen(Doo::conf()->SUBFOLDER)-1) . $moduleUri;
			else
				$_SERVER['REQUEST_URI'] = $moduleUri;

			$tmp = Doo::conf()->PROTECTED_FOLDER;
			if(isset(Doo::conf()->PROTECTED_FOLDER_ORI)===true){
				Doo::conf()->PROTECTED_FOLDER = Doo::conf()->PROTECTED_FOLDER_ORI;
				$tmpOri = Doo::conf()->PROTECTED_FOLDER_ORI;
			}

			ob_start();
			$this->routeTo();
			$data = ob_get_contents();
			ob_end_clean();

			Doo::conf()->PROTECTED_FOLDER = $tmp;

			if(isset($tmpOri)===true){
				Doo::conf()->PROTECTED_FOLDER_ORI = $tmpOri;
            }

			return $data;
		}
		//if Controller name passed in:  Doo::app()->module('admin/SomeController', 'login',  array('nav'=>'home'));
		else if(is_string($moduleUri)){
			$controller_name = $moduleUri;
			if(strpos($moduleUri, '/')!==false){
				$arr = explode('/', $moduleUri);
				$controller_name = $arr[sizeof($arr)-1];
			}
			require_once Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER . "controller/$moduleUri.php";
			$controller = new $controller_name($params);
			if ($prm_base != null) {
				$controller->prm_base = $prm_base;
			}
			$controller->params = $params;
			if($rs = $controller->beforeRun($controller_name, $action)){
				$this->throwHeader( $rs );
				return;
			}

			// ob_start();
			// $rs = $controller->{$action}();

			// if($controller->autorender===true){
			// 	Doo::conf()->AUTO_VIEW_RENDER_PATH = array(strtolower(substr($controller_name, 0, -10)), strtolower(preg_replace('/(?<=\\w)(?=[A-Z])/','-$1', $action)));
			// }
			// $controller->afterRun($rs);

			// $this->throwHeader( $rs );

			// $data = ob_get_contents();
			// ob_end_clean();

			ob_start();
			if (false === $fullMode) {
                if(!method_exists($controller, $action)){
                    //modify by james.ou 2017-9-7 
                    //如果action不存在，则直接跑到page上面处理,以便适配getModule使用场景
                    $controller->params['action'] = $action;
                    $action = 'page';
                }
                $rs = $controller->{$action}();
				if($controller->autorender===true){
					Doo::conf()->AUTO_VIEW_RENDER_PATH = array(strtolower(substr($controller_name, 0, -10)), strtolower(preg_replace('/(?<=\\w)(?=[A-Z])/','-$1', $action)));
				}
				$controller->afterRun($rs);
				$this->throwHeader( $rs );
			}else{
				if ($controller->pageType === EnumClass::PAGE_RET_MSG_TYPE_PAGE || $controller->pageType === EnumClass::PAGE_RET_MSG_TYPE_HTML || $controller->pageType === EnumClass::PAGE_RET_MSG_TYPE_WHOLEPAGE) {
					// ob_start();
                    if(!method_exists($controller, $action)){
                        //modify by james.ou 2017-9-7 
                        //如果action不存在，则直接跑到page上面处理,以便适配getModule使用场景
                        $controller->params['action'] = $action;
                        $action = 'ajax';
                    }
					$routeRs = $controller->{$action}();
					//echo $routeRs;
//					$data = ob_get_contents();
//					ob_end_clean();

					if ($controller->pageType === EnumClass::PAGE_RET_MSG_TYPE_PAGE || $controller->pageType === EnumClass::PAGE_RET_MSG_TYPE_HTML || $controller->pageType === EnumClass::PAGE_RET_MSG_TYPE_WHOLEPAGE) {
						$controller->afterRunEx($routeRs,$data);
					}else{
						echo $data;
						$controller->afterRun($routeRs);
						// $routeRs;
					}
				}else{
					$routeRs = $controller->{$action}();
					$controller->afterRun($routeRs);
					// return $routeRs;
				}
			}

			$data = ob_get_contents();
			ob_end_clean();

			return $data;
		}
		//if array passed in. For controller file name != controller class name.
		//eg. Doo::app()->module(array('admin/Admin', 'AdminController'), 'login',  array('nav'=>'home'));
		else{
			require_once Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER . "controller/{$moduleUri[0]}.php";
			$controller = new $moduleUri[1]($params);
			if ($prm_base != null) {
				$controller->prm_base = $prm_base;
			}
			$controller->params = $params;
			if($rs = $controller->beforeRun($moduleUri[1], $action)){
				$this->throwHeader( $rs );
				return;
			}

			ob_start();
			if (false === $fullMode) {
                if(!method_exists($controller, $action)){
                    //modify by james.ou 2017-9-7 
                    //如果action不存在，则直接跑到page上面处理,以便适配getModule使用场景
                    $controller->params['action'] = $action;
                    $action = 'page';
                }
				$rs = $controller->{$action}();
				if($controller->autorender===true){
					Doo::conf()->AUTO_VIEW_RENDER_PATH = array(strtolower(substr($controller_name, 0, -10)), strtolower(preg_replace('/(?<=\\w)(?=[A-Z])/','-$1', $action)));
				}
				$controller->afterRun($rs);
				$this->throwHeader( $rs );
			}else{
//				echo "module";
//				var_dump($controller);
				if ($controller->pageType === EnumClass::PAGE_RET_MSG_TYPE_PAGE || $controller->pageType === EnumClass::PAGE_RET_MSG_TYPE_HTML || $controller->pageType === EnumClass::PAGE_RET_MSG_TYPE_WHOLEPAGE) {
					// ob_start();
                    if(!method_exists($controller, $action)){
                        //modify by james.ou 2017-9-7 
                        //如果action不存在，则直接跑到page上面处理,以便适配getModule使用场景
                        $controller->params['action'] = $action;
                        $action = 'ajax';
                    }
					$routeRs = $controller->{$action}();
					//echo $routeRs;
//					$data = ob_get_contents();
//					ob_end_clean();

					if ($controller->pageType === EnumClass::PAGE_RET_MSG_TYPE_PAGE || $controller->pageType === EnumClass::PAGE_RET_MSG_TYPE_HTML || $controller->pageType === EnumClass::PAGE_RET_MSG_TYPE_WHOLEPAGE) {
						$controller->afterRunEx($routeRs,$data);
					}else{
						echo $data;
						$controller->afterRun($routeRs);
						// $routeRs;
					}
				}else{
                    if(!method_exists($controller, $action)){
                        //modify by james.ou 2017-9-7 
                        //如果action不存在，则直接跑到page上面处理,以便适配getModule使用场景
                        $controller->params['action'] = $action;
                        $action = 'page';
                    }
					$routeRs = $controller->{$action}();
					$controller->afterRun($routeRs);
					// return $routeRs;
				}
			}

			$data = ob_get_contents();
			ob_end_clean();

			return $data;
		}
	}

	/**
	 * Advanced version of DooWebApp::module(). Process a module from the main application or other modules.
	 *
	 * Module rendered using this method is located in SITE_PATH/PROTECTED_FOLDER/module
	 *
	 * @param string $moduleName Name of the module folder. To execute Controller/method in the main application, pass a null or empty string value for $moduleName.
	 * @param string|array $moduleUri URI or Controller name of the module
	 * @param string $action Action to be called
	 * @param array $params Parameters to be passed in to the Module
	 * @param boolen $fullMode 是否为完整模式，完整表示运行afterRunEx
	 * @return string Output of the module
	 */
	public function getModule($moduleName, $moduleUri, $action=null,
							  $moduleVersion='1.0.0.0', $prm_base=null, $params=null, $fullMode = false){
		if (is_array($params)) {
			$params['moduleVersion'] = $moduleVersion;
		}else if (is_null($params)){
			$params = array('moduleVersion' => $moduleVersion);
		}else{
			echo 'error';
			exit;
		}
		//General.20141127 接口方法加上api_前缀
		$action = 'api_'.$action;
		if(empty($moduleName)===false){
			if(isset(Doo::conf()->PROTECTED_FOLDER_ORI)===false){
				Doo::conf()->PROTECTED_FOLDER_ORI = $tmp = Doo::conf()->PROTECTED_FOLDER;
				Doo::conf()->PROTECTED_FOLDER = $tmp . 'module/'.$moduleName.'/';
				$result = $this->module($moduleUri, $action, $params, $moduleVersion, $prm_base, $fullMode);
				Doo::conf()->PROTECTED_FOLDER = $tmp;
			}else{
				$tmp = Doo::conf()->PROTECTED_FOLDER;
				Doo::conf()->PROTECTED_FOLDER = Doo::conf()->PROTECTED_FOLDER_ORI . 'module/'.$moduleName.'/';
				$result = $this->module($moduleUri, $action, $params, $moduleVersion, $prm_base, $fullMode);
				Doo::conf()->PROTECTED_FOLDER = $tmp;
			}
		}
		else{
			$tmp = Doo::conf()->PROTECTED_FOLDER;
			Doo::conf()->PROTECTED_FOLDER = Doo::conf()->PROTECTED_FOLDER_ORI;
			$result = $this->module($moduleUri, $action, $params, $moduleVersion, $prm_base, $fullMode);
			Doo::conf()->PROTECTED_FOLDER = $tmp;
		}
		return $result;
	}

	/**
	 * Advanced version of DooWebApp::module(). Process a module from the main application or other modules.
	 *
	 * Module rendered using this method is located in SITE_PATH/PROTECTED_FOLDER/module
	 *
	 * @param string $moduleName Name of the module folder. To execute Controller/method in the main application, pass a null or empty string value for $moduleName.
	 * @param string|array $moduleUri URI or Controller name of the module
	 * @param string $action Action to be called
	 * @param array $params Parameters to be passed in to the Module
	 * @return string Output of the module
	 */
	public function getModuleClass($moduleName, $className, $function=null, $moduleVersion='1.0', $isStatic=true, $params=null){
		// if (is_array($params)) {
		// 	$params['moduleVersion'] = $moduleVersion;
		// }else if (is_null($params)){
		// 	$params = array('moduleVersion' => $moduleVersion);
		// }else{
		// 	echo 'error';
		// 	exit;
		// }

		if(isset(Doo::conf()->PROTECTED_FOLDER_ORI)===false){
			Doo::conf()->PROTECTED_FOLDER_ORI = $tmp = Doo::conf()->PROTECTED_FOLDER;
			Doo::conf()->PROTECTED_FOLDER = $tmp . 'module/'.$moduleName.'/';
		}else{
			$tmp = Doo::conf()->PROTECTED_FOLDER;
			Doo::conf()->PROTECTED_FOLDER = Doo::conf()->PROTECTED_FOLDER_ORI . 'module/'.$moduleName.'/';
		}
		$path = Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER .'/class/';
		$result = self::loadFunctionEx($path, $className, $function, $isStatic, $moduleVersion, $prm_base, $params);
		Doo::conf()->PROTECTED_FOLDER = $tmp;
		//General.20160304 返回状态判断
		if(isset($result['ret']) && $result['ret'] == RetClass::SUCCESS){
			return $result;
		}
		return BaseClass::ret();
		//return $result;
	}

	public static function loadFunction($path, $className, $function, $isStatic, $params){
		$pure_class_name = basename($className);
		class_exists($pure_class_name, false)===True || require_once($path . "$className.php");
		if ($isStatic){
			return $pure_class_name::{$function}($params);
		}else{
			$class = new $pure_class_name;
			return $class->{$function}($params);
		}
	}

	public static function loadFunctionEx($path, $className, $function, $isStatic, $moduleVersion, $prm_base, $params){
		$pure_class_name = basename($className);
		class_exists($pure_class_name, false)===True || require_once($path . "$className.php");
		if ($isStatic){
			return $pure_class_name::{$function}($moduleVersion, $prm_base, $params);
		}else{
			$class = new $pure_class_name;
			return $class->{$function}($moduleVersion, $prm_base, $params);
		}
	}

	/**
	 * Analyze controller return value and send appropriate headers such as 404, 302, 301, redirect to internal routes.
	 *
	 * <p>It is very SEO friendly but you would need to know the basics of HTTP status code.</p>
	 * <p>Automatically handles 404, include error document or redirect to inner route
	 * to handle the error based on config <b>ERROR_404_DOCUMENT</b> and <b>ERROR_404_ROUTE</b></p>
	 * <p>Controller return value examples:</p>
	 * <code>
	 * 404                                  #send 404 header
	 * array('/internal/route', 404)        #send 404 header & redirect to an internal route
	 * 'http://www.google.com'              #redirect to URL. default 302 Found sent
	 * array('http://www.google.com',301)   #redirect to URL. forced 301 Moved Permenantly sent
	 * array('/hello/sayhi', 'internal')    #redirect internally, 200 OK
	 * </code>
	 * @param mixed $code
	 */
	public function throwHeader($code){
		if(headers_sent()){
			return;
		}
		if($code!=null){
			if(is_int($code)){
				if($code===404){
					//Controller return 404, send 404 header, include file if ERROR_404_DOCUMENT is set by user
					header('HTTP/1.1 404 Not Found');
					if(!empty(Doo::conf()->ERROR_404_DOCUMENT)){
						include Doo::conf()->SITE_PATH . Doo::conf()->ERROR_404_DOCUMENT;
					}
					//execute route to handler 404 display if ERROR_404_ROUTE is defined, the route handler shouldn't send any headers or return 404
					elseif(!empty(Doo::conf()->ERROR_404_ROUTE)){
						$this->reroute(Doo::conf()->ERROR_404_ROUTE, true);
					}
					exit;
				}
				//if not 404, just send the header code
				else{
					DooUriRouter::redirect(null,true, $code);
				}
			}
			elseif(is_string($code)){
				//Controller return the redirect location, it sends 302 Found
				DooUriRouter::redirect($code);
			}
			elseif(is_array($code) && isset($code[1])){
				//Controller return array('/some/routes/here', 'internal')
				if($code[1]=='internal'){
					$this->reroute($code[0]);
					exit;
				}
				//Controller return array('http://location.to.redirect', 301)
				elseif($code[1]===404){
					$this->reroute($code[0],true);
					exit;
				}
				// if array('http://location.to.redirect', 302), Moved Temporarily is sent before Location:
				elseif($code[1]===302){
					DooUriRouter::redirect($code[0],true, $code[1], array("HTTP/1.1 302 Moved Temporarily"));
				}
				//else redirect with the http status defined,eg. 307
				else{
					DooUriRouter::redirect($code[0],true, $code[1]);
				}
			}
		}
	}

	/**
	 * To debug variables with DooPHP's diagnostic view
	 * @param mixed $var The variable to view in diagnostics.
	 */
	public function debug($var){
		throw new DooDebugException($var);
	}

}