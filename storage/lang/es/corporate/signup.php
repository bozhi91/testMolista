<?php
	return [
		'h1' => 'Tu web inmobiliaria',

		'previous' => 'Anterior',
		'next' => 'Siguiente',
		'accept' => 'Aceptar',

		'user.new.h2' => 'Crear cuenta',
		'user.new.name' => 'Nombre y apellidos',
		'user.new.name.placeholder' => 'Escribe tu nombre y apellidos',
		'user.new.email' => 'Email',
		'user.new.email.placeholder' => 'Escribe tu email',
		'user.new.password' => 'Contraseña',
		'user.new.password.placeholder' => 'Escribe tu contraseña',
		'user.new.accept' => 'Estoy de acuerdo con los términos legales',
		'user.new.have.account' => 'Ya tengo una cuenta',

		'user.old.h2' => '¿Listo para empezar?',
		'user.old.no.account' => 'No tengo cuenta.',
		'user.old.create.account' => 'Crear cuenta',
		'user.old.password.forgot' => '¿Olvidaste tu contraseña?',
		'user.old.password.click' => 'Haz click aquí',
		'user.old.error.combination' => 'Los datos enviados no son correctos.',
		'user.old.error.employee' => 'Este usuario ya está registrado como agente.',
		'user.old.error.admin' => 'Este usuario ya está registrado como administrador de molista.',

		'pack.h2' => 'Elige el plan que deseas contratar',

		'site.h2' => 'Detalles de tu página molista',
		'site.subdomain' => 'Elije el nombre de tu página',
		'site.subdomain.sample' => 'La dirección de tu página molista será:',
		'site.language' => 'Elije el idioma de tu página',

		'payment.h2' => 'Método de pago',
		'payment.choose' => 'Elije el método de pago',
		'payment.iban' => 'Cuenta IBAN',

		'invoicing.h2' => 'Datos de facturación',
		'invoicing.type.individual' => 'Persona',
		'invoicing.type.company' => 'Empresa',
		'invoicing.first_name' => 'Nombre',
		'invoicing.last_name' => 'Apellidos',
		'invoicing.email' => 'Email',
		'invoicing.tax_id' => 'ID fiscal',
		'invoicing.address' => 'Dirección',
		'invoicing.street' => 'Calle',
		'invoicing.zipcode' => 'Código postal',
		'invoicing.city' => 'Ciudad',
		'invoicing.country' => 'País',
		'invoicing.coupon' => 'Cupón o código promocional',
		'invoicing.coupon.have' => '¿Tiene un cupón o código promocional?',
		'invoicing.coupon.use' => 'Canjear cupón',
		'invoicing.coupon.error' => 'El código promocional no es válido',

		'confirm.h2' => 'Resumen',
		'confirm.change' => 'Cambiar',
		'confirm.name' => 'Nombre',
		'confirm.plan' => 'Plan',
		'confirm.subdomain' => 'Nombre de la página',

		'finish.h2' => 'El sitio se ha creado correctamente',
		'finish.congratulations' => '<p>Felicitaciones, tu página web está disponible en <strong>:website_url</strong></p>',
		'finish.gotoweb' => 'Ir a mi página web',
		'finish.gotoaccount' => 'Ir al backoffice',
		'finish.warning.links' => '<p>Para acceder al backoffice debes ingresar los datos del usuario que usaste para crear este sitio.</p>
									<p>Una pista: el email que usaste fue :owner_email</p>',
		'finish.plan.details' => '<ul>
									<li>Plan: :plan</li>
									<li>:price_text</li>
								</ul>',
		'finish.stripe.warnings' => '<p>Para procesar nuestros pagos usamos Stripe, una pasarela de pago segura que utiliza diversas herramientas para evitar el fraude con tarjetas de crédito. Para más información sobre stripe, visite <a href="https://stripe.com" target="_blank">www.stripe.com</a>.</p>',
		'finish.transfer.intro' => '<p>En este momento estamos confirmando los datos de pago que nos has indicado para habilitar el plan :plan (:price_text). Te avisaremos cuando los hayamos comprobado.</p>
									<p>Mientras tanto, comienza a preparar tu página web disfrutando de nuestro plan Free:</p>
									<ul>
										<li data-type="fixa">Fichas de inmuebles</li>
										<li data-type="searcher">Buscador de viviendas con filtros avanzados</li>
										<li data-type="relation">Posibilidad de relacionar inmuebles</li>
										<li data-type="easy">Fácil de usar y gestionar</li>
										<li data-type="settings">Personalizable</li>
										<li data-type="responsive">Compatible con móviles y tabletas</li>
									</ul>',

		'email.subject' => 'Bienvenid@ a molista',
		'email.hello' => '<p>Hola :name,</p>',
		'email.features.intro.now' => '<p>Ya puedes comenzar a disfrutar de todas las ventajas que <strong>molista</strong> ofrece para que tu inmobiliaria esté presente en internet:</p>',
		'email.features.intro.stripe' => '<p>¡Estás a sólo un paso!</p>
											<p>Sólo tienes que completar el pago del plan :plan (:priceperiod) que solicitaste vía :paymethod. Puedes hacerlo desde el backoffice de tu página web.</p>
											<p>Completa el pago y comienza a disfrutar de todas las ventajas que <strong>molista</strong> ofrece para que tu inmobiliaria esté presente en internet:</p>',
		'email.features.intro.transfer' => '<p>¡Estás a sólo un paso!</p>
											<p>En este momento estamos confirmando los datos de pago que nos has indicado para habilitar el plan :plan (:priceperiod). Te avisaremos cuando los hayamos comprobado.</p>
											<p>Mientras tanto, comienza a preparar tu página web disfrutando de nuestro plan Free:</p>',
		'email.features.list' => '<ul>
									<li>Fichas de tus inmuebles</li>
									<li>Buscador de viviendas con filtros avanzados</li>
									<li>Posibilidad de relacionar inmuebles</li>
									<li>Fácil de usar y gestionar</li>
									<li>Personalizable</li>
									<li>Compatible con móviles y tabletas</li>
								</ul>',
		'email.url.site' => '<p>Esta es la url de tu página web: :site_url</p>',
		'email.url.account' => '<p>Y estos son los datos de acceso al backoffice:</p>
								<ul>
									<li>URL: :account_url</li>
									<li>Email: :email</li>
									<li>Contraseña: ******</li>
								</ul>',
		'email.warning.stripe' => '<p><small>* Si al leer este email ya has completado el pago, olvida este mensaje.</span></p>',

		'email.admin.subject' => 'Molista: solicitud de pago vía domiciliación',
		'email.admin.body' => '<p>Se ha recibido una solicitud de pago por transferencia bancaria en Molista:</p>
								<ul>
									<li>ID: :site_id</li>
									<li>Subdomain: :subdomain</li>
									<li>Creación: :created</li>
								</ul>',
	];