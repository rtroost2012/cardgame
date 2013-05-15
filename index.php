<?php
class Deck
{
	protected $cards = array();
	private $card_symbols = array('&spades;', '&clubs;', '&hearts;', '&diams;');

	public function __construct($jokers = false) {
		$this->createCards($jokers);
	}

	private function createCards($jokers) {
		foreach ($this->card_symbols as $symbol) {
			for($i = 1; $i <= 13; $i++) { // 13 cards in a row
				if($i == 1) { // first card of row
					array_push($this->cards, $symbol . 'A');  // Ace
				} else if($i == 11) { // push last 3 cards
					array_push($this->cards, $symbol . 'J'); // Jack
					array_push($this->cards, $symbol . 'Q'); // Queen
					array_push($this->cards, $symbol . 'K'); // King
					break;
				} else { // another card
					array_push($this->cards, $symbol . $i); // No special card
				}
			}
		}

		// jokers
		if($jokers) {
			array_push($this->cards, 'J*');
			array_push($this->cards, 'J*');
		}
	}

	public function giveCard($amount = 1) {
		$cards = array();
		
		for($i = 0; $i < $amount; $i++) { // loop through amount of cards to add
			$card = array_pop($this->cards); // get the last card in the deck
			
			if($card) {
				array_push($cards, $card); // save card to array
			} else {
				// deck ran out of crads
			}
		}
		
		return $cards;
		//return ($card ? $card : 'No cards left in the game!');
	}

	public function countCards() {
		return count($this->cards);
	}

	public function shuffle() {
		return shuffle($this->cards);
	}

	public function clear() {
		$this->cards = array();
	}

	public function debug() {
		echo '<pre>' . var_export($this->cards, true) . '</pre>';
	}
}

class Player extends Deck 
{
	protected $my_cards = array();

	public function addCards($cards) {
		array_push($my_cards, $cards); 
	}
}

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