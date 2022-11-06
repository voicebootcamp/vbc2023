<?php
/**
 * @package JAMP::plugins::system
 * @subpackage jamp
 * @subpackage core
 * @subpackage includes
 * @author Joomla! Extensions Store
 * @copyright (C)2016 Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined ( '_JEXEC' ) or die ();
use Joomla\CMS\Uri\Uri;

/**
 * Class to manage all the stuff to render and transform AMP pages output
 * 
 * @package JAMP::plugins::system
 * @subpackage jamp
 * @subpackage core
 * @subpackage includes
 */
class JAmpImage {
	private static $lastImageInstance;
	
	public $cache = 'cache/';
	public $dirs = array (
			'logo' 
	);
	public $dir = 'images/jamp/';
	public $compression = '90';
	public $purgeLifetime = 3600;
	public $image;
	public $image_type;
	public $valid_exts = array (
			'jpg',
			'jpeg',
			'gif',
			'png' 
	);
	public $newWidth;
	public $newHeight;
	public $image_file;
	
	/**
	 * Return the last created JAmpImage instance object
	 * 
	 * @access public
	 * @static
	 * @return JAmpImage
	 */
	public static function getLastInstance() {
		return self::$lastImageInstance;
	}
	
	/**
	 * Return the image info
	 * 
	 * @access static
	 * @param string $img
	 * @return array
	 */
	public static function getImageInfo($img) {
		$image_file = JPATH_SITE . '/' . $img;
		$path = pathinfo ( $image_file );
		$ext = (isset ( $path ['extension'] )) ? $path ['extension'] : '';
		
		list( $width, $height ) = @getimagesize( $image_file );
		
		return array('extension' => $ext, 'width' => $width, 'height' => $height);
	}
	
	/*
	 * FACTORY METHOD TO LOAD, CROP AND CACHE AN IMAGE // ACCEPTS A RELATIVE IMAGE FILENAME AND RETURNS // THE CACHED IMAGE FILENAME
	 */
	public static function _($img = '', $width = 0, $height = 0, $dirs = array('page'), $crop = false, $newfileName = false, $clearCache = false) {
		$imageInstance = new \JAmpImage ($dirs);
		self::$lastImageInstance = &$imageInstance;
		
		// Avoid query strings for the image
		if (strpos($img, '?') !== false) {
			$imageFileChunks = explode('?', $img)[0];
			$img = $imageFileChunks;
		}
		
		// VALIDATE THE REQUESTED IMAGE
		if(stripos($img, JPATH_SITE) === false && stripos($img, 'http') === false) {
			$imageInstance->image_file = JPATH_SITE . '/' . $img;
		} else {
			$imageInstance->image_file = $img;
		}
		$path = pathinfo ( $imageInstance->image_file );
		$ext = (isset ( $path ['extension'] )) ? $path ['extension'] : '';
		
		$cropping = \JAmpHelper::$pluginParams->get('cropping_images', 0) || $crop;
		$filename = $newfileName ? $newfileName : $path['basename'];
		
		// IF FILE DOES NOT EXIST IN CACHE - CREATE IT
		$newfile = JPATH_SITE . '/' . $imageInstance->dir . $imageInstance->cache . $filename;
		$fileExists = file_exists ( $newfile );
		if (!$fileExists || $clearCache) {
			$writeFile = true;

			// If clear cache override ensure that the image is not already in the desired size
			if($clearCache && $fileExists) {
				list( $cacheWidth, $cacheHeight ) = @getimagesize( $newfile );
				if($cacheWidth == $width && $cacheHeight == $height) {
					// Same image already resized, do not generate again
					$writeFile = false;
				}
			}
			
			if($writeFile) {
				$imageInstance->load ( $imageInstance->image_file );
				$imageInstance->resize ( $width, $height, $cropping );
				$imageInstance->save ( $newfile );
			}
		} else {
			// Always setup the resize calculation
			list( $cacheWidth, $cacheHeight ) = @getimagesize( $newfile );
			$imageInstance->newWidth = $cacheWidth;
			$imageInstance->newHeight = $cacheHeight;
		}
		
		// RETURN THE CACHED IMAGE SRC
		$src = Uri::root ( false ) . $imageInstance->dir . $imageInstance->cache . $filename;
		return $src;
	}
	
