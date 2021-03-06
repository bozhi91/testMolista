<?php

namespace App\Models\Property;

use Illuminate\Database\Eloquent\Model;

class Images extends Model
{
	protected $table = 'properties_images';

	protected $guarded = [];

	public $timestamps = false;

	public function property()
	{
		return $this->belongsTo('App\Property');
	}

	public function getImagePathAttribute()
	{
		return "sites/{$this->property->site_id}/properties/{$this->property->id}/{$this->image}";
	}
	public function getImageUrlAttribute()
	{
		if($this->updated_at){
			return asset($this->image_path . '?v=' . strtotime($this->updated_at));
		}
		
		return asset($this->image_path);
	}
	
	public function getImageUrlThumbAttribute()
	{
		if ( !$this->image_url )
		{
			return false;
		}

		$tmp = pathinfo($this->image_url);

		return implode('/', [
					$tmp['dirname'],
					'thumbnail',
					$tmp['basename'],
				]);
	}

	public function getImageSizeAttribute()
	{
		@list($w, $h) = @getimagesize( public_path($this->image_path) );
		return [
			intval($w),
			intval($h),
		];

	}
	public function getIsVerticalAttribute()
	{
		list($w, $h) = $this->image_size;
		return ( $w && $h && $w < $h ) ? true : false;
	}
	public function getHasSizeAttribute()
	{
		list($w, $h) = $this->image_size;
		return ( $w && $w < 1280 ) ? false : true;
	}

}