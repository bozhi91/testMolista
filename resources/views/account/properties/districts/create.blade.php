@extends('layouts.account')

@section('account_content')

	<div id="admin-districts">

	    @include('common.messages', [ 'dismissible'=>true ])

		<h1 class="page-title">{{ Lang::get('account/properties.districts.create.title') }}</h1>

		{!! Form::model(null, [ 'method'=> 'POST', 'action'=> 'Account\Properties\DistrictsController@postStore', 'id'=>'district-form' ]) !!}

		<div class="custom-tabs">

			<ul class="nav nav-tabs main-tabs" role="tablist">
				<li role="presentation" class="{{ $current_tab == 'general' ? 'active' : '' }}">
					<a href="#tab-general" aria-controls="tab-general" role="tab"
					   data-toggle="tab" data-tab="general">{{ Lang::get('account/properties.districts.general') }}</a>
				</li>	
			</ul>

			<div class="tab-content">
				<div role="tabpanel" class="tab-pane tab-main {{ $current_tab == 'general' ? 'active' : '' }}" id="tab-general">
					 @include('account.properties.districts.general')
				</div>
			</div>
				
			<br />

			<div class="text-right">
				{!! print_goback_button( Lang::get('general.back'), [ 'class'=>'btn btn-default' ]) !!}
				{!! Form::submit( Lang::get('general.continue'), [ 'class'=>'btn btn-primary']) !!}
			</div>
		</div>
		
		{!! Form::close() !!}
	</div>

@endsection