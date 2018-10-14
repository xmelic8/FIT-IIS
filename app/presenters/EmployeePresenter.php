<?php

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use Nette\Database\Connection;


class EmployeePresenter extends BasePresenter
{
   public $user;
   private $database;
   private $id;
        
   public function __construct(Nette\Security\User $user, Nette\Database\Context $database) 
   {
      $this->user=$user;
      $this->database = $database;
   }
        
   public function renderDefault()
	{
      if(!($this->user->isLoggedIn()))
      {
          $this->redirect("Homepage:");
      }
      else if(($this->user->isInRole('Obsluha')))
      {
        $this->redirect("Service:");
      }
      else if(($this->user->isInRole('Vedouci')))
      {
          $this->redirect("Supervisor:");
      }
      $this->template->employees = $this->database->table('tabzamestnanec');
      $this->template->_user = $this->user;
      $this->template->_username = $this->user->getIdentity()->jmeno;
	}
    public function createComponentUserForm()
    {
            $form = new Form;
            $form->addText("jmeno","Jmeno: ")->addRule(Form::MIN_LENGTH,"Zadej jmeno",1);
            $form->addText("prijmeni","Prijmeni: ")->addRule(Form::MIN_LENGTH,"Zadej jmeno",1);
            $form->addText("rodne_cislo","Rodne cislo: ")->addRule(Form::PATTERN,"/^[0-9]{6}\/[0-9]{4}$/","[0-9]{6}\/[0-9]{4}");
            $form->addText("adresa","Adresa: ")->addRule(Form::MIN_LENGTH,"Musi by zadana adresa",1);
            $form->addSelect("funkce","Funkce: ")->setItems(array("Obsluha","Vedouci","Majitel"),FALSE)->setPrompt("Vyber funkci");
            $form->addText("telefon","Telofon: ")->addRule(Form::MIN_LENGTH,"Telefon musi mit minimalne 9 znaku",9);
            $form->addText("username","Username: ")->addRule(Form::MIN_LENGTH,"Jmeno musi mit minimalne 5 znaku",5);
            $form->addText("passwd","Password: ")->addRule(Form::MIN_LENGTH,"Heslo musi mit minimalne 5 znaku",5);
            $form->addSubmit("vytvorit","Vytvorit");
            $form->onValidate[]=  function(Form $form)
            {
                $u=$form->getComponent("username")->getValue();
                $r=$form->getComponent("rodne_cislo")->getValue();
                $checkUser = $this->database->table("tabzamestnanec")->where("username",$u);
                if(count($checkUser)>0)
                {
                     $form->addError("Uzivatel uz existuje!");
                }
                $checkR = $this->database->table("tabzamestnanec")->where("rodne_cislo",$r);
                if(count($checkR)>0)
                {
                     $form->addError("Rodne cislo existuje!");
                }
                if(!$form->getComponent("funkce")->getSelectedItem())
                {
                     $form->addError("Zadejte funkci!");
                }
            };
            $form->onSubmit[]=  function(Form $form)
            {
                 $tabUser = $this->database->table("tabzamestnanec");
                 $val = $form->getValues(TRUE);
                 $id = count($tabUser)+1;

                 list($j,$p,$r,$a,$f,$t,$u,$pass)=  array_combine(array("0","1","2","3","4","5","6","7"), $val);
                 $tabUser->insert(array("jmeno"=>$j,"prijmeni"=>$p,"rodne_cislo"=>$r,"adresa"=>$a,"funkce"=>$f,"telefon"=>(string)$t,"username"=>$u,"passwd"=>$pass));
                 $this->redirect("Employee:");
            };
           
            
            return $form;
     }
   
   public function actionOut()
	{
      $this->getUser()->logout();
      $this->flashMessage('Odhlášení bylo úspěšné.');
      $this->redirect('Homepage:');
	}

   /* Funkce pro zakladni vypis seznamu objednavek. */
   public function renderNew($postId)
   {
      if(!($this->user->isLoggedIn()))
      {
          $this->redirect("Homepage:");
      }
      else if(($this->user->isInRole('Obsluha')))
      {
        $this->redirect("Service:");
      }
      else if(($this->user->isInRole('Vedouci')))
      {
          $this->redirect("Supervisor:");
      }
      $this->template->_user = $this->user;
      $this->template->_username = $this->user->getIdentity()->jmeno;
   }
   
   public function renderDelete($postId)
   {
      if(!($this->user->isLoggedIn()))
      {
          $this->redirect("Homepage:");
      }
      else if(($this->user->isInRole('Obsluha')))
      {
        $this->redirect("Service:");
      }
      else if(($this->user->isInRole('Vedouci')))
      {
          $this->redirect("Supervisor:");
      }
       $this->template->_user = $this->user;
       $this->template->_username = $this->user->getIdentity()->jmeno;
       if(!$postId)
       {
           
       }
       else 
       {
            /*$tabU =$this->database->table("tabzamestnanec")->where("ID_zamestnance",$postId);*/
            $this->database->table('tabzamestnanec')->get($postId)->update(array('username' => new \Nette\Database\SqlLiteral('null'))); 
            $this->database->table('tabzamestnanec')->get($postId)->update(array('passwd' => new \Nette\Database\SqlLiteral('null'))); 
            $this->database->table('tabzamestnanec')->get($postId)->update(array('aktivni' => new \Nette\Database\SqlLiteral('0'))); 
            $this->redirect("Employee:");
      }
            /*if(count($tabU)>0)
            {
               $tabU->fetch()->update(array("username"=>'NULL'));
               $tabU->fetch()->update(array("aktivni"=>"0"));
               $this->redirect("Employee:");
            }*/
        /*}*/
   }
}