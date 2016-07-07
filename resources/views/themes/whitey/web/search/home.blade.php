<div id="home-top-search">
	<div class="relative">
		<div class="home-top-inner">
			<div class="container">
				<div class="row">
					{!! Form::model(null, [ 'action'=>'Web\PropertiesController@index', 'method'=>'GET', 'id'=>'home-search-form' ]) !!}
					<div class="hidden-xs col-sm-12">
						<ul class="nav navbar-nav">
							<li>
								<a href="#advanced-search-modal" id="advanced-search-opener"> <i class="fa fa-bars" aria-hidden="true"></i> </a>
							</li>
							<li>
								{!! Form::text('term', Input::get('term'), [ 'class'=>'form-control', 'placeholder'=>Lang::get('web/properties.term') ]) !!}
							</li>
							<li>
								{!! Form::select('mode', [''=>Lang::get('web/properties.mode')]+$search_data['modes'], Input::get('mode'), [ 'class'=>'form-control has-placeholder' ]) !!}
							</li>
							<li>
								{!! Form::select('type', [''=>Lang::get('web/properties.type')]+$search_data['types'], Input::get('type'), [ 'class'=>'form-control has-placeholder' ]) !!}
							</li>
							<li>
								{!! Form::select('state', [''=>Lang::get('web/properties.state')]+$search_data['states'], Input::get('state'), [ 'class'=>'form-control has-placeholder' ]) !!}
							</li>
							<li class="search-top-submit-main">
								<button class="" type="submit">
									<i class="fa fa-search" aria-hidden="true"></i>
								</button>
							</li>
						</ul>
					</div>
					{!! Form::close() !!}
				</div>
			</div>
		</div>
	</div>
</div>

<div id="advanced-search-modal" class="mfp-hide app-popup-block search-popup-block">
	<h2>{{ Lang::get('web/search.title.popup') }}</h2>
	@include('web.search.form')
</div>

<script type="text/javascript">
	ready_callbacks.push(function(){
		$('#advanced-search-opener').magnificPopup({
			type: 'inline',
			preloader: false,
			closeOnBgClick: false,
			callbacks: {
				open: function() {
					$('.if-overlay-then-blurred').addClass('blurred');
					$('#advanced-search-modal').find('select.has-select-2').each(function(){
						$(this).select2();
					});

					/* Sending selections from home-search-form */
					$('#advanced-search-modal').find('[name="term"]').val($('#home-search-form').find('[name="term"]').val());
					$('#advanced-search-modal').find('[name="mode"]').val($('#home-search-form').find('[name="mode"]').val());
					$('#advanced-search-modal').find('[name="type"]').val($('#home-search-form').find('[name="type"]').val());
					$('#advanced-search-modal').find('[name="state"]').val($('#home-search-form').find('[name="state"]').val());
					/* Sending selections from home-search-form */
				}
			}
		});		
	});
</script>