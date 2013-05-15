<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../includes/stack.php';
require_once __DIR__ . '/../includes/deck.php';
require_once __DIR__ . '/../includes/player.php';

$app = new Silex\Application();

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

// routes
$app->get('/', function() {
	// deck
	$deck = new Deck();
	$deck->shuffle();

	echo '<p>My cards:</p>';

	$player1 = new Player();
	$player1->addCards($deck->giveCards(7));
	$player1->renderCards();	

	echo '<p>Deck has currently ' . $deck->countCards() . ' cards left</p>';
});

// uitvoeren
$app->run();