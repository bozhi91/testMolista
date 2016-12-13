<?php namespace App\Http\Controllers\Account\Profile;

use Illuminate\Http\Request;

use App\Http\Requests;

class InvoicesController extends \App\Http\Controllers\AccountController
{
	public function __initialize()
	{
		parent::__initialize();
		\View::share('menu_section', 'account');
		\View::share('submenu_section', 'profile');
		\View::share('hide_advanced_search_modal', true);
	}

	public function getIndex()
	{
		$invoices = $this->site->invoices()->orderBy('uploaded_at','desc')->paginate( $this->request->input('limit', \Config::get('app.pagination_perpage', 10)) );

		$current_tab = 'invoices';

		return view('account.index', compact('invoices','current_tab'));
	}

	public function getInvoice($invoice_id,$filename)
	{
		$invoice = $this->site->invoices()->findOrFail($invoice_id);

		if ( !$invoice->document )
		{
			abort(404);
		}

		return response()->download($invoice->invoice_path, $filename);
	}

}
