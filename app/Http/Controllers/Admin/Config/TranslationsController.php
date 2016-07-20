<?php

namespace App\Http\Controllers\Admin\Config;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\AdminController;

class TranslationsController extends AdminController
{

	public function __initialize() {
		$this->middleware([ 'permission:translation-view' ], [ 'only' => [ 'index'] ]);
		$this->middleware([ 'permission:translation-edit' ], [ 'only' => [ 'update'] ]);

		parent::__initialize();
	}

	public function index()
	{
		$get_translations = true;

		$enabled_languages = \App\Models\Translation::getCachedLocales();
		$editable_languages = $this->auth->user()->translation_locales()->lists('native','locale')->toArray();
		if ( empty($editable_languages) )
		{
			$editable_languages = \App\Models\Translation::getCachedLocales();
		}

		// General validation
		$validator = \Validator::make($this->request->all(), [
			'base' => [ 'required','string','in:'.implode(',',array_keys($enabled_languages)) ],
			'langs' => [ 'required','array' ],
		]);
		if ( $validator->fails() )
		{
			$get_translations = false;
		}
		else
		{
			// Langs validation
			$rules = [];
			foreach ($this->request->input('langs') as $k=>$v)
			{
				$rules[$k] = [ 'required','string','in:'.implode(',',array_keys($enabled_languages)) ];
			}
			$validator = \Validator::make($this->request->input('langs'), $rules);
			if ( $validator->fails() )
			{
				$get_translations = false;
			}
		}

		// Get translations
		if ($get_translations)
		{
			$base = $this->request->input('base');
			$langs = $this->request->input('langs');

			$query = \App\Models\Translation::with('translations');

			// Filter by file
			if ( $this->request->input('file') )
			{
				$query->where('file', $this->request->input('file'));
			}

			// Filter by tag
			if ( $this->request->input('tag') )
			{
				$query->where('tag', 'LIKE', "%{$this->request->input('tag')}%");
			}

			// Filter by status
			if ( $this->request->input('status') )
			{
				$total_langs = count($langs);
				// If more than one language to edit,
				// we look for translations missing one of them
				if ($total_langs>1)
				{
					$subquery = "`translation_id`
									FROM (
									SELECT `translation_id`, COUNT(`id`) as total
									FROM `translations_translations`
									WHERE `value`!=''
									GROUP BY `translation_id`
									HAVING total < {$total_langs}
									) as e";
					switch ( $this->request->input('status') )
					{
						// Without translation
						case 'untranslated':
							$query->whereIn('id', function($query) use ($subquery) {
								$query->selectRaw($subquery);
							});
							break;
						// With translation
						case 'translated':
							$query->whereNotIn('id', function($query) use ($subquery) {
								$query->selectRaw($subquery);
							});
							break;
					}
				}
				// If only one language to edit,
				// we check rows with no translation in that language
				else
				{
					switch ( $this->request->input('status') )
					{
						// Without translation
						case 'untranslated':
							$query->whereNotIn('id', function($query) use ($langs) {
							$query->select('translation_id')
											->from('translations_translations')
											->where('value','!=','')
											->whereIn('locale', $langs);
							});
							break;
						// With translation
						case 'translated':
							$query->whereIn('id', function($query) use ($langs) {
							$query->select('translation_id')
											->from('translations_translations')
											->where('value','!=','')
											->whereIn('locale', $langs);
							});
							break;
					}
				}
			}

			if ( !$this->request->input('limit') )
			{
				$this->request->merge([ 'limit'=>25 ]);
			}

			$translations = $query->orderBy('file', 'asc')->orderBy('tag', 'asc')->paginate( $this->request->input('limit', \Config::get('app.custom_per_page')) );
		}
		// Get global stats
		else
		{
			// Get general stats
			$language_stats = [
				'total' => \App\Models\Translation::count(),
				'last_24' => \App\Models\Translation::whereDate('created_at','>=', date("Y-m-d", time()-(86400)) )->count(),
				'last_72' => \App\Models\Translation::whereDate('created_at','>=', date("Y-m-d", time()-(3*86400)) )->count(),
				'last_week' => \App\Models\Translation::whereDate('created_at','>=', date("Y-m-d", time()-(7*86400)) )->count(),
				'langs' => [],
			];

			// Get totals by language
			$group_totals = \App\Models\TranslationTranslation::selectRaw('`locale`, COUNT(*) as total')->where('value','!=','')->groupBy('locale')->lists('total','locale');

			// Process language totals
			foreach ($editable_languages as $iso_lang=>$lang_name)
			{
				$iso_total = @intval($group_totals[$iso_lang]);

				$language_stats['langs'][$iso_lang] = [
					'iso_lang' => $iso_lang,
					'title' => $lang_name,
					'total' => $iso_total,
					'percentage' => round((($iso_total/$language_stats['total'])*100),2),
				];
			}
		}

		$keys = \App\Models\Translation::distinct()->select('file')->orderBy('file')->lists('file', 'file')->toArray();

		return view('admin.config.translations.index', compact('translations','enabled_languages','keys','language_stats'));
	}

	public function update(Request $request, $id)
	{
		// Translation exists
		$file = \App\Models\Translation::where('id',$id)->value('file');
		if ( !$file )
		{
			return [ 'error'=>1 ];
		}

		// Validate data
		$validator = \Validator::make($this->request->all(), [
			'locale' => [ 'required','string' ],
		]);
		if ( $validator->fails() )
		{
			return [ 'error'=>1 ];
		}

		// Validate language
		if ( !$this->auth->user()->canTranslate($this->request->input('locale')) )
		{
			return [ 'error'=>1 ];
		}

		// Save translation
		$item = \App\Models\TranslationTranslation::firstOrCreate([
			'translation_id' => $id,
			'locale' => $this->request->input('locale'),
		]);

		if (!$item)
		{
			return [ 'error'=>1 ];
		}

		$item->value = \App\Models\Translation::cleanValue($this->request->input('value'));
		$item->save();

		// Update file
		\App\Models\Translation::compileTranslation($file,$this->request->input('locale'));

		return [ 'success'=>1 ];
	}

}
