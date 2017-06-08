// nonce needs to be added to the header of this ajax request
(function($){

	$(document).ready(function(){
		$.ajax({
			url: 'http://blog.justinestrada.com/wp-json/cupcake-voting/v1/votes',
			method: 'POST',
			beforeSend: function(xhr) {
				xhr.setRequestHeader( 'X-WP-Nonce', cupcake_voting_data.nonce );
			},
			data: {
				id: 89
			}
		});
	});

	console.log( 'Nonce is: ' + cupcake_voting_data.nonce);
	
})(jQuery);