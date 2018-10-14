<?php

namespace App\Forms;

use Nette;
use Nette\Application\UI\Form;
use Nette\Security\User;


class objednavkaForm extends Nette\Object
{
	/** @var User */
	private $user;
        private $db;


	public function __construct(User $user,Nette\Database\Context $database)
	{
		$this->user = $user;
                $this->db=$database;
	}


	/**
	 * @return Form
	 */
	public function create()
	{
		$form = new Form;
                $row = $this->db->table('tabobjednavka');
  
                $arr = array();
                foreach ($row as $item)
                {
                    $obsluha=$item->ref('tabzamestnanec','obsluha');
                    array_push($arr, $item->ID_objednavky." ".$obsluha->jmeno." ".$obsluha->prijmeni);
                }
                $form->addMultiSelect("vyberObjednavku", "Objednavka",  $arr);
		return $form;
	}


	public function formSucceeded(Form $form, $values)
	{
               // $this->user->setExpiration('20 minutes', TRUE);
		try {
			$this->user->login($values->username, $values->password);
                        $this->user->setExpiration('1 days', FALSE);
                        
                } catch (Nette\Security\AuthenticationException $e) {
			$form->addError($e->getMessage());
		}
	}

}
