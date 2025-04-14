<?php

namespace App\Security\Voter;

use App\Entity\Tache;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class TacheVoter extends Voter
{
    public const MODIFIER_STATUT = 'MODIFIER_STATUT';
    public const MODIFIER_TACHE = 'MODIFIER_TACHE';
    public const VOIR = 'VOIR_TACHE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::MODIFIER_STATUT, self::MODIFIER_TACHE, self::VOIR])
            && $subject instanceof Tache;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        /** @var Tache $tache */
        $tache = $subject;

        // Le chef de projet a tous les droits
        if (in_array('ROLE_CHEF_DE_PROJET', $user->getRoles())) {
            return true;
        }

        switch ($attribute) {
            case self::MODIFIER_STATUT:
                // Seul l'employé assigné peut modifier le statut
                return $tache->getEmploye() === $user;

            case self::MODIFIER_TACHE:
                // Aucun collaborateur ne peut modifier toute la tâche
                return false;

            case self::VOIR:
                // Tous les employés du projet peuvent voir la tâche
                return $tache->getProjet()->getEmploye()->contains($user);
        }

        return false;
    }
}
