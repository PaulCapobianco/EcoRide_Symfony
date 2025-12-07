<?php

namespace App\Controller\Profile;

use App\Entity\Utilisateur;
use App\Form\ProfilePasswordType;
use App\Form\ProfileType;
use App\Service\EmailVerificationService;
use App\Service\ProfileHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Édition du compte profil (infos, email, rôle).
 */
class ProfileAccountController extends AbstractController
{
    public function __construct(private ProfileHelper $profileHelper)
    {
    }

    /**
     * Page “Modifier mon profil” : coordonnées + mot de passe.
     */
    #[Route('/profil/edition', name: 'profile_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(
        Request $request,
        EntityManagerInterface $em,
        EmailVerificationService $emailVerificationService,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        /** @var Utilisateur|null $user */
        $user = $this->getUser();

        if (!$user instanceof Utilisateur) {
            return $this->redirectToRoute('login');
        }

        $originalEmail = $user->getEmail();

        $form = $this->createForm(ProfileType::class, $user, [
            'include_profile_type' => false,
        ]);
        $passwordForm = $this->createForm(ProfilePasswordType::class);

        $form->handleRequest($request);
        $passwordForm->handleRequest($request);

        if ($passwordForm->isSubmitted()) {
            if ($passwordForm->isValid()) {
                $currentPassword = (string) $passwordForm->get('currentPassword')->getData();
                if (!$passwordHasher->isPasswordValid($user, $currentPassword)) {
                    $this->addFlash('danger', 'Votre mot de passe actuel est incorrect.');
                    return $this->redirectToRoute('profile_edit');
                }

                $newPassword = (string) $passwordForm->get('newPassword')->getData();
                $hashed      = $passwordHasher->hashPassword($user, $newPassword);
                $user
                    ->setPassword($hashed)
                    ->setResetPasswordToken(null)
                    ->setResetRequestedAt(null);

                $em->flush();

                $this->addFlash('success', 'Votre mot de passe a été mis à jour.');
                return $this->redirectToRoute('profile_edit');
            }

            $this->addFlash('danger', 'Merci de corriger les erreurs du formulaire de mot de passe.');
        }

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $repo           = $em->getRepository(Utilisateur::class);
                $submittedEmail = trim($user->getEmail());
                $emailChanged   = $submittedEmail !== $originalEmail;

                if ($emailChanged) {
                    $existing = $repo->createQueryBuilder('u')
                        ->where('(u.email = :email OR u.pendingEmail = :email) AND u != :user')
                        ->setParameter('email', $submittedEmail)
                        ->setParameter('user', $user)
                        ->setMaxResults(1)
                        ->getQuery()
                        ->getOneOrNullResult();

                    if ($existing) {
                        $this->addFlash('danger', 'Un autre compte utilise déjà cette adresse e-mail.');
                        $user->setEmail($originalEmail);
                        return $this->redirectToRoute('profile_edit');
                    }

                    $user
                        ->setPendingEmail($submittedEmail)
                        ->setEmail($originalEmail);

                    $emailVerificationService->sendVerificationEmail($user, $submittedEmail, true);
                    $this->addFlash(
                        'info',
                        sprintf(
                            'Un e-mail de confirmation a été envoyé à %s. Le changement sera effectif après validation.',
                            $submittedEmail
                        )
                    );
                }

