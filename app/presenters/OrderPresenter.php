<?php

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use Nette\Database\Connection;

class OrderPresenter extends BasePresenter
{
   public $user;
   private $database;
   private $polozka;
        
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
      $this->template->posts = $this->database->table('tabobjednavka');
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
   public function renderShow($postId, $error)
   {
      $this->template->_user = $this->user;
      $this->template->_username = $this->user->getIdentity()->jmeno;
      $this->template->error = $error;
      
      if(!($this->user->isLoggedIn()))
      {
        $this->redirect("Homepage:");
      }
      
      $post = $this->database->table('tabobjednavka')->get($postId);
      if (!$post) {
         $this->error('Stranka nebyla nalezena');
      }

      $this->template->polozky = $post; 
   }
   public function renderModify($postId)
   {
      if(!($this->user->isLoggedIn()))
      {
        $this->redirect("Homepage:");
      }
      $this->template->_user = $this->user;
      $this->template->_username = $this->user->getIdentity()->jmeno;
      
      $post = $this->database->table('tabobjednavka')->get($postId);
      if (!$post) 
      {
          echo $postId;
      }

      $this->template->polozky = $post; 
      $this->template->polozka=$postId;
      $this->template->db = $this->database->table("tabpolozkaobjednavka")->where("cislo_objednavky",  $postId); 
   }
   
   public function createComponentModifyForm() 
   {
       $tabPolOb=$this->database->table("tabpolozkaobjednavka")->where("cislo_objednavky",  $this->polozka);
       $form = new Form;
       $form->addText("datum","Datum: ");
       $form->addText("zaplaceno","Zaplaceno: ");
       $form->addText("stul","Stul");
       $form->addText("obsluha","Obsluha");
       $i=1;
       /*foreach ($tabPolOb as $item)
       {
           $p = $item->ref("tabpolozkyjl","polozka_jl");
           $nazev = $p->ref("tabzbozi","zbozi");
           $form->addText("polozka$i","Polozka $i:")->setValue($nazev->nazev);
           $i = $item->ID_polozky_obj;
           $a = "{link Order deleteP $i}";
           $form->add("obstranit$i")->setAttribute("href = \"/restaurace_v6/order/create?postId=1\"");
           $i++;
       }*/
       
       return $form;
       
   }
   
   public function renderCreate()
	{
      if(!($this->user->isLoggedIn()))
      {
          $this->redirect("Homepage:");
      }
      $this->template->_user = $this->user;
      $this->template->_username = $this->user->getIdentity()->jmeno;
	}
    public function renderDeleteItem($itemId,$orderID)
    {
      if(!($this->user->isLoggedIn()))
      {
          $this->redirect("Homepage:");
      }
      $this->template->_user = $this->user;
      $this->template->_username = $this->user->getIdentity()->jmeno;
      $pozka_obednavky = $this->database->table("tabpolozkaobjednavka")->where("ID_polozky_obj",  $itemId)->fetch();
      
      if(!$pozka_obednavky)
      {
          $this->link("Order:show", $orderID);
          $this->template->mnoztvi="";
          $this->template->zbozi="";
          $this->template->cena="";
      }
      else 
      {
          $zbozi = $pozka_obednavky->ref("tabpolozkyjl","polozka_jl");
          $mnoztvi = $pozka_obednavky->mnozstvi;    
          $tabZbozi = $this->database->table("tabzbozi")->where("ID_polozka",$zbozi->zbozi);
          $z=$tabZbozi->fetch();
          $tabZbozi->update(array("mnozstvi"=>  intval($z->mnozstvi)+$mnoztvi));
          $tabOb = $this->database->table("tabobjednavka")->where("ID_objednavky",$pozka_obednavky->cislo_objednavky);
          $objednavka = $tabOb->fetch();
          $tabOb->update(array("zaplaceno"=>($objednavka->zaplaceno)-($mnoztvi*$z->cena_prodejni)));
          $this->database->table("tabpolozkaobjednavka")->where("ID_polozky_obj",  $itemId)->delete();
          $this->template->mnoztvi = $mnoztvi;
          $this->template->zbozi = $z->nazev;
          $this->template->cena = $z->cena_prodejni*$mnoztvi;
      }
      
      
      
      $this->template->zpet=$orderID;
    }
    
    public function actionPublic($postId)
   {
      $zaplatit = 0;
      $tmp = $this->database->table('tabobjednavka')->get($postId);
      foreach ($tmp->related('tabpolozkaobjednavka') as $zaznam){
         if(!strcmp($zaznam->vydano,'ano')){
            $zaplatit = $zaplatit + (($zaznam->mnozstvi) * ($zaznam->ref('tabpolozkyjl', 'polozka_jl')->ref('tabzbozi', 'zbozi')->cena_prodejni));
         }
         else{
            $this->redirect('Order:show' , array($postId, 1));
         }
      }
      
      $this->database->table('tabobjednavka')->get($postId)->update(array('zaplaceno' => new \Nette\Database\SqlLiteral($zaplatit))); 
      $this->database->table('tabobjednavka')->get($postId)->update(array('aktivni' => new \Nette\Database\SqlLiteral('false'))); 
      $this->redirect('Order:');
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
           $db = $this->database->table("tabobjednavka")->get($tableId);
           if($value == true){
            $db->update(array("aktivni"=>1));
           }
           if($value == false){
            $db->update(array("aktivni"=>0));
           }
           $this->redirect("Order:");
 
       }

   }
   
   public function renderDeleted()
	{
      if(!($this->user->isLoggedIn()))
      {
            $this->redirect("Homepage:");
      }
      $this->template->posts = $this->database->table('tabobjednavka');
      $this->template->_user = $this->user;
      $this->template->_username = $this->user->getIdentity()->jmeno;
	}
   
   public function renderCompleted()
	{
      if(!($this->user->isLoggedIn()))
      {
            $this->redirect("Homepage:");
      }
      $this->template->posts = $this->database->table('tabobjednavka');
      $this->template->_user = $this->user;
      $this->template->_username = $this->user->getIdentity()->jmeno;
	}
   
}
