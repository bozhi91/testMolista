<?php
	switch ($item->status) 
	{
		case 'pending':
		case 'processing':
			$item_class = 'info';
			break;

		case 'completed':
			$item_class = 'success';
			break;

		default:
			$item_class = 'danger';
			break;
	}
?>
<tr class="{{ $item_class }}">
	<td>{{ $item->created_at->format('d/m/Y H:i:s') }}</td>
	<td class="text-nowrap">{{ $item->version }}</td>
	<td class="text-center">
		<a href="{{ action('Account\Properties\ImportsController@getDownload', $item->id) }}" target="_blank">
			<span class="glyphicon glyphicon-download" aria-hidden="true"></span>
		</a>
	</td>
	<td class="text-nowrap">
		@if ($item->status == 'pending' || $item->status == 'processing')
			<a href="#" class="reload-log"><span class="glyphicon glyphicon glyphicon-refresh"></span></a>
			&nbsp;
			{{ Lang::get('account/logs.response.processing') }}
		@elseif ($item->status == 'completed')
			<span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
			&nbsp;
			{{ Lang::get('account/logs.response.success') }}
		@else
			<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
			&nbsp;
			{{ Lang::get('account/logs.response.error') }}
		@endif
		@if ( $item->result )
			&nbsp;
			<a href="#" data-toggle="modal" data-target="#modal-{{ $item->id }}" aria-hidden="true" title="{{ Lang::get('account/logs.response.details') }}">
				<sup>
					<span class="glyphicon glyphicon-info-sign error-icon"></span>
				</sup>
			</a>
		@endif
	</td>
</tr>