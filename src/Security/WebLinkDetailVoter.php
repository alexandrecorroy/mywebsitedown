<?php
namespace App\Security;

use App\Entity\User;
use App\Entity\WebLinkDetail;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class WebLinkDetailVoter extends Voter
{
    const VIEW = 'view';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::VIEW])) {
            return false;
        }

        // only vote on `WebLinkDetail` objects
        if (!$subject instanceof WebLinkDetail) {
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
        /** @var WebLinkDetail $webLinkDetail */
        $webLinkDetail = $subject;

        return match($attribute) {
            self::VIEW => $this->canView($webLinkDetail, $user),
            default => throw new \LogicException('This code should not be reached!')
        };
    }

    private function canView(WebLinkDetail $webLinkDetail, User $user): bool
    {

        return $user === $webLinkDetail->getWebLink()->getWebLinkSchedule()->getOwner();
    }
}