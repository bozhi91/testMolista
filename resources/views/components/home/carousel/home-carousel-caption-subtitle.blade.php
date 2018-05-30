<div class="carousel-caption-subtitle text-nowrap ">
    @if($property->desde=='1')
        {{ Lang::get('web/properties.from') }}
    @endif
{{ price($main_property->price, $main_property->infocurrency->toArray(), $main_property) }} </div>