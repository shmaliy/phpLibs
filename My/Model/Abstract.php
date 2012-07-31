<?php 
class My_Model_Abstract
{
	protected $_db;
	protected $_lang;
	protected $_menu = "cmsmenu";
	protected $_cacheTbl = "cache";
	protected $_content = "cmscontent";
	protected $_categories = "cmscategories";
	protected $_indexation = "indexation";
	protected $_subscript = 'subscription';
	protected $_contentFields = array(
	        	"ru" => array (
	        		"title" => "title",
	        		"introtext" => "introtext",
	        		"fulltext" => "fulltext",
				),
	        	"en" => array (
	            	"title" => "param1",
	            	"introtext" => "param2",
	            	"fulltext" => "param3",
				),
	        	"de" => array (
	            	"title" => "param4",
	                "introtext" => "param5",
	                "fulltext" => "param6",
				)
			);
	
	protected $_categoriesFields = array(
	            "ru" => array (
	             	"title" => "title",
	             	"description" => "description"
				),
	             "en" => array (
	             	"title" => "param1",
	             	"description" => "param2",
				),
	             "de" => array (
	             	"title" => "param3",
	                "description" => "param4",
				),
			);
	
	protected $_month = array(
				'1' => array(
					'ru'	=>	'января',
					'en'	=>	'January',
					'de'	=>	'Januar'
				),
				'2' => array(
					'ru'	=>	'февраля',
					'en'	=>	'February',
					'de'	=>	'Februar'
				),
				'3' => array(
					'ru'	=>	'марта',
					'en'	=>	'March',
					'de'	=>	'März'
				),
				'4' => array(
					'ru'	=>	'апреля',
					'en'	=>	'April',
					'de'	=>	'April'
				),
				'5' => array(
					'ru'	=>	'мая',
					'en'	=>	'May',
					'de'	=>	'Mai'
				),
				'6' => array(
					'ru'	=>	'июня',
					'en'	=>	'June',
					'de'	=>	'Juni'
				),
				'7' => array(
					'ru'	=>	'июля',
					'en'	=>	'July',
					'de'	=>	'Juli'
				),
				'8' => array(
					'ru'	=>	'августа',
					'en'	=>	'August',
					'de'	=>	'August'
				),
				'9' => array(
					'ru'	=>	'сентября',
					'en'	=>	'September',
					'de'	=>	'September'
				),
				'10' => array(
					'ru'	=>	'октября',
					'en'	=>	'October',
					'de'	=>	'Oktober'
				),
				'11' => array(
					'ru'	=>	'ноября',
					'en'	=>	'November',
					'de'	=>	'November'
				),
				'12' => array(
					'ru'	=>	'декабря',
					'en'	=>	'December',
					'de'	=>	'Dezember'
				)
			);
	
	protected $_interfaceWords = array(
		"OUR_PRODUCTION" => array(
			"ru" => 'Наша продукция',
			"en" => 'Our production',
			"de" => 'Unserer Produkte'
		)
	);
	
	protected $_menuLangTitlePosition = array('ru', 'en', 'de');
	protected $_cache;
	
	public $helper;
    
    public function __construct()
    {
    	$this->_db = Zend_Registry::get('db');
    	$this->_cache = Zend_Registry::get('cache');
    	$this->_lang = Zend_Registry::get('lang');
    	$this->helper = new myHelpers();
    }
    
    /**
     * 
     * Returns name of current language
     * @return string
     */
    public function getLang()
    {
    	return $this->_lang;
    }
    
    /**
    *
    * Returns name of menu table
    * @return string
    */
    public function getMenu()
    {
    	return $this->_menu;
    }
        
    /**
     * 
     * Returns name of content table
     * @return string
     */
    public function getContent()
    {
    	return $this->_content;
    }
        
    /**
    *
    * Returns name of categories table
    * @return string
    */
    public function getCategories()
    {
    	return $this->_categories;
    }
    
    /**
    *
    * Returns name of indexation table
    * @return string
    */
    public function getIndexation()
    {
    	return $this->_indexation;
    }
        
    /**
    *
    * Returns name of subscription table
    * @return string
    */
    public function getSubscript()
    {
    	return $this->_subscript;
    }
        
    /**
    *
    * Returns name of content title field by current language
    * @return string
    */
    public function getContentTitle()
    {
    	return $this->_contentFields[$this->_lang]['title'];
    }
        
    /**
    *
    * Returns name of content introtext field by current language
    * @return string
    */
    public function getContentIntrotext()
    {
    	return $this->_contentFields[$this->_lang]['introtext'];
    }
        
    /**
    *
    * Returns name of content fulltext field by current language
    * @return string
    */
    public function getContentFulltext()
    {
    	return $this->_contentFields[$this->_lang]['fulltext'];
    }
        
