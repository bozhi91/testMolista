<?php

namespace App\Http\Controllers\Account\Site;

use Illuminate\Http\Request;
use App\Http\Requests;
use Intervention\Image\ImageManagerStatic as Image;
use DB;

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

	//This methid will check if the blog page is created. If not, it will create it automatically.
    public static function createBlog(){

        $site_id = session('SiteSetup')['site_id'];
        //check if there is page for our blog in this site.
        $blog = DB::table('pages')
            ->select('*')
            ->where('type','blog')
            ->where('site_id',$site_id)
            ->get();

        //check if there is a menu created for the site. We need a menu in order to store our blog
        $menu = DB::table('menus')
            ->select('*')
            ->where('site_id',$site_id)
            ->get();

        //////////////////////////////////////////////////////////////////////////////////////////
        //if the page for our blog is not created for this site, create it.
        if(count($blog)==0){
            DB::table('pages')->insert(
                ['site_id' => $site_id,
                    'type' => 'blog',
                    'configuration'  => 'a:1:{s:7:"default";N;}',
                    'created_at'  => date("Y-m-d H-i-s"),
                ]
            );
            //get the pageId
            $page = DB::table('pages')
                 ->select('id')
               ->where('type','blog')
                ->where('site_id',$site_id)
                ->first();

            DB::table('pages_translations')->insert(
                [   'page_id' => $page->id,
                    'locale' => 'es',
                    'title' => 'Blog',
                    'slug' => 'blog',
                ]
            );

            //create the menu if it doesn't exist
            if(count($menu)==0){

                DB::table('menus')->insert(
                    ['site_id' => $site_id,
                        'title' => 'MyMenu',
                        'slug'  => 'mymenu',
                        'enabled'  =>1,
                        'created_at'=> date("Y-m-d H-i-s"),
                        'updated_at'=> date("Y-m-d H-i-s")
                    ]
                );

                //get the menuId
                $menu = DB::table('menus')
                    ->select('id')
                    ->where('enabled',1)
                    ->where('site_id',$site_id)
                    ->first();

                //Link the page to the menu
                DB::table('menus_items')->insert(
                    ['menu_id'    => $menu->id,
                        'page_id' => $page->id,
                        'type'    => 'page'
                    ]
                );

                //Link the menu to the widgets
                DB::table('widgets')->insert(
                    ['site_id'  => $site_id,
                        'group' => "header",
                        'type'  => 'menu',
                        'menu_id'  => $menu->id
                    ]
                );
            }
        }
        return $blog;
    }

    public function createNewPost(){
        return view('account.site.entradas.create', compact('entradas'));
    }
    public function storePost(){
        $site_id = session('SiteSetup')['site_id'];
        DB::table('entradas')->insert(
            ['site_id' => $site_id,
                'title' => $_POST ['title'],
                'body'  => $_POST ['body'],
                'created_at'  => date("Y-m-d H-i-s"),
            ]
        );
        $entradas =  PagesController::getAllPosts();
        return view('account.site.entradas.entradas', compact('entradas'));
    }

    public function listPosts(){
        $entradas =  PagesController::getAllPosts();
        return view('account.site.entradas.entradas', compact('entradas'));
    }

    public static function getAllPosts(){
	    $entradas = DB::table('entradas')
            ->select('*')
            ->get();
        return $entradas;
    }

    public static function getPostById($id){
        $post = DB::table('entradas')
            ->select('*')
            ->where('id',$id)
            ->get();
        return $post;
    }

    public static function updatePost(){

        $post = DB::table('entradas')
            ->where('id',$_POST ['post_id'])
            ->update(['title' => $_POST ['title'], 'body' => $_POST ['body']]);

        $entradas = PagesController::getAllPosts();
        return view('account.site.entradas.entradas', compact('entradas'));
    }

    public  function deletePost(){
	    //delete the post(by id)
        DB::table('entradas')->where('id',  $_POST ['post_id'])->delete();

        $entradas = PagesController::getAllPosts();
        return view('account.site.entradas.entradas', compact('entradas'));
    }
    ///////////////////////////////////////////////////////////////////////////////////////////////////////

	public function index()
	{
	    if(!empty($_GET['type'])){
	        switch($_GET['action']){

                case 'list':
                    $entradas = $this->listPosts();
                    return view('account.site.entradas.entradas',compact('entradas'));
                break;

                case 'new':
                    //$entradas = $this->blog();
                    return view('account.site.entradas.create');
                break;
            }
        }

		$pages = $this->site->pages()->orderBy('title')->paginate( $this->request->input('limit', \Config::get('app.pagination_perpage', 10)) );
		return view('account.site.pages.index', compact('pages'));
	}

    public function create()
	{
		$types = \App\Models\Site\Page::getTypeOptions();
		return view('account.site.pages.create', compact('types'));
	}

    public function store(){

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

		// Remove images & image folder
		\File::deleteDirectory( public_path($page->image_folder) );

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
					$img_base = str_slug($img_name).".{$mimetype}";
					$filepath = "{$image_dir}/{$img_base}";
					while ( file_exists( public_path( $filepath ) ) )
					{
						$filepath = "{$image_dir}/" . uniqid() . "_{$img_base}";
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
