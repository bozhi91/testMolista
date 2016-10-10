<?php

namespace App\Http\Controllers\Account\Site;

use Illuminate\Http\Request;

use App\Http\Requests;

class WidgetsController extends \App\Http\Controllers\AccountController
{

	public function __initialize()
	{
		$this->middleware([ 'permission:site-edit' ]);

		parent::__initialize();
		\View::share('submenu_section', 'site');
		\View::share('submenu_subsection', 'site-widgets');
	}

	public function getIndex()
	{
		$widgets = $this->site->widgets()->get();

		$type_options = \App\Models\Site\Widget::getTypeOptions();
		$group_options = \App\Models\Site\Widget::getGroupOptions();

		$menus = $this->site->menus()->lists('title','id')->all();
		$sliders = $this->site->slidergroups()->lists('name', 'id')->all();

		return view('account.site.widgets.index', compact('widgets', 'type_options', 'group_options', 'menus', 'sliders'));
	}

	public function getStore()
	{
		$validator = \Validator::make($this->request->all(), [
			'type' => 'required|in:'.implode(',', \App\Models\Site\Widget::getTypeOptions()),
			'group' => 'required|in:'.implode(',', array_keys(\App\Models\Site\Widget::getGroupOptions())),
		]);
		if ($validator->fails())
		{
			return [ 'error'=>true ];
		}

		$last_item = $this->site->widgets()->where('group',$this->request->input('group'))->orderBy('position','desc')->first();
		$position = $last_item ? $last_item->position + 1 : 0;

		$widget = $this->site->widgets()->create([
			'type' => $this->request->input('type'),
			'group' => $this->request->input('group'),
			'position' => $position,
		]);

		$data = [
			'type' => $widget->type,
			'item' => $widget,
		];

		switch ( $widget->type )
		{
			case 'menu':
				$data['menus'] = $this->site->menus()->lists('title','id')->all();
			case 'slider':
				$data['sliders'] = $this->site->slidergroups()->lists('name', 'id')->all();
				break;
		}

		// Update site setup
		$this->site->updateSiteSetup();

		return [
			'success' => 1,
			'html' => view('account.site.widgets.item', $data)->render(),
		];
	}

	public function postUpdate($id)
	{
		$widget = $this->site->widgets()->find($id);
		if ( !$widget )
		{
			return [ 'error'=>true ];
		}

		$data = $this->request->input("items.{$id}");
		if ( !$data || !is_array($data) )
		{
			return [ 'error'=>true ];
		}

		$fields = [
			'title' => 'required|array',
		];

		switch ( $widget->type )
		{
			case 'menu':
				$fields['menu_id'] = 'required|integer|exists:menus,id,site_id,'.$this->site->id;
				break;
			case 'slider':
				$fields['slider_id'] = 'required|integer|exists:slider_group,id,site_id,'.$this->site->id;
				break;
			case 'text':
				$fields['content'] = 'required|array';
				break;
		}

		$validator = \Validator::make($data, $fields);
		if ( $validator->fails() )
		{
			return [
				'error' => true,
				'errors' => $validator->errors(),
			];
		}

		// Save i18n
		foreach (\App\Session\Site::get('locales_tabs') as $locale => $locale_name)
		{
			$widget->translateOrNew($locale)->title = @sanitize( $data['title'][$locale] );
		}

		// Save type related data
		switch ( $widget->type )
		{
			case 'menu':
				$widget->menu_id = $data['menu_id'];
				break;
			case 'slider':
				$widget->slider_id = $data['slider_id'];
				break;
			case 'text':
				foreach (\App\Session\Site::get('locales_tabs') as $locale => $locale_name)
				{
					$widget->translateOrNew($locale)->content = @sanitize( $data['content'][$locale] );
				}
				break;
		}

		$widget->save();

		// Update site setup
		$this->site->updateSiteSetup();

		return [ 'success'=>true ];
	}

	public function postDelete($id)
	{
		$widget = $this->site->widgets()->find($id);
		if ( !$widget )
		{
			return [ 'error'=>true ];
		}

		$widget->delete();

		// Update site setup
		$this->site->updateSiteSetup();

		return [ 'success'=>true ];
	}

	public function postSort($group)
	{
		$items = $this->request->input('items');
		if ( !$items || !is_array($items) )
		{
			return [ 'error'=>true ];
		}

		$widgets = $this->site->widgets()->where('group', $group)->get();
		if ( !$widgets->count() )
		{
			return [ 'error'=>true ];
		}

		foreach ($widgets as $widget)
		{
			$position = array_search($widget->id, $items);

			if ( $position === false )
			{
				continue;
			}

			$widget->update([
				'position' => $position,
			]);
		}

		// Update site setup
		$this->site->updateSiteSetup();

		return [ 'success'=>true ];
	}

}
