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
				// how big is the stack?
				$last = count($playStack->cards) - 1;

				// remove all cards except the one that was played
				$this->cards = array_slice($playStack->cards, 0, $last);
				$this->cards = array_values($this->cards);
				$this->shuffle();

				// unset played cards
				for($i = 0; $i < $last; $i++) {
					unset($playStack->cards[$i]);
				}

				$playStack->cards = array_values($playStack->cards); //recount indexes
				array_push($cards, array_pop($this->cards)); // save card to array
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