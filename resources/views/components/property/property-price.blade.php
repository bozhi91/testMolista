<div class="price property-price">
    @if($property->desde=='1')
        {{ Lang::get('web/properties.from') }}
    @endif
    {{ price($property->price, $property->infocurrency->toArray()) }}
    <span class="pvp">@include('web.properties.discount-price')
    </span></div>