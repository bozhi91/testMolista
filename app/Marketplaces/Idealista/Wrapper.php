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
        if (!isset($config['reference'])) {
            $config['reference'] = null;
        }

        parent::__construct($config);
    }

    public function validateProperty(array $property)
    {
        $data = array_merge($property, $this->config);

        if (in_array($property['type'], ['bungalow', 'hotel', 'aparthotel']))
        {
            $this->errors []= \Lang::get('validation.type');
            return false;
        }

        $rules = [
            'id' => 'required',
            'title' => 'required',
            'code' => 'required',
            'location.lat' => 'required',
            'location.lng' => 'required',
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
