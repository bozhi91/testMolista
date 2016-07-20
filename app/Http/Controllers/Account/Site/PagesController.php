<?php

namespace App\Http\Controllers\Account\Site;

use Illuminate\Http\Request;

use App\Http\Requests;

use Intervention\Image\ImageManagerStatic as Image;

class PagesController extends \App\Http\Controllers\AccountController
{

	protected $preserve_images = [];

	public function __initialize()
	{
		$this->middleware([ 'permission:site-edit' ]);

		parent::__initialize();
		\View::share('submenu_section', 'site');
		\View::share('submenu_subsection', 'site-pages');
	}

	public function index()
	{
		$pages = $this->site->pages()->orderBy('title')->paginate( $this->request->input('limit', \Config::get('app.pagination_perpage', 10)) );
		return view('account.site.pages.index', compact('pages'));
	}

	public function create()
	{
		$types = \App\Models\Site\Page::getTypeOptions();
		return view('account.site.pages.create', compact('types'));
	}
	public function store()
	{
		$validator = \Validator::make($this->request->all(), [
			'i18n.title.'.fallback_lang() => 'required',
			'type' => 'required|in:'.implode(',', array_keys(\App\Models\Site\Page::getTypeOptions())),
		]);
		if ($validator->fails()) 
		{
			return redirect()->back()->withInput()->withErrors($validator);
		}

		$page = $this->site->pages()->create([
			'type' => $this->request->input('type'),
			'enabled' => 0,
		]);

		if ( !$page )
		{
			return redirect()->back()->withInput()->with('error', trans('general.messages.error'));
		}

		$this->savePageTranslations($page, $this->request->input('i18n'));

		$page->save();

		// Get page with slug
		$page = $this->site->pages()->find($page->id);

		return redirect()->action('Account\Site\PagesController@edit', $page->slug)->with('success', trans('account/site.pages.create.success'));
	}

	public function edit($slug)
	{
		$page = $this->site->pages()->whereTranslation('slug', $slug)->first();
		if ( !$page )
		{
			abort(404);
		}

		return view('account.site.pages.edit', compact('page'));
	}

	public function update($slug)
	{
		$page = $this->site->pages()->whereTranslation('slug', $slug)->first();
		if ( !$page )
		{
			abort(404);
		}

		$fields = [
			'i18n' => 'required|array',
			'i18n.title' => 'required|array',
			'i18n.title.'.fallback_lang() => 'required',
			'i18n.body' => 'required|array',
			'i18n.seo_title' => 'required|array',
			'i18n.seo_keywords' => 'required|array',
			'i18n.seo_description' => 'required|array',
		];

		switch ( $page->type )
		{
			case 'contact':
				$fields['configuration.contact.email'] = 'required|email';
				break;
			case 'map':
				$fields['configuration.map.lat'] = 'required|numeric';
				$fields['configuration.map.lng'] = 'required|numeric';
				$fields['configuration.map.zoom'] = 'required|integer|min:0|max:21';
				break;
		}

		// Validate
		$validator = \Validator::make($this->request->all(), $fields);
		if ($validator->fails()) 
		{
			return redirect()->back()->withInput()->withErrors($validator);
		}

		$page->enabled = 1;
		$page->configuration = [ 
			$page->type => $this->request->input("configuration.{$page->type}") 
		];

		$this->savePageTranslations($page, $this->request->input('i18n'));

		$page->save();

		// Get page with slug
		$page = $this->site->pages()->find($page->id);

		// Update site setup
		$this->site->updateSiteSetup();

		return redirect()->action('Account\Site\PagesController@edit', $page->slug)->with('success', trans('account/site.pages.update.success'));
	}

