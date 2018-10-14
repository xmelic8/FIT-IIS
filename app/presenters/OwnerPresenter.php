<?php

namespace App\Presenters;

use Nette;
use App\Model;


class OwnerPresenter extends BasePresenter
{
   public  $user;
   
   public function __construct(Nette\Security\User $user) 
   {
      $this->user=$user;
   }
	
   public function renderDefault()
	{
      if(!($this->user->isLoggedIn())){
         $this->redirect("Homepage:");
      }
      else if(($this->user->isInRole('Obsluha'))){
         $this->redirect("Service:");
      }
      else if(($this->user->isInRole('Vedouci'))){
         $this->redirect("Supervisor:");
      }
      
		$this->template->_user = $this->user;
      $this->template->_username = $this->user->getIdentity()->jmeno;
	}

}
