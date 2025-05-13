

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HomePage</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
          /* Basic Reset */
        *{
            padding: 0;
            margin: 0;
            box-sizing: border-box;
        }
        /* Body and main container */
        body {

            margin: 0;
            padding: 0;
            background: url('wow.jpg');

        }

        /*background color sa header*/
        header {
            background-color: #111;
             }

             /* color text sa ngalan sa Shop*/
             h3{
                color: yellow;
             }
             /*positioning sa logo */
            .logo{
               margin-left: 720px;
                justify-content: center;
            }
        
        }
        /* container sa icons*/
        .socialicons{
            width: 100%;
            padding: 70px 30px 20px; 

        }
        /* position para sa social icons*/
        .socialicons1{
            display: flex;
            justify-content: center;
            
        }
        /*margin,background size sa icons  etc..*/
        .socialicons a{
           text-decoration: none;
           padding: 10px;
           background-color: white;
           margin: 10px;
           border-radius: 50%;
        }
        /* para ma emphasize ang icons*/
        .socialicons i{
            font-size: 2em;
        }
            /*  margin sa headernav kanang Home about us sulod sa header etc.. */
            .headernav{
                margin: 5px 0;

        }
            /* para mag row ang placing sa navigation dili pa column etc. */ 
            .headernav ul{
                 list-style-type: none;
                display: flex;
                justify-content: right;
        }
           /* para design color margin para dili mag dikit dikit ang katong home, about us, etcc..*/ 
            .headernav a{
                color: white;
                margin: 20px;
                text-decoration: none;
                opacity: 0.7;
            }
            
            .headernav a:hover{
                opacity: 1;

            }

            /* para sa searchbar margin alignment margin etc..*/
            .searchbar{
                margin-bottom: 150px;
                display: flex;
                cursor: pointer;
                padding: 10px 20px;
                align-items: center;
                border-radius: 30px;
                margin-left: 665px;
                }
           /* container padding sa mga products*/ 
        .container {
            padding: 5px;
            

        }

        /*container sizes margin etc sa mga products*/
        .product {
            display: flex;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 1rem;
            margin: 1rem 0;
            background-color: white;

        }
        /* size image sa product kanang sulod sa container*/
        .product img {
            max-width: 150px;
            margin-right: 1rem;
        }

        .product-details {
            flex: 1;
        }

        .product-title {
            font-size: 1.2rem;
            margin: 0;
        }

        .product-price {
            color: green;
            margin: 0.5rem 0;
        }

        .product button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 3px;
            cursor: pointer;
        }

        .product button:hover {
            background-color: #0056b3;
        }

        .navigation{
            border: solid 1px black;
            background-color: darkgray;
            height: 650px;
            margin: 60px;

        }
        .youtube{
            float: left;
            margin: 30px;
            padding: 20px;

        }
        .map{
            float: right;
            margin: 40px;
        }

/* Slideshow container */
.slideshow-container {
  max-width: 1000px;
  position: relative;
  margin: auto;
}

/* Next & previous buttons */
.prev, .next {
  cursor: pointer;
  position: absolute;
  top: 50%;
  width: auto;
  padding: 16px;
  margin-top: -22px;
  color: white;
  font-weight: bold;
  font-size: 18px;
  transition: 0.6s ease;
  border-radius: 0 3px 3px 0;
  user-select: none;
}

/* Position the "next button" to the right */
.next {
  right: 0;
  border-radius: 3px 0 0 3px;
}

/* On hover, add a black background color with a little bit see-through */
.prev:hover, .next:hover {
  background-color: rgba(0,0,0,0.8);
}

/* Caption text */
.text {
  color: #f2f2f2;
  font-size: 25px;
  padding: 8px 12px;
  position: absolute;
  bottom: 8px;
  width: 100%;
  text-align: center;
  color: green ;
}

/* Number text (1/3 etc) */
.numbertext {
  color: #f2f2f2;
  font-size: 12px;
  padding: 8px 12px;
  position: absolute;
  top: 0;
}

/* The dots/bullets/indicators */
.dot {
  cursor: pointer;
  height: 15px;
  width: 15px;
  margin: 0 2px;
  background-color: #bbb;
  border-radius: 50%;
  display: inline-block;
  transition: background-color 0.6s ease;
}

.active, .dot:hover {
  background-color: #717171;
}

/* Fading animation */
.fade {
  animation-name: fade;
  animation-duration: 1.5s;
}

@keyframes fade {
  from {opacity: .4} 
  to {opacity: 1}
}

/* On smaller screens, decrease text size */
@media only screen and (max-width: 300px) {
  .prev, .next,.text {font-size: 11px}
}


 </style>


</head>
<body>



