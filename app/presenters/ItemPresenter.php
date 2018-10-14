<?php
namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use Nette\Database\Connection;


class ItemPresenter extends BasePresenter
{
   private $database;

   public function __construct(Nette\Database\Context $database)
   {
      $this->database = $database;
   }

   public function renderShow($postId)
   {
        
   }
   
   
   /*Funkce pro nastaveni, zda byla polozka objednavky vydana nebo ne.*/
   public function actionPublic($postId, $decision)
   {
      if($decision){
         $this->database->table('tabpolozkaobjednavka')->get($postId)->update(array('vydano' => new \Nette\Database\SqlLiteral('"ano"'))); 
      }
      else{
         $this->database->table('tabpolozkaobjednavka')->get($postId)->update(array('vydano' => new \Nette\Database\SqlLiteral('"ne"'))); 
      }
       
      $value = $this->database->table('tabpolozkaobjednavka')->get($postId)->cislo_objednavky;
      $this->redirect('Order:show' , array($value));
   }
}