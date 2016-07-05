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

	function price_symbol($iso='EUR')
	{
		$currency = \App\Property::getCurrencyOption($iso);
		return $currency ? $currency['symbol'] : false;
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

	function since_text($date)
	{
		$time = strtotime($date);

		// Minutes
		$diff = (time() - $time) / 60;
		if ( $diff < 60 )
		{
			if ( $diff < 0 )
			{
				$diff = 0;
			}
			return trans('general.since.minutes', [ 'minutes'=>floor($diff) ]);
		}

		// Hours
		$diff =  $diff / 60;
		if ( $diff < 24 )
		{
			return trans('general.since.hours', [ 'hours'=>floor($diff) ]);
		}

		// Yesterday
		if ( date("Ymd", $time) == date("Ymd") )
		{
			return trans('general.since.yesterday', [ 'time'=>date("H:i", $time) ]);
		}

		return trans('general.since.date', [ 'date'=>date("d/m/Y", $time), 'time'=>date("H:i", $time) ]);
	}

	function print_js_string($text)
	{
		$text = str_replace('"', '', $text);
		$text = trim($text);
		return $text;
	}

	// Draw pagination and includes limit selector on the right
	function drawPagination($collection, $appends=false, $csv_url=false)
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

		$csv_link = '';
		if ( $csv_url )
		{
			$csv_text = Lang::get('general.csv.download');
			$csv_link = "<div class='row'>
							<div class='col-xs-12 text-right'>
								<a href='{$csv_url}' class='download-results-link' target='_blank'>{$csv_text}</a>
							</div>
						</div>";
		}

		return	"<div class='pagination-custom'>
					{$csv_link}
					<div class='row'>
						<div class='col-xs-12 col-sm-7'>{$pagination_links}</div>
						<div class='col-sm-5 hidden-xs'>
							<ul class='pagination-limit list-inline'>
								<li><span class='pagination-limit-pages'>{$collection->currentPage()} / {$collection->lastPage()} {$pages_text}</span></li>
								<li>{$select}</li>
							</ul>
						</div>
					</div>
				</div>";
	}

	function drawTicketsPagination($url,$data)
	{
		$total = @intval( $data['total_pages'] );
		if ( !$total || $total < 2 ) {
			return;
		}

		$page = @intval( $data['page'] );
		if ( !$page ) {
			return;
		}

		$limit = @intval( $data['limit'] );
		if ( !$limit ) {
			return;
		}

		// Prepare url
		@list($href,$query) = explode('?',$url);

		if ( $query )
		{
			parse_str($query,$parts);
			unset($parts['limit'], $parts['page']);
		}

		if ( empty($parts) )
		{
			$href .= "?limit={$limit}&page=";
		}
		else
		{
			$href .= '?'.http_build_query($parts)."&limit={$limit}&page=";
		}

		$pags =	'<div class="row pagination-custom">' .
					'<div class="col-xs-12">' .
						'<ul class="pagination">';

		$range = 3;

		$first_page = $page - $range;
		if ( $first_page < 1 ) 
		{
			$first_page = 1;
		}

		$last_page = $page + $range;
		if ( $last_page > $total ) 
		{
			$last_page = $total;
		}

		if ( $page > 1 )
		{
			$tmp = $page - 1;
			$pags .= "<li><a href='{$href}{$tmp}'>«</a></li>";
		}
		else
		{
			$pags .= '<li class="disabled"><span>«</span></li>';
		}

		if ( $first_page > 1 )
		{
			$pags .= "<li><a href='{$href}1'>1</a></li>";
		}

		if ( $first_page > 2 )
		{
			$pags .= '<li class="disabled"><span>...</span></li>';
		}

		for ($i = $first_page; $i<=$last_page; $i++)
		{
			if ( $i < 1 )
			{
				continue;
			}
			if ( $i > $total )
			{
				continue;
			}

			if ( $i == $page )
			{
				$pags .= "<li class='active' data-url='{$href}{$i}'><span>{$i}</span></li>";
			}
			else
			{
				$pags .= "<li><a href='{$href}{$i}'>{$i}</a></li>";

			}
		}

		if ( $last_page < $total - 1 )
		{
			$pags .= '<li class="disabled"><span>...</span></li>';
		}

		if ( $last_page < $total )
		{
			$pags .= "<li><a href='{$href}{$total}'>{$total}</a></li>";
		}
	
		if ( $page < $total )
		{
			$tmp = $page + 1;
			$pags .= "<li><a href='{$href}{$tmp}'>»</a></li>";
		}
		else
		{
			$pags .= '<li class="disabled"><span>»</span></li>';
		}

		$pags .= 		'</ul>' .
					'</div>' .
				'</div>';

		return $pags;
	}

	function getParsedUrl($url)
	{
		// Prepare url
		$query_parts = [];

		@list($href,$query) = explode('?',$url);
		if ( $query )
		{
			parse_str($query,$query_parts);
		}

		return [ $href, $query_parts ];
	}

	function drawSortableHeaders($url,$columns)
	{
		@list($href,$query) = getParsedUrl($url);

		if ( !$query )
		{
			$query = [];
		}

		$orderby = @$query['orderby'];
		unset($query['orderby']);

		$order = @$query['order'];
		unset($query['order']);

		$query['page'] = 1;

		$str = '';

		foreach ($columns as $key => $def) 
		{
			$str .= '<th class="' . @$def['class'] . '">';

			if ( isset($def['sortable']) && !$def['sortable']) 
			{
				$str .= @$def['title'];
			}
			else
			{

				$tmp_order = 'asc';
				$tmp_class = 'is-sortable';

				if ($orderby == $key)
				{
					$tmp_order = ($order == 'asc') ? 'desc' : 'asc';
					$tmp_class .= " text-nowrap sorted {$tmp_order}";
				}

				$tmp_url = $href . '?' . http_build_query(array_merge($query, [
					'orderby' => $key,
					'order' => $tmp_order,
				]));

				$str .= '<a href="' . $tmp_url . '" class="' . $tmp_class . '">' . @$def['title'] . '</a>';
			}

			$str .= '</th>';
		}

		return $str;
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

	function percent_array() 
	{
		$select = [];

		for ($i=0; $i<=100; $i++)
		{
			$select[$i] = "{$i}%";
		}

		return $select;
	}
