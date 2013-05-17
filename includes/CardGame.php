<?PHP
class CardGame
{
	public function __construct() {
		// 
	}

	public function ValidateMove($session, $card) {
		// get session objects
		$playStack = $session->get('playStack_obj');
		$deck = $session->get('deck_obj');
		$player = $session->get('player_obj');
		$opponent = $session->get('opponent_obj');

		// get last card placed on stack
		$lastCard = $playStack->getCards();
		$lastCard = $lastCard[$playStack->countCards()-1];

		if(in_array($card, $player->getCards())) { // check if player has the card
			$type_stack = substr($lastCard, 0, 2); // card type on the playing stack
			$type_player = substr($card, 0, 2); // card type the player is trying to play
			$number_stack = substr($lastCard, 2, strlen($lastCard) - 2); // card number on the playing stack
			$number_player = substr($card, 2, strlen($card) - 2); // card number the player is trying to play

			//echo 'Number on stack: ' . $number_stack . ' \\ Number im trying to play: ' . $number_player;

			if($type_player == $type_stack || $number_player == $number_stack || $type_player == "JK" || $type_stack == "JK" ) { // joker, same type or same number
				$playStack->addCards(array($card)); // add card to playing stack
				$player->removeCards(array($card)); // remove card from player hand

				if($type_player == "JK") { // used a joker
					$opponent->addCards($deck->giveCards(5));
				} else if($number_player == "2") { // opponent has to take two cards
					$opponent->addCards($deck->giveCards(2));
				}
			} else {
				echo '<p>Please play a card with the same type or number!</p>';
			}

			//echo '<pre>playingStack = ' . var_export($playStack->getCards(), true) . '</pre>';
		}

		// get last card placed on stack
		$lastCard = $playStack->getCards();
		$lastCard = $lastCard[$playStack->countCards()-1];

		// render cards
		return array('playstack_card' => $lastCard,
					'opponentcards' => $opponent->getCards(),
					'playercards' => $player->getCards(),
					'deck_cardsleft' => $deck->countCards());
	}
}