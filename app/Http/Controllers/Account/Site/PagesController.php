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
		$query = $this->site->pages()->withTranslations();

		$pages = $query->orderBy('title')->paginate( $this->request->get('limit', \Config::get('app.pagination_perpage', 10)) );

		return view('account.site.pages.index', compact('pages'));
	}

	public function create()
	{
		return view('account.site.pages.create');
	}
	public function store()
	{
		$validator = \Validator::make($this->request->all(), $this->getRequestFields());
		if ($validator->fails()) 
		{
			return redirect()->back()->withInput()->withErrors($validator);
		}

		$page = $this->site->pages()->create([
			'enabled' => 1,
		]);

		if ( !$page )
		{
			return redirect()->back()->withInput()->with('error', trans('general.messages.error'));
		}

		$this->savePageTranslations($page, $this->request->get('i18n'));

		$page->save();

		// Get page with slug
		$page = $this->site->pages()->withTranslations()->find($page->id);

		\App\Session\Site::flush();

		return redirect()->action('Account\Site\PagesController@edit', $page->slug)->with('success', trans('account/site.pages.create.success'));
	}

	public function edit($slug)
	{
		$page = $this->site->pages()->withTranslations()->whereTranslation('slug', $slug)->first();
		if ( !$page )
		{
			abort(404);
		}

		return view('account.site.pages.edit', compact('page'));
	}

	public function update($slug)
	{
		$page = $this->site->pages()->withTranslations()->whereTranslation('slug', $slug)->first();
		if ( !$page )
		{
			abort(404);
		}

		$this->savePageTranslations($page, $this->request->get('i18n'));

		$page->save();

		// Get page with slug
		$page = $this->site->pages()->withTranslations()->find($page->id);

		\App\Session\Site::flush();

		return redirect()->action('Account\Site\PagesController@edit', $page->slug)->with('success', trans('account/site.pages.update.success'));
	}

	public function destroy($slug)
	{
		$page = $this->site->pages()->withTranslations()->whereTranslation('slug', $slug)->first();
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

		\App\Session\Site::flush();

		return redirect()->action('Account\Site\PagesController@index')->with('success', trans('account/site.pages.deleted.success'));
	}

	protected function getRequestFields($id=false) 
	{
		$fields = [
			'i18n' => 'required|array',
			'i18n.title' => 'required|array',
			'i18n.title.en' => 'required',
			'i18n.body' => 'required|array',
			'i18n.body.en' => 'required',
			'i18n.seo_title' => 'required|array',
			'i18n.seo_keywords' => 'required|array',
			'i18n.seo_description' => 'required|array',
		];

		return $fields;
	}

	protected function savePageTranslations($page, $data)
	{
		// Images to preserve
		$this->preserve_images = [];

		foreach (\LaravelLocalization::getSupportedLocales() as $locale => $locale_def)
		{
			$page->translateOrNew($locale)->title = @$data['title'][$locale];
			$page->translateOrNew($locale)->body = $this->prepareBodyImages($page, @$data['body'][$locale] );
			$page->translateOrNew($locale)->seo_title = @$data['seo_title'][$locale];
			$page->translateOrNew($locale)->seo_description = @$data['seo_description'][$locale];
			$page->translateOrNew($locale)->seo_keywords = @$data['seo_keywords'][$locale];
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
		$dom->loadHtml($body, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
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
					$img_name = pathinfo($img->getAttribute('data-filename'), PATHINFO_FILENAME );
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
