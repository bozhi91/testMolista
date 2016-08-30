@extends('layouts.web')

@section('content')

<section id="servicios">
	<div class="container">

		<div class="row">
			<div class="col-xs-12 col-sm-12">
				<div class="servicios-title">
					<h1>Servicios</h1>
				</div>
			</div>
		</div>

		@include('common.messages', [ 'dismissible'=>true ])

		<div class="row">
			<div class="col-xs-12 col-sm-12">
				<div class="servicios-subtitle">
					<p>Fincas Bellamar ofrece una amplia gama de servicios inmobiliarios: Compra-Venta, alquiler, administraciones horizontal y vertical, tasaciones, valoraciones, traspasos, seguros, gestión de patrimonios y financiaciones.</p>

					<p>Haga clic en la imagen que quiera para más información.</p>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-xs-12 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">
				<div class="servicios-content">
					<div class="row">
						<div class="servicios-top-content">
							<div class="row">

								<div class="col-xs-12 col-sm-4">
									<div class="servicios-picture-content">
										<a data-toggle="modal" data-target="#ModalCompraVenta" >
											<div class="servicios-picture-circle">
												<img src="/images/themes/bellamar/circulo-1.png" alt="" />
											</div>
											<div class="servicios-picture-circle">
												<h4>Compraventa</h4>
											</div>
										</a>
									</div>
								</div>

								<div class="col-xs-12 col-sm-4">
									<div class="servicios-picture-content">
										<a data-toggle="modal" data-target="#ModalAlquiler">
											<div class="servicios-picture-circle">
												<img src="/images/themes/bellamar/circulo-2.png" alt="" />
											</div>
											<div class="servicios-picture-circle">
												<h4>Alquiler</h4>
											</div>
										</a>
									</div>
								</div>

								<div class="col-xs-12 col-sm-4">
									<div class="servicios-picture-content">
										<a data-toggle="modal" data-target="#ModalPatrimonio">
											<div class="servicios-picture-circle">
												<img src="/images/themes/bellamar/circulo-3.png" alt="" />
											</div>
											<div class="servicios-picture-circle">
												<h4>Gestión de <br /> patrimonio</h4>
											</div>
										</a>
									</div>
								</div>

							</div>
						</div>
						<div class="servicios-bottom-content">
							<div class="row">

								<div class="col-xs-12 col-sm-4">
									<div class="servicios-picture-content">
										<a data-toggle="modal" data-target="#ModalComunidades">
											<div class="servicios-picture-circle">
												<img src="/images/themes/bellamar/circulo-4.png" alt="" />
											</div>
											<div class="servicios-picture-circle">
												<h4>Gestión de <br/> Comunidades</h4>
											</div>
										</a>
									</div>
								</div>

								<div class="col-xs-12 col-sm-4">
									<div class="servicios-picture-content">
										<a href="" target="_blank">
											<div class="servicios-picture-circle">
												<img src="/images/themes/bellamar/circulo-5.png" alt="" />
											</div>
											<div class="servicios-picture-circle">
												<h4>Obra nueva</h4>
											</div>
										</a>
									</div>
								</div>

								<div class="col-xs-12 col-sm-4">
									<div class="servicios-picture-content">
										<a href="" target="_blank">
											<div class="servicios-picture-circle">
												<img src="/images/themes/bellamar/circulo-6.png" alt="" />
											</div>
											<div class="servicios-picture-circle">
												<h4>Servicios Jurídicos</h4>
											</div>
										</a>
									</div>
								</div>

							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>


