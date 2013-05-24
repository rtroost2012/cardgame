$(function() {
	$( "#dialog-message" ).dialog({
		modal: true,
		buttons: {
			Ok: function() {
				$( this ).dialog( "close" );
			}
		}
	});
});

$(function() {
	$( "input[type=submit]" ).button();
});