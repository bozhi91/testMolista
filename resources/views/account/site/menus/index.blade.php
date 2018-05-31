@extends('layouts.account')

@section('account_content')

	<div id="site-menus" class="site-menus">

	 	@include('common.messages', [ 'dismissible'=>true ])

		<h1>{{ Lang::get('account/site.menus.h1') }}</h1>

		<div class="row">
			<div class="col-xs-12 col-sm-5">

				<div class="menu-item-options relative">
					@if ( empty($menu) )
						<div class="mfp-bg" style="position: absolute;"></div>
					@endif

					<h3>{{ Lang::get('account/site.menus.links.title') }}</h3>

					<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
						<div class="panel panel-custom">
							<div role="button" id="menu-item-custom-heading" data-toggle="collapse" href="#menu-item-custom" aria-expanded="{{ (old('items.new.type') == 'custom') ? 'true' : 'false' }}" aria-controls="menu-item-custom" class="panel-heading {{ (old('items.new.type') == 'custom') ? '' : 'collapsed' }}">
								<div class="pull-right"><span class="caret"></span></div>
								{{ Lang::get('account/site.menus.links.custom') }}
							</div>
							<div id="menu-item-custom" class="panel-collapse collapse {{ (old('items.new.type') == 'custom') ? 'in' : '' }}" role="tabpanel" aria-labelledby="menu-item-custom-heading">
								<div class="panel-body">
									@if ( !empty($menu) )
										{!! Form::open([ 'method'=>'POST', 'class'=>'new-menu-item-form', 'action'=>['Account\Site\MenusController@postItem', $menu->slug] ]) !!}
											{!! Form::hidden('items[new][type]','custom') !!}
											@include('account.site.menus.item', [ 'type'=>'custom', 'item'=>false ])
											<div class="text-right">
												{!! Form::submit(Lang::get('account/site.menus.links.button'), [ 'class'=>'btn btn-primary btn-sm']) !!}
											</div>
										{!! Form::close() !!}
									@endif
								</div>
							</div>
						</div>

						<div class="panel panel-custom">
							<div role="button" id="menu-item-properties-heading" data-toggle="collapse" href="#menu-item-properties" aria-expanded="{{ (old('items.new.type') == 'property') ? 'true' : 'false' }}" aria-controls="menu-item-properties" class="panel-heading {{ (old('items.new.type') == 'property') ? '' : 'collapsed' }}">
								<div class="pull-right"><span class="caret"></span></div>
								{{ Lang::get('account/site.menus.links.properties') }}
							</div>
							<div id="menu-item-properties" class="panel-collapse collapse {{ (old('items.new.type') == 'property') ? 'in' : '' }}" role="tabpanel" aria-labelledby="menu-item-properties-heading">
								<div class="panel-body">
									@if ( !empty($menu) )
										{!! Form::open([ 'method'=>'POST', 'class'=>'new-menu-item-form', 'action'=>['Account\Site\MenusController@postItem', $menu->slug] ]) !!}
											{!! Form::hidden('items[new][type]','property') !!}
											@include('account.site.menus.item', [ 'type'=>'property', 'item'=>false ])
											<div class="text-right">
												{!! Form::submit(Lang::get('account/site.menus.links.button'), [ 'class'=>'btn btn-primary btn-sm']) !!}
											</div>
										{!! Form::close() !!}
									@endif
								</div>
							</div>
						</div>

						<div class="panel panel-custom">
							<div role="button" id="menu-item-pages-heading" data-toggle="collapse" href="#menu-item-pages" aria-expanded="{{ (old('items.new.type') == 'page') ? 'true' : 'false' }}" aria-controls="menu-item-pages" class="panel-heading {{ (old('items.new.type') == 'page') ? '' : 'collapsed' }}">
								<div class="pull-right"><span class="caret"></span></div>
								{{ Lang::get('account/site.menus.links.pages') }}
							</div>
							<div id="menu-item-pages" class="panel-collapse collapse {{ (old('items.new.type') == 'page') ? 'in' : '' }}" role="tabpanel" aria-labelledby="menu-item-pages-heading">
								<div class="panel-body">
									@if ( !empty($menu) )
										{!! Form::open([ 'method'=>'POST', 'class'=>'new-menu-item-form', 'action'=>['Account\Site\MenusController@postItem', $menu->slug] ]) !!}
											{!! Form::hidden('items[new][type]','page') !!}
											@include('account.site.menus.item', [ 'type'=>'page', 'item'=>false ])
											<div class="text-right">
												{!! Form::submit(Lang::get('account/site.menus.links.button'), [ 'class'=>'btn btn-primary btn-sm']) !!}
											</div>
										{!! Form::close() !!}
									@endif
								</div>
							</div>
						</div>

						<!--------------- CONTACT WIDGET ---------------------------->
						<div class="panel panel-custom">
							<div role="button" id="menu-item-custom-heading_1" data-toggle="collapse" href="#menu-item-custom_1" aria-expanded="{{ (old('items.new.type') == 'contact') ? 'true' : 'false' }}" aria-controls="menu-item-custom_1" class="panel-heading {{ (old('items.new.type') == 'contact') ? '' : 'collapsed' }}">
								<div class="pull-right"><span class="caret"></span></div>
								Contacto
							</div>
							<div id="menu-item-custom_1" class="panel-collapse collapse {{ (old('items.new.type') == 'contact') ? 'in' : '' }}" role="tabpanel" aria-labelledby="menu-item-custom-heading_1">
								<div class="panel-body">
									@if ( !empty($menu) )
										{!! Form::open([ 'method'=>'POST', 'class'=>'new-menu-item-form', 'action'=>['Account\Site\MenusController@postItem', $menu->slug] ]) !!}
										{!! Form::hidden('items[new][type]','contact') !!}
										@include('account.site.menus.item', [ 'type'=>'contact', 'item'=>false ])
										<div class="text-right">
											{!! Form::submit(Lang::get('account/site.menus.links.button'), [ 'class'=>'btn btn-primary btn-sm']) !!}
										</div>
										{!! Form::close() !!}
									@endif
								</div>
							</div>
						</div>
						<!--------------- CONTACT WIDGET ---------------------------->
					</div>

				</div>
			</div>
			<div class="col-xs-12 col-sm-7">

				<ul class="nav nav-tabs" role="tablist">
					@foreach( $menus as $menu )
						<li class="{{ ($tab_current == $menu->id) ? 'active' : '' }}"><a href="{{ action('\App\Http\Controllers\Account\Site\MenusController@edit', $menu->slug) }}" class="menu-link">{{$menu->title}}</a></li>
					@endforeach
					<li class="{{ ($tab_current == 'create') ? 'active' : '' }}"><a href="{{ action('\App\Http\Controllers\Account\Site\MenusController@create') }}" class="menu-link">{{ Lang::get('account/site.menus.tabs.new') }}</a>
				</ul>

				<div class="tab-content">
					<div class="tab-pane active">
						@yield('account_site_menus_content')
					</div>
				</div>

			</div>
		</div>

	</div>

	<script type="text/javascript">
		ready_callbacks.push(function(){
			var cont = $('#site-menus');

			cont.on('click', '.menu-link', function(e){
				if ( $(this).hasClass('active') ) {
					e.preventDefault();
				}
			}) ;

			cont.find('.new-menu-item-form').each(function(){
				var form = $(this);

				form.validate({
					ignore: '',
					errorPlacement: function(error, element) {
						element.closest('.error-container').append(error);
					},
					invalidHandler: function(e, validator){
						if ( validator.errorList.length ) {
							var el = $(validator.errorList[0].element);
							if ( el.closest('.tab-locale').length ) {
								form.find('.locale-tabs a[href="#' + el.closest(".tab-locale").attr('id') + '"]').tab('show');
							}
						}
					},
					submitHandler: function(f) {
						LOADING.show();
						f.submit();
					}
				});

			});

			$('#accordion').on('shown.bs.collapse', function (e) {
				$(e.target).find('.has-select-2').select2();
			});

		});
	</script>

@endsection