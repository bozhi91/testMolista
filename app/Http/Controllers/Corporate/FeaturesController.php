<?php

namespace App\Http\Controllers\Corporate;

use Illuminate\Http\Request;

use App\Http\Requests;

class FeaturesController extends \App\Http\Controllers\CorporateController
{

	public function getIndex($slug=false)
	{
		switch ( \LaravelLocalization::getCurrentLocale() )
		{
			default:
				$tab_options = [
					'tab1' => 'web-inmobiliarias',
					'tab2' => 'gestion-inmobiliaria',
					'tab3' => 'agente-inmobiliario',
					'tab4' => 'clientes-inmobiliaria',
					'tab5' => 'portales-inmobiliarios',
				];
		}

		$current_tab = array_search($slug, $tab_options);

		if ( !$current_tab )
		{
			return redirect()->action('Corporate\FeaturesController@getIndex', reset($tab_options), 301);
		}


		return view('corporate.features.index', compact('current_tab','tab_options'));
	}

}
