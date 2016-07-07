@foreach ($configuration as $block)
<h4>{{ Lang::get('account/marketplaces.configuration.fields.'.$block['block'].'.title') }}</h4>

<div class="row">
	@foreach ($block['fields'] as $field)
	<div class="col-xs-12 col-sm-6">
		<div class="form-group error-container">
			{!! Form::label('marketplace_configuration[configuration]['.$field['name'].']', Lang::get('account/marketplaces.configuration.fields.'.$field['name'])) !!}
			@if ($field['type'] == 'text' || $field['type'] == 'textarea')
			{!! Form::$field['type']('marketplace_configuration[configuration]['.$field['name'].']', @$values->$field['name'], [ 'class'=>'form-control '.(!empty($field['required']) ? 'required' : '') ]) !!}
			@endif
		</div>
	</div>
	@endforeach
</div>

<hr />
@endforeach
