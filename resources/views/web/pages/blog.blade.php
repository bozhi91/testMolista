@extends('layouts.web')

@section('content')

	<?php
    	$posts = App\Http\Controllers\Account\Site\PagesController::getAllPosts();
	?>

	<div id="pages">
		<div class="container">

			<!-- Display the post title -->
			@if(!empty( $_GET['post_id']))
				@foreach($posts as $post)
					@if($post->id == $_GET['post_id'])
						<h1>{!! $post->title !!}</h1><br/>
					@endif
				@endforeach
			@else
				<h1>{!! $posts[0]->body!!}</h1><br/><
			@endif


			<div class="row">
				<div class="col-sm-10">

					@if(!empty( $_GET['post_id']))
						@foreach($posts as $post)
							@if($post->id == $_GET['post_id'])
								{!! $post->body !!}
							@endif
						@endforeach
						@else
						{!! $posts[0]->body!!}
					@endif
				</div>
				<div class="col-sm-2">
					<b>Mis entradas</b>
					<ul>
                        <?php
                       	 	$posts = App\Http\Controllers\Account\Site\PagesController::getAllPosts();
                        ?>
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
