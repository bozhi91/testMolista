<?php

namespace App\Models\Utils;

/**
 * @author Victor Demin demmbox@gmail.com
 */
class VideoHelper {

	/**
	 * Check if video is vimeo
	 * @return bool
	 */
	public static function isVideoVimeo($link) {
		return strpos($link, 'vimeo') > 0 ? true : false;
	}

	/**
	 * Check if video is youtube
	 * @return bool
	 */
	public static function isVideoYoutube($link) {
		return strpos($link, 'youtube') > 0 ? true : false;
	}

	/**
	 * Get YouTube code from YouTube link
	 * @param string link
	 * @return string|null YouTube code or false
	 */
	public static function getYouTubeCode($link) {
		preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $link, $matches);
		return isset($matches[0]) ? $matches[0] : null;
	}

	/**
	 * Will return vimeo code from vimeo JSON API.
	 * todo will be better to store this id and not depend on vimeo
	 * @return string
	 */
	public static function getVimeoCode($link) {
		preg_match('#(?:https?://)?(?:www.)?(?:player.)?vimeo.com/(?:[a-z]*/)*([0-9]{6,11})[?]?.*#', $link, $matches);
		return isset($matches[1]) ? $matches[1] : null;

		/* $vimeoApi = "http://vimeo.com/api/oembed.json?url=";
		  $json = file_get_contents($vimeoApi . $link);
		  $obj = json_decode($json);
		  return $obj->video_id; */
	}

}
