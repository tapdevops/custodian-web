<?php
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initAuth()
    {
        //$session = new Zend_Session_Namespace('Zend_Auth');
        //$session->setExpirationSeconds(60 * 60 * 24); // = 1 day
        $this->bootstrap('session');

        $auth = Zend_Auth::getInstance();
        $auth->setStorage(new Zend_Auth_Storage_Session());
        Zend_Registry::set('auth', $auth);
    }
	
	protected function _initPlugins()
    {
        // resource types
        $loader = $this->getResourceLoader();
        $loader->addResourceTypes(array(
            'controllerplugin'  => array(
                'namespace' => 'Controller_Plugin',
                'path'      => 'controllers/plugins',
            ),
            'controllerhelper'  => array(
                'namespace' => 'Controller_Helper',
                'path'      => 'controllers/helpers',
            ),
        ));

        $this->bootstrap('FrontController');
        $frontController = $this->getResource('FrontController');

        // controller plugins
        $plugins = array(
            'Application_Controller_Plugin_Auth',
            //'Application_Controller_Plugin_AuthExpired', //utk cek expired password
            'Application_Controller_Plugin_Acl',
            'Application_Controller_Plugin_Menu'
        );
        foreach ($plugins as $key => $pluginName) {
            $plugin = new $pluginName();
            $frontController->registerPlugin($plugin);
        }

        // controller helpers
        $helpers = array(
            //'Application_Controller_Helper_LogFirebug'
        );
        foreach ($helpers as $key => $helperName) {
            $helper = new $helperName();
            Zend_Controller_Action_HelperBroker::addHelper($helper);
        }
    }

    protected function _initExtendView()
    {
        $view = $this->bootstrap('view')->getResource('view');
        // Set variable to all view
        // -- baseUrl
        $view->baseUrl = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://') .
                         $_SERVER['SERVER_NAME'] . '/' . APPLICATION_URL . '/';
        // -- jQuery
        ZendX_JQuery::enableView($view);
        $view->jQuery()->setLocalPath('js/jquery.min.js')
                       ->setUiLocalPath('js/jquery-ui.min.js')
                       ->addStyleSheet('themes/redmond/jquery-ui.css');
        $view->jQuery()->enable()
                       ->uiEnable();

        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
        $viewRenderer->setViewSuffix('tpl');
        $viewRenderer->setView($view);
    }

    protected function _initDb()
    {
		// setting untuk DATABASE LOCAL (BILA APLIKASI SITE) -- SABRINA 26/08/2014
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/application.ini', APPLICATION_ENV);
		
		try {
			$db = Zend_Db::factory($config->resources->db->adapter, array(
				'username' => $config->resources->db->params->username,
				'password' => $config->resources->db->params->password,
				'dbname'   => $config->resources->db->params->host . '/' . $config->resources->db->params->dbname,
				'profiler' => $config->resources->db->params->profiler,
				'options'  => array(Zend_Db::AUTO_QUOTE_IDENTIFIERS => false)
			));
			$db->getConnection();
		} catch (Zend_Db_Adapter_Exception $e) {
			echo $e->getMessage();
			die('Could not connect to database.');
		} catch (Zend_Exception $e) {
			echo $e->getMessage();
			die('Could not connect to database.');
		}
		Zend_Registry::set('db', $db);        
		
		// setting untuk DATABASE HO (BILA APLIKASI SITE) -- SABRINA 26/08/2014        
		try {
			$db_ho = Zend_Db::factory($config->resources->db->adapter, array(
				'username' => $config->resources->db->params->username_dbho,
				'password' => $config->resources->db->params->password_dbho,
				'dbname'   => $config->resources->db->params->host_dbho . '/' . $config->resources->db->params->dbname_dbho,
				'profiler' => $config->resources->db->params->profiler,
				'options'  => array(Zend_Db::AUTO_QUOTE_IDENTIFIERS => false)
			));
			$db_ho->getConnection();
		} catch (Zend_Db_Adapter_Exception $e) {
			echo $e->getMessage();
			die('Could not connect to database.');
		} catch (Zend_Exception $e) {
			echo $e->getMessage();
			die('Could not connect to database.');
		}
        Zend_Registry::set('db_ho', $db_ho);

        // set db_adapter to all db_table objects
        Zend_Db_Table::setDefaultAdapter($db);
    }

    //protected function _initRouter()
    //{
    //    $config = new Zend_Config_Ini(APPLICATION_PATH . '/application.ini', APPLICATION_ENV);
    //    $frontController = $this->getResource('FrontController');
    //    $router = $frontController->getRouter();
    //    $router->addConfig($config, 'routes');
    //}

    /*
     * these 2 bootstraps use to increase performance
    */
    protected function _initDbCache()
    {
        $this->bootstrap('CacheManager');
        $cache = $this->getResource('CacheManager')->getCache('database');

        // using cache for metadata of all db_table objects
        Zend_Db_Table_Abstract::setDefaultMetadataCache($cache);
    }

    protected function _initFileIncCache()
    {
        $classFileIncCacheOptions = $this->getOption('cache');
        $classFileIncCache = $classFileIncCacheOptions['classFileIncCache'];

        if(file_exists($classFileIncCache)) {
            include_once $classFileIncCache;
        }
        Zend_Loader_PluginLoader::setIncludeFileCache($classFileIncCache);
    }
}
