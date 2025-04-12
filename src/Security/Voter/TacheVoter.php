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
    public const VOIR = 'VOIR_TACHE'; // 👈 nouveau droit

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

        // Chef de projet = tous les droits
        if (in_array('ROLE_CHEF_DE_PROJET', $user->getRoles())) {
            return true;
        }

        switch ($attribute) {
            case self::MODIFIER_STATUT:
                return $tache->getEmploye() === $user;

            case self::MODIFIER_TACHE:
                return false;

            case self::VOIR:
                return $tache->getEmploye() === $user; // ou tu peux élargir si besoin
        }

        return false;
    }
}
