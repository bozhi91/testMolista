@if ( App::environment() == 'production' && env('WHITELABEL_Contromia', false) )
	<!-- Start of Async HubSpot Analytics Code -->
	<script type="text/javascript">
		(function(d,s,i,r) {
		if (d.getElementById(i)){return;}
		var n=d.createElement(s),e=d.getElementsByTagName(s)[0];
		n.id=i;n.src='//js.hs-analytics.net/analytics/'+(Math.ceil(new Date()/r)*r)+'/2322757.js';
		e.parentNode.insertBefore(n, e);
		})(document,"script","hs-analytics",300000);
	</script>
	<!-- End of Async HubSpot Analytics Code -->
@endif