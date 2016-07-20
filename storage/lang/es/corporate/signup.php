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
		'user.new.email.error' => 'El email ya está registrado',
		'user.new.password' => 'Contraseña',
		'user.new.password.placeholder' => 'Escribe tu contraseña',
		'user.new.accept' => 'Estoy de acuerdo con los términos legales',
		'user.new.have.account' => 'Ya tengo una cuenta',
		'user.new.phone' => 'Teléfono',
		'user.new.phone.placeholder' => 'Escribe tu teléfono',

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
		'site.transfer' => 'Quiero contratar el servicio de traspaso web :cost',

		'payment.h2' => 'Método de pago',
		'payment.choose' => 'Elije el método de pago',
		'payment.iban' => 'Cuenta IBAN',

		'invoicing.h2' => 'Datos de facturación',
		'invoicing.type.individual' => 'Persona',
		'invoicing.type.company' => 'Empresa',
		'invoicing.company' => 'Nombre empresa',
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
		'finish.h2.ready' => 'El sitio está listo',
		'finish.congratulations' => '<p>Felicitaciones, tu página web está disponible en <strong>:website_url</strong></p>',
		'finish.pay' => '<p>Por favor complete el pago.</p>',
		'finish.gotoweb' => 'Ir a mi página web',
		'finish.gotoaccount' => 'Ir al backoffice',
		'finish.warning.links' => '<p>Para acceder al backoffice debes ingresar los datos del usuario que usaste para crear este sitio.</p>
									<p>Una pista: el email que usaste fue :owner_email</p>',
		'finish.plan.details' => '<ul>
									<li>Plan: :plan</li>
									<li>:price_text</li>
								</ul>',
		'finish.stripe.button' => 'Pagar ahora',
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
		'finish.stripe.current.version' => '<p>Tendrás la versión :plan habilitada hasta que pagues el plan elegido.</p>
											<p>Puedes pagarlo ahora o después desde tu backoffice</p>
											¿Necesitas ayuda?<br />
											T: +93 488 52 23<br />
											E: <a href="mailto:soporte@molista.com" target="_blank">soporte@molista.com</a><br />
											L - V de 10 a 18 h',
		'finish.our.help' => 'Cuentas con nuestra ayuda:<br />
								T: +93 488 52 23<br />
								E: <a href="mailto:soporte@molista.com" target="_blank">soporte@molista.com</a><br />
								L - V de 10 a 18 h',

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

		'full.h1' => 'Contratación',
		'full.intro' => '<p>Por favor, rellena el siguiente formulario para contratar tu plan con <strong>molista</strong>.</p>',
		'full.data.title' => 'Datos de registro',
		'full.plan.title' => 'Elije el plan molista que deseas contratar',
		'full.site.warning' => 'Puedes definir tu propio dominio "www.midominio.com" una vez registrado',
		'full.site.optional' => 'Opcional',
		'full.site.transfer' => 'Contratar el servicio de traspaso de mi web',
		'full.invoicing.title' => 'Rellena tus datos',
		'full.invoicing.please' => 'Por favor, selecciona',
		'full.summary.title' => 'Tu pedido',
		'full.summary.total' => 'Total a pagar',
		'full.summary.button' => 'Contratar',
		'full.help.title' => '¿Necesitas ayuda?',
		'full.help.email' => 'soporte@molista.com',
		'full.help.time' => 'L-V de 10:00 a 18:00 h',

	];