                $em->flush();
                $this->addFlash('success', 'Votre profil a été mis à jour.');
                return $this->redirectToRoute('profile');
            } else {
                $this->addFlash('danger', 'Merci de corriger les erreurs dans le formulaire.');
            }
        }

        return $this->render('profile/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'password_form' => $passwordForm->createView(),
        ]);
    }

    /**
     * Mise à jour du rôle principal (passager / conducteur / les deux).
     */
    #[Route('/profil/role', name: 'profile_role_update', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function updateRole(Request $request, EntityManagerInterface $em): Response
    {
        /** @var Utilisateur|null $user */
        $user = $this->getUser();

        if (!$user instanceof Utilisateur) {
            return $this->redirectToRoute('login');
        }

        if (!$this->isCsrfTokenValid('profile_role', (string) $request->request->get('_token'))) {
            $this->addFlash('danger', 'Jeton de sécurité invalide.');
            return $this->redirectToRoute('profile');
        }

        $profilType = (string) $request->request->get('profil_type', 'passenger');
        $allowed    = ['passenger', 'driver', 'both'];

        if (!in_array($profilType, $allowed, true)) {
            $this->addFlash('danger', 'Rôle demandé invalide.');
            return $this->redirectToRoute('profile');
        }

        if ($profilType === 'passenger') {
            $user->setProfilType('passenger');
            $em->flush();

            $this->addFlash('success', 'Votre rôle principal a été mis à jour.');
            return $this->redirect($this->generateUrl('profile') . '#section-role');
        }

        $hasVehicle = $this->profileHelper->userHasAtLeastOneVehicle($user);

        if (!$hasVehicle) {
            $this->addFlash(
                'info',
                'Pour devenir conducteur, veuillez d\'abord ajouter au moins un véhicule.'
            );

            return $this->redirectToRoute('profile_vehicle_new', [
                'target_role' => $profilType,
            ]);
        }

        $user->setProfilType($profilType);
        $em->flush();

        $this->addFlash('success', 'Votre rôle principal a été mis à jour.');
        return $this->redirect($this->generateUrl('profile') . '#section-role');
    }

    /**
     * Mise à jour rapide depuis la page profil (nom, email, etc.).
     */
    #[Route('/profil/compte', name: 'profile_update', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function updateAccount(
        Request $request,
        EntityManagerInterface $em,
        EmailVerificationService $emailVerificationService
    ): Response {
        /** @var Utilisateur|null $user */
        $user = $this->getUser();

        if (!$user instanceof Utilisateur) {
            return $this->redirectToRoute('login');
        }

        $originalEmail = $user->getEmail();

        if (!$this->isCsrfTokenValid('profile_account', (string) $request->request->get('_token'))) {
            $this->addFlash('danger', 'Jeton de sécurité invalide.');
            return $this->redirectToRoute('profile');
        }

        $firstname = trim((string) $request->request->get('firstname'));
        $lastname  = trim((string) $request->request->get('lastname'));
        $email     = trim((string) $request->request->get('email'));
        $phone     = trim((string) $request->request->get('phone'));
        $address   = trim((string) $request->request->get('address'));
        $pseudo    = trim((string) $request->request->get('username'));

        $errors = [];

        if ($firstname === '') {
            $errors[] = 'Le prénom est obligatoire.';
        }
        if ($lastname === '') {
            $errors[] = 'Le nom est obligatoire.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Adresse e-mail invalide.';
        }

        $repo     = $em->getRepository(Utilisateur::class);
        $existing = $repo->createQueryBuilder('u')
            ->where('(u.email = :email OR u.pendingEmail = :email) AND u != :user')
            ->setParameter('email', $email)
            ->setParameter('user', $user)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
        if ($existing) {
            $errors[] = 'Un autre compte utilise déjà cette adresse e-mail.';
        }

        if (!empty($errors)) {
            foreach ($errors as $err) {
                $this->addFlash('danger', $err);
            }
            return $this->redirect($this->generateUrl('profile') . '#section-compte');
        }

        $user
            ->setPrenom($firstname)
            ->setNom($lastname)
            ->setTelephone($phone !== '' ? $phone : null)
            ->setAdresse($address !== '' ? $address : null)
            ->setPseudo($pseudo !== '' ? $pseudo : null);

        if ($email !== $originalEmail) {
            $user
                ->setPendingEmail($email)
                ->setEmail($originalEmail);

            $emailVerificationService->sendVerificationEmail($user, $email, true);
            $this->addFlash(
                'info',
                sprintf(
                    'Un e-mail de confirmation a été envoyé à %s. Le changement sera effectif après validation.',
                    $email
                )
            );
        }

        $em->flush();

        $this->addFlash('success', 'Vos informations ont été mises à jour.');
        return $this->redirect($this->generateUrl('profile') . '#section-compte');
    }


    /**
     * Renvoie un e-mail de vérification si l’utilisateur ne l’a pas validé.
     */
    #[Route('/profil/email/resend', name: 'profile_email_resend', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function resendVerificationEmail(
        Request $request,
        EntityManagerInterface $em,
        EmailVerificationService $emailVerificationService
    ): Response {
        /** @var Utilisateur|null $user */
        $user = $this->getUser();

        if (!$user instanceof Utilisateur) {
            return $this->redirectToRoute('login');
        }

        if (!$this->isCsrfTokenValid('resend_verification_email', (string) $request->request->get('_token'))) {
            $this->addFlash('danger', 'Jeton de sécurité invalide.');
            return $this->redirectToRoute('profile');
        }

        if ($user->isEmailVerified()) {
            $this->addFlash('info', 'Votre adresse e-mail est déjà vérifiée.');
            return $this->redirectToRoute('profile');
        }

        $isEmailChange = $user->getPendingEmail() !== null;
        $targetEmail   = $isEmailChange ? $user->getPendingEmail() : $user->getEmail();

        if (!$targetEmail) {
            $this->addFlash('danger', 'Adresse e-mail introuvable. Merci de mettre à jour votre profil.');
            return $this->redirectToRoute('profile');
        }

        $emailVerificationService->sendVerificationEmail($user, $targetEmail, $isEmailChange);
        $em->flush();

        $this->addFlash('success', sprintf('Un e-mail de vérification a été envoyé à %s.', $targetEmail));
        return $this->redirectToRoute('profile');
    }

}
