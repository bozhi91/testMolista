@foreach ($employees as $employee)
	<tr class="property-line">
		<td class="text-nowrap">
			<img src="{{ $employee->image_url }}" class="employee-image-thumb" />
			{{ $employee->name }}
		</td>
		<td>{{ $employee->email }}</td>
		<td class="text-right text-nowrap">
			@if ( Auth::user()->can('property-edit') && Auth::user()->can('employee-edit'))
				{!! Form::button( Lang::get('account/properties.employees.dissociate'), [ 'class'=>'btn btn-default btn-xs dissociate-trigger', 'data-url'=>action('Account\EmployeesController@getDissociate', [ $employee->id, $property_id ]) ]) !!}
			@endif
		</td>
	</tr>
@endforeach
