<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/dashboard/admin')]
#[IsGranted('ROLE_ADMIN')]
final class AdminController extends AbstractController
{
    #[Route('/list_users', name: 'app_dashboard_admin_list_users')]
    public function listUsers(UserRepository $userRepository): Response
    {
        $users = $userRepository->findByExcludingRoleAdminFallback();

        return $this->render('back/users_list.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/delete_user/{user}', name: 'app_dashboard_admin_delete_user')]
    public function deleteUser(User $user, EntityManagerInterface $entityManager): Response
    {
        $userEmail = $user->getEmail();

        $userSchedules = $user->getWebLinkSchedules();

        foreach ($userSchedules as $schedule)
        {
            $entityManager->remove($schedule);
        }

        $entityManager->remove($user);

        $entityManager->flush();

        $this->addFlash('success', 'User '.$userEmail.' has been correctly deleted!');

        return $this->redirectToRoute('app_dashboard_admin_list_users');
    }

    #[Route('/delete_inactive_users/', name: 'app_dashboard_admin_delete_inactive_users')]
    public function deleteInactiveUsers(UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {

        $inactiveUsers = $userRepository->findBy(['active' => 0]);

        if($inactiveUsers)
        {
            foreach ($inactiveUsers as $user)
            {
                $entityManager->remove($user);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Inactive Users has been correctly deleted!');
        } else {
            $this->addFlash('info', 'No inactive users in database!');
        }

        return $this->redirectToRoute('app_dashboard_admin_list_users');
    }
}
