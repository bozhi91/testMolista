<?php namespace App\Marketplaces\Idealista;

class Wrapper extends Idealista implements \App\Marketplaces\Interfaces\PublishByFtpInterface {

    protected $configuration = [
        [
            'block' => 'account',
            'fields' => [
                [
                    'name' => 'code',
                    'type' => 'text',
                    'required' => true
                ]
            ]
        ]
    ];

    public function __construct(array $config = [])
    {
        $config['aggregator'] = env('IDEALISTA_AGGREGATOR');
        parent::__construct($config);
    }

    public function validateProperty(array $property)
    {
        $data = array_merge($property, $this->config);

        if (in_array($property['type'], ['bungalow', 'hotel', 'aparthotel', 'garage', 'plot']))
        {
            $this->errors []= \Lang::get('validation.type');
            return false;
        }

		if ($property['mode'] == 'transfer') { //Solo hay compra, renta y opcion de compra
			$this->errors []= \Lang::get('validation.transfer');
            return false;
		}

        $rules = [
            'id' => 'required',
			'type' => 'required',
			'mode' => 'required',
			'reference' => 'required',
			'price' => 'required',
            'title' => 'required',
            'code' => 'required',
            'location.lat' => 'required', //not really required
            'location.lng' => 'required', //not really required
			//Flat/House features required
			'baths' => 'required_if:type,apartment,duplex,penthouse,chalet,house,villa,farmhouse',
			'bedrooms' => 'required_if:type,apartment,duplex,penthouse,chalet,house,villa,farmhouse',
			'size' => 'required',
        ];

        $messages = [];

        $validator = \Validator::make($data, $rules, $messages);
        if ($validator->fails())
        {
            return $validator->errors()->all();
        }

        return true;
    }

    public function getFeedRemoteFilename(\App\Site $site)
    {
        return (empty($this->config['code']) ? $site->id : $this->config['code']).'.xml';
    }

}
