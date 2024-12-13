<?php

session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php
	include "includes/scrips.php";
	?>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,600|Open+Sans" rel="stylesheet">
	<title>Sistema Ventas</title>
</head>
	<?php
		include "includes/header.php";
		include "../con.php";
	?>

<body>
    <header class="carousel">
        <div class="carousel-slide active" style="background-image: url('https://dynamic-media-cdn.tripadvisor.com/media/photo-o/21/e3/e3/7e/mipig-cafe-5.jpg?w=1200&h=-1&s=1');">
            <div class="carousel-text">
                <h1 class="color1">Bienvenido a Dulce Pocilga</h1>
                <p class="color4">Para vivir una vida tranquilga</p>
            </div>
        </div>
        <div class="carousel-slide" style="background-image: url('https://dynamic-media-cdn.tripadvisor.com/media/photo-o/2a/dd/06/97/caption.jpg?w=1200&h=-1&s=1');">
            <div class="carousel-text">
                <h1 class="color1">Descubre la magia</h1>
                <p class="color4">Un café, muchos cerditos y momentos inolvidables.</p>
            </div>
        </div>
        <div class="carousel-slide" style="background-image: url('https://dynamic-media-cdn.tripadvisor.com/media/photo-o/19/7b/78/00/2-2.jpg?w=1200&h=-1&s=1');">
            <div class="carousel-text">
                <h1 class="color1">Un café con amor</h1>
                <p class="color4">Te esperamos con los brazos abiertos y un café caliente.</p>
            </div>
        </div>
    <div class="paginadores">
        <button class="carousel-btn prev">❮</button>
        <button class="carousel-btn next">❯</button>
    </div>
    </header>

    <!---<section class="about">
        <div class="containerDataUser">
            <div class="logoUser">
                <img src="img/logopata.png">
            </div>
            <div class="divDataUser">
        <h2 class="color1"><i class="fa-solid fa-mug-hot"></i>Sobre Nosotros</h2>
        <p class="color2">
            En <span class="bold">Dulce Pocilga</span> creemos en el poder de la tranquilidad. Ofrecemos un espacio acogedor donde los cerditos y el café se combinan para crear una experiencia única. Ya sea para relajarte o trabajar, aquí encontrarás tu lugar.
        </p>
    </div>
</div>
</div>
<div class="containerDataEmpresa">
            <div class="logoEmpresa">
                <img src="img/logoEmpresa.jpeg">
            </div>
    </div>
    </section> ----->
    <div class="divInfoSistema">
    <div class="containerPerfil">
        <div class="containerDataUser">
            <div class="logoUser">
                <img src="img/logopata.png">
            </div>
            <div class="divDataUser">
        <h2 class="color1"><i class="fa-solid fa-mug-hot"></i> Sobre Nosotros</h2><br>
        <p class="color2">
            En <span class="bold">Dulce Pocilga</span> creemos en el poder de la tranquilidad. Ofrecemos un espacio acogedor donde los cerditos y el café se combinan para crear una experiencia única. Ya sea para relajarte o trabajar, aquí encontrarás tu lugar.
        </p>
    </div>
        </div>
        <div class="containerDataEmpresa">
                <img src="https://tokyofox.net/wp-content/uploads/2023/02/mipig-cafe-harajuku-pig-cafe-43.jpg" width="100%" height="400px">
        </div>
    </div>
</div>

    <!--<section class="services">
        <h2 class="color1">Nuestros Servicios</h2>
        <div class="service-item color3">
            <h3>Zona de Relajación</h3>
            <p>Disfruta de la compañía de cerditos mientras saboreas tu café favorito.</p>
        </div>
        <div class="service-item color3">
            <h3>Eventos Temáticos</h3>
            <p>Organiza reuniones y eventos en un ambiente único y adorable.</p>
        </div>
        <div class="service-item color3">
            <h3>Delicias Artesanales</h3>
            <p>Prueba nuestra selección de postres y bebidas preparadas con amor.</p>
        </div>
    </section>-->
    <section class="products">
    <h2 class="color31">Algunas de nuestras Recetas</h2>
    <div class="product-grid">
        <div class="product-card">
            <img src="https://guacamole.radioformula.com.mx/resizer/7QtAhoikMLBonbimFOsmx3SGmHc=/arc-photo-radioformula/arc2-prod/public/OGE77ULCQBCGDPW66G2F57KLTQ.jpg" alt="Turbo cerdito">
            <div class="product-info">
                <h3>Turbo Galletas</h3>
                <p>$59.99</p>
                <button class="btn-details">Ver Detalles</button>
            </div>
        </div>
        <div class="product-card">
            <img src="https://e0.pxfuel.com/wallpapers/591/419/desktop-wallpaper-pinkypiggu-kawaii-piggies-bento-recipe-youtube-video-kawaii-lunch.jpg" alt="Poderoso Bento">
            <div class="product-info">
                <h3>Poderoso Bento</h3>
                <p>$109.99</p>
                <button class="btn-details">Ver Detalles</button>
            </div>
        </div>
        <div class="product-card">
            <img src="img/Marraneiro.jpg" alt="Bolso de Cuero">
            <div class="product-info">
                <h3>Pansillos</h3>
                <p>$39.99</p>
                <button class="btn-details">Ver Detalles</button>
            </div>
        </div>
        <div class="product-card">
            <img src="https://www.gastrolabweb.com/u/fotografias/m/2022/2/14/f1280x720-25623_157298_5050.jpg" alt="Malteada">
            <div class="product-info">
                <h3>Malteada Fresa</h3>
                <p>$49.99</p>
                <button class="btn-details">Ver Detalles</button>
            </div>
        </div>
    </div>
</section>

    <footer class="footer">
        <p class="color5">© 2024 Dulce Pocilga. Todos los derechos reservados.</p>
    </footer>

    <script>
        const slides = document.querySelectorAll('.carousel-slide');
const prevBtn = document.querySelector('.carousel-btn.prev');
const nextBtn = document.querySelector('.carousel-btn.next');
let currentIndex = 0;

function showSlide(index) {
    slides.forEach((slide, i) => {
        slide.style.left = i === index ? '0' : '100%';
    });
}

prevBtn.addEventListener('click', () => {
    currentIndex = (currentIndex === 0) ? slides.length - 1 : currentIndex - 1;
    showSlide(currentIndex);
});

nextBtn.addEventListener('click', () => {
    currentIndex = (currentIndex === slides.length - 1) ? 0 : currentIndex + 1;
    showSlide(currentIndex);
});

// Mostrar la primera diapositiva al cargar la página
showSlide(currentIndex);

    </script>

    <script src="scripts.js"></script>
</body>
</html>
