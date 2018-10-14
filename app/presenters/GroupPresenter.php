<?php

namespace App\Presenters;

use Nette;
use App\Forms\objednavkaForm;
use Nette\Database\Connection;

class GroupPresenter extends BasePresenter
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
      $this->template->groups = $this->database->table('tabskupina');
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
   
   public function renderShow($groupId)
   {
      $this->template->_user = $this->user;
      $this->template->_username = $this->user->getIdentity()->jmeno;

      $tmp = $this->database->table('tabskupina')->get($groupId);
      if (!$tmp) {
         $this->error('Stránka nebyla nalezena');
      }
      
      $this->template->group = $tmp;
      $tmp2 = $this->database->table('tabzbozi');
      if (!$tmp2) {
         $this->error('Stránka nebyla nalezena');
      }
      $this->template->group_items = $tmp2;
      $this->template->ID_group= $groupId;
   }
   
   public function actionDelete($polozka)
   {
      $this->database->table('tabskupina')->get($polozka)->update(array('aktivni' => new \Nette\Database\SqlLiteral('false'))); 
      $this->redirect('Group:');
   }
}
