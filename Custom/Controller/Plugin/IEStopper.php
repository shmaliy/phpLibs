<?php

class Custom_Controller_Plugin_IEStopper extends Zend_Controller_Plugin_Abstract
{
    protected $_errorModule;
    
    protected $_errorController = 'error';
    
    protected $_errorAction = 'ieerror';
    
    protected $_errorVersion;
    
    public function __construct(Array $options = array())
    {
    	$this->setErrorHandler($options);    	
    }
    
    public function setErrorHandler(Array $options = array())
    {
        if (isset($options['module'])) {
            $this->setErrorHandlerModule($options['module']);
        }
        if (isset($options['controller'])) {
            $this->setErrorHandlerController($options['controller']);
        }
        if (isset($options['action'])) {
            $this->setErrorHandlerAction($options['action']);
        }
        if (isset($options['ieversion'])) {
        	$this->setErrorHandlerVersion($options['ieversion']);
        }
        
        return $this;
    }
        
    public function setErrorHandlerModule($module)
    {
        $this->_errorModule = (string) $module;
        return $this;
    }

    public function getErrorHandlerModule()
    {
        if (null === $this->_errorModule) {
            $this->_errorModule = Zend_Controller_Front::getInstance()->getDispatcher()->getDefaultModule();
        }
        return $this->_errorModule;
    }

    public function setErrorHandlerController($controller)
    {
        $this->_errorController = (string) $controller;
        return $this;
    }

    public function getErrorHandlerController()
    {
        return $this->_errorController;
    }

    public function setErrorHandlerAction($action)
    {
        $this->_errorAction = (string) $action;
        return $this;
    }

    public function getErrorHandlerAction()
    {
        return $this->_errorAction;
    }
    
    public function setErrorHandlerVersion($version)
    {
        $this->_errorVersion = (int) $version;
        return $this;
    }
    
    public function getErrorHandlerVersion()
    {
    	return $this->_errorVersion;
    }
    
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $frontController = Zend_Controller_Front::getInstance();
        if ($frontController->getParam('noErrorHandler')) {
            return;
        }

        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        
        // be sure that is IE
        $position = stripos($userAgent, 'MSIE');
        if ($position === false) {        	
        	return; // if not IE
        }
        
        // get ie version
        $start = stripos($userAgent, ' ', $position);
        $end = stripos($userAgent, ' ', $position + 6);
        $version = substr($userAgent, $start, $end - $start);
        
        // cleanup version
        $version = (int) floor(trim(trim($version, ' '), ';'));
        if ($this->getErrorHandlerVersion() > $version) {
        	//echo $this->_errorVersion . ' > ' . $version;
		} else {
			//echo $this->getErrorHandlerVersion() . ' <= ' . $version;
		}
        if ($this->getErrorHandlerVersion() > $version) {
        	//return false; // current version above minimal
	        $request->setParam('errorVersion', $version);
	        $request->setParam('errorNeeded', $this->getErrorHandlerVersion());        
	        $request->setModuleName($this->getErrorHandlerModule());
	        $request->setControllerName($this->getErrorHandlerController());
	        $request->setActionName($this->getErrorHandlerAction());
	        //$request->setDispatched(false);
        }                
    }
}

// usage
//$front = Zend_Controller_Front::getInstance();
//$front->registerPlugin(new MyPlugin());
