<div class="property-energy-certification">
	@if ( $property->ec || $property->ec_pending )
		<div class="energy-certification">
			<span class="energy-certification-popover-trigger text-bold cursor-pointer">
				<i class="fa fa-info-circle" aria-hidden="true"></i>
				&nbsp;{{ Lang::get('account/properties.energy.certificate') }}:
			</span>
			&nbsp;
			@if ( $property->ec_pending )
				{{ Lang::get('account/properties.energy.certificate.pending') }}</span>
			@else
				<img src="{{ asset("images/properties/ec-{$property->ec}.png") }}" alt="{{ $property->ec }}" class="energy-certification-icon" />
			@endif
		</div>
		<div class="energy-certification-popover-content hide">
			<table>
				<tr>
					<td class="hidden-xs"><img src="{{ asset("images/properties/ec-all.png") }}" alt="{{ Lang::get('account/properties.energy.certificate') }}" /></td>
					<td class="text">{!! Lang::get('web/properties.energy.certificate.help') !!}
				</tr>
			</table>
		</div>
	@endif
</div>