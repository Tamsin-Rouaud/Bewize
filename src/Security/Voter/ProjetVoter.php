<?php

namespace App\Security\Voter;


use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class ProjetVoter extends Voter
{ // On définit deux types d'autorisations : voir et modifier
    public const VOIR = 'VOIR_PROJET';
    public const MODIFIER = 'MODIFIER_PROJET';

    // Cette méthode vérifie si le Voter doit intervenir
    protected function supports(string $attribute, $subject): bool
    {
        // Il intervient uniquement pour les droits 'VOIR_PROJET' ou 'MODIFIER_PROJET' sur un objet de type Projet
        return in_array($attribute, [self::VOIR, self::MODIFIER])
            && $subject instanceof Projet;
    }

    // C'est ici que l'on détermine si l'utilisateur a l'autorisation ou non
    protected function voteOnAttribute(string $attribute, $projet, TokenInterface $token): bool
    {
        // On récupère l'utilisateur actuel
        $user = $token->getUser();

        // Si ce n'est pas un utilisateur connecté (Employe), on refuse
        if (!$user instanceof Employe) {
            return false;
        }

        // Si l'utilisateur est chef de projet, il a tous les droits
        if (in_array('ROLE_CHEF_DE_PROJET', $user->getRoles())) {
            return true;
        }

        // Pour les autres, on vérifie selon l'action demandée
        switch ($attribute) {
            case self::VOIR:
                // L'utilisateur peut voir le projet s'il en fait partie
                return $projet->getEmploye()->contains($user);

            case self::MODIFIER:
                // Par défaut, seuls les chefs de projet peuvent modifier, donc false ici
                return false;
        }

        // Par sécurité, on refuse tout le reste
        return false;
    }
}


