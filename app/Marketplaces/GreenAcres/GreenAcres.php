<?php namespace App\Marketplaces\GreenAcres;

class GreenAcres extends \App\Marketplaces\XML {

    protected $iso_lang = 'en';

    protected $configuration = [
        [
            'block' => 'account',
            'fields' => [
                [
                    'name' => 'account_id',
                    'type' => 'text',
                    'required' => true
                ]
            ]
        ]
    ];

}
