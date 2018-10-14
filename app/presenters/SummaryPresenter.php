<?php

namespace App\Presenters;

use Nette;
use App\Forms\objednavkaForm;
use Nette\Database\Connection;


class SummaryPresenter extends BasePresenter
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
      else if(($this->user->isInRole('Vedouci')))
      {
          $this->redirect("Supervisor:");
      }
      $this->template->rooms = $this->database->table('tablokace');
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
      else if(($this->user->isInRole('Vedouci')))
      {
          $this->redirect("Supervisor:");
      }
      $this->template->_user = $this->user;
      $this->template->_username = $this->user->getIdentity()->jmeno;
      
      $tmp = $this->database->table('tabblokace')->get($roomId);
      if (!$tmp) {
         $this->error('Stránka nebyla nalezena');
      }

      $this->template->room_tables = $tmp;    
   }
}
