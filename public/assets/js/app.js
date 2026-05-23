/* 
    Animations and UI Interactions 
*/

document.addEventListener('DOMContentLoaded', () => {

    // 1. Mobile Navigation Toggle
    const mobileToggle = document.querySelector('.mobile-toggle');
    const navLinks = document.querySelector('.nav-links');

    if (mobileToggle && navLinks) {
        mobileToggle.addEventListener('click', () => {
            navLinks.classList.toggle('nav-active');
            mobileToggle.classList.toggle('toggle-active');

            // Prevent scrolling on body when menu is open
            if (navLinks.classList.contains('nav-active')) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = 'auto';
            }
        });
    }

    // 2. Header Scroll Effect
    const header = document.querySelector('.header');

    if (header) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });
    }

    // 3. Cart count is rendered by PHP from the session cart.
    function updateCartCountUI() {
        const cartCountBadge = document.querySelector('.cart-count');
        if (cartCountBadge && Number(cartCountBadge.textContent) > 0) {
            cartCountBadge.style.display = 'flex';
        }
    }

    // Initialize cart count on page load
    updateCartCountUI();

    // Export function to global scope so other scripts can call it
    window.updateGlobalCartCount = updateCartCountUI;

    // 4. Smooth Scrolling for anchor links (if any)
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;

            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                e.preventDefault();
                targetElement.scrollIntoView({
                    behavior: 'smooth'
                });
                // Close mobile menu if open
                if (navLinks && navLinks.classList.contains('nav-active')) {
                    navLinks.classList.remove('nav-active');
                    mobileToggle.classList.remove('toggle-active');
                    document.body.style.overflow = 'auto';
                }
            }
        });
    });
    /* 
       Hero Image Slider Controller  
       */
    function setupImageSlider() {
        const sliderItems = document.getElementsByClassName('slide');
        if (sliderItems.length === 0) return;

        const paginationDots = document.querySelectorAll('.dot');
        const btnNext = document.querySelector('.next-slide');
        const btnPrev = document.querySelector('.prev-slide');

        let activeIndex = 0;
        let autoPlayTimer;

        // Function to switch to a specific slide smoothly
        function showSlide(newIndex) {
            // Hide current slide
            sliderItems[activeIndex].classList.remove('active');
            paginationDots[activeIndex].classList.remove('active');

            // Calculate new index with wrap-around logic
            if (newIndex >= sliderItems.length) {
                activeIndex = 0;
            } else if (newIndex < 0) {
                activeIndex = sliderItems.length - 1;
            } else {
                activeIndex = newIndex;
            }

            // Show new slide
            sliderItems[activeIndex].classList.add('active');
            paginationDots[activeIndex].classList.add('active');
        }

        // Navigation helpers
        const moveForward = () => showSlide(activeIndex + 1);
        const moveBackward = () => showSlide(activeIndex - 1);

        // Click events for arrows
        btnNext.addEventListener('click', function () {
            moveForward();
            restartTimer();
        });

        btnPrev.addEventListener('click', function () {
            moveBackward();
            restartTimer();
        });

        // Click events for pagination dots
        for (let i = 0; i < paginationDots.length; i++) {
            paginationDots[i].addEventListener('click', function () {
                showSlide(i);
                restartTimer();
            });
        }





        // SlideShow time control
        function beginAutoPlay() {
            autoPlayTimer = setInterval(moveForward, 3000);
        }

        // Reset timer when user interacts manually
        function restartTimer() {
            clearInterval(autoPlayTimer);
            beginAutoPlay();
        }

        // Stop slider when hovering over it
        const sliderWrapper = document.querySelector('.hero-slideshow');
        sliderWrapper.addEventListener('mouseover', function () {
            clearInterval(autoPlayTimer);
        });

        // Resume when mouse leaves
        sliderWrapper.addEventListener('mouseout', beginAutoPlay);

        // Initialize the auto-playback sequence
        beginAutoPlay();
    }

    // Initialize slider
    setupImageSlider();
});
