<?php


namespace App\Security;

use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        // Add pre-authentication checks (e.g., is the user active?)
        if (method_exists($user, 'isEnabled') && !$user->isEnabled()) {
            throw new \Exception('User account is disabled.');
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        // Add post-authentication checks
        // Example: Verify roles or conditions after login
    }
}