<!-- Modal 1 | Compra Venta -->
<div class="modal fade" id="ModalCompraVenta" tabindex="-1" role="dialog" aria-labelledby="ModalCompraVenta">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
      	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times" aria-hidden="true"></i></span></button>
      </div>
      <div class="modal-body">
        <div class="row">
        	<div class="col-xs-12 col-sm-6 modal-block-left">
        		<div class="Modal-inner-block-top">
        			<h4>¿Quiere <span class="underline">vender</span> su vivienda?</h4>
        			<div class="block-link">
        				<a role="button" data-toggle="collapse" href="#modal-form-compraventa" aria-expanded="true" aria-controls="modal-form-compraventa" >Clic aquí</a>
        			</div>
        		</div>
        		<div id="modal-form-compraventa" class="panel-collapse collapse" role="tabpanel" aria-labelledby="ModalFormCompraventa">
        			<div class="modal-form-compraventa-inner">
        				<h4 class="title">Por favor, rellene y envíe este formulario:</h4>
	        			<div class="form-compraventa">
	        			<!-- codigo laravel forms. -->
	        				{!! Form::open(['action'=>'Web\Custom\ServiciosController@postForm', 'method'=>'post']) !!}
	        					<input type="hidden" name="tipo" value="venta">
							  <div class="form-group">
							    <input type="nombre" class="form-control" id="nombre" name="nombre" placeholder="Nombre">
							  </div>
							  <div class="form-group">
							    <input type="apellidos" class="form-control" id="apellidos" name="apellidos" placeholder="Apellidos">
							  </div>
							  <div class="form-group">
							    <input type="email" class="form-control" id="email" name="email" placeholder="Email">
							  </div>
							  <div class="form-group">
							    <input type="telefono" class="form-control" id="telefono" name="telefono" placeholder="Teléfono">
							  </div>
							  <div class="form-group">
							  	<?php
								    $tipovivienda = [];
								    foreach ($search_data['types'] as $v)
								    {
								        $tipovivienda[$v] = $v;
								    }
								?>
							    {!! Form::select('tipovivienda', [''=>"Tipo de vivienda"]+$tipovivienda, Input::get('type'), [ 'class'=>'form-control' ]) !!}
							  </div>
							  <div class="form-group">
							    <input type="direccion" class="form-control" id="direccion" name="direccion" placeholder="Dirección">
							  </div>
							  <div class="form-group">
							    <textarea class="form-control" rows="4" id="comentario" name="comentario" placeholder="Comentario..."></textarea>
							  </div>
							  <div class="form-group submit-button">
							  	<button type="submit" class="btn btn-default">Enviar</button>
							  </div>
							{!! Form::close() !!}
						<!-- codigo laravel forms. -->
						</div>
        			</div>
        		</div>
        	</div>
        	<div class="col-xs-12 col-sm-6 modal-block-right">
        		<div class="Modal-inner-block-top">
        			<h4>¿Quiere <span class="underline">comprar</span> una vivienda?</h4>
        			<div class="block-link">
        				<a href="http://fincasbellamar.molista.com/properties?search=1&mode=sale">Clic aquí</a>
        			</div>
        		</div>
        	</div>
        </div>
      </div>
      <div class="modal-footer"></div>
    </div>
  </div>
</div>
<!-- End Modal 1 | Compra Venta -->

