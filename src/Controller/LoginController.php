<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\ForgotPasswordType;
use App\Form\Type\ResetPasswordType;
use App\Form\Type\UserType;
use App\Helper\FunctionsHelper;
use App\Mailer\CreateUserMailer;
use App\Mailer\ResetPasswordMailer;
use App\Repository\UserRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class LoginController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    )
    {
    }

    #[Route('/routing', name: 'app_redirect_after_login')]
    public function routing(Security $security): Response
    {
        if ( $this->getUser() instanceof User) {
            if($this->getUser()->isActive())
            {
                return $this->redirectToRoute('app_dashboard_dashboard');
            } else {
                $security->logout(false);
                $this->addFlash('error', 'You must validate your email before sign in.');
                return $this->redirectToRoute('app_login');
            }

        }

        return $this->redirectToRoute('app_dashboard_dashboard');
    }

    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
         $error = $authenticationUtils->getLastAuthenticationError();

         $lastUsername = $authenticationUtils->getLastUsername();
        
         return $this->render('front/login.html.twig', [
               'last_username' => $lastUsername,
               'error'         => $error,
          ]);
    }

    #[Route('/forgot-password', name: 'app_forgot_password')]
    public function forgotPassword(Request $request, UserRepository $userRepository, FunctionsHelper $helper, ResetPasswordMailer $mailer): Response
    {

        $form = $this->createForm(ForgotPasswordType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $submittedToken = $request->getPayload()->get('csrf_token');
            $email = $form->get('email')->getData();

            if (!$this->isCsrfTokenValid('email', $submittedToken)) {
                return throw new AccessDeniedException();
            }


            $user = $userRepository->findOneBy(['email' => $email]);

            if($user)
            {
                if(!$user->isActive())
                {
                    $this->addFlash(
                        'notice',
                        [
                            'alert' => 'error',
                            'message' => 'Your email is not validated yet, please validate it before reset your password.'
                        ]
                    );

                    return $this->redirectToRoute('app_login');
                }


                $user->setTokenVerifiy($helper->generateTokenForUser($user->getEmail()));
                $user->setTokenVerifyDateCreated(new \DateTime('now'));

                $this->entityManager->flush();

                $mailer->sendMail($user);
            }

            $this->addFlash(
                'notice',
                [
                    'alert' => 'success',
                    'message' => 'An email was sent to register your new credentials.'
                ]
            );

            return $this->redirectToRoute('app_login');
        }

        return $this->render('front/forgot_password.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/reset-password/{token}', name: 'app_login_resetpassword')]
    public function resetPassword(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher, $token): Response
    {
        $user = $userRepository->findOneBy(['tokenVerifiy' => $token]);

        if(!$user)
        {
            throw new NotFoundHttpException();
        }

        $diff = $user->getTokenVerifyDateCreated()->diff(new \DateTime('now'));
        $minutes = ($diff->days * 24 * 60) + ($diff->h * 60) + $diff->i;

        if($minutes > 15)
        {
                $this->addFlash(
                    'notice',
                    [
                        'alert' => 'danger',
                        'message' => 'You have exceeded the time allowed to change your password.'
                    ]
                );

                return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(ResetPasswordType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $submittedToken = $request->getPayload()->get('csrf_token');
            $password = $form->get('password')->getData();

            if (!$this->isCsrfTokenValid('password', $submittedToken)) {
                return throw new AccessDeniedException();
            }

            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $password
            );
            $user->setPassword($hashedPassword);

            $this->entityManager->flush();

            $this->addFlash(
                'notice',
                [
                    'alert' => 'success',
                    'message' => 'The password has been changed.'
                ]
            );

            return $this->redirectToRoute('app_login');
        }

        return $this->render('front/reset_password.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/validate-account/{token}', name: 'app_validate_account')]
    public function validateAccount(UserRepository $userRepository, $token): Response
    {
        $user = $userRepository->findOneBy(['tokenVerifiy' => $token]);

        if(!$user)
        {
            throw new NotFoundHttpException();
        }

        if(!$user->isActive())
        {
            $user->setActive(true);

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->addFlash('success', 'Your email has been correctly validated. You can sign in now.');
        } else {
            $this->addFlash('error', 'Your email is already validated.');
        }

        return $this->redirectToRoute('app_login');
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, FunctionsHelper $helper, CreateUserMailer $mailer)
    {
        $user = new User();

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $password = $form->get('password')->getData();

            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $password
            );


            $user->setTokenVerifiy($helper->generateTokenForUser($user->getEmail()));
            $user->setTokenVerifyDateCreated(new \DateTime('now'));
            $user->setPassword($hashedPassword);

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $mailer->sendMail($user);

            $this->addFlash('success', 'You\'ve received an email to validate your email. Please check yours emails and confirm it.');
            return $this->redirectToRoute('app_login');

        }

        return $this->render('front/register.html.twig', [
            'form' => $form
        ]);
    }

}
