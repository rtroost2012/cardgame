<?PHP
class CardGame
{
	private $playStack;
	private $deck;
	private $players;

	public function __construct(Silex\Application $app, Symfony\Component\HttpFoundation\Session\Session $session, $request = NULL) {
		if(!$session->get('player_obj')) { // no game started yet
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

			if($request->getMethod() == 'POST') {
				if($request->get('take')) { // take a player card
					$this->players['player']->addCards($this->deck->giveCards($this->playStack)); // add one card to player cards
					$this->renderCards($app, $this->computerMove()); // render cards
				} else if($request->get('reset')) {
					$session->invalidate(); // reset
					header("location: /"); // go to main URL for starting a new game
					exit(); // don't further execute the code
				} else if($request->get('cheats')) { // handle cheats
					if($request->get('stealJokers')) {
						$jokerIndex = $this->findCard('JK', $this->players['opponent']->getCards(), true); // does the PC have jokers?

						while($jokerIndex) {
							$this->players['opponent']->removeCards(array('JK')); // remove from opponent deck
							$this->players['player']->addCards(array('JK')); // give to player
							$this->players['opponent']->addCards($this->deck->giveCards($this->playStack)); // give opponent a new card

							$jokerIndex = $this->findCard('JK', $this->players['opponent']->getCards(), true); // more jokers to steal?
						}
					} else if($request->get('instantWin')) { // player wins
						$this->players['player']->clearCards();
					} else if($request->get('instantLose')) { // opponent wins
						$this->players['opponent']->clearCards();
					} else if($request->get('cpuCards')) {
						$this->players['opponent']->addCards($this->deck->giveCards($this->playStack, 15)); // add 15 cards to cpu deck
					}
					$this->renderCards($app); // render cards
				}
			}
		}
	}

	public function renderCards(Silex\Application $app, array $info = NULL) { // TODO:seperate render class?
		$defaultInfo = array('playstack_card' => $this->lastOnPlayStack(),
													'opponentcards' => $this->players['opponent']->getCards(),
													'playercards' => $this->players['player']->getCards(),
													'deck_cardsleft' => $this->deck->countCards(),
													'validMove' => true);

		return $app['twig']->render('game.twig', (isset($info) ? $info : $defaultInfo)); // render with default or changed info?
	}

	private function findCard($card, array $targetStack, $ignoreStack = false) { // search for a partial value in an array and return full value
		foreach ($targetStack as $targetCard) {
			if((!$ignoreStack) && $targetCard == "JK" && $card == "JK") { // computer is not trying to put a joker card on a 'J' or 'K'card
				continue;
			}

			if(strpos($targetCard, $card) !== FALSE) {
				return $targetCard;
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

		// get last card placed on stack
		$lastCard = $this->lastOnPlayStack();

		if(in_array($card, $this->players[$inputPlayer]->getCards())) { // check if player has the card
			$type_stack = substr($lastCard, 0, 2); // card type on the playing stack
			$type_player = substr($card, 0, 2); // card type the player is trying to play
			$number_stack = substr($lastCard, 2, strlen($lastCard) - 2); // card number on the playing stack
			$number_player = substr($card, 2, strlen($card) - 2); // card number the player is trying to play]

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
		if($this->players['player']->countCards() != 0) { // player did not place his last card
			$computer_cards = $this->players['opponent']->getCards(); // get computer cards
			$lastCard = $this->lastOnPlayStack();

			$type_stack = substr($lastCard, 0, 2); // card type on the playing stack
			$number_stack = substr($lastCard, 2, strlen($lastCard) - 2); // card number on the playing stack

			$available_card_type = $this->findCard($type_stack, $computer_cards); // do we have a card with the same type?
			$available_card_number = $this->findCard($number_stack, $computer_cards); // do we have a card with the same type?

			if($type_stack != "JK" && !$available_card_type && !$available_card_number) { // no card with the same type or number
				$this->players['opponent']->addCards($this->deck->giveCards($this->playStack)); // grab a card
			} else { // play the card
				if($type_stack == "JK") { // joker on the playing stack, we can choose a card to play
					$cardID = rand(0, count($computer_cards) - 1);
					$move_result = $this->ValidateMove('opponent', $computer_cards[$cardID]);
				} else { // normal card
					// play jokers if needed
					$computer_jokerIndex = $this->findCard("JK", $computer_cards, true);
					$computer_normalIndex = ($available_card_type != NULL ? $available_card_type : $available_card_number);

					if(($computer_jokerIndex !== NULL) && ($this->players['player']->countCards() <= 3 || $this->players['opponent']->countCards() <= 3)) { // should we play it now?
						// make it random but make sure to play it if either one of the players has two cards left so we don't end up with only a joker
						if((rand(0, 1) == 1) || $this->players['player']->countCards() == 2 || $this->players['opponent']->countCards() == 2) {
							$move_result = $this->ValidateMove('opponent', $computer_jokerIndex);
						} else {
							$move_result = $this->ValidateMove('opponent', $computer_normalIndex);	
						}
					} else { // not a good moment to play the joker
						$move_result = $this->ValidateMove('opponent', $computer_normalIndex);
					}
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