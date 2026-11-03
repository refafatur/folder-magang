<?php 
require_once 'config/database.php';

// Get current date for comparison
$currentDate = date('Y-m-d');

// Query for promotions
$queryPromotions = "SELECT * FROM tbl_promotions ORDER BY id DESC";
$resultPromotions = $conn->query($queryPromotions);
$promotions = $resultPromotions;

// Query untuk events
$queryEvents = "SELECT * FROM tbl_events ORDER BY event_date DESC";
$result = $conn->query($queryEvents);
$events = $result;

include 'includes/header.php'; 
?>

<header class="hero" style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('assets/photos/course.avif'); background-position: center; background-size: cover; height: 100vh;padding: 0; display: flex; align-items: center; justify-content: center;">
    <div class="hero-content" data-aos="fade-up" data-aos-duration="1000">
        <span class="section-subtitle" style="font-size: 2em; margin-bottom: 100px; margin: 0 auto; color: white;">Events & Promotions</span>
        <p>Join our exclusive golf events and enjoy special offers</p>
    </div>
</header>

<!-- Promotions Section -->
<section class="events-section">
    <div class="container">
        <span class="section-subtitle" data-aos="fade-up">WHAT'S ON</span>
        <h2 style="color: black;" data-aos="fade-up" data-aos-delay="100">UPCOMING PROMOTIONS</h2>
        
        <div style="position: relative;">
            <button class="scroll-btn left" onclick="scrollGallery('left')" style="position: absolute; left: 0; top: 50%; transform: translateY(-50%); z-index: 2;">
                <i class="fas fa-chevron-left"></i>
            </button>
            
            <div class="events-grid" id="eventsGrid" style="display: flex; overflow-x: auto; gap: 20px; padding: 20px 0; scrollbar-width: none; -ms-overflow-style: none;">
                <?php 
                if($promotions->num_rows > 0):
                    while($promo = $promotions->fetch_assoc()):
                ?>
                    <div class="event-card" data-aos="fade-up" style="min-width: 300px;">
                        <div class="event-image" style="width: 300px; height: 300px; overflow: hidden; position: relative; cursor: pointer;" 
                             onclick="openLightbox('../<?php echo $promo['image']; ?>')">
                            <img src="../<?php echo $promo['image']; ?>" 
                                alt="<?php echo htmlspecialchars($promo['name']); ?>"
                                style="width: 100%; height: 100%; object-fit: cover; object-position: center;">
                        </div>
                    </div>
                <?php 
                    endwhile;
                else:
                ?>
                    <p>No promotions available at the moment.</p>
                <?php endif; ?>
            </div>

            <button class="scroll-btn right" onclick="scrollGallery('right')" style="position: absolute; right: 0; top: 50%; transform: translateY(-50%); z-index: 2;">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>
</section>

<style>
.scroll-btn {
    background: goldenrod;
    border: none;
    color: white;
    padding: 10px;
    cursor: pointer;
    border-radius: 50%;
    width: 40px;
    height: 40px;
}
.scroll-btn:hover {
    background: goldenrod;
}
</style>

<script>
function scrollGallery(direction) {
    const container = document.getElementById('eventsGrid');
    const scrollAmount = 300;
    if (direction === 'left') {
        container.scrollBy({
            left: -scrollAmount,
            behavior: 'smooth'
        });
    } else {
        container.scrollBy({
            left: scrollAmount,
            behavior: 'smooth'
        });
    }
}
</script>

<!-- Lightbox Modal -->
<div id="imageLightbox" class="lightbox" onclick="closeLightbox()">
    <span class="close-lightbox">&times;</span>
    <img id="lightboxImage" class="lightbox-content">
</div>

<!-- Add JavaScript for Lightbox -->
<script>
function openLightbox(imageSrc) {
    document.getElementById('imageLightbox').style.display = 'flex';
    document.getElementById('lightboxImage').src = imageSrc;
}

function closeLightbox() {
    document.getElementById('imageLightbox').style.display = 'none';
}

// Close lightbox when pressing Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeLightbox();
    }
});

// Prevent closing when clicking on the image
document.getElementById('lightboxImage').addEventListener('click', function(event) {
    event.stopPropagation();
});
</script>

