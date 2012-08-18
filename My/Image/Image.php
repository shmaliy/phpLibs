<?php

/**
 * Данный класс создает иконки изображений и кеширует их
 * TODO сделать методы для уменьшения изображения с обрезкой 
 * @author Максим
 *
 */

class My_Image_Image
{
	private $_cacheDirName;
	private $_cachePath;
	private $_image;
	private $_image_type;
	private $_filename;
	private $_compression = 100;
	private $_registeredMimes;
	private $_imagesy;
	private $_imagesx;
	
	public function __construct()
	{
		$this->_registeredMimes = array(
				'jpeg'	=> 'image/jpeg',
				'png'	=> 'image/png',
				'gif'	=> 'image/gif',
		);
	}
   	
	public function setCacheDirName($dir = null)
	{
		$this->_cacheDirName = $dir;
		//TODO
		if (is_null($this->_cacheDirName)) {
			$this->_cachePath = $this->_filename;
		} else {
			$parts = explode('/', $this->_filename);
			$parts[count($parts)] = end($parts);
			$parts[count($parts)-2] = $this->_cacheDirName;
			
			$dir = $parts;
			unset ($dir[count($dir)-1]); 
			$dir = implode('/', $dir);
			
			if (!is_dir($dir)) {
				mkdir($dir, 0777);
			}
			
			$this->_cachePath = implode('/', $parts);
		}
	}
		
	
	public function setImage($filename, $dir = null, $compression = 100)
	{
		$this->_filename = trim($filename, '/');  	
		$this->_compression = $compression;
		$this->_cacheDirName = $this->setCacheDirName($dir);
		
		$image_info = getimagesize($this->_filename);
		$this->_image_type = $image_info[mime];
		
		if ($this->_image_type == $this->_registeredMimes['jpeg']) {
			$this->_image = imagecreatefromjpeg($this->_filename);
		} elseif ($this->_image_type == $this->_registeredMimes['gif']) {
			$this->_image = imagecreatefromgif($this->_filename);
		} elseif ($this->_image_type == $this->_registeredMimes['png']) {
			$this->_image = imagecreatefrompng($this->_filename);
		}
		
		$this->getWidth();
		$this->getHeight();
		
		return $this;
	}
   
	public function save() 
	{
 		if (!is_file($this->_cachePath)) {
				
			if ($this->_image_type == $this->_registeredMimes['jpeg']) {
				imagejpeg($this->_image, $this->_cachePath, $this->_compression);
	
			} elseif ($this->_image_type == $this->_registeredMimes['gif']) {
	 
				imagegif($this->_image, $this->_cachePath);
			} elseif ($this->_image_type == $this->_registeredMimes['png']) {
	 
				imagepng($this->_image, $this->_cachePath, $this->_compression/100);
			}
		}
		//return $this;
		
	}
   
	private function getWidth() 
	{
 		$this->_imagesx = imagesx($this->_image);
		return $this;
	}
   
	private function getHeight() 
	{
		$this->_imagesy = imagesy($this->_image);
		return $this;
	}
   
	public function resizeToHeight ($height) 
	{
		$ratio = $height / $this->_imagesy;
		$width = $this->_imagesx * $ratio;
		return $this->resize($width, $height);
	}
 
	public function resizeToWidth ($width) 
	{
		$ratio = $width / $this->_imagesx;
		$height = $this->_imagesy * $ratio;
		return $this->resize($width, $height);
		
	}
 
	public function scale($width) 
	{
		$width_ = $this->_imagesx * $width/100;
		$height_ = $this->_imagesy * $width/100;
		return $this->resize($width_, $height_);
		
	}
 
	public function resize($width, $height) 
	{
		if (!is_file($this->_cachePath)) {
			$new_image = imagecreatetruecolor($width, $height);
			imagecopyresampled($new_image, $this->_image, 0, 0, 0, 0, $width, $height, $this->_imagesx, $this->_imagesy);
			$this->_image = $new_image;
			$this->save();
		}
		return $this->_cachePath;

   }  
}
