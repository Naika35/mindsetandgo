<?php

namespace App\Security\Voter;

use App\Entity\Quote;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class QuoteVoter extends Voter
{

    // afin d'avoir is_Granted dans notre Voter, on va faire une injection de dépendance (intancier une classe dans une autre) 
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * Vérifie si la permission existe dans le voter/ si notre Voter s'oocupe du droit demandé sur le type d'objet demandé
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
     * Si supports() === true alors cette fonction sera exécutée
     * Elle vérifie si on respect tous les critères pour fonctionner
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

        // Donne tous les pouvoirs au Role SUPER_ADMIN
        if ($this->security->isGranted('ROLE_SUPER_ADMIN')) {
            return true;
        }
        
        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {

            case 'ADMIN_QUOTE_ADD':
            // Si le user a un token, il est connecté, donc il existe
                if ($user) {
                    return true;
                };
                break;
            case 'QUOTE_EDIT':
                // logic to determine if the user can EDIT
                // return true or false
                if($user === $subject->getUser() || (in_array('ROLE_ADMIN', $user->getRoles()))){
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
