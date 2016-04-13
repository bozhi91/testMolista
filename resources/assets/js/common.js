$.validator.addMethod( "alphanumericHypen", function( value, element ) {
	return this.optional( element ) || /^[\w\-]+$/i.test( value );
}, "Letters, numbers, hypen and underscores only please" );

$(function(){
});
