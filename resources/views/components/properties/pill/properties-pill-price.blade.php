<div class="price text-italic">
    @if($property->desde=='1')
        {{ Lang::get('web/properties.from') }}
    @endif
    {{ price($item->price, $item->infocurrency->toArray()) }}
    <span class="pvp">@include('web.properties.discount-price')
    </span></div>