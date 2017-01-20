<?php

namespace App\Http\Controllers\Account\Properties;

use App\Models\Site\District;
use Illuminate\Http\Request;
use App\Http\Requests;

class DistrictsController extends \App\Http\Controllers\AccountController {

	public function __initialize() {
		parent::__initialize();
		\View::share('submenu_section', 'properties');
	}

	/**
	 * @return mixed
	 */
	public function getIndex() {
		$query = $this->site->districts();
		$limit = $this->request->input('limit', \Config::get('app.pagination_perpage', 10));
		$districts = $query->paginate($limit);
		$this->set_go_back_link();
		return view('account.properties.districts.index', compact('districts'));
	}

	/**
	 * @return mixed
	 */
	public function getCreate() {
		return view('account.properties.districts.create', [
			'current_tab' => 'general'
		]);
	}

	/**
	 * @param integer $id
	 * @return mixed
	 */
	public function getEdit($id) {
		return view('account.properties.districts.edit', [
			'current_tab' => 'general',
			'district' => $this->getDistrict($id)
		]);
	}

	/**
	 * @return mixed
	 */
	public function postStore() {
		$validator = $this->getValidator();

		if ($validator->fails()) {
			return redirect()->back()->withInput()
							->withErrors($validator);
		}

		$district = $this->site->districts()->create([
			'name' => $this->request->input('name'),
		]);

		if (!$district) {
			return redirect()->back()->withInput()
							->with('error', trans('general.messages.error'));
		}

		return redirect()->action('Account\Properties\DistrictsController@getEdit', $district->id)
						->with('success', trans('account/properties.districts.message.saved'));
	}

	/**
	 * @param integer $id
	 * @return mixed
	 */
	public function patchUpdate($id) {
		$district = $this->getDistrict($id);

		$validator = $this->getValidator();

		if ($validator->fails()) {
			return redirect()->back()->withInput()
							->withErrors($validator);
		}

		$district->update([
			'name' => $this->request->input('name')
		]);

		return redirect()->action('Account\Properties\DistrictsController@getEdit', $district->id)
						->with('success', trans('account/properties.districts.message.saved'));
	}

	/**
	 * @param integer $id
	 * @return null
	 */
	private function getDistrict($id) {
		$district = $this->site->districts()
						->where('id', $id)->first();

		if (!$district) {
			abort(404);
		}

		return $district;
	}

	/**
	 * @return \Validator
	 */
	private function getValidator() {
		return \Validator::make($this->request->all(), [
					'name' => 'required',
		]);
	}

	/**
	 * @param integer $id
	 */
	public function delete($id) {
		$district = $this->getDistrict($id);
		
		$district->delete();
		
		return redirect()->action('Account\Properties\DistrictsController@getIndex')
				->with('success', trans('account/properties.districts.deleted'));
	}

}
