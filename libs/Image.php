<?php

class Image
{
	// private $resizeFilter = FILTER_CATROM;

	/**
	 * makeDisplayImgs
	 */
	public static function makeDisplayImgs($sourceImg, $sm_destImg, $lg_destImg)
	{
		self::fitImage($sourceImg, $sm_destImg, SmIMAGE, SmIMAGE);
		self::fitImage($sourceImg, $lg_destImg, LgIMAGE, LgIMAGE);
	}

	/**
	 * makeThumbnail
	 */
	public static function makeThumbnail($sourceImg, $destImg)
	{
		$image = new Imagick($sourceImg);
		$image->resizeImage(THUMBSIZE * 2, THUMBSIZE * 2, Imagick::FILTER_CATROM, 1, TRUE);
		$w_orig = $image->getImageWidth();
		$h_orig = $image->getImageHeight();
		$x = ($w_orig - THUMBSIZE) / 2;
		$y = ($h_orig - THUMBSIZE) / 2;
		$image->cropImage(THUMBSIZE, THUMBSIZE, $x, $y);
		// Set to use jpeg compression
		$image->setImageCompression(Imagick::COMPRESSION_JPEG);
		// Set compression level (1 lowest quality, 100 highest quality)
		$image->setImageCompressionQuality(80);
		// Writes resultant image to output directory
		$image->writeImage($destImg);
		// Destroys Imagick object
		$image->destroy();
	}

	/**
	 * makeCover - For shortcuts/gallery covers
	 */
	public static function makeCover($sourceImg, $destImg)
	{
		$image = new Imagick($sourceImg);
		$w_orig = $image->getImageWidth();
		$h_orig = $image->getImageHeight();
		$w_new = SmIMAGE;
		$h_new = SmIMAGE * COVERASPECT;
		$ratio_orig = $h_orig / $w_orig;

		if($ratio_orig == COVERASPECT) {
			// Only resize
			$image->resizeImage($w_new, $h_new, Imagick::FILTER_CATROM, 1, TRUE);
		} else {
			if($ratio_orig >= COVERASPECT) {
				// Taller than target
				$w_temp = $w_new;
				$h_temp = $w_new * $ratio_orig;
				$w_center = 0;
				$h_center = ($h_temp - $h_new) / 2;
			} else {
				// Wider than target
				$w_temp = $h_new / $ratio_orig;
				$h_temp = $h_new;
				$w_center = ($w_temp - $w_new) / 2;
				$h_center = 0;
			}
			$image->resizeImage($w_temp, $h_temp, Imagick::FILTER_CATROM, 1, TRUE);
			$image->cropImage($w_new, $h_new, $w_center, $h_center);
		}

		$image->setImageCompression(Imagick::COMPRESSION_JPEG);
		$image->setImageCompressionQuality(80);
		$image->writeImage($destImg);
		$image->destroy();
	}

	/**
	 * fitImage 
	 * @param string $sourceImg 	The path to the image to fit
	 * @param string $destImg 		The path where the resized image will be saved
	 * @param int $maxW 				Max width
	 * @param int $maxH 				Max height
	 * 
	 */
	public static function fitImage($sourceImg, $destImg, $maxW, $maxH)
	{
		$image = new Imagick($sourceImg);

		$image->resizeImage($maxW, $maxH, Imagick::FILTER_CATROM, 1, TRUE);

		// Set to use jpeg compression
		$image->setImageCompression(Imagick::COMPRESSION_JPEG);
		// Set compression level (1 lowest quality, 100 highest quality)
		$image->setImageCompressionQuality(80);
		// Strip out unneeded meta data
		$image->stripImage();
		// Writes resultant image to output directory
		$image->writeImage($destImg);
		// Destroys Imagick object
		$image->destroy();
	}

	public static function getOrientation($sourceImg)
	{
		list($w, $h) = getimagesize($sourceImg);
		if($w > $h) {
			return 'landscape';
		} else if($w < $h) {
			return 'portrait';
		} else if($w == $h) {
			return 'square';
		}
	}
}