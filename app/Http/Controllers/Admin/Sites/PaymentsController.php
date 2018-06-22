<?php namespace App\Http\Controllers\Admin\Sites;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class PaymentsController extends Controller
{
	public function __initialize() {
		$this->middleware([ 'permission:site-edit' ]);

		parent::__initialize();
	}

    public static function getAllowedTranslations($site){

	    $translations = DB::table('locales')
            ->select('locale')
            ->join('sites_locales', 'locales.id', '=', 'sites_locales.locale_id')
            ->where('sites_locales.site_id',$site)
            ->get();

        return $translations;
    }

    public static function getMaxLanguages($site){
	    $planLimit = DB::table('sites')
            ->select('plans.max_languages')
            ->join('plans', 'sites.plan_id', '=', 'plans.id')
            ->where('sites.id',$site)
            ->first();

	    $limit = $planLimit->max_languages;
	    if($limit==null)$limit=1000;

        return $limit;
    }

    public static function verifyPlan($site){
        $countLanguages = DB::table('sites_locales')
            ->select('*')
            ->where('site_id',$site)
            ->get();

        $countLanguages = count($countLanguages);

        //if the site has more languages than the plan allows, delete all but the allowed ones
        while($countLanguages>self::getMaxLanguages($site)){

            $countLanguages = DB::table('sites_locales')
                ->select('*')
                ->where('site_id',$site)
                ->get();

            $countLanguages = count($countLanguages);

            if($countLanguages>self::getMaxLanguages($site)){
                $language = DB::table('sites_locales')
                    ->select('*')
                    ->where('site_id',$site)
                    ->get();//order by id asc->first()

                $lang = $language[count($language)-1]->locale_id;

                //delete the translation if it's not spanish
                if($lang!=2){
                    DB::table('sites_locales')
                        ->where('locale_id',$lang)
                        ->where('site_id',$site)
                        ->delete();
                }
            }
        }
    }

    public function getList($site_id, $check_ajax = true)
	{
		if ( $check_ajax && !$this->request->ajax() )
		{
			return redirect()->action('Admin\SitesController@edit', $site_id)->with('current_tab','payments');
		}

		$payments = \App\Models\Site\Payment::where('site_id', $site_id)
						->with('reseller')
						->with('infocurrency')
						->orderBy('created_at', 'desc')
						->paginate( $this->request->input('limit', \Config::get('app.pagination_perpage', 10)) );

		$payments->setPath( action('Admin\Sites\PaymentsController@getList', $site_id) );

		return view('admin.sites.payments.list', compact('payments'));
	}

	public function getEdit($id)
	{
		$payment = \App\Models\Site\Payment::findOrFail($id);

		$resellers = \App\Models\Reseller::orderBy('name')->lists('name','id')->all();

		return view('admin.sites.payments.edit', compact('payment','resellers'));
	}

	public function postSave($id)
	{

		$payment = \App\Models\Site\Payment::find($id);
		if ( !$payment )
		{
			return redirect()->back()->withInput()->with('error', trans('general.messages.error'));
		}

		$validator = \Validator::make($this->request->all(), [
			'reseller_id' => 'exists:resellers,id',
			'reseller_fixed' => 'numeric|min:0',
			'reseller_variable' => 'numeric|min:0|max:100',
			'reseller_paid' => 'boolean',
			'reseller_date' => 'required_with_all:reseller_id,reseller_paid|date_format:"Y-m-d"',
		]);
		if ( $validator->fails() )
		{
			return redirect()->back()->withInput()->withErrors($validator);
		}

		$update = [
			'reseller_id' => $this->request->input('reseller_id') ? $this->request->input('reseller_id') : null,
			'reseller_fixed' => $this->request->input('reseller_id') ? floatval($this->request->input('reseller_fixed')) : 0,
			'reseller_variable' => $this->request->input('reseller_id') ? floatval($this->request->input('reseller_variable')) : 0,
			'reseller_paid' => $this->request->input('reseller_paid') ? 1 : 0,
			'reseller_date' => $this->request->input('reseller_paid') ? $this->request->input('reseller_date') : null,
		];

		$payment_net_amount = $payment->payment_amount / ( ( 100 + $payment->payment_vat ) / 100 );

		$update['reseller_amount'] = $update['reseller_fixed'] + ( $payment_net_amount * $update['reseller_variable'] / 100 );

		$payment->update($update);

		return redirect()->back()->with('success', trans('general.messages.success.saved'));
	}
}
