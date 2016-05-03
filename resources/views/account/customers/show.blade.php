@extends('layouts.account')

@section('account_content')

	<div id="admin-customers">

		@include('common.messages', [ 'dismissible'=>true ])

		<h1 class="page-title">{{ Lang::get('account/customers.show.h1') }}</h1>

		<ul class="nav nav-tabs main-tabs" role="tablist">
			<li role="presentation" class="active"><a href="#tab-general" aria-controls="tab-general" role="tab" data-toggle="tab">{{ Lang::get('account/customers.show.tab.general') }}</a></li>
		</ul>

		<div class="tab-content">

			<div role="tabpanel" class="tab-pane tab-main active" id="tab-general">
				<div class="row">
					<div class="col-xs-12 col-sm-6">
						<div class="form-group error-container">
							{!! Form::label(null, Lang::get('account/customers.name') ) !!}
							{!! Form::text(null, @$customer->first_name, [ 'class'=>'form-control', 'readonly'=>'readonly' ]) !!}
						</div>
					</div>
					<div class="col-xs-12 col-sm-6">
						<div class="form-group error-container">
							{!! Form::label(null, Lang::get('account/customers.last_name') ) !!}
							{!! Form::text(null, @$customer->last_name, [ 'class'=>'form-control', 'readonly'=>'readonly' ]) !!}
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-sm-6">
						<div class="form-group error-container">
							{!! Form::label(null, Lang::get('account/customers.email') ) !!}
							{!! Form::text(null, @$customer->email, [ 'class'=>'form-control email', 'readonly'=>'readonly' ]) !!}
						</div>
					</div>
					<div class="col-xs-12 col-sm-6">
						<div class="form-group error-container">
							{!! Form::label(null, Lang::get('account/customers.phone') ) !!}
							{!! Form::text(null, @$customer->phone, [ 'class'=>'form-control', 'readonly'=>'readonly' ]) !!}
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-sm-6">
						<div class="form-group error-container">
							{!! Form::label(null, Lang::get('account/customers.locale') ) !!}
							{!! Form::text(null, @$site_setup['locales_select'][$customer->locale], [ 'class'=>'form-control', 'readonly'=>'readonly' ]) !!}
						</div>
					</div>
					<div class="col-xs-12 col-sm-6">
						<div class="form-group error-container">
							{!! Form::label(null, Lang::get('account/customers.created') ) !!}
							{!! Form::text(null, @$customer->created_at->format('d/m/Y'), [ 'class'=>'form-control', 'readonly'=>'readonly' ]) !!}
						</div>
					</div>
				</div>
			</div>

		</div>

		<br />

		<div class="text-right">
			{!! print_goback_button( Lang::get('general.back'), [ 'class'=>'btn btn-default' ]) !!}
		</div>

	</div>

@endsection