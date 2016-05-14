;(function($){
	var i = 0;

	$(document).on('click', 'textarea.pre', function(){
		if( i == 0 ){
			i++;
			$(this).focus().select();
			return false;
		}
	});

	$(document).on('keyup', 'textarea.pre', function(){
		$(this).height( 0 );
		$(this).height( this.scrollHeight );
	});

	$(document).ready(function(){
		$('textarea.pre').trigger('keyup');
	});

})(jQuery);