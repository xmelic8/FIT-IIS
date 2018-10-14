<?php

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use Nette\Database\Connection;
use App\Forms\newMenuItemForm;
use DateTime;


class MenuPresenter extends BasePresenter
{
   public $user;
   private $database;
   private $JL;
   private $newMenuForm;


   public function __construct(Nette\Security\User $user, Nette\Database\Context $database, \App\Forms\newMenuItemForm $form) 
   {
      $this->user=$user;
      $this->database = $database;
      $this->newMenuForm=$form;
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
      $this->template->menu = $this->database->table('tabjidelnilistek');
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
   
   public function createComponentMenuForm()
   {
       $form = new Form;
       $form->addText("nazev","Název: ");
       $form->addText("od_date","Od: ");
       $form->addText("do_date","Do: ");
       $form->addSubmit("vytvor","Vytvoř");
       $form->onValidate[]=  function(Form $form)
       {
           $nazev = $form->getComponent("nazev")->getValue();
           $od = $form->getComponent("od_date")->getValue();
           $do = $form->getComponent("do_date")->getValue();
           if(!$nazev)
           {
             $form->addError("Musíte vplnit název");
           }
           if(!$od and !$do)
           {
               
           }
           else
           {
                if(!$this->validateDate($od,"Y-m-d") or !$this->validateDate($do,"Y-m-d"))
                {
                    $form->addError("Formát data musíte vplni zprávně yyyy-mm-dd!");
                }
                else
                {
                    $test_od = new DateTime($od);
                    $test_do = new DateTime($do);
                    if($test_od>=$test_do)
                    {
                        $form->addError("Od musí být menší jak do");
                    }
                }
           }
           
       };
       $form->onSubmit[]=function(Form $form)
       {
           $nazev = $form->getComponent("nazev")->getValue();
           $od = $form->getComponent("od_date")->getValue();
           $do = $form->getComponent("do_date")->getValue();
           if(!$od&&!$do)
           {
               $this->database->table("tabjidelnilistek")->insert(array("nazev"=>$nazev));
           }
           else
           {
            $this->database->table("tabjidelnilistek")->insert(array("nazev"=>$nazev,"platnost_od"=>$od,"platnost_do"=>$do));
           }
           $this->redirect("Menu:");
            
        };
       return $form;
   }
   
    public function createComponentModifyMenuForm()
   {
        $id = $this->getParameter("menuId");
        $db = $this->database->table("tabjidelnilistek")->get($id);
       $form = new Form;
       $form->addText("nazev","Název: ")->setDefaultValue($db->nazev);
       $form->addText("od_date","Od: ")->setDefaultValue($db->platnost_od);
       $form->addText("do_date","Do: ")->setDefaultValue($db->platnost_do);
       $form->addSubmit("vytvor","Uprav");
       $form->onValidate[]=  function(Form $form)
       {
           $nazev = $form->getComponent("nazev")->getValue();
           $od = $form->getComponent("od_date")->getValue();
           $do = $form->getComponent("do_date")->getValue();
           if(!$nazev)
           {
             $form->addError("Musíte vplnit název");
           }
           if(!$od and !$do)
           {
               
           }
           else
           {
                if(!$this->validateDate($od,"Y-m-d") or !$this->validateDate($do,"Y-m-d"))
                {
                    $form->addError("Formát data musíte vplni zprávně yyyy-mm-dd!");
                }
                else
                {
                    $test_od = new DateTime($od);
                    $test_do = new DateTime($do);
                    if($test_od>=$test_do)
                    {
                        $form->addError("Od musí být menší jak do");
                    }
                }
           }
           
       };
       $form->onSubmit[]=function(Form $form)
       {
           $id = $this->getParameter("menuId");
           $nazev = $form->getComponent("nazev")->getValue();
           $od = $form->getComponent("od_date")->getValue();
           $do = $form->getComponent("do_date")->getValue();
           if(!$od&&!$do)
           {
               $this->database->table("tabjidelnilistek")->get($id)->update(array("nazev"=>$nazev));
           }
           else
           {
            $this->database->table("tabjidelnilistek")->get($id)->updat(array("nazev"=>$nazev,"platnost_od"=>$od,"platnost_do"=>$do));
           }
           $this->redirect("Menu:");
            
        };
       return $form;
   }
   
   public function renderNewItem($menu)
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
       if(!$menu)
       {
           $this->template->nazev="Nebyla nalezena";
       }
       else
       {
           $nazev = $this->database->table("tabjidelnilistek")->get($menu);
           $this->template->jl=$nazev->nazev;
       }
       
   }
   public function createComponentNewItemForm()
   {
       $this->newMenuForm->setId($this->getParameter("menu"));
       $form = $this->newMenuForm->create();
       return $form;
   }
   public function createComponentDeleteItemForm()
   {
       $id = $this->getParameter("menu");
       $db = $this->database->table("tabpolozkyjl")->where("jidelni_listek",$id);
       $data = array();
       foreach ($db as $item)
       {
           $zbozi=$item->ref("tabzbozi","zbozi");
           array_push($data, "$item->ID_polozky_jl,$zbozi->nazev,$item->mnozstvi,$item->mj");
       }
       $form = new Form;
       $form->addSelect("delete","Vyber")->setPrompt("Vyber")->setItems($data,FALSE);
       $form->addSubmit("odesli","Odtsraň");
       $form->onValidate[]=  function(Form $form)
       {
           $vyber = $form->getComponent("delete")->getSelectedItem();
           if(!$vyber)
           {
               $form->addError("Nevybral jste hodnotu!");
           }
       
       };
       $form->onSuccess[]=  function(Form $form)
       {
           $vyber = $form->getComponent("delete")->getSelectedItem();
           list($id)=  explode(",", $vyber);
           $db = $this->database->table("tabpolozkyjl")->get($id);
           $db->update(array("aktivni"=>0));
           $this->flashMessage("Položka byla odebrána");
       
       };
       return $form;
   }
   
   public function renderDeleteItem($menu)
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
      if(!$menu)
       {
           $this->template->nazev="Nebyla nalezena";
       }
       else
       {
          $db = $this->database->table("tabpolozkyjl")->get($menu);
          $db->update(array("aktivni"=>0));
            $this->redirect('Menu:show' , array($menu));
       }
   
       
   }

   public function renderShow($menuId)
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
      $tmp = $this->database->table('tabjidelnilistek')->get($menuId);
      
      if (!$tmp) {
         $this->error('Stránka nebyla nalezena');
      }

      $this->template->menu = $tmp; 
   }
   public function renderModify($menuId)
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
      
      if (!$menuId) {
         $this->error('Stránka nebyla nalezena');
      }

      $this->template->menu = $menuId; 
   }
   public function renderDelete($menuId, $value)
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
      
      if (!$menuId) {
         $this->error('Stránka nebyla nalezena');
      }
      else
      {
           $db = $this->database->table("tabjidelnilistek")->get($menuId);
           if($value == true){
            $db->update(array("aktivni"=>1));
           }
           if($value == false){
            $db->update(array("aktivni"=>0));
           }
           $this->redirect("Menu:");
 
       }

   }
   
   function validateDate($date, $format = "Y-m-d H:i")
   {          
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
   }
}