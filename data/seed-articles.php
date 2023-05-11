<?php
/* Ce script seed-articles.php va initialiser la table article de notre base de données « blog ». */
// RÉCUPERATION DE TOUS LES ARTICLES QUI SONT DANS articles.json : 
$articles = json_decode(file_get_contents('./articles.json'), true);
  /* RAPPEL : le path/chemin relatif commence par ./ ou .//
     Le point correspond au dossier actuel/dossier courant/dossier sur lequel on travaille
     (ici le dossier « chapter22_blog-v2-mysql »).
     Si on fait './', cela va lister tous les fichiers qui sont dans ce dossier actuel/dossier courant/
     dossier sur lequel on  travaille.
     ./ : dossier actuel/dossier courant/dossier sur lequel on travaille = dossier « chapter22_blog-v2-mysql ».
     ./articles.json : dans le dossier actuel/dossier courant/dossier sur lequel on travaille 
                       (dossier « chapter22_blog-v2-mysql »), on va chercher le fichier articles.json. */
// ------------------------------------------------------------------------------------------------------
  // file_get_contents('/.articles.json') : on récupère ce qu'il y a dans ce fichier articles.json
  // Les infos récupérées sont au format JSON. */
  // CONVERSION du JSON en TABLEAU ASSOCIATIF :
    /* Méthode json_decode()  : va nous permettre de décoder une chaîne de caractère qui est au format JSON valide. 
       - en 1ier paramètre    : file_get_contents('/.articles.json') 
                                = le contenu du fichier articles.json au format JSON valide. 
       - 2nd paramètre précisé: $assoc = true. On récupère un tableau associatif au lieu de récupérer un objet.
      Ce tableau associatif est stocké dans la variable $articles.
      On récupère tous nos articles dans $articles (tableau associatif contenant tous nos articles). */
// ------------------------------------------------------------------------------------------------------
// DÉFINITION DE LA CONNEXION À NOTRE BASE DE DONNÉES « blog » => on utilise l'objet PDO :
  /* Le DNS = tous les éléments qui vont être nécessaires pour la connexion au serveur MySQL. */
   $dns = 'mysql:host=localhost;dbname=blog'; 
  /* - mysql         : Nom du type de serveur que l'on va attendre. Ici, on attend le serveur mysql. 
                       Au moment de la mise en place du DNS, l'objet $pdo de type/classe PDO saura qu'il doit
                       utiliser la technologie MySQL.
   - :host=localhost : Notre base de données tourne en local, donc sur localhost.
   - ;dbname=blog    : Le nom de la base de données qu'on va utiliser. 
                       Ici, on va utiliser la base de données « blog ».*/
  /* Déclaration de la variable $user : pour configurer le nom de l'utilisateur. */
   $user = 'root';  // Ici, on a qu'un utilisateur. C'est l'utilisateur 'root'.
  /* Déclaration de la variable $pwd : pour configurer le mot de passe/password.
     C'est le mot de passe/password que l'on a défini pour cet utilisateur 'root',
     lorsqu'on a installé le serveur MySQL. */
   $pwd = '23ol674PY;AB';
// ------------------------------------------------------------------------------------------------------
// Là, on a tous les éléments qui vont nous permettre de nous connecter à notre base de données « blog ».
// ------------------------------------------------------------------------------------------------------
// CONNEXION À NOTRE BASE DE DONNÉES « blog » EN PRÉCISANT TOUS LES PARAMÈTRES :
/* Déclaration de la variable $pdo : création d'un objet PDO à partir de la classe PDO 
                                     ou instanciation de la classe PDO.
   L'opérateur new pour créer un objet à partir d'une classe. Après le mot-clé new, on précise une classe. */
   $pdo = new PDO($dns, $user, $pwd);/* On créé un objet à partir de la classe PDO. 
                                        On stocke cet objet, de type/classe PDO, dans la variable $pdo.*/
  /*- en 1ièr paramètre de PDO()  : le DNS.                                      Ici, la variable $dns.
    - en 2nd  paramètre de PDO()  : le nom de l'utilisateur.                     Ici, la variable $user.
    - en 3ième paramètre de PDO() : le mot de passe/password de cet utilisateur. Ici, la variable $pwd.
   ON VIENT DE CRÉER NOTRE CONNEXION AU SERVEUR MYSQL. 
   Remarques : - on ne précise pas d'options.
               - on ne va pas faire de gestion d'erreur car ce script/fichier seed-articles.php
                 ne sera jamais exécuté quand un utilisateur utilisera notre application de BLOG. */
