@extends('layouts.web')

@section('content')

	<script type="text/javascript">
		var TICKETS = {
			cont: null,

			options: {},

			init: function(sel, ops) {
				TICKETS.cont = $(sel);

				if ( ops ) {
					TICKETS.options = $.extend(TICKETS.options, ops);
				}

				TICKETS.cont.on('click', '.edit-ticket-trigger', function(e){
					e.preventDefault();

					if ( url = $(this).data().href ) {
						$.magnificPopup.open({
							items: {
								src: url + '?ajax=1'
							},
							type: 'iframe'
						});
					}
				});
			},

			reload: function() {
				if ( !TICKETS.cont ) return;

				if ( TICKETS.cont.find('.pagination li.active').length ) {
					TICKETS.cont.load( TICKETS.cont.find('.pagination li.active').data().url );
				} else if ( TICKETS.cont.data().url ) {
					TICKETS.cont.load( TICKETS.cont.data().url );
				}
			}
		};
	</script>

    <div id="account-container" class="container">
		<div class="row">
			<div class="col-sm-3 col-md-2 hidden-xs">
				<ul class="nav nav-pills nav-stacked account-menu">
					<li role="presentation" class="{{ (@$submenu_section == 'tickets') ? 'active' : '' }}">
						<a href="{{ action('Account\TicketsController@getIndex') }}">{{ Lang::get('account/menu.tickets') }}</a>
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
					@role('company')
						<li role="presentation" class="{{ (@$submenu_section == 'reports') ? 'active' : '' }}">
							<a href="javascript:;" data-toggle="collapse" data-target="#account-submenu-reports" aria-expanded="false" class="{{ (@$submenu_section == 'reports') ? '' : 'collapsed' }}">
								{{ Lang::get('account/menu.reports') }}
							</a>
							<ul id="account-submenu-reports" class="nav {{ (@$submenu_section == 'reports') ? '' : 'collapse' }}" role="menu">
								<li><a href="{{ action('Account\Reports\PropertiesController@getIndex') }}" class="{{ (@$submenu_subsection == 'reports-properties') ? 'current' : '' }}">{{ Lang::get('account/menu.reports.properties') }}</a></li>
								<li><a href="{{ action('Account\Reports\AgentsController@getIndex') }}" class="{{ (@$submenu_subsection == 'reports-agents') ? 'current' : '' }}">{{ Lang::get('account/menu.reports.agents') }}</a></li>
								<li><a href="{{ action('Account\Reports\LeadsController@getIndex') }}" class="{{ (@$submenu_subsection == 'reports-leads') ? 'current' : '' }}">{{ Lang::get('account/menu.reports.leads') }}</a></li>
							</ul>
						</li>
					@endrole
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
					<li role="presentation" class="{{ (@$submenu_section == 'home') ? 'active' : '' }}">
						<a href="{{ action('AccountController@index') }}">{{ Lang::get('account/menu.data') }}</a>
					</li>
				</ul>
			</div>
			<div class="col-xs-12 col-sm-9 col-md-10">
				@yield('account_content')
			</div>
		</div>
    </div>

@endsection
