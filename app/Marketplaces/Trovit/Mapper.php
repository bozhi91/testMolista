<?php namespace App\Marketplaces\Trovit;

class Mapper {

    protected $item;
    protected $iso_lang;

    public function __construct(array $item, $iso_lang)
    {
        $this->item = $item;
        $this->iso_lang = $iso_lang;
    }

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
        $map['price'] = $this->decimal($item['price']);
        //$map['property_type'] = '';
        //$map['foreclosure_type'] = '';

        if (!empty($item['location']['show_address']))
        {
            $map['address'] = $item['location']['address'];
            //$map['floor_number'] = '';
            //$map['neighborhood'] = '';
            $map['city_area'] = $item['location']['district'];
            $map['city'] = $item['location']['city'];
            $map['region'] = $item['location']['territory'];
            //$map['country'] = '';
            $map['postcode'] = $item['location']['zipcode'];
            $map['latitude'] = $this->decimal($item['location']['lat'], 8);
            $map['longitude'] = $this->decimal($item['location']['long'], 8);
        }

        //$map['orientation'] = '';
        //$map['agency'] = '';
        //$map['mls_database'] = '';
        $map['floor_area'] = ceil($item['size']);
        //$map['plot_area'] = '';
        $map['rooms'] = $item['rooms'];
        $map['bathrooms'] = $item['baths'];
        //$map['condition'] = '';
        //$map['year'] = '';
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
        return true;
    }

    protected function translate($item, $lang = null)
    {
        if (!is_array($item))
        {
            return false;
        }

        if (!$lang)
        {
            $lang = $this->iso_lang;
        }

        // return current lang if set...
        if (isset($item[$lang]))
        {
            return $item[$lang];
        }

        // ...return first available if not
        return reset($item);
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
                $type = $this->isSale() ? 'Land For Sale' : '';
                break;
            case 'duplex':
            case 'house':
            case 'penthouse':
            case 'villa':
            case 'apartment':
            default:
                $type = $this->isRent() ? 'For Rent' : 'For Sale';
            break;
        }

        return $type;
    }

    protected function isSale()
    {
        return $this->item['mode'] == 'sale';
    }

    protected function isRent()
    {
        return $this->item['mode'] == 'rent';
    }

    protected function decimal($value, $precision = 2)
    {
        return number_format($value, $precision, '.', '');
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
