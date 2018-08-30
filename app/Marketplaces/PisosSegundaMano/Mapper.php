<?php namespace App\Marketplaces\PisosSegundaMano;

class Mapper extends \App\Marketplaces\Mapper {

    /**
     * Maps a Contromia item to pisos.com format according to documentation.
     *
     * @return array
     */
    public function map()
    {
        $item = $this->item;

        $map = [];
        $map['IdInmobiliariaExterna'] = $item['site_id']; //  OBLIGATORIO
        $map['IdPisoExterno'] = $item['id']; // OBLIGATORIO
        $map['TipoInmueble'] = $this->tipo_inmueble();    // OLBIGATORIO
        $map['TipoOperacion'] = $this->isRent() ? 3 : 4; // Alquiler / Venta
        $map['PrecioEur'] = intval($item['price']);
        $map['OpcionACompra'] = !empty($item['features']['option-to-buy']) ? 1 : 0;
        $map['NombrePoblacion'] = $item['location']['city']; // OBLIGATORIO
        $map['CodigoPostal'] = $item['location']['zipcode']; // OBLIGATORIO
        $map['Situacion1'] = $item['location']['district'];
        $map['SuperficieConstruida'] = floor($item['size']); // OBLIGATORIO
        $map['HabitacionesDobles'] = $item['rooms'];
        $map['BanosCompletos'] = $item['baths'];
        $map['Expediente'] = $item['id'];//floor($item['reference']); // OBLIGATORIO
        $map['Descripcion'] = $this->translate($item['description'], 'es');
        $map['Fotos'] = $this->fotos();
        $map['NombreCalle'] = $item['location']['address']; // OBLIGATORIO
        $map['NumeroCalle'] = $this->getAddressNumber(); // OBLIGATORIO
        $map['MostrarCalle'] = empty($item['location']['show_address']) ? 0 : 1;
        $map['Latitud'] = $item['location']['lat'];
        $map['Longitud'] = $item['location']['lng'];

        $map['AireAcondicionado_tiene'] = !empty($item['features']['air-conditioning']) ? 1 : 0;
        $map['SistemaSeguridad_tiene'] = !empty($item['features']['alarm']) ? 1 : 0;
        $map['Balcon_tiene'] = !empty($item['features']['balcony']) ? 1 : 0;
        $map['ArmariosEmpotrados_tiene'] = !empty($item['features']['built-in-closets']) ? 1 : 0;
        $map['Ascensor_tiene'] = !empty($item['features']['elevator']) ? 1 : 0;
        $map['Exterior_tiene'] = !empty($item['features']['exterior']) ? 1 : 0;
        $map['Amueblado_tiene'] = !empty($item['features']['furnished']) ? 1 : 0;
        $map['Garaje_tiene'] = (!empty($item['features']['garage']) || !empty($item['features']['parking'])) ? 1 : 0;
        $map['Jardin_tiene'] = !empty($item['features']['garden']) ? 1 : 0;
        $map['Calefaccion_tiene'] = !empty($item['features']['heating']) ? 1 : 0;
        $map['Interior_tiene'] = !empty($item['features']['interior']) ? 1 : 0;
        $map['Cocina_tiene'] = !empty($item['features']['kitchen']) ? 1 : 0;
        $map['Piscina_tiene'] = !empty($item['features']['pool']) ? 1 : 0;
        $map['Terraza_tiene'] = !empty($item['features']['terrace']) ? 1 : 0;
        $map['Cerrado_tiene'] = !empty($item['features']['walled']) ? 1 : 0;

        $map['EnergiaEmisionCategoria'] = $this->categoria_emision();

        return $map;
    }

    public function valid()
    {
        if ($this->isNew())
        {
            $this->errors []= \Lang::get('validation.second_hand');
            return false;
        }

        $this->item['location']['number'] = $this->getAddressNumber();

        $rules = [
            'site_id' => 'required',
            'location.address' => 'required',
            'location.city' => 'required',
            'location.zipcode' => 'required',
            'location.number' => 'required',
        ];

        $messages = [];

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

    /**
     * Piso
     * Torre
     * Parking
     * Local comercial
     * Nave industrial
     * Almacén
     * Despacho
     * Casa
     * Solar
     * Dúplex
     * Chalet
     * Oficina
     * Apartamento
     * Edificio
     * Ático
     * Masia
     * Hotel
     * Finca rústica
     * Cortijo
     * Garaje
     * Tríplex
     * Casa de campo
     * Refugio
     * Terreno
     * Parcela
     * Casa unifamiliar
     * Casa adosada
     * Loft
     * Restaurante
     * Trastero
     * Terreno industrial
     * Parcela industrial
     * Bungalow
     * Casa cueva
     * Estudio
     * Habitación
     * Casa pareada
     * Nave comercial
     * @return string
     */
    protected function tipo_inmueble()
    {
        $type = $this->item['type'];
        $types = [
            'apartment' => 'Piso',
            'duplex' => 'Dúplex',
            'house' => 'Casa',
			'terraced_house' => 'Casa',
            'lot' => 'Solar',
            'penthouse' => 'Ático',
            'store' => 'Local comercial',
            'villa' => 'Chalet',
            'hotel' => 'Hotel',
            'aparthotel' => 'Apartamento',
            'chalet' => 'Chalet',
            'bungalow' => 'Bungalow',
            'building' => 'Edificio',
            'industrial' => 'Nave industrial',
            'state' => 'Finca rústica',
            'farmhouse' => 'Masia',
			'garage' => 'Garaje',
			'plot' => 'Parcela',
			'oficina' => 'Oficina',
        ];

        return isset($types[$type]) ? $types[$type] : 'Piso';
    }

    protected function getAddressNumber()
    {
        if (!empty($this->item['location']['address_parts']['number'])) {
            return $this->item['location']['address_parts']['number'];
        }

        preg_match('#[^\d]+(\d+)[\s]*#msi', $this->item['location']['address'], $match);
        if (!empty($match[1])) {
            return $match[1];
        }

        return null;
    }

    protected function categoria_emision()
    {
        return !empty($this->item['ce']) ? $this->item['ce'] : 'No indicado';
    }
}
