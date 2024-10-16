<?php 
session_start();

$shutdown_status = file_get_contents('../admin/shutdown_status.txt');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>PMS Homepage</title>

    <style>

        html, body {
            width: 100%;
            overflow-x: hidden;
        }
    
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            scroll-behavior: smooth;
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            scroll-behavior: smooth;
            background-color: #f9f9f9;
            color: #333;
            width: 100%;
            user-select: none;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            top: 0;
            width: 100%;
            background-color: transparent;
            color: black;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 1rem 2rem;
            z-index: 1000;
        }

        .title {
            font-size: 1.8rem;
            font-variant: small-caps;
            font-weight: bold;
            color: white;
            text-shadow: 2px 4px 6px rgb(0, 0, 0);
        }

        nav ul {
            list-style-type: none;
            display: flex;
            gap: 1.5rem;
        }

        .nav-link {
            display: flex;
            justify-content: center;
            align-items: center; 
            padding: 0.5rem 1rem;
            border-radius: 20px;
            text-decoration: none;
            font-size: 1rem;
            font-weight: bold;
            color: #333;
            background-color: white;
            border: 2px solid #1a74e2;
            color: #1a74e2;
            transition: background-color 0.3s ease;
        }

        .nav-link:hover,
        .nav-link.active {
            background-color: #0d47a1;
            color: #fff;
        }
        .last-nav:hover {
            background-color: darkred;
            color: #fff;
        }

        .container-home {
            width: 100%;
            background-color: #1a74e2;
        }

        /* Sections styling */
        section {
            height: 100vh;
            padding: 8rem;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            background-color: #f0f0f0;
            transition: background-color 0.3s ease;
        }

        section#home {
            background: linear-gradient(to bottom, #1a74e2 56%, white 44%);
        }
        section#about-us{
            background-color: white;
        }
        section#goal {
            background-color: #1a74e2;
        }
        .home-welcome {
            font-size: 3rem;
            color: white;
            font-family: 'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif;
            margin-bottom: 1.5rem;
        }
        h2 {
            font-size: 3rem;
            color: black;
            font-family: 'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif;
            margin-bottom: 1.5rem;
        }

        p {
            font-size: 1.2rem;
            max-width: 800px;
            color: #555;
        }
        .nav-buttons {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            display: flex;
            gap: 1rem;
        }

        .nav-button {
            background-color: #1a74e2; 
            color: white;
            border: none;
            padding: 1rem;
            border-radius: 50%;
            cursor: pointer;
            font-size: 1.2rem;
            transition: background-color 0.3s ease;
        }

        .nav-button:hover {
            background-color: #0d47a1; 
        }

        footer {
            background-color: #333;
            color: #fff;
            text-align: center;
            padding: 1.5rem;
            position: relative;
        }

        /* Carousel for About Us */
        #about-us {
            padding: 20px; 
            color: #0d47a1;
            text-shadow: 2px 4px 6px rgba(0, 0, 0, 0.5);
        }

        .carousel-container {      
            width: 100%; 
            max-width: 600px;
            margin: 0 auto;
            overflow: hidden; 
        }

        .carousel {
            display: flex; 
            transition: transform 0.5s ease; 
        }

        .carousel-item {
            min-width: 100%; 
            box-sizing: border-box;
            padding: 10px; 
            text-align: center; 
        }

        .carousel img {
            width: 100%; 
            border-radius: 50%; 
        }

        .carousel-item img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 3px solid white;
            object-fit: cover;
            margin-bottom: 1rem;
        }

        .carousel h3 {
            margin: 0.5rem 0;
            color: whitesmoke;
        }

        .carousel p {
            font-size: 1rem;
            color: #555;
        }

        .carousel-button {
            background-color: #1a74e2;
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
        }

        .carousel-button.prev {
            left: 10px;
        }

        .carousel-button.next {
            right: 10px;
        }

        .carousel-button:hover {
            background-color: #155a9a; 
        }
        /*home body*/
        .home-welcome {
            text-transform: uppercase;
            font-size: 2rem;
            letter-spacing: 1px;
        }
        

        .home-body {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: none;
            gap: 1.5rem;
        }
        .home-body:hover>:not(:hover){
            opacity: 0.2    ;
        }
        .card-home-body{    
            background-color: rgb(245, 243, 243);
            border: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            height: 100px;
            width: 50vh;
            box-shadow: 2px 4px 6px rgba(0, 0, 0, 0.5);
            cursor: pointer;
            transition: transform 0.3s, box-shadow 0.3s;
         
        }
        .card-home-body:hover{
            transform: scale(1.3);
        }
        
        .card-home-body,
        .h3{
            padding-top: 15px;
            color: black;
        }
        .card-home-body, 
        p{
            font-size: 15px;
        }
        
        /*Goal Content*/
        .goal-content{
            position: relative;
            height: 300px;
            margin-right: 200px;    
       
        }
        .goal-title{
            position: absolute;           
            background-color: white;     
            border-radius: 5px;
            width: 200px;
            height: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 2px 4px 6px rgba(0, 0, 0, 0.5);
            z-index: 999;
            
        }
        .goal-button{     
            padding-bottom: 0px;
            font-size: 1.5rem;
            background-color: transparent;  
            color: #0d47a1;
            font-weight: bold;
            text-shadow: 2px 4px 6px rgba(0, 0, 0, 0.5);
            cursor: pointer;
            border: none;
            transition: transform 0.3s;
            
        }
        .goal-button:hover{
            color: #04285f;
            font-size: 1.9rem;
            transform: scale(9.3s);
        }
        

        .message {
            display: none;
            position: right;
            padding: 150px;
            width: 100%;
            margin-left: 100px;
            margin-top: -100px;
            color: #0a326e;
            font-size: 1rem;
            transition: opacity 5s ease;
            opacity: 0;
            transform: translateY(50px);
            transition: opacity 0.5s ease, transform 0.5s ease;
        }
        .message::first-letter{
            font-size: 1.4rem;
        }
        .message.show{
            width: 100%;
            padding: 40px;
            display: inline-block;
            opacity: 1;
            transform: translateY(0);
            margin-top: -6vh;
            
        }
        .goal-body {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: transparent;
        }

        /*Announcemnet*/
        .ann-container {
            height: 20px;
            width: 210vh;
            margin-top: 20vh;
            margin-bottom: 40vh;
            border: 1px solid #f5c6cb;     
            background-color: #f8d7da;
            color: #721c24;
            font-weight: bold;    
            display:flex;
            justify-content: center;
            align-items: center;   
            
        }
        .announcement {
            
            height: 20px;
            padding: 0;
            background-color: transparent;    
        }

        /*Scroll Effects*/
        .fade-in {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.8s ease-out, transform 0.8s ease-out;
        }
        .fade-in.visible {
            opacity: 1;
            transform: translateY(0);
        }
        .menuicn {
            opacity: 0;
            cursor: pointer;
            color: white;
        }
   
        /*New update For responsive */
        
        @media (min-width: 300px) and (max-width: 400px) {
            .about-title {
                display: none; 
            }
            
            .carousel-container {
                width: 100vw; 
                margin: 0;
                padding: 0; 
                
            }
            
            .message.show {
                width: 100vw;
                font-size: 15px;

            }
            .menuicn {
                margin-left: 50px;
                font-size: 10px;
                opacity: 1;
                color: black;
            }

            

            /*For navigation btn */
            .navcontainer nav {
                    list-style-type: none;
                    padding: 180px;
                    margin-left: 100px;
                    display: flex;
                    flex-direction: column; 
                    align-items: center;
                    height: 100vh; 
                    justify-content: center;
                    margin-right: 25vh;
                }


        }

        @media (max-width: 600px) {
            .home-body {
                flex-direction: column;
                
            }
            .card-home-body{
                width: 25vh;          
            }
            .card-home-body, p{
                font-size: 12.5px;
            }
        }
        

        @media (max-width: 768px) {

            /*for 2nd sec */
            
         
            .about-title {
                opacity: 1; 
                text-align: center;
                margin-bottom: 1rem; 
                font-size: 1.5rem;
                color: #1a74e2; 
                
            }

            .carousel-container {
                width: 100vw;
                margin: 0; 
                padding: 0  ; 
                border-radius: 0; 
                
            }

            .carousel {
                display: flex;
                transition: transform 0.3s ease-in-out;
                width: 100%; 
            }

            .carousel-item {
                min-width: 100%;
                text-align: center;
            }

            .carousel-item img {
                width: 100%;
                height: auto; 
            }

            .carousel h3 {
                font-size: 1.2rem; 
            }

            .carousel p {
                font-size: 0.9rem; 
            }

            .carousel-button {
                padding: 0.5rem; 
            }

                .navcontainer {
                    display: none; 
                    background-color: white; 
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%; 
                    height: 100%; 
                    z-index: 1000; 
                
                }

                .navcontainer.open {
                    display: block; 
                }

                .navcontainer nav ul {
                    list-style-type: none;
                    padding: 20px;
                    margin: 0;
                    display: flex;
                    flex-direction: column; 
                    align-items: center;
                    height: 100vh; 
                    justify-content: center;
                    margin-right: 25vh;
                }

                .navcontainer nav ul li {
                    margin: 10px 0;
                }

                .navcontainer nav ul li a {
                    text-decoration: none;
                    color: black;
                    font-size: 20px;
                    padding: 10px;
                    display: block;
                    width: 100%;
                    text-align: center;
                }

                .navcontainer nav ul li a:hover {
                    background-color: #0d47a1;
                    color: #fff;
                        
                }
                
                .in-header {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    gap: 22vh;
                }

                .menuicn {
                    color:black;
                    font-size: 20px;
                    opacity: 1;
                }  

            }

            header{
                flex-direction: column;
            }
            .home-welcome{
                font-size: 1rem;
            }
            

           .goal-title{
                width: 100%;
                margin-top: -30px;
                height: auto;
                margin-bottom: 1rem;
                position: static;
                box-shadow: none;
                text-align: center;
                margin-left: 100px;
           }
           .goal-button{  
                font-size: 1.5rem;
                padding: 1rem;
                transition: none;
           }
           .goal-body{         
                width: 80vh;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                padding: 1rem;
           }
           .message{
                margin-left: 33vh;
                font-size: 1rem;
                margin-top: 1rem;
                padding: 0.5rem;
                width: 90%;
                transform: none;     
           }
           .message.show{
            display: block;
            opacity: 1; 
           }
           body {
            overflow: hidden;
           }
        

        @media (min-width: 769px) and (max-width: 1024px) {
        .menuicn {
            opacity: 1px;
            color:black;
        }
        header {
            padding: 1.5rem;
        }

        .home-welcome {
            font-size: 1.5rem;
           
        }

        .card-home-body {
            width: 35vh;
            
        }

        .goal-title{
            width: 100%;
            margin-top: -30px;
            height: auto;
            margin-bottom: 1rem;
            position: static;
            box-shadow: none;
            text-align: center;
            margin-left: 100px;
        }
        .goal-button{  
            font-size: 1.5rem;
            padding: 1rem;
            transition: none;
        }

        .goal-body{         
            width: 80vh;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 1rem;
        }
        .message{
            margin-left: 33vh;
            font-size: 1rem;
            margin-top: 1rem;
            padding: 0.5rem;
            width: 90%;
            transform: none;     
        }
        .message.show{
            display: block;
            opacity: 1; 
        }
        .carousel-container {
                width: 100vw; 
                height: 100vw;
                margin: 0;
                padding: 0; 
                background-color: red;
            }
    }
    /*Scroll */
    body::-webkit-scrollbar {
        width: 15px;  
        height: 1px;  
    }
    body::-webkit-scrollbar-track {
        background-color: #f1f1f1;
        border-radius: 10px;
    }
    body::-webkit-scrollbar-thumb {
        background-color: #3498db;
        border-radius: 10px;
        border: 3px solid #ffffff;
    }
    body::-webkit-scrollbar-thumb:hover {
        background-color: #2980b9;
    }
    </style>
