<div class="carousel-caption-subtitle text-nowrap ">
    @if($main_property->desde=='1')
        {{ Lang::get('web/properties.from') }}
    @endif
{{ price($main_property->price, $main_property->infocurrency->toArray()) }}
        @if($main_property->mode=='vacationRental')
            /{{ Lang::get('web/properties.week') }}
        @endif
</div>