<?php namespace App\Models\Site;

class TicketAdm
{
	protected $site;
	protected $site_id;
	protected $site_token;
	protected $site_ready;

	protected $sources = [
		'email',
		'phone',
		'web',
		'chat',
		'facebook',
		'backoffice',
		'other',
	];
	protected $status = [
		'open',
		'waiting',
		'resolved',
		'closed',
	];

	protected $guzzle_client;

	public function __construct($site_id)
	{
		$base_uri = \Config::get('app.ticketing_system_url', false);
		if ( $base_uri )
		{
			if ( substr($base_uri, -1) != '/' )
			{
				$base_uri .= '/';
			}
			$this->guzzle_client = new \GuzzleHttp\Client([
				'base_uri' => $base_uri,
				'http_errors' => false,
			]);
		}
		else
		{
			\Log::error("TICKETING -> environment variable TICKETING_SYSTEM_URL is not defined");
		}

		$this->setSite($site_id);
	}

	public function setSite($site_id)
	{
		$this->site = \App\Site::withTranslations()->findOrFail($site_id);

		$this->site_id = $this->site->ticket_site_id;
		$this->site_token = $this->site->ticket_owner_token;
		$this->site_ready = ( $this->site_id && $this->site_token && $this->guzzle_client );
	}

	public function getAuthorizationHeader()
	{
		return "Bearer {$this->site_token}";
	}

