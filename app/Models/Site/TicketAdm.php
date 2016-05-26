<?php namespace App\Models\Site;

class TicketAdm
{
	protected $site;
	protected $site_id;
	protected $site_token;
	protected $site_ready;

	protected $guzzle_client;

	public function __construct($site)
	{
		$this->setSite($site);

		$base_uri = env('TICKETS_API_URL', false);
		if ( !$base_uri )
		{
			\Log::error("TICKETING -> environment variable TICKETS_API_URL is not defined");
			exit;
		}

		if ( substr($base_uri, -1) != '/' )
		{
			$base_uri .= '/';
		}
		$this->guzzle_client = new \GuzzleHttp\Client([
			'base_uri' => $base_uri,
			'http_errors' => false,
		]);
	}

	public function setSite($site)
	{
		$this->site = $site;
		$this->site_id = $site->ticket_site_id;
		$this->site_token = $site->ticket_owner_token;
		$this->site_ready = ( $this->site_id && $this->site_token );
	}

	public function getAuthorizationHeader()
	{
		return "Bearer {$this->site_token}";
	}

	public function createSite()
	{
		if ( $this->site_ready )
		{
			return $this->site_id;
		}

		// Get all users, companies first
		$users = $this->site->users()->with('roles')->get()->sortBy(function($user){
			return $user->hasRole('company') ? 0 : 1;
		});


		if ( $users->count() < 1 )
		{
			return false;
		}

		// Assign first user as owner (hopefully is a company!)
		$owner = $users->shift();

		// Create site
		$data = [
			'headers' => [],
			'json' => [
				'title' => $this->site->title,
				'user' => [
					'name' => $owner->name,
					'email' => $owner->email,
				],
			],
		];
		if ( $owner->ticket_user_token )
		{
			$data['headers']['Authorization'] = "Bearer {$owner->ticket_user_token}";
		}
		$response = $this->guzzle_client->request('POST', 'site', $data);

		// Get body
		$body = @json_decode( $response->getBody() );

		// Error?
		if ( $response->getStatusCode() != 201 )
		{
			$error_message = "TICKETING -> could not create site {$this->site->id}";
			if ( @$body->message )
			{
				$error_message .= ": {$body->message}";
			}
			\Log::error($error_message);
			return false;
		}

		// Update site
		$this->site->ticket_site_id = $body->id;
		$this->site->ticket_owner_token = $owner->ticket_user_token ? $owner->ticket_user_token : $body->token;
		$this->site->save();
		$this->setSite( $this->site );

		// Update owner
		if ( !$owner->ticket_user_token )
		{
			$owner->ticket_user_token = $body->token;
			$owner->ticket_user_id = $body->user_id;
			$owner->save();
		}

		// Process other users
		$this->associateUsers($users);

		return $this->site->ticket_site_id;
	}

	public function updateSite($site)
	{
		if ( !$this->site_ready )
		{
			return false;
		}

		$data = [
			'headers' => [
				'Authorization' => $this->getAuthorizationHeader(),
			],
			'json' => [
				'title' => $site->title,
				'email_account' => $site->smtp_mailer,
			],
		];
		$response = $this->guzzle_client->request('PUT', "site/{$this->site_id}", $data);

		// Get body
		$body = @json_decode( $response->getBody() );

		// Error?
		if ( $response->getStatusCode() != 204 )
		{
			$error_message = "TICKETING -> could not update site {$this->site->id}";
			if ( @$body->message )
			{
				$error_message .= ": {$body->message}";
			}
			\Log::error($error_message);
			return false;
		}

		return true;
	}

	public function createUser($user) 
	{
		if ( !$this->site_ready )
		{
			return false;
		}

		$data = [
			'headers'=> [
				'Authorization' => $this->getAuthorizationHeader(),
			],
			'json' => [
				'name' => $user->name,
				'email' => $user->email,
				'type' => 'agent',
			],
		];
		$response = $this->guzzle_client->request('POST', "user?site_id={$this->site_id}", $data);

		// Get body
		$body = @json_decode( $response->getBody() );

		// Error?
		if ( $response->getStatusCode() != 201 )
		{
			$error_message = "TICKETING -> could not create user {$user->id}";
			if ( @$body->message )
			{
				$error_message .= ": {$body->message}";
			}
			\Log::error($error_message);
			return false;
		}

		// Update user
		$user->ticket_user_id = $body->id;
		$user->ticket_user_token = $body->token;
		$user->save();

		return true;
	}

