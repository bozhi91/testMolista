<?php

	return [

		'configuration.h1' => 'Site configuration',

		'configuration.tab.config' => 'General',
		'configuration.tab.texts' => 'SEO',
		'configuration.tab.social' => 'Social media',
		'configuration.tab.mail' => 'Emails',
		'configuration.tab.signature' => 'Signature',

		'configuration.languages' => 'Enabled languages',
		'configuration.languages.error' => 'Please select at least one language',
		'configuration.subdomain' => 'Subdomain',
		'configuration.subdomain.error' => 'This subdomain is already taken',
		'configuration.subdomain.alpha' => 'Letters, numbers, hypen and underscores only please',
		'configuration.theme' => 'Website theme',
		'configuration.theme.preview' => 'Show preview',
		'configuration.theme.preview.error' => 'Please first select a theme to see its preview',
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

		'widgets.type.menu' => 'Custom menu',
		'widgets.type.menu.info' => 'Add one of your custom menus as a widget.',
		'widgets.type.menu.select' => 'Select menu',
		'widgets.type.text' => 'Text block',
		'widgets.type.text.info' => 'Add one block of text as a widget.',
		'widgets.type.text.content' => 'Text',

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

		'pages.configuration.map.lat' => 'Latitude',
		'pages.configuration.map.lng' => 'Longitude',
		'pages.configuration.map.zoom' => 'Zoom level',

		'pages.create.success' => 'The page was successfully created',
		'pages.update.success' => 'The page was successfully updated',
		'pages.deleted.success' => 'The page was successfully removed',

	];
