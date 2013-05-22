<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../includes/stack.php';
require_once __DIR__ . '/../includes/deck.php';
require_once __DIR__ . '/../includes/player.php';
require_once __DIR__ . '/../includes/CardGame.php';

$app = new Silex\Application();

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

// start session
$session = new Session();
$session->start();

// routes
$app->match('/', function(Request $request) use ($app, $session) {
	// init cardgame class
	$cardGame = new CardGame($app, $session, $request);

	// render cards
	return $cardGame->renderCards($app);
});

$app->post('/play', function(Request $request) use ($app, $session) {
	// init cardgame class
	$cardGame = new CardGame($app, $session, $request);

	// validate player move
	$playerMove_result = $cardGame->ValidateMove('player', $request->get('card')); // validate player move

	if($playerMove_result['validMove']) { // player made a valid move
		$computerMove_result = $cardGame->computerMove(); // make the computer move
	}

	$renderResult = (isset($computerMove_result) ? $computerMove_result : $playerMove_result); // always display latest move so if the PC has made one then we will show that one
	return $cardGame->renderCards($app, $renderResult); // render cards
});

$app->run();