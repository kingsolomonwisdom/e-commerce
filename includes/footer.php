    </main>
    <footer>
        <style>
            footer {
                background-color: #222;
                color: #f8f8f8;
                padding: 40px 0 0;
                margin-top: 50px;
            }
            
            .footer-content {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 30px;
                max-width: 1200px;
                margin: 0 auto;
                padding: 0 20px;
            }
            
            .footer-section h3 {
                color: #fff;
                font-size: 18px;
                margin-bottom: 15px;
                position: relative;
                padding-bottom: 10px;
            }
            
            .footer-section h3::after {
                content: '';
                position: absolute;
                left: 0;
                bottom: 0;
                width: 50px;
                height: 2px;
                background-color: #ffcc00;
            }
            
            .footer-section p {
                color: #b3b3b3;
                line-height: 1.6;
                margin-bottom: 15px;
            }
            
            .socialicons1 {
                display: flex;
                gap: 15px;
                margin-top: 20px;
            }
            
            .socialicons1 a {
                display: flex;
                justify-content: center;
                align-items: center;
                width: 36px;
                height: 36px;
                background-color: rgba(255, 255, 255, 0.1);
                border-radius: 50%;
                color: #fff;
                transition: all 0.3s ease;
            }
            
            .socialicons1 a:hover {
                background-color: #ffcc00;
                color: #222;
                transform: translateY(-3px);
            }
            
            .footer-section ul {
                list-style: none;
                padding: 0;
            }
            
            .footer-section ul li {
                margin-bottom: 10px;
            }
            
            .footer-section ul li a {
                color: #b3b3b3;
                transition: all 0.3s ease;
                display: block;
                padding: 5px 0;
            }
            
            .footer-section ul li a:hover {
                color: #ffcc00;
                transform: translateX(5px);
            }
            
            .footer-section.contact i {
                margin-right: 10px;
                color: #ffcc00;
            }
            
            .footer-bottom {
                background-color: #111;
                text-align: center;
                padding: 15px 0;
                margin-top: 40px;
                font-size: 14px;
            }
            
            @media (max-width: 768px) {
                .footer-content {
                    grid-template-columns: 1fr;
                    text-align: center;
                }
                
                .footer-section h3::after {
                    left: 50%;
                    transform: translateX(-50%);
                }
                
                .socialicons1 {
                    justify-content: center;
                }
                
                .footer-section ul li a:hover {
                    transform: none;
                }
            }
        </style>
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
    
    <!-- Notification Container -->
    <div class="notification-container"></div>
    
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
            // Show loading state
            showNotification('Adding to cart...', 'info', '', 2000);
            
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
                        console.error('Error parsing response:', e, this.responseText);
                        showNotification('Something went wrong. Please try again.', 'error');
                    }
                } else {
                    showNotification('Server error. Please try again later.', 'error');
                }
            };
            
            xhr.onerror = function() {
                showNotification('Network error. Please check your connection.', 'error');
            };
            
            xhr.send(`product_id=${productId}&quantity=${quantity}`);
        }

        // Enhanced Notification System
        function showNotification(message, type = 'info', title = '', duration = 5000) {
            const container = document.querySelector('.notification-container');
            
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            
            // Set icon based on type
            let icon = '';
            switch(type) {
                case 'success':
                    icon = 'check-circle';
                    if (!title) title = 'Success';
                    break;
                case 'error':
                    icon = 'exclamation-circle';
                    if (!title) title = 'Error';
                    break;
                case 'warning':
                    icon = 'exclamation-triangle';
                    if (!title) title = 'Warning';
                    break;
                default:
                    icon = 'info-circle';
                    if (!title) title = 'Information';
                    break;
            }
            
            // Build notification HTML
            notification.innerHTML = `
                <i class="icon fas fa-${icon}"></i>
                <div class="content">
                    <div class="title">${title}</div>
                    <div class="message">${message}</div>
                </div>
                <button class="close"><i class="fas fa-times"></i></button>
            `;
            
            // Add to container
            container.appendChild(notification);
            
            // Add close button event
            const closeBtn = notification.querySelector('.close');
            closeBtn.addEventListener('click', () => {
                notification.classList.remove('show');
                setTimeout(() => {
                    container.removeChild(notification);
                }, 300);
            });
            
            // Show with animation
            setTimeout(() => {
                notification.classList.add('show');
            }, 10);
            
            // Auto remove after duration
            if (duration > 0) {
                setTimeout(() => {
                    if (notification.parentNode === container) {
                        notification.classList.remove('show');
                        setTimeout(() => {
                            if (notification.parentNode === container) {
                                container.removeChild(notification);
                            }
                        }, 300);
                    }
                }, duration);
            }
            
            return notification;
        }
        
        // Process session messages if they exist
        document.addEventListener('DOMContentLoaded', function() {
            // Check for PHP set session messages and convert them to notifications
            <?php if (isset($_SESSION['success_message'])): ?>
                showNotification('<?php echo addslashes($_SESSION['success_message']); ?>', 'success');
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error_message'])): ?>
                showNotification('<?php echo addslashes($_SESSION['error_message']); ?>', 'error');
                <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['info_message'])): ?>
                showNotification('<?php echo addslashes($_SESSION['info_message']); ?>', 'info');
                <?php unset($_SESSION['info_message']); ?>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['warning_message'])): ?>
                showNotification('<?php echo addslashes($_SESSION['warning_message']); ?>', 'warning');
                <?php unset($_SESSION['warning_message']); ?>
            <?php endif; ?>
        });
    </script>
</body>
</html>