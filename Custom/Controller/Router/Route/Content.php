<?php

require_once 'Zend/Controller/Router/Route/Abstract.php';

class Custom_Controller_Router_Route_Content extends Zend_Controller_Router_Route_Abstract
{

    protected $_route = null;
    protected $_defaults = array();
    protected $_cachedCategories = null;

    public function getVersion() {
        return 1;
    }

    /**
     * Instantiates route based on passed Zend_Config structure
     *
     * @param Zend_Config $config Configuration object
     */
    public static function getInstance(Zend_Config $config)
    {
        $defs = ($config->defaults instanceof Zend_Config) ? $config->defaults->toArray() : array();
        return new self($defs);
    }

    /**
     * Prepares the route for mapping.
     *
     * @param string $route Map used to match with later submitted URL path
     * @param array $defaults Defaults for map variables with keys as variable names
     */
    public function __construct($defaults = array())
    {
        $defaults = (array) $defaults;
        
        if (empty($defaults)) {
        	throw new Zend_Controller_Router_Exception('Options required');
        }
        
        if (!isset($defaults['adapter'])) {
        	throw new Zend_Controller_Router_Exception('Database adapter must be provided');
        }
        
        if (!isset($defaults['elementsTable']) || empty($defaults['elementsTable'])) {
        	throw new Zend_Controller_Router_Exception("Elements table must be provided as array with keys 'name', 'identityField', 'parentField', 'aliasField', 'titleField'");
        }
        if (!isset($defaults['categoriesTable']) || empty($defaults['categoriesTable'])) {
        	$defaults['categoriesTable'] = $defaults['elementsTable'];
        }
        $this->_defaults = $defaults;
    }

    /**
     * Matches a user submitted path with a previously defined route.
     * Assigns and returns an array of defaults on a successful match.
     *
     * @param string $path Path used to match against this routing map
     * @return array|false An array of assigned values or a false on a mismatch
     */
    public function match($path, $partial = false)
    {
        /* Извращаться здесь */
    	
       	$return = $this->_defaults;
    	$adapter = $this->_defaults['adapter'];
    	$tContents = $this->_defaults['elementsTable'];
    	$tCategories = $this->_defaults['categoriesTable'];
    	$parts = explode('/', trim($path, '/'));
    	$filter = new Zend_Filter_Word_UnderscoreToCamelCase();

    	unset($return['adapter']);
       	unset($return['elementsTable']);
       	unset($return['categoriesTable']);
    	
    	$select = $adapter->select();
    	
    	$mode = 'categories';
    	$alias = $parts[count($parts)-1];
    	
    	if (substr($parts[count($parts)-1], -4) == 'html') {
    		$mode = 'content';
    		$alias = substr($parts[count($parts)-1], 0, strlen($parts[count($parts)-1]) - 5);
    	}
    	
    	if ($mode == 'content') {
    		$select->from(array('element' => $tContents['name']));
	    	$eParentField = $tContents['parentField'];
		} else {
			$select->from(array('element' => $tCategories['name']));
    		$eParentField = $tCategories['parentField'];
		}
    	
    	unset($parts[count($parts)-1]);
    	//$parts = array_reverse($parts);
    	
    	$addFields = array();
    	if (is_array($tCategories['addFields']) && !empty($tCategories['addFields'])) {
    		$addFields = $tCategories['addFields'];
    	}
    	
    	for ($i = 0; $i < count($parts); $i ++) {
    		$k = count($parts) - (1 + $i);
    		$fields = array(
    			'clevel_' . $filter->filter($tCategories['aliasField']) . '_' . $i => 'cLevel' . $i . '.' . $tCategories['aliasField'],
			);
			
			foreach ($addFields as $f) {
				$fields['clevel_' . $filter->filter($f) . '_' . $i] = 'cLevel' . $i . '.' . $f;
			}
			
    		if ($i == 0) {
				$select->joinLeft(
					array('cLevel' . $i => $tCategories['name']),
					$adapter->quoteIdentifier('cLevel' . $i . '.' . $tCategories['identityField'])
					. ' = '
					. $adapter->quoteIdentifier('element.' . $eParentField),
					$fields
				);
			} else {
				$select->joinLeft(
					array('cLevel' . $i => $tCategories['name']),
					$adapter->quoteIdentifier('cLevel' . $i . '.' . $tCategories['identityField'])
					. ' = '
					. $adapter->quoteIdentifier('cLevel' . ($i - 1) . '.' . $tCategories['parentField']),
					$fields
				);				
			}
			$select->where($adapter->quoteIdentifier('cLevel' . $i . '.' . $tCategories['aliasField']) . ' = ?', $parts[$k]);
			$select->where($adapter->quoteIdentifier('cLevel' . $i . '.' . $tCategories['enableField']) . ' = ?', 1);
		}
    	
    	if ($mode == 'content') {
    		if (preg_match('(\d+)', $alias)) {
    			$select->where($adapter->quoteIdentifier('element.' . $tContents['identityField']) . ' = ?', $alias);
    		} else {
    			$select->where($adapter->quoteIdentifier('element.' . $tContents['aliasField']) . ' = ?', $alias);
    		}
			$select->where($adapter->quoteIdentifier('element.' . $tContents['enableField']) . ' = ?', 1);
		} else {
			$select->where($adapter->quoteIdentifier('element.' . $tCategories['aliasField']) . ' = ?', $alias);
			$select->where($adapter->quoteIdentifier('element.' . $tCategories['enableField']) . ' = ?', 1);
		}
		//throw new Exception($select, 1);
		$result = $adapter->fetchRow($select);
		$select->reset();
		
		if (!empty($result)) {
			$return['result'] = $result;
			$return['mode'] = $mode;
			return $return;
		} else {
			return false;
		}
		
    	
       	return $return;
        return false;
    }

