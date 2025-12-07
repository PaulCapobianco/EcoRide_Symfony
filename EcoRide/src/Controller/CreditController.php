<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Gestion de l'achat fictif de crédits par un utilisateur connecté.
 */
class CreditController extends AbstractController
{
    /**
     * Affiche le formulaire d'achat et crédite directement le compte (simulation de paiement).
     */
    #[Route('/credits/acheter', name: 'credit_buy', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function buy(Request $request, EntityManagerInterface $em): Response
    {
        /** @var Utilisateur|null $user */
        $user = $this->getUser();
        if (!$user instanceof Utilisateur) {
            return $this->redirectToRoute('login');
        }

        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('buy_credits', (string) $request->request->get('_token'))) {
                $this->addFlash('danger', 'Jeton CSRF invalide.');
                return $this->redirectToRoute('credit_buy');
            }

            $amount = (int) $request->request->get('amount', 0);
            if ($amount <= 0) {
                $this->addFlash('danger', 'Montant invalide.');
                return $this->redirectToRoute('credit_buy');
            }


            $user->setCredit($user->getCredit() + $amount);
            $em->flush();

            $this->addFlash('success', sprintf(
                'Paiement validé : %d crédit(s) ajoutés à votre compte.',
                $amount,
            ));
            return $this->redirectToRoute('profile');
        }

        return $this->render('credits/buy.html.twig');
    }
}
