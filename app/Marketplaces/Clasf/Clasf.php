<?php namespace App\Marketplaces\Clasf;

class Clasf extends \App\Marketplaces\XML {

    protected $iso_lang = 'es';

    protected $configuration = [
        [
            'block' => 'contact_data',
            'fields' => [
                [
                    'name' => 'email',
                    'type' => 'text',
                    'required' => true
                ],
                [
                    'name' => 'contact_data',
                    'type' => 'textarea'
                ]
            ]
        ]
    ];

}
