@extends('layouts.account')

@section('account_content')

	<div id="admin-pages">

		@include('common.messages', [ 'dismissible'=>true ])
		<?php

            $blog     = App\Http\Controllers\Account\Site\PAgesController::getBlog();
            $isActive = null;
            if(!empty($blog)){
                $isActive = App\Http\Controllers\Account\Site\PAgesController::isBlogActivated();
            }
            $inactiveBlog="";
            if($isActive==false && !empty($blog)){
                $inactiveBlog = Lang::get('account/site.blog.inactive');//"The blog was created, but it is not accessible from the web page yet.".
            }

        	//The blog is not created yet
			if($blog==false){
                $blogPath  = "Account\Site\PagesController@createNewBlog";
                $btnTxt    = Lang::get('account/site.blog.createBlog');
                $blogEmpty = Lang::get('account/site.blog.emptyBlog');
            }
            else{//the blog is created
                $blogEmpty =  Lang::get('account/site.blog.emptyPost');//there are no posts in the blog.
                $blogPath  = "Account\Site\PagesController@createNewPost";
                $btnTxt    = Lang::get('account/site.blog.createPost');
           }
        ?>

		<div class="pull-right">
			<a href="{{ action($blogPath) }}" class="btn btn-primary">
				{{ $btnTxt }}
			</a>
		</div><br/><br/><br/>

		@if(!$isActive && !empty($blog))
			<div class="alert alert-info">{{ $inactiveBlog }}</div>
		@endif

		@if (count($entradas)<1)
			<div class="alert alert-info">{{ $blogEmpty }}</div>
		@else
			<table class="table table-striped">
				<thead>
					<tr>
						<th>{{ Lang::get('account/site.pages.column.title') }}</th>
						<th>{{ Lang::get('account/site.pages.column.type') }}</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
				@foreach ($entradas as $entrada)
						<tr>
							<td><a href="/">{{ $entrada->title }}</a></td>
							<td>{{ $entrada->created_at }}</td>

							<td class="text-right text-nowrap">
								{!! Form::open([ 'method'=>'POST', 'class'=>'delete-form', 'action'=>['Account\Site\PagesController@deletePost'] ]) !!}
								{{ Form::input('hidden', 'post_id',$entrada->id) }}
								{{ Form::input('hidden', 'action',"edit") }}

								<?php
									$attribs  = array("action"=>"edit","post_id"=>$entrada->id);

									$site_id  = session('SiteSetup')['site_id'];
                                	$domain   = env('APP_DOMAIN');
									$protocol = env("APP_PROTOCOL");
                                	$site = App\Http\Controllers\Account\Site\PagesController::getSiteById($site_id);
                                	$url  = $protocol."://".$site->subdomain.".".$domain."/pages/blog?post_id=".$entrada->id;

								?>
								<a href="{{$url}}"  class="btn btn-success btn-xs" style="color: white !important;" target="_blank">
									{{ Lang::get('general.view') }}
								</a>

								<a href="{{ action('Account\Site\PagesController@createNewPost',$attribs) }}" class="btn btn-warning btn-xs" target="_blank">
									{{ Lang::get('general.edit') }}
								</a>
								<button type="submit" class="btn btn-danger btn-xs">{{ Lang::get('general.delete') }}</button>
								{!! Form::close() !!}
							</td>
						</tr>
				@endforeach
				</tbody>
			</table>
		@endif
	</div>

	<script type="text/javascript">
		ready_callbacks.push(function() {
			var cont = $('#admin-pages');
			cont.find('form.delete-form').each(function(){
				$(this).validate({
					submitHandler: function(f) {
						SITECOMMON.confirm("{{  Lang::get('account/site.blog.delete') }}", function (e) {
							if (e) {
								LOADING.show();
								f.submit();
							}
						});
					}
				});
			});

		});
	</script>
@endsection