    /**
     * Assembles a URL path defined by this route
     *
     * @param array $data An array of variable and value pairs used as parameters
     * @return string Route path with user submitted parameters
     */
    public function assemble($data = array(), $reset = false, $encode = false, $partial = false)
    {
    	if (empty($data['mode']) || empty($data['element'])) {
    		throw new Zend_Controller_Router_Exception('Mode and element array must be provided');
		}        
    	
        /* И здесь */
       	$adapter = $this->_defaults['adapter'];
    	$tContents = $this->_defaults['elementsTable'];
    	$tCategories = $this->_defaults['categoriesTable'];
    	
    	if (is_null($this->_cachedCategories)) {
	    	$select = $adapter->select();
	    	$select->from($tCategories['name'], array(
	    		$tCategories['identityField'],
	    	    $tCategories['parentField'],
	    	    $tCategories['aliasField'],
	    	    $tCategories['titleField'],
	    	    $tCategories['enableField'],
			));
			$categories = $adapter->fetchAll($select);
			$this->_cachedCategories = array();
			foreach ($categories as $c) {
				$this->_cachedCategories[$c[$tCategories['identityField']]] = $c;
			}
		}
		    	
        $mode = $data['mode'];
        $element = $data['element'];
        $parts = array();
        
        $parent = $element[$tCategories['parentField']];
        $int = 10;
        while ($parent > 0) {
        	if ($this->_cachedCategories[$parent]) {
        		$parts[] = $this->_cachedCategories[$parent][$tCategories['aliasField']];
        		$parent = $this->_cachedCategories[$parent][$tCategories['parentField']];
        	}
        }
        
        $parts = array_reverse($parts);
        
        if ($mode == 'content') {
        	if (!empty($element[$tContents['aliasField']])) {
        		$parts[] = $element[$tContents['aliasField']] . '.html';
        	} else {
        		$parts[] = $element[$tContents['identityField']] . '.html';
        	}
        } else {
        	$parts[] = $element[$tCategories['aliasField']];
        }
        
    	return implode('/', $parts);
    }

    /**
     * Return a single parameter of route's defaults
     *
     * @param string $name Array key of the parameter
     * @return string Previously set default
     */
    public function getDefault($name) {
        if (isset($this->_defaults[$name])) {
            return $this->_defaults[$name];
        }
        return null;
    }

    /**
     * Return an array of defaults
     *
     * @return array Route defaults
     */
    public function getDefaults() {
        return $this->_defaults;
    }

}
