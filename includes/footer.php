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
                    <li><a href="index.php">Home</a></li>
                    <li><a href="products.php">Products</a></li>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="contact.php">Contact</a></li>
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
    
    <?php if (isset($extraJS) && is_array($extraJS)): ?>
    <script>
        <?php foreach($extraJS as $jsFile): ?>
            <?php include_once "assets/js/{$jsFile}.js"; ?>
        <?php endforeach; ?>
    </script>
    <?php endif; ?>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mobile menu toggle
            const mobileMenuButton = document.querySelector('.mobile-menu-toggle');
            if (mobileMenuButton) {
                mobileMenuButton.addEventListener('click', function() {
                    const nav = document.querySelector('.headernav');
                    nav.classList.toggle('show');
                });
            }

            // Product quantity selector
            const quantityInputs = document.querySelectorAll('.quantity-input');
            quantityInputs.forEach(input => {
                const minusBtn = input.parentElement.querySelector('.quantity-minus');
                const plusBtn = input.parentElement.querySelector('.quantity-plus');
                
                if (minusBtn) {
                    minusBtn.addEventListener('click', function() {
                        const currentValue = parseInt(input.value);
                        if (currentValue > 1) {
                            input.value = currentValue - 1;
                            // Trigger change event for any listeners
                            input.dispatchEvent(new Event('change'));
                        }
                    });
                }
                
                if (plusBtn) {
                    plusBtn.addEventListener('click', function() {
                        const currentValue = parseInt(input.value);
                        input.value = currentValue + 1;
                        // Trigger change event for any listeners
                        input.dispatchEvent(new Event('change'));
                    });
                }
            });

            // Add to cart animation
            const addToCartButtons = document.querySelectorAll('.add-to-cart');
            addToCartButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    // Prevent default if it's a link
                    if (button.tagName === 'A') {
                        e.preventDefault();
                    }
                    
                    // Visual feedback
                    button.classList.add('added');
                    button.textContent = 'Added to Cart';
                    
                    setTimeout(() => {
                        button.classList.remove('added');
                        button.textContent = 'Add to Cart';
                    }, 2000);
                    
                    // If there's a form to submit
                    const form = button.closest('form');
                    if (form) {
                        form.submit();
                    }
                });
            });

            // Product image gallery
            const productThumbnails = document.querySelectorAll('.product-thumbnail');
            productThumbnails.forEach(thumbnail => {
                thumbnail.addEventListener('click', function() {
                    const mainImage = document.querySelector('.product-main-image');
                    if (mainImage) {
                        const oldSrc = mainImage.src;
                        mainImage.src = this.src;
                        // Swap images for a smooth transition
                        this.src = oldSrc;
                    }
                });
            });

            // Initialize slideshow if present
            initSlideshow();
        });

        // Slideshow functionality
        function initSlideshow() {
            const slideshows = document.querySelectorAll('.slideshow-container');
            
            slideshows.forEach(slideshow => {
                let slideIndex = 0;
                const slides = slideshow.querySelectorAll('.slide');
                const dots = slideshow.querySelectorAll('.dot');
                
                if (slides.length === 0) return;
                
                // Show first slide
                showSlide(slideIndex);
                
                // Next/previous controls
                const prevButton = slideshow.querySelector('.prev');
                const nextButton = slideshow.querySelector('.next');
                
                if (prevButton) {
                    prevButton.addEventListener('click', () => {
                        changeSlide(-1);
                    });
                }
                
                if (nextButton) {
                    nextButton.addEventListener('click', () => {
                        changeSlide(1);
                    });
                }
                
                // Dots controls
                dots.forEach((dot, index) => {
                    dot.addEventListener('click', () => {
                        showSlide(index);
                        slideIndex = index;
                    });
                });
                
                // Auto slide every 5 seconds
                setInterval(() => {
                    changeSlide(1);
                }, 5000);
                
                function changeSlide(n) {
                    slideIndex += n;
                    if (slideIndex >= slides.length) slideIndex = 0;
                    if (slideIndex < 0) slideIndex = slides.length - 1;
                    showSlide(slideIndex);
                }
                
                function showSlide(n) {
                    slides.forEach(slide => {
                        slide.style.display = 'none';
                    });
                    
                    dots.forEach(dot => {
                        dot.classList.remove('active');
                    });
                    
                    slides[n].style.display = 'block';
                    if (dots.length > 0) {
                        dots[n].classList.add('active');
                    }
                }
            });
        }

        // Ajax add to cart functionality (if supported)
        function addToCart(productId, quantity = 1) {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'add_to_cart.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            
            xhr.onload = function() {
                if (this.status === 200) {
                    try {
                        const response = JSON.parse(this.responseText);
                        if (response.success) {
                            // Update cart count
                            const cartCount = document.querySelector('.cart-count');
                            if (cartCount) {
                                cartCount.textContent = response.cartCount;
                                cartCount.style.display = 'flex';
                            }
                            
                            // Show success message
                            showNotification('Product added to cart successfully!', 'success');
                        } else {
                            showNotification(response.message || 'Failed to add product to cart', 'error');
                        }
                    } catch (e) {
                        showNotification('Something went wrong', 'error');
                    }
                }
            };
            
            xhr.send(`product_id=${productId}&quantity=${quantity}`);
        }

        // Notification helper
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            // Show with animation
            setTimeout(() => {
                notification.classList.add('show');
            }, 10);
            
            // Hide after 3 seconds
            setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => {
                    document.body.removeChild(notification);
                }, 300);
            }, 3000);
        }
    </script>
</body>
</html>