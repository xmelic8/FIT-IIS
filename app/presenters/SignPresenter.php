<?php

namespace App\Presenters;

use Nette;
use App\Forms\SignFormFactory;


class SignPresenter extends BasePresenter
{
	/** @var SignFormFactory @inject */
	public $factory;
   public $user;
   
   public function __construct(Nette\Security\User $user)
   {
      $this->user=$user;
   }
        
   public function renderDefault()
	{
      if(($this->user->isLoggedIn())){
         if($this->user->isInRole('Majitel')){
            redirect('Owner:');
         }
         if($this->user->isInRole('Vedouci')){
            redirect('Supervisor:');
         }
         if($this->user->isInRole('Obsluha')){
            redirect('Service:');
         }
      }
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
         if($this->user->getIdentity()->getRoles()[0]=='Majitel'){
            $form->getPresenter()->redirect('Owner:');
         }
         else if($this->user->getIdentity()->getRoles()[0]=='Vedouci'){
            $form->getPresenter()->redirect('Supervisor:');
         }
         else if($this->user->getIdentity()->getRoles()[0]=='Obsluha'){
            $form->getPresenter()->redirect('Service:');
         }
         else{
            $form->getPresenter()->redirect('Sign:in');
         }
		};
		return $form;
	}
   
   public function actionOut()
	{
      $this->getUser()->logout();
      $this->redirect('Homepage:');
	}

}
