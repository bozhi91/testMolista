<div class="form-group error-container">
	{!! Form::label(null, 'CC') !!}
	<div class="form-control labels-email-input" data-name="cc[]">
		<i class="fa fa-plus-square" aria-hidden="true"></i>
		<ul class="list-inline emails-list"></ul>
	</div>
</div>
<div class="form-group error-container">
	{!! Form::label(null, 'BCC') !!}
	<div class="form-control labels-email-input" data-name="bcc[]">
		<i class="fa fa-plus-square" aria-hidden="true"></i>
		<ul class="list-inline emails-list"></ul>
	</div>
</div>

<script type="text/javascript">
	ready_callbacks.push(function() {
	});
</script>
