@if ( $logs->count() > 0 )
	@foreach ($property->logs as $key=>$log)
		<tr>
			<td data-date="{{ $log->created_at->timestamp }}">{{ $log->created_at->format("d/m/Y H:i" )}}</td>
			<td>{{ $log->user->name ? $log->user->name : Lang::get('account/properties.logs.responsible.unknown') }}</td>
			<td>{{ Lang::get("account/properties.logs.type.{$log->type}") }}</td>
			<td class="text-center">
				<a href="#popup-log-{{$log->id}}" class="btn btn-xs btn-default popup-log-trigger">{{ Lang::get('general.view') }}</a>
				<div id="popup-log-{{$log->id}}" class="app-popup-block-white mfp-hide">
					Log details
				</div>
			</td>
		</tr>
	@endforeach
@endif
