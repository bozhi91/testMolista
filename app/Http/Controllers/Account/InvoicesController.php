<?php namespace App\Http\Controllers\Account;

use Illuminate\Http\Request;

use App\Http\Requests;

class InvoicesController extends \App\Http\Controllers\AccountController
{
	public function getIndex()
	{
		// If not ajax, redirect to account
		if ( !$this->request->ajax() )
		{
			return redirect()->action('AccountController@index');
		}

		$invoices = $this->site->invoices()->orderBy('uploaded_at','desc')->paginate( $this->request->get('limit', \Config::get('app.pagination_perpage', 10)) );

		return view('account.invoices.index', compact('invoices'));
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
