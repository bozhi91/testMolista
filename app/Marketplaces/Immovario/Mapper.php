<?php

namespace App\Marketplaces\Immovario;


class Mapper  extends \App\Marketplaces\Mapper {

    public function map() {
        $item = $this->item;

       /* $fp=fopen("/home/bozhi/Desktop/items.txt","a");
        fwrite($fp,json_encode($item));
        fclose($fp);*/

        $map = [];
        //General Info
        $map['General']['ID'] = $item['id'];
        $map['General']['Reference'] = $item['reference'];
        $map['General']['Objecttype'] = $item['type'];

        $map['General']['Construction'] = "New building*";
        $map['General']['Urbanisation'] = "Urbaniz*";
        $map['General']['Complex'] = "Complex*";

        $map['General']['Place'] = $item['location']['city'];
        $map['General']['Price'] =  $item['price']."€";

        $map['General']['Priceform'] = "price form";
        $map['General']['Onrequest'] = "On request";

        $map['General']['Status'] =  $item['mode'];

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

        $map['Specifications']['Rustic']    = "yes*";//search for rustic in the title(use  $pos= strpos($mystring, $findme); if(pos=== false)->string not found)

        //Seaview? yes/no
        !empty($item['features']['ocean-view'])
            ? $map['Specifications']['SeaView'] = "Yes"
            : $map['Specifications']['SeaView'] = "No";

        $map['Specifications']['CanalView'] = "yes*";//???????

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

        $map['Specifications']['Parking-Places'] = "2*";//?????

        //Heating? Yes/No
        !empty($item['features']['heating'])
            ? $map['Specifications']['Heating'] = "Yes"
            : $map['Specifications']['Heating'] = "No";

        $map['Specifications']['BuiltArea'] = $item['size'];
        $map['Specifications']['LivingArea'] = "---";
        $map['Specifications']['Plot-Size'] = "---";
        $map['Specifications']['Mooring'] = "---";

        //Rooms: out of 10
        $map['Specifications']['Bedrooms'] = $item['bedrooms'];
        $map['Specifications']['Bathrooms'] = $item['toilettes'];
        $map['Specifications']['LivingDiningRooms'] = "--";

        $map['Specifications']['Living-Rooms'] = "--";
        $map['Specifications']['Dining-Rooms'] = "--";
        $map['Specifications']['Kitchens'] = "-----";
        $map['Specifications']['Storage-Rooms'] = "--";
        $map['Specifications']['Laundry-Rooms'] = "--";
        $map['Specifications']['Terraces'] = "-----";

        //Features
        $map['Specifications']['HolidayComplex'] = "-----";//???????????????

        //Air conditioning? Yes/No
        !empty($item['features']['air-conditioning'])
            ? $map['Specifications']['Air-Conditioning'] = "Yes"
            : $map['Specifications']['Air-Conditioning'] = "No";

        $map['Specifications']['Patio'] = "-----";

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


        $map['Specifications']['Bodega'] = "-----";
        $map['Specifications']['Sprinkler-System'] = "-----";

        //Is furnished? Yes/No
        !empty($item['features']['furnished'])
            ? $map['Specifications']['Furnished'] = "Yes"
            : $map['Specifications']['Furnished'] = "No";

        $map['Specifications']['SeperateGuestApartment'] = "-----";

        //Images
        $map['images']['image'] = "-----";

        return $map;
    }


    private function getConstructionType(){

        $type = "";
//http://inmocorona.localhost:8000/feeds/properties/immovario.xml
        if($this->item['newly_build']=='1'){
            $type = "New Construction";
        }
        if($this->item['second_hand']=='1'){
            $type = "Resale";
        }
        else{
            $type = "Unknown type";
            /*	"bank_owned": 0,
	            "private_owned": 1,*/
        }

        return $type;
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