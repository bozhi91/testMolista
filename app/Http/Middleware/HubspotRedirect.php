<?php namespace App\Http\Middleware;

use Closure;

class HubspotRedirect
{
	protected $redirects = [
		'/' => 'http://www.molista.com/',
		'en' => 'http://www.molista.com/en',
		'demo' => 'http://www.molista.com/demo',
		'en/demo' => 'http://www.molista.com/en/demo',
		'pricing' => 'http://www.molista.com/pricing',
		'en/pricing' => 'http://www.molista.com/en/pricing',
		'distribuitors' => 'http://www.molista.com/distribuitors',
		'en/distribuitors' => 'http://www.molista.com/en/distribuitors',
		'info/legal' => 'http://www.molista.com/info/legal',
		'en/info/legal' => 'http://www.molista.com/en/info/legal',
		'features/web-inmobiliarias' => 'http://www.molista.com/features/web-inmobiliarias',
		'en/features/web-inmobiliarias' => 'http://www.molista.com/en/features/web-inmobiliarias',
		'features/gestion-inmobiliaria' => 'http://www.molista.com/features/gestion-inmobiliaria',
		'en/features/gestion-inmobiliaria' => 'http://www.molista.com/en/features/gestion-inmobiliaria',
		'features/agente-inmobiliario' => 'http://www.molista.com/features/agente-inmobiliario',
		'en/features/agente-inmobiliario' => 'http://www.molista.com/en/features/agente-inmobiliario',
		'features/clientes-inmobiliaria' => 'http://www.molista.com/features/clientes-inmobiliaria',
		'en/features/clientes-inmobiliaria' => 'http://www.molista.com/en/features/clientes-inmobiliaria',
		'features/portales-inmobiliarios' => 'http://www.molista.com/features/portales-inmobiliarios',
		'en/features/portales-inmobiliarios' => 'http://www.molista.com/en/features/portales-inmobiliarios',
		'features/agenda' => 'http://www.molista.com/features/agenda',
		'en/features/agenda' => 'http://www.molista.com/en/features/agenda',
		'starter' => 'http://www.molista.com/',
	];

	public function handle($request, Closure $next)
	{
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

}
