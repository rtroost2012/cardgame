<?PHP
class Player
{
	protected $my_cards = array();

	public function addCards($cards) {
		array_push($my_cards, $cards); 
	}
}