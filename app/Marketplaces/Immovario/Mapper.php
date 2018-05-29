<?php

namespace App\Marketplaces\Immovario;


class Mapper  extends \App\Marketplaces\Mapper {

    public function map() {

        $item = $this->item;
        $map = [];

        //General Info
        $map['General']['ID'] = $item['id'];
        $map['General']['Reference']    = $item['reference'];
        $map['General']['Objecttype']   = $item['type'];
        $map['General']['Construction'] = $this->getConstructionType();
        $map['General']['Urbanisation'] = $item['location']['district'];
        $map['General']['Complex']   = "Complex???";
        $map['General']['Place']     = $item['location']['city'];
        $map['General']['Price']     =  $item['price']."€";
        $map['General']['Priceform'] = "price form???";
        $map['General']['Onrequest'] = "On request???";
        $map['General']['Status']    =  $item['mode'];

        //Description
        $map['Description']['DescriptionCatalan'] = $item['description']['ca'];
        $map['Description']['DescriptionSpanish'] = $item['description']['es'];
        $map['Description']['DescriptionEnglish'] = $item['description']['en'];
        $map['Description']['DescriptionFrench'] = $item['description']['fr'];
        $map['Description']['DescriptionGerman'] = $item['description']['de'];
        $map['Description']['DescriptionItalian'] = $item['description']['it'];
        $map['Description']['DescriptionDutch'] = $item['description']['nl'];
        $map['Description']['DescriptionSwedish'] = $item['description']['sv'];
        $map['Description']['DescriptionRussian'] = $item['description']['ru'];

        //Specifications
        $map['Specifications']['Construction'] = $this->getConstructionType();
        $map['Specifications']['Rustic']  = $this->getFeature("rustic");

        //Seaview? yes/no
        !empty($item['features']['ocean-view'])
            ? $map['Specifications']['SeaView'] = "Yes"
            : $map['Specifications']['SeaView'] = "No";

        $map['Specifications']['CanalView'] = "";

        //Garden? yes/no
        !empty($item['features']['garden'])
            ? $map['Specifications']['Garden'] = "Yes"
            : $map['Specifications']['Garden'] = "No";

        //Pool? yes/no
        !empty($item['features']['community-pool'])
            ? $map['Specifications']['Swimming-Pool'] = "Yes"
            : $map['Specifications']['Swimming-Pool'] = "No";

        //Garage? yes/no
        !empty($item['features']['garage'])
            ? $map['Specifications']['Garage'] = "Yes"
            : $map['Specifications']['Garage'] = "No";

        //Num of garages between 0 and 10
        $map['Specifications']['Parking-Places'] = "";

        //Heating? Yes/No
        !empty($item['features']['heating'])
            ? $map['Specifications']['Heating'] = "Yes"
            : $map['Specifications']['Heating'] = "No";

        $map['Specifications']['BuiltArea'] = $item['size_real'];
        $map['Specifications']['LivingArea'] = $item['size'];
        $map['Specifications']['Plot-Size'] = $item['covered_area'];
        $map['Specifications']['Mooring'] = "";

        //Rooms: out of 10
        $map['Specifications']['Bedrooms'] = $item['bedrooms'];
        $map['Specifications']['Bathrooms'] = $item['baths'];
        $map['Specifications']['LivingDiningRooms'] = "";
        $map['Specifications']['Living-Rooms'] = "";
        $map['Specifications']['Dining-Rooms'] = "";
        $map['Specifications']['Kitchens'] = "";
        $map['Specifications']['Storage-Rooms'] = "";
        $map['Specifications']['Laundry-Rooms'] = "";
        $map['Specifications']['Terraces'] = "";

        //Features
        $map['Specifications']['HolidayComplex'] = "";

        //Air conditioning? Yes/No
        !empty($item['features']['air-conditioning'])
            ? $map['Specifications']['Air-Conditioning'] = "Yes"
            : $map['Specifications']['Air-Conditioning'] = "No";

        $map['Specifications']['Patio'] = $this->getFeature("patio");

        //Elevator/Lift? yes/no
        !empty($item['features']['elevator'])
            ? $map['Specifications']['Lift'] = "Yes"
            : $map['Specifications']['Lift'] = "No";

        //Fireplace/chimney
        !empty($item['features']['chimney'])
            ? $map['Specifications']['OpenFirePlace'] = "Yes"
            : $map['Specifications']['OpenFirePlace'] = "No";

        //Garden?
        !empty($item['features']['garden'])
            ? $map['Specifications']['CommunityGarden'] = "Yes"
            : $map['Specifications']['CommunityGarden'] = "No";

        //Alarm
        !empty($item['features']['alarm'])
            ? $map['Specifications']['Alarm'] = "Yes"
            : $map['Specifications']['Alarm'] = "No";

        $map['Specifications']['Bodega'] = $this->getFeature("bodega");
        $map['Specifications']['Sprinkler-System'] = "???";

        //Is furnished? Yes/No
        !empty($item['features']['furnished'])
            ? $map['Specifications']['Furnished'] = "Yes"
            : $map['Specifications']['Furnished'] = "No";

        $map['Specifications']['SeperateGuestApartment'] = "";

        //Images
        $map['images'] = $this->getImages();

        return $map;
    }

    private function getFeature($feature){
        $pos = strpos($this->item['description']['es'], $feature);
        if($pos=== false){
            return "No";
        }
        return "Yes";
    }

    private function getConstructionType(){
        $type = "";
        if($this->item['newly_build']=='1'){
            $type = "New Construction";
        }
        if($this->item['second_hand']=='1'){
            $type = "Resale";
        }
        if($this->item['bank_owned']=='1'){
            $type = "Bank Owned";
        }
        if($this->item['private_owned']=='1'){
            $type = "Private Owned";
        }
        else{
            $type = "";
        }
        return $type;
    }

        protected function getImages(){
        $pictures = [];

        foreach ($this->item['images'] as $i => $image)
        {
            $pictures ['image@number='.($i+1)]= [
                '#image' => $image,
                '#alttext' => "text"
            ];
        }
        return $pictures;
    }

    /**
     * @return boolean
     */
    public function valid() {

        if (in_array($this->item['type'], ['garage', 'plot'])){
            $this->errors []= \Lang::get('validation.type');
            return false;
        }
        
        $rules = [
            'id' => 'required',
            'reference' => 'required',
            'type' => 'required',
            'location.city' => 'required'
        ];

        return empty($this->errors);

        $validator = \Validator::make($data, $rules, []);
        if ($validator->fails()) {
            $this->errors = $validator->errors()->all();
        }

        return empty($this->errors);
    }

}