<?php namespace App\Marketplaces\Interfaces;

interface PublishByFtpInterface {

	/**
	* Get the remote filename for the given site.
	*
	* @param  App\Site	$site
	* @return string
	*/
	public function getFeedRemoteFilename(\App\Site $site);

}
