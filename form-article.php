<?php
require_once __DIR__ . '/database/database.php'; // On importe le fichier/script database.php et donc PDO.
/* DANS database.php : l'objet $pdo de type/classe PDO ou instance PDO est déclaré 
   dans le namespace global/contexte global de PHP. 
   Maintenant, pour avoir accès à ce $pdo, on doit require le database.php en 1ière instruction ou en 1ier require
   ici dans form-article.php */
// -----------------------------------------------------------------------------------
// On importe le fichier/script security.php où la fonction isLoggedin() est définie/implémentée :
$authDB= require_once __DIR__ . '/database/security.php'; 
/* require __DIR__ . '/database/security.php' dans le contexte global de form-article.php
   => on exécute alors le fichier security.php dans le contexte global de form-article.php
   => on stocke l'objet de type/classe AuthDB ou instance AuthDB dans la variable $authDB
      (auteur dans notre DBB « blog »).*/
// ----------------------------------------------------------------------------------------------
// Déclaration de la variable $currentUser (RÉCUPÉRATION D'UN UTILISATEUR QUI EST ACTUELLEMENT CONNECTÉ):
$currentUser = $authDB->isLoggedin();  // On appelle-invoque directement la fonction isLoggedin().
if (!$currentUser) {/* SI $currentUser n'existe pas (S'IL N'Y A PAS D'UTILISATEUR CONNECTÉ)
                       ALORS REDIRECTION SUR LA PAGE D'ACCUEIL index.php. */
  header('Location: /');
}
/* SINON, si $currentUser existe (S'IL Y A UN UTILISATEUR QUI EST CONNECTÉ) ALORS : 
   => RÉCUPÉRATION DE TOUS LES ARTICLES DEPUIS LA BDD « blog » : */
