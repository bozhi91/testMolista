<?php
	$item_id = empty($item) ? 'new' : $item->id;
	$item_key = "items[{$item_id}]";
?>
<div class="menu-item-block">

	<ul class="nav nav-tabs nav-tabs-small locale-tabs" role="tablist">
		<li role="presentation" class="active"><a href="#menu-item-locale-tab-{{$type}}-en-{{$item_id}}" aria-controls="menu-item-locale-tab-{{$type}}-en-{{$item_id}}" role="tab" data-toggle="tab" class="text-uppercase">en</a></li>
		@foreach ($site_setup['locales_tabs'] as $locale => $locale_name)
			@if ( $locale != 'en' )
				<li role="presentation"><a href="#menu-item-locale-tab-{{$type}}-{{$locale}}-{{$item_id}}" aria-controls="menu-item-locale-tab-{{$type}}-{{$locale}}-{{$item_id}}" role="tab" data-toggle="tab" class="text-uppercase">{{$locale}}</a></li>
			@endif
		@endforeach
	</ul>
	<div class="tab-content tab-content-grey translate-area">
		@foreach ($site_setup['locales_tabs'] as $locale => $locale_name)
			<div role="tabpanel" class="tab-pane tab-locale {{ ($locale == 'en') ? 'active' : '' }}" id="menu-item-locale-tab-{{$type}}-{{$locale}}-{{$item_id}}">
				<div class="form-group error-container">
					{!! Form::label("{$item_key}[title][{$locale}]", Lang::get('account/site.menus.update.field.title')) !!}
					{!! Form::text("{$item_key}[title][{$locale}]", @$item->i18n['title'][$locale], [ 'class'=>'input-sm form-control '.(($type == 'custom' && $locale == 'en') ? 'required' : '') ]) !!}
				</div>
				@if ($type == 'custom')
					<div class="form-group error-container">
						{!! Form::label("{$item_key}[url][{$locale}]", Lang::get('account/site.menus.update.field.url')) !!}
						{!! Form::text("{$item_key}[url][{$locale}]", @$item->i18n['url'][$locale], [ 'class'=>'input-sm form-control url '.(($locale == 'en') ? 'required' : '') ]) !!}
					</div>
				@endif
			</div>
		@endforeach
	</div>

	@if ($type == 'page')
		<div class="form-group error-container">
			{!! Form::label("{$item_key}[page_id]", Lang::get('account/site.menus.update.field.page')) !!}
			{!! Form::select("{$item_key}[page_id]", [''=>'']+$pages, @$item->page_id, [ 'class'=>'has-select-2 form-control required' ]) !!}
		</div>

	@elseif ($type == 'property')
		<div class="form-group error-container">
			{!! Form::label("{$item_key}[property_id]", Lang::get('account/site.menus.update.field.property')) !!}
			{!! Form::select("{$item_key}[property_id]", [''=>'']+$properties, @$item->property_id, [ 'class'=>'has-select-2 form-control required' ]) !!}
		</div>
	@endif

	<div class="form-group error-container">
		{!! Form::label("{$item_key}[target]", Lang::get('account/site.menus.update.field.target')) !!}
		{!! Form::select("{$item_key}[target]", [ 
			'' => Lang::get('account/site.menus.update.field.target.self'),
			'_blank' => Lang::get('account/site.menus.update.field.target.new'),
		], @$item->target, [ 'class'=>'form-control' ]) !!}
	</div>

</div>
