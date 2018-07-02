@extends('layouts.web')
@section('content')

	<?php
        //Display the first post by default
        if(empty($_GET['post_id'])){
            $posts = App\Http\Controllers\Account\Site\BlogController::getAllPosts();
        }
        else{
            $posts = App\Http\Controllers\Account\Site\BlogController::getPostById($_GET['post_id']);
        }

		if(count($posts)==0){
        	$title="Blog";
        	$body="El blog está vacio!";
    	}
    	else{
            $title = $posts[0]->title;
            $body  = $posts[0]->body;
        }
	?>

	<div id="pages">
		<div class="container">

			<!-- Display the post title and body-->
			<div class="row">
				<div class="col-sm-10">
                    <h1>{!! $title !!}</h1><br/>
                    {!! $body !!}<br/>

				</div>
				<div class="col-sm-2">
					<b>Mis entradas</b>
					<ul>
                        <?php $posts = App\Http\Controllers\Account\Site\BlogController::getAllPosts();?>
						@foreach($posts as $post)
							<li> <a href="?post_id={{ $post->id}}">{{ $post->title}} </a> </li>
						@endforeach
					</ul>
				</div>
			</div>
		</div>
	</div>

	<script type="text/javascript">
	</script>

@endsection
