<?PHP
class Deck extends Stack
{
	public function __construct() {
		$this->createCards();
	}

	private function createCards() {
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
		array_push($this->cards, 'J*');
		array_push($this->cards, 'J*');
	}
}