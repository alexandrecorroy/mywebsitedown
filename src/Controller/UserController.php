<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\UserType;
use App\Helper\FunctionsHelper;
use App\Mailer\CreateUserMailer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class UserController extends AbstractController
{
    #[Route('dashboard/user/settings', name: 'app_dashboard_user_settings')]
    public function userSettings(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager, FunctionsHelper $helper, #[CurrentUser] ?User $user, CreateUserMailer $mailer, Security $security): Response
    {
        $formUser = clone $user;

        $form = $this->createForm(UserType::class, $formUser);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $password = $form->get('password')->getData();

            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $password
            );

            $user->setEmail($formUser->getEmail());
            $user->setTokenVerifiy($helper->generateTokenForUser($formUser->getEmail()));
            $user->setTokenVerifyDateCreated(new \DateTime('now'));
            $user->setPassword($hashedPassword);
            $user->setActive(false);

            $entityManager->persist($user);
            $entityManager->flush();

            $mailer->sendMail($user);

            $security->logout(false);

            $this->addFlash('success', 'You\'ve received an email to validate your email. Please check yours emails and confirm it.');
            return $this->redirectToRoute('app_login');

        }

        return $this->render('back/user_settings.html.twig', [
            'form' => $form
        ]);

    }

    #[Route('dashboard/user/delete', name: 'app_dashboard_user_delete')]
    public function userDelete(EntityManagerInterface $entityManager, #[CurrentUser] ?User $user, Security $security): Response
    {
        $userSchedules = $user->getWebLinkSchedules();

        foreach ($userSchedules as $schedule)
        {
            $entityManager->remove($schedule);
        }

        $entityManager->remove($user);

        $entityManager->flush();

        $security->logout(false);

        $this->addFlash('success', 'Your account and all your data has been correctly deleted');

        return $this->redirectToRoute('app_login');

    }
}