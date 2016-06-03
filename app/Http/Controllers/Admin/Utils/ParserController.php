<?php

namespace App\Http\Controllers\Admin\Utils;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ParserController extends Controller
{

    public function getIndex()
	{
		if ( !$this->request->get('limit') )
		{
			$this->request->merge([ 'limit'=>25 ]);
		}

		$query = \App\Models\Utils\ParseRequest::orderBy('query');

		if ( $this->request->get('query') )
		{
			$query->where('query', $this->request->get('query'));
		}

		$requests = $query->paginate($this->request->get('limit'));

		return view('admin.utils.parser.index', compact('requests'));
	}

	public function getDownload($id)
	{
		$request = \App\Models\Utils\ParseRequest::findOrFail($id);

		// Instantiate the Csv Writer
		$csv = \League\Csv\Writer::createFromFileObject(new \SplTempFileObject());
		$csv->setEnclosure('"');
		$csv->setDelimiter(';');

		$columns = $request->csv_headers;

		// Headers
		$csv->insertOne($columns);

		foreach ($request->items as $item)
		{
			$csv->insertOne($item->columns);
		}

		$csv->output("{$request->service}-{$request->query}.csv");
		exit;
	}

}
