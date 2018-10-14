<?php
namespace App\Forms;

use Nette;

use Nette\Security as NS;


class SingManager extends Nette\Object implements NS\IAuthenticator
{
    private $database;
            

    function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
        
    }

    public function authenticate(array $credentials)
    {
        list($username, $password) = $credentials;
         $row = $this->database->table('tabzamestnanec')
                ->where('username', $username)->fetch();
        if (!$row || $row->aktivni == 0) {
            throw new NS\AuthenticationException("User '$username' not found.", self::IDENTITY_NOT_FOUND);
        }

        if ($row->passwd != $password ) {
            throw new NS\AuthenticationException("Invalid password.", self::INVALID_CREDENTIAL);
        }
        return new NS\Identity($row->ID_zamestnance, $row->funkce, array('username' => $row->username,'jmeno' => $row->jmeno." ".$row->prijmeni));
    }
        
}



