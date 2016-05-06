<?php

	function nl2p($text)
	{
		$text = explode("\n", $text);
		$text = array_filter($text);
		return '<p>' . implode('</p><p>', $text). '</p>';
	}

	function price($price, $params=false)
	{
		if ( !is_array($params) )
		{
			$params = [];
		}

		$iso = isset($params['iso']) ? $params['iso'] : \App\Session\Currency::get('iso', 'EUR');
		$symbol = isset($params['symbol']) ? $params['symbol'] : \App\Session\Currency::get('symbol', '€');
		$decimals = isset($params['decimals']) ? $params['decimals'] : \App\Session\Currency::get('decimals', 2);
		$position = isset($params['position']) ? $params['position'] : \App\Session\Currency::get('position', 'after');

		$currency = [];

		if ( $position == 'before' )
		{
			$currency[] = $symbol;
		}

		$currency[] = number_format($price, $decimals, ',', '.');

		if ( $position == 'after' )
		{
			$currency[] = $symbol;
		}

		return implode(' ', $currency);
	}

	function print_goback_button($text, $attr=false)
	{
    	$nav = session()->get('SmartBackLinks', false);
    	if ( !$nav ) return false;

    	foreach ($nav as $url => $prev)
    	{
    		if ( strpos(url()->current(), $url) === false )
    		{
    			continue;
    		}
    		if ( !is_array($attr) )
    		{
    			$attr = [ $attr ];
    		}

    		$input = [ 'href="' . $prev . '"' ];
    		foreach ($attr as $key => $value) 
    		{
    			$input[] = "{$key}=\"{$value}\"";
    		}

    		return  "<a " . implode(' ', $input) . ">{$text}</a>";
    	}

    	return false;
	}

	function print_js_string($text)
	{
		$text = str_replace('"', '', $text);
		$text = trim($text);
		return $text;
	}

	// Draw pagination and includes limit selector on the right
	function drawPagination($collection, $appends=false)
	{
		if ( !$collection )
		{
			return false;
		}

		if ( !empty($appends) && is_array($appends) )
		{
			$collection->appends( $appends );
		}

		$pagination_links = ($collection->lastPage() <= 1) ? false : $collection->links();

		$perpage_default = Config::get('app.pagination_perpage', 10);

		$perpage_options = [ 10, 25, 50, 100, 500 ];
		if ( !in_array($perpage_default, $perpage_options))
		{
			$perpage_options[] = $perpage_default;
			asort($perpage_options);
		}


		$url_parts = parse_url( $collection->url(1) );
		@parse_str( $url_parts['query'], $query );
		unset($query['limit']);
		$select_url = "{$url_parts['scheme']}://{$url_parts['host']}{$url_parts['path']}?" . http_build_query($query) . "&limit=";

		$pages_text = Lang::get('general.pages');
		$perpage_text = Lang::get('general.pages.per');
		$perpage_current = empty($appends['limit']) ? $perpage_default : $appends['limit'];

		$select = '<select class="form-control pagination-limit-select" onchange="document.location.href=this.value;">';
		foreach ($perpage_options as $limit) 
		{
			$sel = ($perpage_current == $limit) ? "selected='selected'" : '';
			$select .= "<option value='{$select_url}{$limit}' {$sel}>{$limit} {$perpage_text}</option>";
		}
		$select .= '</select>';

		return	"<div class='row pagination-custom'>
					<div class='col-xs-12 col-sm-7'>{$pagination_links}</div>
					<div class='col-sm-5 hidden-xs'>
						<ul class='pagination-limit list-inline'>
							<li><span class='pagination-limit-pages'>{$collection->currentPage()} / {$collection->lastPage()} {$pages_text}</span></li>
							<li>{$select}</li>
						</ul>
					</div>
				</div>";
	}

	function sort_link($field) 
	{
		return url()->current() . '?' . http_build_query(Input::except('sort')) . '&sort=' . $field;
	}

	function fallback_lang() 
	{
		return Config::get('app.fallback_locale');
	}
	function fallback_lang_text() 
	{
		$locales = LaravelLocalization::getSupportedLocales();
		return @$locales[fallback_lang()]['native'];
	}
	function lang_text($locale) 
	{
		$locales = LaravelLocalization::getSupportedLocales();
		return @$locales[$locale]['native'];
	}

	function summetime_lang() 
	{
		return str_replace('_','-', LaravelLocalization::getCurrentLocaleRegional() );
	}

	function sanitize($string, $type = false) 
	{
		switch ( $type )
		{
			case 'url':
				return trim( filter_var($string, FILTER_SANITIZE_URL) );
			case 'email':
				return trim( filter_var($string, FILTER_SANITIZE_EMAIL) );
			case 'text':
			default:
				return trim( filter_var($string, FILTER_SANITIZE_STRING) );
		}
	}