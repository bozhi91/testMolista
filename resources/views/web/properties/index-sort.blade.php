@if ( !empty($search_data['sort_options']) )
	<div class="results-sort-trigger">
		<div class="btn-group" role="group">
			<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				@if ( Input::get('order') && array_key_exists(Input::get('order'), $search_data['sort_options']) )
					{{ $search_data['sort_options'][Input::get('order')] }}
				@else
					{{ Lang::get('web/search.sort.title') }}
				@endif
				<span class="caret"></span>
			</button>
			<ul class="dropdown-menu">
				@foreach ($search_data['sort_options'] as $order_key => $order_title)
					@if ( $order_key != Input::get('order') )
						<li><a href="{!! Request::fullUrlWithQuery([ 'order'=>$order_key ]) !!}">{{ $order_title }}</a></li>
					@endif
				@endforeach
			</ul>
		</div>
	</div>
@endif