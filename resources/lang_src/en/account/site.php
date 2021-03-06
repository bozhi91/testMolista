<?php

	return [
        'blog.notAllowed'=>'Not allowed in your plan. To use this service, please update to plan Enterprise.',

        'blog.inactive' => 'The blog was created but is still inactive.
                            To activate it, create a new entrance from the menu.',

        'blog.createBlog' => 'Create a new Blog',
        'blog.createPost' => 'Create a new Post',
        'blog.emptyBlog'  => 'The blog is not created yet. To create one, click on the button above.',
        'blog.emptyPost'  => 'There are no posts for this blog',
        'blog.delete' => 'Are you sure you want to delete this post?',

        'planactual'=>'Current Plan: ',
        'Update' => 'Update',

        'subscription.expired_1' =>'Your subscription has expired 1 day ago. Your plan will be downgraded to Free of you dont renew your subscription in 48 hours. Please update your plan.',
        'subscription.expired_2'=>'Your plan has expired 3 days ago. If you dont renew your subscription, youll be downgraded to Free plan tomorrow at: ' ,
        'subscription.expired_3'=>'your subscription has expired. You didnt renew it so you were downgraded to free plan.',

        'configuration.h1' => 'Site configuration',
		'configuration.tab.config' => 'General',
		'configuration.tab.texts' => 'SEO',
		'configuration.tab.social' => 'Social media',
		'configuration.tab.mail' => 'Emails',
		'configuration.tab.signature' => 'Signature',
		'configuration.tab.theme' => 'Theme',

		'configuration.languages' => 'Enabled languages',
		'configuration.languages.error' => 'Please select at least one language',
		'configuration.subdomain' => 'Subdomain',
		'configuration.subdomain.error' => 'This subdomain is already taken',
		'configuration.subdomain.alpha' => 'Letters, numbers, hypen and underscores only please',
		'configuration.theme' => 'Website theme',
		'configuration.theme.preview' => 'Show preview',
		'configuration.theme.preview.error' => 'Please first select a theme to see its preview',
		'configuration.theme.preview.home' => 'Home',
		'configuration.theme.preview.product' => 'Property',
		'configuration.theme.install' => 'Select',
		'configuration.theme.pages.showing' => 'Showing :from - :to of :total results',
		'configuration.logo' => 'Logo',
		'configuration.logo.helper' => '<p>We support JPG, PNG and GIF formats.</p><p>Images bigger than :IMAGE_MAXSIZE kilobytes will not be uploaded.</p>',
		'configuration.favicon' => 'Favicon',
		'configuration.favicon.helper' => '<p>Only ICO formats.</p>',

		'configuration.title' => 'Title',
		'configuration.subtitle' => 'Subtitle',
		'configuration.description' => 'Description',

		'configuration.domains' => 'Custom domain',
		'configuration.domains.error' => 'This domain is already taken',

		'configuration.currency' => 'Website currency',
		'configuration.currency.warning' => 'If you change the website currency, all your properties and stats will use the new currency. Are you sure you wish to change it?',

		'configuration.mailing.out' => 'Sending emails',
		'configuration.mailing.in' => 'Retriving emails',
		'configuration.mailing.protocol' => 'Protocol',
		'configuration.mailing.default' => 'Default',
		'configuration.mailing.default.help' => '<p>If you use the default method, emails might end up in the spam folder.</p>',
		'configuration.mailing.mandrill' => 'Mandrill',
		'configuration.mailing.mandrill.user' => 'Mandrill username',
		'configuration.mailing.mandrill.key' => 'Mandrill API key',
		'configuration.mailing.mandrill.host' => 'Mandrill host',
		'configuration.mailing.mandrill.port' => 'Mandrill port',
		'configuration.mailing.mandrill.help' => '<p>Please provide username, API key, host and port for Mandrill.</p>',
		'configuration.mailing.smtp' => 'Custom',
		'configuration.mailing.smtp.login' => 'Login',
		'configuration.mailing.smtp.pass' => 'Password',
		'configuration.mailing.smtp.host' => 'SMTP host',
		'configuration.mailing.smtp.port' => 'SMTP port',
		'configuration.mailing.smtp.help' => '<p>Please provide the login, password, host and port for the SMTP connection.</p>',
		'configuration.mailing.pop3.help' => '<p>Please provide the login, password, host and port for the POP3 connection.</p>',
		'configuration.mailing.current' => 'In use',
		'configuration.mailing.from.name' => 'Sender name',
		'configuration.mailing.from.email' => 'Sender email',
		'configuration.mailing.test.button' => 'Test stored configuration',
		'configuration.mailing.test.email' => 'Receiver email',
		'configuration.mailing.test.email.subject' => 'Email configuration test',
		'configuration.mailing.test.email.content' => '<p>This is a test for your email configuration.</p><p>If you receive this email, it means your configuration works as expected.</p>',
		'configuration.mailing.test.success' => 'The configuration was successfully tested',
		'configuration.mailing.test.error' => 'An error occurred while testing the configuration',
		'configuration.mailing.mailgun.username' => 'Username',
		'configuration.mailing.mailgun.help' => '<p>Please provide the host, username and password for Mailgun.</p>',
		'configuration.mailing.test.changed' => 'The email configuration has changed. Please save your changes before performing the test.',

		'configuration.signature.name' => 'Company name',
		'configuration.signature.phone' => 'Company phone',
		'configuration.signature.email' => 'Company email',
		'configuration.signature.address' => 'Business address',

		'configuration.client.register' => 'Allow online customer registration',
		'configuration.timezone' => 'Timezone',

		'configuration.saved' => 'The site configuration was successfully saved',

		'configuration.ga.account' => 'Google Analytics tracking ID',
		'configuration.ga.account.helper' => 'Example: UA-123456-1',
		'configuration.ga.account.error' => 'The Google Analytics tracking ID format is invalid',

		'configuration.hide.molista' => 'Hide :webname logos',
		'configuration.hide.molista.helper' => 'This option is only available for site using the Plus plan',

		'configuration.tab.alerts' => 'Alerts',
		'configuration.alerts.price.down' => 'Notify for property\'s price fall',
		'configuration.alerts.agents' => 'Agents',
		'configuration.alerts.customers' => 'Customers',
		
		'configuration.home.highlights.label' => 'Home highlighted properties',
		'configuration.home.highlights.group.3' => 'Show in groups of 3',
		'configuration.home.highlights.group.6' => 'Show in groups of 6',
		'configuration.home.highlights.group.9' => 'Show in groups of 9',
		'configuration.home.highlights.group.all' => 'Show all',

		'configuration.recaptcha.enabled.title' => 'Activate Google reCAPTCHA',
		'configuration.recaptcha.enabled.helper' => 'Checks if the forms are filled by humans',
		'configuration.recaptcha.sitekey.title' => 'Site Key',
		'configuration.recaptcha.sitekey.helper' => 'Enter the site key provided by the reCAPTCHA of Google',
		'configuration.recaptcha.secretkey.title' => 'Secret Key',
		'configuration.recaptcha.secretkey.helper' => 'Enter the secret key provided by the reCAPTCHA of Google',
		'configuration.recaptcha.example' => 'Example: ',

		'menus.h1' => 'Menus',

		'menus.links.title' => 'Links options',
		'menus.links.custom' => 'Custom link',
		'menus.links.properties' => 'Property link',
		'menus.links.pages' => 'Page link',
		'menus.links.button' => 'Add to menu',

		'menus.tabs.new' => 'Create menu',

		'menus.create.name' => 'Menu name',
		'menus.create.intro' => '<p>To create a custom menu, give it a name above and click Create Menu. Then choose items like properties, pages or custom links from the left column to add to this menu.</p>
								<p>After you have added your items, drag and drop to put them in the order you want. You can also click each item to reveal additional configuration options.</p>
								<p>When you have finished building your custom menu, make sure you click the Save Menu button.</p>',
		'menus.create.button' => 'Create menu',
		'menus.create.success' => 'The menu was successfully created',

		'menus.update.button' => 'Save menu',
		'menus.update.field.title' => 'Label',
		'menus.update.field.url' => 'URL',
		'menus.update.field.page' => 'Page',
		'menus.update.field.property' => 'Property',
		'menus.update.field.target' => 'Target',
		'menus.update.field.target.self' => 'Same window',
		'menus.update.field.target.new' => 'New window',
		'menus.update.success' => 'The menu was successfully updated',
		'menus.update.items.warning.save' => 'Make sure you click the Save menu button to save all your changes!',
		'menus.update.items.warning.delete' => 'Are you sure you want to remove this element?',

		'menus.delete.button' => 'Delete menu',
		'menus.delete.warning' => 'Are you sure you want to remove this menu?',
		'menus.delete.success' => 'The menu was successfully deleted',

		'menus.items.created' => 'The item was successfully deleted',



		'widgets.h1' => 'Widgets',

		'widgets.available' => 'Available widgets',

		'widgets.group.header' => 'Page header',
		'widgets.group.footer' => 'Page footer',
		'widgets.group.home' => 'Page home',
		'widgets.group.home-footer' => 'Home footer',

		'widgets.type.menu' => 'Custom menu',
		'widgets.type.menu.info' => 'Add one of your custom menus as a widget.',
		'widgets.type.menu.select' => 'Select menu',
		'widgets.type.text' => 'Text block',
		'widgets.type.text.info' => 'Add one block of text as a widget.',
		'widgets.type.text.content' => 'Text',
		'widgets.type.slider' => 'Slider',
		'widgets.type.slider.info' => 'Add a slider to your home page.',
		'widgets.type.slider.select' => 'Select slider',

		'widgets.type.awesome-link' => 'Custom link',
		'widgets.type.awesome-link.info' => 'Add multiples custom links to your home page.',
		'widgets.type.awesome-link.color' => 'Color',
		'widgets.type.awesome-link.link' => 'Link',
		'widgets.type.awesome-link.file' => 'Image',
		'widgets.type.awesome-link.help' => 'Image must have 100x100 pixels max size',
		
		'widgets.messages.created' => 'The widget was successfully created',
		'widgets.messages.updated' => 'The widget was successfully updated',
		'widgets.messages.delete.warning' => 'Are you sure you want to remove this element?',
		'widgets.messages.deleted' => 'The widget was successfully removed',
		'widgets.messages.not.accepted' => 'The type of widget is not accepted in this area',
		'widgets.messages.max.reached' => 'You have reached the maximun number of widgets accepted for this area',



		'pages.h1' => 'Pages',
		'pages.empty' => 'No pages found',
		'pages.button.new' => 'Create page',

		'pages.column.title' => 'Title',
		'pages.column.type' => 'Type',

		'pages.delete.warning' => 'Are you sure you want to remove this page?',

		'pages.create.title' => 'Create page',
		'pages.edit.title' => 'Edit page',

		'pages.tab.general' => 'Content',
		'pages.tab.seo' => 'SEO',

		'pages.type.default' => 'Default',
		'pages.type.contact' => 'Contact form',
		'pages.type.map' => 'Map location',

		'pages.title' => 'Title',
		'pages.body' => 'Content',
		'pages.seo_title' => 'Title',
		'pages.seo_description' => 'Description',
		'pages.seo_keywords' => 'Keywords',

		'pages.configuration.contact.email' => 'Contact email',
		'pages.configuration.contact.email.helper' => 'Contact forms will be sent to this email address',
		'pages.configuration.contact.phone.required' => 'Phone is mandatory',

		'pages.configuration.map.lat' => 'Latitude',
		'pages.configuration.map.lng' => 'Longitude',
		'pages.configuration.map.zoom' => 'Zoom level',
		'pages.configuration.map.button' => 'Address search',
		'pages.configuration.map.address' => 'Address',
		'pages.configuration.map.address.helper' => 'Street, Zipcode, City, Country',
		'pages.configuration.map.geolocate' => 'Geolocate',

		'pages.create.success' => 'The page was successfully created',
		'pages.update.success' => 'The page was successfully updated',
		'pages.deleted.success' => 'The page was successfully removed',

		'sliders.h1' => 'Sliders',
		'sliders.empty' => 'No sliders found',
		'sliders.button.new' => 'Create slider',
		
		'sliders.create.title' => 'Create slider',
		'sliders.edit.title' => 'Edit slider',
		
		'sliders.tab.general' => 'Content',
		'sliders.column.title' => 'Title',
		'sliders.column.languages' => 'Languages',
		'sliders.select.alllanguages' => 'All',
		'sliders.label.title' => 'Title',
		'sliders.label.languages' => 'Languages',
		'sliders.label.link' => 'Link',
		
		'sliders.general.sliders' => 'Slides',
		'sliders.general.empty' => 'This slider has no slides',
		'sliders.upload' => 'Slide upload',
		
		'sliders.update.success' => 'The slider was successfully created',
		'sliders.create.success' => 'The slider was successfully updated',
		'sliders.deleted.success' => 'The slider was successfully removed',
		
		'sliders.delete.warning' => 'Are you sure you want to remove this slider?',
		
		
		'domainname.h1' => 'Domain name',
		'domainname.domain' => 'Domain',
		'domainname.domain.helper' => '<p>The domain name is not provided by Molista. You should by your own domain name with the domain name providers of your choice.</p>
										<p>Before setting your domain name of your website, please point your domain name to the following IP address: 46.101.105.169 (you shoud,be able to change this on your domain name provider admin panel).</p>
										<p>The IP change propagation may take up to 48 hours.</p>
										<p>If you have questions, please contact your domain name provider and ask how can you manage yopur domain redirection.</p>',
		'domainname.domain.error' => 'This domain name is already in use',
	];
