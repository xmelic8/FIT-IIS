<?php

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use Nette\Database\Connection;


class TablePresenter extends BasePresenter
{
   public $user;
   private $database;
        
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
      $this->template->tables = $this->database->table('tabstul');
      $this->template->_user = $this->user;
      $this->template->_username = $this->user->getIdentity()->jmeno;
	}
   
   public function actionOut()
	{
      $this->getUser()->logout();
      $this->flashMessage('Odhlášení bylo úspěšné.');
      $this->redirect('Homepage:');
	}

   /* Funkce pro zakladni vypis seznamu objednavek. */
   public function renderShow($roomId)
   {
      if(!($this->user->isLoggedIn()))
      {
         $this->redirect("Homepage:");
      }
      else if(($this->user->isInRole('Obsluha')))
      {
                    $this->redirect("Service:");
      }
      $this->template->_user = $this->user;
      $this->template->_username = $this->user->getIdentity()->jmeno;
      
      $tmp = $this->database->table('tabblokace')->get($roomId);
      if (!$tmp) {
         $this->error('Stránka nebyla nalezena');
      }

      $this->template->tables = $tmp;    
   }
   
   public function renderNew()
	{
      if(!($this->user->isLoggedIn()))
      {
         $this->redirect("Homepage:");
      }
      else if(($this->user->isInRole('Obsluha')))
      {
                    $this->redirect("Service:");
      }
      $this->template->_user = $this->user;
      $this->template->_username = $this->user->getIdentity()->jmeno;
	}
    public function createComponentTableForm()
    {
        $ob = $this->database->table("tabzamestnanec");
        $obsluha = array();
        foreach ($ob as $o)
        {
            array_push($obsluha, "$o->jmeno $o->prijmeni");
        }
        $mi = $this->database->table("tablokace");
        $mistnost = array();
        foreach ($mi as $m)
        {
            array_push($mistnost, "$m->ID_lokace,$m->nazev");
        }
        $form = new Form;
        $form->addText("cisloS","Číslo stolu: ")->addRule(Form::INTEGER,"Zadejte cislo");
        $form->addText("pocetM","Počet Míst: ")->addRule(Form::INTEGER,"Zadejte cislo");
        $form->addSelect("lokace","Lokace: ")->setItems($mistnost,FALSE);
        $form->addSelect("obsluha","Obsluha: ")->setItems($obsluha,FALSE);
        $form->onValidate[]=  function(Form $form)
        {
            $cislo=$form->getComponent("cisloS")->getValue();
            $pocet=$form->getComponent("pocetM")->getValue();
            $lo = $form->getComponent("lokace")->getSelectedItem();
            $ob = $form->getComponent("obsluha")->getSelectedItem();
            list($id)= explode(",", $lo);
            if($cislo<1 || $pocet<1)
            {
                $form->addError("Zadejte kladne cislo");
            }
            if(!$ob)
            {
                $form->addError("Vyberet obsluhu");
            }
            if(!$lo)
            {
                $form->addError("Vyberet lokaci");
            }
            $t = $this->database->table("tabstul")->where("ID_stul",$cislo);
            $l = $this->database->table("tablokace")->where("ID_lokace",$id);
            if($t->count())
            {
                $form->addError("Daný stul už exstuje");
            }
            $p = $this->database->table("tabstul")->where("umisteni_lokace",$id);
            if($p->count()>=$l->fetch()->pocet_stolu)
            {
                $form->addError("Lokace už má maximální počet stolů");
            }
        };
        $form->onSuccess[]=  function(Form $form)
        {
            $cislo=$form->getComponent("cisloS")->getValue();
            $pocet=$form->getComponent("pocetM")->getValue();
            $lo = $form->getComponent("lokace")->getSelectedItem();
            $ob = $form->getComponent("obsluha")->getSelectedItem();
            list($id)= explode(",", $lo);
            $t = $this->database->table("tabstul");
            //$t->insert(array("pocet_mist"=>$pocet,"umisteni_lokace"=>$id,"obsluha"=>$ob));
            
         
            
        };
        $form->addSubmit("odesli","Vytvoř");
        
        return $form;        
    } 
    
    public function actionDelete($tableId, $value)
   {
      if(!($this->user->isLoggedIn()))
      {
         $this->redirect("Homepage:");
      }
      else if(($this->user->isInRole('Obsluha')))
      {
          $this->redirect("Service:");
      }
      $this->template->_user = $this->user;
      $this->template->_username = $this->user->getIdentity()->jmeno;
      
      if (!$tableId) {
         $this->error('Stránka nebyla nalezena');
      }
      else
      {
           $db = $this->database->table("tabstul")->get($tableId);
           if($value == true){
            $db->update(array("aktivni"=>1));
           }
           if($value == false){
            $db->update(array("aktivni"=>0));
           }
           $this->redirect("Table:");
 
       }

   }
}
