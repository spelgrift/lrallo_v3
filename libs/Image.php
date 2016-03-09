<?php

class Image
{
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
		$x = ($w_orig / 2) - (THUMBSIZE / 2);
		$y = ($h_orig / 2) - (THUMBSIZE / 2);
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