<?php
require_once './database/database.php'; // On importe le fichier/script database.php et donc PDO.
/* DANS database.php : l'objet $pdo de type/classe PDO ou instance PDO est déclaré 
   dans le namespace global/contexte global de PHP. 
   Maintenant, pour avoir accès à ce $pdo, on doit require le database.php en 1ière instruction ou en 1ier require
   ici dans auth-logout.php */
// -----------------------------------------------------------------------------------
// On importe le fichier/script security.php où la fonction isLoggedin() est définie/implémentée :
$authDB = require __DIR__ . '/database/security.php'; 
/* require __DIR__ . '/database/security.php' dans le contexte global de auth-logout.php
   => on exécute alors le fichier security.php dans le contexte global de auth-logout.php
   => on stocke l'objet de type/classe AuthDB ou instance AuthDB dans la variable $authDB
      (auteur dans notre DBB « blog »).*/
// -----------------------------------------------------------------------------------
/* UN UTILISATEUR EST DÉJÀ CONNECTÉ. IL EXISTE DONC UNE SESSION-UTILISATEUR AVEC UN ID DE SESSION PRÉCIS-EN PARTICULIER.
   => RÉCUPÉRATION DE CET ID DE SESSION-UTILISATEUR PRÉCIS-EN PARTICULIER : */
$sessionId = $_COOKIE['session'] ?? ''; 
/* Opérateur de fusion NULL ?? : s'il n'y a pas de cookie 'session' de l'utilisateur,
                                 on met une chaîne vide ''. */
if ($sessionId) {
  /* SI $sessionId existe (SI on a bien récupéré un ID de SESSION ou une SESSION-UTILISATEUR en particulier),
     ALORS : => SUPPRESSION DE CETTE SESSION-UTILISATEUR EN PARTICULIER.
     DANS CE CAS : 
     - SUPPRESSION, PLUS PRÉCISEMENT, DE L'ID DE SESSION DE CETTE SESSION-UTILISATEUR EN PARTICULIER.
     - ET VIDAGE LA CLÉ 'session' DANS LES COOKIES DE L'UTILISATEUR QUI ÉTAIT CONNECTÉ.
     CONSÉQUENCE DIRECTE : L'UTILISATEUR, QUI ETAIT LIÉ A CETTE SESSION-UTILISATEUR LORSQU'IL ETAIT CONNECTÉ,
                           EST MAINTENANT/DORÉNAVANT DECONNECTÉ.*/
      $authDB->logout($sessionId);
   /* Sur l'objet $authDB, on appelle la méthode logout() avec $sessionId en paramètre
      ($sessionId) = identifiant d'une session-utilisateur en particulier.
      RAPPEL : toute la configuration de la requête SQL est définie dans la classe AuthDB. */
   // UNE FOIS QUE L'UTILISATEUR EST DECONNECTÉ, IL EST REDIRIGÉ SUR LA PAGE DE CONNEXION (auth-login.php) :
      header('Location: /auth-login.php');     
  }
// ON A NOTRE MÉCANIQUE DE DÉCONNEXION/LOGOUT QUI DEVRAIT BIEN FONCTIONNER !
?>