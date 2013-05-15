<?PHP
class Player extends Deck
{
	protected $my_cards = array();

	public function addCard($amount) {
		$cards = $this->giveCard($amount); // save cards
		array_push($my_cards, $cards); // add cards to array
	}

	public function cardTotal() {
		return $this->countCards($my_cards);
	}
}