<?php namespace App\Http\Middleware;

use Closure;

class HubspotRedirect
{
	protected $redirects = [];

	public function handle($request, Closure $next)
	{
		$this->setRedirectsUrls();

		if ( $redirect_url = $this->getRedirectUrl($request) )
		{
			return redirect()->to($redirect_url, 301); 
		}

		return $next($request);
	}

	protected function getRedirectUrl($request)
	{
		uksort($this->redirects, function($a, $b) {
			$al = strlen($a);
			$bl = strlen($b);
			return $al == $bl ? 0 : ( $al > $bl ? -1 : 1 );
		});

		foreach ($this->redirects as $redirect_from => $redirect_to)
		{
			$redirect_array = [];

			if ($redirect_from == '/')
			{
				$redirect_array = [ $redirect_from ];
			}
			else
			{
				$redirect_from = trim($redirect_from, '/');
				$redirect_array = [ $redirect_from, "*/{$redirect_from}" ];
			}

			foreach ($redirect_array as $from)
			{
				if ( $request->is($from) )
				{
					return $redirect_to;
				}
			}
		}

		// Check if is home in other language
		if ( !$request->segment(2) && strlen($request->segment(1)) == 2 )
		{
			if ( array_key_exists('/', $this->redirects) )
			{
				return $this->redirects['/'];
			}
		}

		return false;
	}

	protected function setRedirectsUrls()
	{
		if ( $redirect_key = env('HUBSPOST_REDIRECT_KEY', false) )
		{
			if ( $redirects = config()->get("hubspot-redirect.{$redirect_key}") )
			{
				if ( is_array($redirects) )
				{
					$this->redirects = $redirects;
				}
			}
		}
	}

}
