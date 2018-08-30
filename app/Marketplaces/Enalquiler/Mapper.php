<?php namespace App\Marketplaces\Enalquiler;

class Mapper extends \App\Marketplaces\Mapper {

    /**
     * Maps a Contromia item to enalquiler.com format according to:
     * http://www.enalquiler.com/feeds/public/inmuebles.xsd
     * http://www.enalquiler.com/feeds/public/inmuebles.xml
     *
     * @return array
     */
    public function map()
    {
        $item = $this->item;

        $map = [];
        $map['id'] = $item['id'];
        $map['id_propietario'] = $item['site_id'];
        $map['referencia'] = $item['reference'];
        //$map['titulo'] = $this->translate($item['title']);
        //$map['id_propietario'] = '';
        $map['num'] = $item['location']['zipcode'];
        $map['num_no_visible'] = $item['location']['show_address'] ? 0 : 1;
        $map['fk_id_tbl_esconder_en_mapa'] = $item['location']['show_address'] ? 1 : 3;
        $map['latitud'] = $this->decimal($item['location']['lat'], 8);
        $map['longitud'] = $this->decimal($item['location']['lng'], 8);

        if (!empty($item['location']['show_address']))
        {
            $map['cp'] = $item['location']['zipcode'];
            $map['barrio'] = $item['location']['district'];
            $map['nombre_distrito'] = $item['location']['district'];
        }

        $map['nombre_poblacion'] = $item['location']['city'];
        $map['nombre_provincia'] = $item['location']['state'];

        $map['precio_mes'] = $this->decimal($item['price']);
        $map['fk_id_tbl_categorias'] = $this->category();
        $map['metros2'] = ceil($item['size']);
        $map['eficiencia_energetica'] = $item['ec'];
        $map['num_habitaciones'] = $item['rooms'];
        $map['num_banos'] = $item['baths'];
        $map['amueblado'] = !empty($item['features']['furnished']) ? 1 : 0;
        $map['aa'] = !empty($item['features']['air-conditioning']) ? 1 : 0;
        $map['balcon'] = !empty($item['features']['balcony']) ? 1 : 0;
        $map['ascensor'] = !empty($item['features']['elevator']) ? 1 : 0;
        $map['exterior'] = !empty($item['features']['exterior']) ? 1 : 0;
        $map['amueblado'] = !empty($item['features']['furnished']) ? 1 : 0;
        $map['garaje_incluido'] = !empty($item['features']['parking']) ? 1 : 0;
        $map['jardin'] = !empty($item['features']['garden']) ? 1 : 0;
        $map['calefaccion'] = !empty($item['features']['heating']) ? 1 : 0;
        $map['opcion_compra'] = !empty($item['features']['option-to-buy']) ? 1 : 0;
        $map['piscina'] = !empty($item['features']['pool']) ? 1 : 0;
        $map['terraza'] = !empty($item['features']['terrace']) ? 1 : 0;
        $map['descripciones']['es']['breve_descripcion'] = $this->translate($item['description'], 'es');
        $map['fotos']['foto'] = $this->pictures();
        if (!empty($item['construction_year']))
        {
            $map['fk_id_tbl_antiguedad_inmuebles'] = $this->antiguedad_inmuebles($item['construction_year']);
        }

        return $map;
    }

    public function valid()
    {
        if (!$this->isRent())
        {
            $this->errors []= \Lang::get('validation.rent');
            return false;
        }

        if (in_array($this->item['type'], ['hotel', 'aparthotel',
			'building', 'industrial', 'bungalow', 'state', 'garage', 'plot', 'office']))
        {
            $this->errors []= \Lang::get('validation.type');
            return false;
        }

        $rules = [];

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

    protected function pictures()
    {
        $pictures = [];

        foreach ($this->item['images'] as $i => $image)
        {
            $pictures []= [
                'fk_id_tbl_titulos_fotos' => 12, // Otras fotos: http://www.enalquiler.com/feeds/public/helpers/titulos_fotos.xml
                'url' => $image
            ];
        }

        return $pictures;
    }

    /**
     * http://www.enalquiler.com/feeds/public/helpers/categorias.xml
     * 2: Piso
     * 3: Ático
     * 4: Dúplex
     * 5: Loft
     * 6: Estudio
     * 7: Casa/Chalet
     *
     * @return integer
     */
    protected function category()
    {
        switch ($this->item['type']) {
            case 'house':
			case 'terraced_house':
            case 'villa':
            case 'farmhouse':
            case 'chalet':
                $code = 7;
                break;
            case 'duplex':
                $code = 4;
                break;
            case 'penthouse':
            $code = 3;
                break;
            case 'apartment':
            default:
                $code = 2;
                break;
        }

        return $code;
    }

    /**
     * http://www.enalquiler.com/feeds/public/helpers/antiguedad_inmuebles.xml
     * 1: Menos de 5 años
     * 2: Entre 5 y 10 años
     * 3: Entre 10 y 20 años
     * 4: Entre 20 y 30 años
     * 5: Más de 30 años
     * 6: No disponible
     *
     * @param  integer $year Year of construction
     * @return integer
     */
    protected function antiguedad_inmuebles($year)
    {
        if (!intval($year))
        {
            return 6;
        }

        $old = date('Y') - $year;

        switch(true)
        {
            case $old < 5:
                $condition = 1;
                break;

            case $old < 10:
                $condition = 2;
                break;

            case $old < 20:
                $condition = 3;
                break;

            case $old < 30:
                $condition = 4;
                break;

            case $old > 30;
                $condition = 5;
                break;
        }

        return $condition;
    }

}
