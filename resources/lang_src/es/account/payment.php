<?php
	return [
		'plan.h1' => 'Plan contratado',
		'plan.show' => 'Ver planes',
		'plan.upgrade' => 'Cambiar plan',
		'plan.upgrade.simple' => 'Upgrade',
		'plan.price' => 'Precio',
		'plan.valid.from' => 'Fecha inicio',
		'plan.next.charge' => 'Siguiente cobro',
		'plan.last.charge.attempt' => 'Último intento de cobro',
		'plan.last.charge.warning' => '<p>Todos los intentos de realizar el pago han fracasado.</p>
										<p>Por favor actualice los datos de su tarjeta e intente nuevamente realizar el pago</p>
										<p>Si el problema persiste, contacte con nosotros.</p>',

		'method.h1' => 'Método de pago',
		'method.stripe' => 'Tarjeta de crédito',
		'method.stripe.update' => 'Actualizar',
		'method.stripe.retry' => 'Reintentar pago',
		'method.transfer' => 'Domiciliación bancaria',
		'method.change' => 'Cambiar método',
		'method.account' => 'Cuenta bancaria',

		'upgrade.select' => 'Seleccione su plan',
		'upgrade.success.plan' => 'El plan se ha actualizado correctamente.',
		'upgrade.success.payment' => 'Los datos de pago se han actualizado correctamente.',

		'data' => 'Mis datos',

		'plans' => 'Mi plan',
		'plans.pending.transfer' => '<p>Aún estamos confirmando los datos de pago que nos indicaste para el upgrade de tu plan:</p>
									<ul>
										<li>Plan: :plan</li>
										<li>:paymethod</li>
									</ul>
									<p>Contactaremos contigo cuanto antes.</p>',
		'plans.pending.stripe' => '<p>Tienes una solicitud de upgrade pendiente:</p>
									<ul>
										<li>Plan: :plan</li>
										<li>:paymethod</li>
									</ul>',
		'plans.pending.button' => 'Pagar ahora',
		'plans.pending.cancel' => 'Cancelar solicitud',
		'plans.cancel.warning' => '¿Confirma que desea cancelar esta solicitud?',
		'plans.cancel.success' => 'La solicitud se canceló correctamente',

		'invoices' => 'Mis facturas',
		'invoices.empty' => 'No se encontraron facturas.',
		'invoices.uploaded_at' => 'Fecha',
		'invoices.reference' => 'Referencia',
		'invoices.amount' => 'Monto',

		'invoicing.title' => 'Información facturación',
		'invoicing.created' => 'La solicitud de cambio de plan está en proceso',
		'invoicing.updated' => 'El cambio de plan se ha realizado correctamente',
		'invoicing.transfer.created' => '<h4>Gracias por elegir la forma de pago domiciliado.</h4><p>Hemos recibido todos sus datos para iniciar las gestiones necesarias.</p><p>En cuanto esté todo listo, nos pondremos en contacto con usted.</p>',
		'invoicing.transfer.intro' => '<p>En este momento estamos confirmando los datos de pago que nos has indicado para habilitar el plan :plan (:price_text). Te avisaremos cuando los hayamos comprobado.</p>',

		'cc.update.title' => 'Actualizar tarjeta de crédito',
		'cc.update.intro' => '<p>Para mayor seguridad, no guardamos información de su tarjeta de crédito.</p>
								<p>Para procesar nuestros pagos usamos Stripe, una pasarela de pago segura que utiliza diversas herramientas para evitar el fraude con tarjetas de crédito.</p>
								<p>Para más información sobre stripe, visite <a href="https://stripe.com" target="_blank">www.stripe.com</a>.</p>',
		'cc.update.button' => 'Continuar',
		'cc.update.label' => 'Actualizar tarjeta',
		'cc.update.success' => 'Tu tarjeta de crédito se actualizó correctamente',

		'retry.title' => 'Reintentar pago última factura',
		'retry.paid' => 'La última factura ya se encuentra pagada',
		'retry.success' => 'La última factura ya ha pagado con éxito',
		'retry.error' => 'Ocurrió un error al pagar la última factura',
		'retry.empty' => 'No se encontró ninguna factura asociada a esta cuenta',
	];
