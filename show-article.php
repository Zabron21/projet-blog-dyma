<?php
require_once __DIR__ . '/database/database.php'; // On importe le fichier/script database.php et donc PDO.
/* DANS database.php : l'objet $pdo de type/classe PDO ou instance PDO est déclaré 
   dans le namespace global/contexte global de PHP. 
   Maintenant, pour avoir accès à ce $pdo, on doit require le database.php en 1ière instruction ou en 1ier require
   ici dans show-article.php */
// ----------------------------------------------------------------------------------------------
// On importe le fichier/script security.php où la fonction isLoggedin() est définie/implémentée :
$authDB = require __DIR__ . '/database/security.php'; 
/* require __DIR__ . '/database/security.php' dans le contexte global de show-article.php
   => on exécute alors le fichier security.php dans le contexte global de show-article.php
   => on stocke l'objet de type/classe AuthDB ou instance AuthDB dans la variable $authDB
      (auteur dans notre DBB « blog »).*/
// ----------------------------------------------------------------------------------------------
// Déclaration de la variable $currentUser (RÉCUPÉRATION D'UN UTILISATEUR QUI EST ACTUELLEMENT CONNECTÉ):
$currentUser = $authDB->isLoggedin();  // On appelle-invoque directement la fonction isLoggedin().
// ----------------------------------------------------------------------------------------------
// RÉCUPÉRATION DE TOUS LES ARTICLES DEPUIS LA BDD « blog » :
$articleDB = require_once __DIR__ . '/database/models/ArticleDB.php';
// RÉCUPÉRATION DE L'ID-IDENTIFIANT D'UN ARTICLE PRÉCIS-EN PARTICULIER DEPUIS L'URL : 
$_GET = filter_input_array(INPUT_GET, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$id = $_GET['id'] ?? '';// ON RÉCUPÈRE L'ID-IDENTIFIANT D'UN ARTICLE PRÉCIS-EN PARTICULIER DEPUIS L'URL.
// ------------------------------------------------------------------------------------------------
if (!$id) { /* SI $id n'existe pas (si on ne récupère pas d'identifiant concernant un article précis-en particulier) 
               ALORS REDIRECTION VERS LA PAGE D'ACCUEIL (index.php) :*/
  header('Location: /');
} else {    /* SINON, si $id existe (si on récupère l'identifiant d'un article précis-en particulier) ALORS :*/
  $article = $articleDB->fetchOne($id);
  /* Sur l'objet $articleDB, on appelle la méthode fetchOne() avec $id en paramètre
     ($id = identifiant d'un article précis-en particulier).
     RAPPEL : toute la configuration de la requête SQL est définie dans la classe ArticleDB. */
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <?php require_once 'includes/head.php' ?>
  <link rel="stylesheet" href="/public/css/show-article.css">
  <title>Article</title>
</head>
<body>
  <div class="container">
    <?php require_once 'includes/header.php' ?>
    <div class="content">
      <div class="article-container">
        <a class="article-back" href="/">Retour à la liste des articles</a>
        <div class="article-cover-img" style="background-image:url(<?= $article['image'] ?>)"></div>
        <h1 class="article-title"><?= $article['title'] ?></h1>
        <div class="separator"></div>
        <p class="article-content"><?= $article['content'] ?></p>
        <!-- En-dessous du contenu d'un article précis-en particulier, on affiche 
             le Prénom et le Nom de l'utilisateur-auteur-propriétaire de cet article précis-en particulier.
             L'affichage est fait avec la notation raccourcie de PHP < ?php ?>. -->
        <p class="article-author"><?= $article['firstname']. ' ' . $article['lastname']?></p>
        <?php if($currentUser && $currentUser['id'] === $article['author']) : ?>
        <!-- SI UN UTILISATEUR EST CONNECTÉ ($currentUser) ET EST SUR LA PAGE D'UN ARTICLE PRÉCIS-EN PARTICULIER 
             ET S'IL EST, EN PLUS, LUI-MÊME L'AUTEUR-PROPRIÉTAIRE DE CET ARTICLE PRÉCIS-EN PARTICULIER 
             QU'IL EST EN TRAIN DE CONSULTER : $currentUser['id'] === $article['author'] 
             ALORS IL PEUT : 
             - SOIT ÉDITER SON ARTICLE,
             - OU SOIT SUPPRIMER SON ARTICLE. -->
          <div class="action">
            <a class="btn btn-secondary" href="/delete-article.php?id=<?= $article['id'] ?>">Supprimer</a>
            <a class="btn btn-primary" href="/form-article.php?id=<?= $article['id'] ?>">Editer l'article</a>
          </div>
        <?php endif; ?>
      </div>
    </div>
    <?php require_once 'includes/footer.php' ?>
  </div>
</body>
</html>