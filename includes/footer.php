    </main>
    <footer>
        <div class="footer-content">
            <div class="footer-section about">
                <h3>About Shopway</h3>
                <p>Your ultimate destination for the latest products with the best prices and service.</p>
                <div class="socialicons">
                    <div class="socialicons1">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
            </div>
            <div class="footer-section links">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="<?php echo BASE_URL; ?>/index.php">Home</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/products.php">Products</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/about.php">About Us</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/contact.php">Contact</a></li>
                </ul>
            </div>
            <div class="footer-section contact">
                <h3>Contact Us</h3>
                <p><i class="fas fa-phone"></i> +123 456 7890</p>
                <p><i class="fas fa-envelope"></i> info@shopway.com</p>
                <p><i class="fas fa-map-marker-alt"></i> 123 Shopping St, Retail City</p>
            </div>
        </div>
        <div class="footer-bottom">
            &copy; <?php echo date('Y'); ?> Shopway | All rights reserved
        </div>
    </footer>
    
    <?php if (isset($extraJS)): foreach($extraJS as $js): ?>
    <script src="<?php echo BASE_URL; ?>/assets/js/<?php echo $js; ?>.js"></script>
    <?php endforeach; endif; ?>
    <script src="<?php echo BASE_URL; ?>/assets/js/main.js"></script>
</body>
</html>