<?php namespace App\Http\Controllers\Admin\Resellers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class PaymentsController extends Controller
{

    public function getIndex()
	{
		return view('admin.resellers.payments.index', compact('requests'));
	}

}
