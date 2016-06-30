<?php
	return [
		'h1' => 'Your real state web',

		'previous' => 'Previous',
		'next' => 'Next',
		'accept' => 'Accept',

		'user.new.h2' => 'Create account',
		'user.new.name' => 'Name and lastname',
		'user.new.name.placeholder' => 'Enter your name',
		'user.new.email' => 'Email',
		'user.new.email.placeholder' => 'Enter your email',
		'user.new.password' => 'Password',
		'user.new.password.placeholder' => 'Enter your password',
		'user.new.accept' => 'I have read and accept the legal terms',
		'user.new.have.account' => 'I already have an account',

		'user.old.h2' => 'Ready to start?',
		'user.old.no.account' => "I don't have an account.",
		'user.old.create.account' => 'Create one',
		'user.old.password.forgot' => 'Forgot your password?',
		'user.old.password.click' => 'Click here',
		'user.old.error.combination' => 'Data submitted is not valid.',
		'user.old.error.employee' => 'This email belongs to a user already registered as an agent.',
		'user.old.error.admin' => 'This email belongs to a user already registered as an administrator.',

		'pack.h2' => 'Choose the plan you wish to contract',

		'site.h2' => 'Your website details',
		'site.subdomain' => 'Choose a name for your website',
		'site.subdomain.sample' => 'The address of you website will be:',
		'site.language' => 'Choose a language for your website',

		'payment.h2' => 'Payment method',
		'payment.choose' => 'Choose a payment method',
		'payment.iban' => 'IBAN account',

		'invoicing.h2' => 'Invoicing data',
		'invoicing.type.individual' => 'Individual',
		'invoicing.type.company' => 'Company',
		'invoicing.first_name' => 'Name',
		'invoicing.last_name' => 'Last name',
		'invoicing.email' => 'Email',
		'invoicing.tax_id' => 'Tax ID',
		'invoicing.address' => 'Address',
		'invoicing.street' => 'Street',
		'invoicing.zipcode' => 'Zipcode',
		'invoicing.city' => 'City',
		'invoicing.country' => 'Country',
		'invoicing.coupon' => 'Coupon or gift code',
		'invoicing.coupon.have' => 'Do you have a coupon or gift code?',
		'invoicing.coupon.use' => 'Use it',
		'invoicing.coupon.error' => 'The gift code is not valid',

		'confirm.h2' => 'Summary',
		'confirm.change' => 'Change',
		'confirm.name' => 'Name',
		'confirm.plan' => 'Plan',
		'confirm.subdomain' => 'Website name',

		'finish.h2' => 'The website was successfully created',
		'finish.h2.ready' => 'Your website is up and running',
		'finish.congratulations' => '<p>Congratulations, your website is available at <strong>:website_url</strong></p>',
		'finish.gotoweb' => 'Go to my website',
		'finish.gotoaccount' => 'Go to my backoffice',
		'finish.warning.links' => '<p>To access your backoffice you must use the email/password combination for the user you provided when creating this website.</p>
									<p>Hint: the email used was :owner_email</p>',
		'finish.plan.details' => '<ul>
									<li>Plan: :plan</li>
									<li>:price_text</li>
								</ul>',
		'finish.stripe.button' => 'Pay now',
		'finish.stripe.warnings' => '<p>To process our payments we use Stripe, a very secure payment gateway that utilizes several tools to prevent fraud. For more information on Stripe, please visit <a href="https://stripe.com" target="_blank">www.stripe.com</a>.</p>',
		'finish.transfer.intro' => '<p>We are now verifying the payment information you provided. We will let you know when we are done with it and you will be able to enjoy your chose plan (:plan - :price_text).</p>
									<p>In the meantime, prepare your website with our Free plan:</p>
									<ul>
										<li data-type="fixa">Detailed webpages for all your properties</li>
										<li data-type="searcher">Property search with advanced  filters</li>
										<li data-type="relation">Ability to link properties</li>
										<li data-type="easy">Easy to use and manage</li>
										<li data-type="settings">Customizable</li>
										<li data-type="responsive">Compatible with phones and tablets</li>
									</ul>',

		'email.subject' => 'Welcome to molista',
		'email.hello' => '<p>Hello :name,</p>',
		'email.features.intro.now' => '<p>You can now begin to enjoy the advantages that <strong>molista</strong> offers so that your company can use the internet fully:</p>',
		'email.features.intro.stripe' => '<p>You are almost there!</p>
											<p>You just need to complete the payment of your chosen plan (:plan - :priceperiod) using :paymethod from you website backoffice.</p>
											<p>Complete the process begin to enjoy the advantages that <strong>molista</strong> offers so that your company can use the internet fully:</p>',
		'email.features.intro.transfer' => '<p>You are almost there!</p>
											<p>We are now verifying the payment information you provided. We will let you know when we are done with it and you will be able to enjoy your chose plan (:plan - :priceperiod).</p>
											<p>In the meantime, prepare your website with our Free plan:</p>',
		'email.features.list' => '<ul>
									<li>Property information pages</li>
									<li>Property search with advanced filters</li>
									<li>Posibility to link properties</li>
									<li>Easy to use and manage</li>
									<li>Customizable</li>
									<li>Adapted to cell phones and tablets</li>
								</ul>',
		'email.url.site' => '<p>This is your website URL: :site_url</p>',
		'email.url.account' => '<p>And these are the access information for the backoffice of your site:</p>
								<ul>
									<li>URL: :account_url</li>
									<li>Email: :email</li>
									<li>Password: ******</li>
								</ul>',
		'email.warning.stripe' => '<p><small>* If you already completed the process, forget this message</span></p>',
	];