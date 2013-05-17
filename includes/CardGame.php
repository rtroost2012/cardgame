<?PHP
class CardGame
{
	private $playStack;
	private $deck;
	private $players;

	public function __construct($session) {
		// init session vars
		$this->playStack = $session->get('playStack_obj');
		$this->deck = $session->get('deck_obj');
		$this->players = array('player' => $session->get('player_obj'),
								'opponent' => $session->get('opponent_obj'));
	}

	private function array_find($needle, $haystack) { // search for a partial value in an array and return full value
	   foreach ($haystack as $item) {
	      if(strpos($item, $needle) !== FALSE) {
	         return $item;
	         break;
	      }
	   }
	   return NULL;
	}

	private function lastOnStack() {
		// get last card placed on stack
		$lastCard = $this->playStack->getCards();
		return $lastCard[$this->playStack->countCards()-1];
	}

	public function ValidateMove($inputPlayer, $card) {
		if(!isset($this->players[$inputPlayer])) // valid player?
			die('ValidateMove(): Invalid player ($inputPlayer = ' . $inputPlayer . ')');

		// not a valid move yet
		$validMove = false;

		//echo '<br>Validating move for: ' . $testplayer;

		// get last card placed on stack
		$lastCard = $this->lastOnStack();

		if(in_array($card, $this->players[$inputPlayer]->getCards())) { // check if player has the card
			$type_stack = substr($lastCard, 0, 2); // card type on the playing stack
			$type_player = substr($card, 0, 2); // card type the player is trying to play
			$number_stack = substr($lastCard, 2, strlen($lastCard) - 2); // card number on the playing stack
			$number_player = substr($card, 2, strlen($card) - 2); // card number the player is trying to play

			//echo 'Number on stack: ' . $number_stack . ' \\ Number im trying to play: ' . $number_player;

			if($type_player == $type_stack || $number_player == $number_stack || $type_player == "JK" || $type_stack == "JK" ) { // joker, same type or same number
				$this->playStack->addCards(array($card)); // add card to playing stack
				$this->players[$inputPlayer]->removeCards(array($card)); // remove card from player hand

				if($type_player == "JK") { // used a joker
					$targetPlayer = ($inputPlayer == 'opponent' ? 'player' : 'opponent'); // which player to give the card
					$this->players[$targetPlayer]->addCards($this->deck->giveCards(5));
				} else if($number_player == "2") { // opponent has to take two cards
					$targetPlayer = ($inputPlayer == 'opponent' ? 'player' : 'opponent'); // which player to give the card
					$this->players[$targetPlayer]->addCards($this->deck->giveCards(2));
				}

				$validMove = true; // player made a valid move
			} else {
				echo '<p>Please play a card with the same type or number!</p>';
			}
		}

		// return info about move validation and rendering
		$validateInfo = array('playstack_card' => $this->lastOnStack(),
					'opponentcards' => $this->players['opponent']->getCards(),
					'playercards' => $this->players['player']->getCards(),
					'deck_cardsleft' => $this->deck->countCards());

		return array('validMove' => $validMove, 
					'validateInfo' => $validateInfo);
	}

	public function ComputerMove() {
		$computer_cards = $this->players['opponent']->getCards(); // get computer cards
		$lastCard = $this->lastOnStack();

		$type_stack = substr($lastCard, 0, 2); // card type on the playing stack
		$number_stack = substr($lastCard, 2, strlen($lastCard) - 2); // card number on the playing stack
	
		$available_card_type = $this->array_find($type_stack, $computer_cards); // do we have a card with the same type?
		$available_card_number = $this->array_find($number_stack, $computer_cards); // do we have a card with the same type?

		$move_result = NULL;

		if(!$available_card_type && !$available_card_number) { // no card with the same type or number
			$this->players['opponent']->addCards($this->deck->giveCards());
			echo 'grabbing card';
		} else { // play the card
			$playcard = '';

			if($available_card_type != '') { // not the same type but the same number
				//echo 'valid card type:' . $available_card_type;
				$playcard =  $available_card_type;
				$move_result = $this->ValidateMove('opponent', $available_card_type);
			} else { // same number
				//echo 'valid card number:' . $available_card_number;
				$playcard =  $available_card_number;
				$move_result = $this->ValidateMove('opponent', $available_card_number);
			}
		}

		if($move_result == NULL) { // pc is grabbing card
			$validateInfo = array('playstack_card' => $this->lastOnStack(),
						'opponentcards' => $this->players['opponent']->getCards(),
						'playercards' => $this->players['player']->getCards(),
						'deck_cardsleft' => $this->deck->countCards());

			//echo 'move_result  = <pre>' . var_export($move_result, true) . '</pre>';

			return array('validMove' => true, 
						'validateInfo' => $validateInfo);

			//echo ' no move possible';		
		}

		return $move_result; // pc played a card
	}
}