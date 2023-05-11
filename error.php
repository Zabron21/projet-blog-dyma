<!DOCTYPE html>
<html lang="en">
<head>
  <?php require_once 'includes/head.php' ?>
  <link rel="stylesheet" href="/public/css/index.css">
  <title>Erreur</title>
</head>
<body>
  <div class="container">
    <?php require_once 'includes/header.php' ?>
    <div class="content"><!-- À l'intérieur du contenu de notre page, on a le message de l'erreur. -->
      <h1 style="font-size:7rem;text-align:center;">Oops une erreur est survenue</h1>
      <!-- font-size : définit la taille de fonte utilisée pour le texte. Ici 7 rem = 70 pixels.
           text-align : center = alignement du texte au centre selon l'axe horizontal. -->
    </div>
    <?php require_once 'includes/footer.php' ?>
  </div>
</body>
</html>