</head>

<body>

<header>
    <div class="in-header">

        <h1 class="title">PaBook</h1>
        <i class="fas fa-bars icn menuicn" id="menuicn"></i>
        
    </div>


    
    <div class="navcontainer">
        <nav>
            <ul>
                <li><a href="#home" class="nav-link">Home</a></li>
                <li><a href="#about-us" class="nav-link">Contributor</a></li>
                <li><a href="#goal" class="nav-link">Goal</a></li>
                <li><a href="index.php" class="nav-link">Dashboard</a></li>
                <li><a href="logout.php" class="nav-link last-nav">Logout</a></li>
            </ul>
        </nav>
    </div>
    
</header>

    <section id="home" class="container-home">  
        <div class="home-content">

        <?php if ($shutdown_status === '1') : ?>   
            <section class="announcement">
                <div class="ann-container">
                    <p>Announcement: The Wesite will be shit down for maintainance. HAHAHAH</p>
                </div>
            </section>
        <?php endif; ?>
        
            <h2 class="home-welcome"><strong>Welcome to The Parking Management System</strong></h2>

                <div class="home-body">
                    <div class="card-home-body">
                        <h3>Easy Reservation</h3>
                        <p>Book parking slots in just a few clicks</p>
                    </div>

                    <div class="card-home-body">
                        <h3>Real-time Availability</h3>
                        <p>Check slot availability in real-time</p>
                    </div>

                    <div class="card-home-body">
                        <h3>Secure Payments</h3>
                        <p>Make payments securely online</p>
                    </div>
                </div>
            </div>
        
    </section>

    <section id="about-us">
    <h2 class="about-title">About Us</h2>
    <div class="carousel-container">
        <div class="carousel">
            <div class="carousel-item active">
                <img src="../img/ced.jpg" alt="Person 1">
                <h3>Name</h3>
                <p>Title</p>
            </div>
            <div class="carousel-item">
                <img src="../img/jeep.gif" alt="Person 2">
                <h3>Name</h3>
                <p>Title</p>
            </div>
            <div class="carousel-item">
                <img src="../img/melv.jpg" alt="Person 3">
                <h3>Name</h3>
                <p>Title</p>
            </div>
            <div class="carousel-item">
                <img src="../img/cmu.jpg" alt="Person 4">
                <h3>Name</h3>
                <p>Title</p>
            </div>
            <div class="carousel-item">
                <img src="../img/cmu.jpg" alt="Person 5">
                <h3>Name</h3>
                <p>Title</p>
            </div>
            <div class="carousel-item">
                <img src="../img/cmu.jpg" alt="Person 6">
                <h3>Name</h3>
                <p>Title</p>
            </div>
        </div>
        <button class="carousel-button prev">&lt;</button>
        <button class="carousel-button next">&gt;</button>
    </div>
