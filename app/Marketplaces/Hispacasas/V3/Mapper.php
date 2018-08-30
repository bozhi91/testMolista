<?php namespace App\Marketplaces\Hispacasas\V3;

class Mapper extends \App\Marketplaces\Mapper {

    protected $locales = ['ca', 'da', 'de', 'en', 'es', 'fi', 'fr', 'it', 'nl', 'no', 'ru', 'sv'];

    /**
     * Maps a Contromia item to kyero.com format according to documentation.
     * https://s3.amazonaws.com/helpscout.net/docs/assets/569b1b0ec69791436155f6f7/attachments/575963b49033606599d1bffb/kyero_v3_import_spec.txt
     * https://s3.amazonaws.com/helpscout.net/docs/assets/569b1b0ec69791436155f6f7/attachments/5715e4229033602796677027/kyero_v3_test_feed.xml
     *
     * @return array
     */
    public function map()
    {
        $item = $this->item;

        $updated_at = !empty($item['updated_at']) ? \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $item['updated_at']) : \Carbon\Carbon::now();

        $map = [];
        $map['#id'] = $item['id'];
        $map['#date'] = $updated_at->format('Y-m-d H:m:s');
        $map['#ref'] = $item['reference'];
        $map['#price'] = intval($item['price']);
        $map['#currency'] = 'EUR';
        $map['#price_freq'] = $this->isSale() ? 'sale' : 'month';
        $map['#new_build'] = $this->isNew() ? 1 : 0;
        $map['#type'] = $this->type();
        $map['#town'] = $item['location']['city'];
        $map['#province'] = $item['location']['state'];
        $map['#postalcode'] = $item['location']['zipcode'];

        if (!empty($item['location']['lng']) && !empty($item['location']['lat']))
        {
            $map['location'] =
            [
                '#latitude' => $item['location']['lat'],
                '#longitude' => $item['location']['lng']
            ];
        }

        if (!empty($item['location']['district']))
        {
            $map['#location_detail'] = $item['location']['district'];
        }

        $map['#beds'] = $item['rooms'];
        $map['#baths'] = $item['baths'];
        $map['#pool'] = !empty($item['features']['pool']) ? 1 : 0;

        if (!empty($item['size']))
        {
            $map['surface_area'] =
            [
                '#built' => $item['size']
            ];
        }

        if (!empty($item['ec']))
        {
            $map['energy_rating'] =
            [
                '#consumption' => $item['ec'],
                '#emissions' => $item['ec'],
            ];
        }

        $map['url'] = $this->url();
        $map['desc'] = $this->desc();
        $map['features']['#feature'] = $this->features();
        $map['images'] = $this->images();

        return $map;
    }

    public function valid()
    {
        $rules = [
            'location.city' => 'required',
            'location.state' => 'required',
            'location.zipcode' => 'required',
        ];

        $messages = [
        ];

        $validator = \Validator::make($this->item, $rules, $messages);
        if ($validator->fails())
        {
            $this->errors = $validator->errors()->all();
        }

        return empty($this->errors);
    }

    protected function images()
    {
        $images = [];

        foreach ($this->item['images'] as $i => $image)
        {
            $images['#image@id='.($i+1)] = $image;
        }

        return $images;
    }

    protected function type()
    {
        return $this->item['type'];
    }

    protected function url()
    {
        return $this->grouped_translations('url');
    }

    protected function desc()
    {
        return $this->grouped_translations('description');
    }

    protected function grouped_translations($name)
    {
        $items = [];
        foreach ($this->item[$name] as $lang => $item)
        {
            if (in_array($lang, $this->locales))
            {
                $items['#'.$lang] = $item;
            }
        }

        return $items;
    }

    protected function features()
    {
        $features = [];
        foreach ($this->item['features'] as $f)
        {
            $features []= $this->translate($f);
        }

        return $features;
    }

}
