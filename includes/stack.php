<?PHP
class Stack
{
	protected $cards = array();
	protected $card_symbols = array('S_', 'C_', 'H_', 'D_'); // spades, clubs, hearts, diamonds

	public function __construct() {
		// 
	}

	public function giveCards($playStack = NULL, $amount = 1) {
		$cards = array();

		for($i = 0; $i < $amount; $i++) { // loop through amount of cards to add
			$card = array_pop($this->cards); // get the last card in the deck

			if($card) {
				array_push($cards, $card); // save card to array
			} else {
				// how big is the play stack?
				$totalCards = count($playStack->cards) - 1;

				if($totalCards != 0) { // in a rare occasion the player could be holding many cards and the stack can't be reset because it's already empty
					// remove all cards except the one that was played
					$this->cards = array_slice($playStack->cards, 0, $totalCards);
					$this->shuffle();

					// unset played cards
					for($i = 0; $i < $totalCards; $i++) {
						unset($playStack->cards[$i]);
					}

					$playStack->cards = array_values($playStack->cards); //recount indexes
					array_push($cards, array_pop($this->cards)); // save card to array
				}
			}
		}
		return $cards;
	}

	public function addCards(array $cards) {
		$this->cards = array_merge($this->cards, $cards); // add cards to array
	}

	public function countCards() {
		return count($this->cards); // return amount of cards
	}

	public function getCards() {
		return $this->cards; // return cards
	}

	public function shuffle() {
		return shuffle($this->cards); // shuffle cards
	}

	public function clear() { 
		$this->cards = array(); // reset deck
	}
}