    /**
    *
    * Returns name of categories title field by current language
    * @return string
    */
    public function getCategoriesTitle()
    {
    	return $this->_categoriesFields[$this->_lang]['title'];
    }
    
    /**
    *
    * Returns name of categories description field by current language
    * @return string
    */
    public function getCategoriesDescription()
    {
    	return $this->_categoriesFields[$this->_lang]['description'];
    }
    
    /**
     * 
     * Returns title of the month by current language
     * @param string $timestamp
     * @return string
     */
    public function getMonthName($timestamp)
    {
    	return $this->_month[date("n", $timestamp)][$this->_lang];
    }
    
    /**
     * 
     * Returns position part of menu item title 
     */
    public function getMenuTitlePosition()
    {
    	return array_search($this->_lang, $this->_menuLangTitlePosition);
    }
    
    /**
     * 
     * Enter description here ...
     * @param string $title
     * @return string
     */
    public function getCurrentMenuItemTitle($title)
    {
    	$title = explode('|', trim($title, '|'));
    	return $title[$this->getMenuTitlePosition()];
    }
    
    /**
    *
    * Returns cache mode
    * @return srting
    */
    public function getCache()
    {
    	return $this->_cache;
    }
    
    /**
    *
    * Returns cache tbl name
    * @return srting
    */
    public function getCacheTbl()
    {
    	return $this->_cacheTbl;
    }

    /**
     * 
     * Enabling cache
     * @return string
     */
    public function enableCache()
    {
    	if(file_put_contents('cache.ini', 'cache = "true"'))
    	{
    		return 'success';
    	} else {
    		return 'error';
    	}
    }
    
    /**
     * 
     * Disabling cache
     * @return string
     */
    public function disableCache()
    {
    	if(file_put_contents('cache.ini', 'cache = "false"'))
    	{
    		return 'success';
    	} else {
    		return 'error';
    	}
    }
    
    /**
     * 
     * Remove all rows in cache
     * @return string
     */    
    public function clearCache()
    {
    	$this->_db->delete($this->_cacheTbl);
    	return 'success';
    }
    
    /**
     * 
     * Adding row to cache
     * @param string $alias
     * @param array $content
     */
    public function setCacheEntry($alias = null, $content = null)
    {
    	if (is_null($alias) || is_null($content)) {
    		return 0;
    	}
    	
    	$insert = array(
    		'alias' => $alias,
    		'content' => serialize($content),
    		'created' => time()
    	);
    	
    	return $this->_insert($this->_cacheTbl, $insert);
    }
    
    /**
     * 
     * Returns array from cache
     * @param unknown_type $alias
     * @param unknown_type $age
     * @return multitype:
     */
    public function getCacheEntry($alias = null, $age = null)
    {
    	if (is_null($alias) && is_null($age)) {
    		return array();
    	}
    	
    	$select = $this->_db->select();
    	$select->from(array("cache" => $this->_cacheTbl));
    	$select->where("cache.created >= ?", time() - $age*60);
    	
    	$item = $this->_db->fetchRow($select);
    	
    	if(!empty($item)) {
    		return unserialize($item['content']);
    	} else {
    		return array();
    	}
    }
    
    /**
     * Returns current interface translation
     * @param string $alias
     */
    public function getInterfaceWord($alias)
    {
    	return $this->_interfaceWord[$alias][$this->_lang];
    }
    
    /**
	 * 
	 * Returns content item by alias
	 * @param string $alias
	 * @return string
	 */
    protected function _getContentItemByAlias($alias)
    {
    	$select = $this->_db->select();
    	$select->from(
    		array("content" => $this->_content),
    		array(
    			'id',
    			'parent_id',
    			'title' => $this->getContentTitle(),
    			'title_alias',
    			'introtext' => $this->getContentIntrotext(),
    			'fulltext' => $this->getContentFulltext(),
    			'image',
    			'images',
    			'hits',
    			'created',
    			'publish_up',
    			'publish_down',
    			'ordering'
    		)
    	);
    	$select->where("content.title_alias = ?", $alias);
    	$select->where("content.published = 1");
    	$item = $this->_db->fetchRow($select);
    	$this->_setContentHits($item['id']);
    	
    	return $item;
    }
    
    /**
     * 
     * Returns content item by id
     * @param integer $id
     * @param integer $setContentHits must be 0 or 1, default 1
     * @return array
     */
    protected function _getContentItemById($id, $setContentHits = 1)
    {
    	$select = $this->_db->select();
    	$select->from(
    		array("content" => $this->_content),
    		array(
    	    	'id',
    	    	'parent_id',
    	    	'title' => $this->getContentTitle(),
    	    	'title_alias',
    	    	'introtext' => $this->getContentIntrotext(),
    	    	'fulltext' => $this->getContentFulltext(),
    	    	'image',
    	    	'images',
    	    	'hits',
    	    	'created',
    	    	'publish_up',
    	    	'publish_down',
    	    	'ordering'
    		)
    	);
    	$select->where("content.id = ?", $id);
    	$select->where("content.published = 1");
    	$item = $this->_db->fetchRow($select);
    	
    	if ($setContentHits == 1) {
    		$this->_setContentHits($item['id']);
    	}
    	
    	return $item;
    }
    
