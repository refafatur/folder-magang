    document.addEventListener("DOMContentLoaded", function() {
        let carousel = new bootstrap.Carousel(document.getElementById("testimoniCarousel"), {
            interval: false, // Nonaktifkan auto-slide dari Bootstrap
            wrap: true
        });

        setInterval(() => {
            carousel.next();
        }, 3000);
    });
