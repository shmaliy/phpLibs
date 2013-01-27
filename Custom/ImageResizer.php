<?php


class Custom_ImageResizer
{
	const TYPE_JPG = 'jpg';
	const TYPE_GIF = 'gif';
	const TYPE_PNG = 'png';
	
	const FIT_HEIGHT = 'height';
	const FIT_WIDTH  = 'width';
	const FIT_BOX    = 'box';
	
	const CROP_TOP    = 'top';
	const CROP_BOTTOM = 'bottom';
	const CROP_LEFT   = 'left';
	const CROP_RIGHT  = 'right';
	const CROP_CENTER = 'center';
	
	public $_supressErrors = false;
	
	public $types = array(
        self::TYPE_JPG => self::TYPE_JPG,
        self::TYPE_GIF => self::TYPE_GIF,
        self::TYPE_PNG => self::TYPE_PNG
	);
	
	public function __construct($supressErrors = false)
	{
		$this->_supressErrors = (bool) $supressErrors;
	}
	
	public function getType($filename)
	{
		$extension = strtolower(substr($filename, -3));
		
		if (in_array($extension, $this->types)) {
			return $this->types[$extension];
		}
		
		if ($this->_supressErrors) {
			throw new Zend_Exception('Unknown file type');
		}
		return false;
	}
    
    public function readImage($filename)
    {
        $type = $this->getType($filename);
        
        switch ($type) {
	        case self::TYPE_JPG:
		        return imagecreatefromjpeg($filename);
		        break;
	        case self::TYPE_GIF:
		        return imagecreatefromgif($filename);
		        break;
	        case self::TYPE_PNG:
		        return imagecreatefrompng($filename);
		        break;
	        default:
	        	return false;
		        break;
        }
    }
    
    public function writeImage($imageID, $type = NULL, $filename = NULL, $quality = NULL)
    {
    	if (!$imageID) {
    		if ($this->_supressErrors) {
    			//throw new Exception('Cannot write image');
    		}
    		return false;
    	}
    	
    	if ($type === NULL) {
    		if ($filename !== NULL) {
	        	$type = $this->getType($filename);
    		} else {
    			$type = self::TYPE_JPG;
    		}
        }
        
        /* between 0 and 100 */
        if ($quality !== NULL && is_numeric($quality) && ($type == self::TYPE_JPG || $type == self::TYPE_PNG)) {
        	$quality = abs((int) $quality);
        	$quality = $quality > 100 ? 100 : $quality;
        	
        	if ($type == self::TYPE_PNG) {
        		$quality = round(($quality * 0.09));
        	}
        } else if ($type == self::TYPE_JPG) {
        	$quality = 80;
        } else if ($type == self::TYPE_PNG) {
        	$quality = 7;
        }
        
        if ($filename === NULL) {
        	switch ($type) {
		        case self::TYPE_JPG:
			        header("Content-type: image/jpeg");
			        break;
		        case self::TYPE_GIF:
			        header("Content-type: image/gif");
			        break;
		        case self::TYPE_PNG:
			        header("Content-type: image/png");
			        break;
		        default:
			        break;
        	}
        } else {
        	$path = explode('/', $filename);
        	unset($path[count($path) - 1]);
        	$path = implode('/', $path);
        	if (!file_exists($path)) {
        		mkdir($path, 0777, true);
        	}
        }
        
		switch ($type) {
	        case self::TYPE_JPG:
		        imagejpeg($imageID, $filename, $quality); // 0-100
		        break;
	        case self::TYPE_GIF:
		        imagegif($imageID, $filename);
		        break;
	        case self::TYPE_PNG:
		        imagepng($imageID, $filename, $quality); // 0-9
		        break;
	        default:
		        break;
        }
        imagedestroy($imageID);
        return true;
    }
    
