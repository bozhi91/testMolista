<?php

namespace App\Http\Controllers\Account\Site;

use Illuminate\Http\Request;

use App\Http\Requests;

class MenusController extends \App\Http\Controllers\AccountController
{
	protected $menus = [];

	public function __initialize()
	{
		$this->middleware([ 'permission:site-edit' ]);

		parent::__initialize();
		\View::share('submenu_section', 'site');
		\View::share('submenu_subsection', 'site-menus');

		if ( $this->site )
		{
			$this->menus = $this->site->menus()->get();
			\View::share('menus', $this->menus);
		}

	}

	public function index()
	{
		if ( $this->menus->count() )
		{
			$first = $this->menus->first();	
			return $this->edit($first->slug);
		}

		return $this->create();
	}

	public function create()
	{
		return view('account.site.menus.create');
	}

	public function store()
	{
		$validator = \Validator::make($this->request->all(), [
			'title' => 'required|max:255',
		]);
		if ($validator->fails()) 
		{
			return redirect()->back()->withInput()->withErrors($validator);
		}

		$menu = $this->site->menus()->create([
			'title' => $this->request->get('title'),
		]);

		if ( !$menu )
		{
			return redirect()->back()->withInput()->with('error', trans('general.messages.error'));
		}

		\App\Session\Site::flush();

		return redirect()->action('Account\Site\MenusController@edit', $menu->slug)->with('success', trans('account/site.menus.create.success'));
	}

	public function edit($slug)
	{
		$menu = $this->site->menus()->whereSlug($slug)->withItems()->firstOrFail();

		$pages = $this->site->pages()->withTranslations()->orderBy('title')->lists('title','id')->all();
		$properties = $this->site->properties()->withTranslations()->orderBy('title')->lists('title','id')->all();

		return view('account.site.menus.edit', compact('menu','pages','properties'));
	}

	public function update($slug)
	{
		$menu = $this->site->menus()->whereSlug($slug)->firstOrFail();

		$validator = \Validator::make($this->request->all(), [
			'title' => 'required|max:255',
			'items' => 'array',
		]);
		if ($validator->fails()) 
		{
			return redirect()->back()->withInput()->withErrors($validator);
		}

		// Save menu
		$menu->update([
			'title' => $this->request->get('title'),
		]);

		// Get old items
		$current_items = $menu->items->keyBy('id')->all();

		// Save items
		$saved_items = $this->request->get('items');
		if ( !is_array($saved_items) )
		{
			$saved_items = [];
		}

		// Update items
		$position = 0;
		foreach ($saved_items as $item_id => $request)
		{
			// Check if exists
			if ( !array_key_exists($item_id, $current_items) )
			{
				continue;
			}

			// Get item
			$item = $current_items[$item_id];

			// Validate
			$validator = \Validator::make($request, $this->getItemTypeFields($item->type));
			if ( $validator->fails())
			{
				continue;
			}

			// Update item
			$item->update([
				'target' => @$request['target'],
				'position' => $position,
			]);

			// Save type related values
			$this->saveItemTypeValues($item, $item->type, $request);

			// Save translations
			$this->saveItemTranslations($item, $item->type, $request);

			// Save item
			$item->save();

			// Preserve item
			unset($current_items[$item_id]);

			// Increment position
			$position++;
		}

		// Delete items
		foreach ($current_items as $item) 
		{
			$item->delete();
		}

		\App\Session\Site::flush();

		return redirect()->action('Account\Site\MenusController@edit', $menu->slug)->with('success', trans('account/site.menus.update.success'));
	}

	public function destroy($slug)
	{
		$menu = $this->site->menus()->whereSlug($slug)->firstOrFail();

		$menu->delete();

		\App\Session\Site::flush();

		return redirect()->action('Account\Site\MenusController@index')->with('success', trans('account/site.menus.delete.success'));
	}

	public function postItem($slug)
	{
		$menu = $this->site->menus()->whereSlug($slug)->firstOrFail();

		$item_type = $this->request->input('items.new.type');

		// Get fields
		$fields = $this->getItemTypeFields($item_type);

		if ( $fields === false )
		{
			abort(400);
		}

		$request = $this->request->input('items.new');

		// Validate
		$validator = \Validator::make($request, $fields);
		if ( $validator->fails() ) 
		{
			return redirect()->back()->withInput()->withErrors($validator);
		}

		// Define position
		$last_item = $menu->items()->orderBy('position','desc')->first();
		$position = $last_item ? $last_item->position + 1 : 0;

		// Create item
		$item = $menu->items()->create([
			'type' => $item_type,
			'target' => @$request['target'],
			'position' => $position,
		]);

		// Save type related values
		$this->saveItemTypeValues($item, $item_type, $request);

		// Save translations
		$this->saveItemTranslations($item, $item_type, $request);

		// Save item
		$item->save();

		\App\Session\Site::flush();

		return redirect()->action('Account\Site\MenusController@edit', $menu->slug)->with('success', trans('account/site.menus.update.success'));
	}

	protected function saveItemTranslations($item, $type, $data)
	{
		foreach (\App\Session\Site::get('locales_tabs') as $locale => $locale_name)
		{
			$item->translateOrNew($locale)->title = @$data['title'][$locale];

			switch ( $type )
			{
				case 'custom':
					$item->translateOrNew($locale)->url = @$data['url'][$locale];
					break;
			}
		}

		return true;
	}

	protected function saveItemTypeValues($item, $type, $data)
	{
		switch ( $type )
		{
			case 'custom':
				return true;
			case 'page':
				$item->page_id = $data['page_id'];
				return true;
			case 'property':
				$item->property_id = $data['property_id'];
				return true;
		}

		return false;
	}

	protected function getItemTypeFields($type)
	{
		switch ( $type )
		{
			case 'custom':
				return [
					'title' => 'required|array',
					'title.'.fallback_lang() => 'required|string',
					'url' => 'required|array',
					'url.'.fallback_lang() => 'required|url',
					'target' => '',
				];
			case 'property':
				return [
					'title' => 'array',
					'property_id' => 'required|integer|exists:properties,id,site_id,'.$this->site->id,
				];
			case 'page':
				return [
					'title' => 'array',
					'page_id' => 'required|integer|exists:pages,id,site_id,'.$this->site->id,
				];
		}

		return false;
	}

}
