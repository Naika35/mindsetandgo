<?php

namespace App\Security\Voter;

use App\Entity\Quote;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class QuoteVoter extends Voter
{

    // afin d'avoir is_Granted() dans notre Voter, on va faire une injection de dépendance (instancier une classe dans une autre) 
    // in order to have is_Granted () in our Vote, we will make an injection of dependence (instantiate one class in another)
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * Checks if permission exists in the vote
     *
     * @param string $attribute
     * @param [type] $subject
     * @return boolean
     */
    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['QUOTE_ADD', 'QUOTE_EDIT', 'QUOTE_DELETE',])
            && $subject instanceof Quote;
    }
    
    /**
     * If supports() === true this function will be executed
     * It checks whether we meet all the criteria to operate
     *
     * @param string $attribute
     * @param [type] $subject
     * @param TokenInterface $token
     * @return boolean
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // Gives all power to ROLE_ADMIN
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }
        
        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {

            case 'QUOTE_ADD':
            // if user is connected
                if ($user) {
                    return true;
                };
                break;
            case 'QUOTE_EDIT':
                // logic to determine if the user can EDIT
                // return true or false
                if($user === $subject->getUser()){
                    return true;
                }
                break;
            case 'QUOTE_DELETE':
                // logic to determine if the user can DELETE
                // return true or false
                if($user === $subject->getUser()){
                    return true;
                }
                break;
        }

        return false;
    }
}
