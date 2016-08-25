<?php namespace App\Marketplaces\SpainHouses;

class Mapper extends \App\Marketplaces\Mapper {

    /**
     * Maps a Molista item to spain-houses.net format according to:
     * http://www.entersoftweb.com/xcp/sh/xsd/cartera.xsd
     * http://www.entersoftweb.com/xcp/sh/examples/cartera.xml
     *
     * @return array
     */
    public function map()
    {
        $item = $this->item;

        $map = [];
        $map['#referencia'] = $item['reference'];
        $map['#fecha'] = $this->format_date($item['updated_at']);
        $map['#tipoInmueble'] = $this->type();
        $map['#tipoOferta'] = $this->isRent() ? 2 : 1;
        $map['#precio'] = $this->decimal($item['price'], 0);
        $map['#provincia'] = $item['location']['state'];
        $map['#localidad'] = $item['location']['city'];
        $map['#geoLocalizacion'] = $item['location']['lat'].','.$item['location']['lng'];
        $map['#codigoPostal'] = $item['location']['zipcode'];
        $map['direccion'] = $item['location']['address'];
        $map['descripcionPrincipal']['descripcion'] = $this->tipoDescripcion($item['description']);
        $map['#mostrarDireccion'] = empty($item['location']['show_address']) ? 0 : 1;

        if (!empty($item['ec']))
        {
            $map['#calificacionEnergetica'] = strtoupper($item['ec']);
        }

        $map['listaImagenes']['imagen'] = $this->lista_imagenes();

        if (!empty($item['construction_year']))
        {
            $map['#antiguedad'] = date('Y') - $item['construction_year'];
        }

        if (!empty($item['size']) && $item['size_unit'] == 'sqm')
        {
            $map['#superficieTotal'] = $this->decimal($item['size']);
            $map['#tipoUnidades'] = 1;
        }

        $map['#dormitorios'] = intval($item['rooms']);
        $map['#baños'] = intval($item['baths']);

        $map['#amueblado'] = empty($item['features']['furnished']) ? 0 : 1;
        $map['#armariosEmpotrados'] = empty($item['features']['built-in-closets']) ? 0 : 1;
        $map['#jardines'] = empty($item['features']['garden']) ? 0 : 1;
        $map['#piscina'] = empty($item['features']['pool']) ? 0 : 1;
        $map['#exterior'] = empty($item['features']['exterior']) ? 0 : 1;
        $map['#terrazas'] = empty($item['features']['terrace']) ? 0 : 1;
        $map['#ascensor'] = empty($item['features']['elevator']) ? 0 : 1;
        $map['#alarma'] = empty($item['features']['alarm']) ? 0 : 1;
        $map['#zonaAparcamiento'] = empty($item['features']['parking']) ? 0 : 1;

        return $map;
    }

    public function valid()
    {
        if ($this->isTransfer()) {
			$this->errors []= \Lang::get('validation.transfer');
            return false;
		}

        if (@$this->item['type'] == 'hotel') {
			$this->errors []= \Lang::get('validation.type');
            return false;
		}

        $rules = [
            'reference' => 'required',
            'price' => 'required',
            'mode' => 'required',
            'location.state' => 'required',
            'location.city' => 'required',
            'location.lat' => 'required',
            'location.lng' => 'required',
            'location.address' => 'required',
            'location.zipcode' => 'required',
            'ec' => 'regex:#[a-gA-G]#',
        ];

        $messages = [];

        $validator = \Validator::make($this->item, $rules, $messages);
        if ($validator->fails())
        {
            $this->errors = $validator->errors()->all();
        }

        return empty($this->errors);
    }

    protected function format_date($date)
    {
        $time = strtotime($date);
        return date('Y-m-d', $time).'T'.date('H:i:s', $time);
    }

    /**
     * Possible options: http://www.entersoftweb.com/xcp/sh/helpers/main.xml
     *
     * 1: Estudios
     * 2: Apartamentos
     * 4: Pisos
     * 8: Dúplex
     * 16: Casas
     * 32: Bungalows
     * 64: Chalets
     * 128: Villas
     * 256: Oficinas
     * 512: Locales
     * 1024: Naves
     * 2048: Edificios
     * 4096: Fincas
     * 8192: Solares
     * 16384: Parcelas
     * 32768: Garajes
     *
    */
    protected function type()
    {
        switch ($this->item['type'])
        {
            case 'store':
                $type = 512;
                break;
            case 'lot':
                $type = 8192;
                break;
            case 'duplex':
                $type = 8;
                break;
            case 'house':
                $type = 16;
                break;
            case 'penthouse':
                $type = 2;
                break;
            case 'villa':
                $type = 128;
                break;
            case 'apartment':
            case 'aparthotel':
                $type = 2;
                break;
            default:
                $type = 4;
                break;
        }

        return $type;
    }

    protected function tipoDescripcion($textos)
    {
        $langs = ['es', 'en', 'de', 'fr', 'it', 'fi', 'ru', 'nl', 'sv', 'da', 'ar', 'zh'];

        $descripcion = [];
        foreach ($textos as $lang => $texto)
        {
            if (in_array($lang, $langs))
            {
                $descripcion []= [
                    '#idioma' => $lang,
                    'texto' => $texto
                ];
            }
        }

        return $descripcion;
    }

    protected function lista_imagenes()
    {
        $pictures = [];

        foreach ($this->item['images'] as $image)
        {
            $pictures []= [
                'url' => $image
            ];
        }

        return $pictures;
    }

}
