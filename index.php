<?php
require_once __DIR__ . '/database/database.php'; // On importe le fichier/script database.php et donc PDO.
/* DANS database.php : l'objet $pdo de type/classe PDO ou instance PDO est déclaré 
   dans le namespace global/contexte global de PHP. 
   Maintenant, pour avoir accès à ce $pdo, on doit require le database.php en 1ière instruction ou en 1ier require
   ici dans index.php */
// ----------------------------------------------------------------------------------------------
// On importe le fichier/script security.php où la fonction isLoggedin() est définie/implémentée :
$authDB = require __DIR__ . '/database/security.php'; 
/* require __DIR__ . '/database/security.php' dans le contexte global de index.php
   => on exécute alors le fichier security.php dans le contexte global de index.php
   => on stocke l'objet de type/classe AuthDB ou instance AuthDB dans la variable $authDB
      (auteur dans notre DBB « blog »).*/
// ----------------------------------------------------------------------------------------------
// Déclaration de la variable $currentUser (RÉCUPÉRATION D'UN UTILISATEUR QUI EST ACTUELLEMENT CONNECTÉ)::
$currentUser = $authDB->isLoggedin(); /* On appelle-invoque la fonction isLoggedin() depuis l'instance authDB.
                                         ou depuis un objet de type/classe authDB. */
/* RÉCUPÉRATION DE TOUS LES ARTICLES DEPUIS LA BDD « blog » : */
$articleDB = require_once __DIR__ . '/database/models/ArticleDB.php';// Tous les articles sont stockés dans $articleDB.
$articles = $articleDB->fetchAll();
/* Sur l'objet $articleDB, on appelle la méthode fetchAll().
   RAPPEL : toute la configuration de la requête SQL est définie dans la classe ArticleDB. */
// RÉCUPERATION DES CATÉGORIES : ON DOIT RÉCUPERER LA LISTE/LE TABLEAU DE TOUTES NOS CATÉGORIES.
$categories = [];// On initialise, donne comme valeur, à notre liste de catégories : un tableau vide [].
// ----------------------------------------------------------------------------------------------
// RÉCUPÉRATION DES CATEGORIES CONCERNANT TOUS NOS ARTICLES : :
$_GET = filter_input_array(INPUT_GET, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
// SELECTION DE LA CATÉGORIE DANS LE MENU "CATÉGORIES" : 
$selectedCat = $_GET['cat'] ?? '';
/* Opérateur de fusion NULL ?? : s'il n'y a rien dans $selectedCat (donc pas de catégorie choisie/sélectionnée
                                 dans le menu "catégories"), on met une chaîne vide ''. */
// ------------------------------------------------------------------------------------------------
// Vérifions qu'il y a bien quelque chose dans $articles (tableau contenant tous nos articles) : 
if (count($articles)) { // S'IL y a des articles dans $articles (tableau contenant tous nos articles) ALORS :
  // RÉCUPERATION DE LA CATÉGORIE D'UN ARTICLE EN PARTICULIER :    
  $cattmp = array_map(fn ($a) => $a['category'],  $articles);
  // ON A RETOURNÉ LA CATÉGORIE DE CHAQUE ARTICLE DE NOTRE BLOG.
  /* PUIS, ON COMPTE LE NOMBRE D'ARTICLES PAR CATÉGORIE : */
  $categories = array_reduce($cattmp, function ($acc, $cat) {
    if (isset($acc[$cat])) {
      $acc[$cat]++;
    } else {
      $acc[$cat] = 1;
    }
    return $acc;
  }, []);
  // ON CONNAÎT LE NOMBRE D'ARTICLES PAR CATÉGORIE.
  // MAINTENANT, ON REGROUPE/CLASSE/TRIE/RÉPERTORIE TOUS LES ARTICLES PROPRES PAR CATÉGORIE.
  $articlePerCategories = array_reduce($articles, function ($acc, $article) {
    if (isset($acc[$article['category']])) {
      $acc[$article['category']] = [...$acc[$article['category']], $article];
    } else {
      $acc[$article['category']] = [$article];
    }
    return $acc;
  }, []);
  // ON A MAINTENANT UN TABLEAU CONTENANT TOUS NOS ARTICLES QUI SONT CLASSÉS/RÉPERTORIÉS/REGROUPÉS/TRIÉS PAR CATÉGORIE.
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php require_once 'includes/head.php' ?>
  <link rel="stylesheet" href="/public/css/index.css">
  <title>Blog</title>
</head>
<body>
  <div class="container">
    <?php require_once 'includes/header.php' ?>
    <div class="content">
      <div class="newsfeed-container">
        <ul class="category-container">
          <li class=<?= $selectedCat ? '' : 'cat-active' ?>><a href="/">Tous les articles <span class="small">
            (<?= count($articles) ?>)</span></a></li>
          <?php foreach ($categories as $catName => $catNum) : ?>
            <li class=<?= $selectedCat ===  $catName ? 'cat-active' : '' ?>><a href="/?cat=<?= $catName ?>">
            <?= $catName ?><span class="small">(<?= $catNum ?>)</span> </a></li>
          <?php endforeach; ?>
        </ul>
        <div class="newsfeed-content">
          <?php if (!$selectedCat) : ?>
            <?php foreach ($categories as $cat => $num) : ?>
              <h2><?= $cat ?></h2>
              <div class="articles-container">
                <?php foreach ($articlePerCategories[$cat] as $a) : ?>
                  <a href="/show-article.php?id=<?= $a['id'] ?>" class="article block">
                    <div class="overflow">
                      <div class="img-container" style="background-image:url(<?= $a['image'] ?>"></div>
                    </div>
                    <h3><?= $a['title'] ?></h3>
                    <?php if ($a['author']) : ?> <!-- SI l'article actuel a un auteur/propriétaire ALORS : 
                      => on affiche le Prénom et le Nom de l'utilisateur-auteur-propriétaire de cet article 
                      précis-en particulier. L'affichage est fait avec la notation raccourcie de PHP < ?php ?>. -->
                      <div class="article-author">
                        <p><?= $a['firstname'].' '.$a['lastname'] ?></p>
                      </div>
                    <?php endif ; ?>
                  </a>
                <?php endforeach; ?>
              </div>
            <?php endforeach; ?>
          <?php else : ?>
            <h2><?= $selectedCat ?></h2>
            <div class="articles-container">
              <?php foreach ($articlePerCategories[$selectedCat] as $a) : ?>
                <a href="/show-article.php?id=<?= $a['id'] ?>" class="article block">
                  <div class="overflow">
                    <div class="img-container" style="background-image:url(<?= $a['image'] ?>"></div>
                  </div>
                  <h3><?= $a['title'] ?></h3>
                </a>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <?php require_once 'includes/footer.php' ?>
  </div>
</body>
</html>