
@if(!empty($property->html_property))
    <div class="row" style="margin-top:100px;">
        <div>
            <div class="property-pill" style=" border: 1px solid #555; padding:20px;">
                {!! $property->html_property !!}
            </div>
        </div>
    </div>
@endif