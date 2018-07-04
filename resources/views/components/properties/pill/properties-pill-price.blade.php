<div class="price text-italic">
    @if($property->desde=='1')
        {{ Lang::get('web/properties.from') }}
    @endif
    {{ price($item->price, $item->infocurrency->toArray()) }}

        @if($property->mode=='vacationRental')
            /{{ Lang::get('web/properties.week') }}
        @endif
    <span class="pvp">@include('web.properties.discount-price')
    </span></div>