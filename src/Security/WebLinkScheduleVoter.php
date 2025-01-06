<?php
namespace App\Security;

use App\Entity\User;
use App\Entity\WebLinkSchedule;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class WebLinkScheduleVoter extends Voter
{
    const VIEW = 'view';
    const EDIT = 'edit';
    const DELETE = 'delete';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::VIEW, self::EDIT, self::DELETE])) {
            return false;
        }

        // only vote on `WebLinkSchedule` objects
        if (!$subject instanceof WebLinkSchedule) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        // you know $subject is a WebLinkSchedule object, thanks to `supports()`
        /** @var WebLinkSchedule $webLinkSchedule */
        $webLinkSchedule = $subject;

        return match($attribute) {
            self::VIEW => $this->canView($webLinkSchedule, $user),
            self::EDIT => $this->canEdit($webLinkSchedule, $user),
            self::DELETE => $this->canDelete($webLinkSchedule, $user),
            default => throw new \LogicException('This code should not be reached!')
        };
    }

    private function canDelete(WebLinkSchedule $webLinkSchedule, User $user): bool
    {
        // if they can edit, they can view
        if ($this->canEdit($webLinkSchedule, $user)) {
            return true;
        }

        return false;
    }

    private function canView(WebLinkSchedule $webLinkSchedule, User $user): bool
    {
        // if they can edit, they can view
        if ($this->canEdit($webLinkSchedule, $user)) {
            return true;
        }

        return false;
    }

    private function canEdit(WebLinkSchedule $webLinkSchedule, User $user): bool
    {
        // this assumes that the WebLinkSchedule object has a `getOwner()` method
        return $user === $webLinkSchedule->getOwner();
    }
}