// ------------------------------------------------------------------------------------------------------
// CRÉATION DE NOTRE STATEMENT : Déclaration de la variable $statement.
/* création d'un objet PDOStatement à partir de la classe PDOStatement ou instanciation de la classe PDOStatement.
   La classe PDOStatement représente : - d'une part une requête SQL préparée,
                                       - et d'autre part, le résultat de cette requête SQL
                                         une fois qu'elle est exécutée. 
   On créé un objet à partir de la classe PDOStatement. On stocke cet objet dans la variable $statement. 
   Sur l'objet $pdo de type/classe PDO, on utilise la petite flèche -> pour récupérer la méthode prepare().
   Ici, on appelle directement la méthode prepare().
   Méthode « prepare() » : méthode dans laquelle on va écrire une query/requête SQL. 
   -- 1)PRÉPARATION DE LA REQUÊTE SQL : INSERTION DE D'ARTICLES DANS NOTRE TABLE article. */
   $statement =$pdo->prepare('INSERT INTO article (title, category, content, image) VALUES (
    :title,
    :category,
    :content,
    :image
   )');
  /*  :title    sera la valeur que l'on va ajouter dans le champ/colonne title de la table article.
      :category sera la valeur que l'on va ajouter dans le champ/colonne category de la table article.
      :content  sera la valeur que l'on va ajouter dans le champ/colonne content de la table article.
      :image    sera la valeur que l'on va ajouter dans le champ/colonne image de la table article. 
     On a créé l'objet $statement de type PDOStatement. Par défaut, cet objet est inactif. Il faut l'exécuter . */
  // ON A CRÉE NOTRE STATEMENT. MAINTENANT, ON PEUT L'UTILISER.
  // ON VA PARCOURIR NOTRE LISTE/TABLEAU D'ARTICLES.
  /* Pour parcourir un tableau, on va utiliser : foreach.
     Syntaxe : foreach (un-itérable as une variable qui va contenir la valeur de l'itération en cours). */
     foreach ($articles as $article) {
  /* On parcourt $articles (tableau contenant tous nos articles).
     - $articles : l'itérable que l'on va parcourir.
     - $article  : représente l'itération en cours (un article) sur $articles (tableau contenant tous nos articles).
  -- 2) BINDVALUE() TOUTES NOS VALEURS :
  /* Méthode « bindValue() » : méthode qui va associer une valeur à un placeholder nommé.
     Le bindValue() va regarder la valeur et la copier dans le placeholder nommé. */
     $statement->bindValue(':title', $article['title']);      // placeholder nommé :title    = $article['title'].
     $statement->bindValue(':category', $article['category']);// placeholder nommé :category = $article['category'].
     $statement->bindValue(':image', $article['image']);      // placeholder nommé :image    = $article['image'].
     $statement->bindValue(':content', $article['content']);  // placeholder nommé :content  = $article['content'].
/* -- 3) ÉXÉCUTION DE LA REQUÊTE SQL : */
     $statement->execute();/* Sur l'objet $statement, de type/classe PDOStatement, on utilise la petite flèche
                              -> pour récupérer la méthode execute(). Ici, on appelle directement execute().
     Une fois que l'on a exécuté l'objet $statement, de type/classe PDOStatement, la requête SQL va être envoyée
     et sera exécutée depuis le serveur MySQL. */
    }
/* QUAND ON VA ÉXCÉCUTER CE SCRIPT/FICHIER seed-articles.php, ON VA REMPLIR LA TABLE article 
   (DE NOTRE BASE DE DONNÉES « blog ») AVEC TOUS LES ARTICLES QUI ÉTAIENT PRÉSENTS DANS articles.json */
?>