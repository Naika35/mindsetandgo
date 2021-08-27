<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class UserVoter extends Voter
{
    // afin d'avoir is_Granted dans notre Voter, on va faire une injection de dépendance (intancier une classe dans une autre) 
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['ADMIN_USER_ADD', 'USER_EDIT', 'USER_DELETE'])
            && $subject instanceof User;
    }


    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // Donne tous les pouvoirs au Role SUPER_ADMIN
        if ($this->security->isGranted('ROLE_SUPER_ADMIN')) {
            return true;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case 'ADMIN_USER_ADD':
                if(in_array('ROLE_SUPER_ADMIN', $user->getRoles())){
                    return true;
                }
                break;
            case 'USER_EDIT':
                // logic to determine if the user can EDIT
                // return true or false
                if ($user != null) {
                    return true;
                }
                break;
            case 'USER_DELETE':
                // logic to determine if the user can DELETE
                // return true or false
                if($user != null ){
                    return true;
                }
                break;
               
        }

        return false;
    }
}
