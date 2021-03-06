<?php
/**
* Ok, glad you are here
* first we get a config instance, and set the settings
* $config = HTMLPurifier_Config::createDefault();
* $config->set('Core.Encoding', $this->config->get('purifier.encoding'));
* $config->set('Cache.SerializerPath', $this->config->get('purifier.cachePath'));
* if ( ! $this->config->get('purifier.finalize')) {
*     $config->autoFinalize = false;
* }
* $config->loadArray($this->getConfig());
*
* You must NOT delete the default settings
* anything in settings should be compacted with params that needed to instance HTMLPurifier_Config.
*
* @link http://htmlpurifier.org/live/configdoc/plain.html
*/

return [

	'encoding' => 'UTF-8',
	'finalize' => true,
	'cachePath' => false, //storage_path('app/purifier'),
	'cacheFileMode' => 0755,
	'settings' => [
		'default' => [
			'HTML.Doctype'				=> 'XHTML 1.0 Transitional',
			'HTML.Allowed'				=> 'blockquote,pre,h1,h2,h3,h4,h5,h6,div,b,strong,i,em,a[href|title],ul,ol,li,p[style],br,span[style],img[width|height|alt|src|style],iframe[frameborder|width|height|alt|src|style]',
			'CSS.AllowedProperties'		=> 'font,font-size,font-weight,font-style,font-family,text-decoration,padding-left,color,background-color,text-align,float,width',
			'HTML.MaxImgLength'			=> NULL,
			'HTML.TargetBlank'			=> true,
			'CSS.MaxImgLength'			=> NULL,
			'AutoFormat.AutoParagraph'	=> true,
			'AutoFormat.RemoveEmpty'	=> true,
			"HTML.SafeIframe"      		=> true,
			"URI.SafeIframeRegexp" 		=> "%^(http://|https://|//)(www.youtube.com/embed/|player.vimeo.com/video/)%",
		],
		'test'    => [
			'Attr.EnableID' => true
		],
		"youtube" => [
			"HTML.SafeIframe"      => 'true',
			"URI.SafeIframeRegexp" => "%^(http://|https://|//)(www.youtube.com/embed/|player.vimeo.com/video/)%",
		],
	],

];