<!-- Modal 2 | Alquiler -->
<div class="modal fade" id="ModalAlquiler" tabindex="-1" role="dialog" aria-labelledby="ModalAlquiler">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
      	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times" aria-hidden="true"></i></span></button>
      </div>
      <div class="modal-body">
        <div class="row">
        	<div class="col-xs-12 col-sm-6 modal-block-left">
        		<div class="Modal-inner-block-top">
        			<h4>¿Quiere <span class="underline">alquilar</span> su vivienda?</h4>
        			<div class="block-link">
        				<a role="button" data-toggle="collapse" href="#modal-form-alquiler" aria-expanded="true" aria-controls="modal-form-alquiler" >Clic aquí</a>
        			</div>
        		</div>
        		<div id="modal-form-alquiler" class="panel-collapse collapse" role="tabpanel" aria-labelledby="ModalFormAlquiler">
        			<div class="modal-form-alquiler-inner">
        				<h4 class="title">Por favor, rellene y envíe este formulario:</h4>
	        			<div class="form-alquiler">
	        			<!-- codigo laravel forms. -->
	        				{!! Form::open(['action'=>'Web\Custom\ServiciosController@postForm', 'method'=>'post']) !!}
	        					<input type="hidden" name="tipo" value="alquiler">
							  <div class="form-group">
							    <input type="nombre" class="form-control" id="nombre" name="nombre" placeholder="Nombre">
							  </div>
							  <div class="form-group">
							    <input type="apellidos" class="form-control" id="apellidos" name="apellidos" placeholder="Apellidos">
							  </div>
							  <div class="form-group">
							    <input type="email" class="form-control" id="email" name="email" placeholder="Email">
							  </div>
							  <div class="form-group">
							    <input type="telefono" class="form-control" id="telefono" name="telefono" placeholder="Teléfono">
							  </div>
							  <div class="form-group">
							    {!! Form::select('tipovivienda', [''=>"Tipo de vivienda"]+$search_data['types'], Input::get('type'), [ 'class'=>'form-control' ]) !!}
							  </div>
							  <div class="form-group">
							    <input type="direccion" class="form-control" id="direccion" name="direccion" placeholder="Dirección">
							  </div>
							  <div class="form-group">
							    <textarea class="form-control" rows="4" id="comentario" name="comentario" placeholder="Comentario..."></textarea>
							  </div>
							  <div class="form-group submit-button">
							  	<button type="submit" class="btn btn-default">Enviar</button>
							  </div>
							{!! Form::close() !!}
						<!-- codigo laravel forms. -->
						</div>
        			</div>
        		</div>
        	</div>
        	<div class="col-xs-12 col-sm-6 modal-block-right">
        		<div class="Modal-inner-block-top">
        			<h4>¿Quiere <span class="underline">alquilar</span> una vivienda?</h4>
        			<div class="block-link">
        				<a href="http://fincasbellamar.molista.com/properties?search=1&mode=rent">Clic aquí</a>
        			</div>
        		</div>
        	</div>
        </div>
      </div>
      <div class="modal-footer"></div>
    </div>
  </div>
</div>
<!-- End Modal 2 | Alquiler -->

<!-- Modal 3 | Static Text -->
<div class="modal fade ModalStaticText" id="ModalPatrimonio" tabindex="-1" role="dialog" aria-labelledby="GestionDelPatrimonio">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Gestión del patrimonio</h4>
      </div>
      <div class="modal-body">
        
        <div class="Patrimonio-title">Presupuesto Administración Integral</div>

        <div class="Patrimonio-list">

        	<div class="Patrimonio-list-block">
        		<div class="block-link">
	        		<span class="block-link-title">Texto</span>
	        		<span class="pull-right"><a data-toggle="collapse" href="#PatrimonioInfo1" aria-expanded="true" aria-controls="PatrimonioInfo1">+ info</a></span>
	        	</div>

	        	<div id="PatrimonioInfo1" class="panel-collapse collapse block-text" role="tabpanel">
	        		<ul>
	        			<li> li </li>
	        			<li> li </li>
	        		</ul>
	        	</div>
        	</div>

        	<div class="Patrimonio-list-block">
        		<div class="block-link">
	        		<span class="block-link-title">Texto</span>
	        		<span class="pull-right"><a data-toggle="collapse" href="#PatrimonioInfo2" aria-expanded="true" aria-controls="PatrimonioInfo2">+ info</a></span>
	        	</div>

	        	<div id="PatrimonioInfo2" class="panel-collapse collapse block-text" role="tabpanel">
	        		<ul>
	        			<li> li </li>
	        			<li> li </li>
	        		</ul>
	        	</div>
        	</div>

        	<div class="Patrimonio-list-block">
        		<div class="block-link">
	        		<span class="block-link-title">Texto</span>
	        		<span class="pull-right"><a data-toggle="collapse" href="#PatrimonioInfo3" aria-expanded="true" aria-controls="PatrimonioInfo3">+ info</a></span>
	        	</div>

	        	<div id="PatrimonioInfo3" class="panel-collapse collapse block-text" role="tabpanel">
	        		<ul>
	        			<li> li </li>
	        			<li> li </li>
	        		</ul>
	        	</div>
        	</div>

        </div>

      </div>
      <div class="modal-footer">
      </div>
    </div>
  </div>
</div>

@endsection
