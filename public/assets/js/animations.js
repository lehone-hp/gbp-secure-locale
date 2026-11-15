/**
 * Scroll-triggered animations using Animate.css
 */

// Deepen navbar shadow on scroll
window.addEventListener('scroll', function () {
    var navbar = document.querySelector('.navbar');
    if (navbar) {
        navbar.classList.toggle('scrolled', window.scrollY > 10);
    }
});

// Hide preloader once all resources (scripts, images, fonts) have loaded
window.addEventListener('load', function () {
    var preloader = document.querySelector('.preloader');
    if (preloader) {
        preloader.classList.add('fade-out');
        setTimeout(function () { preloader.remove(); }, 500);
    }
});

// Floating bubbles background
(function () {
    var canvas = document.createElement('canvas');
    canvas.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;pointer-events:none;z-index:1000;';
    document.body.insertBefore(canvas, document.body.firstChild);

    var ctx = canvas.getContext('2d');
    var bubbles = [];

    var COLORS = [
        'rgba(98,84,231,',    // primary purple
        'rgba(98,84,231,',    // primary purple (weighted)
        'rgba(244,117,20,',   // orange accent
        'rgba(183,229,100,',  // soft green
        'rgba(255,223,114,',  // soft yellow
    ];

    function resize() {
        canvas.width  = window.innerWidth;
        canvas.height = window.innerHeight;
    }

    function randomBetween(a, b) {
        return a + Math.random() * (b - a);
        return a + Math.random() * (b - a);
    }

    function createBubble(startAtBottom) {
        var color = COLORS[Math.floor(Math.random() * COLORS.length)];
        return {
            x:       randomBetween(0, canvas.width),
            y:       startAtBottom ? canvas.height + randomBetween(0, canvas.height) : randomBetween(0, canvas.height),
            r:       randomBetween(2, 5),
            speed:   randomBetween(0.3, 0.8),
            drift:   randomBetween(-0.25, 0.25),
            opacity: randomBetween(0.05, 0.2),
            color:   color,
        };
    }

    function init() {
        bubbles = [];
        for (var i = 0; i < 18; i++) {
            bubbles.push(createBubble(false));
        }
    }

    function draw() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        for (var i = 0; i < bubbles.length; i++) {
            var b = bubbles[i];
            ctx.beginPath();
            ctx.arc(b.x, b.y, b.r, 0, Math.PI * 2);
            ctx.fillStyle = b.color + b.opacity + ')';
            ctx.fill();

            b.y     -= b.speed;
            b.x     += b.drift;

            if (b.y + b.r < 0) {
                bubbles[i] = createBubble(true);
                bubbles[i].y = canvas.height + b.r;
            }
        }
        requestAnimationFrame(draw);
    }

    window.addEventListener('resize', function () { resize(); init(); });
    resize();
    init();
    draw();
})();

(function($) {
    'use strict';

    // Animation Observer
    const animateOnScroll = () => {
        const animatedElements = document.querySelectorAll('.animate-on-scroll');

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const element = entry.target;
                    const animationClass = element.dataset.animation || 'fadeInUp';
                    const delay = element.dataset.delay || 0;

                    setTimeout(() => {
                        element.classList.add('animate__animated', `animate__${animationClass}`);
                        element.style.opacity = '1';
                    }, delay);

                    // Unobserve after animation
                    observer.unobserve(element);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });

        animatedElements.forEach(el => {
            el.style.opacity = '0';
            observer.observe(el);
        });
    };

    // Stagger animations for lists
    const staggerAnimations = () => {
        const staggerGroups = document.querySelectorAll('.stagger-animation');

        staggerGroups.forEach(group => {
            const children = group.querySelectorAll('.stagger-item');
            const baseDelay = parseInt(group.dataset.staggerDelay) || 100;

            children.forEach((child, index) => {
                child.dataset.delay = index * baseDelay;
                child.classList.add('animate-on-scroll');
            });
        });
    };

    // Initialize on page load
    $(document).ready(function() {
        staggerAnimations();
        animateOnScroll();
    });

    // Re-initialize on dynamic content load
    window.initAnimations = function() {
        staggerAnimations();
        animateOnScroll();
    };

})(jQuery);
