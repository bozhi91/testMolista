<?php

namespace App\Models\Site;

use Illuminate\Database\Eloquent\Model;

class SliderGroup extends Model {

	protected $table = 'slider_group';
	protected $guarded = [];

	public function site() {
		return $this->belongsTo('App\Site');
	}

	public function groupLocales() {
		return $this->hasMany('App\Models\Site\SliderGroupLocale', 'group_id');
	}

	public function images() {
		return $this->hasMany('App\Models\Site\SliderImage', 'group_id');
	}

	/**
	 * @return string
	 */
	public function getLocalesString() {
		if ($this->isAllLocales) {
			return \Lang::get('account/site.sliders.select.alllanguages');
		}

		$languages = [];
		foreach ($this->groupLocales as $groupLocale) {
			$languages[] = $groupLocale->locale->name;
		}

		return implode(', ', $languages);
	}

}
