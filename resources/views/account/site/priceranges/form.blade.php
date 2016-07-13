@if ( @$form_item )
	{!! Form::model($form_item, [ 'action'=>$form_action, 'id'=>$form_id, 'class'=>'mfp-hide app-popup-block-white pricerange-item-form' ]) !!}
@else
	{!! Form::open([ 'action'=>$form_action, 'id'=>$form_id, 'class'=>'mfp-hide app-popup-block-white pricerange-item-form' ]) !!}
@endif

	{!! Form::hidden('id', null) !!}
	{!! Form::hidden('type', @$form_type) !!}

	<h4>{{ $form_title }}</h4>

	<div class="form-group title-area">
		{!! Form::label(null, Lang::get('account/priceranges.form.title')) !!}
		<ul class="nav nav-tabs locale-tabs pull-right" role="tablist">
			<li role="presentation" class="active"><a href="#{{ $form_id }}-lang-{{ fallback_lang() }}" aria-controls="lang-{{ fallback_lang() }}" role="tab" data-toggle="tab">{{ fallback_lang() }}</a></li>
			@foreach ($site_setup['locales_tabs'] as $lang_iso => $lang_name)
				@if ( $lang_iso != fallback_lang() )
					<li role="presentation"><a href="#{{ $form_id }}-lang-{{ $lang_iso }}" aria-controls="lang-{{ $lang_iso }}" role="tab" data-toggle="tab">{{ $lang_iso }}</a></li>
				@endif
			@endforeach
		</ul>
		<div class="tab-content">
			@foreach ($site_setup['locales_tabs'] as $lang_iso => $lang_name)
				<div role="tabpanel" class="tab-pane tab-locale {{ $lang_iso == fallback_lang() ? 'active' : '' }}" id="{{ $form_id }}-lang-{{ $lang_iso }}">
					<div class="error-container">
						{!! Form::text("i18n[title][{$lang_iso}]", null, [ 'class'=>'form-control'.(($lang_iso == fallback_lang()) ? ' required' : '') ]) !!}
					</div>
				</div>
			@endforeach
		</div>
	</div>

	<div class="row">
		<div class="col-xs-12 col-sm-6">
			<div class="form-group error-container">
				{!! Form::label(null, Lang::get('account/priceranges.form.from')) !!}
				<div class="input-group">
					{!! Form::text('from', null, [ 'class'=>'form-control digits', 'min'=>'0' ]) !!}
					<div class="input-group-addon">{{ price_symbol('EUR') }}</div>
				</div>
			</div>
		</div>
		<div class="col-xs-12 col-sm-6">
			<div class="form-group error-container">
				{!! Form::label(null, Lang::get('account/priceranges.form.till')) !!}
				<div class="input-group">
					{!! Form::text('till', null, [ 'class'=>'form-control digits', 'min'=>'0' ]) !!}
					<div class="input-group-addon">{{ price_symbol('EUR') }}</div>
				</div>
			</div>
		</div>
	</div>

	<div class="text-right">
		<a href="#" class="btn btn-default trigger-popup-close">{{ Lang::get('general.cancel') }}</a>
		{!! Form::button(Lang::get('general.continue'), [ 'type'=>'submit', 'class'=>'btn btn-primary' ]) !!}
	</div>

{!! Form::close() !!}
