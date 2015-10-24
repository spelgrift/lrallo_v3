<?php

class Image
{
	/**
	 * @param string $sourceImg 	The path to the image to fit
	 * @param string $destImg 		The path where the resized image will be saved
	 * @param int $maxW 				Max width
	 * @param int $maxH 				Max height
	 * 
	 * @return bool True on success
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
		// Destroys Imagick object, freeing allocated resources in the process
		$image->destroy();
	}

}
?>