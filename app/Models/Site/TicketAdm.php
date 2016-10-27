<?php namespace App\Models\Site;

class TicketAdm
{
	protected $site;
	protected $site_id;
	protected $site_token;
	protected $site_ready;

	protected $sources = [
		'email' => 'Email',
		'phone' => 'Phone',
		'web' => 'Web',
		'chat' => 'Chat',
		'facebook' => 'Facebook',
		'backoffice' => 'Backoffice',
		'other' => 'Other',
	];
	protected $status = [
		'open' => 'Open',
		'waiting' => 'Waiting',
		'resolved' => 'Resolved',
		'closed' => 'Closed',
	];
	protected $states = [
		'open',
		'closed',
	];

	protected $guzzle_client;

	public function __construct($site_id, $user_token=false)
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

		if ( $user_token )
		{
			$this->setSiteToken($user_token);
		}
		else
		{
			$this->setSiteToken($this->site->ticket_owner_token);
		}
	}

	public function setSite($site_id)
	{
		$this->site = \App\Site::withTranslations()->findOrFail($site_id);
		$this->site_id = $this->site->ticket_site_id;
	}

	public function setSiteToken($token)
	{
		$this->site_token = $token;
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
		$this->setSiteToken($this->site->ticket_owner_token);

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

	public function updateUser($user) 
	{
		if ( !$this->site_ready )
		{
			return false;
		}

		// User type
		$user_type = $user->hasRole('company') ? 'manager' : 'agent';

		// Update user
		$response = $this->guzzle_client->request('PUT', "user/{$user->ticket_user_id}?site_id={$this->site_id}", [
			'headers'=> [
				'Authorization' => $this->getAuthorizationHeader(),
			],
			'json' => [
				'name' => $user->name,
				//'email' => $user->email,
				'type' => $user->hasRole('company') ? 'manager' : 'agent',
			],
		]);
		// Error?
		if ( $response->getStatusCode() == 204 )
		{
			return true;
		}

		$error_message = "TICKETING -> could not update user {$user->id}";

		$body = @json_decode( $response->getBody() );
		if ( @$body->message )
		{
			$error_message .= ": {$body->message}";
		}
		\Log::error($error_message);

		return false;
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
				$this->updateUser($user);
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

	public function dissociateUsers($users, $reassignee=false) 
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

			// Add to delete queue
			$deletes[] = $user->ticket_user_id;

			// Reassign tickets
			$this->reassignUserTickets($user, $reassignee); 
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

	public function reassignUserTickets($user, $reassignee) 
	{
		if ( !$user || !$user->ticket_user_id )
		{
			return false;
		}

		if ( !$reassignee || !$reassignee->ticket_user_id )
		{
			return false;
		}

		$data = [
			'headers'=> [
				'Authorization' => $this->getAuthorizationHeader(),
			],
			'json' => [
				'user_id' => $reassignee->ticket_user_id,
			],
		];
		$response = $this->guzzle_client->request('POST', "user/{$user->ticket_user_id}/reasign?site_id={$this->site_id}", $data);

		// Success
		if ( $response->getStatusCode() == 204 )
		{
			return true;
		}

		// Get body
		$body = @json_decode( $response->getBody() );

		// Log error
		$error_message = "TICKETING -> could not reassign user tickets of site {$this->site->id}: {$user->id} => {$reassignee->id}";
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
				'referer' => $contact->origin,
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
			'json' => $data,
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
			'orderby' => empty($params['orderby']) ? 'created_at' : $params['orderby'],
			'order' => empty($params['order']) ? 'desc' : $params['order'],
		];

		if ( isset($params['user_id']) )
		{
			$data['user_id'] = $params['user_id'];
		}

		if ( isset($params['contact_email']) )
		{
			$data['contact_email'] = $params['contact_email'];
		}

		if ( isset($params['contact_id']) )
		{
			$data['contact_id'] = $params['contact_id'];
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

		$ticket = $body;

		$first_message = @array_pop(array_values($ticket->messages));
		$ticket->subject = @$first_message->subject;

		//  Ticket contact
		if ( @$ticket->contact->id )
		{
			$customer = \App\Models\Site\Customer::where('ticket_contact_id', $ticket->contact->id)->first();
			$ticket->contact->id_molista = @$customer->id;
		}

		//  Ticket property
		if ( @$ticket->item->id )
		{
			$property = \App\Property::withTrashed()->where('ticket_item_id', $ticket->item->id)->first();
			$ticket->item->id_molista = @$property->id;
		}

		// Add user images
		$user_ids_rel = [];
		$images = [ 'default' => asset('images/users/default.png') ];

		//  Ticket user
		if ( @$ticket->user )
		{
			if ( @$ticket->user->id )
			{
				if ( empty($images[$ticket->user->id]) )
				{
					$user = \App\User::where('ticket_user_id', $ticket->user->id)->first();
					$user_ids_rel[$ticket->user->id] = @$user->id;
					$images[$ticket->user->id] = ( $user && $user->image ) ? $user->image_url : $images['default'];
				}
				$ticket->user->image = $images[$ticket->user->id];
				$ticket->user->id_molista = @$user_ids_rel[$ticket->user->id];
			}
			else
			{
				$ticket->user->image = $images['default'];
			}
		}

		//  Ticket messages
		foreach ($ticket->messages as $key => $message)
		{
			if ( $message->user )
			{
				if ( @$message->user->id )
				{
					if ( empty($images[$message->user->id]) )
					{
						$user = \App\User::where('ticket_user_id', $message->user->id)->first();
						$images[$message->user->id] = ( $user && $user->image ) ? $user->image_url : $images['default'];
					}
					$ticket->messages[$key]->user->image = $images[$message->user->id];
				}
				else
				{
					$ticket->messages[$key]->user->image = $images['default'];
				}
			}

			if ( empty($message->files) || !is_array($message->files) )
			{
				$message->files = [];
			}
			foreach ($message->files as $file_key => $file_value)
			{
				if ( empty($file_value->title) )
				{
					$tmp = parse_url($file_value->url);
					$file_value->title = basename($tmp['path']);
					$message->files[$file_key] = $file_value;
				}
			}
		}

		return $ticket;
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

	public function getEmailAccounts($user_id, $user_token=false)
	{
		$default = [];

		if ($user_token)
		{
			$this->setSiteToken($user_token);
		}
			
		if ( !$this->site_ready )
		{
			return $default;
		}

		$data = [
			'site_id' => $this->site_id,
		];

		$response = $this->guzzle_client->request('GET', "user/{$user_id}/email_account/?".http_build_query($data), [
			'headers'=> [
				'Authorization' => $this->getAuthorizationHeader(),
			],
		]);

		// Error
		$body = @json_decode( $response->getBody() );

		if ( $response->getStatusCode() != 200 )
		{
			$error_message = "TICKETING -> could not retrieve user email accounts list";
			if ( @$body->message )
			{
				$error_message .= ": {$body->message}";
			}
			\Log::error($error_message);
			return $default;
		}

		// Add title
		foreach ($body as $key => $value)
		{
			$body[$key]->title = $value->from_name ? "{$value->from_name} ($value->from_email)" : $value->from_email;
		}

		// Sort accounts by name/email
		uasort($body, function($a, $b) {
			return ($a->title < $b->title) ? -1 : ($a->title == $b->title ? 0 : 1);
		});

		return $body;
	}

	public function saveEmailAccount($data, $account_id=false) 
	{
		// Set user id
		$user_id = @$data['user_id'];
		if ( !$user_id )
		{
			return false;
		}

		// Update user token ?
		if ( @$data['user_token'] )
		{
			$this->setSiteToken($data['user_token']);
		}

		// Check site ready
		if ( !$this->site_ready )
		{
			return false;
		}

		// Prepare request data
		$request_data = [
			'headers'=> [
				'Authorization' => $this->getAuthorizationHeader(),
			],
			'json' => [
				'protocol' => @$data['protocol'],
				'from_name' => @$data['from_name'],
				'from_email' => @$data['from_email'],
				'host' => @$data['host'],
				'password' => @$data['password'],
				'username' => @$data['username'],
				'port' => @$data['port'],
				'layer' => @$data['layer'],
			],
		];

		// Empty password?
		if ( !$request_data['json']['password'] )
		{
			unset($request_data['json']['password'])	;
		}

		// Send request
		if ( $account_id )
		{
			$request_type = 'update';
			$expected_status = 204;
			$response = $this->guzzle_client->request('PUT', "user/{$user_id}/email_account/{$account_id}?site_id={$this->site_id}", $request_data);
		}
		else
		{
			$request_type = 'create';
			$expected_status = 201;
			$response = $this->guzzle_client->request('POST', "user/{$user_id}/email_account?site_id={$this->site_id}", $request_data);
		}

		// Get body
		$body = @json_decode( $response->getBody() );

		// Error?
		if ( $response->getStatusCode() != $expected_status )
		{
			$error_message = "TICKETING -> could not {$request_type} user email account {$account_id}";
			if ( @$body->message )
			{
				$error_message .= ": {$body->message}";
			}
			\Log::error($error_message);
			return false;
		}

		if ( $request_type == 'create' )
		{
			$account_id = $body->id;
		}

		return $account_id;
	}

	public function testEmailAccount($account_id,$user_id, $user_token=false) 
	{
		if ($user_token)
		{
			$this->setSiteToken($user_token);
		}
			
		if ( !$this->site_ready )
		{
			return false;
		}

		$data = [
			'headers'=> [
				'Authorization' => $this->getAuthorizationHeader(),
			],
		];
		$response = $this->guzzle_client->request('GET', "user/{$user_id}/email_account/{$account_id}/test?site_id={$this->site_id}", $data);

		// Success
		if ( $response->getStatusCode() == 200 )
		{
			return true;
		}

		return false;
	}
	
	public function deleteEmailAccount($account_id,$user_id, $user_token=false) 
	{
		if ($user_token)
		{
			$this->setSiteToken($user_token);
		}
			
		if ( !$this->site_ready )
		{
			return false;
		}

		$data = [
			'headers'=> [
				'Authorization' => $this->getAuthorizationHeader(),
			],
		];
		$response = $this->guzzle_client->request('DELETE', "user/{$user_id}/email_account/{$account_id}?site_id={$this->site_id}", $data);

		// Success
		if ( $response->getStatusCode() == 204 )
		{
			return true;
		}

		// Get body
		$body = @json_decode( $response->getBody() );

		// Log error
		$error_message = "TICKETING -> could not delete user email account {$account_id}";
		if ( @$body->message )
		{
			$error_message .= ": {$body->message}";
		}
		\Log::error($error_message);

		return false;
	}

	public function getUsersStats($user_ids)
	{
		if ( !$this->site_ready || empty($user_ids) )
		{
			return false;
		}

		if ( !is_array($user_ids) )
		{
			$user_ids = [ $user_ids ];
		}

		// Stats groups
		$stats_groups = ['tickets','contacts'];

		// Stats default
		$stats_default = [
			'tickets' => [],
			'contacts' => [],
			'items' => [],
		];
		foreach ($this->states as $state)
		{
			$stats_default['tickets'][$state] = 0;
			$stats_default['contacts'][$state] = 0;
		}

		// Request url
		$url = "stats/user/?site_id={$this->site_id}";

		$stats = [];
		foreach ($user_ids as $id) 
		{
			$url .= "&user_id[]={$id}";
			$stats[$id] = $stats_default;
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
			$error_message = "TICKETING -> could not access users stats";
			if ( @$body->message )
			{
				$error_message .= ": {$body->message}";
			}
			\Log::error($error_message);
			return false;
		}
		
		foreach ($body as $data)
		{
			$user_id = @$data->user_id;
			if ( !$user_id )
			{
				continue;
			}

			$stat = $stats[$user_id];

			foreach ($stats_groups as $group)
			{
				if ( @$data->$group->states )
				{
					foreach ($data->$group->states as $type => $quantity)
					{
						$stat[$group][$type] = $quantity;
					}
				}
			}

			$stats[$user_id] = json_decode(json_encode($stat));
		}

		return $stats;
	}

	
	public function getCustomersStats($customers_ids)
	{
		if ( !$this->site_ready || empty($customers_ids) )
		{
			return false;
		}

		if ( !is_array($customers_ids) )
		{
			$customers_ids = [ $customers_ids ];
		}

		// Stats groups
		$stats_groups = ['tickets','contacts'];

		// Stats default
		$stats_default = [
			'tickets' => [],
			'items' => [],
		];
		foreach ($this->states as $state)
		{
			$stats_default['tickets'][$state] = 0;
		}

		// Request url
		$url = "stats/contact/?site_id={$this->site_id}";

		$stats = [];
		foreach ($customers_ids as $id) 
		{
			$url .= "&contact_id[]={$id}";
			$stats[$id] = $stats_default;
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
			$error_message = "TICKETING -> could not access customers stats";
			if ( @$body->message )
			{
				$error_message .= ": {$body->message}";
			}
			\Log::error($error_message);
			return false;
		}
						
		foreach ($body as $data)
		{			
			$contact_id = @$data->contact_id;
			if ( !$contact_id )
			{
				continue;
			}

			$stat = $stats[$contact_id];

			foreach ($stats_groups as $group)
			{
				if ( @$data->$group->states )
				{
					foreach ($data->$group->states as $type => $quantity)
					{
						$stat[$group][$type] = $quantity;
					}
				}
			}

			$stats[$contact_id] = json_decode(json_encode($stat));
		}

		return $stats;
	}
	
	
	public function getStatusOptions() 
	{
		$options = [];
		
		foreach ($this->status as $key => $value) 
		{
			$options[$key] = trans("account/tickets.status.{$key}");
		}

		return $options;
	}

	public function testEmail($protocol)
	{
		if ( !$this->site_ready )
		{
			return false;
		}

		// Request url
		$response = $this->guzzle_client->request('GET', "email_account/{$protocol}?site_id={$this->site_id}", [
			'headers'=> [
				'Authorization' => $this->getAuthorizationHeader(),
			],
		]);

		// Error
		$body = @json_decode( $response->getBody() );

		if ( $response->getStatusCode() != 200 )
		{
			$error_message = "TICKETING -> testEmail error";
			if ( @$body->message )
			{
				$error_message .= ": {$body->message}";
			}
			\Log::error($error_message);
			return false;
		}

		return true;
	}

	public function prepareSiteSignature($user,$site) 
	{
		if ( !$user || empty($user['name']) )
		{
			return false;
		}

		$user_info = implode('<br />', array_filter([
			@$user['name'],
			@$user['email'],
			@$user['phone'],
			@$user['linkedin'],
		]));

		$site_info = implode('<br />', array_filter([
			@$site['name'],
			@$site['address'],
			@$site['url'],
		]));

		$signature = implode("\n", array_filter([
			@$user['image'] ? "<img src='{$user['image']}' />" : '',
			$user_info,
			$site_info,
		]));

		return nl2p($signature);
	}

}
