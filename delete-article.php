<?php
require_once __DIR__ . '/database/database.php'; // On importe le fichier/script database.php et donc PDO.
/* DANS database.php : l'objet $pdo de type/classe PDO ou instance PDO est déclaré 
   dans le namespace global/contexte global de PHP. 
   Maintenant, pour avoir accès à ce $pdo, on doit require le database.php en 1ière instruction ou en 1ier require
   ici dans delete-article.php */
// -----------------------------------------------------------------------------------
// On importe le fichier/script security.php où la fonction isLoggedin() est définie/implémentée :
$authDB = require __DIR__ . '/database/security.php'; 
/* require __DIR__ . '/database/security.php' dans le contexte global de delete-article.php
   => on exécute alors le fichier security.php dans le contexte global de delete-article.php 
   => on stocke l'objet de type/classe AuthDB ou instance AuthDB dans la variable $authDB
      (auteur dans notre DBB « blog »).*/
// ----------------------------------------------------------------------------------------------
// Déclaration de la variable $currentUser (RÉCUPÉRATION D'UN UTILISATEUR QUI EST ACTUELLEMENT CONNECTÉ) :
$currentUser = $authDB->isLoggedin();  // On appelle-invoque directement la fonction isLoggedin().
// ----------------------------------------------------------------------------------------------
if ($currentUser) {/* SI $currentUser existe (SI UN UTILISATEUR EST BIEN CONNECTÉ) ALORS :
                      => RÉCUPÉRATION DE TOUS LES ARTICLES DEPUIS LA BDD « blog ». */
    $articleDB = require_once __DIR__ . '/database/models/ArticleDB.php';
    // METHODE GET : POUR RÉCUPÉRER CETTE FOIS-CI L'ID-IDENTIFIANT D'UN ARTICLE PRÉCIS-EN PARTICULIER :
    $_GET = filter_input_array(INPUT_GET, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    // RÉCUPÉRATION DE L'ID-IDENTIFIANT D'UN ARTICLE PRÉCIS-EN PARTICULIER DEPUIS L'URL :  
    $id = $_GET['id'] ?? ''; // ON RÉCUPÈRE L'IDENTIFIANT D'UN ARTICLE PRÉCIS-EN PARTICULIER DEPUIS L'URL.
    /* Opérateur de fusion NULL ?? : s'il n'y a rien dans $id (on ne récupère rien), on met une chaîne vide ''. */

    if ($id) {/* SI $id existe (si on récupère bien l'identifiant d'un article précis-en particulier) ALORS : */
    $article = $articleDB->fetchOne($id); /* => l'article récupéré est stocké dans la variable $article. */
      /* Sur l'objet $articleDB, on appelle la méthode fetchOne() avec $id en paramètre
         ($id = identifiant d'un article précis-en particulier).
         RAPPEL : toute la configuration de la requête SQL est définie dans la classe ArticleDB. */
      // ON A BIEN RÉCUPERÉ NOTRE ARTICLE PRÉCIS-EN PARTICULIER.
      if ($article['author'] === $currentUSer['id']) {
      /* MAINTENANT SI L'UTILISATEUR, QUI EST ACTUELLEMENT CONNECTÉ, EST BIEN L'AUTEUR DE 
         CET ARTICLE PRÉCIS-EN PARTICULIER ALORS :
         => IL A LE DROIT DE SUPPRIMER SON ARTICLE. */
         $articleDB->deleteOne($id);
      /* Sur l'objet $articleDB, on appelle la méthode deleteOne avec $id en paramètre
         ($id = identifiant d'un article précis-en particulier).
         RAPPEL : toute la configuration de la requête SQL est définie dans la classe ArticleDB. */
      }
    }
}
/* SI $currentUser n'existe pas (SI L'UTILISATEUR ACTUEL N'EST PAS CONNECTÉ OU S'IL N'Y A PAS D'UTILISATEUR CONNECTÉ)
   ALORS : REDIRECTION SUR LA PAGE D'ACCUEIL index.php. */
header('Location: /');
?>