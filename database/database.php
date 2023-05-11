<?php
// DÉFINITION DE LA CONNEXION À NOTRE BASE DE DONNÉES « blog » => on utilise l'objet PDO :
  /* Le DNS = ce sont tous les éléments qui vont être nécessaires pour la connexion au serveur MySQL. */
  $dns = 'mysql:host=localhost;dbname=blog';
  /* - mysql         : Nom du type de serveur que l'on va attendre. Ici, on attend le serveur mysql. 
                       Au moment de la mise en place du DNS, l'objet $pdo de type/classe PDO saura qu'il doit
                       utiliser la technologie MySQL.
   - :host=localhost : Notre base de données tourne en local, donc sur localhost.
   - ;dbname=blo     : Le nom de la base de données qu'on va utiliser. 
                       Ici, on va utiliser la base de données « blog ».
  /* Déclaration de la variable $user : pour configurer le nom de l'utilisateur. */
  $user = 'root';  // Ici, on a qu'un utilisateur. C'est l'utilisateur 'root'.
  /* Déclaration de la variable $pwd : pour configurer le mot de passe/password.
     C'est le mot de passe/password que l'on a défini pour cet utilisateur 'root',
     lorsqu'on a installé le serveur MySQL. */
  $pwd = '23ol674PY;AB';
// ------------------------------------------------------------------------------------------------------
// Là, on a tous les éléments qui vont nous permettre de nous connecter à notre base de données « blog ».
// ------------------------------------------------------------------------------------------------------
// BLOC « try-catch » : pour ne pas afficher une information à l'utilisateur si la connexion a échouée.
try { /* Dans le « try » : on met l'expression qui va potentiellement retourner une erreur/exception. */
  // CONNEXION À NOTRE BASE DE DONNÉES « blog » EN PRÉCISANT TOUS LES PARAMÈTRES :
  /* Déclaration de la variable $pdo : création d'un objet PDO à partir de la classe PDO 
                                     ou instanciation de la classe PDO.
     L'opérateur new pour créer un objet à partir d'une classe. Après le mot-clé new, on précise une classe. */
     $pdo = new PDO($dns, $user, $pwd, [
     PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
     /* => à chaque fois qu'il y aura une erreur sur l'objet $pdo de type/classe PDO,
           on va throw/lancer des PDOException. */
     PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
     /* => lorsque l'on va fetch()/récupérer les résultats, on va avoir un tableau général.
           Dans ce tableau général, on aura des petits tableaux associatifs sans système d'indices/index du style :
           [0] => valeur associée. */
    ]);
   /* On créé un objet à partir de la classe PDO. 
      On stocke cet objet, de type/classe PDO, dans la variable $pdo.*/
  /*- en 1ièr paramètre de PDO()  : le DNS.                                      Ici, la variable $dns.
    - en 2nd  paramètre de PDO()  : le nom de l'utilisateur.                     Ici, la variable $user.
    - en 3ième paramètre de PDO() : le mot de passe/password de cet utilisateur. Ici, la variable $pwd.
    - en 4ième paramètre de PDO() : on passe un tableau d'options car il s'agit ici de notre application.
        -> 1ière OPTION : PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
           Depuis la classe PDO, avec l'opérateur de résolution de portée ::, on va chercher
           le ATTR_ERRMODE (ATTRibut ERRMODE). ATTR_ERRMODE est, en fait, une constante.
           Le ERRMODE définit la façon dont PDO va gérer les erreurs.
           La valeur PDO::ERRMODE_EXCEPTION <=> à chaque fois qu’il y aura une erreur sur l'objet $pdo 
                                                de type/classe PDO, une PDOException sera lancée
                                                à des fins de débogage.
        -> 2ième option : [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
           Depuis la classe PDO, avec l'opérateur de résolution de portée ::, on va chercher 
           le ATTR_DEFAULT_FETCH_MODE (ATTRibut DEFAULT). ATTR_DEFAULT_FETCH_MODE est, en fait, une constante.
           La valeur PDO::FETCH_ASSOC <=> à chaque fois que l'on récupérera un tableau général, on aura 
                                          à l'intérieur des petits tableaux associatifs 
                                          [nom-colonne] = valeur associée.
                                          Dans ces petits tableaux associatifs, il n'y aura pas de système 
                                          d'indices/index du style : [0] => valeur associée.
      ON VIENT DE CRÉER NOTRE CONNEXION AU SERVEUR MYSQL. */
} catch (PDOException $e) {
  /* Dans le « catch » : on va catcher/attraper/traiter les PDOExceptions qui vont être throw
                         à partir de notre objet $pdo de type/classe PDO.
                         Les erreurs qui vont être retournées par notre objet $pdo, de type/classe PDO,
                         sont des PDOExceptions.
                         Ici, on va catcher/attraper/traiter un objet e de type/classe PDOException,
                         qui est stocké dans la variable $e. */
     throw new Exception($e->getMessage()); 
     /* ICI, on re-throw/re-lance manuellement une nouvelle erreur de type/classe PDOException.
        Maintenant, le serveur WEB (APACHE, NGINX) que l'on aura mis en place, saura qu'il y a eu une erreur
        ou qu'une erreur s'est produite dans notre application de BLOG.
        => ce serveur web va donc pouvoir écrire le contenu de cette erreur dans le fichier/script PHP 
           avec l'extension .log (exemple : php_errors.log)
        Comme on re-throw/re-lance manuellement une nouvelle erreur de type/classe PDOException 
        avec simplement le message de l'erreur qui s'est produite sur notre application de BLOG,
        => le nom de l'utilisateur 'root' et le mot de passe '23ol674PY;AB' 
           (qui permettent tous deux d'accéder à la BDD, ici la BDD « blog »)
           ne seront pas affichés dans le corps du message de cette erreur.
        Sur cette nouvelle erreur de type/classe PDOException que l'on lance manuellement (throw new Exception),
        on utilise la petite flèche pour récupérer le getter getMessage().
        Ici, on appelle directement la fonction getMessage() pour afficher uniquement le message d'erreur 
        de cette nouvelle erreur de type/classe PDOException qu'on lance manuellement. */
}
return $pdo; /* On retourne l'objet $pdo de type/classe PDO pour pouvoir le récupérer,
                notamment quand on va importer/require ce fichier database.php dans d'autres fichiers/scripts PHP.*/ 
?>