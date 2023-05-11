<?php
require_once './database/database.php'; // On récupère notre instance/objet de type/classe PDO.
/* DANS database.php : l'objet $pdo de type/classe PDO ou instance PDO est déclaré 
   dans le namespace global/contexte global de PHP. 
   Maintenant, pour avoir accès à ce $pdo, on doit require le database.php en 1ière instruction ou en 1ier require
   ici dans auth-login.php */
// -----------------------------------------------------------------------------------
// On importe le fichier/script security.php où la fonction isLoggedin() est définie/implémentée :
$authDB = require __DIR__ . '/database/security.php'; 
/* require __DIR__ . '/database/security.php' dans le contexte global de auth-login.php
   => on exécute alors le fichier security.php dans le contexte global de auth-login.php
   => on stocke l'objet de type/classe AuthDB ou instance AuthDB dans la variable $authDB
      (auteur dans notre DBB « blog »).*/
// -----------------------------------------------------------------------------------
/* Déclaration de nos cas d'erreurs (constantes) : on leur assigne, donne comme valeur, des messages d'erreurs. */
const ERROR_REQUIRED = 'Veuillez renseigner ce champ';  /* Message d'erreur : Veuillez renseigner ce champ */
const ERROR_PASSWORD_TOO_SHORT = 'Le mot de passe doit faire au moins 6 caractères';  
/* Message d'erreur : Le mot de passe doit faire au moins 6 caractères */
const ERROR_PASSWORD_MISMATCH = 'Le mot de passe n\'est pas valide';  
/* Message d'erreur : Le mot de passe n\'est pas valide */
const ERROR_EMAIL_INVALID = 'L\'email n\'est pas valide';   /* Message d'erreur : L'email n'est pas valide */
const ERROR_EMAIL_UNKNOW = 'L\'email n\'est pas enregistré';/* Message d'erreur : L'email n'est pas enregistré */
// ------------------------------------------------------------------------------
/* Déclaration de notre tableau d'erreurs $errors + initialisation : */
$errors = [
  'email' => '',    /* Erreur sur le champ email    : si ce champ est vide. */
  'password' => '', /* Erreur sur le champ password : si ce champ est vide. */
];
// ------------------------------------------------------------------------------
/* Vérifions que l'on est sur une requête "POST" (Requête "POST" : pour soumettre un formulaire).*/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    /* RÉCUPERATION DE TOUTES LES VALEURS GRÂCE A LA METHODE "POST" dans la variable $input
       On assainit/protège le champ Email. */
    $input= filter_input_array(INPUT_POST, [
      'email'    => FILTER_SANITIZE_EMAIL,
    ]);
    // /* DÉFINITION DE NOS VARIABLES $email et $password DEPUIS $input et $_POST :  
    $email = $input['email'] ?? '';         // On récupère l'Email depuis l'input.
    /* Opérateur de fusion NULL ?? : s'il n'y a rien dans $input['email'] (si on ne récupère rien),
                                     on met une chaîne vide ''. */
    $password = $_POST['password'] ?? '';   /* On récupère le Mot de Passe depuis la requête "POST".
    /* Opérateur de fusion NULL ?? : s'il n'y a rien dans $_POST['password'] (si on ne récupère rien),
                                     on met une chaîne vide ''. */
