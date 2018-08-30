<?php namespace App\Marketplaces\Globaliza;

class Mapper extends \App\Marketplaces\Mapper {

    /**
     * Maps a Contromia item to globaliza.com format.
     *
     * @return array
     */
    public function map()
    {
        $item = $this->item;

        $map = [];
        $map['REFERENCIA'] = $item['reference'];
        $map['TIPOINMUEBLE'] = $this->type();
        $map['SUBTIPOINMUEBLE'] = $this->subtype();
        $map['#FINALIDAD'] = $this->isSale() ? 'Venta' : 'Alquiler';
        $map['#PRECIO'] = $this->decimal($item['price'], 0);
        $map['PROVINCIA'] = $item['location']['state'];
        $map['POBLACION'] = $item['location']['city'];
        $map['CP'] = $item['location']['zipcode'];
        $map['DIRECCION'] = !empty($item['location']['address_parts']['street']) ? $item['location']['address_parts']['street'] : $item['location']['address'];
        if (!empty($item['location']['address_parts']['number']))
        {
            $map['NUMERO'] = $item['location']['address_parts']['number'];
        }
        $map['#MOSTRAR_DIRECCION'] = empty($item['location']['show_address']) ? 'SI' : 'NO';
        $map['ZONA'] = $item['location']['district'];

        if (!empty($item['location']['lat']) && !empty($item['location']['lng']))
        {
            $map['LATITUD'] = $this->decimal($item['location']['lat'], 8);
            $map['LONGITUD'] = $this->decimal($item['location']['lng'], 8);
        }

        $map['#M_CONSTRUIDOS'] = ceil($item['size']);
        $map['#DORMITORIOS'] = $item['rooms'];
        $map['#BAOS'] = $item['baths'];
        $map['DESCRIPCION'] = $this->translate($item['description']);
        $map['TITULO'] = $this->translate($item['title']);

        $map['#AIRE_ACONDICIONADO'] = !empty($item['features']['air-conditioning']) ? 'SI' : 'NO';
        $map['#AO_EDIFICACION'] = $this->antiguedad();
        $map['#ASCENSORES'] = !empty($item['features']['elevator']) ? 'SI' : 'NO';
        $map['#CALEFACCION'] = !empty($item['features']['heating']) ? 'SI' : 'NO';
        $map['#JARDIN_PRIVADO'] = !empty($item['features']['garden']) ? 'SI' : 'NO';
        $map['#PISCINA'] = !empty($item['features']['pool']) ? 'SI' : 'NO';

        if (!empty($item['location']['address_parts']['floor']))
        {
            $map['#PLANTA_PISO'] = $item['location']['address_parts']['floor'];
        }

        $map['#ALARMA'] = !empty($item['features']['alarm']) ? 'SI' : 'NO';

        if (!empty($item['ec']))
        {
            $map['#EFICIENCIA_ENERGETICA'] = strtoupper($item['ec']);
        }

        $map['photos']['photo'] = $this->photos();

        return $map;
    }

    public function valid()
    {
        $rules = [
            'reference' => 'required',
            'type' => 'required',
            'mode' => 'required|in:rent,sale',
            'location.state' => 'required',
            'location.city' => 'required',
            'location.zipcode' => 'required',
            'location.address' => 'required',
            'location.district' => 'required',
            'size' => 'required',
            'baths' => 'required',
            'description' => 'required',
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
     *
     * ÁRBOL DE TIPOLOGÍAS (Tipo/Subtipo)
     *
     * Vivienda
     * 			 Bungalow
     *           Loft
     *           Bajo
     *           Dúplex
     *           Apartamento / Estudio
     *           Piso
     *           Chalet adosado / pareado
     *           Edifio
     *           Mansion / Palacio
     *           Atico
     *           Casa Rural
     *           Chalet individual / Casa
     *   Local comercial
     *           A la calle
     *           Dentro un centro comercial
     *   Oficina
     *           Modulable
     *           Temporal
     *           Fija/Permanente
     *   Suelo
     *           Urbanizable programado
     *           Zona turistica
     *           Residencial
     *           Terciario
     *           Finca
     *           Jardin
     *           Parcela
     *           Solar
     *           Terrenos
     *           Parque de ocio
     *           Parte tematico
     *           Parque empresarial
     *           Poligono comercial
     *           Poligono industrial
     *   Garaje
     *           Plaza de garaje
     *  Nave
     *  		Nave comercial
     *    		Nave industrial
     */
    protected function type()
    {
        $types = [
            'store' => 'Local comercial',
            'lot' => 'Suelo',
            'duplex' => 'Vivienda',
            'house' => 'Vivienda',
			'terraced_house' => 'Vivienda',
            'penthouse' => 'Vivienda',
            'villa' => 'Vivienda',
            'apartment' => 'Vivienda',
            'chalet' => 'Vivienda',
            'bungalow' => 'Vivienda',
            'building' => 'Vivienda',
            'farmhouse' => 'Vivienda',
            'industrial' => 'Nave',
            'state' => 'Suelo',
			'garage' => 'Garaje',
			'plot' => 'Suelo',
			'office' => 'Oficina',
        ];

        return isset($types[$this->item['type']]) ? $types[$this->item['type']] : 'Vivienda';
    }

    protected function subtype()
    {
        $types = [
            'store' => 'A la calle',
            'lot' => 'Solar',
            'duplex' => 'Dúplex',
            'house' => 'Chalet individual / Casa',
			'terraced_house' => 'Chalet adosado / pareado',
            'penthouse' => 'Atico',
            'villa' => 'Casa Rural',
            'apartment' => 'Apartamento / Estudio',
            'aparthotel' => 'Apartamento / Estudio',
            'chalet' => 'Chalet adosado / pareado',
            'bungalow' => 'Bungalow',
            'building' => 'Edificio',
            'industrial' => 'Nave industrial',
            'state' => 'Finca',
            'farmhouse' => 'Casa Rural',
			'garage' => 'Plaza de garaje',
			'plot' => 'Parcela',
			'office' => 'Fija/Permanente',
        ];

        return isset($types[$this->item['type']]) ? $types[$this->item['type']] : 'Piso';
    }

    /**
     *
     * - NO_DISPONIBLE
     * - MENOS_DE_5_ANIOS
     * - ENTRE_5_Y_10_ANIOS
     * - ENTRE_10_Y_20_ANIOS
     * - ENTRE_20_Y_30_ANIOS
     * - MAS_DE_30_ANIOS
     * - NUEVO
     * - MENOS_DE_1_ANIO
     *
     * @return string
     */
    protected function antiguedad()
    {
        $antiguedad = 'NO_DISPONIBLE';
        if (!empty($this->item['construction_year']))
        {
            $old = date('Y') - $this->item['construction_year'];
            switch(true)
            {
                case $old < 5:
                    $antiguedad = 'MENOS_DE_5_ANIOS';
                    break;

                case $old < 10:
                    $antiguedad = 'ENTRE_5_Y_10_ANIOS';
                    break;

                case $old < 20:
                    $condition = 'ENTRE_10_Y_20_ANIOS';
                    break;

                case $old < 30:
                    $antiguedad = 'ENTRE_20_Y_30_ANIOS';
                    break;

                case $old >= 30;
                    $antiguedad = 'MAS_DE_30_ANIOS';
                    break;
            }
        }
        return $antiguedad;
    }

    protected function photos()
    {
        $pictures = [];

        foreach ($this->item['images'] as $i => $image)
        {
            if (!$i > 20) continue;

            $pictures []= [
                '#orden' => $i+1,
                '#url' => $image
            ];
        }

        return $pictures;
    }

}
