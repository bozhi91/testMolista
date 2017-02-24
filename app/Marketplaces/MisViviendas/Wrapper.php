<?php namespace App\Marketplaces\MisViviendas;

class Wrapper extends \App\Marketplaces\Idealista\Idealista {

    public function __construct(array $config = [])
    {
        $config['aggregator'] = env('APP_URL');
        if (!isset($config['reference'])) {
            $config['reference'] = null;
        }

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

}