	/*
	 * FACTORY METHOD TO LOAD, CROP AND CACHE AN IMAGE // ACCEPTS A RELATIVE IMAGE FILENAME AND RETURNS // THE CACHED IMAGE FILENAME
	*/
	public static function __($absoluteImgUrl = '', $width = 0, $height = 0, $dirs = array('page')) {
		$imageInstance = new \JAmpImage ($dirs);
		self::$lastImageInstance = &$imageInstance;
	
		// VALIDATE THE REQUESTED IMAGE
		$imageInstance->image_file = $absoluteImgUrl;
		$path = pathinfo ( $imageInstance->image_file );
		$ext = (isset ( $path ['extension'] )) ? $path ['extension'] : '';
	
		$cropping = \JAmpHelper::$pluginParams->get('cropping_images', 0);
		$filename = $path['basename'];
	
		// IF FILE DOES NOT EXIST IN CACHE - CREATE IT
		$newfile = JPATH_SITE . '/' . $imageInstance->dir . $imageInstance->cache . $filename;
		if (! file_exists ( $newfile )) {
			$imageInstance->load ( $imageInstance->image_file );
			$imageInstance->resize ( $width, $height, $cropping );
			$imageInstance->save ( $newfile );
		} else {
			// Always setup the resize calculation
			list( $cacheWidth, $cacheHeight ) = @getimagesize( $newfile );
			$imageInstance->newWidth = $cacheWidth;
			$imageInstance->newHeight = $cacheHeight;
		}
	
		// RETURN THE CACHED IMAGE SRC
		$src = Uri::root ( false ) . $imageInstance->dir . $imageInstance->cache . $filename;
		return $src;
	}
	
