<?php
	return [
		'plan.h1' => 'Plan contratado',
		'plan.show' => 'Ver planes',
		'plan.upgrade' => 'Cambiar plan',
		'plan.upgrade.simple' => 'Upgrade',

		'method.h1' => 'Método de pago',
		'method.stripe' => 'Tarjeta de crédito',
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


		'invoices' => 'Mis facturas',
		'invoices.empty' => 'No se encontraron facturas.',
		'invoices.uploaded_at' => 'Fecha',
		'invoices.reference' => 'Referencia',
		'invoices.amount' => 'Monto',
	];
