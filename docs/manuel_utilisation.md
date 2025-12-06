# Manuel d’utilisation — EcoRide

Ce document décrit les parcours utilisateurs clés, les rôles et les points d’entrée. Exportez-le en PDF (impression du fichier) pour la remise finale. Les identifiants restent à renseigner (à compléter après création en base).

## Accès
- URL locale : `http://127.0.0.1:8000/`
- Comptes pour tester :
  - Administrateur : `admin@ecoride.fr` / `123456`
  - Employé : `employe@ecoride.fr` / `123456`
  - Utilisateur : `user@ecoride.fr` / `123456`

## Rôles
- **Utilisateur** : cherche, participe, dépose des avis.
- **Chauffeur** (profil utilisateur en mode conducteur) : publie, démarre, termine des trajets.
- **Employé** : valide/refuse les avis, consulte les incidents.
- **Administrateur** : crée des comptes employés, consulte les stats, suspend des comptes.

## Parcours Utilisateur
1) **Inscription**  
   - Page : `S’inscrire` (header > Mon compte > Créer un compte).  
   - Un e-mail de vérification est envoyé ; le compte s’active après clic.
2) **Connexion**  
   - Page : `Connexion` (header > Mon compte).
   - Lien « Mot de passe oublié ? » → formulaire `/mot-de-passe/oublie` (saisie de l’e-mail, réception d’un lien valable 2 h).  
   - Lien reçu par e-mail → formulaire sécurisé pour définir un nouveau mot de passe.
3) **Acheter des crédits**  
   - Lien header « Acheter des crédits ».  
   - Choisir un montant, cliquer sur « Payer » → crédits ajoutés.
4) **Chercher/Participer à un covoiturage**  
   - Page `Covoiturages` (recherche + filtres).  
   - Bouton « Participer » (déduction immédiate des crédits, place réservée).
5) **Avis passager**  
   - Après un trajet terminé par le chauffeur : page `Mes avis trajets` (profil).  
   - Laisser une note/commentaire → avis en attente de validation employé.
6) **Modifier ses infos / mot de passe**  
   - Profil > « Modifier mon profil » : informations personnelles + avatar.  
   - Même page, bloc « Sécurité / mot de passe » pour changer son mot de passe actuel (contrôle du mot de passe courant + nouveau mot de passe fort).

## Parcours Chauffeur
1) **Passer en mode conducteur**  
   - Profil > « Rôle & statut » → choisir « Conducteur » ou « Les deux ».  
   - Ajouter au moins un véhicule (Profil > Véhicules).
2) **Publier un trajet**  
   - Header « Publier un trajet » (ou Profil > Rôle & statut si redirigé).  
   - Saisir départ/arrivée, horaires, prix, véhicule.
   - **Définir ses préférences** : Profil > « Préférences conducteur » permet de sélectionner les options prédéfinies (musique, climatisation, animaux, bagages, non-fumeur…) et d’ajouter/cacher des préférences personnalisées visibles par les passagers.
3) **Démarrer / Terminer**  
   - Profil > Mes trajets : bouton « Démarrer », puis « Arrivée à destination ».  
   - Les passagers reçoivent un e-mail pour confirmer/laisser un avis.  
   - Le chauffeur est crédité lorsque l’avis est validé par un employé.
4) **Annuler un covoiturage**  
   - Chauffeur ou passager : annulation possible depuis l’historique des trajets.  
   - Effets : remboursement des passagers, remise à jour des places, mail d’info aux participants.  
   - Un trajet terminé ou passé n’est plus annulable.

## Parcours Employé
1) **Accès** : menu compte > « Espace employé ».  
2) **Avis à valider** : liste des avis en statut `A_VALIDER`.  
   - Bouton « Valider & créditer » : crédite automatiquement le chauffeur concerné (prise en compte du nombre de places achetées) s’il ne l’a pas encore été.  
   - Bouton « Refuser » : aucune rémunération chauffeur, l’avis passe en `REFUSE`.  
3) **Incidents** : section « Covoiturages signalés » (commentaires négatifs/`REPORTED`).  
   - Formulaire « Créditer le passager » : permet à l’employé d’attribuer manuellement des crédits au passager lésé (montant libre + note interne conservée dans l’incident).  
   - Après validation, le crédit est ajouté instantanément au compte du passager, l’incident est marqué comme résolu et disparaît de la liste.

## Parcours Administrateur
1) **Accès** : menu compte > « Espace admin ».  
2) **Création d’employés** : formulaire de création (email, prénom, nom, mot de passe).  
3) **Statistiques** : graphiques « Covoiturages par jour » et « Crédits gagnés par jour ».  
4) **Gestion des comptes** : suspension/réactivation d’un utilisateur/employé.

## Notifications E-mail
- Vérification de compte / changement d’e-mail.
- Réinitialisation de mot de passe (lien valable 2 h) ainsi que confirmation de changement via le profil.
- Invitation à confirmer un trajet (passagers) après clôture par le conducteur.
- Notification d’annulation, messages contact.
