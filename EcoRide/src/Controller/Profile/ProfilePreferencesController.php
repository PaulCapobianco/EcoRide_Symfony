<?php

namespace App\Controller\Profile;

use App\Entity\Configuration;
use App\Entity\Parametre;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Gestion des préférences conducteur.
 */
class ProfilePreferencesController extends AbstractController
{
    /**
     * Édition des préférences conducteur (standard & personnalisées).
     */
    #[Route('/profil/preferences', name: 'profile_preferences', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, EntityManagerInterface $em): Response
    {
        /** @var Utilisateur|null $user */
        $user = $this->getUser();
        if (!$user instanceof Utilisateur) {
            return $this->redirectToRoute('login');
        }

        $configRepo = $em->getRepository(Configuration::class);
        $paramRepo  = $em->getRepository(Parametre::class);

        $configuration = $configRepo->findOneBy(['utilisateur' => $user]);
        if (!$configuration) {
            $configuration = new Configuration();
            $configuration->setUtilisateur($user);
            $em->persist($configuration);
            $em->flush();
        }

        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('profile_prefs', (string) $request->request->get('_token'))) {
                $this->addFlash('danger', 'Jeton de sécurité invalide.');
                return $this->redirectToRoute('profile_preferences');
            }

            $builtinInputs = [
                'music',
                'ac',
                'pets',
                'baggage',
                'noSmoking',
            ];

            foreach ($builtinInputs as $inputName) {
                $isChecked = $request->request->has($inputName);

                $param = $paramRepo->findOneBy([
                    'configuration' => $configuration,
                    'propriete'     => $inputName,
                ]);

                if ($isChecked && !$param) {
                    $param = new Parametre();
                    $param
                        ->setConfiguration($configuration)
                        ->setPropriete($inputName)
                        ->setValeur('1');
                    $em->persist($param);
                } elseif (!$isChecked && $param) {
                    $em->remove($param);
                } elseif ($isChecked && $param) {
                    $param->setValeur('1');
                }
            }

            $newCustom = trim((string) $request->request->get('new_custom_pref'));
            if ($newCustom !== '') {
                $custom = new Parametre();
                $custom
                    ->setConfiguration($configuration)
                    ->setPropriete('custom')
                    ->setValeur($newCustom);
                $em->persist($custom);
            }

            $em->flush();

            $this->addFlash('success', 'Vos préférences ont été mises à jour.');
            return $this->redirectToRoute('profile_preferences');
        }

        // Prépare la vue : état des cases + préférences custom
        $params = $paramRepo->findBy(
            ['configuration' => $configuration],
            ['id' => 'ASC']
        );

        $builtinsState = [
            'music'     => false,
            'ac'        => false,
            'pets'      => false,
            'baggage'   => false,
            'noSmoking' => false,
        ];
        $customPrefs = [];

        foreach ($params as $param) {
            $prop = $param->getPropriete();

            switch ($prop) {
                case 'music':
                    $builtinsState['music'] = true;
                    break;
                case 'ac':
                    $builtinsState['ac'] = true;
                    break;
                case 'pets':
                    $builtinsState['pets'] = true;
                    break;
                case 'baggage':
                    $builtinsState['baggage'] = true;
                    break;
                case 'noSmoking':
                    $builtinsState['noSmoking'] = true;
                    break;
                case 'custom':
                    $customPrefs[] = [
                        'id'    => $param->getId(),
                        'label' => $param->getValeur(),
                    ];
                    break;
            }
        }

        return $this->render('profile/preferences.html.twig', [
            'prefs' => [
                'builtins' => $builtinsState,
                'custom'   => $customPrefs,
            ],
        ]);
    }

    /**
     * Supprime une préférence personnalisée de l'utilisateur.
     */
    #[Route('/profil/preferences/{id}/supprimer', name: 'profile_preferences_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(int $id, Request $request, EntityManagerInterface $em): Response
    {
        /** @var Utilisateur|null $user */
        $user = $this->getUser();
        if (!$user instanceof Utilisateur) {
            return $this->redirectToRoute('login');
        }

        $configRepo = $em->getRepository(Configuration::class);
        $paramRepo  = $em->getRepository(Parametre::class);

        $configuration = $configRepo->findOneBy(['utilisateur' => $user]);
        if (!$configuration) {
            $this->addFlash('danger', 'Configuration introuvable.');
            return $this->redirectToRoute('profile_preferences');
        }

        /** @var Parametre|null $param */
        $param = $paramRepo->find($id);
        if (!$param || $param->getConfiguration()?->getId() !== $configuration->getId()) {
            $this->addFlash('danger', 'Préférence introuvable.');
            return $this->redirectToRoute('profile_preferences');
        }

        if ($param->getPropriete() !== 'custom') {
            $this->addFlash('danger', 'Vous ne pouvez pas supprimer cette préférence.');
            return $this->redirectToRoute('profile_preferences');
        }

        if (!$this->isCsrfTokenValid('delete_pref_' . $param->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('danger', 'Jeton de sécurité invalide.');
            return $this->redirectToRoute('profile_preferences');
        }

        $em->remove($param);
        $em->flush();

        $this->addFlash('success', 'Préférence supprimée.');
        return $this->redirectToRoute('profile_preferences');
    }
}
