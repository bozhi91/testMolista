<?php

use App\Models\Utils\VideoHelper;

/* @var $video string */
?>
<div class="video-container">
    <iframe src="//www.youtube.com/embed/<?= VideoHelper::getYouTubeCode($video) ?>" 
			frameborder="0" allowfullscreen>
	</iframe> 
</div>