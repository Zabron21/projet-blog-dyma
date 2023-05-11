<?php
require_once __DIR__ . '/database/database.php'; // On importe le fichier/script database.php et donc PDO.
/* DANS database.php : l'objet $pdo de type/classe PDO ou instance PDO est déclaré 
   dans le namespace global/contexte global de PHP. 
   Maintenant, pour avoir accès à ce $pdo, on doit require le database.php en 1ière instruction ou en 1ier require
   ici dans profile.php */
// -----------------------------------------------------------------------------------
// On importe le fichier/script security.php où la fonction isLoggedin() est définie/implémentée :
$authDB = require_once __DIR__ . '/database/security.php'; 
/* require __DIR__ . '/database/security.php' dans le contexte global de profile.php
   => on exécute alors le fichier security.php dans le contexte global de profile.php
   => on stocke l'objet de type/classe AuthDB ou instance AuthDB dans la variable $authDB
      (auteur dans notre DBB « blog »).*/
// -----------------------------------------------------------------------------------
// RÉCUPÉRATION DE TOUS LES ARTICLES DEPUIS LA BDD « blog » :
$articleDB = require_once __DIR__ . '/database/models/ArticleDB.php';
// Déclaration de $articles (tableau contenant tous nos articles) :
$articles = []; /* On l'initialise avec un tableau vide []. 
                   EN EFFET, ON N'EST PAS SÛR QU'UN UTILISATEUR QUI EST CONNECTÉ ET QUI EST SUR SA PAGE DE PROFIL
                   SOIT AUSSI FORCÉMENT UN AUTEUR-PROPRIÉTAIRE D'ARTICLES. */
// -----------------------------------------------------------------------------------
// Déclaration de la variable $currentUser (RÉCUPÉRATION D'UN UTILISATEUR QUI EST ACTUELLEMENT CONNECTÉ):
$currentUser = $authDB->isLoggedin();  // On appelle-invoque directement la fonction isLoggedin().
if (!$currentUser) {/* SI $currentUser n'existe pas (S'IL N'Y A PAS D'UTILISATEUR CONNECTÉ) ALORS : 
                       => REDIRECTION SUR LA PAGE D'ACCUEIL index.php. */
  header('Location: /');
}
// SINON, si $currentUser existe : ON A UN UTILISATEUR ACTUELLEMENT QUI EST CONNECTÉ SUR SA PAGE DE PROFIL.
// => ALORS RÉCUPÉRATION DES ARTICLES DEPUIS CET UTILISATEUR QUI EST CONNECTÉ ET QUI EST SUR SA PAGE DE PROFIL :
$articles = $articleDB->fetchUserArticle($currentUser['id']);
/* Sur l'objet $articleDB, on appelle la méthode fetchUserArticle() avec $currentUser['id'] en paramètre
   ($currentUser['id'] : ID ou identifiant de l'utilisateur qui est connecté et qui est sur sa page de profil.
                         Sur cet utilisateur précisément, on va récupérer des articles.
   RAPPEL : toute la configuration de la requête SQL est définie dans la classe ArticleDB. 
   À PARTIR DE LÀ, ON PEUT EXPLOITER TOUS LES ARTICLES DE CET UTILISATEUR QUI EST CONNECTÉ SUR SA PAGE DE PROFIL.
   CET UTILISATEUR, QUI EST CONNECTÉ SUR SA PAGE DE PROFIL, EST DONC AUSSI L'AUTEUR-PROPRIÉTAIRE D'ARTICLE(S). */
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <?php require_once 'includes/head.php' ?>
    <!-- On charge le fichier profile.css qui est dans : (dossier)public/(sous-dossier)css. -->
    <link rel="stylesheet" href="/public/css/profile.css">
    <title>Mon Profil</title><!-- Titre : Mon Profil sur l'onglet de notre page WEB. -->
  </head>
  <body>
    <div class="container">
      <?php require_once 'includes/header.php' ?>
      <div class="content">
        <h1>Mon espace</h1>
        <h2>Mes informations</h2>
        <div class="info-container">
          <ul><!-- <ul></ul> : une liste d'éléments sans ordre particulier. -->
            <li><!-- <li></li> : pour représenter un élément dans une liste. -->
                <!-- <strong></strong> : sert à marquer un texte sur lequel on veut insister.
                                         Généralement, ce texte sera affiché en gras. -->
              <!-- On affiche le Prénom de l'utilisateur qui est connecté et qui est sur sa page de profil. 
                   L'affichage se fait avec la notation raccourcie de PHP < ?php ?> -->
              <strong>Prénom :</strong>
              <p><?= $currentUser['firstname'] ?></p>
            </li>
            <li><!-- <li></li> : pour représenter un élément dans une liste-->
                <!-- <strong></strong> : sert à marquer un texte sur lequel on veut insister.
                                         Généralement, ce texte sera affiché en gras. -->
              <!-- On affiche le Nom de l'utilisateur qui est connecté et qui est sur sa page de profil. 
                   L'affichage se fait avec la notation raccourcie de PHP < ?php ?> -->
              <strong>Nom :</strong>
              <p><?= $currentUser['lastname'] ?></p>
            </li>
            <li><!-- <li></li> : pour représenter un élément dans une liste-->
                <!-- <strong></strong> : sert à marquer un texte sur lequel on veut insister.
                                         Généralement, ce texte sera affiché en gras. -->
              <!-- On affiche l'Email de l'utilisateur qui est connecté et qui est sur sa page de profil. -->
              <strong>Email :</strong>
              <p><?= $currentUser['email'] ?></p>
            </li><!-- <li></li> : pour représenter un élément dans une liste. -->
          </ul><!-- <ul></ul> : une liste d'éléments sans ordre particulier. -->
        </div>
        <h2>Mes articles</h2>
        <div class="articles-list">
          <ul>
            <!-- ON VA ITÉRER/PARCOURIR LA LISTE DE TOUS NOS ARTICLES/TABLEAU CONTENANT TOUS NOS ARTICLES. -->
            <!-- On va donc parcourir $articles (tableau contenant tous nos articles).
                 Pour parcourir un tableau, on va utiliser : foreach.
                 Syntaxe : foreach (un-itérable as une variable qui va contenir la valeur de l'itération en cours). 
                     $a   : représente l'itération en cours soit ici un article. -->
            <?php foreach($articles as $a) : ?>
            <li>
              <!-- On affiche le titre de l'article ou des articles de l'utilisateur-auteur-propriétaire d'article(s) 
                   qui est connecté et qui est sur sa page de profil. -->
              <span><?= $a['title'] ?></span>
              <!-- Cet utilisateur-auteur-propriétaires d'article(s), qui est connecté et qui est sur sa page de profil,
                   peut :
                   - soit supprimer son/ses article(s)
                   - ou soit modifier son/ses article(s). -->
              <div class="article-actions">
                <a href="/delete-article.php?id=<?= $a['id'] ?>" class="btn btn-secondary btn-small">Supprimer</a>
                <a href="/form-article.php?id=<?= $a['id'] ?>" class="btn btn-primary btn-small">Modifier</a>
              </div>
            </li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
      <?php require_once 'includes/footer.php' ?>
    </div>
  </body>
</html>