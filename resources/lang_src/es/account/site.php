<?php

	return [
	    'planactual'=>'Plan Actual: ',
        'Update' => 'Actualizar',

	    'subscription.expired_1'=>'Tu subscripción ha expirado hace 1 dia. Si no actualizas tu plan en 48 horas, te bajaremos al plan free.',
        'subscription.expired_2'=>'Tu subscripción ha expirado hace 3 dias. Si no renuevas tu plan, te bajaremos a plan free a: ',
        'subscription.expired_3'=>'Tu subscripción ha expirado. No has renovado tu plan, por lo tanto has sido bajado al plan Free',

        'blog.inactive' => 'El blog ha sido creado, pero todavía no es accesible.
                            Para activarlo, crea una nueva entrada desde el menu',
        'blog.createBlog' => 'Crear el blog',
        'blog.createPost' => 'Crear una entrada',
        'blog.emptyBlog' => 'El blog no está creado todavía. Para crear uno, haga click en el botón de arriba.',
        'blog.emptyPost' => 'No se encontraron entradas en este blog',
        'blog.delete' => '¿Confirma que desea eliminar esta entrada?',
		'configuration.h1' => 'Configuración del sitio',
		'configuration.tab.config' => 'General',
		'configuration.tab.texts' => 'SEO',
		'configuration.tab.social' => 'Redes sociales',
		'configuration.tab.mail' => 'Emails',
		'configuration.tab.signature' => 'Firma',
		'configuration.tab.theme' => 'Diseño',

		'configuration.languages' => 'Idiomas habilitados',
		'configuration.languages.error' => 'Seleccione al menos un idioma',
		'configuration.subdomain' => 'Subdominio',
		'configuration.subdomain.error' => 'Este subdominio está en uso',
		'configuration.subdomain.alpha' => 'Sólo letras números y guiones',
		'configuration.theme' => 'Tema del sitio',
		'configuration.theme.preview' => 'Ver muestra',
		'configuration.theme.preview.error' => 'Por favor seleccione un tema para ver una muestra',
		'configuration.theme.preview.home' => 'Portada',
		'configuration.theme.preview.product' => 'Propiedad',
		'configuration.theme.install' => 'Seleccionar',
		'configuration.theme.pages.showing' => 'Mostrando :from - :to de :total resultados',
		'configuration.logo' => 'Logo',
		'configuration.logo.helper' => '<p>Aceptamos imagenes en formato JPG, PNG y GIF.</p><p>No se subirán imágenes con peso superior a :IMAGE_MAXSIZE kilobytes.</p>',
		'configuration.favicon' => 'Favicon',
		'configuration.favicon.helper' => '<p>Sólo formato ICO.</p>',

		'configuration.title' => 'Título',
		'configuration.subtitle' => 'Subtítulo',
		'configuration.description' => 'Descripción',

		'configuration.domains' => 'Dominio personalizado',
		'configuration.domains.error' => 'Este dominio ya se encuentra en uso',

		'configuration.currency' => 'Moneda del sitio',
		'configuration.currency.warning' => 'Si cambias la moneda, todas las propiedades y estadísticas usarán la nueva moneda. ¿Estás seguro de que deseas cambiarla?',

		'configuration.mailing.out' => 'Envío de emails',
		'configuration.mailing.in' => 'Recepción de emails',
		'configuration.mailing.protocol' => 'Protocolo',
		'configuration.mailing.default' => 'Default',
		'configuration.mailing.default.help' => '<p>Si utiliza el sistema de envío de emails por default, es posible que sus emails acaben en la bandeja de correo no deseado.</p>',
		'configuration.mailing.mandrill' => 'Mandrill',
		'configuration.mailing.mandrill.user' => 'Mandrill username',
		'configuration.mailing.mandrill.key' => 'Mandrill API key',
		'configuration.mailing.mandrill.host' => 'Mandrill host',
		'configuration.mailing.mandrill.port' => 'Mandrill port',
		'configuration.mailing.mandrill.help' => '<p>Ingrese el username, API key, host y port de Mandrill.</p>',
		'configuration.mailing.smtp' => 'Custom',
		'configuration.mailing.smtp.login' => 'Login',
		'configuration.mailing.smtp.pass' => 'Password',
		'configuration.mailing.smtp.pass' => 'SMTP host',
		'configuration.mailing.smtp.pass' => 'SMTP port',
		'configuration.mailing.smtp.help' => '<p>Ingrese el login, contraseña, host y puerto para la conexión SMTP.</p>',
		'configuration.mailing.pop3.help' => '<p>Ingrese el login, contraseña, host y puerto para la conexión POP3.</p>',
		'configuration.mailing.current' => 'En uso',
		'configuration.mailing.from.name' => 'Nombre remitente',
		'configuration.mailing.from.email' => 'Email remitente',
		'configuration.mailing.test.email.subject' => 'Prueba de configuración de email',
		'configuration.mailing.test.email.content' => '<p>Esta es una prueba de su configuración de email.</p><p>Si ha recibido este email, su configuración funciona correctamente.</p>',
		'configuration.mailing.test.button' => 'Probar configuración guardada',
		'configuration.mailing.test.email' => 'Email destinatario',
		'configuration.mailing.test.success' => 'La configuración funciona correctamente',
		'configuration.mailing.test.error' => 'Ocurrió un error al comprobar la configuración',
		'configuration.mailing.mailgun.username' => 'Username',
		'configuration.mailing.mailgun.help' => '<p>Ingrese el host, username y password para Mailgun.</p>',
		'configuration.mailing.test.changed' => 'La configuración ha cambiado. Por favor guarde los cambios antes de realizar la prueba.',

		'configuration.signature.name' => 'Nombre de la empresa',
		'configuration.signature.phone' => 'Teléfono de empresa',
		'configuration.signature.email' => 'Email de empresa',
		'configuration.signature.address' => 'Dirección principal',

		'configuration.client.register' => 'Permitir registro de clientes online',
		'configuration.timezone' => 'Zona horaria',

		'configuration.saved' => 'La configuración se ha guardado correctamente',

		'configuration.ga.account' => 'ID de seguimiento de Google Analytics',
		'configuration.ga.account.helper' => 'Por ejemplo: UA-123456-1',
		'configuration.ga.account.error' => 'El formato del código de seguimiento no es válido',

		'configuration.hide.molista' => 'Ocultar logos de :webname',
		'configuration.hide.molista.helper' => 'Esta opción sólo está disponible para los planes Plus',

		'configuration.tab.alerts' => 'Alertas',
		'configuration.alerts.price.down' => 'Notificar de bajada de precio',
		'configuration.alerts.agents' => 'Agentes',
		'configuration.alerts.customers' => 'Leads',
		
		'configuration.home.highlights.label' => 'Propiedades destacadas home',
		'configuration.home.highlights.group.3' => 'Mostrar en grupos de 3',
		'configuration.home.highlights.group.6' => 'Mostrar en grupos de 6',
		'configuration.home.highlights.group.9' => 'Mostrar en grupos de 9',
		'configuration.home.highlights.group.all' => 'Mostrar todas',

		'configuration.recaptcha.enabled.title' => 'Activar reCAPTCHA de Google',
		'configuration.recaptcha.enabled.helper' => 'Si deseas activar una comprobación en los formularios de contacto, activa el reCAPTCHA',
		'configuration.recaptcha.sitekey.title' => 'Clave del sitio',
		'configuration.recaptcha.sitekey.helper' => 'Introduce la clave del sitio proporcionada por el reCAPTCHA de Google',
		'configuration.recaptcha.secretkey.title' => 'Clave secreta',
		'configuration.recaptcha.secretkey.helper' => 'Introduce la Clave secreta proporcionada por el reCAPTCHA de Google',
		'configuration.recaptcha.example' => 'Ejemplo: ',

		'menus.h1' => 'Menúes',

		'menus.links.title' => 'Opciones de enlaces',
		'menus.links.custom' => 'Enlace personalizado',
		'menus.links.properties' => 'Enlace a propiedades',
		'menus.links.pages' => 'Enlace a páginas',
		'menus.links.button' => 'Añadir al menú',

		'menus.tabs.new' => 'Crear menú',

		'menus.create.name' => 'Nombre',
		'menus.create.intro' => '<p>Para crear un menú, dele un nombre y haga click en Crear menú. Luego podrá seleccionar los items que desea añadir desde la columna de la izquierda.</p>
								<p>Cuando haya finalizado la construcción del menú, asegúrese de hacer click en el botón Guardar menú.</p>',

		'menus.create.button' => 'Crear menú',
		'menus.create.success' => 'El menú se ha creado correctamemte',

		'menus.update.button' => 'Guardar menú',
		'menus.update.field.title' => 'Nombre',
		'menus.update.field.url' => 'URL',
		'menus.update.field.page' => 'Página',
		'menus.update.field.property' => 'Propiedad',
		'menus.update.field.target' => 'Destino',
		'menus.update.field.target.self' => 'Misma ventana',
		'menus.update.field.target.new' => 'Nueva ventana',
		'menus.update.success' => 'El menú se ha guardado correctamemte',
		'menus.update.items.warning.save' => '¡Recuerde hacer click en Guardar menú para guardar sus cambios!',
		'menus.update.items.warning.delete' => '¿Confirma que desea eliminar este elemento?',

		'menus.delete.button' => 'Eliminar menú',
		'menus.delete.warning' => '¿Confirma que desea eliminar este menú?',
		'menus.delete.success' => 'El menú se ha eliminado correctamemte',

		'menus.items.created' => 'El item se ha eliminado correctamemte',



		'widgets.h1' => 'Widgets',

		'widgets.available' => 'Widgets disponibles',

		'widgets.group.header' => 'Page header',
		'widgets.group.footer' => 'Page footer',
		'widgets.group.home' => 'Page home',
		'widgets.group.home-footer' => 'Home footer',

		'widgets.type.menu' => 'Menú personalizado',
		'widgets.type.menu.info' => 'Añada uno de sus menúes personalizados como widget.',
		'widgets.type.menu.select' => 'Seleccionar menú',
		'widgets.type.text' => 'Bloque de texto',
		'widgets.type.text.info' => 'Añada un bloque de texto como widget.',
		'widgets.type.text.content' => 'Texto',
		'widgets.type.slider' => 'Slider',
		'widgets.type.slider.info' => 'Añada un slider para tu página de início.',
		'widgets.type.slider.select' => 'Seleccionar slider',

		'widgets.type.awesome-link' => 'Link personalizado',
		'widgets.type.awesome-link.info' => 'Añada multiples links personalizados a tu página principal.',
		'widgets.type.awesome-link.color' => 'Color',
		'widgets.type.awesome-link.link' => 'Enlace',
		'widgets.type.awesome-link.file' => 'Imagen',
		'widgets.type.awesome-link.help' => 'Imagen tiene que tener el tamaño máximo de 100x100 pixels',
		
		'widgets.messages.created' => 'El widget se ha creado correctamente',
		'widgets.messages.updated' => 'El widget se ha guardado correctamente',
		'widgets.messages.delete.warning' => '¿Confirma que desea eliminar este elemento?',
		'widgets.messages.deleted' => 'El widget se ha eliminado correctamente',
		'widgets.messages.not.accepted' => 'El tipo de widget no es aceptable en esta área',
		'widgets.messages.max.reached' => 'Ha alcanzado el número máximo de widgets permitido en esta área',



		'pages.h1' => 'Páginas',
		'pages.empty' => 'No se encontraron páginas',
		'pages.button.new' => 'Crear página',

		'pages.column.title' => 'Título',
		'pages.column.type' => 'Tipo',

		'pages.delete.warning' => '¿Confirma que desea eliminar esta página?',

		'pages.create.title' => 'Crear página',
		'pages.edit.title' => 'Editar página',

		'pages.tab.general' => 'Contenido',
		'pages.tab.seo' => 'SEO',

		'pages.type.default' => 'Default',
		'pages.type.contact' => 'Formulario de contacto',
		'pages.type.map' => 'Mapa de localización',

		'pages.title' => 'Título',
		'pages.body' => 'Contenido',
		'pages.seo_title' => 'Título',
		'pages.seo_description' => 'Descripción',
		'pages.seo_keywords' => 'Keywords',

		'pages.configuration.contact.email' => 'Email de contacto',
		'pages.configuration.contact.email.helper' => 'Los formularios serán enviados a este email',
		'pages.configuration.contact.phone.required' => 'Teléfono obligatorio',

		'pages.configuration.map.lat' => 'Latitud',
		'pages.configuration.map.lng' => 'Longitud',
		'pages.configuration.map.zoom' => 'Nivel de zoom',
		'pages.configuration.map.button' => 'Buscar dirección',
		'pages.configuration.map.address' => 'Dirección',
		'pages.configuration.map.address.helper' => 'Calle, Código postal, Ciudad, País',
		'pages.configuration.map.geolocate' => 'Geolocalizar',

		'pages.create.success' => 'La página se ha creado correctamente',
		'pages.update.success' => 'La página se ha guardado correctamente',
		'pages.deleted.success' => 'La página se ha eliminado correctamente',

		'sliders.h1' => 'Sliders',
		'sliders.empty' => 'No se encontraron sliders',
		'sliders.button.new' => 'Crear slider',

		'sliders.create.title' => 'Crear slider',
		'sliders.edit.title' => 'Editar slider',

		'sliders.tab.general' => 'Contenido',
		'sliders.column.title' => 'Título',
		'sliders.column.languages' => 'Idiomas',
		'sliders.select.alllanguages' => 'Todos',
		'sliders.label.title' => 'Título',
		'sliders.label.languages' => 'Idiomas',
		'sliders.label.link' => 'Enlace',

		'sliders.general.sliders' => 'Slides',
		'sliders.general.empty' => 'El slider no tiene imágenes',
		'sliders.upload' => 'Subir slides',

		'sliders.update.success' => 'El slider se ha guardado correctamente',
		'sliders.create.success' => 'El slider se ha creado correctamente',
		'sliders.deleted.success' => 'El slider se ha eliminado correctamente',

		'sliders.delete.warning' => '¿Confirma que desea eliminar este slider?',


		'domainname.h1' => 'Nombre de dominio',
		'domainname.domain' => 'Dominio',
		'domainname.domain.helper' => '<p>El nombre de dominio no lo da Molista. Debes comprarlo previamente en cualquiera de las empresas dedicadas a vender nombres de dominio.</p>
										<p>Indica el nombre de dominio de tu página (por ejemplo, tutienda.com) y apúntalo a la IP: 46.101.105.169 (debes ajustar esta redirección con tu proveedor de dominios).</p>
										<p>El ajuste de redirección puede tardar hasta 48 horas.</p>
										<p>Si tienes dudas, pregunta a tu proveedor de dominio cómo gestionar el dominio y redireccionarlo.</p>',
		'domainname.domain.error' => 'El nombre de dominio ya está en uso',
	];