    /**
     * 
     * Returns all menu items
     */
    protected function _getMenuItems($order = 'asc')
    {
    	$select = $this->_db->select();
    	$select->from(
    		array('menu' => $this->_menu)
    	);
    	
    	$select->where('menu.published = 1');
    	$select->order('menu.ordering ' . $order);
    	return $this->_db->fetchAll($select);
    } 
    
    /**
     * 
     * Builds tree of menu items
     * @param array $items
     * @param integer $parent
     * @return array
     */
    protected function _getMenuTree($items, $parent = 0)
    {
    	$tree = array();
    	//$hasCurrent = false;
    	foreach ($items as &$item) {
    		if($item['parent_id'] == $parent) {
    			$item['title'] = $this->getCurrentMenuItemTitle($item['title']);
    			$item['link'] = str_replace(':lang:', $this->_lang, $item['link']);
    			
    			$item['childs'] = $this->_getMenuTree($items, $item['id']);
    			
    			$item['current'] = $this->_checkCurrentMenuItem($item['link']);
    			//$hasCurrent = true;
    			$tree[] = $item;
    		} 
    	}
    	return $tree;
    }
    
    protected function _getMenuItemIdByAlias($alias)
    {
    	$select = $this->_db->select();
    	$select->from(
    	array('menu' => $this->_menu)
    	);
    	 
    	$select->where('menu.published = 1');
    	$select->where('menu.title_alias = ?', $alias);
    	$item =  $this->_db->fetchRow($select);
    	
    	return $item['id'];
    }    
    /**
     * 
     * Checks current menu position
     * @param string $link
     * @return integer
     */    
    protected function _checkCurrentMenuItem($link)
    {
    	if($link == $_SERVER["REQUEST_URI"]){
    		return 1;
    	}
    	return 0;
    }
    
    /**
     * 
     * Returns all categories items
     */
    protected function _getCategoriesItems($order = 'asc')
    {
    	$select = $this->_db->select();
    	$select->from(
    		array('categories' => $this->_categories)
    	);
    	 
    	$select->where('categories.published = 1');
    	$select->order('categories.ordering ' . $order);
    	return $this->_db->fetchAll($select);
    }
    
    /**
     * 
     * Builds tree of categories
     * @param array $items
     * @param integer $parent
     * @return array
     */
    protected function _getCategoriesTree($items, $parent = 0)
    {
    	$tree = array();
    	foreach ($items as &$item) {
    		if($item['parent_id'] == $parent) {
    			$item['title'] = $item[$this->getCategoriesTitle()];
    			$item['description'] = $item[$this->getCategoriesDescription()];
    			$item['childs'] = $this->_getCategoriesTree($items, $item['id']);
    			$tree[] = $item;
    		}
    	}
    	return $tree;
    }
    
    /**
     * Returns root category id
     * @param string $alias
     * @return integer
     */
    protected function _getRootCategoryIdByAlias($alias)
    {
    	$list = $this->_getCategoriesItems();
    	
    	foreach ($list as $item) {
    		if ($item['title_alias'] == $alias && $item['parent_id'] == 0) {
    			return $item['id'];
    		}
    	}
    }
    
    /**
     * 
     * Update hits count to content
     * @param integer $id
     */
    protected function _setContentHits($id)
    {
    	$hitsNS = new Zend_Session_Namespace('hits');
    	
    	if (!isset($hitsNS->updated)) {
    		$hitsNS->updated = 1;
	    	$item = $this->_getContentItemById($id, 0);
	    	$update = array(
	    		'hits' => new Zend_Db_Expr('hits+1')
	    	);
	    	$this->_update($item['id'], $this->_content, $update);
    	}
    }
    
    /**
     * 
     * Inserts row in table
     * @param string $tbl
     * @param array $array
     * @return integer
     */
    protected function _insert($tbl, $array)
    {
    	$this->_db->insert($tbl, $array);
    	return $this->_db->lastInsertId();
    }
    
    /**
     * 
     * Updates row in tables
     * @param integer $id
     * @param string $tbl
     * @param array $array
     */
    protected function _update($id, $tbl, $array)
    {
    	$this->_db->update($tbl, $array, 'id = ' . $id);
    }
    
    /**
     * 
     * Remove row from table
     * @param integer $id
     * @param string $tbl
     */
    protected function _delete($id, $tbl)
    {
    	$this->_db->delete($tbl, 'id = ' . $id);
    }
}