$articleDB = require_once __DIR__ . '/database/models/ArticleDB.php';
const ERROR_REQUIRED = 'Veuillez renseigner ce champ';
const ERROR_TITLE_TOO_SHORT = 'Le titre est trop court';
const ERROR_CONTENT_TOO_SHORT = 'L\'article est trop court';
const ERROR_IMAGE_URL = 'L\'image doit être une url valide';
/* Déclaration de notre tableau d'erreurs $errors + initialisation : */
$errors = [
  'title' => '',      /* Erreur sur le champ title : si ce champ est vide. */
  'image' => '',      /* Erreur sur le champ image : si ce champ est vide. */
  'category' => '',   /* Erreur sur le champ category : si ce champ est vide. */
  'content' => ''     /* Erreur sur le champ content  : si ce champ est vide. */
];
$category = '';
// ------------------------------------------------------------------------------------------------
// METHODE GET : POUR RÉCUPÉRER L'ID-IDENTIFIANT D'UN ARTICLE PRÉCIS-EN PARTICULIER :
$_GET = filter_input_array(INPUT_GET, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
// ------------------------------------------------------------------------------------------------
// RÉCUPÉRATION DE L'ID-IDENTIFIANT D'UN ARTICLE PRÉCIS-EN PARTICULIER DEPUIS L'URL :  
$id = $_GET['id'] ?? ''; // ON RÉCUPÈRE L'ID-IDENTIFIANT D'UN ARTICLE PRÉCIS-EN PARTICULIER DEPUIS L'URL.
/* Opérateur de fusion NULL ?? : s'il n'y a rien dans $id (si on ne récupère rien), on met une chaîne vide ''. */
// ------------------------------------------------------------------------------------------------
if ($id) { /* SI $id existe (si on récupère bien un identifiant d'un article précis-en particulier) 
              AVANT l'ajout d'un nouvel article, on est : EN MODE ÉDITION.  
              => RÉCUPÈRATION DE CET ARTICLE PRÉCIS-EN PARTICULIER.*/ 
  $article = $articleDB->fetchOne($id); /* L'article récupéré est stocké dans la variable $article. */
  /* Sur l'objet $articleDB, on appelle la méthode fetchOne() avec $id en paramètre
     ($id = identifiant d'un article précis-en particulier).
     RAPPEL : toute la configuration de la requête SQL est définie dans la classe ArticleDB. */
  // ON A RÉCUPÈRÉ NOTRE ARTICLE PRÉCIS-EN PARTICULIER. 
  // VÉRIFIONS QUE CET ARTICLE PRÉCIS-EN PARTICULIER APPARTIENT BIEN À L'UTILISATEUR QUI EST ACTUELLEMENT CONNECTÉ :
  if ($article['author'] !== $currentUser['id']) { 
    /* SI L'UTILISATEUR, QUI EST CONNECTÉ, N'EST PAS L'AUTEUR-PROPRIÉTAIRE DE CET ARTICLE PRÉCIS-EN PARTICULIER ALORS :
       => IL EST REDIRIGÉ SUR LA PAGE D'ACCUEIL. IL N'A PAS LE DROIT D'ÉDITER CET ARTICLE PRÉCIS-EN PARTICULIER. */
       header('Location: /');// REDIRECTION vers la page index.php (page d'accueil):
  }
  /*  SINON SI L'UTILISATEUR, QUI EST CONNECTÉ, EST BIEN L'AUTEUR-PROPRIÉTAIRE DE CET ARTICLE PRÉCIS-EN PARTICULIER 
      ALORS : => IL A LE DROIT D'ÉDITER SON ARTICLE. */
  $title = $article['title'];
  $image = $article['image'];
  $category = $article['category'];
  $content = $article['content'];
}
// ------------------------------------------------------------------------------------------------
/* Vérifions que l'on est sur une requête "POST" (Requête "POST" : pour soumettre un formulaire).*/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $_POST = filter_input_array(INPUT_POST, [
    'title' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
    'image' => FILTER_SANITIZE_URL,
    'category' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
    'content' => [
      'filter' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
      'flags' => FILTER_FLAG_NO_ENCODE_QUOTES
    ]
  ]);
  // Récupération des variables :
  $title = $_POST['title'] ?? '';       // On récupère 'title' à partir de $_POST   . Si rien, on met une chaîne ''.
  $image = $_POST['image'] ?? '';       // On récupère 'image' à partir de $_POST   . Si rien, on met une chaîne ''.
  $category = $_POST['category'] ?? ''; // On récupère 'category' à partir de $_POST. Si rien, on met une chaîne ''.
  $content = $_POST['content'] ?? '';   // On récupère 'content' à partir de $_POST . Si rien, on met une chaîne ''.
  if (!$title) {
    $errors['title'] = ERROR_REQUIRED;
  } elseif (mb_strlen($title) < 5) {
    $errors['title'] = ERROR_TITLE_TOO_SHORT;
  }
  if (!$image) {
    $errors['image'] = ERROR_REQUIRED;
  } elseif (!filter_var($image, FILTER_VALIDATE_URL)) {
    $errors['image'] = ERROR_IMAGE_URL;
  }
  if (!$category) {
    $errors['category'] = ERROR_REQUIRED;
  }
  if (!$content) {
    $errors['content'] = ERROR_REQUIRED;
  } elseif (mb_strlen($content) < 50) {
    $errors['content'] = ERROR_CONTENT_TOO_SHORT;
  }
  /* S'IL N'Y A PAS D'ERREURS APRES LA REQUÊTE POST (après soumission du formulaire dans la page ÉCRIRE UN ARTICLE), 
     autrement di si le tableau $errors est VIDE ALORS : */
  if (empty(array_filter($errors, fn ($e) => $e !== ''))) {
    if ($id) { /* ET SI $id existe (et si on récupère un identifiant d'un article précis-en particulier) ALORS 
                  ON VA MODIFIER/METTRE À JOUR/UPDATE CET ARTICLE PRÉCIS-EN PARTICULIER (CAS MODIFICATION D'UN ARTICLE):*/
      $article['title'] = $title;
      $article['image'] = $image;
      $article['category'] = $category;
      $article['content'] = $content;
      $article['author'] = $currentUser['id'];
      /* $currentUser['id'] : on récupère l'id de l'utilisateur qui est connecté et qui a modifié cet article précis.
         On stocke $currentUser['id'] (l'id de l'utilisateur qui est connecté et qui a modifié cet article précis) 
         dans le champ/colonne author de la table article de la BDD « blog ». */
      $articleDB->updateOne($article);
      /* Sur l'objet $articleDB, on appelle la méthode updateOne() avec $article en paramètre
         ($article = article précis-en particulier).
         RAPPEL : toute la configuration de la requête SQL est définie dans la classe ArticleDB. */
    } else { /* SINON, si $id n'existe pas (si on ne récupère pas d'identifiant correspondant à 
                un article précis-en particulier) ALORS ON VA CRÉER UN NOUVEL ARTICLE (CAS CRÉATION D'UN ARTICLE):*/
      $articleDB->createOne([
      /* Sur l'objet $articleDB, on appelle la méthode createOne().
         RAPPEL : toute la configuration de la requête SQL est définie dans la classe ArticleDB. */
        'title' => $title,              // Le titre de ce nouvel article sera le contenu de l'input Titre.
        'content' => $content,          // Le content de ce nouvel article sera le contenu de l'input Content.
        'category' => $category,        // La catégorie de ce nouvel article sera la valeur de Catégorie.
        'image' => $image,              // L'image de ce nouvel article sera la valeur de l'input Image.
        'author' => $currentUser['id']
      /* $currentUser['id'] : on récupère l'id de l'utilisateur qui est connecté et qui a créé ce nouvel article.
         On stocke $currentUser['id'] (l'id de l'utilisateur qui est connecté et qui a créé ce nouvel article)
         dans le champ/colonne author de la table article de la BDD « blog ». */
      ]);
    }
    /* APRÈS LA MODIFICATION D'UN ARTICLE PRÉCIS-EN PARTICULIER OU APRÈS LA CRÉATION D'UN NOUVEL ARTICLE,
       L'UTILISATEUR EST REDIRIGÉ SUR LA PAGE D'ACCUEIL. */
    header('Location: /');
    /* MAINTENANT, COMME ON A UNE REDIRECTION => ON VA PERDRE TOUTES LES DONNÉES DU FORMULAIRE.
       SI L'UTILISATEUR RAFRAÎCHIT LA PAGE, IL N'Y AURA PAS :
       => DE MESSAGE "DEMANDE DE CONFIRMATION D'UN NOUVEL ENVOI DU FORMULAIRE".
       => ET LE INPUT EST VIDÉ APRES QUE L'ON AIT FINI DE SOUMETTRE LE FORMULAIRE.*/
  }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <?php require_once 'includes/head.php' ?>
  <title><?= $id ? 'Modifier' : 'Créer' ?> un article</title>
</head>
<body>
  <div class="container">
    <?php require_once 'includes/header.php' ?>
    <div class="content">
      <div class="block p-20 form-container">
        <h1><?= $id ? 'Modifier' : 'Écrire' ?> un article</h1>
        <form action="/form-article.php<?= $id ? "?id=$id" : '' ?>" , method="POST">
          <div class="form-control">
            <label for="title">Titre</label>
            <input type="text" name="title" id="title" value="<?= $title ?? '' ?>">
            <?php if ($errors['title']) : ?>
              <p class="text-danger"><?= $errors['title'] ?></p>
            <?php endif; ?>
          </div>
          <div class="form-control">
            <label for="image">Image</label>
            <input type="text" name="image" id="image" value="<?= $image ?? '' ?>">
            <?php if ($errors['image']) : ?>
              <p class="text-danger"><?= $errors['image'] ?></p>
            <?php endif; ?>
          </div>
          <div class="form-control">
            <label for="category">Catégorie</label>
            <select name="category" id="category">
              <option <?= !$category || $category === 'technologie' ? 'selected' : '' ?> value="technologie">
              Technologie</option>
              <option <?= $category === 'nature' ? 'selected' : '' ?> value="nature">Nature</option>
              <option <?= $category === 'politique' ? 'selected' : '' ?> value="politique">Politique</option>
            </select>
            <?php if ($errors['category']) : ?>
              <p class="text-danger"><?= $errors['category'] ?></p>
            <?php endif; ?>
          </div>
          <div class="form-control">
            <label for="content">Content</label>
            <textarea name="content" id="content"><?= $content ?? '' ?></textarea>
            <?php if ($errors['content']) : ?>
              <p class="text-danger"><?= $errors['content'] ?></p>
            <?php endif; ?>
          </div>
          <div class="form-actions">
            <a href="/" class="btn btn-secondary" type="button">Annuler</a>
            <button class="btn btn-primary" type="submit"><?= $id ? 'Modifier' : 'Sauvegarder' ?></button>
          </div>
        </form>
      </div>
    </div>
    <?php require_once 'includes/footer.php' ?>
  </div>
</body>
</html>