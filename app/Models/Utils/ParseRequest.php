<?php namespace App\Models\Utils;

use Illuminate\Database\Eloquent\Model;

class ParseRequest extends Model
{
	protected $table = 'parse_requests';
	protected $guarded = [];

	protected $casts = [
		'report' => 'array',
	];

	protected $services = [
		'paginas-amarillas' => [
			'service' => 'paginas-amarillas',
			'title' => 'Páginas Amarillas',
			'url' => 'http://www.paginasamarillas.es/search/[QUERY_STRING]/all-ma/all-pr/all-is/all-ci/all-ba/all-pu/all-nc/[CURRENT_PAGE]',
			'items' => 'li[class*=m-results-business]',
			'columns' => [
				'name' => [
					'title' => 'Company',
					'selector' => 'span[itemprop=name]',
					'attribute' => 'plaintext',
					'required' => true,
				],
				'website' => [
					'title' => 'Website',
					'selector' => 'a[itemprop=url]',
					'attribute' => 'plaintext',
				],
				'address' => [
					'title' => 'Address',
					'selector' => 'span[itemprop=streetAddress]',
					'attribute' => 'plaintext',
				],
				'zipcode' => [
					'title' => 'Zipcode',
					'selector' => 'span[itemprop=postalCode]',
					'attribute' => 'plaintext',
				],
				'city' => [
					'title' => 'City',
					'selector' => 'span[itemprop=addressLocality]',
					'attribute' => 'plaintext',
				],
				'phone' => [
					'title' => 'Phone',
					'selector' => 'span[data-single-phone]',
					'attribute' => 'plaintext',
				],
				'moreinfo' => [
					'title' => 'Link',
					'selector' => 'a[id^=businessId]',
					'attribute' => 'href',
				],
			],
		],
	];

	public function items() {
		return $this->hasMany('App\Models\Utils\ParseRequestItem');
	}

	public function getServiceDetailsAttribute() {
		return $this->services[$this->service];
	}
	public function getServiceTitleAttribute() {
		return $this->service_details['title'];
	}

	public function getReadyAttribute() {
		return $this->finished_at ? true : false;
	}

	public function getCsvHeadersAttribute() {
		$headers = [];

		foreach ($this->service_details['columns'] as $key => $column)
		{
			$headers[$key] = $column['title'];
		}

		return $headers;
	}

	public function getServiceIdFromDom($item)
	{
		switch ( $this->service )
		{
			case 'paginas-amarillas':
				// Get from details url
				return @basename($item->find('a[id^=businessId]',0)->href, '.html');
		}

		return false;
	}

}
