<?php

namespace App\Validator;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class UniqueEmailValidator extends ConstraintValidator
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof UniqueEmail) {
            throw new \InvalidArgumentException(sprintf('The "%s" constraint is not supported.', UniqueEmail::class));
        }

        if (null === $value || '' === $value) {
            return;
        }

        // Vérifier si l'email existe déjà
        $userRepository = $this->entityManager->getRepository('App\Entity\User');
        $existingUser = $userRepository->findOneBy(['email' => $value]);

        if ($existingUser) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}
