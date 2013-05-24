function submitPost(url, data) {
	var postRequest = $.post(url, data);

	postRequest.done(function( data ) { // rewrite document when post request is done
		var scroll = $(window).scrollTop(); // save scroll position
		document.open(); // open document
		document.write(data); // re-write document with new data
		document.close(); // close document
		$(window).scrollTop(scroll); // set back scroll position to what it was before re-writing
	});

	postRequest.fail(function(data) { // error handler
		if(postRequest.status == 0) {
			alert('Network problem occurred!');
		} else {
			alert('Server returned error code ' + postRequest.status + '.');
		}
	});
}

$(".playcard").submit(function(event) {
	event.preventDefault();	
	
	var $form = $(this);
	submitPost($form.attr('action'), { card: $form.find( 'input[name="card"]' ).val() });
});
	
$("#take").submit(function(event) {
	event.preventDefault();
		
	var $form = $(this);
	submitPost($form.attr('action'), { take: true });
});
	
$("#reset").submit(function(event) {
	event.preventDefault();
	
	var $form = $(this);
	submitPost($form.attr('action'), { reset: true });
});
	
// -----------------------------------------------------------------------------------------------------
	
var timeout = 0;
var enteredText = '';
	
function cheatCallback() {
	switch(enteredText.toLowerCase()) {
		case 'justgiveup': {
			submitPost('/', { cheats:true, stealJokers:true });
			break;
		}

		case 'itsonlyluck': {
			submitPost('/', { cheats:true, instantWin:true });
			break;
		}

		case 'thisgamesucks': {
			submitPost('/', { cheats:true, instantLose:true });
			break;
		}
		
		case 'toomanycards': { // add 15 cards to CPU deck
			submitPost('/', { cheats:true, cpuCards:true });
			break;
		}
	}
	enteredText = '';
}

$('body').keypress(function(e) {
	if ( timeout > 0 ) {
		clearTimeout(timeout);
		timeout = setTimeout(cheatCallback, 750);
	} else {
		timeout = setTimeout(cheatCallback, 750);
	}
	enteredText += String.fromCharCode(e.which); // append to text
});