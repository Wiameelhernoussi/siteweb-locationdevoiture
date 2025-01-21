<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Luxury World Cars - À Propos</title>
  <style>
    /* Styles généraux */
    body, html {
      margin: 0;
      padding: 0;
      font-family: 'Arial', sans-serif;
    }

    /* Barre de navigation */
    .navbar {  
      background-color: #333; /* Fond gris foncé */  
      padding: 10px;  
      text-align: center;  
    }  

    .navbar a {  
      color: #fff; /* Texte blanc */  
      padding: 10px 15px;  
      text-decoration: none;  
      display: inline-block;  
    }  

    .navbar a:hover {  
      color: #f0b42f;  
    }  

    .navbar span {  
      color: #ffb74d; /* Texte orange */  
      margin-left: 15px;  
    }

    /* Section À Propos */
    .about-section {
      position: relative;
      height: 100vh;
      background: url('https://www.largus.fr/images/styles/max_1300x1300/public/2024-09/Bentley-Continental-GT-Speed-2024-essai-DR-16.jpg?itok=HerMXKm5') no-repeat center center/cover;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      color: white;
    }

    .about-section::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.5);
    }

    .about-content {
      position: relative;
      z-index: 2;
    }

    .about-content h1 {
      font-size: 4em;
      color: #d4af37;
      margin: 0;
      text-transform: uppercase;
    }

    .about-content p {
      font-size: 1.2em;
      margin-top: 10px;
    }

    /* Section du contenu supplémentaire */
    .section-container {
      display: flex;
      align-items: stretch;
      justify-content: space-between;
      padding: 50px;
    }

    /* Colonne Texte */
    .text-column {
      flex: 1;
      padding: 50px;
      background: #fff;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .text-column h1 {
      font-size: 2.5em;
      font-weight: bold;
      margin-bottom: 10px;
      text-align: left;
    }

    .text-column h2 {
      font-size: 1.5em;
      color: #d4af37;
      font-weight: bold;
      margin-bottom: 20px;
    }

    .text-column p {
      font-size: 1em;
      line-height: 1.6;
      color: #333;
      text-align: justify;
    }

    .text-column p strong {
      font-weight: bold;
    }

    .text-column hr {
      width: 100px;
      border: none;
      border-top: 2px solid #d4af37;
      margin: 20px 0;
    }

    /* Colonne Image */
    .image-column {
      flex: 1;
      background: url('https://hips.hearstapps.com/hmg-prod/images/2025-bentley-bentayga-2-672cf19b3eb4c.jpg?crop=0.614xw:0.517xh;0.200xw,0.308xh&resize=2048:*') no-repeat center center/cover;
      background-size: cover;
    }

    /* Section Cartes */
    .card-container {
      display: flex;
      justify-content: center;
      align-items: center;
      flex-wrap: wrap;
      gap: 20px;
      padding: 20px;
    }

    .card {
      position: relative;
      width: 300px;
      height: 200px;
      background: linear-gradient(-45deg, #f89b29 0%, rgb(165, 163, 6) 100%);
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
      transform-style: preserve-3d;
      transition: transform 0.6s cubic-bezier(0.23, 1, 0.320, 1);
    }

    .card__content {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%) rotate(-45deg);
      width: 100%;
      height: 100%;
      padding: 20px;
      box-sizing: border-box;
      background-color: #73681a;
      opacity: 0;
      transition: all 0.6s cubic-bezier(0.23, 1, 0.320, 1);
    }

    .card__images {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 10px;
      width: 100%;
      height: 100%;
    }

    .card__images img {
      width: 90px;
      height: 90px;
      object-fit: cover;
      border-radius: 5px;
    }

    .card:hover .card__content {
      transform: translate(-50%, -50%) rotate(0deg);
      opacity: 1;
    }

    .card:hover {
      transform: rotate(-5deg) scale(1.1);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    }

    .card__title {
      margin: 0;
      font-size: 24px;
      color: #c7b108;
      font-weight: 700;
    }

    .card__description {
      margin: 10px 0 0;
      font-size: 14px;
      color: rgb(234, 232, 225);
      line-height: 1.4;
    }

  </style>
</head>
<body>

  <!-- Barre de navigation -->
  <div class="navbar">  
    <a href="home.php">Homepage</a>  
    <a href="products.php">Products</a>  
    <a href="profile.php">Profile</a>    
    <a href="logout.php">Log out</a>   
  </div>

  <!-- Section À Propos -->
  <section class="about-section">
    <div class="about-content">
      <h1>À Propos</h1>
      <p>Luxe World cars met à votre disposition voitures de prestige au meilleurs prix</p>
    </div>
  </section>

  <!-- Section du contenu supplémentaire -->
  <section class="section-container">
    <!-- Colonne Texte -->
    <div class="text-column">
      <h1>Luxe World Cars</h1>
      <h2>AVEC UNE <span style="color: #d4af37;">LARGE GAMME DE VÉHICULES</span></h2>
      <hr>
      <p>
        Chez <strong>Luxe World Cars</strong>, nous transformons vos déplacements en une expérience inoubliable. Nous nous engageons à offrir un service d'exception, alliant élégance, confort et performance. Grâce à notre vaste flotte de véhicules prestigieux, incluant des marques emblématiques telles que <strong>Ferrari</strong>, <strong>Alfa Romeo</strong>, et <strong>Porsche</strong>, chaque trajet devient un moment unique.  
        Que ce soit pour un événement spécial, un voyage d'affaires ou simplement pour le plaisir de conduire une voiture de luxe, nous sommes là pour réaliser vos rêves. Faites confiance à <strong>Luxe World Cars</strong> pour une expérience de conduite sans compromis, où chaque détail compte.
      </p>
    </div>

    <!-- Colonne Image -->
    <div class="image-column"></div>
  </section>

  <!-- Section Cartes -->
  <div class="card-container">
    <!-- Première carte -->
    <div class="card">
      <div class="card__images">
        <img src="https://img.freepik.com/photos-premium/mix-logo-halloween_1029974-7370.jpg" alt="Image 1">
      </div>
      <div class="card__content">
        <p class="card__title">Voiture Luxe</p>
        <p class="card__description">Découvrez nos voitures de luxe, conçues pour offrir confort et performance exceptionnels.</p>
      </div>
    </div>

    <!-- Deuxième carte -->
    <div class="card">
      <div class="card__images">
        <img src="https://media.istockphoto.com/id/913855596/fr/vectoriel/conception-de-voiture-de-sport-sur-fond-noir-avec-reflet-illustration-vectorielle.jpg?s=612x612&w=0&k=20&c=UVT4JiRGZewuO8naue1JTjX4LFsdmdr7q3iBmnKECTQ=" alt="Image 1">
      </div>
      <div class="card__content">
        <p class="card__title">Luxe Sportif</p>
        <p class="card__description">Nos modèles sportifs vous offrent une expérience de conduite ultime avec une touche de luxe.</p>
      </div>
    </div>

    <!-- Troisième carte -->
    <div class="card">
      <div class="card__images">
        <img src="https://img.pikbest.com/wp/202404/eco-friendly-transportation-concept-3d-render-of-a-green-car-outline_9799711.jpg!w700wp" alt="Image 1">
      </div>
      <div class="card__content">
        <p class="card__title">Voiture Écologique</p>
        <p class="card__description">Profitez de nos voitures écologiques et performantes, idéales pour l'avenir durable.</p>
      </div>
    </div>
  </div>

</body>
</html>
