<?php namespace App\Marketplaces\Enalquiler;

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
     * Maps a Molista item to trovit.com format according to:
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
        //$map['id_propietario'] = '00000001';
        $map['referencia'] = $item['reference'];
        $map['titulo'] = $this->translate($item['title']);
        //$map['id_propietario'] = '';
        $map['num'] = $item['location']['zipcode'];
        $map['num_no_visible'] = $item['location']['show_address'] ? 0 : 1;
        $map['fk_id_tbl_esconder_en_mapa'] = $item['location']['show_address'] ? 1 : 3;
        $map['latitud'] = $this->decimal($item['lat'], 8);
        $map['longitud'] = $this->decimal($item['long'], 8);

        if (!empty($item['location']['show_address']))
        {
            $map['cp'] = $item['location']['zipcode'];
            $map['barrio'] = $item['location']['district'];
            $map['nombre_distrito'] = $item['location']['district'];
            $map['nombre_poblacion'] = $item['location']['city'];
            $map['nombre_provincia'] = $item['location']['state'];
        }

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
        $map['terraza'] = !empty($item['features']['terrase']) ? 1 : 0;
        $map['descripciones']['es']['breve_descripcion'] = $this->translate($item['description'], 'es');
        $map['fotos']['foto'] = $this->pictures();

        return $map;
    }

    public function valid()
    {
        if (!$this->isRent())
        {
            $this->errors []= 'Only properties for rent are allowed in this marketplace.';
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

    protected function pictures()
    {
        $pictures = [];

        foreach ($this->item['images'] as $i => $image)
        {
            $pictures []= [
                'fk_id_tbl_titulos_fotos' => $i+1,
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
            case 'villa':
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

}
