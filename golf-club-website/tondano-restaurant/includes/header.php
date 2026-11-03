<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tondano Restaurant</title>
    <link rel="icon" type="image/x-icon" href="/tondano-restaurant/assets/logo.avif">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <style>
        /* CSS Reset dan Global Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            overflow-x: hidden;
            position: relative;
            width: 100%;
            max-width: 100%;
        }

        body {
            color: #fff; /* Tambahkan warna default untuk semua teks */
        }

        /* Tambahkan style global untuk headings */
        h1, h2, h3, h4, h5, h6, p {
            color: #fff;
        }

        /* Tambahkan style global untuk links */
        a {
            color: #fff;
        }

        /* Container responsif */
        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
            overflow: hidden;
        }

        .nav-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            height: 80px;
            background: rgba(0,0,0,0.9);
            transition: all 0.3s ease;
            transform: translateY(0);
        }

        .nav-header.scrolled {
            height: 60px;
            background: #000;
        }

        .nav-header.hidden {
            transform: translateY(-100%);
        }

        .nav-container {
            height: 100%;
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
        }

        .logo-container {
            height: 100%;
            display: flex;
            align-items: center;
        }

        .logo-image {
            height: 70%;
            width: auto;
            transition: all 0.3s ease;
        }

        .nav-header.scrolled .logo-image {
            height: 50%;
        }

        .nav-links {
            display: flex;
            gap: 30px;
        }

        .nav-links a {
            color: #fff;
            text-decoration: none;
            font-size: 16px;
            transition: color 0.3s ease;
        }

        .nav-links a:hover {
            color: #DAA520;
        }

        .hamburger {
            display: none;
            cursor: pointer;
            border: none;
            background: none;
            padding: 10px;
        }

        .hamburger span {
            display: block;
            width: 25px;
            height: 3px;
            background-color: #fff;
            margin: 5px 0;
            transition: all 0.3s ease;
        }

        /* Fix untuk iframe responsive */
        .map {
            width: 100%;
            overflow: hidden;
        }

        .map iframe {
            width: 100%;
            max-width: 100%;
        }

        @media (max-width: 768px) {
            body {
                overflow-x: hidden;
                position: relative;
            }

            .hamburger {
                display: block;
                z-index: 1001;
            }

            .nav-links {
                position: fixed;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100vh;
                background: rgba(0,0,0,0.95);
                flex-direction: column;
                justify-content: center;
                align-items: center;
                padding: 20px 0;
                transition: 0.3s ease;
                z-index: 1000;
            }

            .nav-links a {
                padding: 15px 0;
                font-size: 18px;
            }

            .nav-links.active {
                left: 0;
            }

            .nav-header.scrolled .nav-links {
                height: 100vh;
                top: 0;
            }

            .nav-container {
                position: relative;
                z-index: 1001;
            }

            .map iframe {
                width: 100%;
            }
        }

        @media (max-width: 480px) {
            /* Gabungkan semua media query 480px di sini */
            .nav-links a {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <nav class="nav-header">
        <div class="nav-container">
            <div class="logo-container">
                <img src="/tondano-restaurant/assets/logo.avif" alt="Tondano Logo" class="logo-image">
            </div>
            <button class="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </button>
            <div class="nav-links">
                <a href="index.php">Home</a>
                <a href="#about">About</a>
                <a href="#menu">Menu</a>
                <a href="#location">Location</a>
            </div>
        </div>
    </nav>

    <script>
        let lastScroll = 0;
        const header = document.querySelector('.nav-header');
        const scrollThreshold = 100;

        window.addEventListener('scroll', () => {
            const currentScroll = window.pageYOffset;
            
            // Add scrolled class for compact style
            if (currentScroll > scrollThreshold) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }

            // Hide header when scrolling down, show when scrolling up
            if (currentScroll > lastScroll && currentScroll > scrollThreshold) {
                header.classList.add('hidden');
            } else {
                header.classList.remove('hidden');
            }

            lastScroll = currentScroll;
        });

        // Initialize AOS
        document.addEventListener('DOMContentLoaded', () => {
            AOS.init();
        });

        // Mobile menu toggle
        const hamburger = document.querySelector('.hamburger');
        const navLinks = document.querySelector('.nav-links');

        hamburger.addEventListener('click', () => {
            navLinks.classList.toggle('active');
        });

        // Close mobile menu when clicking outside
        document.addEventListener('click', (e) => {
            if (!hamburger.contains(e.target) && !navLinks.contains(e.target)) {
                navLinks.classList.remove('active');
            }
        });
    </script>
</body>
</html>
