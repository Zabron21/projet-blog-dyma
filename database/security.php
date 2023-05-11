<?php
/* Création de la classe AuthDB : */
class AuthDB {
  // Déclaration de statements : on stocke les statements comme des propriétés privées de la classe AuthDB : 
  private PDOStatement $statementRegister;
  private PDOStatement $statementReadSession;
  private PDOStatement $statementReadUser;
  private PDOStatement $statementReadUserFromEmail;
  private PDOStatement $statementCreateSession;
  private PDOStatement $statementDeleteSession;
  // -----------------------------------------------
  function __construct(private PDO $pdo) { // function __construct() = Fonction constructor.
    /* PDO $pdo         = nouvelle instance de la classe PDO ou nouvel objet $pdo de type/classe PDO.
       private PDO $pdo = à cause de private, $pdo est maintenant une propriété privée de type/classe PDO
                          de la classe AuthDB. */
    /* À chaque nouvel objet de type/classe AuthDB ou nouvelle instance AuthDB,
       => à chaque new AuthDB(), on appelle cette fonction function __construct().
       DANS CE CONSTRUCTOR function __construct(), ON VA PRÉPARER-INITIALISER TOUS LES STATEMENTS. */
   // -----------------------------
      // DÉCLARATION DE statementRegister (STATEMENT POUR CRÉER UN UTILISATEUR) + PRÉPARATION DE LA REQUÊTE SQL.
      /* Sur le $this (objet AuthDB ou instance AuthDB), on appelle la propriété privée statementRegister. */
      $this->statementRegister = $pdo->prepare('INSERT INTO user VALUES (
       DEFAULT,     /* DEFAULT = on laisse MySQL gérer la définition du champ/colonne id de la table user. */
       :firstname,  /* placeholder nommé :firstname */
        :lastname,  /* placeholder nommé :lastname */
        :email,     /* placeholder nommé :email */
        :password   /* placeholder nommé :password */
      )') ;
      /* On ne passe pas le confirmpassword.
         Le confirmpassword ne sert qu'à vérifier que l'utilisateur n'a pas fait d'erreur au moment de son inscription.*/
      /* :firstname sera la valeur que l'on va ajouter dans le champ/colonne firstname de la table user.
         :lastname  sera la valeur que l'on va ajouter dans le champ/colonne lastname  de la table user.
         :email     sera la valeur que l'on va ajouter dans le champ/colonne email     de la table user.
         :password  sera la valeur que l'on va ajouter dans le champ/colonne password  de la table user. 
          On a créé l'objet statementRegister de type PDOStatement. Cet objet est inactif. Il faut l'exécuter. */
   // -----------------------------
      /* DÉCLARATION DE statementReadSession (STATEMENT POUR RÉCUPERER UNE SESSION-UTILISATEUR EN PARTICULIER) 
         + PRÉPARATION DE LA REQUÊTE SQL.
         Sur le $this (objet AuthDB ou instance AuthDB), on appelle la propriété privée statementReadSession. */
      $this->statementReadSession = $pdo->prepare('SELECT * FROM session WHERE id=:id');  
      /* :id sera la valeur que l'on va ajouter dans le champ/colonne id de la table session.
          On a créé l'objet statementReadSessionde type PDOStatement. Cet objet est inactif. Il faut l'exécuter. */
   // -----------------------------
   /* -- DÉCLARATION de statementReadUser (STATEMENT POUR RÉCUPÉRER UN UTILISATEUR QUI EST CONNECTÉ) 
         + PRÉPARATION REQUÊTE SQL. */
      $this->statementReadUser = $pdo->prepare('SELECT * FROM user WHERE id=:id');
      /* :id sera la valeur que l'on va ajouter dans le champ/colonne id de la table user.
          On a créé l'objet statementReadUser de type PDOStatement. Cet objet est inactif. Il faut l'exécuter. */
   // -----------------------------
   /* -- DÉCLARATION de statementReadUserFromEmail (STATEMENT POUR RÉCUPERER UN UTILISATEUR DEPUIS UN EMAIL) 
         + PRÉPARATION REQUÊTE SQL. */
      $this->statementReadUserFromEmail = $pdo->prepare('SELECT * FROM user WHERE email=:email');
     /* :email sera la valeur que l'on va ajouter dans le champ/colonne email de la table user.
         On a créé l'objet statementReadUserFromEmail de type PDOStatement. Cet objet est inactif. Il faut l'exécuter. */
   // -----------------------------
   /* -- DÉCLARATION de statementCreateSession (STATEMENT POUR CRÉER UNE SESSION-UTILISATEUR) 
      + PRÉPARATION REQUÊTE SQL. */
      $this->statementCreateSession = $pdo->prepare('INSERT INTO session VALUES (
         :sessionid, /* placeholder nommé :sessionid */
         :userid     /* placeholder nommé :userid */
         )');
        /* :sessionid sera la valeur que l'on va ajouter dans le champ/colonne id de la table session.
           1IER NIVEAU DE SÉCURITÉ : l’ID de chaque nouvelle SESSION-UTILISATEUR sera généré par nous-mêmes 
           (c’est-à-dire à partir du Serveur et non à partir de MySQL).
           :userid sera la valeur que l'on va ajouter dans le champ/colonne userid de la table session.
            On a créé l'objet statementCreateSession de type PDOStatement. Cet objet est inactif. Il faut l'exécuter. */
   // -----------------------------
   /* -- Déclaration de statementDeleteSession (STATEMENT POUR SUPPRIMER UNE SESSION-UTILISATEUR) 
         + PRÉPARATION REQUÊTE SQL. */
      $this->statementDeleteSession = $pdo->prepare('DELETE FROM session WHERE id=:id');
      /* :id sera la valeur que l'on va ajouter dans le champ/colonne id de la table session.
          On a créé l'objet statementDeleteSession de type PDOStatement. Cet objet est inactif. Il faut l'exécuter. */
  }
  // ---------------------------------------------------------------------------------------
  /* PRÉPARATION DES MÉTHODES public => ON LES UTILISERA DEPUIS L'EXTERIEUR DE LA CLASSE AuthDB.*/
  // -----------------------------
    /* Déclaration de la fonction nommée login() : pour permettre à l'utilisateur de se connecter/logger. */
    function login(string $userId) : void { /* On récupère $userId (identifiant d'un utilisateur en particulier). */
    /* : void <=> indique que la fonction login() ne va rien retourner (n'aura rien comme valeur de retour).
         RAPPEL : typer les fonctions permet à l'éditeur de code VSCode de trouver des erreurs + efficacement. */
    // --------------
       /* Déclaration de la variable $sessionId : */
       // 1IER NIVEAU DE SÉCURITÉ : L'ID DE SESSION $sessionId, ON LE GÉNÈRE NOUS-MÊME (par le SERVEUR et non par MySQL).
       /* $sessionId = ID de SESSION (D'UNE SESSION-UTILISATEUR EN PARTICULIER) QUI NE SERA PAS AUTO-INCREMENTÉ par MySQL.
                       SI LES IDs de SESSION-UTILISATEUR SONT AUTO-INCREMENTÉS/GÉNÉRÉS par MySQL,
                       ALORS UN ATTAQUANT POURRA  SE CONNECTER SUR N'IMPORTE QUEL COMPTE À PARTIR DU MOMENT OÙ 
                       IL CONNAÎT L'ID D'UNE SEULE SESSION-UTILISATEUR EN PARTICULIER. */ 
      /* random_bytes(32)           : ici, on va avoir 1 binaire généré sur 32 bits. 
                                      Ce binaire généré sur 32 bits est une chaîne de caractères/string. */
      /* bin2hex(random_bytes(32))  : représentation hexadécimale de cette chaîne de caractères 
                                      (binaire généré sur 32 bits) qui va être stockée dans la table session 
                                      de notre base de données « blog ». */
       $sessionId = bin2hex(random_bytes(32)); /* MAINTENANT, l'ID de SESSION $sessionId = une chaîne de caractères
                                                              et non plus un INT généré par MySQL. */     
       // -- BINDVALUE() NOS VALEURS CONCERNANT NOTRE SESSION-UTILISATEUR QUI EST MAINTENANT SECURISÉE :
       $this->statementCreateSession->bindValue(':userid', $userId);      // placeholder nommé  :userid = $userId.
       $this->statementCreateSession->bindValue(':sessionid', $sessionId);// placeholder nommé  :sessionid = $sessionId.
       // -- ÉXECUTION DE statementCreateSession ET DE LA REQUÊTE SQL ASSOCIÉE.
       $this->statementCreateSession->execute();/* Une fois que l'on a exécuté statementCreateSession 
                                                   de type/classe PDOStatement,
                                                   la requête SQL associée va être envoyée et sera exécutée 
                                                   depuis le serveur MySQL. */
      /* 2IÈME NIVEAU DE SÉCURITE : CRÉATION D'UN HASH GÉNÉRAL SIGNATURE DE CHAQUE NOUVELLE SESSION-UTILISATEUR.
         SIGNER UNE SESSION-UTILISATEUR PERMET DE S'ASSURER QU'UNE SESSION-UTILISATEUR EN PARTICULIER 
         A BIEN ÉTÉ CRÉÉE PAR LE SERVEUR (c'est-à-dire par nous-mêmes et non par quelqu'un d'autre).
         C'EST L'ASSURANCE QUE PERSONNE NE POURRA MANIPULER OU CRÉER UNE SESSION-UTILISATEUR EN PARTICULIER.
         Déclaration de la variable $signature = hash général signature de chaque nouvelle session-utilisateur.*/
       $signature = hash_hmac('sha256', $sessionId, 'cinq petits chats');  
      /* Méthode « hash_hmac() » : on hashe L'ID DE SESSION-UTILISATEUR $sessionId (chaîne de caractère) 
                                   via algorithme de hashage 'sha256' pour récupérer une chaîne de caractères unique.
         - en 1ier paramètre : on passe un algorithme de hashage. Si on ne sait pas quoi mettre, on met 'sha256'.
           sha-256 = Secure Hash Algorithm (algorithme de hachage sécurisé) 256 bits. Il est utilisé 
                     dans les applications de sécurité cryptographique.
                     Les algorithmes de hachage cryptographique génèrent des hachages irréversibles et uniques.
                     sha-256 est un bon algorithme de hachage sécurisé. 
         - en 2nd paramètre : une chaîne de caractères qu'on va devoir « hasher ». 
           Ici : L'ID DE SESSION-UTILISATEUR $sessionId = chaîne de caractères qu'on va « hasher ».
         - en 3ième paramètre : notre secret ou phrase secrète. Ici, le secret est : 'cinq petits chats'.
           Grâce ce secret, en gros, aucun attaquant ne pourra reproduire le « hash » ID de SESSION-UTILISATEUR
           $sessionId d'un utilisateur en particulier qui vient de se connecter.
           Il faut bien garder cette phrase secrète (ici : 'cinq petits chats') secrète sinon toutes nos 
           sessions-utilisateurs seront toutes compromises. */
      // ICI, ON A BIEN GÉNÉRÉ NOTRE « HASH » GÉNÉRAL SIGNATURE DE CHAQUE NOUVELLE SESSIONS-UTILISATEUR : $signature.
       /* -- ÉCRITURE DE CET ID DE SESSION-UTILISATEUR, $sessionId, DANS LES COOKIES DE L'UTILISATEUR QUI S'EST CONNECTÉ: 
             Méthode « setcookie() » : définit un cookie qui sera envoyé avec le reste des en-têtes HTTP. */
             setcookie('session', $sessionId, time() + 60 * 60 * 24 * 14, "", "", false, true);
        /*- $name  = 'session' : 'session' est la clé que l'on va utiliser dans notre cookie.
          - $value = $sessionId: $sessionId est la valeur associée à la clé 'session' dans notre cookie.
          - $expires_or_options = time() + 60 * 60 * 24 * 14 = maintien connexion active pour une durée de 2 semaines,
                                -> time() récupère le nombre de secondes depuis le 1ier Janvier 1970,
                                -> + 60 (1 min) * 60 (1 heure) * 24 (1 journée) * 14 (14 jours) = durée de vie du cookie.
                                   Ici, le cookie va durer 2 semaines et, au bout de 2 semaines, il sera supprimé.
          - $path = ""         : On laisse le path par défaut. Le cookie sera disponible partout.
          - $domain = "",      : On laisse le domaine par défaut. Le cookie s'applique partout.
          - $secure = false    : Si cette option = true, le cookie sera passé à l'utilisateur si ce-dernier est avec une
                                 connexion HTTPS. On laisse à false car on n'a pas de HTTPS de configuré.
          - $httponly = true   : Personne ne pourra accéder à l'id de SESSION-UTILISATEUR depuis le JAVASCRIPT du Client.*/
       /* -- ÉCRITURE DE CE « HASH » GÉNÉRAL SIGNATURE DE CHAQUE NOUVELLE SESSION-UTILISATEUR, $signature, 
             DANS LES COOKIES DE L'UTILISATEUR QUI S'EST CONNECTÉ/VIENT DE SE CONNECTER : */
             setcookie('signature', $signature, time() + 60 * 60 * 24 * 14, "", "", false, true);
        /*- $name  = 'signature' : 'signature' est la clé que l'on va utiliser dans notre cookie.
          - $value = $signature  :  $signature est la valeur associée à la clé 'signature' dans notre cookie.*/
       return;
    }
  // --------------------------
    /* Déclaration de la fonction nommée register() : pour permettre à l'utilisateur de s'inscrire/créer un compte. */
    function register(array $user): void {  /* On récupère $user (tableau contenant les infos du nouvel utilisateur
                                               qui va s'inscrire/créer un compte sur notre site). */
       /*: void <=> indique que la fonction register() ne va rien retourner (n'aura rien comme valeur de retour).
         RAPPEL : typer les fonctions permet à l'éditeur de code VSCode de trouver des erreurs + efficacement. */
       // Déclaration de la variable $hashedPassword :
       /* Méthode « password_hash » : va créer un « hash » à partir d'un password/mot de passe.
           - en 1ier paramètre : le password que l’on veut « hasher ». Ici $user['password'].
           - en 2nd paramètre  : l'algorithme de hashage. Ici, on utilise l'algorithme de hashage ARGON2I. */
       $hashedPassword = password_hash($user['password'], PASSWORD_ARGON2I);
       // ON A NOTRE HASH.
       // -- BINDVALUE() TOUTES NOS VALEURS CONCERNANT UN UTILISATEUR QUI VA S'INSCRIRE/CRÉER UN COMPTE. :
       $this->statementRegister->bindValue(':firstname', $user['firstname']);
       // placeholder nommé :firstname = $user['firstname'].
       $this->statementRegister->bindValue(':lastname', $user['lastname']);
       // placeholder nommé :lastname = $user['lastname'].
       $this->statementRegister->bindValue(':email', $user['email']);// placeholder nommé :email = $user['email'].
       $this->statementRegister->bindValue(':password', $hashedPassword);// placeholder nommé :password = $hashedPassword.
       // -- ÉXECUTION DE statementRegister ET DE LA REQUÊTE SQL ASSOCIÉE.
       $this->statementRegister->execute(); /* Une fois que l'on a exécuté statementRegister de type/classe PDOStatement,
                                               la requête SQL associée va être envoyée et sera exécutée 
                                               depuis le serveur MySQL. */
        return;      
    }
    // --------------------------
    /* Déclaration de la fonction nommée isLoggedin() : pour savoir si un utilisateur est connecté/loggé ou pas. */
    function isLoggedin(): array | false {  // isLoggedin() va vérifier si l'utilisateur est connecté/loggé ou pas.
    /* : array | false <=> indique que la fonction isLoggedin() va retourner :
                           - un array/tableau associatif si l'utilisateur est bien connecté/loggé à notre site.
                           - ou false si l'utilisateur n'est pas connecté/loggé à notre site.*/
    /* RÉCUPÉRATION DE L'ID DE SESSION-UTILISATEUR D'UN UTILISATEUR QUI VIENT DE SE CONNECTER : */
       $sessionId = $_COOKIE['session'] ?? ''; 
       /* Opérateur de fusion NULL ?? : s'il n'y a pas de cookie 'session' de l'utilisateur,
                                        on met une chaîne vide ''. */
    // RÉCUPÉRATION DE LA SIGNATURE DE CETTE SESSION-UTILISATEUR, D'UN UTILISATEUR QUI VIENT DE SE CONNECTER.
       $signature = $_COOKIE['signature'] ?? '';
      /* Opérateur de fusion NULL ?? : s'il n'y a pas de cookie 'signature' de l'utilisateur, 
                                       on met une chaîne de caractères vide ''. */                                 
       if ($sessionId && $signature) { 
         /* SI $sessionId existe (SI on a bien un ID de SESSION ou une SESSION-UTILISATEUR EN PARTICULIER
                                  => UN UTILISATEUR, EN PARTICULIER, EST BIEN CONNECTÉ À NOTRE SITE) 
         ET SI $signature existe (ET SI LE HASH GÉNÉRAL SIGNATURE DE CHAQUE NOUVELLE SESSION-UTILISATEUR EXISTE BIEN 
                                  DANS LES COOKIES DE CET UTILISATEUR QUI VIENT DE SE CONNECTER
                                  => CETTE SESSION-UTILISATEUR, EN PARTICULIER, EST BIEN SIGNÉE) 
         ALORS DANS CE CAS :
         GÉNÉRATION D'UN « HASH » À PARTIR DE L'ID de SESSION-UTILISATEUR $sessionId DE CET UTILISATEUR 
         EN PARTICULIER QUI S'EST CONNECTÉ/VIENT DE SE CONNECTER : */
        $hash = hash_hmac('sha256', $sessionId, 'cinq petits chats');
       /* ON A GÉNÉRÉ LE « HASH » $hash DE L'ID de SESSION-UTILISATEUR $sessionId DE CET UTILISATEUR EN PARTICULIER 
          QUI S'EST CONNECTÉ/VIENT DE SE CONNECTER. */
       /* VERIFIONS MAINTENANT QUE CE « HASH » $hash (DE L'ID de SESSION-UTILISATEUR $sessionId DE CET UTILISATEUR
          EN PARTICULIER QUI S'EST CONNECTÉ/VIENT DE SE CONNECTER),
          EST BIEN LE MÊME QUE LE HASH GÉNÉRAL SIGNATURE $signature DE CHAQUE NOUVELLE SESSION-UTILISATEUR : */
        if (hash_equals($hash, $signature)) { // va nous dire si les hashs $signature et $hash sont égaux ou non.
            /* Méthode « hash_equals() » : prend 2 hashs en paramètres et va nous dire s'ils sont égaux ou non. */
            /* SI les 2 hashs $signature et $hash sont égaux, ALORS :
               -> L'UTILISATEUR EST BIEN CONNECTÉ.
               -> ET ON EST CERTAIN QUE l’ID de SESSION-UTILISATEUR $sessionId, qui est stocké dans 
                  les COOKIES de cet utilisateur en particulier qui vient de se connecter, est bien 
                  un ID de SESSION-UTILISATEUR que l’on a déclaré nous-mêmes. */
            // -- BINDVALUE() NOS VALEURS POUR RÉCUPÉRER UNE SESSION-UTILISATEUR EN PARTICULIER :
            $this->statementReadSession->bindValue(':id', $sessionId); // placeholder nommé :id = $sessionId.
            // -- ÉXECUTION DE statementReadSession ET DE LA REQUÊTE SQL ASSOCIÉE.
            $this->statementReadSession->execute(); /* Une fois que l'on a exécuté statementReadSession de type/classe 
                                                       PDOStatement, la requête SQL associée va être envoyée et 
                                                       sera exécutée depuis le serveur MySQL. */
            // -- RÉCUPÉRATION DE LA SESSION-UTILISATEUR EN PARTICULIER.
            // Comme on récupère 1 seul objet SESSION (1 seul array/tableau associatif), on utilise : fetch().
            $session = $this->statementReadSession->fetch(); /* Dans cet objet SESSION, on a l'ID de SESSION-UTILISATEUR. */
            // ON A RÉCUPERÉ UNE SESSION-UTILISATEUR EN PARTICULIER, CONCERNANT UN UTILISATEUR QUI VIENT DE SE CONNECTER.

            // VÉRIFIONS QUE L'ON A EFFECTIVEMENT BIEN UNE SESSION-UTILISATEUR.
              if ($session) { /* SI $session existe (SI on a bien UNE SESSION-UTILISATEUR EN PARTICULIER),
                               ALORS UN UTILISATEUR BIEN EST CONNECTÉ.
                               DANS CE CAS => RÉCUPÉRATION DE CET UTILISATEUR QUI EST CONNECTÉ : */
            // -- BINDVALUE() NOS VALEURS POUR RÉCUPÉRER CET UTILISATEUR QUI S'EST CONNECTÉ :
                $this->statementReadUser->bindValue(':id', $session['userid']); 
               // placeholder nommé :id = $session['userid'].
            // -- ÉXECUTION DE statementReadUser ET DE LA REQUÊTE SQL ASSOCIÉE.
                $this->statementReadUser->execute(); /* Une fois que l'on a exécuté statementReadUser de type/classe 
                                                        PDOStatement, la requête SQL associée va être envoyée et 
                                                        sera exécutée depuis le serveur MySQL. */
            // -- RÉCUPÉRATION DE CET UTILISATEUR QUI EST CONNECTÉ/VIENT DE SE CONNECTER :
            //    Comme on récupère 1 seul objet USER (1 seul array/tableau associatif), on utilise : fetch().
                $user = $this->statementReadUser->fetch();/* Dans cet objet USER, on a l'ID de L'UTILISATEUR 
                QUI S'EST CONNECTÉ/VIENT DE SE CONNECTER.
                ICI, COMMME L'UTILISATEUR EST CONNECTÉ, ON RÉCUPÈRE TOUTES CES INFORMATIONS.*/
            }
        }
      }
      return $user ?? false;/* Si $user existe (si on a bien récupéré un utilisateur qui vient de se connecter),
                               alors on le retourne et on récupère donc toutes ces informations.
                               Si $user n'existe pas, on retourne false. */
    }
    // --------------------------
    /* Déclaration de la fonction nommée logout() : pour savoir si un utilisateur est déconnecté ou pas. */
    function logout(string $sessionId): void {
    /* Pas besoin de récupérer L'ID DE SESSION-UTILISATEUR $sessionId d'un utilisateur en particulier.
       On a déjà $sessionId car cet utilisateur, en particulier, est déjà connecté/loggé.
       Il veut maintenant se déconnecter. */ 
    /* : void <=> indique que la fonction logout() ne va rien retourner (n'aura rien comme valeur de retour).
         RAPPEL : typer les fonctions permet à l'éditeur de code VSCode de trouver des erreurs + efficacement. */
       // -- BINDVALUE() NOS VALEURS POUR DÉCONNECTER UN UTILISATEUR QUI EST ACTUELLEMENT CONNECTÉ :
       $this->statementDeleteSession->bindValue(':id', $sessionId); // placeholder nommé :id = $sessionId.
       // -- ÉXECUTION DE statementDeleteSession ET DE LA REQUÊTE SQL ASSOCIÉE.
       $this->statementDeleteSession->execute(); 
       /* Une fois que l'on a exécuté statementDeleteSession de type/classe PDOStatement,
          la requête SQL associée va être envoyée et sera exécutée depuis le serveur MySQL. */
       /* À PARTIR DE LÀ, LA BONNE SESSION-UTILISATEUR EST SUPPRIMÉE DANS LA BDD « blog » (AVEC LE BON ID DE 
          SESSION-UTILISATEUR). 
          L'UTILISATEUR, QUI ETAIT LIÉ A CETTE SESSION-UTILISATEUR EN PARTICULIER LORSQU'IL ETAIT CONNECTÉ,
          EST MAINTENANT DECONNECTÉ DE NOTRE BLOG. */
       // -----------------
       /* L'UTILISATEUR EST DECONNECTÉ : SA SESSION-UTILISATEUR EST DONC EXPIRÉE.
          => VIDONS LA CLÉ 'session' DANS LES COOKIES DE CET UTILISATEUR QUI EST MAINTENANT DECONNECTÉ. 
          => POUR CELA, ON VA METTRE A JOUR LE COOKIE AVEC LA CLÉ 'session' EN LUI DÉFINISSANT UNE DATE DÉJÀ EXPIRÉE. 
          Méthode « setcookie() » : définit un cookie qui sera envoyé avec le reste des en-têtes HTTP. */
          setcookie('session', '', time() - 1);
       /* - $name  = 'session' : 'session' est la clé que l'on va utiliser dans notre cookie.
          - $value = ''        : une chaîne de caractères vide (cela n'a pas d'importance).
          - $expires_or_options = time() - 1
                            -> time()     : récupère le nombre de secondes depuis le 1ier Janvier 1970,
                            -> time() - 1 : ici, le cookie est expiré. */
       // -----------------
       /* L'UTILISATEUR EST DECONNECTÉ : SA SESSION-UTILISATEUR EST DONC EXPIRÉE.
          => VIDONS LA CLÉ 'signature' DANS LES COOKIES DE L'UTILISATEUR QUI EST DECONNECTÉ.
             (RAPPEL : le cookie signature avait permis de signer la session-utilisateur de cet utilisateur 
                       en particulier lorsque ce-dernier était connecté sur notre blog).
          => POUR CELA, ON VA METTRE A JOUR LE COOKIE AVEC LA CLÉ 'signature' EN LUI DÉFINISSANT UNE DATE DÉJÀ EXPIRÉE. */
          setcookie('signature', '', time() - 1);
       /* - $name  = 'signature' : 'signature' est la clé que l'on va utiliser dans notre cookie. */
       return;  
    }
    // --------------------------
    /* Déclaration de la fonction nommée getUserFromEmail() : pour récupérer un utilisateur depuis un email précis. */
    function getUserFromEmail(string $email): array | false {
    /* On récupère $email (email d'un utilisateur en particulier qui est en train de se connecter/logger sur notre site). */
    /* : array <=> indique que la fonction getUserFromEmail() va retourner (aura comme valeur de retour) un array/tableau.
       RAPPEL : typer les fonctions permet à l'éditeur de code VSCode de trouver des erreurs + efficacement. */
      // -- BINDVALUE() NOS VALEURS CONCERNANT UN UTILISATEUR QUE L'ON VA RÉCUPÉRÉ DEPUIS UN EMAIL :
      $this->statementReadUserFromEmail->bindValue(':email', $email); // placeholder nommé :email = $email.
      // -- ÉXECUTION DE statementReadUserFromEmail ET DE LA REQUÊTE SQL ASSOCIÉE.
      $this->statementReadUserFromEmail->execute(); 
      /* Une fois que l'on a exécuté statementReadUserFromEmail de type/classe PDOStatement,
         la requête SQL associée va être envoyée et sera exécutée depuis le serveur MySQL. */
      // -- RÉCUPÉRATION DE L'UTILISATEUR QUI EST LIÉ A L'EMAIL RENSEIGNÉ LORS DE LA CONNEXION.
      return $this->statementReadUserFromEmail->fetch();
      // ON A RÉCUPERÉ L'UTILISATEUR GRÂCE À L'EMAIL RENSEIGNÉ LORS DE LA CONNEXION.
      }
}
return new AuthDB($pdo); /* On retourne une nouvelle instance AuthDB
                            OU un nouvel objet de type/classe AuthDB (qui est issu de la classe AuthDB).
                            Dans les instances qui seront issues de la classe AuthDB ou objets créés à partir
                            de cette classe AuthDB, on aura : $pdo (propriété privée de type/classe PDO).
Au moment de créer un objet de type/classe AuthDB ou d’instancier AuthDB,
on sera sûr d’accéder à $pdo depuis le contexte global/namespace global de PHP.
Par contre, pour que cela marche, sur toutes nos pages PHP, on doit require le database.php en 1ière instruction 
ou en 1ier require, soit avant de require/importer le fichier/script security.php 
Si on ne fait pas require database.php avant require security.php sur toutes nos pages PHP,
on aura null ou undefined pour $pdo.*/
?>