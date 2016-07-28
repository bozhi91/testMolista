{!! Form::model($signature, [ 'method'=>$method, 'url'=>$action, 'id'=>'signature-form' ]) !!}

	<div class="row">
		<div class="col-xs-12 col-lg-4">
			<div class="form-group error-container">
				{!! Form::label('title', Lang::get('account/profile.signatures.field.title')) !!}
				{!! Form::text('title', null, [ 'class'=>'form-control required' ]) !!}
			</div>
			<div class="form-group error-container">
				<div class="checkbox">
					<label>
						{!! Form::checkbox('default', 1) !!}
						{{ Lang::get('account/profile.signatures.field.default') }}
					</label>
				</div>
			</div>
		</div>
		<div class="col-xs-12 col-lg-8">
			<div class="form-group error-container">
				{!! Form::label('signature', Lang::get('account/profile.signatures.field.signature')) !!}
				{!! Form::textarea('signature', null, [ 'id'=>'signature-input', 'class'=>'is-wysiwyg form-control' ]) !!}
			</div>
		</div>
	</div>

	<div class="text-right">
		<a href="{{ action('Account\Profile\SignaturesController@getIndex') }}" class="btn btn-default">{{ Lang::get('general.back') }}</a>
		@if ( @$signature )
			{!! Form::button( Lang::get('general.delete'), [ 'class'=>'btn btn-danger btn-delete-trigger']) !!}
		@endif
		{!! Form::button( Lang::get('general.continue'), [ 'type'=>'submit', 'class'=>'btn btn-primary']) !!}
	</div>

{!! Form::close() !!}

@if ( @$signature )
	{!! Form::open([ 'method'=>'delete', 'action'=>['Account\Profile\SignaturesController@deleteRemove', $signature->id], 'id'=>'signature-delete-form' ]) !!}
	{!! Form::close() !!}
@endif

<script type="text/javascript">
	ready_callbacks.push(function(){
		var form = $('#signature-form');
		var form_delete = $('#signature-delete-form');

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

		form.on('click','.btn-delete-trigger',function(e){
			e.preventDefault();
			form_delete.submit();
		});

		form_delete.validate({
			submitHandler: function(f) {
				SITECOMMON.confirm("{{ print_js_string( Lang::get('account/profile.signatures.delete.warning') ) }}", function (e) {
					if (e) {
						LOADING.show();
						f.submit();
					}
				});
			}
		});

		form.find('.is-wysiwyg').each(function(){
			var el = $(this);

			$(this).summernote({
				height: 250,
				lang: '{{ summetime_lang() }}',
				disableDragAndDrop: true,
				toolbar: [
					['style', ['bold', 'italic', 'underline', 'clear']],
					['font', ['strikethrough', 'superscript', 'subscript']],
					['insert', ['link','picture']]
				],
				callbacks: {
					onChange: function(content) {
						el.val( content );
					}
				}
			});
		});

	});
</script>