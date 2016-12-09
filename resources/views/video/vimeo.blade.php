<?php

use App\Models\Utils\VideoHelper;

/* @var $video string */
?>
<div class="video-container">
	<iframe src="//player.vimeo.com/video/<?= VideoHelper::getVimeoCode($video) ?>"
			frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen>
	</iframe>
</div>