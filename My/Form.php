<?php

require_once 'Zend/Form.php';

class My_Form extends Zend_Form
{
	public function __construct($options = null)
	{
		
		parent::__construct();
	}
	
	public function sites($exclude = null)
	{
		$sites = array(
			'/' => '',
			'http://global.wutmarc.com' => 'WUTMARC GLOBAL',
			'http://wt.wutmarc.com' => 'WUTMARC WELDING TECHNOLOGY',
			'http://sa.wutmarc.com' => 'WUTMARC SPECIAL ALLOYS',
			'http://ss.wutmarc.com' => 'WUTMARC STAINLESS STEEL',
			'http://nfm.wutmarc.com' => 'WUTMARC NON-FERROUS METALS'
		);
		
		if(!is_null($exclude)) {
			unset($sites[$exclude]);
		}
		
		return $sites;
	}
}
	
	