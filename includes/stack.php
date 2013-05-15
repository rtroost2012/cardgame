<?PHP
class Stack
{
	protected $cards = array();
	protected $card_symbols = array('&spades;', '&clubs;', '&hearts;', '&diams;');

	public function __construct() {
		//
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

	public function countCards($in = ' ') {
		return ($in == ' ' ? count($this->cards) : count($in)); // return stack cards or count another object
	}

	public function shuffle() {
		return shuffle($this->cards);
	}

	public function clear() { //reset
		$this->cards = array();
	}

	public function debug() {
		echo '<pre>' . var_export($this->cards, true) . '</pre>';
	}
}
