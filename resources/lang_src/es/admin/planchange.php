<?php
	return [
		'empty' => 'No se encontraron solicitudes',

		'site' => 'Site',
		'plan' => 'Plan',
		'payment.interval' => 'Pago',
		'payment.method' => 'Método',
		'status' => 'Status',
		'created' => 'Fecha',
		'paid.amount' => 'Cantidad pagada',
		'paid.from' => 'Marcar como válido desde',
		'paid.until' => 'Marcar como válido hasta',

		'edit.title' => 'Payment request',
		'edit.request' => 'Solicitud',
		'edit.history' => 'Historial',
		'edit.history.empty' => 'No se encontraron datos históricos',
		'edit.data.current' => 'Datos actuales',
		'edit.data.requested' => 'Cambios solicitados',

		'button.accept' => 'Aceptar solicitud',
		'button.reject' => 'Rechazar solicitud',

		'message.rejected' => 'La solicitud ha sido rechazada',
		'message.accepted' => 'La solicitud ha sido aceptada',

		'reject.reason' => 'Motivo rechazo',
		'reject.reason.helper' => '<p>Se incluirá en el email que se envía al dueño del sitio.</p><p>El email se enviará en :language.</p>',
		'reject.subject' => 'Su solicitud de cambio de plan ha sido rechazada',
		'reject.body' => '<p>Hola :username,</p>
							<p>Sentimos comunicarte que la solicitud para el cambio de plan de tu sitio :siteurl ha sido rechazada.</p>
							<div>:reason</div>',
		'accept.subject' => 'Su solicitud de cambio de plan se ha realizado correctamente',
		'accept.body' => '<p>Hola :username,</p>
							<p>Nos complace informarte que tu solicitud de cambio de plan se ha realizado correctamente.</p>
							<p>A partir de ahora ya puedes disfrutar de tu nuevo plan:</p>
							<ul>
								<li>Plan: :plan</li>
								<li>:payment</li>
								<li>Url: :siteurl</li>
							</ul>',
	];