	public function associateUsers($users) 
	{
		if ( !$this->site_ready )
		{
			return false;
		}

		$updates = [];

		// Create users
		foreach ($users as $user)
		{
			if ( $user->ticket_user_id )
			{
				$updates[] = [
					'id' => $user->ticket_user_id,
					'type' => $user->hasRole('company') ? 'manager' : 'agent'
				];
				continue;
			}
			$this->createUser($user);
		}

		if ( count($updates) < 1 )
		{
			return true;
		}

		$data = [
			'headers'=> [
				'Authorization' => $this->getAuthorizationHeader(),
			],
			'json' => [
				'users' => $updates,
			],
		];
		$response = $this->guzzle_client->request('POST', "site/{$this->site_id}/user", $data);

		// Success
		if ( $response->getStatusCode() == 204 )
		{
			return true;
		}

		// Get body
		$body = @json_decode( $response->getBody() );

		// Log error
		$error_message = "TICKETING -> could not associate users to site {$this->site->id}";
		if ( @$body->message )
		{
			$error_message .= ": {$body->message}";
		}
		\Log::error($error_message);

		return false;
	}

	public function dissociateUsers($users) 
	{
		if ( !$this->site_ready )
		{
			return false;
		}

		$deletes = [];

		foreach ($users as $user)
		{
			if ( !$user->ticket_user_id )
			{
				continue;
			}

			$deletes[] = $user->ticket_user_id;
		}

		if ( count($deletes) < 1 )
		{
			return true;
		}

		$data = [
			'headers'=> [
				'Authorization' => $this->getAuthorizationHeader(),
			],
			'json' => [
				'users' => $deletes,
			],
		];
		$response = $this->guzzle_client->request('DELETE', "site/{$this->site_id}/user", $data);

		// Success
		if ( $response->getStatusCode() == 204 )
		{
			return true;
		}

		// Get body
		$body = @json_decode( $response->getBody() );

		// Log error
		$error_message = "TICKETING -> could not dissociate users to site {$this->site->id}";
		if ( @$body->message )
		{
			$error_message .= ": {$body->message}";
		}
		\Log::error($error_message);

		return false;
	}

	public function associateContact($contact) 
	{
		if ( !$this->site_ready )
		{
			return false;
		}

		$data = [
			'headers'=> [
				'Authorization' => $this->getAuthorizationHeader(),
			],
			'json' => [
				'email' => $contact->email,
				'fullname' => $contact->full_name,
				'phone' => $contact->phone,
				'locale' => $contact->locale,
				//'company' => '',
				//'address' => '',
				//'image' => '',
				//'notes' => '',
			],
		];

		if ( $contact->ticket_contact_id )
		{
			$request_type = 'update';
			$expected_status = 204;
			$response = $this->guzzle_client->request('PUT', "contact/{$contact->ticket_contact_id}?site_id={$this->site_id}", $data);
		}
		else
		{
			$request_type = 'create';
			$expected_status = 201;
			$response = $this->guzzle_client->request('POST', "contact/?site_id={$this->site_id}", $data);
		}

		// Get body
		$body = @json_decode( $response->getBody() );

		// Error?
		if ( $response->getStatusCode() != $expected_status )
		{
			$error_message = "TICKETING -> could not {$request_type} contact {$contact->id}";
			if ( @$body->message )
			{
				$error_message .= ": {$body->message}";
			}
			\Log::error($error_message);
			return false;
		}

		// Update contact
		if ( $request_type == 'create' )
		{
			$contact->ticket_contact_id = $body->id;
			$contact->save();
		}

		return true;
	}

	public function associateItem($item) 
	{
		if ( !$this->site_ready )
		{
			return false;
		}

		$data = [
			'headers'=> [
				'Authorization' => $this->getAuthorizationHeader(),
			],
			'json' => [
				'type' => 'property',
				'reference' => $item->id,
				'title' => $item->title,
				//'image' => '',
				//'url' => '',
			],
		];

		if ( $item->ticket_item_id )
		{
			$request_type = 'update';
			$expected_status = 204;
			$response = $this->guzzle_client->request('PUT', "item/{$item->ticket_item_id}?site_id={$this->site_id}", $data);
		}
		else
		{
			$request_type = 'create';
			$expected_status = 201;
			$response = $this->guzzle_client->request('POST', "item/?site_id={$this->site_id}", $data);
		}

		// Get body
		$body = @json_decode( $response->getBody() );

		// Error?
		if ( $response->getStatusCode() != $expected_status )
		{
			$error_message = "TICKETING -> could not {$request_type} item {$item->id}";
			if ( @$body->message )
			{
				$error_message .= ": {$body->message}";
			}
			\Log::error($error_message);
			return false;
		}

		// Update item
		if ( $request_type == 'create' )
		{
			$item->ticket_item_id = $body->id;
			$item->save();
		}

		return true;
	}

}