</section>


    <section id="goal">
        <div class="goal-content">
            <div class="goal-title">
                <h2><button id="showMessage" class="goal-button">   
                    Our Goal
                </button></h2>
            </div>
            <div class="goal-body">
                <span id="message" class="message">Our primary goal is to simplify parking management 
for everyone. We aim to provide a user-friendly system that enhances convenience, efficiency, and security.Our primary goal is to simplify parking management 
for everyone. We aim to provide a user-friendly system that enhances convenience, efficiency, and security.</span>
            </div>
        </div>
    </section>

    <footer>
        <p>&copy; 2024 PaBook. All rights reserved.</p>
    </footer>

    <script>

        //disable
        //Show message
        const button = document.getElementById('showMessage');
        const message = document.getElementById('message');

        button.addEventListener('click', () => {
             setTimeout(() => {
                message.classList.toggle('show');
             }, 1000);
        }); 
        //Active nav link on scroll
        const navLinks = document.querySelectorAll('.nav-link');

        window.addEventListener('scroll', () => {
            let current = '';
            document.querySelectorAll('section').forEach(section => {
                
                const sectionTop = section.offsetTop;
                if (window.pageYOffset >= sectionTop - 60) {
                    current = section.getAttribute('id');
                }
            });
        
            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === `#${current}`) {
                    link.classList.add('active');
                }
            });
        });

        //Carousel functionality
        const carousel = document.querySelector('.carousel');
        const carouselItems = document.querySelectorAll('.carousel-item');
        const prevButton = document.querySelector('.carousel-button.prev');
        const nextButton = document.querySelector('.carousel-button.next');

        let currentSlide = 0;

        function showSlide(index) {
            carousel.style.transform = `translateX(-${index * 100}%)`;
        }

        nextButton.addEventListener('click', () => {
            currentSlide = (currentSlide + 1) % carouselItems.length;
            showSlide(currentSlide);
        });

        prevButton.addEventListener('click', () => {
            currentSlide = (currentSlide -1 + carouselItems.lenth) % carouselItems.length;
            showSlide(currentSlide); 
        });

        //scroll effects

        document.addEventListener('DOMContentLoaded', () => {
            const sections = document.querySelectorAll('section');
            
            const observer = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                    } else {
                        entry.target.classList.remove('visible');
                    }
                });
            }, { threshold: 0.2 });

            sections.forEach(section => {
                section.classList.add('fade-in');
                observer.observe(section);
            });
        });

        //for menu 
        let menuicn = document.querySelector(".menuicn");
        let nav = document.querySelector(".navcontainer");
        let navigationLinks = document.querySelectorAll(".nav-link"); 

        menuicn.addEventListener("click", () => {
            nav.classList.toggle("open"); 
        });


        navigationLinks.forEach(link => {
            link.addEventListener("click", () => {
                nav.classList.remove("open"); 
            });
        });


    </script>

</body>
</html>
