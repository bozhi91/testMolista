<?php namespace App\Marketplaces\ThinkSpain;

class Mapper extends \App\Marketplaces\Mapper {

    /**
     * Maps a Molista item to thinkspain.com format according to:
     * http://www.thinkspain.com/thinkspain.xml
     *
     * @return array
     */
    public function map()
    {
        $item = $this->item;

        $map = [];
        $map['#last_amended_date'] = $item['updated_at'];
        $map['#unique_id'] = $item['id'];
        $map['#agent_ref'] = $item['reference'];
        $map['#euro_price'] = intval($item['price'] * ($this->saleType() == 'holiday' ? 4 : 1));
        $map['#sale_type'] = $this->saleType();
        $map['#property_type'] = $this->property_type();
        $map['town'] = $item['location']['city'];
        $map['location_detail'] = $item['location']['district'];
        $map['province'] = $item['location']['state'];
        $map['description'] = $this->tranlatedText($item['description']);
        $map['images'] = $this->images();
        $map['#bedrooms'] = intval($item['rooms']);
        $map['#bathrooms'] = intval($item['baths']);
        $map['#pool'] = empty($item['features']['pool']) ? 0 : 1;
        $map['#aircon'] = empty($item['features']['air-conditioning']) ? 0 : 1;
        $map['#heating'] = empty($item['features']['heating']) ? 0 : 1;
        $map['#garage'] = empty($item['features']['garage']) ? 0 : 1;

        return $map;
    }

    public function valid()
    {
        if ($this->isTransfer()) {
			$this->errors []= \Lang::get('validation.transfer');
            return false;
		}

        $rules = [
            'updated_at' => 'required',
            'mode' => 'required',
            'location.city' => 'required',
            'description.'.$this->iso_lang => 'required',
        ];

        $messages = [];

        $validator = \Validator::make($this->item, $rules, $messages);
        if ($validator->fails())
        {
            $this->errors = $validator->errors()->all();
        }

        return empty($this->errors);
    }

    /**
     * Beach Apartment
     * Flat
     * Penthouse
     * Studio
     * Villa
     * Finca/Country House
     * Semi-detached Villa
     * Terraced Villa
     * Undeveloped Land
     * Building Plot
     * Restaurant/Bar
     * Hotel
     * Office
     * Business
     * Townhouse
     * Garage
     * Apartment
     * Ruin
     * Cave House
     * Bungalow
     * Wooden Home
     * Loft
     * Shop
     * Commercial
     * Mobile Home
     *
     * @return string
     */
    protected function property_type()
    {
        $types = [
            'store' => 'Commercial',
            'lot' => 'Undeveloped Land',
            'duplex' => 'Apartment',
            'house' => 'Townhouse',
            'penthouse' => 'Penthouse',
            'villa' => 'Villa',
            'apartment' => 'Apartment',
            'aparthotel' => 'Apartment',
            'hotel' => 'Hotel',
        ];

        return isset($types[$this->item['type']]) ? $types[$this->item['type']] : 'Flat';
    }

    protected function images()
    {
        $pictures = [];

        foreach ($this->item['images'] as $i => $image)
        {
            if ($i > 9) continue;
            $pictures ['photo@id='.($i+1)] = $image;
        }

        return $pictures;
    }

    protected function tranlatedText($texts)
    {
        $langs = ['en', 'es', 'nl', 'fr', 'de'];

        $response = [];
        foreach ($texts as $lang => $text)
        {
            if (!in_array($lang, $langs)) continue;
            $response[$lang] = $text;
        }

        return $response;
    }

    protected function saleType()
    {
        if ($this->isRent()) {
            return in_array($this->item['type'], ['hotel','aparthotel']) ? 'holiday' : 'longterm';
        }

        return 'sale';
    }

}
