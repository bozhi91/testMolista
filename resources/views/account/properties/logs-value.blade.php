<?php
	switch ( $field )
	{
		case 'type':
		case 'mode':
			if ( !empty($values[$field]) ) 
			echo trans("web/properties.{$field}.{$values[$field]}");
			break;
		case 'price':
			if ( !empty($values['price']) ) 
			{
				$currency = \App\Property::getCurrencyOption( empty($values['currency']) ? @$property->currency : $values['currency'] );
				echo price($values['price'], $property->infocurrency->toArray());
			}
			break;
		case 'size':
			if ( !empty($values['size']) ) 
			{
				$size_unit = \App\Property::getSizeUnitOption( empty($values['size_unit']) ? @$property->size_unit : $values['size_unit'] );
				echo number_format($values['size'], 0, ',', '.') . ($size_unit ? " {$size_unit['symbol']}" : '');
			}
			break;
		case 'newly_build':
		case 'second_hand':
		case 'highlighted':
		case 'enabled':
		case 'ec_pending':
			if ( isset($values[$field]) && strlen($values[$field]) )
			{
				echo $values[$field] ? trans('general.yes') : trans('general.no');
			}
			break;
		case 'country_id':
		case 'territory_id':
		case 'state_id':
		case 'city_id':
			$item = false;
			if ( !empty($values[$field]) ) 
			{
				switch ( $field )
				{
					case 'country_id':
						$item = \App\Models\Geography\Country::withTranslations()->find($values[$field]);
						break;
					case 'territory_id':
						$item = \App\Models\Geography\Territory::find($values[$field]);
						break;
					case 'state_id':
						$item = \App\Models\Geography\State::find($values[$field]);
						break;
					case 'city_id':
						$item = \App\Models\Geography\City::find($values[$field]);
						break;
				}
			}
			echo @$item->name;
			break;
		default:
			echo @$values[$field];
	}
?>