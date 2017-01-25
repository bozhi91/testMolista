<?php namespace App\Marketplaces\Trovit;

class Mapper extends \App\Marketplaces\Mapper {

    /**
     * Maps a Molista item to trovit.com format according to:
     * http://about.trovit.com/feed-technical-specs/es/homes/specs.html
     *
     * @return array
     */
    public function map()
    {
        $item = $this->item;

        $map = [];
        $map['id'] = $item['id'];
        $map['url'] = $this->translate($item['url']);
        //$map['mobile_url'] = '';
        $map['title'] = $this->translate($item['title']);
        $map['type'] = $this->type();
        $map['content'] = $this->translate($item['description']);

        if ($this->isRent())
        {
            $map['price@period=monthly@currency=' . $item['currency']] = $this->decimal($item['price']);
        }
        else
        {
            $map['price@currency=' . $item['currency']] = $this->decimal($item['price']);
        }

        $map['property_type'] = $this->property_type();
        //$map['foreclosure_type'] = '';

        $map['city'] = $item['location']['city'];
        $map['region'] = $item['location']['state'];
        $map['postcode'] = $item['location']['zipcode'];
        $map['city_area'] = $item['location']['district'];

        if (!empty($item['location']['show_address']))
        {
            $map['address'] = $item['location']['address'];
            //$map['floor_number'] = '';
            //$map['neighborhood'] = '';
            //$map['country'] = '';
            $map['latitude'] = $this->decimal($item['location']['lat'], 8);
            $map['longitude'] = $this->decimal($item['location']['lng'], 8);
        }

        //$map['orientation'] = '';
        //$map['agency'] = '';
        //$map['mls_database'] = '';
        $map['floor_area'] = ceil($item['size']);
        //$map['plot_area'] = '';
        $map['rooms'] = $item['rooms'];
        $map['bathrooms'] = $item['baths'];
        //$map['condition'] = '';
        if (!empty($item['construction_year']))
        {
            $map['year'] = $item['construction_year'];
        }
        //$map['virtual_tour'] = '';
        $map['eco_score'] = $item['ec'];
        $map['pictures']['picture']= $this->pictures();
        //$map['date'] = '';
        //$map['expiration_date'] = '';
        //$map['by_owner'] = '';
        //$map['is_rent_to_own'] = '';
        $map['parking'] = !empty($item['features']['parking']) ? 'true' : 'false';
        //$map['foreclosure'] = '';
        $map['is_furnished'] = !empty($item['features']['furnished']) ? 'true' : 'false';
        $map['is_new'] = !empty($item['newly_build']) ? 'true' : 'false';

        return $map;
    }

    public function valid()
    {
        $rules = [
            'id' => 'required',
            'url' => 'required',
            'title' => 'required',
            'type' => 'required',
            'description.'.$this->iso_lang => 'required|min:30',
            'construction_year' => 'regex:#\d{4}#'
        ];

        $messages = [
            'construction_year.regex' => \Lang::get('validation.date'),
        ];

        $validator = \Validator::make($this->item, $rules, $messages);
        if ($validator->fails())
        {
            $this->errors = $validator->errors()->all();
        }

        return empty($this->errors);
    }

    /**
     * Possible options: http://about.trovit.com/feed-technical-specs/es/homes/specs.html
     *
     *	For Rent
     *	For Sale
     *	Roommate
     *	Parking For Rent
     *	Parking For Sale
     *	Office For Rent
     *	Office For Sale
     *	Land For Sale
     *	For Rent Local
     *	For Sale Local
     *	Transfer Local
     *	Country House Rentals
     *	Warehouse For Rent
     *	Warehouse For Sale
     *	Overseas
     *	Short Term Rentals
     *	Unlisted Foreclosure
    */
    protected function type()
    {
        switch ($this->item['type'])
        {
            case 'store':
                $type = $this->isRent() ? 'For Rent Local' : 'For Sale Local';
            break;
            case 'lot':
            case 'state':
                $type = $this->isSale() ? 'Land For Sale' : 'Land For Rent';
            break;
            case 'industrial':
                $type = $this->isSale() ? 'Warehouse For Sale' : 'Warehouse For Rent';
            break;
            case 'hotel':
            case 'aparthotel':
                $type = 'Short Term Rentals';
            break;
            case 'duplex':
            case 'house':
            case 'penthouse':
            case 'villa':
            case 'apartment':
			case 'terraced_house':
            default:
                $type = $this->isRent() ? 'For Rent' : 'For Sale';
            break;
        }

        return $type;
    }

    protected function property_type()
    {
        $types = [
            'apartment' => 'Apartamento',
            'duplex' => 'Dúplex',
            'house' => 'Casa',
			'terraced_house' => 'Casa',
            'lot' => 'Solar',
            'penthouse' => 'Ático',
            'store' => 'Local',
            'villa' => 'Villa',
            'ranch' => 'Finca',
            'flat' => 'Piso',
            'hotel' => 'Hotel',
            'aparthotel' => 'Aparthotel',
            'chalet' => 'Chalet',
            'bungalow' => 'Bungalow',
            'building' => 'Edificio',
            'industrial' => 'Nave industrial',
            'state' => 'Finca rústica',
            'farmhouse' => 'Masía rural',
        ];

        return isset($types[$this->item['type']]) ? $types[$this->item['type']] : $this->item['type'];
    }

    protected function pictures()
    {
        $pictures = [];

        foreach ($this->item['images'] as $image)
        {
            $pictures []= [
                    'picture_url' => $image
            ];
        }

        return $pictures;
    }

}
