<li class="handler ui-sortable-handle {{ empty($warning_orientation) ? (empty($warning_size) ? '' : 'handler-orange') : 'handler-red' }}">
	<div class="property-image-container">
		<a href="{{ $image_url }}" target="_blank" class="thumb" style="background-image: url('{{ $image_url }}')"></a>

        <?php
			$plan = DB::table('sites')
				->select('plan_id')
				->where('id',session("SiteSetup")['site_id'])
				->first();

			if($plan->plan_id=='2' || $plan->plan_id=='3' || $plan->plan_id=='7' ){
                if(@strstr($image->image_url, "watermark")){
                    echo "<br>
						<input type='checkbox' name='img_water[]' value='$image_id'>Restaurar imagen";
				}
			}
        ?>

	</div>
	<div class="options text-right">
		@if ( !empty($warning_orientation) )
			<span class="pull-left glyphicon glyphicon-question-sign info-label thumb-has-tooltip cursor-pointer" title="{{ print_js_string( Lang::get('web/properties.images.warning.orientation') ) }}"></span>
		@elseif ( !empty($warning_size) )
			<span class="pull-left glyphicon glyphicon-question-sign info-label thumb-has-tooltip cursor-pointer" title="{{ print_js_string( Lang::get('web/properties.images.warning.size') ) }}"></span>
		@endif
		<span class="default-label pull-left">{{ Lang::get('web/properties.images.label.default') }}</span>
		<input name="images[]" type="hidden" value="{{ $image_id }}">
		{!! Form::hidden("rotation[$image_id]", '', ['class' => 'rotation-hidden-input']) !!}
		<a href="#" class="image-rotate-trigger"><span class="glyphicon glyphicon-repeat" aria-hidden="true"></span></a>
		<a href="#" class="image-delete-trigger"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a>
	</div>
</li>
