<?PHP
require_once("includes/stack.php");
require_once("includes/deck.php");
require_once("includes/player.php");

// deck
$deck = new Deck();
$deck->shuffle();

echo '<p>My cards:</p>';

$player1 = new Player();
$player1->addCards($deck->giveCards(32));
$player1->renderCards();	

echo '<p>Deck has currently ' . $deck->countCards() . ' cards left</p>';


