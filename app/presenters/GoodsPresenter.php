<?php

namespace App\Presenters;

use Nette;
use App\Forms\objednavkaForm;
use Nette\Database\Connection;

class GoodsPresenter extends BasePresenter
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
      $this->template->goods = $this->database->table('tabzbozi');
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
   public function renderShow($zboziId)
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
      
      $tmp = $this->database->table('tabzbozi')->get($zboziId);
      if (!$tmp) {
         $this->error('Stránka nebyla nalezena');
      }

      $this->template->goods = $tmp;    
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
    
     public function actionDelete($goodId, $value)
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
      
      if (!$goodId) {
         $this->error('Stránka nebyla nalezena');
      }
      else
      {
           $db = $this->database->table("tabzbozi")->get($goodId);
           if($value == true){
            $db->update(array("aktivni"=>1));
           }
           if($value == false){
            $db->update(array("aktivni"=>0));
           }
           $this->redirect("Goods:");
 
       }

   }
}
