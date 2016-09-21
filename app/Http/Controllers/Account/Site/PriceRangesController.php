<?php namespace App\Http\Controllers\Account\Site;

use Illuminate\Http\Request;

use App\Http\Requests;

class PriceRangesController extends \App\Http\Controllers\AccountController
{

	public function __initialize()
	{
		$this->middleware([ 'permission:site-edit' ]);

		parent::__initialize();
		\View::share('submenu_section', 'site');
		\View::share('submenu_subsection', 'site-priceranges');
	}

	public function getIndex()
	{
		$priceranges = $this->site->getGroupedPriceranges();

		$current_tab = session('current_tab', $this->request->input('current_tab','sale'));

		return view('account.site.priceranges.index', compact('priceranges','current_tab'));
	}

	public function postIndex()
	{
		$data = array_merge($this->request->all(), [
			'site_id' => $this->site->id,
		]);

		// Validate ID
		if ( $id = $this->request->input('id') )
		{
			$item = $this->site->priceranges()->findOrFail( $id );
			$validator = \App\Models\Site\Pricerange::getUpdateValidator($data, $id);
		}
		else
		{
			$id = false;
			$validator = \App\Models\Site\Pricerange::getCreateValidator($data,false);
		}

		if ($validator->fails())
		{
			return redirect()->back()->with('current_tab', $this->request->input('current_tab'))->withErrors($validator);
		}


		$item = \App\Models\Site\Pricerange::saveModel($data, $id);
		if (!$item)
		{
			return redirect()->back()->with('current_tab', $this->request->input('current_tab'))->with('error', trans('general.messages.error'));
		}

		return redirect()->back()->with('current_tab', $this->request->input('current_tab'))->with('success', trans('general.messages.success.saved'));
	}

	public function deleteRemove($id)
	{
		$item = $this->site->priceranges()->find( $id );
		if ( !$item )
		{
			return redirect()->back()->with('current_tab', $this->request->input('current_tab'))->with('error', trans('general.messages.error'));
		}

		$item->delete();

		return redirect()->back()->with('current_tab', $this->request->input('current_tab'))->with('success', trans('account/priceranges.form.delete.success'));
	}

	public function getSort($type)
	{
		if ( !in_array($type, \App\Property::getModes()) )
		{
			return [ 'error'=>true ];
		}

		$items = $this->request->input('items');
		if ( !$items || !is_array($items) )
		{
			return [ 'error'=>true ];
		}

		$position = 0;
		foreach ($items as $id) 
		{
			$position++;
			$this->site->priceranges()->where('sites_priceranges.id',$id)->update([
				'position' => $position,
			]);
		}

		return [ 'success'=>true ];
	}

}
