<?php
	return [
		'payment_failed.subject' => 'Molista: ERROR al recibir el pago',
		'payment_failed.body' => '<p>Se ha producido un error en un pago de stripe para el siguiente site de Molista:</p>
									<ul>
									<li>ID: :site_id</li>
									<li>Subdomain: :subdomain</li>
									<li>Válido hasta: :created</li>
									</ul>',

		'payment_succeeded.subject' => 'Confirmación de pago recibido - :webname',
		'payment_succeeded.body' => '<p>Hola :name:</p>
										<p>Te informamos que hemos recibido correctamente el pago de tu plan :plan, correspondiente al período comprendido entre el :start y el :end.</p>',

		'payment_failed_warning.subject' => 'Error al realizar pago - :webname',
		'payment_failed_warning.body' => '<p>Hola :name:</p>
										<p>Te informamos que no hemos podido procesar el pago de tu plan :plan de :webname.</p>
										<p>El próximo :next_attempt realizaremos un nuevo intento de pago.</p>',

		'payment_failed_final.subject' => 'Error crítico al realizar pago - :webname',
		'payment_failed_final.body' => '<p>Hola :name:</p>
										<p>Te informamos que, tras muchos intentos, no hemos podido procesar el pago de tu plan :plan de :webname, por lo que tu web ":sitename" ha sido desactivada.</p>
										<p>Si deseas volver a activarla, por favor contacta con nosotros.</p>',

	];
