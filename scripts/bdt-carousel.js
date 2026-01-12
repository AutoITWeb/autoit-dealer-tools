/**
 * BDT Featured Vehicles Carousel
 * Responsive carousel with touch support and infinite forward loop
 * @param {string} carouselId - The ID of the carousel element
 * @param {number} autoplayInterval - Autoplay interval in seconds (0 = disabled)
 */

function initBDTCarousel(carouselId, autoplayInterval = 0) {
    const carousel = document.getElementById(carouselId);
    if (!carousel) return;

    const track = carousel.querySelector('.bdt-carousel-track');
    const originalSlides = Array.from(track.children);
    const prevBtn = carousel.querySelector('.bdt-carousel-prev');
    const nextBtn = carousel.querySelector('.bdt-carousel-next');
    const dotsContainer = carousel.querySelector('.bdt-carousel-dots');
    const showNav = carousel.getAttribute('data-show-nav') === 'true';

    if (originalSlides.length === 0) return;
    if (originalSlides.length === 1) {
        if (prevBtn) prevBtn.style.display = 'none';
        if (nextBtn) nextBtn.style.display = 'none';
        if (dotsContainer) dotsContainer.style.display = 'none';
        return;
    }

    // Clone slides for infinite loop. Cap clone count so we never index past
    // originalSlides when the filtered result has fewer items than slidesToShow.
    let slidesToShow = getSlidesToShow();
    let clonedBeforeCount = Math.min(slidesToShow, originalSlides.length);
    let clonedAfterCount = Math.min(slidesToShow, originalSlides.length);

    // Clone slides and append/prepend for seamless loop
    for (let i = 0; i < clonedAfterCount; i++) {
        const clone = originalSlides[i].cloneNode(true);
        clone.classList.add('bdt-carousel-clone');
        track.appendChild(clone);
    }

    for (let i = originalSlides.length - clonedBeforeCount; i < originalSlides.length; i++) {
        const clone = originalSlides[i].cloneNode(true);
        clone.classList.add('bdt-carousel-clone');
        track.insertBefore(clone, track.firstChild);
    }

    let slides = Array.from(track.children);
    let currentIndex = clonedBeforeCount; // Start at first real slide
    let isDragging = false;
    let startPos = 0;
    let currentTranslate = 0;
    let prevTranslate = 0;
    let animationID = 0;
    let autoplayTimer = null;
    let isTransitioning = false;

    // Calculate how many slides to show based on screen width
    function getSlidesToShow() {
        const width = window.innerWidth;
        if (width >= 992) return 3;
        if (width >= 768) return 2;
        return 1;
    }

    // Get the actual dot index (excluding clones)
    function getRealSlideIndex() {
        const realIndex = currentIndex - clonedBeforeCount;
        if (realIndex < 0) {
            return originalSlides.length + realIndex;
        } else if (realIndex >= originalSlides.length) {
            return realIndex - originalSlides.length;
        }
        return realIndex;
    }

    // Create dot indicators (one per original slide)
    function createDots() {
        if (!dotsContainer) return;

        dotsContainer.innerHTML = '';

        // Create dots for each original slide
        for (let i = 0; i < originalSlides.length; i++) {
            const dot = document.createElement('button');
            dot.classList.add('bdt-carousel-dot');
            dot.setAttribute('aria-label', 'Go to slide ' + (i + 1));
            if (i === 0) dot.classList.add('active');

            dot.addEventListener('click', () => {
                currentIndex = clonedBeforeCount + i;
                updateCarousel(true);
                resetAutoplay();
            });

            dotsContainer.appendChild(dot);
        }
    }

    // Update carousel position
    function updateCarousel(animate = true) {
        if (isTransitioning) return;

        // Calculate slide width including gap (scroll ONE slide at a time)
        const slideWidth = slides[0].offsetWidth;
        const gap = 20;
        const moveAmount = (slideWidth + gap) * currentIndex;

        // Apply transform
        if (animate) {
            track.style.transition = 'transform 0.5s ease-in-out';
            isTransitioning = true;
        } else {
            track.style.transition = 'none';
        }

        track.style.transform = `translateX(-${moveAmount}px)`;

        // Update dots based on real slide index (only if dots exist)
        if (dotsContainer) {
            const dots = dotsContainer.querySelectorAll('.bdt-carousel-dot');
            const realIndex = getRealSlideIndex();
            dots.forEach((dot, index) => {
                dot.classList.toggle('active', index === realIndex);
            });
        }

        // Handle loop: check if we need to jump to real slide after animation
        if (animate) {
            setTimeout(() => {
                handleInfiniteLoop();
                isTransitioning = false;
            }, 500); // Match transition duration
        }
    }

    // Handle infinite loop by jumping to real slides when on clones
    function handleInfiniteLoop() {
        const slideWidth = slides[0].offsetWidth;
        const gap = 20;

        // If we're past the last real slide (in the cloned beginning slides)
        if (currentIndex >= clonedBeforeCount + originalSlides.length) {
            currentIndex = clonedBeforeCount + (currentIndex - clonedBeforeCount - originalSlides.length);
            track.style.transition = 'none';
            track.style.transform = `translateX(-${(slideWidth + gap) * currentIndex}px)`;
        }
        // If we're before the first real slide (in the cloned end slides)
        else if (currentIndex < clonedBeforeCount) {
            currentIndex = clonedBeforeCount + originalSlides.length - (clonedBeforeCount - currentIndex);
            track.style.transition = 'none';
            track.style.transform = `translateX(-${(slideWidth + gap) * currentIndex}px)`;
        }

        // Update dots after potential jump (only if dots exist)
        if (dotsContainer) {
            const dots = dotsContainer.querySelectorAll('.bdt-carousel-dot');
            const realIndex = getRealSlideIndex();
            dots.forEach((dot, index) => {
                dot.classList.toggle('active', index === realIndex);
            });
        }
    }

    // Previous slide
    function prevSlide() {
        if (isTransitioning) return;
        currentIndex--;
        updateCarousel(true);
    }

    // Next slide
    function nextSlide() {
        if (isTransitioning) return;
        currentIndex++;
        updateCarousel(true);
    }

    // Touch/Mouse events for swipe
    function touchStart(event) {
        isDragging = true;
        startPos = getPositionX(event);
        animationID = requestAnimationFrame(animation);
        track.style.cursor = 'grabbing';
    }

    function touchMove(event) {
        if (isDragging) {
            const currentPosition = getPositionX(event);
            currentTranslate = prevTranslate + currentPosition - startPos;
        }
    }

    function touchEnd() {
        isDragging = false;
        cancelAnimationFrame(animationID);
        track.style.cursor = 'grab';

        const movedBy = currentTranslate - prevTranslate;

        // Determine if swipe was significant enough
        if (movedBy < -50) {
            // Swipe left - go to next
            currentIndex++;
        } else if (movedBy > 50) {
            // Swipe right - go to previous
            currentIndex--;
        }

        currentTranslate = 0;
        prevTranslate = 0;
        updateCarousel(true);
    }

    function getPositionX(event) {
        return event.type.includes('mouse') ? event.pageX : event.touches[0].clientX;
    }

    function animation() {
        if (isDragging) {
            const slideWidth = slides[0].offsetWidth;
            const gap = 20;
            const moveAmount = (slideWidth + gap) * currentIndex; // Move by single slide
            const baseTranslate = -moveAmount;

            track.style.transform = `translateX(${baseTranslate + currentTranslate}px)`;
            requestAnimationFrame(animation);
        }
    }

    // Keyboard navigation
    function handleKeyboard(event) {
        if (event.key === 'ArrowLeft') {
            prevSlide();
        } else if (event.key === 'ArrowRight') {
            nextSlide();
        }
    }

    // Handle window resize
    let resizeTimeout;
    function handleResize() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(() => {
            const newSlidesToShow = getSlidesToShow();
            if (newSlidesToShow !== slidesToShow) {
                slidesToShow = newSlidesToShow;

                // Rebuild clones with new slidesToShow count
                // Remove old clones
                const clones = track.querySelectorAll('.bdt-carousel-clone');
                clones.forEach(clone => clone.remove());

                // Update clone counts (capped so we never index past originalSlides).
                clonedBeforeCount = Math.min(newSlidesToShow, originalSlides.length);
                clonedAfterCount = Math.min(newSlidesToShow, originalSlides.length);

                // Re-clone slides (use originalSlides, not current children)
                for (let i = 0; i < clonedAfterCount; i++) {
                    const clone = originalSlides[i].cloneNode(true);
                    clone.classList.add('bdt-carousel-clone');
                    track.appendChild(clone);
                }

                for (let i = originalSlides.length - clonedBeforeCount; i < originalSlides.length; i++) {
                    const clone = originalSlides[i].cloneNode(true);
                    clone.classList.add('bdt-carousel-clone');
                    track.insertBefore(clone, track.firstChild);
                }

                // Update slides reference
                slides = Array.from(track.children);

                // Reset to first real slide
                currentIndex = clonedBeforeCount;
                createDots();
                updateCarousel(false);

                // Force position
                const slideWidth = slides[0].offsetWidth;
                const gap = 20;
                track.style.transition = 'none';
                track.style.transform = `translateX(-${(slideWidth + gap) * currentIndex}px)`;
            } else {
                updateCarousel(false);
            }
        }, 250);
    }

    // Autoplay helper functions
    function startAutoplay() {
        if (autoplayInterval > 0 && !autoplayTimer) {
            autoplayTimer = setInterval(() => {
                nextSlide(); // Uses the nextSlide function which has forward loop
            }, autoplayInterval * 1000);
        }
    }

    function stopAutoplay() {
        if (autoplayTimer) {
            clearInterval(autoplayTimer);
            autoplayTimer = null;
        }
    }

    function resetAutoplay() {
        if (autoplayInterval > 0) {
            stopAutoplay();
            startAutoplay();
        }
    }

    // Initialize - position at first real slide
    createDots();
    updateCarousel(false);

    // Force initial position without animation
    const slideWidth = slides[0].offsetWidth;
    const gap = 20;
    track.style.transition = 'none';
    track.style.transform = `translateX(-${(slideWidth + gap) * currentIndex}px)`;

    // Event listeners for navigation buttons (only if they exist)
    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            prevSlide();
            resetAutoplay();
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            nextSlide();
            resetAutoplay();
        });
    }

    carousel.addEventListener('keydown', (event) => {
        handleKeyboard(event);
        resetAutoplay();
    });

    window.addEventListener('resize', handleResize);

    // Touch events
    track.addEventListener('touchstart', (e) => {
        touchStart(e);
        stopAutoplay();
    });
    track.addEventListener('touchmove', touchMove);
    track.addEventListener('touchend', (e) => {
        touchEnd(e);
        resetAutoplay();
    });

    // Mouse events (for desktop drag)
    track.addEventListener('mousedown', (e) => {
        touchStart(e);
        stopAutoplay();
    });
    track.addEventListener('mousemove', touchMove);
    track.addEventListener('mouseup', (e) => {
        touchEnd(e);
        resetAutoplay();
    });
    track.addEventListener('mouseleave', () => {
        if (isDragging) {
            touchEnd();
            resetAutoplay();
        }
    });

    // Prevent context menu on long press
    track.addEventListener('contextmenu', (e) => {
        if (isDragging) e.preventDefault();
    });

    // Set initial cursor
    track.style.cursor = 'grab';

    // Start autoplay if enabled
    if (autoplayInterval > 0) {
        startAutoplay();

        // Pause autoplay on hover over carousel
        carousel.addEventListener('mouseenter', stopAutoplay);
        carousel.addEventListener('mouseleave', startAutoplay);
    }
}
