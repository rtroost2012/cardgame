<?PHP
class CardGame
{
	private $playStack;
	private $deck;
	private $players;

	public function __construct(Silex\Application $app, Symfony\Component\HttpFoundation\Session\Session $session, $request = NULL) {
		if(!$session->get('started')) { // no game started yet
			// session init
			$session->set('started', true);

			// create deck
			$this->deck = new Deck();
			$this->deck->shuffle();

			// create playing stack in the middle with one card to start with
			$this->playStack = new Stack();
			$this->playStack->addCards($this->deck->giveCards($this->playStack));

			// create player and give cards
			$this->players['player'] = new Player();
			$this->players['player']->addCards($this->deck->giveCards($this->playStack, 7));

			// create opponent and give cards
			$this->players['opponent'] = new Player();
			$this->players['opponent']->addCards($this->deck->giveCards($this->playStack, 7));

			// save instances to session
			$session->set('playStack_obj', $this->playStack);
			$session->set('deck_obj', $this->deck);
			$session->set('player_obj', $this->players['player']);
			$session->set('opponent_obj', $this->players['opponent']);
		} else { // game already started
			// set objects to session ones
			$this->playStack = $session->get('playStack_obj');
			$this->deck = $session->get('deck_obj');
			$this->players = array('player' => $session->get('player_obj'),
									'opponent' => $session->get('opponent_obj'));

			if(isset($request) && $request->getMethod() == 'POST') {
				if($request->get('take')) { // take a player card
					$this->players['player']->addCards($this->deck->giveCards($this->playStack)); // add one card to player cards

					$computerMove_result = $this->computerMove();
					$this->renderCards($app, $computerMove_result); // render cards
				} else if($request->get('reset')) {
					$session->invalidate(); // reset
					header("location: /"); // go to main URL for starting a new game
					exit; // don't further execute the code
				}
			}
		}
	}

	public function renderCards(Silex\Application $app, array $info = NULL) { // TODO:seperate render class?
		if(!isset($info)) { // render with current info
			return $app['twig']->render('game.twig', array('playstack_card' => $this->lastOnPlayStack(),
													'opponentcards' => $this->players['opponent']->getCards(),
													'playercards' => $this->players['player']->getCards(),
													'deck_cardsleft' => $this->deck->countCards(),
													'validMove' => true));
		} else {
			return $app['twig']->render('game.twig', $info); // render with different info
		}
	}

	private function array_find($needle, array $haystack) { // search for a partial value in an array and return full value
	   foreach ($haystack as $item) {
	      if(strpos($item, $needle) !== FALSE) {
	         return $item;
	         break;
	      }
	   }
	   return NULL;
	}

	private function lastOnPlayStack() { // get last card placed on stack
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
		$lastCard = $this->lastOnPlayStack();

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
					$this->players[$targetPlayer]->addCards($this->deck->giveCards($this->playStack, 5));
				} else if($number_player == "2") { // opponent has to take two cards
					$targetPlayer = ($inputPlayer == 'opponent' ? 'player' : 'opponent'); // which player to give the card
					$this->players[$targetPlayer]->addCards($this->deck->giveCards($this->playStack, 2));
				}

				$validMove = true; // player made a valid move
			}
		}

		// return info about move validation and rendering
		return array('playstack_card' => $this->lastOnPlayStack(),
					'opponentcards' => $this->players['opponent']->getCards(),
					'playercards' => $this->players['player']->getCards(),
					'deck_cardsleft' => $this->deck->countCards(),
					'validMove' => $validMove);
	}

	public function ComputerMove() {
		$computer_cards = $this->players['opponent']->getCards(); // get computer cards
		$lastCard = $this->lastOnPlayStack();

		$type_stack = substr($lastCard, 0, 2); // card type on the playing stack
		$number_stack = substr($lastCard, 2, strlen($lastCard) - 2); // card number on the playing stack

		$available_card_type = $this->array_find($type_stack, $computer_cards); // do we have a card with the same type?
		$available_card_number = $this->array_find($number_stack, $computer_cards); // do we have a card with the same type?

		if($type_stack != "JK" && !$available_card_type && !$available_card_number) { // no card with the same type or number
			$this->players['opponent']->addCards($this->deck->giveCards($this->playStack)); // grab a card
		} else { // play the card
			if($type_stack == "JK") { // joker on the stack
				$cardID = rand(0, count($computer_cards) - 1);
				$move_result = $this->ValidateMove('opponent', $computer_cards[$cardID]);
			} else { // normal card
				// play jokers if needed
				$jokerIndex = $this->array_find("JK", $computer_cards);

				if(($jokerIndex !== NULL) && ($this->players['player']->countCards() <= 3 || $this->players['opponent']->countCards() <= 3)) { // should we play it now?
					$move_result = $this->ValidateMove('opponent', $jokerIndex);
				} else { // not a good moment to play the joker
					$move_result = $this->ValidateMove('opponent', ($available_card_type != NULL ? $available_card_type : $available_card_number));
				}
			}
		}

		if(!isset($move_result)) { // pc has grabbed a card
			return array('playstack_card' => $this->lastOnPlayStack(),
						'opponentcards' => $this->players['opponent']->getCards(),
						'playercards' => $this->players['player']->getCards(),
						'deck_cardsleft' => $this->deck->countCards(),
						'validMove' => true);	
		}
		return $move_result; // pc played a card
	}
}