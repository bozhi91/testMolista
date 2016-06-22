<?php

namespace App\Http\Controllers\Account\Properties;

use Illuminate\Http\Request;

use App\Http\Requests;

class DocumentsController extends \App\Http\Controllers\AccountController
{

	public function postUpload($property_id)
	{
		$fields = [
			'title' => 'required|string',
			'description' => 'required|string',
			'file' => 'required|max:' . \Config::get('app.property_image_maxsize', 2048),
		];
		$validator = \Validator::make($this->request->all(), $fields);
		if ($validator->fails()) 
		{
			return redirect()->back()->withInput($this->request->only('current_tab'))->withErrors($validator);
		}

		$doc = $this->site->properties()->findOrFail($property_id)->documents()->create([
			'user_id' => \Auth::check() ? \Auth::user()->id : null,
			'type' => sanitize($this->request->input('type', 'other')),
			'date' => sanitize($this->request->input('date', date("Y-m-d"))),
			'title' => sanitize($this->request->input('title')),
			'description' => sanitize($this->request->input('description')),
		]);
		if ( !$doc )
		{
			return redirect()->back()->withInput($this->request->only('current_tab'))->with('error', trans('general.messages.error'));
		}

		// Move new file
		$doc->file = $this->request->file('file')->getClientOriginalName();
		while ( file_exists("{$doc->file_directory}/{$doc->file}") )
		{
			$doc->file = uniqid() . '_' . $this->request->file('file')->getClientOriginalName();
		}
		$this->request->file('file')->move($doc->file_directory, $doc->file);
		$doc->save();

		return redirect()->back()->withInput($this->request->only('current_tab'))->with('success', trans('general.messages.success.saved'));
	}

	public function getDownload($document_id,$file)
	{
		$doc = \App\Models\Property\Documents::with('property')->findOrFail($document_id);
		if ( $this->site->id != $doc->property->site->id )
		{
			abort(404);
		}

		$filepath = "{$doc->file_directory}/{$doc->file}";
		if ( !file_exists($filepath) )
		{
			abort(404);
		}

		return response()->download($filepath, $file);
	}

	public function getDelete($document_id,$file)
	{
		$doc = \App\Models\Property\Documents::with('property')->findOrFail($document_id);
		if ( $this->site->id != $doc->property->site->id )
		{
			abort(404);
		}

		$filepath = "{$doc->file_directory}/{$doc->file}";
		\File::delete($filepath);

		$doc->delete();

		return redirect()->back()->withInput($this->request->only('current_tab'))->with('success', trans('account/properties.documents.delete.success'));
	}

}
