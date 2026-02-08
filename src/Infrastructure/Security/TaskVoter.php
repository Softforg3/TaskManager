<?php

declare(strict_types=1);

namespace App\Infrastructure\Security;

use App\Domain\User\Enum\UserRole;
use App\Domain\User\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class TaskVoter extends Voter
{
    public const ASSIGN_TO_OTHERS = 'TASK_ASSIGN_TO_OTHERS';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::ASSIGN_TO_OTHERS;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        return $user->getRole() === UserRole::ADMIN;
    }
}