	private function __construct($dirs) {
		$this->purgeLifetime = \JAmpHelper::$pluginParams->get('resized_images_cache_lifetime', 60) * 60;
		
		$this->dirs = $dirs;
		$this->cache = $this->dirs[0] . '/';
		$this->checkCacheDir ();
		$this->purge ( 0 );
	}
	private function checkCacheDir() {
		if (! is_dir ( JPATH_SITE . '/' . $this->dir ))
			@mkdir ( JPATH_SITE . '/' . $this->dir );
		foreach ( $this->dirs as $d ) {
			if (! is_dir ( JPATH_SITE . '/' . $this->dir . $d ))
				@mkdir ( JPATH_SITE . '/' . $this->dir . $d );
		}
	}
	private function purge($all = 1) {
		$files = glob ( JPATH_SITE . '/' . $this->dir . $this->cache . '*', GLOB_MARK );
		if (is_array ( $files ) && count ( $files ) > 0)
			foreach ( $files as $f ) {
				if ($all === 0) {
					$time = time () - filemtime ( $f );
					if ($time < $this->purgeLifetime)
						continue;
				}
				unlink ( $f );
			}
	}
	private function load($filename) {
		$image_info = getimagesize ( $filename );
		$this->image_type = $image_info [2];
		if ($this->image_type == IMAGETYPE_JPEG) {
			$this->image = imagecreatefromjpeg ( $filename );
		} elseif ($this->image_type == IMAGETYPE_GIF) {
			$this->image = imagecreatefromgif ( $filename );
		} elseif ($this->image_type == IMAGETYPE_PNG) {
			$this->image = imagecreatefrompng ( $filename );
		} elseif ($this->image_type == IMAGETYPE_WEBP) {
			$this->image = imagecreatefromwebp ( $filename );
		}
	}
	private function save($filename, $compression = 75, $permissions = null) {
		$path = pathinfo ( $filename );
		$ext = $path ['extension'];
		switch ($ext) {
			case 'jpg' :
			case 'jpeg' :
				imagejpeg ( $this->image, $filename, $compression );
				break;
			case 'gif' :
				imagegif ( $this->image, $filename );
				break;
			case 'webp' :
				imagewebp ( $this->image, $filename );
				break;
			case 'png' :
			default :
				imagepng ( $this->image, $filename );
				break;
		}
		
		if ($permissions != null) {
			chmod ( $filename, $permissions );
		}
	}
	private function getWidth() {
		return imagesx ( $this->image );
	}
	private function getHeight() {
		return imagesy ( $this->image );
	}
	private function resizeToHeight($height) {
		$ratio = $height / $this->getHeight ();
		$width = $this->getWidth () * $ratio;
		$this->newWidth = (int)$width;
		return $width;
	}
	private function resizeToWidth($width) {
		$ratio = $width / $this->getWidth ();
		$height = $this->getheight () * $ratio;
		$this->newHeight = (int)$height;
		return $height;
	}
	private function scale($scale) {
		$width = $this->getWidth () * $scale / 100;
		$height = $this->getheight () * $scale / 100;
		$this->resize ( $width, $height );
	}
	private function hex2rgb($hex) {
		$hex = str_replace ( "#", "", $hex );
		if (strlen ( $hex ) == 3) {
			$r = hexdec ( substr ( $hex, 0, 1 ) . substr ( $hex, 0, 1 ) );
			$g = hexdec ( substr ( $hex, 1, 1 ) . substr ( $hex, 1, 1 ) );
			$b = hexdec ( substr ( $hex, 2, 1 ) . substr ( $hex, 2, 1 ) );
		} else {
			$r = hexdec ( substr ( $hex, 0, 2 ) );
			$g = hexdec ( substr ( $hex, 2, 2 ) );
			$b = hexdec ( substr ( $hex, 4, 2 ) );
		}
		$rgb = array (
				$r,
				$g,
				$b 
		);
		return $rgb;
	}
	private function resize($newwidth = 0, $newheight = 0, $cropping = 0, $background = '#FFF') {
		if ($newwidth == 0 && $newheight == 0) {
			$newheight = $this->getHeight ();
			$newwidth = $this->getWidth ();
		} elseif ($newwidth == 0)
			$newwidth = $this->resizeToHeight ( $newheight );
		elseif ($newheight == 0)
			$newheight = $this->resizeToWidth ( $newwidth );
		
		$width = $this->getWidth ();
		$height = $this->getHeight ();
		$x = 0;
		$y = 0;
		$tmp = imagecreatetruecolor ( (int)$newwidth, (int)$newheight );
		
		/*
		 * if($this->image_type == IMAGETYPE_PNG) { imagealphablending($tmp, false); imagesavealpha($tmp,true); $color = imagecolorallocatealpha($tmp, 0, 0, 0, 127); imagefill($tmp, 0, 0, $color); } else { $bgcolor = imagecolorallocate($tmp, 255, 0, 0); imagefill($tmp,0,0,$bgcolor); }
		 */
		
		imagealphablending ( $tmp, false );
		imagesavealpha ( $tmp, true );
		
		if ($background !== '') {
			$rgb = $this->hex2rgb ( $background );
			$bgcolor = imagecolorallocate ( $tmp, $rgb [0], $rgb [1], $rgb [2] );
			imagefill ( $tmp, 0, 0, $bgcolor );
		}
		
		$widthProportion = $newwidth / $width;
		$heightProportion = $newheight / $height;
		
		if ($cropping == 1) {
			if ($widthProportion > $heightProportion) {
				$savewidth = $widthProportion * $width;
				$saveheight = $widthProportion * $height;
				$y = ($height * ($heightProportion - $widthProportion)) / 2;
			} else {
				$savewidth = $heightProportion * $width;
				$saveheight = $heightProportion * $height;
				$x = ($width * ($widthProportion - $heightProportion)) / 2;
			}
		} else {
			if ($widthProportion > $heightProportion) {
				$savewidth = $heightProportion * $width;
				$saveheight = $heightProportion * $height;
				$x = ($width * ($widthProportion - $heightProportion)) / 2;
			} else {
				$savewidth = $widthProportion * $width;
				$saveheight = $widthProportion * $height;
				$y = ($height * ($heightProportion - $widthProportion)) / 2;
			}
		}
		imagecopyresampled ( $tmp, $this->image, (int)$x, (int)$y, 0, 0, (int)$savewidth, (int)$saveheight, (int)$width, (int)$height );
		$this->image = $tmp;
	}
}