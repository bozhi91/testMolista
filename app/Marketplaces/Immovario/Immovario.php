<?php

namespace App\Marketplaces\Immovario;

class Immovario extends \App\Marketplaces\XML {

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
            ]
        ]
    ];
}
