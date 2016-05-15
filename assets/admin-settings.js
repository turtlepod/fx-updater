;(function($){

	$( document ).on( 'click', "textarea.pre", function(){
		if( ! $( this ).hasClass( "done" ) ){
			$( this ).focus().select().addClass( "done" );
			return false;
		}
	});

	$( document ).on( 'keyup', 'textarea.pre', function(){
		$(this).height( 0 );
		$(this).height( this.scrollHeight );
	});

	$( document ).ready(function(){
		$( 'textarea.pre' ).trigger( 'keyup' );
	});

})(jQuery);