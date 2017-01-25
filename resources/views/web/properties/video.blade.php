<?php

use App\Models\Property\Videos;

/* @var $video Videos */
?>

<div class="video-container">
	@if(Videos::isVideoVimeo($video))
	<iframe src="//player.vimeo.com/video/<?= Videos::getVimeoCode($video) ?>"
			frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen>
	</iframe>
	@elseif(Videos::isVideoYoutube($video))
	<iframe src="//www.youtube.com/embed/<?= Videos::getYouTubeCode($video) ?>"
			frameborder="0" allowfullscreen>
	</iframe>
	@endif
</div>
