<?PHP
require_once("includes/deck.php");
require_once("includes/player.php");

// deck
$deck = new Deck(true);
$deck->shuffle();
$deck->debug();

echo 'There are ' . $deck->countCards() . ' cards left in the deck.<br>';

echo 'Deck gave the card: ' . var_export($deck->giveCard(), true);
$deck->debug();

echo 'There are ' . $deck->countCards() . ' cards left in the deck.<br>';

echo 'Deck gave the cards: ' . var_export($deck->giveCard(2), true);
$deck->debug();

echo 'There are ' . $deck->countCards() . ' cards left in the deck.';
