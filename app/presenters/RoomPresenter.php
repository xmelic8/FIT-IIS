<?php

namespace App\Presenters;

use Nette;
use App\Forms\objednavkaForm;
use Nette\Database\Connection;


class RoomPresenter extends BasePresenter
{
	/** @var objednavkaForm @inject */
	public $factory;
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
      $this->template->rooms = $this->database->table('tablokace');
      $this->template->_user = $this->user;
      $this->template->_username = $this->user->getIdentity()->jmeno;
	}

   /**
    * Sign-in form factory.
	 * @return Nette\Application\UI\Form
	*/
	protected function createComponentSignInForm()
	{
		$form = $this->factory->create();
		$form->onSuccess[] = function ($form) {
                    //tady opravneni
                   
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

      $this->template->room_tables = $tmp;    
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
      $this->template->rooms = $this->database->table('tablokace');
      $this->template->_user = $this->user;
      $this->template->_username = $this->user->getIdentity()->jmeno;
	}
   
   public function renderShowTables($room)
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
      $this->template->rooms= $room;
      $this->template->_user = $this->user;
      $this->template->_username = $this->user->getIdentity()->jmeno;
   }
   
   public function actionDelete($roomId, $value)
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
      
      if (!$roomId) {
         $this->error('Stránka nebyla nalezena');
      }
      else
      {
           $db = $this->database->table("tablokace")->get($roomId);
           if($value == true){
            $db->update(array("aktivni"=>1));
           }
           if($value == false){
            $db->update(array("aktivni"=>0));
           }
           $this->redirect("Room:");
 
       }

   }
}
