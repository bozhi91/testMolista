<?php

namespace App\Marketplaces\Immovario;


class Mapper  extends \App\Marketplaces\Mapper {

    public function map() {
        $item = $this->item;

       // echo json_encode($this->item); die;

        $map = [];

        $map['id_inmueble'] = $item['id'];

        return $map;

    }

    /**
     * @return boolean
     */
    public function valid() {

        if (in_array($this->item['type'], ['garage', 'plot'])){
            $this->errors []= \Lang::get('validation.type');
            return false;
        }

        $data = array_merge($this->item, $this->config);

        $rules = [
            'id' => 'required',
            'reference' => 'required',
            'type' => 'required',
            'attributes.habitaclia-city' => 'required',
            'location.address' => 'required',
            'email' => 'required',
        ];

        return empty($this->errors);

        $validator = \Validator::make($data, $rules, []);
        if ($validator->fails()) {
            $this->errors = $validator->errors()->all();
        }

        return empty($this->errors);
    }

}