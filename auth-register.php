<?php
$pdo = require_once './database/database.php';  // On importe le fichier/script database.php et donc PDO.
/* DANS database.php : l'objet $pdo de type/classe PDO ou instance PDO est déclaré 
   dans le namespace global/contexte global de PHP. 
   Maintenant, pour avoir accès à ce $pdo, on doit require le database.php en 1ière instruction ou en 1ier require
   ici dans auth-register.php */
// -----------------------------------------------------------------------------------
// On importe le fichier/script security.php où la fonction isLoggedin() est définie/implémentée :
$authDB = require __DIR__ . '/database/security.php'; 
/* require __DIR__ . '/database/security.php' dans le contexte global de auth-register.php
   => on exécute alors le fichier security.php dans le contexte global de auth-register.php
   => on stocke l'objet de type/classe AuthDB ou instance AuthDB dans la variable $authDB
      (auteur dans notre DBB « blog »).*/
// ----------------------------------------------------------------------------------------------
/* Déclaration de nos cas d'erreurs (constantes) : on leur assigne, donne comme valeur, des messages d'erreurs. */
const ERROR_REQUIRED = 'Veuillez renseigner ce champ';  /* Message d'erreur : Veuillez renseigner ce champ */
const ERROR_TOO_SHORT = 'Ce champ est trop court';      /* Message d'erreur : Ce champ est trop court */
const ERROR_PASSWORD_TOO_SHORT = 'Le mot de passe doit faire au moins 6 caractères';  
/* Message d'erreur : Le mot de passe doit faire au moins 6 caractères */
const ERROR_PASSWORD_MISMATCH = 'Le mot de passe de confirmation est différent';  
/* Message d'erreur : Le mot de passe de confirmation est différent */
const ERROR_EMAIL_INVALID = 'L\'email n\'est pas valide';  /* Message d'erreur : L'email n'est pas valide */
// ------------------------------------------------------------------------------
/* Déclaration de notre tableau d'erreurs $errors + initialisation : */
$errors = [
  'firstname' => '',      /* Erreur sur le champ firstname       : si ce champ est vide. */
  'lastname' => '',       /* Erreur sur le champ lastname        : si ce champ est vide. */
  'email' => '',          /* Erreur sur le champ email           : si ce champ est vide. */
  'password' => '',       /* Erreur sur le champ password        : si ce champ est vide. */
  'confirmpassword' => '' /* Erreur sur le champ confirmpassword : si ce champ est vide. */
];
// ------------------------------------------------------------------------------
/* Vérifions que l'on est sur une requête "POST" (Requête "POST" : pour soumettre un formulaire).*/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    /* RÉCUPERATION DES VALEURS Prénom, Nom et Email VIA LA MÉTHODE "POST" dans la variable $input
       (les mots de passes ne seront pas sanitize/protégés).
       On assainit/protège tous les champs. */
    $input= filter_input_array(INPUT_POST, [
      'firstname' => FILTER_SANITIZE_SPECIAL_CHARS,
      'lastname' => FILTER_SANITIZE_SPECIAL_CHARS,
      /* FILTER_SANITIZE_SPECIAL_CHARS => pour conserver les accents. */
      'email'    => FILTER_SANITIZE_EMAIL,
    /* 'password' et 'confirmpassword' ne seront pas sanitize/protégés. */
    ]);
    /* DÉFINITION DE NOS VARIABLES $firstname, $lastname, $email, $password et $confirmpassword 
          DEPUIS $input et $_POST : */
    $firstname = $input['firstname'] ?? ''; // On récupère le Prénom depuis l'input Prénom sur la page Web.
    /* Opérateur de fusion NULL ?? : s'il n'y a rien dans $input['firstname'] (si on ne récupère pas de Prénom),
                                     on met une chaîne vide ''. */
    $lastname = $input['lastname'] ?? '';   // On récupère le Nom depuis l'input Nom sur la page Web.
    /* Opérateur de fusion NULL ?? : s'il n'y a rien dans $input['lastname'] (si on ne récupère pas de Nom),
                                     on met une chaîne vide ''. */   
    $email = $input['email'] ?? '';         // On récupère l'Email depuis l'input Email sur la page Web.
    /* Opérateur de fusion NULL ?? : s'il n'y a rien dans $input['email'] (si on ne récupère pas d'Email),
                                     on met une chaîne vide ''. */
    $password = $_POST['password'] ?? '';   /* On récupère le Mot de Passe via la requête "POST" 
                                              (après soumission du formulaire).
    /* Opérateur de fusion NULL ?? : s'il n'y a rien dans $_POST['password'] (si on ne récupère pas de MDP),
                                     on met une chaîne vide ''. */   
    $confirmpassword = $_POST['confirmpassword'] ?? '';
    /* On récupère Confirmation Mot de Passe via la requête "POST" (après soumission du formulaire). */
    /* Opérateur de fusion NULL ?? : s'il n'y a rien dans $_POST['confirmpassword']
       (si on ne récupère pas de confirmation de MDP), on met une chaîne vide ''. */
