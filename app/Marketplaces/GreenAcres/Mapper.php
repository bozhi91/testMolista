<?php namespace App\Marketplaces\GreenAcres;

class Mapper extends \App\Marketplaces\Mapper {

    /**
     * Maps a Molista item to green-acres.com format: http://www.green-acres.com/en/GatewayInfo
     *
     * @return array
     */
    public function map()
    {
        $item = $this->item;

        $map = [];
        $map['#account_id'] = $this->config['account_id'];
        $map['#reference'] = $item['reference'];
        $map['#type'] = $this->isRent() ? 'rentals' : 'properties';
        $map['#price'] = $this->decimal($item['price'], 0);
        $map['#has_included_fees'] = 0;
        $map['#agency_rates_type'] = 2;
        $map['#currency'] = 'EUR';
        $map['#commune'] = $item['location']['state'];
        $map['#country_code'] = $item['location']['country'];
        $map['#habitat'] = $this->habitat();

        if (!empty($item['location']['lat']) && !empty($item['location']['lng']))
        {
            $map['#latitude'] = $this->decimal($item['location']['lat'], 8);
            $map['#longitude'] = $this->decimal($item['location']['lng'], 8);
        }
        else
        {
            $map['#postal_code'] = $item['location']['zipcode'];
        }

        $map['#mandate_number'] = $item['reference'];

        foreach ($item['title'] as $lang => $title)
        {
            $map['title_'.$lang] = $title;
        }

        foreach ($item['description'] as $lang => $title)
        {
            $map['summary_'.$lang] = $title;
        }

        $map['#n_beds'] = $item['rooms'];
        $map['#n_baths'] = $item['baths'];
        $map['#dpe_type'] = strtoupper($item['ec']);
        $map['#precise_location'] = empty($item['location']['show_address']) ? 0 : 1;

        if (!empty($item['location']['address_parts']['number']))
        {
            $map['street_number'] = $item['location']['address_parts']['number'];
        }

        if (!empty($item['location']['address_parts']['street']))
        {
            $map['street_name'] = $item['location']['address_parts']['street'];
        }

        $map['#furnished'] = empty($item['features']['furnished']) ? 0 : 1;

        $map['pics'] = $this->pics();

        return $map;
    }

    public function valid()
    {
        $data = array_merge($this->item, $this->config);

        $rules = [
            'reference' => 'required|max:16',
            'type' => 'required',
            'price' => 'required',
            'location.state' => 'required',
            'location.country' => 'required',
            'location.zipcode' => 'required',
            'account_id' => 'required',
        ];

        $messages = [];

        $validator = \Validator::make($data, $rules, $messages);
        if ($validator->fails())
        {
            $this->errors = $validator->errors()->all();
        }

        return empty($this->errors);
    }

    /**
    * OPTIONS: ('old', 'recent', 'luxury', 'land', 'appartement', 'business', 'new', 'fazenda')
     */
    protected function habitat()
    {
        $types = [
            'store' => 'business',
            'lot' => 'land',
            'duplex' => 'appartement',
            'house' => 'old',
            'penthouse' => 'luxury',
            'villa' => 'old',
            'apartment' => 'appartement',
        ];

        return isset($types[$this->item['type']]) ? $types[$this->item['type']] : 'old';
    }

    protected function pics()
    {
        $pictures = [];

        foreach ($this->item['images'] as $i => $image)
        {
            if (!$i > 25) continue;

            $pictures['pic@order='.($i+1)] = $image;
        }

        return $pictures;
    }

}
