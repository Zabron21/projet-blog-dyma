/* Affichage sur Mobile : récupération d'une référence au bouton à 3 barres "header-mobile-icon"
                          (qui est placé en haut à droite sur l'écran Mobile) : */
const headerMobileButton = document.querySelector('.header-mobile-icon');
// => on retourne l'Element HTML contenant le nom "header-mobile-icon" : <div class="header-mobile-icon">
// -------------------------------
/* Affichage sur Mobile : récupération d'une référence à la liste du « menu mobile » "header-mobile-list" : */
const headerMobileList = document.querySelector('.header-mobile-list');
/* => on retourne l'Element HTML contenant le nom "header-mobile-list" : <ul class="header-mobile-list> */
// -------------------------------------------------------------------------------------------
/* Sur la référence au bouton à 3 barres "header-mobile-icon" (qui est placé en haut à droite sur l'écran Mobile),
   on applique un évènement avec la méthode addEventListener() : */
headerMobileButton.addEventListener('click', 
/* Méthode « addEventListener() » : pour définir un évènement à appliquer sur 1 élément HTML précis.
   headerMobileButton.addEventListener('click'
    => on définit l'évènement 'click' qui va s'appliquer sur le HTMLElement headerMobileButton. 
    => Cet évènement 'click' se produira lorsque l'utilisateur cliquera sur le bouton headerMobileButton
       (bouton à 3 barres "header-mobile-icon" qui est placé en haut à droite sur l'écran Mobile). */
  () => {
    headerMobileList.classList.toggle('show');
  });
/*- () => { }
    => on définit le « event handler » (FONCTION DE RAPPEL/FONCTION DE CALLBACK/CALLBACK/CLOSURE
                                        QUI EST DÉFINIE COMME UNE FONCTION FLÉCHÉE).
       Dans cette fonction fléchée, on choisit un évènement à DISPATCHER-RÉPARTIR sur un autre élément HTML précis.
  - headerMobileButton.addEventListener('click', () => {headerMobileList.classList.toggle('show');});
    => quand l'utilisateur cliquera sur le bouton headerMobileButton
      (bouton à 3 barres "header-mobile-icon" qui est placé en haut à droite sur l'écran Mobile),
       on va DISPATCHER-RÉPARTIR, de manière artificielle, l'évènement 'show' sur 'headerMobileList'
      (qui est une référence à la liste du « menu mobile »).
      Méthode « toogle() » : permet de basculer entre la méthode hide() et la méthode show() pour un élément 
                             sélectionné.
      headerMobileList.classList.toggle('show') : on va basculer entre le fait de cacher, avec hide(), 
                                                  et le fait de montrer, avec show(),
                                                  la liste du « menu mobile ». 
    => cela aura comme effet de déplier/cacher/re-déplier/re-cacher le « menu mobile ».
    => le « menu mobile » est maintenant dynamique. */