window.onload = () => {
    // on cherche tous les type = checkbox
    let activer = document.querySelectorAll("[type=checkbox]")
    // sur chaque lien je mets un ecouteur d'évènement sur click
    for(let bouton of activer){
        bouton.addEventListener("click", function(){
            // action sur le click
            let xmlHttp = new XMLHttpRequest
            // je vais chercher l'id de l'article
            xmlHttp.open("get", `/admin/article/activer/${this.dataset.id}` )
            xmlHttp.send()
        })
    }
}
