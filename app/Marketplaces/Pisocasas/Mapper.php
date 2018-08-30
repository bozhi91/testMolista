<?php namespace App\Marketplaces\Pisocasas;

class Mapper extends \App\Marketplaces\Mapper {

    /**
     * Maps a Contromia item to pisocasas.com format according to:
     * http://www.pisocasas.com/enviar-feed-xml
     *
     * @return array
     */
    public function map()
    {
        $item = $this->item;

        $map = [];
        $map['nombrecomercial'] = $this->config['agency_name'];
        $map['#email1'] = $this->config['contact_email'];
        $map['#tlf1'] = preg_replace('#[^0-9]#', '', $this->config['contact_phone']);
        //$map['#web'] = '';
        //$map['logo'] = '';

        $map['#codpostalusu'] = $this->config['zipcode'];
        $map['poblacionusu'] = $this->config['city'];
        $map['provinciausu'] = $this->config['state'];
        $map['url_cliente'] = $this->translate($item['url']);
        $map['referencia'] = $item['reference'];
        $map['#tipo_inmueble'] = $this->tipo_inmueble();
        $map['#operacion'] = $this->isSale() ? 'Venta' : 'Alquiler';
        $map['#precio'] = intval($item['price']);

        $map['#codpostal'] = $item['location']['zipcode'];
        $map['poblacion'] = $item['location']['city'];
        $map['provincia'] = $item['location']['state'];
        $map['zona'] = $item['location']['district'];

        if (!empty($item['location']['show_addres']))
        {
            $map['calle'] = $item['location']['address_parts']['street'];
            $map['portal'] = $item['location']['address_parts']['door'];
            $map['planta'] = $item['location']['address_parts']['floor'];
        }

        if (!empty($item['size']))
        {
            $map['#metros'] = intval($item['size']);
        }

        if (!empty($item['features']['interior']))
        {
            $map['#exterior_interior'] = 'Interior';
        }

        if (!empty($item['features']['exterior']))
        {
            $map['#exterior_interior'] = 'Exterior';
        }

        $map['#dormitorios'] = $item['rooms'];
        $map['#banos'] = $item['baths'];
        $map['#amueblado'] = empty($item['features']['furnished']) ? 'No' : 'Si';
        $map['#ascensor'] = empty($item['features']['elevator']) ? 'No' : 'Si';
        $map['#piscina'] = empty($item['features']['pool']) ? 'No' : 'Si';

        $map['datos_adicionales'] = $this->translate($item['description']);

        foreach ($item['images'] as $i => $image)
        {
            if ($i > 15) continue;
            $map['#foto'.($i+1)] = $image;
        }

        if (!empty($item['location']['lat']) && !empty($item['location']['lng']))
        {
            $map['latitud'] = $item['location']['lat'];
            $map['longitud'] = $item['location']['lng'];
        }

        return $map;
    }

    public function valid()
    {
        if (in_array($this->item['type'], ['hotel', 'aparthotel', 'chalet', 'bungalow', 'building', 'plot']))
        {
            $this->errors []= \Lang::get('validation.type');
            return false;
        }

        $data = array_merge($this->item, $this->config);

        $rules = [
            'location.zipcode' => 'required',
            'location.city' => 'required',
            'location.state' => 'required',
            'location.district' => 'required',
            'reference' => 'required',
            'price' => 'required',
            'agency_name' => 'required',
            'contact_email' => 'required',
            'contact_phone' => 'required',
            'zipcode' => 'required',
            'city' => 'required',
            'state' => 'required',
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
     *
     * Piso, Local, Casa, Atico, Oficina, Garaje, Estudio, Duplex,
     * Terreno, Nave Industrial, Plantabaja, Trastero
     *
     * @return string
     */
    protected function tipo_inmueble()
    {
        $types = [
            'store' => 'Local',
            'lot' => 'Terreno',
            'state' => 'Terreno',
            'duplex' => 'Duplex',
            'house' => 'Casa',
			'terraced_house' => 'Casa',
            'farmhouse' => 'Casa',
            'penthouse' => 'Atico',
            'villa' => 'Casa',
            'apartment' => 'Piso',
            'industrial' => 'Nave Industrial',
			'garage' => 'Garaje',
			'office' => 'Oficina',
        ];

        return isset($types[$this->item['type']]) ? $types[$this->item['type']] : 'Piso';
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
