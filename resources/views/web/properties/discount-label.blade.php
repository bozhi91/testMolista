@if ( !empty($item->price_before) && ($item->price_before > $item->price) && !empty($item->discount_show) )
<div class="discount-label">{{ ceil(($item->price_before - $item->price) * 100 / $item->price_before) * -1 }}%</div>
@endif
