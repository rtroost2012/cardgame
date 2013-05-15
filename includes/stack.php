<?PHP
class Stack
{
	protected $cards = array();
	protected $card_symbols = array('&spades;', '&clubs;', '&hearts;', '&diams;');

	public function __construct() {
		// 
	}

	public function giveCards($amount = 1) {
		$cards = array();
		
		for($i = 0; $i < $amount; $i++) { // loop through amount of cards to add
			$card = array_pop($this->cards); // get the last card in the deck

			if($card) {
				array_push($cards, $card); // save card to array
			} /* else {
				// deck ran out of cards
			}*/
		}

		return $cards;
		//return ($card ? $card : 'No cards left in the game!');
	}

	public function countCards() {
		return count($this->cards); // return amount of cards
	}

	public function shuffle() {
		return shuffle($this->cards);
	}

	public function clear() { 
		$this->cards = array(); // reset deck
	}

	public function debug() {
		echo '<pre>' . var_export($this->cards, true) . '</pre>';
	}
}