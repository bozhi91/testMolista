<?php

use App\Models\Property\Videos;

/* @var $video string */
?>
<div class="video-container">
    <iframe src="//www.youtube.com/embed/<?= Videos::getYouTubeCode($video) ?>" 
			frameborder="0" allowfullscreen>
	</iframe> 
</div>