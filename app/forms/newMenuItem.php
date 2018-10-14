<?php

namespace App\Forms;

use Nette;
use Nette\Application\UI\Form;
use Nette\Security\User;


class newMenuItemForm extends Nette\Object
{
	/** @var User */
	private $user;
        private $database;
        private $id;


	public function __construct(User $user,Nette\Database\Context $database)
	{
		$this->user = $user;
                $this->database=$database;
	}
        public function setId($id)
        {
            $this->id=$id;
        }
        public function getId()
        {
            return $this->id;
        }
        public function getZbozi()
        {
            $zbozi = $this->database->table("tabzbozi");
            $arr = array();
            foreach ($zbozi as $item)
            {
                array_push($arr, "$item->ID_polozka,$item->nazev,$item->cena_prodejni,[$item->mj]");
            }
            return $arr;
        }
           
   public function jeVaha($v)
   {
       if(($v == "kg" or $v == "g"))
       {
           return TRUE;
       }
       return FALSE;
   }
   
   public function jeObjem($v)
   {
       if(($v == "L" or $v == "ml"))
       {
           return TRUE;
       }
       return FALSE;
   }        



        /**
	 * @return Form
	 */
	public function create()
	{
		$form = new Form;
                $form->addSelect("zbozi","Zbozi: ")->setItems($this->getZbozi(),FALSE)->setPrompt("Vyber");
                $form->addText("mnostvi","Mnosztvi: ")->addRule(Form::INTEGER, "Musi být zadané číslo");
                $form->addSelect("mj","Mj: ")->setItems(array("kg","g","ks","L","ml"))->setPrompt("Vyber");
                $form->addTextArea("poznamka","Poznamky: ");
                $form->addSubmit("check","Přidej");
                $form->onValidate[]=  function (Form $form)
                {
                    $vybraneZbozi = $form->getComponent("zbozi")->getSelectedItem();
                    list($id)=  explode(",", $vybraneZbozi);
                    if (!$vybraneZbozi) 
                    {
                        $form->addError("Nebylo vybrané zbozí!");
                    }
                        $zbozi = $this->database->table("tabzbozi")->get($id);
                    $mj = $form->getComponent("mj")->getSelectedItem();
                    $mnozstvi = $form->getComponent("mnostvi")->getValue();
                    if(!$mj)
                    {
                        $form->addError("Nebyla vybraná měrná jednotka!");
                    }
                    if($mnozstvi<1)
                    {
                        $form->addError("Mnostvi musí být kladné číslo!");
                    }
                    if($zbozi)
                    {
                       if($zbozi->mj!=$mj)
                       {
                           if($this->jeVaha($zbozi->mj)and$this->jeVaha($mj))
                           {
                       
                           }
                           elseif($this->jeObjem($zbozi->mj)and$this->jeObjem($mj))
                           {
                       
                           }
                            else
                            {
                                $form->addError("Mnerne jednotky musi sohlasit!");
                            }
                        }
                    }
                };
                $form->onSuccess[]=  function(Form $form)
                {
                    $vzbozi = $form->getComponent("zbozi")->getSelectedItem();
                    list($id)=  explode(",", $vzbozi);
                    $zbozi = $this->database->table("tabzbozi")->get($id);
                    $mnostvi = $form->getComponent("mnostvi")->getValue();
                    $mj = $form->getComponent("mj")->getSelectedItem();
                    $p = $form->getComponent("poznamka")->getValue();
                     if($mj=="kg"&&$zbozi->mj=="g")
                      {
                         $mj = "g";
                         $mnostvi*=1000;
                      }
                      if($mj=="g"&&$zbozi->mj=="kg")
                    {
               $mj = "kg";
               $mnostvi/=1000;
           }
           if($mj=="L"&&$zbozi->mj=="ml")
           {
               $mj = "ml";
               $mnostvi*=1000;
           }
           if($mj=="ml"&&$zbozi->mj=="L")
           {
               $mj = "L";
               $mnostvi/=1000;
           }
           if($this->id)
           {
              $db = $this->database->table("tabpolozkyjl","tabjidelnilistek")->where("tabjidelnilistek","tabpolozkyjl.jidelni_listek");
              //$item = $db->fetch();
              //$ite->related("tabjidelnilistek","jidelni_listek");
              $db->insert(array("mnozstvi"=>$mnostvi,"mj"=>$mj,"poznamka"=>$p, "jidelni_listek" => $this->id,"zbozi"=>$id));
           }
       };
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
