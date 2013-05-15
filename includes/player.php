<?PHP
class Player extends Stack
{
	public function addCard($cards) {
		$this->cards = array_merge($this->cards, $cards); // add cards to array
	}
}