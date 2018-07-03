<?php

namespace App\Http\Controllers\Account\Site;

use Illuminate\Http\Request;
use App\Http\Requests;
use Intervention\Image\ImageManagerStatic as Image;
use DB;

class BlogController extends \App\Http\Controllers\AccountController
{
	protected $preserve_images = [];
	public function __initialize()
	{
	//	$this->middleware([ 'permission:site-blog' ]);
		parent::__initialize();
		\View::share('submenu_section', 'site');
		\View::share('submenu_subsection', 'site-blog');
	}

    public static function getSiteById($site_id){
	    $sites = DB::table('sites')
            ->select('*')
            ->where('id',$site_id)
            ->first();
	    return $sites;
    }

    //returns 'true' if the blog is created. 'False' otherwise
    public static function getBlog(){
        $site_id = session('SiteSetup')['site_id'];
        //Check if there is page for our blog in this site.

        $blog = DB::table('pages')
            ->select('*')
            ->where('type','blog')
            ->where('site_id',$site_id)
            ->get();

        if(count($blog)==0){
            return false;
        }
        return true;
    }

    //check if the blog is activated in the menu
    public static function isBlogActivated(){
        $site_id = session('SiteSetup')['site_id'];
        //get the menu for this site
        $menu = DB::table('menus')
            ->select('*')
            ->where('enabled',1)
            ->where('site_id',$site_id)
            ->first();

        if($menu!=null){
            //get the pageId of the blog we just created
            $page = DB::table('pages')
                ->select('id')
                ->where('type','blog')
                ->where('site_id',$site_id)
                ->first();

            if(!empty($page)){
                $menu_item = DB::table('menus_items')
                    ->select('id')
                    ->where('menu_id',$menu->id)
                    ->where('page_id',$page->id)
                    ->first();
                return $menu_item;
            }
        }
        return false;
    }

    //This methid will check if the blog page is created. If not, it will create it automatically.
    public function createNewBlog(){
        $site_id = session('SiteSetup')['site_id'];
        //Check if there is page for our blog in this site.
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
            //get the pageId of the blog we just created
            $page = DB::table('pages')
                ->select('id')
                ->where('type','blog')
                ->where('site_id',$site_id)
                ->first();

            DB::table('pages_translations')->insert(
                [   'page_id' => $page->id,
                    'locale'  => 'es',
                    'title'   => 'Blog',
                    'slug'    => 'blog',
                ]
            );
        }
        $entradas =  BlogController::getAllPosts();
        return view('account.site.entradas.entradas', compact('entradas'));
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
        $entradas =  BlogController::getAllPosts();
        return view('account.site.entradas.entradas', compact('entradas'));
    }

    public function listPosts(){
        $entradas =  BlogController::getAllPosts();
        return view('account.site.entradas.entradas', compact('entradas'));
    }

    public static function getAllPosts(){
        $site_id = session('SiteSetup')['site_id'];
	    $entradas = DB::table('entradas')
            ->select('*')
            ->where('site_id',$site_id)
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

        $entradas = BlogController::getAllPosts();
        return view('account.site.entradas.entradas', compact('entradas'));
    }

    public  function deletePost(){
	    //delete the post(by id)
        DB::table('entradas')->where('id',  $_POST ['post_id'])->delete();

        $entradas = BlogController::getAllPosts();
        return view('account.site.entradas.entradas', compact('entradas'));
    }
    ///////////////////////////////////////////////////////////////////////////////////////////////////////
}