// ------------------------------------------------------------------------------
/* Gestionnaire d'erreurs : vérifions que les champs 'firstname', 'lastname', 'email', 'password' et 'confirmpassword' 
   sont bien valides. */
if (!$firstname) {                          /* SI Prénom n'a pas de valeur ALORS : */
  $errors['firstname'] = ERROR_REQUIRED;    // => on affiche : Veuillez renseigner ce champ
} elseif (mb_strlen($firstname) < 2) {      // SINON SI Prénom a une valeur < à 2 caractères ALORS :
  $errors['firstname'] = ERROR_TOO_SHORT;   // => on affiche : Ce champ est trop court
}
if (!$lastname) {                          /* SI Nom n'a pas de valeur ALORS : */
  $errors['lastname'] = ERROR_REQUIRED;    // => on affiche : Veuillez renseigner ce champ
} elseif (mb_strlen($lastname) < 2) {      // SINON SI Nom a une valeur < à 2 caractères ALORS :
  $errors['lastname'] = ERROR_TOO_SHORT;   // => on affiche : Ce champ est trop court
}
if (!$email) {                             /* SI Email n'a pas de valeur ALORS : */
  $errors['email'] = ERROR_REQUIRED;       // => on affiche : Veuillez renseigner ce champ
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) { // SINON SI Email n'est pas au format valide ALORS :
  $errors['email'] = ERROR_EMAIL_INVALID;  // => on affiche : L'email n'est pas valide
}
if (!$password) {                             /* SI Mot de passe n'a pas de valeur ALORS : */
  $errors['password'] = ERROR_REQUIRED;       // => on affiche : Veuillez renseigner ce champ
} elseif (mb_strlen($password) < 6) {         // SINON SI Mot de passe a une valeur < à 6 caractères ALORS :
  $errors['password'] = ERROR_PASSWORD_TOO_SHORT;// => on affiche : Le mot de passe doit faire au moins 6 caractères
}
if (!$confirmpassword) {                      /* SI Confirmation Mot de passe n'a pas de valeur ALORS : */
  $errors['confirmpassword'] = ERROR_REQUIRED;// => on affiche : Veuillez renseigner ce champ
} elseif ($confirmpassword !== $password) {    // SINON SI Conformation MDP est différent de MPD ALORS : 
  /* Le double == après le ! : va empêcher les conversions implicites. */
  $errors['confirmpassword'] = ERROR_PASSWORD_MISMATCH;// => on affiche : Le mot de passe de confirmation est différent
}
  /* S'IL N'Y A PAS D'ERREURS APRES LA REQUÊTE POST (après soumission du formulaire par l'utilisateur 
     dans la page INSCRIPTION), autrement di si le tableau $errors est VIDE ALORS : */
