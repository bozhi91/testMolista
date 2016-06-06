<?php namespace App\Console\Commands;

use Illuminate\Console\Command;

class ParseWebsiteCommand extends Command
{
	protected $signature = 'parser:process';
	protected $description = 'Process pending website parsing requests';

	protected $request;
	protected $max_pages = 50;

	public function handle()
	{
		$pending_requests = \App\Models\Utils\ParseRequest::whereNull('finished_at');

		if ( $pending_requests->count() < 1 )
		{
			return;
		}

		foreach ($pending_requests->get() as $request) 
		{
			// Wait at least 2 hours between crawls
			$elapsed_time = (time() - strtotime($request->updated_at)) / 3600;
			if ( $elapsed_time < 2 )
			{
				$this->log("waiting; last crawl: " . since_text($request->updated_at) );
				continue;
			}

			// Set as global
			$this->request = $request;

			// Log init
			$this->log("begin processing {$this->request->service} / {$this->request->query}");

			// Get service
			$service = $this->request->service_details;
			if ( !$service )
			{
				$this->log("service {$this->request->service} is not defined", 'error');
				continue;
			}

			// Columns
			$columns = $service['columns'];

			// Get current items
			$items = $this->request->items->lists('id','service_id')->all();

			// Prepare url
			$url = str_replace('[QUERY_STRING]', $this->request->query, $service['url']);

			$loop = 0;
			$page = $this->request->last_page;

			while ($loop < $this->max_pages)
			{
				$loop++;
				$page++;

				// Set page
				$current_url = str_replace('[CURRENT_PAGE]', $page, $url);

				// Get html
				$html = new \Htmldom( $current_url );

				// Find items
				$lines = $html->find($service['items']);

				// Reset counter
				$lines_found = 0;
				$lines_processed = 0;

				foreach ($lines as $line) 
				{
					$elem = [
						'parse_request_id' => $this->request->id,
						'service_id' => $this->request->getServiceIdFromDom($line),
						'columns' => [],
					];

					if ( !$elem['service_id'] )
					{
						$this->log("service_id not found for service {$this->request->service}", 'warning');
						continue;
					}

					// Add countes
					$lines_found++;

					// Check if exists
					if ( array_key_exists($elem['service_id'], $items) )
					{
						$this->log("service_id already processed: {$elem['service_id']}");
						continue;
					}

					foreach ($columns as $key => $def)
					{
						$attr = empty($def['attribute']) ? 'innertext' : $def['attribute'];
						$elem['columns'][$key] = @$line->find($def['selector'],0)->$attr;
					}

					$item = $this->request->items()->create($elem);

					$items[$elem['service_id']] = $item->id;

					$lines_processed++;
				}

				$this->info("Page {$page} ({$lines_processed} items)");

				if ( $lines_found < 1 )
				{
					break;
				}

				$this->request->update([ 'last_page' => $page ]);
			}

			$this->log("total pages processed: " . number_format($page,0,',','.'));
			$this->log("total items found: " . number_format(count($items),0,',','.'));

			$this->request->update([
				'updated_at' => date('Y-m-d H:i:s'),
				//'finished_at' => date('Y-m-d H:i:s'),
			]);

			$this->log("finish processing");
		}
	}

	public function log($message, $type=false)
	{
		$log_message = date("Y-m-d-H:i:s") . ' -> ParseWebsiteCommand -> ' . $message;

		switch ( $type )
		{
			case 'error':
				\Log::error($log_message);
				break;
			case 'warning':
				\Log::warning($log_message);
				break;
			case 'info':
			default:
				\Log::info($log_message);
				break;
		}
	}

}
