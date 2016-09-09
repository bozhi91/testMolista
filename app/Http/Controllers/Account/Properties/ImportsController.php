<?php namespace App\Http\Controllers\Account\Properties;

use Illuminate\Http\Request;

use App\Http\Requests;

class ImportsController extends \App\Http\Controllers\AccountController
{
	public function __initialize()
	{
		parent::__initialize();
		\View::share('submenu_section', 'properties');
	}

	public function getIndex()
	{
		$logs = $this->site->imports()->orderBy('created_at', 'desc')->paginate( $this->request->input('limit', \Config::get('app.pagination_perpage', 10)) );
		return view('account.properties.imports.index', compact('logs'));
	}

	public function getUpload()
	{
		$current_version = \App\Models\Site\Import::versionCurrent();
		return view('account.properties.imports.upload', compact('current_version'));
	}

	public function postUpload()
	{
		$data = $this->request->all();

		$validator = \Validator::make($data, [
			'file' => 'required',
		]);
		if ($validator->fails())
		{
			return redirect()->back()->withInput()->with('errors', $validator->errors());
		}

		// Save the file for processing
		$file = $this->request->file('file', false);
		$destination = "/sites/{$this->site->id}/imports/";
		$filename = uniqid('', true);

		if ( $file === false || $file->move(storage_path($destination), $filename) === false)
		{
			return redirect()->back()->withInput()->with('error', trans('account/properties.imports.file.error.type'));
		}

		// Create the entry
		$import = $this->site->imports()->create([
			'status' => 'pending',
			'filename' => $destination.$filename,
		]);

		// Process the file in a queue
		$job = ( new \App\Jobs\ImportSiteProperties($import, $this->site_user->id) );
		$this->dispatch( $job );

		return redirect()->action('Account\Properties\ImportsController@getIndex')->with('success', trans('account/properties.imports.created'));
	}

	public function getSample($version)
	{
		$url = \App\Models\Site\Import::getSampleFileLocation($version);
		return redirect()->away( $url );
	}

	public function getDownload($id)
	{
		$import = $this->site->imports()->findOrFail($id);
		return response()->download(storage_path($import->filename));
	}

}
