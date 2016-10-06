<?php

namespace App\Http\Controllers\Account\Site;

use App\Models\Locale;
use App\Models\Site\SliderGroup;
use App\Models\Site\SliderGroupLocale;
use App\Models\Site\SliderImage;

class SlidersController extends \App\Http\Controllers\AccountController {

	const ALL_LANGE_VALUE = 0;

	/**
	 * Initialize..
	 */
	public function __initialize() {
		$this->middleware([ 'permission:site-edit']);

		parent::__initialize();
		\View::share('submenu_section', 'site');
		\View::share('submenu_subsection', 'site-sliders');
	}

	/**
	 * Show slider groups
	 * @return string
	 */
	public function index() {
		$configLimit = \Config::get('app.pagination_perpage', 10);
		$limit = $this->request->input('limit', $configLimit);

		$sliders = $this->site->slidergroups()
						->orderBy('created_at')->paginate($limit);

		return view('account.site.sliders.index', compact('sliders'));
	}

	/**
	 * Create slider groups
	 * @return type
	 */
	public function create() {
		$languages = $this->getLanguages();
		//$this->set_go_back_link(); lol
		return view('account.site.sliders.create', compact('languages'));
	}

	/**
	 * Create POST slider groups
	 * @return type
	 */
	public function store() {
		$validator = \Validator::make($this->request->all(), [
					'title' => 'required',
					'languages.0' => 'required',
					'images.0' => 'required',
		]);

		if ($validator->fails()) {
			return redirect()->back()->withInput()->withErrors($validator);
		}

		$isAllLocales = $this->isAllLocales();

		$group = $this->site->slidergroups()->create([
			'name' => $this->request->input('title'),
			'isAllLocales' => $isAllLocales,
		]);

		if (!$group) {
			return redirect()->back()->withInput()
							->with('error', trans('general.messages.error'));
		}

		if (!$isAllLocales) {
			$this->createGroupLocales($group->id, $this->request->input('languages'));
		}

		$this->createImages($group->id
				, $this->request->input('images')
				, $this->request->input('links'));

		$this->site->updateSiteSetup();
		
		return redirect()->action('Account\Site\SlidersController@edit', $group->id)
						->with('success', trans('account/site.sliders.create.success'));
	}

	/**
	 * Post UPDATE
	 * @return redirect
	 */
	public function update($id) {
		$sliderGroup = $this->site->slidergroups()->where('id', $id)->first();

		if (!$sliderGroup) {
			abort(404);
		}

		$validator = \Validator::make($this->request->all(), [
					'title' => 'required',
					'languages.0' => 'required',
					'images.0' => 'required',
		]);

		if ($validator->fails()) {
			return redirect()->back()->withInput()->withErrors($validator);
		}

		$isAllLocales = $this->isAllLocales();

		$sliderGroup->name = $this->request->input('title');
		$sliderGroup->isAllLocales = $isAllLocales;
		$sliderGroup->save();

		if ($sliderGroup && !$isAllLocales) {
			SliderGroupLocale::where('group_id', $sliderGroup->id)->delete();
			$this->createGroupLocales($sliderGroup->id, $this->request->input('languages'));
		}

		$this->deleteImages($sliderGroup, $this->request->input('images'));

		$this->createImages($sliderGroup->id
				, $this->request->input('images')
				, $this->request->input('links'));

		
		$this->site->updateSiteSetup();
		
		return redirect()->action('Account\Site\SlidersController@edit', $sliderGroup->id)
						->with('success', trans('account/site.sliders.update.success'));
	}

	/**
	 * Delet slider group
	 * @param integer $id
	 * @return redirect
	 */
	public function destroy($id) {
		$sliderGroup = $this->site->slidergroups()->where('id', $id)->first();

		if (!$sliderGroup) {
			abort(404);
		}

		//Delete slider folder
		$sliderDir = 'sites/' . $this->site->id . '/sliders/' . $sliderGroup->id;
		$sliderDirPath = public_path($sliderDir);
		if (is_dir($sliderDirPath)) {
			array_map('unlink', glob("$sliderDirPath/*.*"));
			rmdir($sliderDirPath);
		}

		// Delete slider
		$sliderGroup->delete();

		return redirect()->action('Account\Site\SlidersController@index')
						->with('success', trans('account/site.sliders.deleted.success'));
	}

