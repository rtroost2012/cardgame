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
use Symfony\Component\HttpFoundation\Session\Session;

// start session
$session = new Session();
$session->start();

// routes
$app->match('/', function(Request $request) use ($app, $session) {
	if(!$session->get('started')) { // no game started yet
		// session init
		$session->set('started', true);

		// create deck
		$deck = new Deck();
		$deck->shuffle();

		// create player and give cards
		$player = new Player();
		$player->addCards($deck->giveCards(7));

		// create opponent and give cards
		$opponent = new Player();
		$opponent->addCards($deck->giveCards(7));

		// save both to session
		$session->set('deck_obj', $deck);
		$session->set('player_obj', $player);
		$session->set('opponent_obj', $opponent);
	} else { // game already started
		// set objects to session ones
		$player = $session->get('player_obj');
		$opponent = $session->get('opponent_obj');
		$deck = $session->get('deck_obj');

		// process commands if needed
		if($request->getMethod() == 'POST') {
			if($request->get('take')) {
				$player->addCards($deck->giveCards()); // add one card to player cards
			} else if($request->get('reset')) {
				$session->invalidate(); // reset
				header("location: /"); // go to main URL for starting a new game
				exit; // don't further execute the code
			}
		}
	}

	// render cards
	return $app['twig']->render('game.twig', array('opponentcards' => $opponent->getCards(),
												   'playercards' => $player->getCards(),
												   'deck_cardsleft' => $deck->countCards()));
});

$app->get('/remove/{card}', function($card) use ($app, $session) {
	// remove card
	$deck = $session->get('deck_obj');
	$player = $session->get('player_obj');
	$opponent = $session->get('opponent_obj');
	$player->removeCards(array($card));

	// render cards
	return $app['twig']->render('game.twig', array('playercards' => $player->getCards(),
												   'opponentcards' => $opponent->getCards(),
												   'deck_cardsleft' => $deck->countCards()));
});

$app->run();