<?php namespace App\Marketplaces\PisosAlquiler;

class Mapper {

    protected $item;
    protected $iso_lang;

    protected $errors = [];

    public function __construct(array $item, $iso_lang)
    {
        $this->item = $item;
        $this->iso_lang = $iso_lang;
    }

    /**
     * Maps a Molista item to pisos.com format according to documentation.
     *
     * @return array
     */
    public function map()
    {
        $item = $this->item;

        $map = [];
        $map['IdInmobiliariaExterna'] = $item['user_id']; //  OBLIGATORIO
        $map['IdPisoExterno'] = $item['id']; // OBLIGATORIO
        $map['TipoInmueble'] = $this->tipo_inmueble();    // OLBIGATORIO
        $map['TipoOperacion'] = 3; // Alquiler
        $map['PrecioEur'] = intval($item['price']);
        $map['OpcionACompra'] = !empty($item['features']['option-to-buy']) ? 1 : 0;
        $map['NombrePoblacion'] = $item['location']['city']; // OBLIGATORIO
        $map['CodigoPostal'] = $item['location']['zipcode']; // OBLIGATORIO
        $map['Situacion1'] = $item['location']['district'];
        $map['SuperficieConstruida'] = floor($item['size']); // OBLIGATORIO
        $map['HabitacionesDobles'] = $item['rooms'];
        $map['BanosCompletos'] = $item['baths'];
        $map['Expediente'] = floor($item['reference']); // OBLIGATORIO
        $map['Descripcion'] = $this->translate($item['description'], 'es');
        $map['Fotos'] = $this->fotos();
        $map['NombreCalle'] = $item['location']['address']; // OBLIGATORIO
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
        if (!$this->isRent())
        {
            $this->errors []= 'Only properties for rent are allowed in this marketplace.';
        }

        if (empty($this->item['user_id']))
        {
            $this->errors []= 'Agency identifier is required.';
        }

        return empty($this->errors);
    }

    public function errors()
    {
        return $this->errors;
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

    protected function isRent()
    {
        return $this->item['mode'] == 'rent';
    }

    protected function decimal($value, $precision = 2)
    {
        return number_format($value, $precision, '.', '');
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
