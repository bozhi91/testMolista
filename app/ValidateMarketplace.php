<?php

namespace App;

use  App\Marketplaces\Base;

class ValidateMarketplace extends Base {

    function validateProp(){
        $this->config = [];
        $this->iso_lang = null;
    }

}
