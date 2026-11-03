<?php 
require_once 'config/database.php';

// Fetch courses data
$sql = "SELECT * FROM tbl_courses ORDER BY id ASC";
$result = $conn->query($sql);
$courses = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $courses[] = $row;
    }
}

include 'includes/header.php'; ?>

<header class="hero" style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),url('assets/photos/course.avif'); background-position: center; background-size: cover; height: 100vh;padding: 0; display: flex; align-items: center; justify-content: center;">
    <div class="hero-content" data-aos="fade-up" data-aos-duration="1000">
            <img src="assets/photos/digest.avif" alt="Home For Golfers Logo" width="auto" height="110">
        </div>
</header>

<main class="course-container">
    <section class="course-intro">
        <div class="container" data-aos="fade-up">
            <h2 style="color: black;">Golf Course Public in Bogor West Java Indonesian</h2>
            <p class="intro-text">Welcome to Klub Golf Bogor Raya an 18 holes best golf course public in Bogor, West Java, Indonesia (golfdigest), designed by renowned international golf course architect, Graham V. Marsh, is a 72-hectares masterpiece of stunning natural beauty. In his design, Marsh known for producing some of the region’s finest golf facilities, which emphasize upon the site’s abundance of mature vegetation and natural hazards to create and refreshing golfing, established 1997.</p>
        </div>
    </section>

    <section class="course-highlights">
        <div class="container">
            <h2 data-aos="fade-up">Course Overview</h2>
            <div class="course-cards">
                <?php foreach ($courses as $key => $course): ?>
                    <div class="course-card" data-course-id="<?php echo $course['id']; ?>" 
                         data-aos="fade-up" 
                         data-aos-delay="<?php echo $key * 100; ?>">
                        <img src="../<?php echo $course['view_image']; ?>" alt="<?php echo $course['name']; ?>">
                        <div class="card-content">
                            <div class="hole-header">
                                <div class="hole-number">
                                    <span class="number"><?php 
                                        $num = $key + 1;
                                        $suffix = match ($num % 10) {
                                            1 => $num === 11 ? 'th' : 'st',
                                            2 => $num === 12 ? 'th' : 'nd',
                                            3 => $num === 13 ? 'th' : 'rd',
                                            default => 'th'
                                        };
                                        echo $num . $suffix;
                                    ?> <span class="tree-type"><?php echo $course['name']; ?></span>
                                    </span>
                                </div>
                                <div class="hole-par">Par <?php echo $course['par']; ?></div>
                            </div>
                            <p class="hole-description"><?php echo $course['description']; ?></p>
                            <div class="hole-actions">
                                <a href="#" class="action-btn map-btn">VIEW</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</main>

<!-- Map Modal -->
<div class="modal" id="mapModal">
    <button class="modal-close">&times;</button>
    <div class="modal-content slide-modal">
        <h2 class="hole-title">Hole Details</h2>
        <div class="hole-map">
            <img src="" alt="Hole Map" style="width: 100%; height: 100%; object-fit: cover;">
        </div>
        <div class="map-details">
            <div class="map-stat">
                <div class="label">Length</div>
                <div class="value length-value">--</div>
            </div>
            <div class="map-stat">
                <div class="label">Par</div>
                <div class="value par-value">--</div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="assets/css/course.css">
<link rel="stylesheet" href="assets/css/course-modals.css">

<script>
document.addEventListener('DOMContentLoaded', function() {
    const mapModal = document.getElementById('mapModal');
    const modals = document.querySelectorAll('.modal');
    const courseCards = document.querySelectorAll('.course-card');

    // Course data from PHP
    const courseData = <?php echo json_encode(array_map(function($course) {
        $course['view_image'] = '../' . $course['view_image'];
        $course['field_image'] = '../' . $course['field_image'];
        return $course;
    }, $courses)); ?>;

    // Update map links
    courseCards.forEach(card => {
        const courseId = card.dataset.courseId;
        const mapBtn = card.querySelector('.map-btn');
        const courseInfo = courseData.find(c => c.id === courseId);
        
        if (courseInfo) {
            mapBtn.addEventListener('click', (e) => {
                e.preventDefault();
                updateMapModal(courseInfo);
                openModal(mapModal);
            });
        }
    });

    function updateMapModal(data) {
        const modal = mapModal;
        modal.querySelector('.hole-title').textContent = data.name;
        modal.querySelector('.hole-map img').src = data.field_image;
        modal.querySelector('.length-value').textContent = data.length + ' yards';
        modal.querySelector('.par-value').textContent = data.par;
    }

    function openModal(modal) {
        modal.style.display = 'flex';
        // Force reflow
        modal.offsetHeight;
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeModal(modal) {
        modal.classList.remove('active');
        modal.addEventListener('transitionend', function handler(e) {
            // Only handle the transform transition
            if (e.propertyName === 'transform') {
                modal.style.display = 'none';
                modal.removeEventListener('transitionend', handler);
                document.body.style.overflow = '';
            }
        });
    }

    // Close button functionality
    document.querySelectorAll('.modal-close').forEach(button => {
        button.addEventListener('click', () => {
            closeModal(button.closest('.modal'));
        });
    });

    // Close on outside click
    modals.forEach(modal => {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                closeModal(modal);
            }
        });
    });

    // Close on ESC key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            modals.forEach(modal => {
                if (modal.classList.contains('active')) {
                    closeModal(modal);
                }
            });
        }
    });
});
</script>

<style>
.course-card {
    aspect-ratio: 1;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.course-card img {
    height: 40%;
    object-fit: cover;
}

.card-content {
    height: 60%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 15px;
}

.hole-description {
    flex: 1;
    overflow-y: auto;
    margin: 10px 0;
    font-size: 0.9em;
    line-height: 1.4;
}

.hole-header {
    margin-bottom: 5px;
}

.hole-actions {
    margin-top: auto;
    padding-top: 10px;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 10px;
}

.action-btn {
    text-decoration: none;
    color: #333;
    font-weight: bold;
}

.divider {
    color: #666;
}

.modal {
    display: none;
    opacity: 0;
    transition: opacity 0.3s ease-in-out;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    justify-content: center;
    align-items: center;
}

.modal-content {
    position: relative;
    transform: translateY(-100%);
    transition: transform 0.3s ease-in-out;
    background: white;
    width: auto;
    max-width: 90vw;
}

.modal.active .modal-content {
    transform: translateY(0);
}

#reviewModal .modal-content {
    padding: 20px;
    background: rgba(255, 255, 255, 0.95);
    width: auto;
}

.course-preview {
    height: auto;
    max-height: 90vh;
}

.course-preview-image {
    max-width: 90vw;
    max-height: 90vh;
    width: auto;
    height: auto;
    object-fit: contain;
    border-radius: 8px;
}

.course-cards {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
    padding: 20px;
}

.modal-close {
    position: absolute;
    right: 20px;
    top: 20px;
    background: none;
    border: none;
    color: white;
    font-size: 32px;
    cursor: pointer;
    z-index: 1000;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: rgba(0, 0, 0, 0.5);
    transition: background 0.3s;
}

.modal-close:hover {
    background: rgba(0, 0, 0, 0.8);
}

.map-details {
    display: flex;
    justify-content: center;
    gap: 2rem;
    padding: 1rem;
    background: #f5f5f5;
    border-radius: 0 0 8px 8px;
}

.map-stat {
    text-align: center;
}

.map-stat .label {
    font-size: 0.9em;
    color: #666;
    margin-bottom: 0.25rem;
}

.map-stat .value {
    font-size: 1.2em;
    font-weight: 600;
    color: #333;
}
</style>

<?php include 'includes/footer.php'; ?>