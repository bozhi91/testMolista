<?php namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;

use App\Http\Requests;

class ThumbnailsController extends \App\Http\Controllers\Controller
{
	protected $max_weight = null; // MB
	protected $max_width = null; // Pixels
	protected $min_width = null; // Pixels
	protected $max_height = null; // Pixels
	protected $min_height = null; // Pixels

	protected $original; // Source image path
	protected $destination; // Destination image path

	protected $encode_format; // jpg, png, gif, tif, bmp, data-url

	public function property($site_id, $property_id, $flag, $image)
	{
		$this->original = public_path("sites/{$site_id}/properties/{$property_id}/{$image}");
		$this->destination = public_path("sites/{$site_id}/properties/{$property_id}/{$flag}/{$image}");

		// Flag
		switch ($flag)
		{
			case 'trovit':
				$this->max_weight = 1;
				$this->min_width = 186;
				$this->min_height = 186;
				break;
			case 'kyero':
				$this->min_width = 1280;
				$this->min_height = 960;
				break;
			case 'greenacres':
				$this->encode_format = 'jpg';
				$this->original = substr($this->original, 0, -4);
				break;
			case 'tuad':
				$this->max_weight = 1;
				break;
			default:
				abort(404);
		}

		return $this->createThumbnail();
	}

	protected function createThumbnail()
	{
		// Check original
		if ( !$this->original || !file_exists($this->original) )
		{
			abort(404);
		}

		// Check detination
		if ( !$this->destination )
		{
			abort(404);
		}

		// Check destination folder
		list($dirname,$filename) = array_values(pathinfo($this->destination));
		if ( !is_dir($dirname) )
		{
			\File::makeDirectory($dirname, 0775, true);
		}

		// Create thumb
		$thumb = \Image::make($this->original)->save($this->destination);

		// Need encoding?
		if ( $this->encode_format )
		{
			$thumb->encode($this->encode_format)->save($this->destination);
		}

		// Get original size
		$width = $thumb->width();
		$height = $thumb->height();

		// Min width
		if ( $this->min_width && $this->min_width > $width )
		{
			$thumb->widen($this->min_width)->save($this->destination);
			$width = $thumb->width();
			$height = $thumb->height();
		}

		// Min height
		if ( $this->min_height && $this->min_height > $height )
		{
			$thumb->heighten($this->min_height)->save($this->destination);
			$width = $thumb->width();
			$height = $thumb->height();
		}

		// Max width / height
		if ( $this->max_width || $this->max_height )
		{
			$thumb->resize($this->max_width,$this->max_height,function($constraint) {
				$constraint->aspectRatio();
				$constraint->upsize();
			})->save($this->destination);
			$width = $thumb->width();
			$height = $thumb->height();
		}

		// Max weight
		if ( $this->max_weight )
		{
			// Max weight to bytes
			$this->max_weight = $this->max_weight * pow(1024,2);

			$filesize = $thumb->filesize();
			$quality = ceil( $this->max_weight / $filesize * 10 ) * 10;
			while ( $filesize > $this->max_weight && $quality > 10 )
			{
				$thumb->save($this->destination, $quality);
				$thumb->destroy();

				$thumb = \Image::make($this->destination);
				$filesize = $this->getImageWeight($this->destination);

				if ( $filesize > $this->max_weight )
				{
					$thumb->resize(ceil($width*$quality/100), null, function($constraint) {
						$constraint->aspectRatio();
						$constraint->upsize();
					})->save($this->destination);
					$thumb->destroy();

					$thumb = \Image::make($this->destination);
					$width = $thumb->width();
					$height = $thumb->height();
					$filesize = $this->getImageWeight($this->destination);
				}

				$quality -= 5;
			}
		}

		return $thumb->response();
	}

	public function getImageWeight($imagepath)
	{
		clearstatcache();
		return filesize($imagepath);
	}

}
