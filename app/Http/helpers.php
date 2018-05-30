<?php

	function nl2p($text)
	{
		$text = explode("\n", $text);
		$text = array_filter($text);
		return '<p>' . implode('</p><p>', $text). '</p>';
	}

	function price($price, $params=false, $propId = null)
	{
		if ( is_object($params) && method_exists($params,'toArray') )
		{
			$params = $params->toArray();
		}

		if ( !is_array($params) )
		{
			$params = [];
		}

		$iso = isset($params['iso']) ? $params['iso'] : \App\Session\Currency::get('iso', 'EUR');
		$symbol = isset($params['symbol']) ? $params['symbol'] : \App\Session\Currency::get('symbol', '€');
		$decimals = isset($params['decimals']) ? $params['decimals'] : \App\Session\Currency::get('decimals', 0);
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

		//TODO: move DESDE to translations
        //	{{ Lang::get('web/properties.more.room') }}
       if($propId['desde']!=null){
            return "".implode(' ', $currency);
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
    		if ( strpos(url_current(), $url) === false )
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
		return url_current() . '?' . http_build_query(Input::except('sort')) . '&sort=' . $field;
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
	function lang_dir($locale=false)
	{
		if ( !$locale )
		{
			$locale = LaravelLocalization::getCurrentLocaleName();
		}

		$locales = LaravelLocalization::getSupportedLocales();

		$info = @$locales[$locale];

		return @$info['dir'] ? $info['dir'] : 'ltr';
	}

	function summetime_lang($locale=false)
	{
		if ( !$locale )
		{
			$locale = LaravelLocalization::getCurrentLocale();
		}

		switch ( $locale )
		{
			case 'ar':
				return 'ar-AR';
		}

		$locales = LaravelLocalization::getSupportedLocales();

		$info = @$locales[$locale];

		$regional = @$info['regional'] ? $info['regional'] : LaravelLocalization::getCurrentLocaleRegional();

		return str_replace('_','-', $regional);
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

	function translate_marketplace_error($message)
	{
		// EN
		if (preg_match('#^The ([\w\s\.\-]+) field#', $message, $match)) {}
		// ES
		elseif (preg_match('#^El campo ([\w\s\.\-]+) es#', $message, $match)) {}

		if (!empty($match[1])) {
			$field = preg_replace('#\s#', '_', $match[1]);
			$translation_field = 'account/properties.'.$field;
			if (\Lang::has($translation_field)) {
				$message = preg_replace('#'.$match[1].'#', '"'.\Lang::get($translation_field).'"', $message);
			}
		}

		return $message;
	}

	function url_current()
	{
		$full = url()->full();
		$parts = explode('?', $full);
		return $parts[0];
	}

	function moment_lang()
	{
		switch ( strtolower(LaravelLocalization::getCurrentLocaleScript()) )
		{
			case 'arab':
				return 'en';

		}

		return LaravelLocalization::getCurrentLocale();
	}

	function site_url($url, \App\Site $site = null)
	{
		if (!$site) return $url;

		// Use always the main domain
		$parts = parse_url($url);

		$final = trim($site->main_url);

		if (!empty($parts['path'])) {
			$final .= $parts['path'];
		}

		if (!empty($parts['query'])) {
			$final .= '?'.$parts['query'];
		}

		if (!empty($parts['fragment'])) {
			$final .= '#'.$parts['fragment'];
		}

		return $final;
	}

	function email_render($view, $data = null, $css = null)
	{
		$content = view($view, $data)->render();

		if ($css) {
			$css_path = base_path($css);
			if ( file_exists($css_path) )
			{
				$emogrifier = new \Pelago\Emogrifier($content, file_get_contents($css_path));
				$content = $emogrifier->emogrify();
			}
		}

		return $content;
	}

	function email_render_corporate($view, $data = null)
	{
		return email_render($view, $data, 'resources/assets/css/emails/corporate.css');
	}

	function linkify($string)
	{
		return preg_replace('@(http)(s)?(://)?(([a-zA-Z])([-\w]+\.)+([^\s\.]+[^\s]*)+[^,.\s])@', '<a href="http$2://$4" target="_blank" title="$0">$0</a>', $string);
	}

	function message($text)
	{
		$prefix = 'message_'.uniqid();
		$html_id = 'html_'.$prefix;
		$body_id = 'body_'.$prefix;

		// Replace thml tag
		$text = preg_replace('#<html#ims', '<div id="'.$html_id.'"', $text);
		$text = preg_replace('#<\/html>#ims', '</div>', $text);

		// Replace body tag
		$text = preg_replace('#<body#ims', '<div id="'.$body_id.'"', $text);
		$text = preg_replace('#<\/body>#ims', '</div>', $text);

		// Remove DOCTYPE
		$text = preg_replace('#<!DOCTYPE.+?>#ims', '', $text);

		// Remove meta tags
		$text = preg_replace('#<meta .+?(>|\/>)#ims', '', $text);

		// Remove script tags
		$text = preg_replace('#<script .+?<\/script>#ims', '', $text);

		// Remove "javascript:" actions
		$text = preg_replace('#javascript\:#', '', $text);

		// Remove all links
		$text = preg_replace('#href="(.+?)"#', 'href="javascript:alert(\'Link disabled for security:\n$1\');"', $text);

		// Prefix css: ([^>\r\n,{}]+)(,(?=[^}]*{)|\s*{)
		if (preg_match_all('#<style .+?<\/style>#ims', $text, $match)) {
			// 2. Remove them from the text
			$text = preg_replace('#<style .+?<\/style>#ims', '', $text);

			foreach ($match[0] as $style) {
				// Replace the body for the body tag
				$style = preg_replace('#(body)(,(?=[^}]*{)|\s*{)#ims', "#$body_id$2", $style);

				// Add prefix in all the tags
				$style = preg_replace('#([^>\r\n,{}]+)(,(?=[^}]*{)|\s*{)#ims', "#$prefix $1$2", $style);
				$text .= $style;
			}
		}

		return '<div id="'.$prefix.'" style="overflow-x: auto;">'.$text.'</div>';
	}
