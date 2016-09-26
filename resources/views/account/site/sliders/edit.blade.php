@extends('layouts.account')

@section('account_content')

<div id="admin-pages">

	@include('common.messages', [ 'dismissible'=>true ])

	<h1 class="page-title">{{ Lang::get('account/site.sliders.edit.title') }}</h1>

	{!! Form::model(null, [ 'method'=>'PATCH', 'action'=> ['Account\Site\SlidersController@update', $sliderGroup->id], 'id'=>'sliders-form' ]) !!}

	<div class="custom-tabs">
		<ul class="nav nav-tabs main-tabs" role="tablist">
			<li role="presentation" class="active">
				<a href="#tab-general" aria-controls="tab-general" role="tab" data-toggle="tab">
					{{ Lang::get('account/site.sliders.tab.general') }}
				</a>
			</li>
		</ul>

		<div class="tab-content">
			<div role="tabpanel" class="tab-pane tab-main active" id="tab-general">
				@include('account.site.sliders.general', [ 
					'group' => $sliderGroup,
					'languages' => $languages,
					'languagesValues' => $languagesCurrent,
				])
			</div>
		</div>

		<br />

		<div class="text-right">
			{!! print_goback_button( Lang::get('general.back'), [ 'class'=>'btn btn-default' ]) !!}
			{!! Form::submit( Lang::get('general.continue'), [ 'class'=>'btn btn-primary']) !!}
		</div>

		<br />

	</div>

	{!! Form::close() !!}
</div>

@endsection