	public function destroy($slug)
	{
		$page = $this->site->pages()->whereTranslation('slug', $slug)->first();
		if ( !$page )
		{
			abort(404);
		}

		// Remove images
		foreach( glob( public_path("{$page->image_folder}/*") ) as $file ) 
		{
			@unlink($file);
		}

		// Remove image folder
		rmdir( public_path($page->image_folder) );

		// Delete  page
		$page->delete();

		// Update site setup
		$this->site->updateSiteSetup();

		return redirect()->action('Account\Site\PagesController@index')->with('success', trans('account/site.pages.deleted.success'));
	}

	protected function savePageTranslations($page, $data)
	{
		// Images to preserve
		$this->preserve_images = [];

		foreach (\App\Session\Site::get('locales_tabs') as $locale => $locale_def)
		{
			$page->translateOrNew($locale)->title = @sanitize( $data['title'][$locale] );
			$page->translateOrNew($locale)->body = @clean( $this->prepareBodyImages($page, $data['body'][$locale] ) );
			$page->translateOrNew($locale)->seo_title = @sanitize( $data['seo_title'][$locale] );
			$page->translateOrNew($locale)->seo_description = @sanitize( $data['seo_description'][$locale] );
			$page->translateOrNew($locale)->seo_keywords = @sanitize( $data['seo_keywords'][$locale] );
		}

		// Images maitenance
		foreach( glob( public_path("{$page->image_folder}/*") ) as $file ) 
		{
			if ( in_array(basename($file), $this->preserve_images) )
			{
				continue;
			}

			@unlink($file);
		}

		return true;
	}

	protected function prepareBodyImages($page, $body)
	{
		if ( !$body )
		{
			return $body;
		}

		// Set image folder
		$image_dir = $page->image_folder;
		if ( !is_dir( public_path($image_dir) ) )
		{
			\File::makeDirectory(public_path($image_dir), 0777, true, true);
		}

		// Load dom
		$dom = new \DomDocument();
		libxml_use_internal_errors(true);
		$dom->loadHtml(mb_convert_encoding($body, 'HTML-ENTITIES', "UTF-8"), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
		libxml_clear_errors();

		// Get images
		$images = $dom->getElementsByTagName('img');

		foreach($images as $img)
		{
			$src = $img->getAttribute('src');

			if ( preg_match('#^/'.$image_dir.'/(.*)$#', $src, $matches) )
			{
				$this->preserve_images[] = $matches[1];
			}

			$classes = @explode(' ', $img->getAttribute('class'));
			if ( in_array('img-responsive', $classes) === false )
			{
				$classes[] = 'img-responsive';
				$img->removeAttribute('class');
				$img->setAttribute('class', implode(' ', $classes));
			}
			
			// If img source is 'data-url'
			if( preg_match('/data:image/', $src) )
			{
				// Get mimetype
				preg_match('/data:image\/(?<mime>.*?)\;/', $src, $groups);
				$mimetype = $groups['mime'];
				
				// Get file path
				if ( $img->getAttribute('data-filename') )
				{
					$img_name = pathinfo($img->getAttribute('data-filename'), PATHINFO_FILENAME);
					$filepath = "{$image_dir}/{$img_name}.{$mimetype}";
					while ( file_exists( public_path( $filepath ) ) )
					{
						$filepath = "{$image_dir}/" . uniqid() . "_{$img_name}.{$mimetype}";
					}
					$img->removeAttribute('data-filename');
				}
				else
				{
					$filepath = "{$image_dir}/" . uniqid() . ".{$mimetype}";
				}

				// See http://image.intervention.io/api/
				$image = Image::make($src)
					->resize(800, null, function ($constraint) {
						$constraint->aspectRatio();
					})
					->encode($mimetype, 100) 	// encode file to the specified mimetype
					->save( public_path( $filepath )	);
			
				$img->removeAttribute('src');
				$img->setAttribute('src', "/{$filepath}");

				if ( preg_match('#^'.$image_dir.'/(.*)$#', $filepath, $matches) )
				{
					$this->preserve_images[] = $matches[1];
				}
			}
		}

		$body = $dom->saveHTML();

		return $body;
	}

}