<section class="events-intro">
    <div class="container" data-aos="fade-up">
        <h2>Events at Klub Golf Bogor Raya</h2>
        <p>From unforgettable celebrations to the Indonesian biggest golf tournaments including the 2019 Festival of Golf is home to extraordinary events of all shapes and sizes. Because when it comes to Klub Golf Bogor Raya, the greatest meeting of land and the greatest gathering place on Bogor.</p>
    </div>
</section>
<!-- Events Section -->
<section class="promotions-section">
    <div class="container">
        <span class="section-subtitle" data-aos="fade-up">SPECIAL OFFERS</span>
        <h2 style="color:black;" data-aos="fade-up" data-aos-delay="100">CURRENT EVENTS</h2>
        
        <div class="promotions-grid" style="display: flex; flex-wrap: wrap; gap: 20px; justify-content: center;">
            <?php 
            if($events->num_rows > 0):
            $count = 0;
            while($event = $events->fetch_assoc()):
                $visibilityClass = $count < 3 ? 'initial-visible' : 'hidden-promo';
            ?>
            <div class="promo-card <?php echo $visibilityClass; ?>" 
                 data-aos="fade-up" 
                 data-aos-delay="<?php echo $count * 100; ?>" 
                 style="width: 300px; margin-bottom: 20px;">
                <div class="promo-image" style="width: 300px; height: 300px; overflow: hidden; position: relative;">
                    <img src="../<?php echo $event['image']; ?>" 
                        alt="<?php echo htmlspecialchars($event['name']); ?>"
                        style="width: 100%; height: 100%; object-fit: cover; object-position: center;">
                </div>
                <div class="promo-content" style="padding: 20px; display: flex; flex-direction: column; height: 100%;">
                    <h3 style="margin-top: 0; font-size: 1.1em; word-break: break-word; line-height: 1.2; max-height: 2.6em; overflow: hidden;"><?php echo htmlspecialchars($event['name']); ?></h3>
                    <div class="promo-meta" style="font-size: 0.95em; margin-bottom: 8px;">
                        <p class="promo-period" style="margin: 0;">
                            <i class="far fa-calendar-alt"></i> 
                            <?php echo date('F d, Y', strtotime($event['event_date'])); ?>
                        </p>
                    </div>
                    <?php
                    $desc = strip_tags($event['description']);
                    $words = explode(' ', $desc);
                    if (count($words) > 22) {
                        $desc = implode(' ', array_slice($words, 0, 22)) . '...';
                    }
                    ?>
                    <p class="promo-description" style="font-size: 0.98em; line-height: 1.4; margin-bottom: 16px; word-break: break-word; max-height: 4.2em; overflow: hidden;"><?php echo htmlspecialchars($desc); ?></p>
                    <a href="event-detail.php?id=<?php echo $event['id']; ?>" class="btn-register" style="text-decoration: none; display: block; text-align: center; margin-top: 12px;">Learn More</a>
                </div>
                <style>
                @media (max-width: 600px) {
                    .promo-card {
                        width: 95vw !important;
                        min-width: 0 !important;
                    }
                    .promo-image {
                        width: 100% !important;
                        height: 180px !important;
                    }
                    .promo-content {
                        padding: 12px !important;
                    }
                    .promo-description {
                        font-size: 0.95em !important;
                        max-height: 3.2em !important;
                    }
                    .promo-card h3 {
                        font-size: 1em !important;
                        max-height: 2em !important;
                    }
                }
                </style>
            </div>
            <?php 
            $count++;
            endwhile;
            else:
            ?>
            <p>No upcoming events at the moment.</p>
            <?php endif; ?>
        </div>
        <?php if ($count > 3): ?>
        <div class="view-all-container" data-aos="fade-up" data-aos-delay="400">
            <button id="viewAllPromos" class="btn-view-all">
                <span class="show-text">View All Events</span>
                <span class="hide-text">Show Less</span>
                <i class="fas fa-chevron-down"></i>
            </button>
        </div>
        <?php endif; ?>
    </div>
</section>

<link rel="stylesheet" href="assets/css/style.css">

<?php include 'includes/footer.php'; ?>