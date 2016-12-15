<?php

use App\Models\Property\Videos;

/* @var $video string */
?>
<div class="video-container">
	<iframe src="//player.vimeo.com/video/<?= Videos::getVimeoCode($video) ?>"
			frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen>
	</iframe>
</div>