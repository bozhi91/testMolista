<?php namespace App\Models\Site;

use Illuminate\Database\Eloquent\Model;

use App\Traits\ValidatorTrait;

class Invoice extends Model
{
	use ValidatorTrait;

    protected $table = 'sites_invoices';

	protected $guarded = [];

	protected $dates = [ 'uploaded_at' ];

	protected static $create_validator_fields = [
		'site_id' => 'required|exists:sites,id',
		'title' => 'required',
		'document' => 'required|mimes:pdf',
		'amount' => 'required|numeric',
		'uploaded_at' => 'required|date_format:"Y-m-d"',
	];

	protected static $update_validator_fields = [
		'title' => 'required',
		'document' => 'mimes:pdf',
		'amount' => 'required|numeric',
		'uploaded_at' => 'required|date_format:"Y-m-d"',
	];

	public function site()
	{
		return $this->belongsTo('App\Site');
	}

	public function getInvoiceFolderAttribute()
	{
		return storage_path("sites/{$this->site->id}/invoices");
	}
	public function getInvoicePathAttribute()
	{
		return "{$this->invoice_folder}/{$this->document}";
	}
	public function getInvoiceFilenameAttribute()
	{
		return str_slug($this->title).'.pdf';
	}

	public static function saveModel($data, $id = null)
	{
		if ($id)
		{
			$item = self::find($id);
			if (!$item)
			{
				return false;
			}
			$fields = array_keys(self::$update_validator_fields);
		}
		else
		{
			$item = new \App\Models\Site\Invoice();
			$fields = array_keys(self::$create_validator_fields);
		}

		foreach ($fields as $field)
		{
			switch ($field) 
			{
				case 'document':
					break;
				default:
					$item->$field = @$data[$field];
			}
		}

		$item->save();

		return $item;
	}

	public function saveInvoice($file)
	{

		// Delete old logo
		if ( $this->document )
		{
			@unlink( $this->invoice_path );
		}

		// Move new logo
		$this->document = $file->getClientOriginalName();
		while ( file_exists("{$this->invoice_folder}/{$this->document}") )
		{
			$this->document = uniqid() . '_' . $file->getClientOriginalName();
		}

		$file->move($this->invoice_folder, $this->document);

		return $this->save();
	}

}
