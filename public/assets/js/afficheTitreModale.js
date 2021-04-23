//
// Fichier afficheTitreModale.js appelé depuis afficher.html.twig
//
// Au chargement de la page on affiche le titre de l'annonce dans la modale de contact
//
window.onload = () => {
    document.querySelector("#article_contact_titre").value = "{{article.titre | raw}}"

    // traitement pour les réponses aux commentaires
    document.querySelectorAll("[data-reply]").forEach(element => {
            element.addEventListener("click", function(){
                document.querySelector("#commentaire_parentId").value = this.dataset.id;
            });
        });
}