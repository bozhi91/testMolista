<?php
	$item_id = empty($item) ? 'new' : $item->id;
	$item_key = "items[{$item_id}]";
?>

<div class="widget panel panel-custom widget-type-{{$type}} {{ @$widget_class }}" data-type="{{$type}}" data-id="{{$item_id}}">
	<div class="widget-title panel-heading {{ empty($widget_closed) ? '' : 'closed' }}">
		<div class="pull-right cursor-pointer widget-toggle"><span class="caret"></span></div>
		<span class="text">
			@if ( empty($item) || empty($item->title) )
				{{ Lang::get("account/site.widgets.type.{$type}") }}
			@else
				{{ $item->title }}
			@endif
		</span>
	</div>
	<div class="widget-body panel-body" {!! empty($widget_closed) ? '' : 'style="display: none;"' !!}>
		@if ( empty($item) )
			<div class="widget-info help-block">
				{!! Lang::get("account/site.widgets.type.{$type}.info") !!}
			</div>
		@else
			{!! Form::open([ 'method'=>'POST', 'action'=>[ 'Account\Site\WidgetsController@postUpdate', $item->id], 'class'=>'widget-form' ]) !!}
				<div class="widget-configuration">
					<ul class="nav nav-tabs nav-tabs-small locale-tabs" role="tablist">
						<li role="presentation" class="active"><a href="#menu-item-locale-tab-{{$type}}-{{fallback_lang()}}-{{$item_id}}" aria-controls="menu-item-locale-tab-{{$type}}-{{fallback_lang()}}-{{$item_id}}" role="tab" data-toggle="tab" class="text-uppercase">{{fallback_lang()}}</a></li>
						@foreach ($site_setup['locales_tabs'] as $locale => $locale_name)
							@if ( $locale != fallback_lang() )
								<li role="presentation"><a href="#menu-item-locale-tab-{{$type}}-{{$locale}}-{{$item_id}}" aria-controls="menu-item-locale-tab-{{$type}}-{{$locale}}-{{$item_id}}" role="tab" data-toggle="tab" class="text-uppercase">{{$locale}}</a></li>
							@endif
						@endforeach
					</ul>
					<div class="tab-content tab-content-grey translate-area">
						@foreach ($site_setup['locales_tabs'] as $locale => $locale_name)
							<div role="tabpanel" class="tab-pane tab-locale {{ ($locale == fallback_lang()) ? 'active' : '' }}" id="menu-item-locale-tab-{{$type}}-{{$locale}}-{{$item_id}}">
								<div class="form-group error-container">
									{!! Form::label("{$item_key}[title][{$locale}]", Lang::get('account/site.menus.update.field.title')) !!}
									{!! Form::text("{$item_key}[title][{$locale}]", @$item->i18n['title'][$locale], [ 'class'=>'title-input input-sm form-control '.(($type == 'custom' && $locale == fallback_lang()) ? 'required' : ''), 'lang'=>$locale ]) !!}
								</div>
								@if ($type == 'text')
									<div class="form-group error-container">
										{!! Form::label("{$item_key}[content][{$locale}]", Lang::get("account/site.widgets.type.text.content")) !!}
										{!! Form::textarea("{$item_key}[content][{$locale}]", @$item->i18n['content'][$locale], [ 'class'=>'form-control', 'rows'=>4 ]) !!}
									</div>
								@endif
							</div>
						@endforeach
					</div>

					@if ($type == 'menu')
						<div class="form-group error-container">
							{!! Form::label("{$item_key}[menu_id]", Lang::get("account/site.widgets.type.menu.select")) !!}
							{!! Form::select("{$item_key}[menu_id]", [''=>'']+$menus, @$item->menu_id, [ 'class'=>'form-control required' ]) !!}
						</div>
					@endif

				</div>
				<div class="text-right">
					<div class="pull-left">
						<a href="#" data-href="{{ action('Account\Site\WidgetsController@postDelete', $item->id) }}" class="btn btn-sm btn-link btn-widget-delete">{{ Lang::get('general.delete') }}</a>
						|
						<a href="#" class="btn btn-sm btn-link btn-widget-close">{{ Lang::get('general.cancel') }}</a>
					</div>
					{!! Form::submit(Lang::get('general.save'), [ 'class'=>'btn btn-primary btn-sm' ]) !!}
				</div>
			{!! Form::close() !!}

		@endif		

	</div>
</div>
