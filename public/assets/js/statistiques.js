// 
// public/assets/statistiques.js
// appelé depuis templates/admin/categorie/stats.html.twig
// controller : AdminController::stats()
//

window.onload = () => {
   // on identifie la balise à traiter
   let categorie = document.querySelector('#categorie')
   // on récupère les données passées en dataset
   let nom = JSON.parse(categorie.dataset.nom);
   let count = JSON.parse(categorie.dataset.count);
   let couleur = JSON.parse(categorie.dataset.couleur);

   // lier le contexte au noeud canvas
   
   // Contruction du graph 
   var categorieChart = new Chart(categorie, {

       type: 'pie',
       
       data: {
           labels: nom,
           datasets: [{
               label: 'Catégories',
               data: count,
               backgroundColor: couleur,
               borderColor: couleur,
               borderWidth: 1
           }]
       },
       options: {
           scales: {
               y: {
                   beginAtZero: true
               }
           }
       }
   });

   // on identifie la balise à traiter
   let article = document.querySelector('#article')
   // on récupère les données passées en dataset
   let dates = JSON.parse(article.dataset.dates);
   let articleCount = article.dataset.articleCount;
   alert(dates)
   // lier le contexte au noeud canvas
   // Contruction du graph 
   let articleChart = new Chart(article, {

       type: 'line',
       
       data: {
           labels: dates,
           datasets: [{
               label: 'Articles par jours',
               data: articleCount,
           }]
       },
       options: {
           scales: {
               y: {
                   beginAtZero: true
               }
           }
       }
   });

}