if (empty(array_filter($errors, fn ($e) => $e !== ''))) {
  $authDB->register([
    'firstname' => $firstname,
    'lastname'  => $lastname,
    'email'     => $email,
    'password'  => $password
  ]);
  /* Sur l'objet $authDB, on appelle la méthode register() avec un tableau en paramètre.
     Ce tableau va contenir les informations relatives au nouvel utilisateur qui veut s'inscrire/créer un compte
     sur notre site.
     RAPPEL : toute la configuration de la requête SQL est définie dans la classe AuthDB.*/
  /* UNE FOIS QUE L'UTILISATEUR S'EST BIEN INSCRIT, A BIEN CRÉE UN COMPTE SUR NOTRE SITE, IL EST REDIRIGÉ 
     VERS LA PAGE D'ACCEUIL (index.php) : */
   header('Location: /');
/* MAINTENANT, COMME ON A UNE REDIRECTION => ON VA PERDRE TOUTES LES DONNÉES DU FORMULAIRE.
   SI L'UTILISATEUR RAFRAÎCHIT LA PAGE, IL N'Y AURA PAS :
   => DE MESSAGE "DEMANDE DE CONFIRMATION D'UN NOUVEL ENVOI DU FORMULAIRE".
   => ET LE INPUT EST VIDÉ APRÈS QUE L'ON AIT FINI DE SOUMETTRE LE FORMULAIRE.*/
  }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <?php require_once 'includes/head.php' ?><!-- on importe le head dans notre page auth-register.php -->
    <!-- On charge le fichier auth-register.css qui est dans : (dossier)public/(sous-dossier)css. -->
    <link rel="stylesheet" href="/public/css/auth-register.css">
    <title>Inscription</title><!-- Titre : Inscription sur l'onglet de notre page WEB. -->
  </head>
  <body>
    <div class="container">
      <?php require_once 'includes/header.php' ?><!-- on importe le header dans notre page auth-register.php -->
      <div class="content">
        <div class="block p-20 form-container">
          <h1>Inscription</h1>
          <form action="/auth-register.php", method="POST">
            <div class="form-control">
              <label for="firstname">Prénom</label>
              <input type="text" name="firstname" id="firstname" value="<?= $firstname ?? '' ?>">
              <!-- <input> type="text" : pour créer un champ de saisie sur 1 seule ligne, qui va recevoir du texte. -->
              <!-- value="< ?= $firstname ?? '' ?>" : On récupère la valeur de firstname si jamais il y a 
                   une erreur dans le champ firstname après saisie de l'utilisateur.
                   Cela pré-remplira la valeur de ce champ Prénom. -->
              <?php if ($errors['firstname']) : ?><!-- SI on a une erreur dans le champ Prénom : -->
                <p class="text-danger"><?= $errors['firstname'] ?></p>
                <!-- => on affiche le message d'erreur avec la notation raccourcie PHP < ?= ?> --> 
              <?php endif; ?>
            </div>
            <div class="form-control">
              <label for="lastname">Nom</label>
              <input type="text" name="lastname" id="lastname" value="<?= $lastname ?? '' ?>">
              <!-- <input> type="text" : pour créer un champ de saisie sur 1 seule ligne, qui va recevoir du texte. -->
              <!-- value="< ?= $lastname ?? '' ?>" : On récupère la valeur de lastname si jamais il y a 
                   une erreur dans le champ lastname après saisie de l'utilisateur.
                   Cela pré-remplira la valeur de ce champ Nom. -->
              <?php if ($errors['lastname']) : ?><!-- SI on a une erreur dans le champ Nom : -->
                <p class="text-danger"><?= $errors['lastname'] ?></p>
                <!-- => on affiche le message d'erreur avec la notation raccourcie PHP < ?= ?> --> 
             <?php endif; ?>
            </div>
            <div class="form-control">
              <label for="email">Email</label>
              <input type="email" name="email" id="email" value="<?= $email ?? '' ?>">
              <!-- input type="email" : pour saisir 1 adresse email.
                   Soit le champ est vide, soit il contient une adresse email qui est correcte/valide.-->
              <!-- value="< ?= $email ?? '' ?>" : On récupère la valeur de email si jamais il y a 
                   une erreur dans le champ email après saisie de l'utilisateur.
                   Cela pré-remplira la valeur de ce champ Email. -->
              <?php if ($errors['email']) : ?><!-- SI on a une erreur dans le champ Email : -->
                <p class="text-danger"><?= $errors['email'] ?></p>
                <!-- => on affiche le message d'erreur avec la notation raccourcie PHP < ?= ?> --> 
             <?php endif; ?>
            </div>
            <div class="form-control">
              <label for="password">Mot de passe</label>
              <input type="password" name="password" id="password">
              <!-- input type="password" : pour saisir un mot de passe sans que ce MDP soit visible à l'écran. -->
              <!-- Pas de value="< ?= $password ?? '' ?>" : car on ne veut pas afficher le mot de passe.
                                                            On va le ré-initialiser à chaque fois. -->
              <?php if ($errors['password']) : ?><!-- SI on a une erreur dans le champ Mot de passe : -->
                <p class="text-danger"><?= $errors['password'] ?></p>
                <!-- => on affiche le message d'erreur avec la notation raccourcie PHP < ?= ?> --> 
             <?php endif; ?>
            </div>
            <div class="form-control">
              <label for="confirmpassword">Confirmation Mot de passe</label>
              <input type="password" name="confirmpassword" id="confirmpassword">
              <!-- input type="password" : pour saisir un mot de passe sans que ce MDP soit visible à l'écran. -->
              <!-- Pas de value="< ?= $confirmpassword ?? '' ?>" : car on ne veut pas afficher le mot de passe.
                                                                   On va le ré-initialiser à chaque fois. -->
              <!-- SI on a une erreur dans le champ Confirmation Mot de passe : -->                                                   
              <?php if ($errors['confirmpassword']) : ?>
                <p class="text-danger"><?= $errors['confirmpassword'] ?></p>
                <!-- => on affiche le message d'erreur avec la notation raccourcie PHP < ?= ?> --> 
             <?php endif; ?>
            </div>
            <div class="form-actions">
            <!-- Sur cette page INSCRIPTION, on a 2 boutons : Valider et Annuler. -->
              <!-- Bouton Annuler : l'utilisateur est redirigé sur la page d'accueil. -->
              <a href="/" class="btn btn-secondary" type="button">Annuler</a>
              <!-- Bouton Valider : l'utilisateur valide le formulaire qui lui permet de s'inscrire/créer un compte
                                    sur notre site. -->
              <button class="btn btn-primary" type="submit">Valider</button>
            </div>
          </form>
        </div>
      </div>
      <?php require_once 'includes/footer.php' ?><!-- on importe le footer dans notre page auth-register.php -->
    </div>
  </body>
</html>