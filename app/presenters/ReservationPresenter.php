<?php

namespace App\Presenters;

use Nette;
use App\Forms\objednavkaForm;
use Nette\Database\Connection;


class ReservationPresenter extends BasePresenter
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
        
   public function renderTableshow()
	{
      $this->template->reservations = $this->database->table('tabrezervace');
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
   public function renderRoomshow()
	{
      $this->template->reservations = $this->database->table('tabrezervace');
      $this->template->_user = $this->user;
      $this->template->_username = $this->user->getIdentity()->jmeno;
	}
   
   public function renderRestaurantshow()
	{
      $this->template->reservations = $this->database->table('tabrezervace');
      $this->template->_user = $this->user;
      $this->template->_username = $this->user->getIdentity()->jmeno;
	}
   
   public function renderNew()
	{
      $this->template->reservations = $this->database->table('tabrezervace');
      $this->template->_user = $this->user;
      $this->template->_username = $this->user->getIdentity()->jmeno;
	}
   
   public function actionDelete($reservationId, $value, $presmerovani)
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
      
      if (!$reservationId) {
         $this->error('Stránka nebyla nalezena');
      }
      else
      {
           $db = $this->database->table("tabrezervace")->get($reservationId);
           if($value == true){
            $db->update(array("aktivni"=>1));
           }
           if($value == false){
            $db->update(array("aktivni"=>0));
           }
           
           if($presmerovani == 1){
            $this->redirect("Reservation:tableshow");
           }           
           if($presmerovani == 2){
            $this->redirect("Reservation:roomshow");
           }
           if($presmerovani == 3){
            $this->redirect("Reservation:restaurantshow");
           }
 
       }

   }
}