<header>

    <div class="logo">
    <img src="shopway.jpg" width="100" height="100">
    <Br>
    <h3>SHOPWAY</h3>   

     </div>


    <div class="socialicons">
       <div class="socialicons1">
        <a href=""><i class="fa-brands fa-facebook"></i></a>
        <a href=""><i class="fa-brands fa-instagram"></i></a>
        <a href=""><i class="fa-brands fa-twitter"></i></a>
        <a href=""><i class="fa-brands fa-youtube"></i></a>
        <a href=""><i class="fa-brands fa-tiktok"></i></a>
        </div>
    </div>
        <div class="headernav"> 
        <ul>
            <li><a href="">Home</a></li>
            <li><a href="">About us</a></li>
            <li><a href="">Contact Us</a></li>
            <li><a href="Dashboard.php">Dashboard</a></li>
            <li><a href="loginpage.php">Log In</a></li>
        </ul>
        </div>   
        <div class="searchbar">
            <input type="text" placeholder="Search...">
            <a href="#">
             <i class="fas fa-search"></i>
            </a>
       </div>
   
   
</header>


    <div class="container">
        <!-- Product 1 -->
        <div class="product">

            <img src="iphone.jpg" >
            <div class="product-details">
                <h2 class="product-title">Iphone 12pro max</h2>
                <p class="product-price">₱34,000 </p>
                <button><a href="add_product.php"> Add to Cart</button>
            </div>
        </div>

        <!-- Product 2 -->
        <div class="product">
            <img src="UA.jpg">
            <div class="product-details">
                <h2 class="product-title">Ua  apparel Short vintage</h2>
                <p class="product-price">₱499.99</p>
                <button>Add to Cart</button>
            </div>
        </div>

        <!-- Product 3 -->
        <div class="product">
            <img src="tshirt.jpg">
            <div class="product-details">
                <h2 class="product-title">Ua apparel Tshirt vintage</h2>
                <p class="product-price">₱599.99</p>
                <button>Add to Cart</button>
            </div>
        </div>

        <!-- Product 4 -->
        <div class="product">
            <img src="airpods.jpg">
            <div class="product-details">
                <h2 class="product-title">Airpods gen2pro </h2>
                <p class="product-price">₱3700.00</p>
                <button>Add to Cart</button>
            </div>
        </div>

        <!-- Product 5 -->
        <div class="product">
            <img src="earings.jpg">
            <div class="product-details">
                <h2 class="product-title">aesthetic earings for men</h2>
                <p class="product-price">₱788.99</p>
                <button>Add to Cart</button>
            </div>
        </div>

        <!-- Product 6 -->
        <div class="product">
            <img src="watch.jpg">
            <div class="product-details">
                <h2 class="product-title">vintage watch for menour best seller for the month</h2>
                <p class="product-price">₱1300.00</p>
                <button>Add to Cart</button>
            </div>
        </div>
    </div>
    <div class="navigation">

 <div class="youtube">
    <iframe width="600" height="550" src="https://www.youtube.com/embed/5IERofIpMS4?si=jhZiaFKQxYaGZSdw" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
</div>

<div class="map">
    <iframe width="600" height="550" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3946.1109494816815!2d124.6493727757615!3d8.488592297212453!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x32fff2e7ff6d2ec9%3A0x180566d5585d5301!2sCapitol%20University!5e0!3m2!1sen!2sph!4v1733038711628!5m2!1sen!2sph" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
</div>
</div>

<div class="slideshow-container">

<div class="mySlides fade">
  <div class="numbertext">1 / 3</div>
  <img src="iphone.jpg" style="width:100%">
  <div class="text">our best seller for the month 30% off</div>
</div>

<div class="mySlides fade">
  <div class="numbertext">2 / 3</div>
  <img src="tshirt.jpg" style="width:100%">
  <div class="text">our best seller for the month 10% off</div>
</div>

<div class="mySlides fade">
  <div class="numbertext">3 / 3</div>
  <img src="airpods.jpg" style="width:100%">
  <div class="text">our best seller for the month 25% off</div>
</div>

<a class="prev" onclick="plusSlides(-1)">❮</a>
<a class="next" onclick="plusSlides(1)">❯</a>

</div>

<div style="text-align:center">
  <span class="dot" onclick="currentSlide(1)"></span> 
  <span class="dot" onclick="currentSlide(2)"></span> 
  <span class="dot" onclick="currentSlide(3)"></span> 
</div>
<script>
let slideIndex = 1;
showSlides(slideIndex);

function plusSlides(n) {
  showSlides(slideIndex += n);
}

function currentSlide(n) {
  showSlides(slideIndex = n);
}

function showSlides(n) {
  let i;
  let slides = document.getElementsByClassName("mySlides");
  let dots = document.getElementsByClassName("dot");
  if (n > slides.length) {slideIndex = 1}    
  if (n < 1) {slideIndex = slides.length}
  for (i = 0; i < slides.length; i++) {
    slides[i].style.display = "none";  
  }
  for (i = 0; i < dots.length; i++) {
    dots[i].className = dots[i].className.replace(" active", "");
  }
  slides[slideIndex-1].style.display = "block";  
  dots[slideIndex-1].className += " active";
}
</script>


</body>
</html>

