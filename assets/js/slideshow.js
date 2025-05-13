document.addEventListener('DOMContentLoaded', function() {
    // Slideshow functionality
    const slideshowContainer = document.querySelector('.slideshow-container');
    if (!slideshowContainer) return;
    
    const slides = document.querySelectorAll('.slide');
    const dots = document.querySelectorAll('.dot');
    const prevBtn = document.querySelector('.prev');
    const nextBtn = document.querySelector('.next');
    
    let slideIndex = 0;
    
    // Initialize the slideshow
    showSlide(slideIndex);
    
    // Next/previous controls
    if (prevBtn) {
        prevBtn.addEventListener('click', function() {
            changeSlide(-1);
        });
    }
    
    if (nextBtn) {
        nextBtn.addEventListener('click', function() {
            changeSlide(1);
        });
    }
    
    // Dots controls
    dots.forEach((dot, index) => {
        dot.addEventListener('click', function() {
            slideIndex = index;
            showSlide(slideIndex);
        });
    });
    
    // Automatic slideshow
    setInterval(function() {
        changeSlide(1);
    }, 5000);
    
    function changeSlide(n) {
        slideIndex += n;
        
        // Wrap around if at the beginning/end
        if (slideIndex >= slides.length) {
            slideIndex = 0;
        } else if (slideIndex < 0) {
            slideIndex = slides.length - 1;
        }
        
        showSlide(slideIndex);
    }
    
    function showSlide(n) {
        // Hide all slides
        slides.forEach(slide => {
            slide.style.display = 'none';
        });
        
        // Remove active class from all dots
        dots.forEach(dot => {
            dot.classList.remove('active');
        });
        
        // Show the current slide and activate the corresponding dot
        slides[n].style.display = 'block';
        dots[n].classList.add('active');
    }
}); 