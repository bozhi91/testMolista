@if ($documents->count() > 0 )
	<div class="text-right">
		<a href="#appraisal-modal-form" class="btn btn-primary document-modal-trigger">{{ Lang::get('account/properties.documents.new') }}</a>
	</div>
	<table class="table">
		<thead>
			<th>{{ Lang::get('account/properties.documents.date') }}</th>
			<th>{{ Lang::get('account/properties.documents.appraiser') }}</th>
			<th>{{ Lang::get('account/properties.documents.appraisal.value') }}</th>
			<th class="text-right">&nbsp;</th>
		</thead>
		<tbody>
			@foreach($documents as $document)
				<tr>
					<td>{{ $document->date->format('d/m/Y') }}</td>
					<td>{{ $document->title }}</td>
					<td>{{ $document->description }}</td>
					<td class="text-right text-nowrap">
						<a href="{{ action('Account\Properties\DocumentsController@getDownload', [ $document->id,$document->file ]) }}" target="_blank" class="btn btn-primary btn-xs">{{ Lang::get('general.view') }}</a>
						<a href="#" data-href="{{ action('Account\Properties\DocumentsController@getDelete', [ $document->id,$document->file ]) }}?current_tab=tab-appraisal" class="btn btn-danger btn-xs document-delete-trigger">{{ Lang::get('general.delete') }}</a>
					</td>
				</tr>
			@endforeach
		</tbody>
	</table>
@else
	<a href="#appraisal-modal-form" class="btn btn-primary document-modal-trigger pull-right">{{ Lang::get('account/properties.documents.new') }}</a>
	<p>{{ Lang::get('account/properties.documents.none') }}</p>
@endif

{!! Form::model(null, [ 'method'=>'post', 'action'=>[ 'Account\Properties\DocumentsController@postUpload', $property->id ], 'files'=>true, 'id'=>'appraisal-modal-form', 'class'=>'mfp-hide app-popup-block-white']) !!}
	<input type="hidden" name="current_tab" value="tab-appraisal" />
	{!! Form::hidden('type','appraisal') !!}
	<h4>{{ Lang::get('account/properties.documents.new') }}</h4>
	<div style="padding: 10px 0px;">
		<div class="form-group error-container">
			{!! Form::label('title', Lang::get('account/properties.documents.appraiser')) !!}
			{!! Form::text('title', null, [ 'class'=>'form-control required' ]) !!}
		</div>
		<div class="form-group error-container">
			{!! Form::label('description', Lang::get('account/properties.documents.appraisal.value')) !!}
			{!! Form::text('description', null, [ 'class'=>'form-control required number' ]) !!}
		</div>
		<div class="form-group error-container">
			{!! Form::label('file', Lang::get('account/properties.documents.file')) !!}
			{!! Form::file('file', [ 'class'=>'form-control required' ]) !!}
		</div>
	</div>
	<div class="form-group text-right">
		{!! Form::button(Lang::get('general.cancel'), [ 'class'=>'btn btn-default pull-left document-modal-close' ]) !!}
		{!! Form::button(Lang::get('general.continue'), [ 'class'=>'btn btn-primary', 'type'=>'submit' ]) !!}
	</div>
{!! Form::close() !!}

<script type="text/javascript">
	ready_callbacks.push(function() {
		$('#appraisal-modal-form').validate({
			ignore: '',
			errorPlacement: function(error, element) {
				element.closest('.error-container').append(error);
			},
			submitHandler: function(f) {
				LOADING.show();
				f.submit();
			}
		});
	});
</script>