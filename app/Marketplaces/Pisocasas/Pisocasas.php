<?php namespace App\Marketplaces\Pisocasas;

class Pisocasas extends \App\Marketplaces\XML {

    protected $iso_lang = 'es';

    protected $configuration = [
        [
            'block' => 'contact_data',
            'fields' => [
                [
                    'name' => 'agency_name',
                    'type' => 'text',
                    'required' => true
                ],
                [
                    'name' => 'contact_email',
                    'type' => 'text',
                    'required' => true
                ],
                [
                    'name' => 'contact_phone',
                    'type' => 'text',
                    'required' => true
                ],
                [
                    'name' => 'zipcode',
                    'type' => 'text',
                    'required' => true
                ],
                [
                    'name' => 'city',
                    'type' => 'text',
                    'required' => true
                ],
                [
                    'name' => 'state',
                    'type' => 'text',
                    'required' => true
                ],
            ]
        ]
    ];

}
