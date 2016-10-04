@extends('layouts.account')

@section('account_content')

	<div id="site-widgets" class="site-widgets">

	 	@include('common.messages', [ 'dismissible'=>true ])

		<h1 class="page-title">{{ Lang::get('account/site.widgets.h1') }}</h1>

		<div class="row">
			<div class="col-xs-12 col-sm-7">

				<div class="panel panel-custom">
					<div class="panel-heading">{{ Lang::get('account/site.widgets.available') }}</div>
					<div class="panel-body">
						<div class="row widget-list widget-draggables">

							@foreach ($type_options as $type)
								<div class="widget-available col-xs-6">
									@include('account.site.widgets.item', [ 'type'=>$type, 'item'=>false, 'widget_class'=>'widget-draggable' ])
								</div>
							@endforeach

						</div>
					</div>
				</div>

			</div>
			<div class="col-xs-12 col-sm-5">

				<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">

					@foreach ($group_options as $group => $group_def)
						<div class="panel panel-custom">
							<div role="button" id="widget-{{$group}}-heading" data-toggle="collapse" href="#widget-{{$group}}" aria-expanded="true" aria-controls="widget-{{$group}}" class="panel-heading">
								<div class="pull-right"><span class="caret"></span></div>
								{{ Lang::get("account/site.widgets.group.{$group}") }}
							</div>
							<div id="widget-{{$group}}" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="widget-{{$group}}-heading">
								<div class="panel-body">
									<div class="widget-list widget-droppables" data-group="{{$group}}" data-max="{{@$group_def['max']}}" data-accept="{{@$group_def['accept']}}" data-sort="{{ action('Account\Site\WidgetsController@postSort', $group) }}">
										@foreach ($widgets->where('group', $group)->sortBy('position') as $widget)
											@include('account.site.widgets.item', [ 'type'=>$widget->type, 'item'=>$widget, 'widget_closed'=>true ])
										@endforeach
									</div>
								</div>
							</div>
						</div>
					@endforeach

				</div>

			</div>
		</div>

	</div>

	<script type="text/javascript">
		ready_callbacks.push(function(){
			var cont = $('#site-widgets');

			function initForms() {
				cont.find('.widget-form').each(function(){
					var form = $(this);
						
					if ( form.hasClass('initialized') )
					{
						return true;
					}

					form.addClass('initialized').validate({
						ignore: '',
						errorPlacement: function(error, element) {
							element.closest('.error-container').append(error);
						},
						submitHandler: function(f) {
							LOADING.show();
							$.ajax({
								method: 'POST',
								url: form.attr('action'),
								data: new FormData(form[0]),
								processData: false,
								contentType: false,
								success: function(data) {
									LOADING.hide();
									if ( data.success ) {
										alertify.success("{{ print_js_string( Lang::get('account/site.widgets.messages.updated') ) }}");
										// change title
										var title = form.find('.title-input[lang="' + $('html').attr('lang') + '"]').val();
										if ( !title ) {
											title = form.find('.title-input[lang="{{fallback_lang()}}"]').val();
										}
										if ( title ) {
											form.closest('.widget').find('.widget-title .text').text(title);
										}
									} else {
										alertify.error("{{ print_js_string( Lang::get('general.messages.error') ) }}");
									}
								},
								error: function() {
									LOADING.hide();
									alertify.error("{{ print_js_string( Lang::get('general.messages.error') ) }}");
								}
							});
						}
					});
					
					form.find('.label-color-input').each(function(){
						var el = $(this);
						var target = form.find('.label-color-hidden');

						if ( !target.val() ) {
							target.val('s');
						}

						el.spectrum({
							preferredFormat: "hex",
							showInput: true,
							color: target.val(),
							move: function(color) {
								target.val( color.toHexString() );
								form.find('.label-color-input').spectrum("set", color);
							}
						});
					});
					
				});
			}

			function saveWidgetOrder(group) {
				var items = [];
				group.find('.widget').each(function(){
					items.push( $(this).data().id );
				});

				$.post(group.data().sort, { items : items }, function(data) {
					//console.log(data);
				});
			}

			cont.find('.widget-droppables').sortable({
				revert: true,
				update: function(event, ui) {
					if ( $(this).find('.widget.widget-draggable').length ) {
						return;
					}
					saveWidgetOrder( $(this) );
				},
				receive: function(event, ui) {
					var el = $(this).find('.widget.widget-draggable');

					if ( !el.length ) {
						return;
					}

					var list = $(this);

					// Check if type is accepted
					if ( list.data().accept ) {
						var accepted = false;
						$.each(list.data().accept.split('|'), function(k,v) {
							if ( el.hasClass('widget-type-'+v) ) {
								accepted = true;
							}
						});
						if ( !accepted ) {
							alertify.error("{{ print_js_string( Lang::get('account/site.widgets.messages.not.accepted') ) }}");
							el.slideUp(function(){
								$(this).remove();
							});
							return false;
						}
					}

					// Check if max has been reached
					if ( list.data().max ) {
						if ( parseInt( list.data().max ) < list.find('.widget').length ) {
							alertify.error("{{ print_js_string( Lang::get('account/site.widgets.messages.max.reached') ) }}");
							el.slideUp(function(){
								$(this).remove();
							});
							return false;
						}
					}

					el.removeAttr('style').find('.widget-title').addClass('closed');

					LOADING.show();

					$.ajax({
						dataType: 'json',
						url: '{{ action('Account\Site\WidgetsController@getStore') }}',
						data: {
							group : list.data().group,
							type : el.data().type
						},
						success: function(data) {
							LOADING.hide();
							if ( data.success ) {
								alertify.success("{{ print_js_string( Lang::get('account/site.widgets.messages.created') ) }}");
								el.replaceWith( data.html );
								initForms();
								saveWidgetOrder( $(this) );
							} else {
								el.remove();
								alertify.error("{{ print_js_string( Lang::get('general.messages.error') ) }}");
							}
						},
						error: function() {
							el.remove();
							LOADING.hide();
							alertify.error("{{ print_js_string( Lang::get('general.messages.error') ) }}");
						}
					});
				}
			});

			cont.find('.widget-draggable').draggable({
				connectToSortable: '.widget-droppables',
				helper: function(e) {
					var target = $(e.target).hasClass('widget-draggable') ? $(e.target) : $(e.target).closest('.widget-draggable');
					return target.clone().css({
						width: target.width()
					});                
				},
				opacity: 0.9,
				revert: 'invalid'
			});

			cont.on('click','.widget-toggle',function(e){
				e.preventDefault();

				var header = $(this).closest('.widget-title');
				var target = $(this).closest('.widget').find('.widget-body');

				if ( target.is(':visible') ) {
					target.slideUp(function(){
						header.addClass('closed');
					});
				} else {
					header.removeClass('closed');
					target.slideDown();
				}
			});

			cont.on('click','.btn-widget-delete',function(e){
				e.preventDefault();

				var el = $(this);

				SITECOMMON.confirm("{{ print_js_string( Lang::get('account/site.widgets.messages.delete.warning') ) }}", function (e) {
					if (e) {
						LOADING.show();
						$.ajax({
							method: 'POST',
							dataType: 'json',
							url: el.data().href,
							success: function(data) {
								LOADING.hide();
								if ( data.success ) {
									alertify.success("{{ print_js_string( Lang::get('account/site.widgets.messages.deleted') ) }}");
									el.closest('.widget').remove();
								} else {
									alertify.error("{{ print_js_string( Lang::get('general.messages.error') ) }}");
								}
							},
							error: function() {
								LOADING.hide();
								alertify.error("{{ print_js_string( Lang::get('general.messages.error') ) }}");
							}
						});
					}
				});
			});

			cont.on('click','.btn-widget-close',function(e){
				e.preventDefault();
				$(this).closest('.widget').find('.widget-toggle').trigger('click');
			});

			initForms();

		});
	</script>

@endsection