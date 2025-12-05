# Gestion de projet — EcoRide

## Approche
- **Méthode agile légère** : travail par itérations alignées sur les US (US1 à US13).
- **Priorisation** : d’abord parcours visiteur (US1–5), puis engagement/participation (US6–11), enfin back-office (US12–13).
- **Livrables intermédiaires** : chaque US validée via démo rapide (page, action, mail, crédits).

## Organisation
- **Planification** : backlog des US, définition des critères d’acceptation (chaque item de la consigne).
- **Découpage technique** : controllers/services (Covoiturage, Participation, Profil, Admin, Employé), templates, assets, e-mails.
- **Environnement** : branche principale, refactors encadrés (controllers rangés par domaines, partials Twig pour pages longues).
- **Suivi** : vérification fonctionnelle après chaque bloc (recherche, participation, lifecycle, avis), recompilation assets après modifs JS/CSS.

## Gestion des risques / choix
- **AssetMapper** sans build Node pour limiter la complexité ; Chart.js importé en local via importmap.
- **Crédits** : débit passager immédiat, crédit chauffeur après validation avis par employé (contrôle qualité).
- **Annulation** : rembourse passager, notifie participants, remet les places ; non annulable si trajet terminé.
- **Validation avis** : modération employé avant visibilité/credit chauffeur.
- **Données** : dump SQL fourni pour reproductibilité (structure + données).

## Communication / Documentation
- README installation/déploiement.
- Manuel utilisateur (parcours rôles/US, à exporter en PDF).
- Documentation technique (choix techno, architecture, déploiement).
- Dump SQL (schema+data).

## Étapes clés (alignées sur US)
- US1–4 : Accueil, menu, recherche/filtres covoiturages (ville/date, eco, prix, durée, note).
- US5 : Détail covoiturage (avis conducteur, véhicule, préférences).
- US6 : Participation avec double confirmation, contrôle crédits/places.
- US7–8 : Inscription, profil (rôle, véhicules, préférences).
- US9–11 : Publication trajets, historique, démarrer/terminer, avis/confirmation, mails, annulation.
- US12 : Espace employé (valider/refuser avis, incidents).
- US13 : Espace admin (création employé, stats journalières, crédits plateforme, suspension comptes).
