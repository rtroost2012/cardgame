<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../includes/stack.php';
require_once __DIR__ . '/../includes/deck.php';
require_once __DIR__ . '/../includes/player.php';

$app = new Silex\Application();

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

// start session
$session = new Session();
$session->start();

// routes
$app->get('/', function() use ($app, $session) {
	// deck
	if(!$session->get('started')) { // no game started yet
		// session init
		echo 'new session';
		$session->set('started', true);

		// create deck
		$deck = new Deck();
		$session->set('deck_obj', $deck);
		$deck->shuffle();

		// create player
		$player = new Player();
		$session->set('player_obj', $player);
		$player->addCards($deck->giveCards(7));

		// render cards
		echo '<p>My cards:</p>';
		$player->renderCards();	
	} else { // game already started
		$player = $session->get('player_obj');
		$player->renderCards();	
		$session->invalidate();
		//echo '<p>Deck has currently ' . $deck->countCards() . ' cards left</p>';
	}

	return new Response();
});

// uitvoeren
$app->run();