@extends('layouts.web', [
	'body_id' => 'body-account',
	'google_analitics_account' => env('GA_ACCOUNT_BACKOFFICE','UA-79943513-2'),
])

@section('content')
	@include('account.warning.pending-request')

    <?php
    $plan_name = DB::table('sites')
        ->join('plans', 'sites.plan_id', '=', 'plans.id')
        ->select('plans.code')
        ->where('sites.id',session("SiteSetup")['site_id'])
        ->first();
    ?>

    <div id="account-container" class="container">
		<div class="row">
			<div class="col-sm-3 col-md-2 hidden-xs">
				<ul class="nav nav-pills nav-stacked account-menu">

					<li role="presentation">
						<a href="{{ action('WebController@index') }}" target="_blank">
							<i class="account-icon account-icon-web"></i>
							{{ Lang::get('account/menu.web') }}
						</a>
					</li>
					<li class="separator"></li>

					@permission('property-*')
						@if ( (@$submenu_section == 'properties') && $current_site_user->can('property-create') )
							<li role="presentation" class="active">
								<a href="{{ action('Account\PropertiesController@index') }}">
									<i class="account-icon account-icon-property"></i>
									{{ Lang::get('account/menu.properties') }}
								</a>
								<ul id="account-submenu-properties" class="nav" role="menu" aria-labelledby="account-menu-btn-properties">
									<li><a href="{{ action('Account\Properties\ImportsController@getIndex') }}" class="{{ (@$submenu_subsection == 'profile-accounts') ? 'current' : '' }}">{{ Lang::get('account/properties.imports.h1') }}</a></li>
								</ul>
								<ul class="nav" role="menu" aria-labelledby="account-menu-btn-properties">
									<li><a href="{{ action('Account\Properties\DistrictsController@getIndex') }}" class="{{ (@$submenu_subsection == 'profile-accounts') ? 'current' : '' }}">{{ Lang::get('account/properties.districts.h1') }}</a></li>
								</ul>
							</li>
						@else
							<li role="presentation" class="{{ (@$submenu_section == 'properties') ? 'active' : '' }}">
								<a href="{{ action('Account\PropertiesController@index') }}">
									<i class="account-icon account-icon-property"></i>
									{{ Lang::get('account/menu.properties') }}
								</a>
							</li>
						@endif
					@endpermission

					@permission('employee-*')
						<li role="presentation" class="{{ (@$submenu_section == 'employees') ? 'active' : '' }}">
							<a href="{{ action('Account\EmployeesController@index') }}">
								<i class="account-icon account-icon-user"></i>
								{{ Lang::get('account/menu.employees') }}
							</a>
						</li>
					@endpermission

					<li role="presentation" class="{{ (@$submenu_section == 'customers') ? 'active' : '' }}">
						<a href="{{ action('Account\CustomersController@index') }}">
							<i class="account-icon account-icon-lead"></i>
							{{ Lang::get('account/menu.customers') }}
						</a>
					</li>
					<li role="presentation" class="{{ (@$submenu_section == 'tickets') ? 'active' : '' }}">
						<a href="{{ action('Account\TicketsController@getIndex') }}">
							<i class="account-icon account-icon-ticket"></i>
							{{ Lang::get('account/menu.tickets') }}
						</a>
					</li>
					<li role="presentation" class="{{ (@$submenu_section == 'calendar') ? 'active' : '' }}">
						<a href="{{ action('Account\Calendar\BaseController@getIndex') }}">
							<i class="account-icon account-icon-calendar"></i>
							{{ Lang::get('account/menu.calendar') }}
						</a>
					</li>
					<li class="separator"></li>

					<?phpaction('Account\ReportsController@getIndex');?>
					<!-- if the current plan is not premium, do not display the menu bellow. -->
					@if(empty($plan))
						{{$plan=''}}
					@endif

					@role('company')
						@if ( @$submenu_section == 'reports' )
							<li role="presentation" class="active">
								<a onclick="$('#commonModal').modal()" id="dialog" href="{{ action('Account\ReportsController@getIndex') }}">
									<i class="account-icon account-icon-reports"></i>
									{{ Lang::get('account/menu.reports') }}
								</a>

								@if($plan_name->code=="free")
                                    <?php
										$protocol =isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
										$message  = "<p><a href='".$protocol.$_SERVER['HTTP_HOST']."/account/payment/upgrade' target='_blank'>
											 <button type='button' class='btn btn-info .btn-md' style='margin-top:10px !important;'>Actualizar</button>
											 </a></p>";
                                    ?>

                                    @include('Modals.commonModal', ['header'=>"Acceso Denegado!",
                                        'message'=>"No puedes acceder al informe. El informe solo podrá verse
                                         en las versiones de pago. <br>Por favor, actualiza tu plan!<br><br>".$message])
								@else
										<ul id="account-submenu-reports" class="nav" role="menu">
										<li><a href="{{ action('Account\Reports\PropertiesController@getIndex') }}" class="{{ (@$submenu_subsection == 'reports-properties') ? 'current' : '' }}">{{ Lang::get('account/menu.reports.properties') }}</a></li>
										<li><a href="{{ action('Account\Reports\AgentsController@getIndex') }}" class="{{ (@$submenu_subsection == 'reports-agents') ? 'current' : '' }}">{{ Lang::get('account/menu.reports.agents') }}</a></li>
										<li><a href="{{ action('Account\Reports\LeadsController@getIndex') }}" class="{{ (@$submenu_subsection == 'reports-leads') ? 'current' : '' }}">{{ Lang::get('account/menu.reports.leads') }}</a></li>
										<li><a href="{{ action('Account\Reports\ReferersController@getIndex') }}" class="{{ (@$submenu_subsection == 'reports-referers') ? 'current' : '' }}">{{ Lang::get('account/menu.reports.referers') }}</a></li>
										</ul>
								@endif
							</li>
						@else
							<li role="presentation">
								<a href="{{ action('Account\ReportsController@getIndex') }}">
									<i class="account-icon account-icon-reports"></i>
									{{ Lang::get('account/menu.reports') }}
								</a>
							</li>
						@endif
						<li class="separator"></li>
					@endrole

					@if ( Auth::user()->hasRole('company') || Auth::user()->can('site-*') )
						@permission('site-*')
							<li role="presentation" class="{{ (@$submenu_section == 'site') ? 'active' : '' }}">
								<a href="javascript:;" id="account-menu-btn-site" data-toggle="collapse" data-target="#account-submenu-site" aria-expanded="false" class="{{ (@$submenu_section == 'site') ? '' : 'collapsed' }}">
									<i class="account-icon account-icon-settings"></i>
									{{ Lang::get('account/menu.site') }}
								</a>
								<ul id="account-submenu-site" class="nav {{ (@$submenu_section == 'site') ? '' : 'collapse' }}" role="menu" aria-labelledby="account-menu-btn-site">
									<li><a href="{{ action('Account\Site\ConfigurationController@getIndex') }}" class="{{ (@$submenu_subsection == 'site-configuration') ? 'current' : '' }}">{{ Lang::get('account/menu.site.configuration') }}</a></li>
									<li><a href="{{ action('Account\Site\DomainNameController@getIndex') }}" class="{{ (@$submenu_subsection == 'site-domainname') ? 'current' : '' }}">{{ Lang::get('account/menu.site.domainname') }}</a></li>
									<li><a href="{{ action('Account\Site\PriceRangesController@getIndex') }}" class="{{ (@$submenu_subsection == 'site-priceranges') ? 'current' : '' }}">{{ Lang::get('account/menu.site.priceranges') }}</a></li>
									<li><a href="{{ action('Account\Site\CountriesController@getIndex') }}" class="{{ (@$submenu_subsection == 'site-countries') ? 'current' : '' }}">{{ Lang::get('account/menu.site.countries') }}</a></li>
									<li><a href="{{ action('Account\Site\WidgetsController@getIndex') }}" class="{{ (@$submenu_subsection == 'site-widgets') ? 'current' : '' }}">{{ Lang::get('account/menu.site.widgets') }}</a></li>
									<li><a href="{{ action('Account\Site\MenusController@index') }}" class="{{ (@$submenu_subsection == 'site-menus') ? 'current' : '' }}">{{ Lang::get('account/menu.site.menus') }}</a></li>
									<li><a href="{{ action('Account\Site\PagesController@index') }}" class="{{ (@$submenu_subsection == 'site-pages') ? 'current' : '' }}">{{ Lang::get('account/menu.site.pages') }}</a></li>
									<li><a href="{{ action('Account\Site\SlidersController@index') }}" class="{{ (@$submenu_subsection == 'site-sliders') ? 'current' : '' }}">{{ Lang::get('account/menu.site.sliders') }}</a></li>

									<li><a href="{{ action('Account\Site\BlogController@listPosts')}}" class="{{ (@$submenu_subsection == 'site-blog') ? 'current' : '' }}">Blog</a></li>
                                </ul>
							</li>
						@endpermission
						@role('company')
							<li role="presentation" class="{{ (@$submenu_section == 'marketplaces') ? 'active' : '' }}">
								<a href="{{ action('Account\MarketplacesController@getIndex') }}">
									<i class="account-icon account-icon-marketplaces"></i>
									{{ Lang::get('account/menu.marketplaces') }}
								</a>
							</li>
						@endrole
						<li class="separator"></li>
					@endif
					@if ( @$submenu_section == 'profile' )
						<li role="presentation" class="active">
							<a href="{{ action('AccountController@index') }}">
								<i class="account-icon account-icon-info"></i>
								{{ Lang::get('account/menu.data') }}
							</a>
							<ul id="account-submenu-profile" class="nav" role="menu" aria-labelledby="account-menu-btn-profile">
								<li><a href="{{ action('Account\Profile\AccountsController@getIndex') }}" class="{{ (@$submenu_subsection == 'profile-accounts') ? 'current' : '' }}">{{ Lang::get('account/menu.data.accounts') }}</a></li>
								<li><a href="{{ action('Account\Profile\SignaturesController@getIndex') }}" class="{{ (@$submenu_subsection == 'profile-signatures') ? 'current' : '' }}">{{ Lang::get('account/menu.data.signatures') }}</a></li>
							</ul>
						</li>
					@else
						<li role="presentation">
							<a href="{{ action('AccountController@index') }}">
								<i class="account-icon account-icon-info"></i>
								{{ Lang::get('account/menu.data') }}
							</a>
						</li>
					@endif
					<!-- Custom Link -->
					<li class="separator"></li>
					<li style="position:relative;">
						<a href="{{ action('Account\ReportsController@getUserData') }}">
							<i class="account-icon glyphicon glyphicon-cog"></i>
							Benefíciate de Garantify.com
							<div
							style="	position: absolute;top: 6px;left: -6px;
									color: white;
									background: red;
									padding: 3px;
									font-size: 10px;">nuevo</div>
						</a>
					</li>

					<!-- Custom Link -->
					<li role="presentation">
						<a href="{{ action('Account\ReportsController@getIndex') }}">
							<i class="account-icon account-icon-logout_2"></i>
							{{ Lang::get('web/header.logout') }}
						</a>
					</li>
				</ul>
			</div>
			<div class="col-xs-12 col-sm-9 col-md-10">

				@yield('account_content')

				<?php
					$url = isset($_SERVER['HTTPS']) ? "https" : "http";
					$url = $url."://".$_SERVER['HTTP_HOST']."/account/payment/upgrade";
                	$protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
				?>

				@if($plan_name->code=="free" &&  @$submenu_subsection == 'site-blog')
					@include("account.site.entradas.limitedPlan",['url'=>$url])

                    <?php $message  = " <p><a href='".$protocol.$_SERVER['HTTP_HOST']."/account/payment/upgrade' target='_blank'>
								<button type='button' class='btn btn-info .btn-md' style='margin-top:10px !important;'>".Lang::get('account/properties.update')."</button>
								</a></p>";
                    ?>
				@endif

				@if($plan_name->code=="free" &&  @$submenu_section == 'reports')
					@include('Modals.commonModal', ['header'=>Lang::get('account/properties.accessDenied'),
                                 'message'=> Lang::get('account/properties.propMessage_3').$message])
				@endif

				@if($plan=="free" &&  @$submenu_section == 'reports' )
					@include("account.reports.limitedPlan",['url'=>$url])
				@endif

			</div>
		</div>
    </div>

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
                TICKETS.cont.on('click', '.#dialog', function(e){
                        e.preventDefault();

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

		ready_callbacks.push(function() {
			var account_menu = $('#account-container .account-menu');
			var header_menu = $('#header .header-menu-search-trigger');
			var locale_menu = $('#header .header-locale-social');

			//Display modal dialog when try to access the reports in free plan.
			$("#dialog").click(function(e){
                e.preventDefault();
			    e.stopPropagation();
                $('#commonModal').modal();
            });

			if({{$submenu_section=="reports"}}) $('#commonModal').modal();

            // Hide header menu
			header_menu.find('>li').addClass('hidden-xs');

			// Create account menu items
			account_menu.find('>li').each(function(){
				var el = $(this);

				// Is separator?
				if ( el.hasClass('separator') ) {
					return true;
				}

				var lnk = el.find('>a').eq(0);
				var item = $('<li class="visible-xs"></li>');

				// Has submenu
				if ( el.find('ul').length > 0 ) {
					item.addClass('dropdown');

					var html = '<a href="#" class="main-item dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">' + lnk.text() + ' <span class="caret"></span></a>';
						html += '<ul class="dropdown-menu">';
						el.find('ul').each(function(){
							html += $(this).html();
						});
						html += '</ul>';

					item.html(html);

				} else {
					item.html('<a href="' + lnk.attr('href') + '" class="main-item">' + lnk.text() + '</a>');
				}

				header_menu.append(item);
			});

			// Hide social media
			locale_menu.find('.social-media').addClass('hidden-xs');
		});

	</script>

@endsection
