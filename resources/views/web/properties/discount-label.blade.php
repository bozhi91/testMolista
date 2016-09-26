@if ( !empty($item->price_before) && ($item->price_before > $item->price) )
<div class="discount-label">{{ ceil(($item->price - $item->price_before)/100) }}%</div>
@endif
