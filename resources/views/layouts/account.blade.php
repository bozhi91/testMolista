@extends('layouts.web')

@section('content')

    <div id="account-container" class="container">
		<div class="row">
			<div class="col-sm-3 col-md-2 hidden-xs">
				<ul class="nav nav-pills nav-stacked account-menu">
					<li role="presentation" class="{{ (@$submenu_section == 'home') ? 'active' : '' }}">
						<a href="{{ action('AccountController@index') }}">{{ Lang::get('account/menu.data') }}</a>
					</li>
					@permission('property-*')
						<li role="presentation" class="{{ (@$submenu_section == 'properties') ? 'active' : '' }}">
							<a href="{{ action('Account\PropertiesController@index') }}">{{ Lang::get('account/menu.properties') }}</a>
						</li>
					@endpermission
					@permission('employee-*')
						<li role="presentation" class="{{ (@$submenu_section == 'employees') ? 'active' : '' }}">
							<a href="{{ action('Account\EmployeesController@index') }}">{{ Lang::get('account/menu.employees') }}</a>
						</li>
					@endpermission
					<li role="presentation" class="{{ (@$submenu_section == 'customers') ? 'active' : '' }}">
						<a href="{{ action('Account\CustomersController@index') }}">{{ Lang::get('account/menu.customers') }}</a>
					</li>
					@permission('site-*')
						<li role="presentation" class="{{ (@$submenu_section == 'site') ? 'active' : '' }}">
							<a href="javascript:;" id="account-menu-btn-site" data-toggle="collapse" data-target="#account-submenu-site" aria-expanded="false" class="{{ (@$submenu_section == 'site') ? '' : 'collapsed' }}">
								{{ Lang::get('account/menu.site') }}
							</a>
							<ul id="account-submenu-site" class="nav {{ (@$submenu_section == 'site') ? '' : 'collapse' }}" role="menu" aria-labelledby="account-menu-btn-site">
								<li><a href="{{ action('Account\Site\ConfigurationController@getIndex') }}" class="{{ (@$submenu_subsection == 'site-configuration') ? 'current' : '' }}">{{ Lang::get('account/menu.site.configuration') }}</a></li>
								<li><a href="{{ action('Account\Site\WidgetsController@getIndex') }}" class="{{ (@$submenu_subsection == 'site-widgets') ? 'current' : '' }}">{{ Lang::get('account/menu.site.widgets') }}</a></li>
								<li><a href="{{ action('Account\Site\MenusController@index') }}" class="{{ (@$submenu_subsection == 'site-menus') ? 'current' : '' }}">{{ Lang::get('account/menu.site.menus') }}</a></li>
								<li><a href="{{ action('Account\Site\PagesController@index') }}" class="{{ (@$submenu_subsection == 'site-pages') ? 'current' : '' }}">{{ Lang::get('account/menu.site.pages') }}</a></li>
							</ul>
						</li>
					@endpermission
				</ul>
			</div>
			<div class="col-xs-12 col-sm-9 col-md-10">
				@yield('account_content')
			</div>
		</div>
    </div>

@endsection