    public function resize($src_image, $width = NULL, $height = NULL, $fit = NULL, $cropX = NULL, $cropY = NULL, $transparencyFlag = false)
    {
        if (!$src_image) {
        	if ($this->_supressErrors) {
        		
        	}
        	return false;
        }
        
        /* if width and height not specified return original */
    	if ($width === NULL && $height === NULL) {
	        return $src_image;
        }
        
        /* override invalid fit value */
        if ($fit !== NULL && $fit != self::FIT_BOX && $fit != self::FIT_HEIGHT && $fit != self::FIT_WIDTH) {
        	$fit = NULL;
        }
        
        $origW = $newW = imagesx($src_image);
        $origH = $newH = imagesy($src_image);
        $origX = $origY = $newX = $newY = 0;
        $origAR = $origH / $origW;
        
        /* if height only was specified */
        if ($width === NULL && $height !== NULL) {
        	$width = round($height / $origH * $origW);
	        if ($origH >= $height || $fit == self::FIT_HEIGHT) {
		        $newH = $height;
		        $newW = round($newH / $origH * $origW);
	        }
        }
        
        /* if width only was specified */
        if ($width !== NULL && $height === NULL) {
        	$height = round($width / $origW * $origH);
	        if ($origW >= $width || $fit == self::FIT_WIDTH) {
		        $newW = $width;
		        $newH = round($newW / $origW * $origH);
	        }
        }
        
        /* if width and height specified */
        if ($width !== NULL && $height !== NULL) {
	        /* if the image fits and $fit is null, return original */
	        if ($origW < $width && $origH < $height && $fit === NULL) {
		        return $src_image;
	        }
	        
	        /* override invalid fit to box value */
	        if ($fit !== NULL && $fit != self::FIT_BOX) {
	        	$fit = NULL;
	        }
	        
	        $newAR = $height / $width;
	        /* if cropping not set */
	        if ($cropX === NULL && $cropY === NULL) {
	        	if ($fit == self::FIT_BOX) {
			        $newH = $height;
			        $newW = $width;	        		
	        	} else if ($origAR > $newAR) {
	        		/* result higher than box fit height */
			        $newH = $height;
			        $newW = round($newH / $origH * $origW);	        		
	        	} else {
	        		/* result wider than box fit width */
			        $newW = $width;
			        $newH = round($newW / $origW * $origH);
	        	}
	        	
	        	$newX = round(($width - $newW) / 2);
	        	$newY = round(($height - $newH) / 2);
	        }

	        if ($origAR > $newAR && $cropY !== NULL) {
			    $newW = $width;
			    $newH = round($newW / $origW * $origH);
	        }
	        
	        if ($origAR <= $newAR && $cropX !== NULL) {
			    $newH = $height;
			    $newW = round($newH / $origH * $origW);	        		
	        }
	        
	        if ($cropX !== NULL) {
	        	switch ($cropX) {
	        		case self::CROP_LEFT:
	        			break;
	        		case self::CROP_RIGHT:
	        			$origX = $newW - $width;
	        			break;
	        		case self::CROP_CENTER:
	        			$origX = round(($newW - $width) / 2);
	        			break;
	        		default:
	        			$origX = (int) $cropX;
	        			break;
	        	}
	        }
	        
	        if ($cropY !== NULL) {
	        	switch ($cropY) {
	        		case self::CROP_TOP:
	        			break;
	        		case self::CROP_BOTTOM:
	        			$origY = $newH - $height;
	        			break;
	        		case self::CROP_CENTER:
	        			$origY = round(($newH - $height) / 2);
	        			break;
	        		default:
	        			$origY = (int) $cropY;
	        			break;
	        	}
	        }
        }
        
        $dst_image = imagecreatetruecolor($width, $height);
    	
    	if ($transparencyFlag) {
	        $TIndex = imagecolortransparent($src_image);
	        $TColor = array('red' => 255, 'green' => 255, 'blue' => 255);
	        
	        if ($TIndex >= 0) { $TColor = imagecolorsforindex($image_source, $TIndex); }
	        
	        $TIndex = imagecolorallocate($dst_image, $TColor['red'], $TColor['green'], $TColor['blue']);
	        imagefill($dst_image, 0, 0, $TIndex);
	        imagecolortransparent($dst_image, $TIndex);
        }

	    imagecopyresampled(
    		$dst_image, 
    		$src_image, 
    		$newX, 
    		$newY, 
    		$origX, 
    		$origY, 
    		$newW, 
    		$newH, 
    		$origW, 
    		$origH
    	);
    	
    	return $dst_image;
    }
	
}