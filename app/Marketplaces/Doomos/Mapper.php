<?php

namespace App\Marketplaces\Doomos;

class Mapper extends \App\Marketplaces\Trovit\Mapper {

	public function map() {
		$mapped = parent::map();

		$mapped['contact_name'] = $this->config['name'];
		$mapped['contact_email'] = $this->config['email'];

		return $mapped;
	}

	public function valid() {
		$data = array_merge($this->item, $this->config);

		$rules = [
			'id' => 'required',
			'url' => 'required',
			'title' => 'required',
			'type' => 'required',
			'description.' . $this->iso_lang => 'required|min:30',
			'construction_year' => 'regex:#\d{4}#',
			'name' => 'required',
			'email' => 'required',
		];

		$messages = [
			'construction_year.regex' => \Lang::get('validation.date'),
		];

		$validator = \Validator::make($data, $rules, $messages);

		if ($validator->fails()) {
			$this->errors = $validator->errors()->all();
		}

		return empty($this->errors);
	}

}
