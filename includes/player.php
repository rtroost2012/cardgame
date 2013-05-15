<?PHP
class Player extends Stack
{
	public function __construct() {
		// 
	}

	public function addCards($cards) {
		$this->cards = array_merge($this->cards, $cards); // add cards to array
	}

	public function removeCards($cards = array(), $allOccurrences = false) {
		foreach($cards as $card) {
			$index = array_search($card, $this->cards); // does the card array contain the card we are looking for?

			if($allOccurrences) {
				while($index !== false) { // item exists
					unset($this->cards[$index]); // remove card from array
					$this->cards = array_values($this->cards); // re-calc indexes

					// are there more results?
					$index = array_search($card, $this->cards);
				}
			} else {
				if($index !== false) { // item exists
					unset($this->cards[$index]); // remove card from array
					$this->cards = array_values($this->cards); // re-calc indexes
				}
			}
		}
	}

	public function renderCards() {
		foreach ($this->cards as $card) {
			// retrieve card number and card prefix
			$startIndex = strpos($card, '_') + 1;
			$endIndex = strlen($card) - $startIndex;
			$cardPrefix = substr($card, 0, $startIndex);
			$cardNumber = substr($card, $startIndex, strlen($card) - $startIndex);

			echo "<img src='images/" . $cardPrefix . $cardNumber . ".gif' alt='card'/>"; // twig render
		}
	}
}