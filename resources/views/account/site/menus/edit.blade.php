@extends('account.site.menus.index', [ 'tab_current'=>$menu->id ])

@section('account_site_menus_content')

	{!! Form::model($menu, [ 'method'=>'PATCH', 'action'=>[ 'Account\Site\MenusController@update', $menu->slug ], 'id'=>'edit-form' ]) !!}
		<div class="form-horizontal">
			<div class="form-group">
				{!! Form::label('title', Lang::get('account/site.menus.create.name'), [ 'class'=>'col-md-4 control-label' ]) !!}
				<div class="col-md-6 error-container">
					{!! Form::text('title', null, [ 'class'=>'form-control required' ]) !!}
				</div>
			</div>
		</div>
		<hr />
		<ul class="items-sortable">
			@foreach ($menu->items->sortBy('position') as $item)
				<li class="item-container" id="item-{{$item->id}}-container">
					<div class="panel panel-custom">
						<div class="panel-heading item-handle cursor-move closed">
							<div class="pull-right">
								<a href="#item-{{$item->id}}-edit-modal" class="item-edit-trigger"><i class="fa fa-edit" aria-hidden="true"></i></a>
							</div>
							{{ $item->item_title }}
						</div>
						<div class="panel-body" style="display: none;">
							@include('account.site.menus.item', [ 'type'=>$item->type, 'item'=>$item ])
							<div class="text-right">
								<a href="#" data-rel="#item-{{$item->id}}-container" class="btn btn-danger btn-sm pull-left item-delete-trigger">{{ Lang::get('general.delete') }}</a>
								<a href="#" class="btn btn-primary btn-sm item-edit-modal-dismiss">{{ Lang::get('general.continue') }}</a>
							</div>
							<div class="text-center">
								<br />
								{!! Lang::get('account/site.menus.update.items.warning.save') !!}
							</div>
						</div>
					</div>
				</li>
			@endforeach
		</ul>
		<hr />
		<div class="text-right">
			<a href="#" class="btn btn-danger pull-left delete-trigger">{{ Lang::get('account/site.menus.delete.button') }}</a>
			{!! Form::submit(Lang::get('account/site.menus.update.button'), [ 'class'=>'btn btn-primary']) !!}
		</div>
	{!! Form::close() !!}

	{!! Form::open([ 'method'=>'DELETE', 'id'=>'delete-form', 'action'=>['Account\Site\MenusController@destroy', $menu->slug] ]) !!}
	{!! Form::close() !!}

	<script type="text/javascript">
		ready_callbacks.push(function(){
			var form = $('#edit-form');

			form.validate({
				ignore: '',
				errorPlacement: function(error, element) {
					element.closest('.error-container').append(error);
				},
				submitHandler: function(f) {
					LOADING.show();
					f.submit();
				}
			});

			form.on('click','.delete-trigger',function(e){
				var elem = $(this);

				e.preventDefault();

				alertify.confirm("{{ print_js_string( Lang::get('account/site.menus.delete.warning') ) }}", function (res) {
					if ( res ) {
						$('#delete-form').submit();
					}
				});
			});

			form.on('click','.item-edit-trigger',function(e){
				e.preventDefault();

				var header = $(this).closest('.panel-heading');
				var target = $(this).closest('.panel').find('.panel-body');

				if ( target.is(':visible') ) {
					target.slideUp(function(){
						header.addClass('closed');
					});
				} else {
					header.removeClass('closed');
					target.slideDown();
				}
			});

			form.find('.items-sortable').sortable({
				handle: '.item-handle'
			});

			$('.item-edit-modal-dismiss').on('click', function (e) {
				e.preventDefault();
				if ( form.valid() ) {
					var header = $(this).closest('.panel').find('.panel-heading');
					var target = $(this).closest('.panel').find('.panel-body');
					target.slideUp(function(){
						header.addClass('closed');
					});
				}
			});

			$('.item-delete-trigger').on('click', function (e) {
				var sel = $(this).data().rel;

				e.preventDefault();

				alertify.confirm("{{ print_js_string( Lang::get('account/site.menus.update.items.warning.delete') ) }}", function (res) {
					if ( res ) {
						$.magnificPopup.close();
						$(sel).remove();
					}
				});
			});

		});
	</script>

@endsection