@if (count($items)<1)
    <p>No hay notas.</p>
@else
	<table class="table table-hover">
		<tbody>
		    @foreach ($items as $index => $item)
				<tr class="{{ !$index ? 'success' : '' }}">
                    <td width="20%">
                        {{ $item->created_at->format('d-m-Y H:i') }}
                        <br>
                        <small>{{ $item->user ? $item->user->email : 'Anónimo' }}</small>
                    </td>
					<td class="no-wrap">
						{!! linkify(nl2br($item->comment)) !!}
					</td>
				</tr>
		    @endforeach
		</tbody>
	</table>

@endif
