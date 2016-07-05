<?php namespace App\Marketplaces\Clasf;

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
        $map['url'] = $item['id'];
        $map['title'] = strip_tags($this->translate($item['title']));
        $map['content'] = strip_tags($this->translate($item['description']), '<br>');
        $map['#price'] = $this->decimal($item['price']);
        $map['email'] = '';

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
        $rules = [
            'id' => 'required',
            'title' => 'required',
            'description.'.$this->iso_lang => 'required|min:100'
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
                $type = 'venta terrenos';
                break;
            case 'duplex':
            case 'house':
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
            case 'store':
                $type = $this->isRent() ? 'alquiler de locales' : 'venta de locales';
                break;
            case 'lot':
                $type = $this->isSale() ? 'venta solares' : '';
                break;
            case 'penthouse':
                $type = 'alquiler venta aticos';
                break;
            case 'duplex':
            case 'house':
            case 'villa':
                $type = 'alquiler venta casas';
                break;
            case 'apartment':
                $type = 'alquiler y compra apartamentos';
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
                $type = '';
                break;
            case 'penthouse':
                $type = $this->isRent() ? 'alquiler de aticos' : 'venta de aticos';
                break;
            case 'duplex':
            case 'house':
            case 'villa':
                $type = $this->isRent() ? 'alquiler de casas' : 'venta de casas';
                break;
            case 'apartment':
                $type = $this->isRent() ? 'alquiler apartamentos' : 'venta de apartamentos';
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
