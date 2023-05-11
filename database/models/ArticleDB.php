<?php
/* DANS database.php : l'objet $pdo de type/classe PDO ou instance PDO est déclaré 
   dans le namespace global/contexte global de PHP. On ne va donc pas require database.php ici dans ArticleDB.php */
// ----------------------------------------------------------------------------------------------------------------
/* Création de la classe ArticleDB : */
class ArticleDB {
  // Déclaration de statements : on stocke les statements comme des propriétés privées de la classe ArticleDB : 
  private PDOStatement $statementCreateOne;
  private PDOStatement $statementUpdateOne;
  private PDOStatement $statementReadOne;
  private PDOStatement $statementReadAll;
  private PDOStatement $statementDeleteOne;
  private PDOStatement $statementReadUserAll;
  // -----------------------------------------------
  function __construct(private PDO $pdo) { // function __construct() = Fonction constructor.
    /* PDO $pdo         = nouvelle instance de la classe PDO ou nouvel objet $pdo de type/classe PDO.
       private PDO $pdo = à cause de private, $pdo est maintenant une propriété privée de type/classe PDO
                          de la classe ArticleDB. */
    /* À chaque nouvel objet de type/classe ArticleDB ou nouvelle instance d'ArticleDB,
       => à chaque new ArticleDB(), on appelle cette fonction function __construct().
       DANS CE CONSTRUCTOR function __construct(), ON VA PRÉPARER-INITIALISER TOUS LES STATEMENTS.
       // --------------------------------------------------------------------------
         // DÉCLARATION DE statementCreateOne (STATEMENT POUR CRÉER UN ARTICLE) + PRÉPARATION DE LA REQUÊTE SQL.
         /* Sur le $this (objet ArticleDB ou instance ArticleDB), on appelle la propriété privée statementCreateOne. */
         $this->statementCreateOne = $pdo->prepare('
        INSERT INTO article (
          title,
          category,
          content,
          image,
          author
        ) VALUES (
          :title,
          :category,
          :content,
          :image,
          :author
        )
        ');
       // On a créé statementCreateOne. Cet objet est inactif. Il faut l'exécuter.
       // -----------------------------
       // DÉCLARATION DE statementUpdateOne (STATEMENT POUR MAJ/MODIFIER UN ARTICLE) + PRÉPARATION DE LA REQUÊTE SQL.
       /* Sur le $this (objet ArticleDB ou instance ArticleDB), on appelle la propriété privée statementUpdateOne. */
        $this->statementUpdateOne = $pdo->prepare('
        UPDATE article
        SET
          title=:title,
          category=:category,
          content=:content,
          image=:image,
          author=:author
        WHERE id=:id; 
        ');
       // On a créé statementUpdateOne. Cet objet est inactif. Il faut l'exécuter.
       // -----------------------------
       // DÉCLARATION DE statementReadOne (STATEMENT POUR LIRE UN SEUL ARTICLE) + PRÉPARATION DE LA REQUÊTE SQL.
       /* Sur le $this (objet ArticleDB ou instance ArticleDB), on appelle la propriété privée statementReadOne. */
        $this->statementReadOne = $pdo->prepare('SELECT article.*, user.firstname, user.lastname FROM article 
                                                 LEFT JOIN user ON article.author = user.id WHERE article.id = :id');
      /* SELECT article.*, user.firstname, user.lastname : - Récupère tous les champs/colonnes de la table article.
                                                           - Et Récupère les champs/colonnes firstname et lastname
                                                             de la table user.
         FROM article                : Depuis la table article,
         LEFT JOIN user              : on joint la table user au résultat final.
                                       on récupère tous les articles.
                                       S'il y a un user/propriétaire alors on le joint.
                                       LEFT JOIN récupère toutes les entrées de la table de gauche 
                                       (celle sur laquelle on est positionné avec FROM : ici FROM article).
         ON article.author = user.id : et on fait la jointure/lien entre
                                       -> author (champ/colonne clé étrangère FK de la table article)
                                          = auteur-propriétaire de l'article.
                                       -> et id (champ/colonne clé primaire de la table user).
                                          = id ou identifiant utilisateur
         WHERE article.id = :id'     : et cette jointure/ce lien sera fait sur chaque article en particulier.*/
       // On a créé statementReadOne. Cet objet est inactif. Il faut l'exécuter.
       // -----------------------------
       // DÉCLARATION DE statementReadAll (STATEMENT POUR LIRE TOUS LES ARTICLES) + PRÉPARATION DE LA REQUÊTE SQL.
       /* Sur le $this (objet ArticleDB ou instance ArticleDB), on appelle la propriété privée statementReadAll. */
        $this->statementReadAll = $pdo->prepare('SELECT article.*, user.firstname, user.lastname 
                                                 FROM article LEFT JOIN user ON article.author = user.id');
      /* SELECT article.*, user.firstname, user.lastname : - Récupère tous les champs/colonnes de la table article.
                                                           - Et Récupère les champs/colonnes firstname et lastname
                                                             de la table user.
         FROM article                : Depuis la table article,
         LEFT JOIN user              : on joint la table user au résultat final.
                                       on récupère tous les articles.
                                       S'il y a un user/propriétaire alors on le joint.
                                       LEFT JOIN récupère toutes les entrées de la table de gauche 
                                       (celle sur laquelle on est positionné avec FROM : ici FROM article).
         ON article.author = user.id : et on fait la jointure/lien entre
                                       -> author (champ/colonne clé étrangère FK de la table article)
                                          = auteur-propriétaire de l'article.
                                       -> et id (champ/colonne clé primaire de la table user).
                                          = id ou identifiant utilisateur.
                                        Cette jointure/ce lien est fait sur tous les articles. */
       // On a créé statementReadAll. Cet objet est inactif. Il faut l'exécuter.
       // -----------------------------
       // DÉCLARATION DE statementDeleteOne (STATEMENT POUR SUPPRIMER UN SEUL ARTICLE) + PRÉPARATION DE LA REQUÊTE SQL.
       /* Sur le $this (objet ArticleDB ou instance ArticleDB), on appelle la propriété privée statementDeleteOne. */
       $this->statementDeleteOne = $pdo->prepare('DELETE FROM article WHERE id=:id');
       // On a créé statementDeleteOne. Cet objet est inactif. Il faut l'exécuter.
       // -----------------------------
       /* DÉCLARATION DE statementReadUserAll (STATEMENT POUR LIRE TOUS LES ARTICLES D'UN UTILISATEUR-AUTEUR 
                                               EN PARTICULIER) + PRÉPARATION DE LA REQUÊTE SQL. */
       /* Sur le $this (objet ArticleDB ou instance ArticleDB), on appelle la propriété privée statementReadUserAll. */
       $this->statementReadUserAll = $pdo->prepare('SELECT * FROM article WHERE author=:authorId');
       // ON RÉCUPERE ICI TOUS LES ARTICLES QUI PROVIENNENT D'UN UTILISATEUR ET AUSSI AUTEUR-PROPRIÉTAIRE EN PARTICULIER.  
  } // --------------------------------------------------------------------------
  /* PRÉPARATION DES MÉTHODES public => ON LES UTILISERA DEPUIS L'EXTERIEUR DE LA CLASSE ArticleDB.*/
  /* Déclaration de la méthode fetchAll() : méthode qui va retourner la liste de tous nos articles. */
  public function fetchAll(): array {
    /* : array <=> indique que la fonction fetchAll() va retourner (aura comme valeur de retour) un array/tableau.
                   RAPPEL : typer les fonctions permet à l'éditeur de code VSCode de trouver des erreurs +
                            efficacement. */
    // ÉXECUTION DE statementReadAll ET DE LA REQUÊTE SQL ASSOCIÉE.
    $this->statementReadAll->execute(); /* Une fois que l'on a exécuté statementReadAll de type/classe PDOStatement,
                                           la requête SQL associée va être envoyée et sera exécutée 
                                           depuis le serveur MySQL. */
    // RÉCUPÉRATION DES RÉSULTATS :
    return $this->statementReadAll->fetchAll(); /* Méthode fetchAll() : retourne un tableau général contenant 
                                                                        tous les articles (chaque article est 
                                                                        stocké dans un petit tableau). */
  }
  // --------------------------
  /* Déclaration de la méthode fetchOne() : méthode qui va retourner un seul article. */
  public function fetchOne(int $id): array { /* On récupère $id (ID ou identifiant d'un article précis-en particulier) 
                                                qu'on veut lire. */
    /* : array <=> indique que la fonction fetchOne() va retourner (aura comme valeur de retour) un array/tableau.
                   RAPPEL : typer les fonctions permet à l'éditeur de code VSCode de trouver des erreurs +
                            efficacement. */
    // BINDVALUE L'ID DE L'ARTICLE PRÉCIS-EN PARTICULIER QU'ON VEUT LIRE :
    $this->statementReadOne->bindValue(':id', $id);
    // ÉXECUTION DE statementReadOne ET DE LA REQUÊTE SQL ASSOCIÉE.
    $this->statementReadOne->execute(); /* Une fois que l'on a exécuté statementReadOne de type/classe PDOStatement,
                                           la requête SQL associée va être envoyée et sera exécutée 
                                           depuis le serveur MySQL. */
    // RÉCUPÉRATION DES RÉSULTATS :
    return $this->statementReadOne->fetch();/* Méthode fetch() : retourne un tableau avec un seul article
                                               précis-en particulier. */
  }
  // --------------------------
  /* Déclaration de la méthode deleteOne() : méthode qui va supprimer un seul article précis-en particulier. */
  public function deleteOne(int $id): string {/* On récupère $id (identifiant d'un article précis-en particulier)
                                                 qu'on veut supprimer. */
    /* : string <=> indique que la fonction deleteOne() va retourner (aura comme valeur de retour) un string/une chaîne
                    de caractères.
                    RAPPEL : typer les fonctions permet à l'éditeur de code VSCode de trouver des erreurs +
                             efficacement. */
    // BINDVALUE L'ID DE L'ARTICLE PRÉCIS-EN PARTICULIER QU'ON VEUT SUPPRIMER :
    $this->statementDeleteOne->bindValue(':id', $id);
    // ÉXECUTION DE statementDeleteOne ET DE LA REQUÊTE SQL ASSOCIÉE.
    $this->statementDeleteOne->execute(); /* Une fois que l'on a exécuté statementDeleteOne de type/classe PDOStatement,
                                             la requête SQL associée va être envoyée et sera exécutée 
                                             depuis le serveur MySQL. */
    return $id; // On retourne $id (ID ou identifiant de l'article précis-en particulier qui a été supprimé).
  }
  // --------------------------
  /* Déclaration de la méthode createOne() : méthode qui va créer un seul article. */
  public function createOne($article): array { // On récupère $article (tableau contenant un seul article).
    /* : array <=> indique que la fonction createOne() va retourner (aura comme valeur de retour) un array/tableau.
                   RAPPEL : typer les fonctions permet à l'éditeur de code VSCode de trouver des erreurs +
                            efficacement. */
    // -- BINDVALUE() TOUTES NOS VALEURS :
    $this->statementCreateOne->bindValue(':title', $article['title']);
    $this->statementCreateOne->bindValue(':content', $article['content']);
    $this->statementCreateOne->bindValue(':category', $article['category']);
    $this->statementCreateOne->bindValue(':image', $article['image']);
    $this->statementCreateOne->bindValue(':author', $article['author']);
    // PAS BESOIN DE BINDVALUE() D'ID CAR ON VA CRÉER UN NOUVEL ARTICLE QUI N'EXISTE PAS (QUI N'A PAS D'ID-IDENTIFIANT).
    // ÉXECUTION DE statementCreateOne ET DE LA REQUÊTE SQL ASSOCIÉE.
    $this->statementCreateOne->execute(); /* Une fois que l'on a exécuté statementCreateOne de type/classe PDOStatement,
                                             la requête SQL associée va être envoyée et sera exécutée 
                                             depuis le serveur MySQL. */
    /* On retourne ce nouvel article une fois qu'il a été créé.
       PROBLÈME : on n'a pas l'ID ou IDENTIFIANT de ce nouvel article qui vient tout juste d'être créé. */
    return $this->fetchOne($this->pdo->lastInsertId());
    /* Méthode « lastInsertId() » qui existe sur l'objet $pdo de type/classe PDO : 
       => va récupèrer l'ID du dernier élément/objet qui a été créé dans notre BDD de données.
       => va récupérer ici l'ID du dernier article qui vient tout juste d'être créé et qui est 
          sur la dernière ligne/rangée dans la table article de notre BDD « blog ».
       => Pour ça, on écrit : $this->pdo(objet stocké dans notre instance ArticleDB)->lastlastInsertId().
       Méthode fetchOne() : retourne un seul article.
       return $this->fetchOne($this->pdo->lastInsertId()); => retourne l'objet qui récupère ce nouvel article
                                                              qui vient tout juste d'être créé. */
  }
  // --------------------------
  /* Déclaration de la méthode updateOne() : méthode qui va modifier/mettre à jour un seul article. */
  public function updateOne($article): array { // On récupère $article (tableau contenant un seul article).
    /* : array <=> indique que la fonction updateOne() va retourner (aura comme valeur de retour) un array/tableau.
                   RAPPEL : typer les fonctions permet à l'éditeur de code VSCode de trouver des erreurs +
                            efficacement. */
    // -- BINDVALUE() TOUTES NOS VALEURS :
    $this->statementUpdateOne->bindValue(':title', $article['title']);
    $this->statementUpdateOne->bindValue(':content', $article['content']);  
    $this->statementUpdateOne->bindValue(':category', $article['category']);
    $this->statementUpdateOne->bindValue(':image', $article['image']);
    $this->statementUpdateOne->bindValue(':id', $article['id']);
    $this->statementUpdateOne->bindValue(':author', $article['author']);
    // ÉXECUTION DE statementUpdateOne ET DE LA REQUÊTE SQL ASSOCIÉE.
    $this->statementUpdateOne->execute(); /* Une fois que l'on a exécuté statementUpdateOne de type/classe PDOStatement,
                                             la requête SQL associée va être envoyée et sera exécutée 
                                             depuis le serveur MySQL. */
    // RÉCUPÉRATION DES RÉSULTATS :
    return $article;/* On retourne $article (l'article qui vient d'être modifié) 
                       car on a déjà l'ID-IDENTIFIANT de cet article qui vient d'être modifié. */
  }
  // --------------------------
  /* Déclaration de la méthode fetchUserArticle() : méthode qui va récupérer les articles d'un utilisateur-auteur
                                                    en particulier. */
  public function fetchUserArticle(string $authorId): array {
    /* : array <=> indique que la fonction fetchUserArticle() va retourner (aura comme valeur de retour) un array/tableau.
                   RAPPEL : typer les fonctions permet à l'éditeur de code VSCode de trouver des erreurs +
                            efficacement. */
    // On récupère $authorId (identifiant d'un utilisateur-auteur-propriétaire d'articles en particulier).
    /* BINDVALUE L'ID DE L'UTILISATEUR-AUTEUR-PROPRIÉTAIRE D'ARTICLES EN PARTICULIER SUR LEQUEL ON 
       RÉCUPERE LES ARTICLES : */
    $this->statementReadUserAll->bindValue(':authorId', $authorId);
    // ÉXECUTION DE statementReadUserAll ET DE LA REQUÊTE SQL ASSOCIÉE.
    $this->statementReadUserAll->execute(); /* Une fois que l'on a exécuté statementReadUserAll de type/classe 
                                               PDOStatement, la requête SQL associée va être envoyée et sera exécutée 
                                               depuis le serveur MySQL. */
    // RÉCUPÉRATION DES RÉSULTATS :
    return $this->statementReadUserAll->fetchAll();
    /* On retourne tous les articles d'un utilisateur-auteur-propriétaire d'articles en particulier. */
  }   
}
return new ArticleDB($pdo); /* On retourne une nouvelle instance ArticleDB
                               OU un nouvel objet de type/classe ArticleDB (qui est issu de la classe ArticleDB).
                               Dans les instances qui seront issues de la classe ArticleDB ou objets créés à partir
                               de cette classe ArticleDB, on aura : $pdo (propriété privée de type/classe PDO).
Au moment de créer un objet de type/classe ArticleDB ou d’instancier ArticleDB,
on sera sûr d’accéder à $pdo depuis le contexte global/namespace global de PHP.
Par contre, pour que cela marche, sur toutes nos pages PHP, on doit require le database.php en 1ière instruction 
ou en 1ier require. */
?>