	/**
	 * Get edit page
	 * @param integer $id
	 * @return mixed
	 */
	public function edit($id) {
		$sliderGroup = $this->site->slidergroups()->where('id', $id)->first();

		if (!$sliderGroup) {
			abort(404);
		}

		$languages = $this->getLanguages();

		$languagesCurrent = [];
		if ($sliderGroup->isAllLocales) {
			$languagesCurrent = [self::ALL_LANGE_VALUE];
		} else {
			foreach ($sliderGroup->groupLocales as $groupLocale) {
				$languagesCurrent[] = $groupLocale->locale_id;
			}
		}

		//$this->set_go_back_link(); lol
		return view('account.site.sliders.edit', compact(
						'languages', 'sliderGroup', 'languagesCurrent'));
	}

	/**
	 * @return boolean
	 */
	protected function isAllLocales() {
		$languageInput = $this->request->input('languages');
		return in_array(self::ALL_LANGE_VALUE, $languageInput);
	}

	/**
	 * @return array
	 */
	protected function getLanguages() {
		$default = [self::ALL_LANGE_VALUE => \Lang::get('account/site.sliders.select.alllanguages')];
		$languages = Locale::pluck('name', 'id')->toArray();

		return array_merge($default, $languages);
	}

	/**
	 * @param integer $groupId
	 * @param array $imagesInput
	 * @param array $linksInput
	 */
	protected function createImages($groupId, $imagesInput, $linksInput) {
		foreach ($imagesInput as $pos => $image) {
			if (strpos($image, 'new_/') !== false) {

				//Move file to persist folder	
				$oldDir = preg_replace('/^new_\//', '', $image);
				$oldDirPath = public_path($oldDir);
				$newDir = 'sites/' . $this->site->id . '/sliders/' . $groupId . '/' . basename($oldDir);
				$newDirPath = public_path($newDir);
				if (!is_dir(dirname($newDirPath))) {
					mkdir(dirname($newDirPath), 0755, true);
				}
				rename($oldDirPath, $newDirPath);

				$sliderImage = new SliderImage();
				$sliderImage->group_id = $groupId;
				$sliderImage->image = '/' . $newDir;
			} else { //update slider image
				$sliderImage = SliderImage::find($image);
			}

			if ($sliderImage) {
				$sliderImage->link = isset($linksInput[$image]) ? $linksInput[$image] : null;
				$sliderImage->position = $pos;
				$sliderImage->save();
			}
		}
	}

	/**
	 * @param SliderGroup $group
	 * @param array $imagesInput
	 */
	protected function deleteImages($group, $imagesInput) {
		foreach ($group->images as $currentImage) {
			if (!in_array($currentImage->id, $imagesInput)) {
				$path = public_path($currentImage->image);
				unlink($path);

				$currentImage->delete();
			}
		}
	}

	/**
	 * @param integer $groupId
	 * @param array $languageInput
	 */
	protected function createGroupLocales($groupId, $languageInput) {
		$data = [];
		foreach ($languageInput as $localeId) {
			if ($localeId != 0) {
				$data[] = ['group_id' => $groupId, 'locale_id' => $localeId];
			}
		}
		SlidergroupLocale::insert($data);
	}

	/**
	 * Upload images
	 * @return type
	 */
	public function upload() {
		$file = \Input::file('file');

		$validator = \Validator::make($this->request->all(), [
					'file' => 'required|image|max:' . \Config::get('app.slider_image_maxsize', 2048),
		]);

		$validator->setAttributeNames([
			'file' => ucfirst(trans('account/properties.images.dropzone.nicename')),
		]);

		if ($validator->fails()) {
			$errors = $validator->errors();
			return response()->json([
						'error' => true,
						'message' => $errors->first('file'),
							], 400);
		}

		$dir = 'sites/uploads/' . date('Ymd');
		$dirpath = public_path($dir);

		// If the uploads fail due to file system, you can try doing public_path().'/uploads'
		$filename = $ofilename = preg_replace('#[^a-z0-9\.]#', '', strtolower($file->getClientOriginalName()));
		while (file_exists("{$dirpath}/{$filename}")) {
			$filename = uniqid() . "_{$ofilename}";
		}

		$upload_success = $file->move($dirpath, $filename);

		if ($upload_success) {
			@list($w, $h) = @getimagesize(public_path("{$dir}/{$filename}"));
			$is_vertical = ( $w && $h && $w < $h ) ? true : false;
			$has_size = ( $w && $w < 1280 ) ? false : true;

			return response()->json([
						'success' => true,
						'directory' => $dir,
						'filename' => $filename,
						'html' => view('account.site.sliders.thumb', [
							'image_url' => "/{$dir}/{$filename}",
							'image_id' => "new_/{$dir}/{$filename}",
							'warning_orientation' => $is_vertical,
							'warning_size' => $has_size ? 0 : 1,
						])->render()], 200);
		}

		return response()->json(['error' => true], 400);
	}

}
