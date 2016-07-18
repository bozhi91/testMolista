<?php

namespace App\Http\Controllers\Ajax;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class AutotranslateController extends Controller
{

	public function getIndex()
	{
		$validator = \Validator::make($this->request->all(), [
			'text' => [ 'required', 'string' ],
			'from' => [ 'required', 'string' ],
			'to' => [ 'required', 'array' ],
		]);
		if ($validator->fails()) {
			return [ 'error' => true ];
		}

		$translations = [];
		foreach ($this->request->input('to') as $iso_lang)
		{
			$translations[$iso_lang] = \App\Autotranslate\Base::translate($this->request->input('from'), $iso_lang, $this->request->input('text'));
		}

		return [
			'success' => true,
			'translations' => $translations,
		];
	}

}
