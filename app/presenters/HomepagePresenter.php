<?php

namespace App\Presenters;

use Nette;
use Nette\Security\User;


class HomepagePresenter extends BasePresenter
{
   private $user;
   private $result;
   
   public function __construct(User $user)
   {
      parent::__construct();
      $this->user = $user;
   }
        
	public function renderDefault()
	{
            if($this->user->isLoggedIn())
            {
                if($this->user->isInRole("Majittel"))
                {
                     $this->redirect("Owner:");
                }
                elseif($this->user->isInRole("Majittel"))
                {
                     $this->redirect("Supervisor:");
                }
                else 
                {
                    $this->redirect("Service:");
                }
            }
            else
            {
                $this->redirect("Sign:in");
            }
	}

}
