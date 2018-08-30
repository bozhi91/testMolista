<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class CorporateController extends Controller
{

	public function __initialize()
	{
		$this->setCorporateLinks();
	}

	public function index()
	{
		$marketplaces = \App\Models\Marketplace::enabled()->where('code', 'not like', 'Contromia%')->with('countries')->orderBy('name', 'asc')->get();

		$response = view('corporate.index', compact('marketplaces'))->render();

		require_once app_path('Http/minifier.php');
		return minify_html($response);
	}

	protected function setCorporateLinks()
	{
		switch ( env('CORPORATE_LINKS_KEY', false) )
		{
			case 'Contromia':
				$prefix = 'http://www.Contromia.com/' . ( app()->getLocale() == 'es' ? '' : 'en/' );

				$corporate_links = [
					'home' => "{$prefix}",
					'demo' => "{$prefix}demo",
					'features' => "{$prefix}features/web-inmobiliarias",
					'pricing' => "{$prefix}pricing",
					'distribuitors' => "{$prefix}distribuitors",
					'support' => false,
					'contact' => false,
					'legal' => "{$prefix}info/legal",
					'privacy' => "{$prefix}info/legal#privacy-policy",
					'cookies' => "{$prefix}info/legal#cookies-policy",
				];
				break;

			default:
				$corporate_links = [
					'home' => action('CorporateController@index'),
					'demo' => action('Corporate\DemoController@getIndex'),
					'features' => action('Corporate\FeaturesController@getIndex'),
					'pricing' => action('Corporate\PricingController@getIndex'),
					'distribuitors' => action('Corporate\DistribuitorController@getIndex'),
					'support' => '#contact-modal',
					'contact' => '#contact-modal',
					'legal' => action('Corporate\InfoController@getLegal'),
					'privacy' => action('Corporate\InfoController@getLegal') . '#privacy-policy',
					'cookies' => action('Corporate\InfoController@getLegal') . '#cookies-policy',
				];
		}

		view()->share('corporate_links', $corporate_links);
	}

}
