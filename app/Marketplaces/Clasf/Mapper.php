<?php namespace App\Marketplaces\Clasf;

class Mapper extends \App\Marketplaces\Mapper {

    /**
     * Maps a Contromia item to clasf.es format according to:
     * http://www.clasf.es/feed-specifications/
     *
     * @return array
     */
    public function map()
    {
        $item = $this->item;

        $map = [];
        $map['url'] = $item['id'];
        $map['title'] = strip_tags($this->translate($item['title']));
        $map['content'] = strip_tags($this->translate($item['description']), '<br>');
        $map['#price'] = $this->decimal($item['price']);
        $map['email'] = $this->config['email'];
        $map['contact'] = $this->config['contact_data'];

        if (!empty($item['location']['city']))
        {
            $map['city'] = $item['location']['city'];
        }

        if (!empty($item['location']['state']))
        {
            $map['province'] = $item['location']['state'];
        }

        // http://www.clasf.es/feed-specifications/#categories
        $map['category'] = 'Inmobiliaria';
        $map['subcategory'] = $this->subcategory();
        $map['subcategory2'] = $this->subcategory2();
        $map['subcategory3'] = $this->subcategory3();

        $map['pictures']['url_img']= $this->pictures();

        return $map;
    }

    public function valid()
    {
        $data = array_merge($this->item, $this->config);

        $rules = [
            'id' => 'required',
            'title' => 'required',
            'description.'.$this->iso_lang => 'required|min:100',
            'email' => 'required',
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
     * Possible options: http://www.clasf.es/feed-specifications/#categories
    */
    protected function subcategory()
    {
        switch ($this->item['type'])
        {
            case 'store':
                $type = 'locales comerciales';
            break;
            case 'lot':
            case 'state':
                $type = 'venta terrenos';
            break;
            case 'building':
                $type = 'alquiler y venta edificios';
            break;
            case 'industrial':
                $type = 'venta naves industriales';
            break;
			case 'garage':
				$type = 'alquiler venta plazas garaje';
			break;
			case 'plot':
				$type = 'venta terrenos';
			break;
            case 'office':
				$type = 'alquiler venta oficinas';
			break;
            case 'duplex':
            case 'house':
			case 'terraced_house':
            case 'penthouse':
            case 'villa':
            case 'apartment':
            default:
                $type = 'viviendas';
                break;
        }

        return $type;
    }

    /**
     * Possible options: http://www.clasf.es/feed-specifications/#categories
    */
    protected function subcategory2()
    {
        switch ($this->item['type'])
        {
			case 'garage':
				$type = '';
			break;
			case 'plot':
				$type = 'parcelas rusticas';
			break;
            case 'store':
                $type = $this->isRent() ? 'alquiler de locales' : ($this->isTransfer() ? 'traspaso' : 'venta de locales');
            break;
            case 'lot':
                $type = $this->isSale() ? 'venta solares' : '';
            break;
            case 'state':
                $type = 'alquiler venta fincas rusticas';
            break;
            case 'penthouse':
                $type = 'alquiler venta aticos';
            break;
            case 'duplex':
            case 'house':
            case 'villa':
            case 'farmhouse':
                $type = 'alquiler venta casas';
            break;
            case 'apartment':
                $type = 'alquiler y compra apartamentos';
            break;
            case 'building':
                $type = $this->isSale() ? 'venta de edificios' : 'alquiler de edificios';
            break;
            case 'industrial':
                $type = $this->isSale() ? 'venta de naves' : 'alquiler de naves';
            break;
            case 'chalet':
                $type = 'alquiler venta chalets';
            break;
            case 'office':
				$type = 'venta de oficinas';
			break;
            default:
                $type = 'alquiler venta pisos';
            break;
        }

        return $type;
    }

    /**
     * Possible options: http://www.clasf.es/feed-specifications/#categories
    */
    protected function subcategory3()
    {
        switch ($this->item['type'])
        {
            case 'store':
            case 'lot':
            case 'industrial':
            case 'building':
			case 'garage':
			case 'plot':
                $type = '';
            break;
            case 'penthouse':
                $type = $this->isRent() ? 'alquiler de aticos' : 'venta de aticos';
            break;
            case 'state':
                $type = $this->isRent() ? 'alquiler de fincas' : 'venta de fincas';
            break;
            case 'duplex':
            case 'house':
            case 'villa':
            case 'farmhouse':
                $type = $this->isRent() ? 'alquiler de casas' : 'venta de casas';
            break;
            case 'apartment':
                $type = $this->isRent() ? 'alquiler apartamentos' : 'venta de apartamentos';
            break;
            case 'chalet':
                $type = $this->isRent() ? 'alquiler de chalets' : 'venta de chalets';
            break;
            default:
                $type = $this->isRent() ? 'alquiler de pisos' : 'venta de pisos';
            break;
        }

        return $type;
    }

    protected function pictures()
    {
        $pictures = [];

        foreach ($this->item['images'] as $i => $image)
        {
            if ($i > 20) continue;

            $pictures []= [
                $image
            ];
        }

        return $pictures;
    }

}
