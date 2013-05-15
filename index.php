<?PHP
require_once("includes/stack.php");
require_once("includes/deck.php");
require_once("includes/player.php");

// deck
$deck = new Deck();
$deck->shuffle();

echo 'Deck currently has the following cards:';
$deck->debug();

/*
echo 'There are ' . $deck->countCards() . ' cards left in the deck.<br>';

echo 'Deck gave the card: ' . var_export($deck->giveCard(), true);
$deck->debug();

echo 'There are ' . $deck->countCards() . ' cards left in the deck.<br>';

echo 'Deck gave the cards: ' . var_export($deck->giveCard(2), true);
$deck->debug();

echo 'There are ' . $deck->countCards() . ' cards left in the deck.';
*/

$player1 = new Player();

echo 'Adding first card ...';
$player1->addCards($deck->giveCards(7));
$player1->debug();	

echo 'Deck has currently ' . $deck->countCards() . ' cards left:';
$deck->debug();

echo 'Removing cards (&diams;K and J*)...';
$player1->removeCards(array('&diams;K', 'J*'));
$player1->debug();