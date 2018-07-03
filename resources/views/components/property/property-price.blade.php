<div class="price property-price">
    @if($property->desde=='1')
        {{ Lang::get('web/properties.from') }}
    @endif
    {{ price($property->price, $property->infocurrency->toArray()) }}
        @if($property->mode=='vacationRental')
            /{{ Lang::get('web/properties.week') }}
        @endif
    <span class="pvp">@include('web.properties.discount-price')
    </span></div>