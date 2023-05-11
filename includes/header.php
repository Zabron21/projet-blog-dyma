<?php
/* Déclaration de la variable $currentUser POUR DES RAISONS DE SÉCURITÉ : 
   => RÉCUPÉRATION D'UN UTILISATEUR QUI EST ACTUELLEMENT CONNECTÉ. */
$currentUser = $currentUser ?? false;
/* Opérateur de fusion null ?? : - si $currentUser existe
                                   (si on récupère bien un utilisateur qui est connecté),
                                   alors $currentUser = $currentUser (l'utilisateur connecté = l'utilisateur connecté).
                                 - sinon (si on ne récupère pas d'utilisateur connecté ou si ce-dernier n'existe pas), 
                                   on retourne false. */
?>
<header><!-- début partie HEADER. -->
  <a href="/" class="logo">Dyma Blog</a><!-- href="/" : lien pour retourner à la page d'accueil. -->
  <!-- ---------------------------------------- « MENU MOBILE  » ----------------------------------------------- -->
  <div class="header-mobile">
  <!-- Par défaut, ce "header-mobile" est caché.
       Par contre, si on a une taille d'écran en largeur qui est < à 800 px, on va alors l'afficher. -->
    <div class="header-mobile-icon">
      <img src="/public/img/bar.png"><!-- Dans notre « menu mobile », on a un bouton à 3 barres qui est placé 
                                          en haut à droite sur l'écran Mobile. -->
    </div>
    <ul class="header-mobile-list show">
    <?php if ($currentUser): ?> <!-- SI L'UTILISATEUR EST BIEN CONNECTÉ : -->
                                <!-- 1-1) => ALORS L'UTILISATEUR, QUI EST BIEN CONNECTÉ, PEUT ÉCRIRE UN ARTICLE. -->
      <li class=<?= $_SERVER['REQUEST_URI'] === '/form-article.php' ? 'active' : '' ?>>
        <a href="/form-article.php">Écrire un article</a><!-- Création d'un lien : Écrire un article. -->
        <!-- Pour écrire un article, l'utilisateur, qui est connecté, est redirigé sur la page : form-article.php -->
      </li>
      <li>           <!-- 1-2) => => OU ALORS L'UTILISATEUR, QUI EST BIEN CONNECTÉ, PEUT SE DÉCONNECTER. -->
        <a href="/auth-logout.php">Déconnexion</a><!-- Création d'un lien : Déconnexion. -->
        <!-- Pour se déconnecter de notre site, l'utilisateur connecté est redirigé sur la page : auth-logout.php -->
      </li>          <!-- 1-3) => OU ALORS L'UTILISATEUR, QUI EST BIEN CONNECTÉ, PEUT ALLER SUR SA PAGE DE PROFIL. -->
      <li class="<?= $_SERVER['REQUEST_URI'] === '/profile.php' ? 'active' : '' ?>">
        <a href="/profile.php">Mon espace</a>
        <!-- Pour aller sur sa page de profil, l'utilisateur qui est connecté, est redirigé sur : profile.php -->
      </li>
    <?php else : ?><!-- SINON SI L'UTILISATEUR N'EST PAS CONNECTÉ : -->
      <!-- 2-1) => ALORS, L'UTILISATEUR, QUI N'EST PAS CONNECTÉ, PEUT S'INSCRIRE/CRÉER UN COMPTE SUR NOTRE SITE. -->
      <li class=<?= $_SERVER['REQUEST_URI'] === '/auth-register.php' ? 'active' : '' ?>>
        <a href="/auth-register.php">Inscription</a><!-- Création d'un lien : Inscription -->
        <!-- Pour s'inscrire/créer un compte sur notre site, l'utilisateur est redirigé sur : auth-register.php -->
      </li>
                 <!-- 2-2) => OU ALORS, L'UTILISATEUR, QUI N'EST PAS CONNECTÉ, PEUT SE CONNECTER. -->
      <li class=<?= $_SERVER['REQUEST_URI'] === '/auth-login.php' ? 'active' : '' ?>>
        <a href="/auth-login.php">Connexion</a>
        <!-- Pour se connecter/se logger sur notre site, l'utilisateur est redirigé sur la page : auth-login.php -->
      </li>
    <?php endif; ?>
    </ul>
  </div>
  <!-- ---------------------------------------- « FIN MENU MOBILE  » ------------------------------------------- -->
  <!-- ---------------------------------------- « MENU CLASSIQUE  » -------------------------------------------- -->
  <ul class="header-menu">
    <?php if ($currentUser): ?> <!-- SI L'UTILISATEUR EST BIEN CONNECTÉ : -->
                                <!-- 1-1) => ALORS L'UTILISATEUR, QUI EST BIEN CONNECTÉ, PEUT ÉCRIRE UN ARTICLE. -->
      <li class=<?= $_SERVER['REQUEST_URI'] === '/form-article.php' ? 'active' : '' ?>>
        <a href="/form-article.php">Écrire un article</a><!-- Création d'un lien : Écrire un article. -->
        <!-- Pour écrire un article, l'utilisateur, qui est connecté, est redirigé sur la page : form-article.php -->
      </li>
      <li>     <!-- 1-2) => => OU ALORS L'UTILISATEUR, QUI EST BIEN CONNECTÉ, PEUT SE DÉCONNECTER. -->
        <a href="/auth-logout.php">Déconnexion</a><!-- Création d'un lien : Déconnexion. -->
        <!-- Pour se déconnecter de notre site, l'utilisateur connecté est redirigé sur la page : auth-logout.php -->
      </li>    <!-- 1-3) => OU ALORS L'UTILISATEUR, QUI EST BIEN CONNECTÉ, PEUT ALLER SUR SA PAGE DE PROFIL. -->
      <li class="<?= $_SERVER['REQUEST_URI'] === '/profile.php' ? 'active' : '' ?> header-profile">
        <a href="/profile.php"><?= $currentUser['firstname'][0] . $currentUser['lastname'][0]?></a>
        <!-- $currentUser['firstname'][0] . $currentUser['lastname'][0]
             => lorsque que l'utilisateur, qui est connecté, va sur sa page de profil,
                on récupère : la 1ière lettre de son prénom et la 1ière lettre de son nom.
             => ces 2 lettres sont placées dans le bouton qui lui permet d'aller sur sa page de profil. -->
        <!-- Pour aller sur sa page de profil, l'utilisateur qui est connecté, est redirigé sur : profile.php -->
      </li>
    <?php else : ?><!-- SINON SI L'UTILISATEUR N'EST PAS CONNECTÉ : -->
      <!-- 2-1) => ALORS, L'UTILISATEUR, QUI N'EST PAS CONNECTÉ, PEUT S'INSCRIRE/CRÉER UN COMPTE SUR NOTRE SITE. -->
      <li class=<?= $_SERVER['REQUEST_URI'] === '/auth-register.php' ? 'active' : '' ?>>
        <a href="/auth-register.php">Inscription</a><!-- Création d'un lien : Inscription -->
        <!-- Pour s'inscrire/créer un compte sur notre site, l'utilisateur est redirigé sur : auth-register.php -->
      </li>
                  <!-- 2-2) => OU ALORS, L'UTILISATEUR, QUI N'EST PAS CONNECTÉ, PEUT SE CONNECTER. -->
      <li class=<?= $_SERVER['REQUEST_URI'] === '/auth-login.php' ? 'active' : '' ?>>
        <a href="/auth-login.php">Connexion</a>
        <!-- Pour se connecter/se logger sur notre site, l'utilisateur est redirigé sur la page : auth-login.php -->
      </li>
    <?php endif; ?>
  </ul>
  <!-- ---------------------------------------- « FIN MENU CLASSIQUE  » ---------------------------------------- -->
</header><!-- fin partie HEADER. -->