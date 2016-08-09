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

}