// ------------------------------------------------------------------------------
// Gestionnaire d'erreurs : vérifions que les champs 'email' et 'password' sont bien valides.
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
  /* S'IL N'Y A PAS D'ERREURS APRES LA REQUÊTE POST (après soumission du formulaire dans la page CONNEXION), 
     autrement di si le tableau $errors est VIDE ALORS : */
    if (empty(array_filter($errors, fn ($e) => $e !== ''))) {
      // => ON VA RÉCUPERER L'UTILISATEUR QUI EST LIÉ A L'EMAIL RENSEIGNÉ LORS DE LA CONNEXION.
      // -- RÉCUPERATION DE L'UTILISATEUR QUI EST LIÉ A L'EMAIL RENSEIGNÉ LORS DE LA CONNEXION.
      $user = $authDB->getUserFromEmail($email); // L'utilisateur récupéré est stocké dans la variable $user.
      /* Sur l'objet $authDB, on appelle la méthode getUserFromEmail() avec $email en paramètre
         ($email = email d'un utilisateur précis-en particulier).
         RAPPEL : toute la configuration de la requête SQL est définie dans la classe AuthDB. */
      // => ON A RÉCUPERÉ L'UTILISATEUR GRÂCE À L'EMAIL RENSEIGNÉ LORS DE LA CONNEXION.
      // Vérifions que l'on a bien un utilisateur.
      if (!$user) { // SI $user n'existe pas (si on n'a pas récupéré d'utilisateur depuis un email précis) ALORS :
        $errors['email'] = ERROR_EMAIL_UNKNOW; /* => on affiche : L'email n'est pas enregistré.
        DANS CE CAS, ON N'A AUCUN UTILISATEUR LIÉ A CET EMAIL QUI A ÉTÉ RENSEIGNÉ LORS DE LA CONNEXION.
        SOIT L'EMAIL N'EST PAS BON, SOIT L'UTILISATEUR N'EST PAS INSCRIT/N'A PAS CRÉE DE COMPTE SUR NOTRE SITE. */ 
      } else {     /* SINON SI on a réussi à récupérer un utilisateur depuis un email précis
                      (email qui a été renseigné lors de la connexion) ALORS :
                      => on vérifie maintenant que le password, renseigné aussi lors de la connexion, est bien le bon : */
        /* Méthode « password_verify() » : vérifie qu'un mot de passe correspond à un hashage.
          - en 1ier paramètre : password renseigné lors de la connexion par un utilisateur = $password.
          - en 2nd paramètre  : password hashé ou hash = $user['password'] = array user/clé "password" 
                                mot de passe qui correspond à un utilisateur qui est déjà inscrit sur notre site
                                et qui est stocké dans notre BDD « blog ».*/
        if (!password_verify($password, $user['password'])) { 
            /* SI le password renseigné par l'utilisateur lors de la connexion ($password) est différent 
               du password hashé qui est stocké dans la table user de la BDD « blog » ($user['password']) ALORS : */
            $errors['password'] = ERROR_PASSWORD_MISMATCH;// => on affiche : Le mot de passe n'est pas valide
        } else { /* SINON SI le password renseigné par l'utilisateur lors de la connexion ($password) 
                    est = au password hashé qui est stocké dans la table user de la BDD « blog » 
                    ALORS ON A BIEN UN UTILISATEUR, DÉJÀ INSCRIT DANS NOTRE BDD « blog », ET QUI VIENT DE SE CONNECTER. 
                    DANS CE CAS :
                    - ON VA CRÉER UNE SESSION-UTILISATEUR AVEC UN ID DE SESSION À PARTIR DE CET UTILISATEUR CONNECTÉ.
                    - ET ON VA ÉCRIRE CET ID DE SESSION DANS LES COOKIES DE CET UTILISATEUR QUI EST CONNECTÉ.*/
            $authDB->login($user['id']);
         /* Sur l'objet $authDB, on appelle la méthode login() avec $user['id'] en paramètre
            ($user['id'] = identifiant d'un utilisateur précis-en particulier).
            RAPPEL : toute la configuration de la requête SQL est définie dans la classe AuthDB. */
         // UNE FOIS CONNECTÉ, L'UTILISATEUR EST REDIRIGÉ VERS LA PAGE D'ACCUEIL (index.php):
            header('Location: /');
         /* MAINTENANT, COMME ON A UNE REDIRECTION => ON VA PERDRE TOUTES LES DONNÉES DU FORMULAIRE.
            SI L'UTILISATEUR RAFRAÎCHIT LA PAGE, IL N'Y AURA PAS :
            => DE MESSAGE "DEMANDE DE CONFIRMATION D'UN NOUVEL ENVOI DU FORMULAIRE".
            => ET LE INPUT EST VIDÉ APRES QUE L'ON AIT FINI DE SOUMETTRE LE FORMULAIRE.*/  
       }
      }
     }
    }
// ON A MIS EN PLACE NOTRE SYSTÈME DE LOGIN-CONNEXION.
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <?php require_once 'includes/head.php' ?><!-- on importe le head dans notre page auth-register.php -->
    <!-- On charge le fichier auth-register.css qui est dans : (dossier)public/(sous-dossier)css. -->
    <link rel="stylesheet" href="/public/css/auth-register.css">
    <title>Connexion</title><!-- Titre : Connexion sur l'onglet de notre page WEB. -->
  </head>
  <body>
    <div class="container">
      <?php require_once 'includes/header.php' ?><!-- on importe le header dans notre page auth-register.php -->
      <div class="content">
        <div class="block p-20 form-container">
          <h1>Connexion</h1>
          <form action="/auth-login.php", method="POST">
            <div class="form-control">
              <label for="email">Email</label>
              <input type="email" name="email" id="email" value="<?= $email ?? '' ?>">
              <!-- input type="email" : pour saisir une adresse email.
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
            <div class="form-actions">
            <!-- Sur cette page CONNEXION, on a 2 boutons : Connexion et Annuler. -->
              <!-- Bouton Annuler : on retourne sur la page d'accueil. -->
              <a href="/" class="btn btn-secondary" type="button">Annuler</a>
              <!-- Bouton Connexion. -->
              <button class="btn btn-primary" type="submit">Connexion</button>
            </div>
          </form>
        </div>
      </div>
      <?php require_once 'includes/footer.php' ?><!-- on importe le footer dans notre page auth-register.php -->
    </div>
  </body>
</html>