	public function createSite()
	{
		if ( $this->site_id )
		{
			return $this->site_id;
		}

		if ( !$this->guzzle_client )
		{
			return false;
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
		$this->setSite( $this->site->id );

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

	public function updateSite()
	{
		if ( !$this->site_ready )
		{
			return false;
		}

		$email_account = [];

		if ( $out = $this->site->mailer_out )
		{
			$email_account[$out['protocol']] = $out;
			unset($email_account[$out['protocol']]['protocol']);
		}

		if ( $in = $this->site->mailer_in )
		{
			$email_account[$in['protocol']] = $in;
			unset($email_account[$in['protocol']]['protocol']);
		}

		$data = [
			'headers' => [
				'Authorization' => $this->getAuthorizationHeader(),
			],
			'json' => [
				'title' => $this->site->title,
				'email_account' => $email_account,
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
				'title' => $item->ref,
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

	public function createTicket($data)
	{
		if ( !$this->site_ready || !is_array($data) )
		{
			return false;
		}

		// Send request
		$response = $this->guzzle_client->request('POST', "ticket/?site_id={$this->site_id}", [
			'headers'=> [
				'Authorization' => $this->getAuthorizationHeader(),
			],
			'json' => [
				'contact_id' => isset($data['contact_id']) ? $data['contact_id'] : null,
				'user_id' => isset($data['user_id']) ? $data['user_id'] : null,
				'item_id' => isset($data['item_id']) ? $data['item_id'] : null,
				'source' => isset($data['source']) ? $data['source'] : null,
				'subject' => isset($data['subject']) ? $data['subject'] : null,
				'body' => isset($data['body']) ? $data['body'] : null,
				'referer' => isset($data['referer']) ? $data['referer'] : null,
			],
		]);

		// Success
		if ( $response->getStatusCode() == 201 )
		{
			return true;
		}

		// Log error
		$body = @json_decode( $response->getBody() );
		$error_message = "TICKETING -> could not create ticket";
		if ( @$body->message )
		{
			$error_message .= ": {$body->message}";
		}
		\Log::error($error_message);

		return false;
	}
	public function getTickets($params)
	{
		if ( !$this->site_ready )
		{
			return false;
		}

		$data = [
			'site_id' => $this->site_id,
			'limit' => empty($params['limit']) ? 50 : intval($params['limit']),
			'page' => empty($params['page']) ? 1 : intval($params['page']),
		];

		if ( isset($params['user_id']) )
		{
			$data['user_id'] = $params['user_id'];
		}

		if ( @$params['status'] )
		{
			$data['status'] = is_array($params['status']) ? implode(',',$params['status']) : $params['status'];
		}

		$response = $this->guzzle_client->request('GET', 'ticket/?'.http_build_query($data), [
			'headers'=> [
				'Authorization' => $this->getAuthorizationHeader(),
			],
		]);

		// Error
		$body = @json_decode( $response->getBody() );

		if ( $response->getStatusCode() != 200 )
		{
			$error_message = "TICKETING -> could not retrieve tickets list";
			if ( @$body->message )
			{
				$error_message .= ": {$body->message}";
			}
			\Log::error($error_message);
			return false;
		}

		$total_pages = @array_shift( $response->getHeader('Pages-Total') );
		if ( !$total_pages ) $total_pages = 1;

		$total_items = @array_shift( $response->getHeader('Items-Total') );
		if ( !$total_items ) $total_items = 1;

		return [
			'items' => $body,
			'total_pages' => $total_pages,
			'total_items' => $total_items,
			'page' => $data['page'],
			'limit' => $data['limit'],
		];
	}
	public function getTicket($ticket_id)
	{
		if ( !$this->site_ready )
		{
			return false;
		}

		$response = $this->guzzle_client->request('GET', "ticket/{$ticket_id}?site_id={$this->site_id}", [
			'headers'=> [
				'Authorization' => $this->getAuthorizationHeader(),
			],
		]);

		// Error
		$body = @json_decode( $response->getBody() );

		if ( $response->getStatusCode() != 200 )
		{
			$error_message = "TICKETING -> could not retrieve ticket with ID {$ticket_id}";
			if ( @$body->message )
			{
				$error_message .= ": {$body->message}";
			}
			\Log::error($error_message);
			return false;
		}

		return $body;
	}
	public function updateTicket($ticket_id, $data)
	{
		if ( !$this->site_ready )
		{
			return false;
		}

		$data['site_id'] = $this->site_id;

		$response = $this->guzzle_client->request('PUT', "ticket/{$ticket_id}?".http_build_query($data), [
			'headers'=> [
				'Authorization' => $this->getAuthorizationHeader(),
			],
		]);

		// Error
		$body = @json_decode( $response->getBody() );

		if ( $response->getStatusCode() != 204 )
		{
			$error_message = "TICKETING -> could not update ticket with ID {$ticket_id}";
			if ( @$body->message )
			{
				$error_message .= ": {$body->message}";
			}
			\Log::error($error_message);
			return false;
		}

		return true;
	}

	public function postMessage($ticket_id, $data) 
	{
		if ( !$this->site_ready )
		{
			return false;
		}

		$response = $this->guzzle_client->request('POST', "ticket/{$ticket_id}/message/?site_id={$this->site_id}", [
			'headers'=> [
				'Authorization' => $this->getAuthorizationHeader(),
			],
			'json' => $data,
		]);

		// Get body
		$body = @json_decode( $response->getBody() );

		// Error?
		if ( $response->getStatusCode() != 201 )
		{
			$error_message = "TICKETING -> could not create message for ticket {$ticket_id}";
			if ( @$body->message )
			{
				$error_message .= ": {$body->message}";
			}
			\Log::error($error_message);
			return false;
		}

		return true;
	}

	public function getDefaultStats()
	{
		$stats = [
			'tickets' => [],
			'items' => [],
		];

		foreach ($this->status as $status)
		{
			$stats['tickets'][$status] = 0;
		}

		return json_decode(json_encode($stats));
	}

	public function getUsersStats($contact_ids)
	{
		if ( !$this->site_ready || empty($contact_ids) )
		{
			return false;
		}

		if ( !is_array($contact_ids) )
		{
			$contact_ids = [ $contact_ids ];
		}

		$url = "stats/contact/?site_id={$this->site_id}";

		$stats = [];
		foreach ($contact_ids as $id) 
		{
			$url .= "&contact_id[]={$id}";
			$stats[$id] = $this->getDefaultStats();
		}

		$response = $this->guzzle_client->request('GET', $url, [
			'headers'=> [
				'Authorization' => $this->getAuthorizationHeader(),
			],
		]);

		// Error
		$body = @json_decode( $response->getBody() );

		if ( $response->getStatusCode() != 200 )
		{
			$error_message = "TICKETING -> could not access contact stats";
			if ( @$body->message )
			{
				$error_message .= ": {$body->message}";
			}
			\Log::error($error_message);
			return false;
		}

		foreach ($body as $data)
		{
			if ( !empty($data->tickets) )
			{
				foreach ($data->tickets as $type=>$quantity)
				{
					$stats[$data->contact_id]->tickets->$type = $quantity;

				}
			}
		}

		return $stats;
	}

	public function getStatusOptions() 
	{
		$options = [];
		
		foreach ($this->status as $key) 
		{
			$options[$key] = trans("account/tickets.status.{$key}");
		}

		return $options;
	}

}
