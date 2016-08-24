<?php namespace App\Marketplaces\Idealista;

class Wrapper extends Idealista {

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
