<?php

namespace App\Controller\Admin;

use App\Entity\Participation;
use App\Entity\Role;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Tableau de bord administrateur : création d'employés, stats et gestion des comptes.
 */
#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminDashboardController extends AbstractController
{
    /**
     * Page principale du back office admin : création d’employés, stats et liste.
     */
    #[Route('', name: 'admin_dashboard', methods: ['GET', 'POST'])]
    public function index(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        // Gestion du formulaire de création d’un employé (soumission sur la même page)
        if ($request->isMethod('POST')) {
            $email    = trim((string) $request->request->get('email'));
            $password = (string) $request->request->get('password');
            $prenom   = trim((string) $request->request->get('firstname'));
            $nom      = trim((string) $request->request->get('lastname'));

            if ($email && $password && $prenom && $nom) {
                $existing = $em->getRepository(Utilisateur::class)->findOneBy(['email' => $email]);
                if ($existing) {
                    $this->addFlash('danger', 'Un compte existe déjà avec cet e-mail.');
                } else {
                    $roleEmploye = $em->getRepository(Role::class)->findOneBy(['libelle' => 'ROLE_EMPLOYE']);
                    if (!$roleEmploye) {
                        $this->addFlash('danger', 'Le rôle ROLE_EMPLOYE est manquant en base.');
                    } else {
                        $employee = new Utilisateur();
                        $employee
                            ->setEmail($email)
                            ->setPrenom($prenom)
                            ->setNom($nom)
                            ->setRole($roleEmploye)
                            ->setProfilType('passenger')
                            ->setActive(true)
                            ->setEmailVerified(true);

                        $hashed = $passwordHasher->hashPassword($employee, $password);
                        $employee->setPassword($hashed);
                        $em->persist($employee);
                        $em->flush();
                        $this->addFlash('success', 'Employé créé.');
                        return $this->redirectToRoute('admin_dashboard');
                    }
                }
            } else {
                $this->addFlash('danger', 'Tous les champs sont obligatoires pour créer un employé.');
            }
        }

        $conn = $em->getConnection();

        $ridesPerDay = $conn->fetchAllAssociative("
            SELECT DATE(date_depart) AS jour, COUNT(*) AS total
            FROM covoiturage
            GROUP BY jour
            ORDER BY jour DESC
            LIMIT 30
        ");

        $gainRows = $conn->fetchAllAssociative("
            SELECT DATE(c.date_depart) AS jour, SUM(p.nb_places * c.prix_personne) AS credits
            FROM participation p
            INNER JOIN covoiturage c ON p.covoiturage_id = c.covoiturage_id
            WHERE p.confirmation_status = 'VALIDATED'
            GROUP BY jour
            ORDER BY jour DESC
            LIMIT 30
        ");

        $totalCredits = array_reduce($gainRows, static function (int $carry, array $row): int {
            return $carry + (int) ($row['credits'] ?? 0);
        }, 0);

        $page    = max(1, (int) $request->query->get('page', 1));
        $perPage = 5;

        $userRepo = $em->getRepository(Utilisateur::class);
        $qb       = $userRepo->createQueryBuilder('u')
            ->leftJoin('u.role', 'r')
            ->addSelect('r')
            ->orderBy('u.id', 'DESC');

        $totalUsers = (clone $qb)
            ->select('COUNT(u.id)')
            ->resetDQLPart('orderBy')
            ->getQuery()
            ->getSingleScalarResult();

        $users = $qb
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage)
            ->getQuery()
            ->getResult();

        $totalPages = (int) ceil($totalUsers / $perPage);

        return $this->render('admin/dashboard.html.twig', [
            'rides_per_day'   => $ridesPerDay,
            'gains_per_day'   => $gainRows,
            'total_credits'   => $totalCredits,
            'users'           => $users,
            'current_page'    => $page,
            'total_pages'     => $totalPages,
        ]);
    }

    /**
     * Active/désactive un compte via la liste du tableau de bord.
     */
    #[Route('/utilisateurs/{id}/toggle', name: 'admin_user_toggle', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function toggleUser(Utilisateur $utilisateur, EntityManagerInterface $em, Request $request): Response
    {
        if (!$this->isCsrfTokenValid('toggle_user_' . $utilisateur->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('danger', 'Jeton CSRF invalide.');
            return $this->redirectToRoute('admin_dashboard');
        }

        $utilisateur->setActive(!$utilisateur->isActive());
        $em->flush();

        $this->addFlash('info', sprintf(
            'Compte %s %s.',
            $utilisateur->isActive() ? 'réactivé' : 'suspendu',
            $utilisateur->getEmail()
        ));

        return $this->redirectToRoute('admin_dashboard');
    }

    /**
     * Ajoute ponctuellement des crédits manquants (litiges).
     */
    #[Route('/utilisateurs/{id}/credit', name: 'admin_user_credit', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function creditUser(Utilisateur $utilisateur, Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->isCsrfTokenValid('credit_user_' . $utilisateur->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('danger', 'Jeton CSRF invalide.');
            return $this->redirectToRoute('admin_dashboard');
        }

        $amount = (int) $request->request->get('amount', 0);
        if ($amount <= 0) {
            $this->addFlash('danger', 'Montant invalide.');
            return $this->redirectToRoute('admin_dashboard');
        }

        $utilisateur->setCredit($utilisateur->getCredit() + $amount);
        $em->flush();

        $this->addFlash('success', sprintf(
            '%d crédits ajoutés au compte %s.',
            $amount,
            $utilisateur->getEmail()
        ));

        return $this->redirectToRoute('admin_dashboard');
    }
}
