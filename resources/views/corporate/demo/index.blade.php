@extends('layouts.corporate')

@section('content')

	<div id="demo-page" >

		<div class="demo-intro">
			<div class="container">
				<div class="row">
					<div class="col-xs-12 col-sm-5">
						<h2>{{ Lang::get('corporate/demo.intro.title') }}</h2>
						{!! Lang::get('corporate/demo.intro.text') !!}
						<div class="btn-area">
							<a href="http://demo.Contromia.com" target="_blank" class="btn btn-demo-site">{{ Lang::get('corporate/general.demo') }}</a>
						</div>

						<div class="xs-spacer"></div>
					</div>
					<div class="col-xs-12 col-sm-6 col-sm-offset-1">
						<a href="http://demo.Contromia.com" target="_blank"><img src="{{ asset('images/corporate/responsive.png') }}" class="img-responsive" alt="" /></a>
					</div>
				</div>
			</div>
		</div>

		<div class="steps-intro">
			<div class="container">
				<div class="row">
					<div class="col-xs-12 col-sm-6 col-sm-offset-3">
						<h2>{{ Lang::get('corporate/demo.features.title') }}</h2>
					</div>
				</div>
			</div>
		</div>

		<div class="container">
			<div class="steps-block">
				<div class="row">
					<div class="col-xs-12 col-sm-4 col-sm-offset-4">
						<h2 style="background-image: url('{{ asset('images/corporate/demo/properties-h2.png') }}')">{{ Lang::get('corporate/demo.properties.title') }}</h2>
						<div class="intro-text">{{ Lang::get('corporate/demo.properties.text') }}</div>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-sm-6">
						<div class="image-block">
							<img src="{{ asset('images/corporate/demo/properties-01.jpg') }}" alt="" />
						</div>
					</div>
					<div class="col-xs-12 col-sm-6">
						<div class="image-block">
							<img src="{{ asset('images/corporate/demo/properties-02.jpg') }}" alt="" />
						</div>
					</div>
					<div class="col-xs-12 col-sm-6">
						<div class="image-block">
							<img src="{{ asset('images/corporate/demo/properties-03.jpg') }}" alt="" />
						</div>
					</div>
					<div class="col-xs-12 col-sm-6">
						<div class="image-block">
							<img src="{{ asset('images/corporate/demo/properties-04.jpg') }}" alt="" />
						</div>
					</div>
				</div>
			</div>


			<div class="steps-block">
				<div class="row">
					<div class="col-xs-12 col-sm-6">
						<h2 style="background-image: url('{{ asset('images/corporate/demo/agents-h2.png') }}')">{{ Lang::get('corporate/demo.agents.title') }}</h2>
						<div class="intro-text">{{ Lang::get('corporate/demo.agents.text') }}</div>
						<div class="visible-xs image-block">
							<img src="{{ asset('images/corporate/demo/agents-01.jpg') }}" alt="" />
						</div>
						<div class="xs-spacer"></div>
					</div>
					<div class="col-xs-12 col-sm-6">
						<h2 style="background-image: url('{{ asset('images/corporate/demo/leads-h2.png') }}')">{{ Lang::get('corporate/demo.leads.title') }}</h2>
						<div class="intro-text">{{ Lang::get('corporate/demo.leads.text') }}</div>
						<div class="visible-xs image-block">
							<img src="{{ asset('images/corporate/demo/leads-01.jpg') }}" alt="" />
						</div>
					</div>
				</div>
				<div class="row hidden-xs">
					<div class="col-xs-12 col-sm-6">
						<div class="image-block">
							<img src="{{ asset('images/corporate/demo/agents-01.jpg') }}" alt="" />
						</div>
					</div>
					<div class="col-xs-12 col-sm-6">
						<div class="image-block">
							<img src="{{ asset('images/corporate/demo/leads-01.jpg') }}" alt="" />
						</div>
					</div>
				</div>
			</div>

			<div class="steps-block">
				<div class="row">
					<div class="col-xs-12 col-sm-6 col-sm-offset-3">
						<h2 style="background-image: url('{{ asset('images/corporate/demo/tickets-h2.png') }}')">{{ Lang::get('corporate/demo.tickets.title') }}</h2>
						<div class="intro-text">{{ Lang::get('corporate/demo.tickets.text') }}</div>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-sm-6">
						<div class="image-block">
							<img src="{{ asset('images/corporate/demo/tickets-01.jpg') }}" alt="" />
						</div>
					</div>
					<div class="col-xs-12 col-sm-6">
						<div class="image-block">
							<img src="{{ asset('images/corporate/demo/tickets-02.jpg') }}" alt="" />
						</div>
					</div>
				</div>
			</div>

			<div class="steps-block">
				<div class="row">
					<div class="col-xs-12 col-sm-6">
						<h2 style="background-image: url('{{ asset('images/corporate/demo/reports-h2.png') }}')">{{ Lang::get('corporate/demo.reports.title') }}</h2>
						<div class="intro-text">{{ Lang::get('corporate/demo.reports.text') }}</div>
						<div class="visible-xs image-block">
							<img src="{{ asset('images/corporate/demo/reports-01.jpg') }}" alt="" />
						</div>
						<div class="xs-spacer"></div>
					</div>
					<div class="col-xs-12 col-sm-6">
						<h2 style="background-image: url('{{ asset('images/corporate/demo/exports-h2.png') }}')">{{ Lang::get('corporate/demo.exports.title') }}</h2>
						<div class="intro-text">{{ Lang::get('corporate/demo.exports.text') }}</div>
						<div class="visible-xs image-block">
							<img src="{{ asset('images/corporate/demo/exports-01.jpg') }}" alt="" />
						</div>
					</div>
				</div>
				<div class="row hidden-xs">
					<div class="col-xs-12 col-sm-6">
						<div class="image-block">
							<img src="{{ asset('images/corporate/demo/reports-01.jpg') }}" alt="" />
						</div>
					</div>
					<div class="col-xs-12 col-sm-6">
						<div class="image-block">
							<img src="{{ asset('images/corporate/demo/exports-01.jpg') }}" alt="" />
						</div>
					</div>
				</div>
			</div>

			<div class="steps-block">
				<div class="row">
					<div class="col-xs-12 col-sm-6">
						<h2 style="background-image: url('{{ asset('images/corporate/demo/calendar-h2.png') }}')">{{ Lang::get('corporate/demo.calendar.title') }}</h2>
						<div class="intro-text">{{ Lang::get('corporate/demo.calendar.text') }}</div>
						<div class="visible-xs image-block">
							<img src="{{ asset('images/corporate/demo/calendar-01.jpg') }}" alt="" />
						</div>
						<div class="xs-spacer"></div>
					</div>
					<div class="col-xs-12 col-sm-6">
					</div>
				</div>
				<div class="row hidden-xs">
					<div class="col-xs-12 col-sm-6">
						<div class="image-block">
							<img src="{{ asset('images/corporate/demo/calendar-01.jpg') }}" alt="" />
						</div>
					</div>
					<div class="col-xs-12 col-sm-6">
					</div>
				</div>
			</div>

		</div>

		<div id="home">
			@include('corporate.common.home-fourth-block', [
				'demo_link' => 'http://demo.Contromia.com/',
				'demo_target' => '_blank',
			])
		</div>

	</div>

	<script type="text/javascript">
		ready_callbacks.push(function(){
			var cont = $('#demo-page');
		});
	</script>

@endsection
