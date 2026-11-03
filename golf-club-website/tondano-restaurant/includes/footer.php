<footer class="footer">
    <div class="footer-container">
        <div class="footer-left">
            <div class="footer-logo">
                <img src="/tondano-restaurant/assets/logo.avif" alt="Golf Tondano Terrace" class="logo-img">
            </div>
            <p class="footer-desc">The food at Klub Golf Bogor Raya is a testament to the excellence the Club provide. Golf Tondano Terrace provides delicious Indonesian, Korean, Chinese, Japanese and European / Western food. Each dish is perfectly prepared, cooked, presented, and served to please its golfer.</p>
        </div>

        <div class="footer-links">
            <div class="footer-section">
                <h4>About</h4>
                <a href="#">Ala Carta Menu</a>
                <a href="#">Appetizer</a>
                <a href="#">Main Course</a>
                <a href="#">Dessert</a>
                <a href="#">Beverage</a>
            </div>

            <div class="footer-section">
                <h4>Services</h4>
                <a href="#">Wedding Package</a>
                <a href="#">Meeting Package</a>
                <a href="#">Gathering & Birthday Package</a>
            </div>

            <div class="footer-section">
                <h4>Quick Links</h4>
                <a href="#">Event</a>
                <a href="#">Gallery</a>
                <a href="#">Contact Us</a>
            </div>
        </div>
    </div>

    <div class="footer-bottom">
        <div class="footer-legal">
            <a href="#">Privacy Policy</a>
            <a href="#">Terms and Conditions</a>
        </div>
        <div class="footer-copyright">
            <p>©2020 Golf Tondano Terrace member of Klub Golf Bogor Raya.</p>
            <p>Proudly created with Creative Design Marketing Sales Klub Golf Bogor Raya</p>
        </div>
        <div class="footer-partnered">
            <img src="/tondano-restaurant/assets/logo 2.avif" alt="Partnered Logo" class="partner-logo">
        </div>
    </div>
</footer>

<style>
.footer {
    background-image: url('/tondano-restaurant/assets/footer.avif');
    background-size: cover;
    background-position: center;
    color: #fff;
    padding: 0;
    margin: 0;
    width: 100%;
    /* hapus vw dan margin negative yang menyebabkan scroll horizontal */
}

.footer-container {
    width: 100%;
    max-width: 1200px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 50px;
    padding: 50px 20px 0 20px;
}

.footer-left {
    max-width: 400px;
}

.footer-logo {
    margin-bottom: 20px;
}

.footer-desc {
    color: #fff;
    line-height: 1.6;
    font-size: 0.9em;
}

.footer-links {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 30px;
}

.footer-section h4 {
    color: #DAA520;
    margin-bottom: 20px;
    font-size: 1.1em;
}

.footer-section a {
    display: block;
    color: #fff;
    text-decoration: none;
    margin-bottom: 10px;
    font-size: 0.9em;
    transition: color 0.3s ease;
}

.footer-section a:hover {
    color: #DAA520;
}

.footer-bottom {
    max-width: 100%;
    margin: 0;
    padding: 20px 50px;
    border-top: 1px solid rgba(255,255,255,0.1);
    display: grid;
    grid-template-columns: 1fr 2fr 1fr;
    align-items: center;
    background: #000;
    margin-bottom: 0; /* ensure no bottom margin */
}

.footer-legal a {
    color: #fff;
    text-decoration: none;
    font-size: 0.8em;
    margin-right: 20px;
}

.footer-copyright {
    text-align: center;
    font-size: 0.8em;
    color: #fff;
}

.footer-partnered {
    text-align: right;
}

.partner-logo {
    max-height: 50px;
}

@media (max-width: 768px) {
    .footer-container {
        padding: 20px;
        grid-template-columns: 1fr;
    }
    
    .footer-bottom {
        padding: 20px;
    }
}
</style>
</body>
</html>
