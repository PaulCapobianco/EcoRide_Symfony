<?php

namespace App\Controller;

use App\Entity\Role;
use App\Entity\Utilisateur;
use App\Form\ForgotPasswordType;
use App\Form\LoginType;
use App\Form\RegisterType;
use App\Form\ResetPasswordType;
use App\Service\EmailVerificationService;
use App\Service\PasswordResetService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Authentification et inscription des utilisateurs.
 */
class AuthController extends AbstractController
{
    private const RESET_TOKEN_EXPIRATION_HOURS = 2;

    /**
     * Page de connexion : renvoie sur l'accueil si l'utilisateur est déjà authentifié.
     */
    #[Route('/login', name: 'login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }

        $error        = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        $form = $this->createForm(LoginType::class, [
            '_username' => $lastUsername,
        ]);

        return $this->render('auth/login.html.twig', [
            'form'  => $form,
            'error' => $error,
        ]);
    }

    /**
     * Point de sortie (géré par le firewall, méthode volontairement vide).
     */
    #[Route('/logout', name: 'logout')]
    public function logout(): void
    {
        throw new \LogicException('Logout est géré par le firewall.');
    }

    /**
     * Inscription + envoi d’e-mail de vérification.
     */
    #[Route('/register', name: 'register', methods: ['GET', 'POST'])]
    public function register(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher,
        EmailVerificationService $emailVerificationService
    ): Response {
        $form = $this->createForm(RegisterType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var array<string,mixed> $data */
            $data = $form->getData();

            $firstname     = trim((string) ($data['firstname'] ?? ''));
            $lastname      = trim((string) ($data['lastname'] ?? ''));
            $email         = trim((string) ($data['email'] ?? ''));
            $username      = trim((string) ($data['username'] ?? ''));
            $phone         = trim((string) ($data['phone'] ?? ''));
            $birthdate     = trim((string) ($data['birthdate'] ?? ''));
            $address       = trim((string) ($data['address'] ?? ''));
            $zip           = trim((string) ($data['zip'] ?? ''));
            $city          = trim((string) ($data['city'] ?? ''));
            $plainPassword = (string) ($data['password'] ?? '');

            $existing = $em->getRepository(Utilisateur::class)->createQueryBuilder('u')
                ->where('u.email = :email OR u.pendingEmail = :email')
                ->setParameter('email', $email)
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
            if ($existing) {
                $form->get('email')->addError(new FormError('Un compte existe déjà avec cette adresse e-mail.'));
            } else {
                $roleUser = $em->getRepository(Role::class)->findOneBy(['libelle' => 'ROLE_USER']);
                if (!$roleUser) {
                    throw new \RuntimeException('Le rôle ROLE_USER est manquant en base de données.');
                }

                $user = new Utilisateur();
                $user
                    ->setPrenom($firstname)
                    ->setNom($lastname)
                    ->setEmail($email)
                    ->setEmailVerified(false)
                    ->setPseudo($username !== '' ? $username : null)
                    ->setTelephone($phone !== '' ? $phone : null)
                    ->setAdresse(
                        trim(sprintf('%s, %s %s', $address, $zip, $city))
                    )
                    ->setDateNaissance($birthdate !== '' ? $birthdate : null)
                    ->setRole($roleUser)
                ;

                $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);

                // Bonus de bienvenue afin d'inciter au premier trajet
                $user->setCredit(20);

                $emailVerificationService->sendVerificationEmail($user, $email);

                $em->persist($user);
                $em->flush();

                $this->addFlash(
                    'success',
                    'Votre compte a été créé. Un e-mail de confirmation vous a été envoyé pour activer votre compte et recevoir vos 20 crédits de bienvenue.'
                );

                return $this->redirectToRoute('login');
            }
        }

        return $this->render('auth/register.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * Demande de réinitialisation (sans révéler si l’e-mail existe).
     */
    #[Route('/mot-de-passe/oublie', name: 'forgot_password', methods: ['GET', 'POST'])]
    public function forgotPassword(
        Request $request,
        EntityManagerInterface $em,
        PasswordResetService $passwordResetService
    ): Response {
        $form = $this->createForm(ForgotPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = trim((string) $form->get('email')->getData());
            $user  = $em->getRepository(Utilisateur::class)->findOneBy(['email' => $email]);

            if ($user instanceof Utilisateur) {
                // On ne révèle pas l’existence du compte, mais on déclenche l’envoi si présent
                $passwordResetService->sendResetLink($user);
                $em->flush();
            }

            $this->addFlash(
                'success',
                'Si un compte correspond à cet e-mail, un lien de réinitialisation vient de vous être envoyé.'
            );

            return $this->redirectToRoute('login');
        }

        return $this->render('auth/forgot_password.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * Formulaire de réinitialisation avec token temporaire.
     */
    #[Route('/mot-de-passe/reinitialiser/{token}', name: 'reset_password', methods: ['GET', 'POST'])]
    public function resetPassword(
        string $token,
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $user = $em->getRepository(Utilisateur::class)->findOneBy(['resetPasswordToken' => $token]);

        if (!$user instanceof Utilisateur) {
            $this->addFlash('danger', 'Ce lien de réinitialisation est invalide ou a déjà été utilisé.');

            return $this->redirectToRoute('forgot_password');
        }

        $requestedAt = $user->getResetRequestedAt();
        if (
            !$requestedAt
            || $requestedAt < (new \DateTimeImmutable(sprintf('-%d hours', self::RESET_TOKEN_EXPIRATION_HOURS)))
        ) {
            // On invalide le token expiré pour éviter les réutilisations
            $user
                ->setResetPasswordToken(null)
                ->setResetRequestedAt(null);
            $em->flush();

            $this->addFlash('danger', 'Ce lien de réinitialisation a expiré. Merci d’en demander un nouveau.');

            return $this->redirectToRoute('forgot_password');
        }

        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = (string) $form->get('password')->getData();

            $hashed = $passwordHasher->hashPassword($user, $plainPassword);
            $user
                ->setPassword($hashed)
                ->setResetPasswordToken(null)
                ->setResetRequestedAt(null);

            $em->flush();

            $this->addFlash('success', 'Votre mot de passe a été mis à jour. Vous pouvez vous connecter.');

            return $this->redirectToRoute('login');
        }

        return $this->render('auth/reset_password.html.twig', [
            'form' => $form,
        ]);
    }
}
