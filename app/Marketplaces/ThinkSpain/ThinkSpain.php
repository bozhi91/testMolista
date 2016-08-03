<?php namespace App\Marketplaces\ThinkSpain;

class ThinkSpain extends \App\Marketplaces\XML {

    protected $iso_lang = 'es';

    protected $configuration = [
        [
            'block' => 'agency_data',
            'fields' => [
                [
                    'name' => 'agent_name',
                    'type' => 'text',
                    'required' => true
                ]
            ]
        ]
    ];

}
