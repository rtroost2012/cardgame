<?PHP
class Stack
{
	protected $cards = array();
	protected $card_symbols = array('S_', 'C_', 'H_', 'D_'); // spades, clubs, hearts, diamonds

	public function __construct() {
		// 
	}

	public function giveCards($amount = 1) {
		$cards = array();
		
		for($i = 0; $i < $amount; $i++) { // loop through amount of cards to add
			$card = array_pop($this->cards); // get the last card in the deck

			if($card) {
				array_push($cards, $card); // save card to array
			} else {
				break; // deck ran out of cards
			}
		}
		return $cards;
	}

	public function addCards(array $cards) {
		$this->cards = array_merge($this->cards, $cards); // add cards to array
		// echo '<pre>' . var_export($this->cards, true) . '</pre>';
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