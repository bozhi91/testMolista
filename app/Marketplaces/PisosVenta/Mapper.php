<?php namespace App\Marketplaces\PisosVenta;

class Mapper extends \App\Marketplaces\Mapper {

    /**
     * Maps a Molista item to pisos.com format according to documentation.
     *
     * @return array
     */
    public function map()
    {
        $item = $this->item;

        $map = [];
        $map['IdPromocionExterna'] = $item['id']; // OBLIGATORIO
        $map['Expediente'] = floor($item['reference']); // OBLIGATORIO
        $map['Nombre'] = $this->translate($item['title']); // OBLIGATORIO
        $map['Descripcion'] = $this->translate($item['description']); // OBLIGATORIO
        $map['Poblacion'] = $item['location']['city']; // OBLIGATORIO
        $map['CodigoPostal'] = $item['location']['zipcode']; // OBLIGATORIO
        $map['MostrarNivelDireccion'] = empty($item['location']['show_address']) ? 1 : 3; // OBLIGATORIO
        $map['NombreCallePromocion'] = preg_replace('#\d+#', '', $item['location']['address']); // OBLIGATORIO
        $map['NumeroCallePromocion'] = preg_replace('#[^\d]+#', '', $item['location']['address']); // OBLIGATORIO
        $map['Fotos'] = $this->fotos(); // OBLIGATORIO 1 foto
        if (!empty($item['location']['lng']) && !empty($item['location']['lat']))
        {
            $map['Longitud'] = $item['location']['lng'];
            $map['Latitud'] = $item['location']['lat'];
        }

        $map['CabeceraCS'] = $this->translate($item['title']);
        $map['CabeceraCT'] = $this->translate($item['title'], 'ca');
        $map['CabeceraIN'] = $this->translate($item['title'], 'en   ');
        $map['CabeceraFR'] = $this->translate($item['title'], 'fr');
        $map['CabeceraAL'] = $this->translate($item['title'], 'de');

        $map['DescripcionCS'] = $this->translate($item['description']);
        $map['DescripcionCT'] = $this->translate($item['description'], 'ca');
        $map['DescripcionIN'] = $this->translate($item['description'], 'en   ');
        $map['DescripcionFR'] = $this->translate($item['description'], 'fr');
        $map['DescripcionAL'] = $this->translate($item['description'], 'de');

        $map['MostrarMapa'] = empty($item['location']['show_address']) ? 0 : 1;
        $map['UrlExterna'] = $this->translate($item['url']);
        $map['OcultarSituacion'] = empty($item['location']['show_address']) ? 1 : 0;

        $map['Piscina_box'] = !empty($item['features']['pool']) ? 1 : 0;
        $map['Seguidad_box'] = !empty($item['features']['alarm']) ? 1 : 0;
        //$map['Zonacomunitaria_box'] = !empty($item['features']['pool']) ? 1 : 0;
        //$map['InstalacionesDeportivas_box'] = !empty($item['features']['pool']) ? 1 : 0;
        //$map['CampoGolf_box'] = !empty($item['features']['pool']) ? 1 : 0;
        //$map['Playa_box'] = !empty($item['features']['pool']) ? 1 : 0;

        $map['Tipologias']['Tipologia'] =
        [
            'IdPromocionExterna' => $item['id'], // OLBIGATORIO
            'IdTipologiaExterna' => $item['reference'], // OLBIGATORIO
            'ReferenciaTipologia' => $item['reference'], // OLBIGATORIO
            'TipoInmueble' => $this->tipo_inmueble(), // OLBIGATORIO
            'SuperficieTotal' => $item['size'], // OLBIGATORIO
            'EnVenta' => 1, // OBLIGATORIO
            'EnAlquiler' => 0, // OBLIGATORIO
            'EnAlquilerOpcionCompra' => 0,
            'Descripcion' => $this->translate($item['description']), // OBLIGATORIO
            'Fotos' => $this->fotos(), // OBLIGATORIO
            'PrecioVenta' => intval($item['price']),
            'HabitacionesDobles' => intval($item['rooms']),
            'BanoC_num' => intval($item['baths']),
            'Garaje_box' => (!empty($item['features']['garage']) || !empty($item['features']['parking'])) ? 1 : 0,
            'Terraza_box' => !empty($item['features']['terrace']) ? 1 : 0,
            'AireAcondicionado_box' => !empty($item['features']['air-conditioning']) ? 1 : 0,
            'Jardin_box' => !empty($item['features']['garden']) ? 1 : 0,
            'Armarios_box' => !empty($item['features']['built-in-closets']) ? 1 : 0,
            'Calefaccion_box' => !empty($item['features']['heating']) ? 1 : 0,
            'Cocina_box' => !empty($item['features']['kitchen']) ? 1 : 0,
            'Balcon_box' => !empty($item['features']['balcony']) ? 1 : 0,
        ];

        return $map;
    }

    public function valid()
    {
        if (!$this->isRent())
        {
            $this->errors []= \Lang::get('validation.sale');
            return false;
        }

        $rules = [
            'site_id' => 'required',
            'reference' => 'required',
            'title' => 'required',
            'description' => 'required',
            'location.city' => 'required',
            'location.zipcode' => 'required',
            'location.address' => 'required',
            'images.0' => 'required',
            'size' => 'required',
        ];

        $messages = [
            'images.0.required' => \Lang::get('validation.one_picture'),
        ];

        $validator = \Validator::make($this->item, $rules, $messages);
        if ($validator->fails())
        {
            $this->errors = $validator->errors()->all();
        }

        return empty($this->errors);
    }

    protected function fotos()
    {
        $pictures = [];

        foreach ($this->item['images'] as $i => $image)
        {
            $pictures['Foto'.($i+1)] = $image;
        }

        return $pictures;
    }

    protected function tipo_inmueble()
    {
        $type = $this->item['type'];
        $types = [
            'apartment' => 'Piso',
            'duplex' => 'Dúplex',
            'house' => 'Casa',
            'lot' => 'Solar',
            'penthouse' => 'Ático',
            'store' => 'Local comercial',
            'villa' => 'Chalet',
        ];

        return isset($types[$type]) ? $types[$type] : 'Piso';
    }

    protected function categoria_emision()
    {
        return !empty($this->item['ce']) ? $this->item['ce'] : 'No indicado';
    }
}
