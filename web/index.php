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
	if(!$session->get('started')) { // no game started yet
		// session init
		$session->set('started', true);

		// create deck
		$deck = new Deck();
		$deck->shuffle();

		// create playing stack in the middle with one card to start with
		$playStack = new Stack();
		$playStack->addCards($deck->giveCards());

		// create player and give cards
		$player = new Player();
		$player->addCards($deck->giveCards(7));

		// create opponent and give cards
		$opponent = new Player();
		$opponent->addCards($deck->giveCards(7));

		// save instances to session
		$session->set('playStack_obj', $playStack);
		$session->set('deck_obj', $deck);
		$session->set('player_obj', $player);
		$session->set('opponent_obj', $opponent);
	} else { // game already started
		// set objects to session ones
		$playStack = $session->get('playStack_obj');
		$deck = $session->get('deck_obj');
		$player = $session->get('player_obj');
		$opponent = $session->get('opponent_obj');

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

	//echo '<pre>' . var_export($playStack->getCards(), true) . '</pre>';

	// get last card placed on stack
	$lastCard = $playStack->getCards();
	$lastCard = $lastCard[$playStack->countCards()-1];

	// render cards
	return $app['twig']->render('game.twig', array('playstack_card' => $lastCard,
													'opponentcards' => $opponent->getCards(),
													'playercards' => $player->getCards(),
													'deck_cardsleft' => $deck->countCards()));
});

$app->get('/play/{card}', function($card) use ($app, $session) {
	// init cardgame class for validating move
	$cardGame = new CardGame();

	// render cards
	return $app['twig']->render('game.twig', $cardGame->ValidateMove($session, $card));
});

$app->run();