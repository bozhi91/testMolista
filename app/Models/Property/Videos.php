<?php

namespace App\Models\Property;

use Illuminate\Database\Eloquent\Model;

class Videos extends Model {

	protected $table = 'properties_videos';
	protected $guarded = [];

	public function property() {
		return $this->belongsTo('App\Property');
	}

	/**
	 * @return string
	 */
	public function getImagePathAttribute(){
		return "sites/{$this->property->site_id}/properties/{$this->property->id}/video/{$this->thumbnail}";
	}
	
	/**
	 * @return string
	 */
	public function getImageUrlAttribute(){
		if($this->updated_at){
			return asset($this->image_path . '?v=' . strtotime($this->updated_at));
		}
		return asset($this->image_path);
	}
	
	/**
	 * Check if video is vimeo
	 * @param string $link
	 * @return bool
	 */
	public static function isVideoVimeo($link) {
		return strpos($link, 'vimeo') > 0 ? true : false;
	}

	/**
	 * Check if video is youtube
	 * @param string $link
	 * @return bool
	 */
	public static function isVideoYoutube($link) {
		
		
		return strpos($link, 'youtu') > 0 ? true : false;
	}

	/**
	 * Get YouTube code from YouTube link
	 * @param string link
	 * @return string|null YouTube code or false
	 */
	public static function getYouTubeCode($link) {
		preg_match("/^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#\&\?]*).*/", $link, $matches);
		return isset($matches[7]) ? $matches[7] : null;
	}

	/**
	 * Will return vimeo code
	 * @param string $link
	 * @return string|null
	 */
	public static function getVimeoCode($link) {
		preg_match('#(?:https?://)?(?:www.)?(?:player.)?vimeo.com/(?:[a-z]*/)*([0-9]{6,11})[?]?.*#', $link, $matches);
		return isset($matches[1]) ? $matches[1] : null;

		/* $vimeoApi = "http://vimeo.com/api/oembed.json?url=";
		  $json = file_get_contents($vimeoApi . $link);
		  $obj = json_decode($json);
		  return $obj->video_id; */
	}

	/**
	 * @param string $link
	 * @return string|null
	 */
	public static function getVimeoThumbnail($link) {
		$code = self::getVimeoCode($link);

		if (!$code) {
			return null;
		}

		$json_data = file_get_contents("http://vimeo.com/api/v2/video/$code.json");
		$data = json_decode($json_data);
		return $data[0]->thumbnail_large;
	}

	/**
	 * @param string $link
	 * @return string|null
	 */
	public static function getYoutubeThumbnail($link) {
		$code = self::getYouTubeCode($link);
		return $code ? "https://img.youtube.com/vi/$code/0.jpg" : null;
	}

}
