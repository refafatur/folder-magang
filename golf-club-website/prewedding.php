<?php 
require_once 'config/database.php';
include 'includes/header.php';

// Fetch pre-wedding spots from database
try {
    $type = isset($_GET['type']) ? $conn->real_escape_string($_GET['type']) : 'Pre-Wedding';
    if ($type) {
        $sql = "SELECT * FROM tbl_wedding WHERE type = '$type' ORDER BY created_at DESC";
    } else {
        $sql = "SELECT * FROM tbl_wedding ORDER BY created_at DESC";
    }
    $result = $conn->query($sql);
    if (!$result) {
        throw new Exception("Error fetching pre-wedding spots: " . $conn->error);
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    $result = false;
}
?>

<header class="hero" style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('assets/prewedding/1.avif'); background-position: center; background-size: cover; height: 100vh;padding: 0; display: flex; align-items: center; justify-content: center;">
        <div class="hero-content" data-aos="fade-up" data-aos-duration="1000">
            <span class="section-subtitle" style="font-size: 2em; margin-bottom: 100px; margin: 0 auto; color: white;">Pre-Weeding Photoshoot</span>
        </div>
    </header>

    <section class="wedding-section">
        <div class="container">
            <div class="intro-text text-center" data-aos="fade-up">
                <span class="section-subtitle">Get the most beautiful moments before the wedding</span>
                <p class="intro-description" style="color: black">
                    Searching for an unique view for your pre-wedding photo shoot, Klub Golf Bogor Raya is the best place. With the beautiful golf and garden view will make your moment captured for once in a lifetime.
                </p>
            </div>

            <style>
                .wedding-showcase {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                    gap: 15px;
                    padding: 15px;
                    width: 100%;
                    max-width: 100%;
                }
                .venue-image {
                    position: relative;
                    overflow: hidden;
                    border-radius: 8px;
                    break-inside: avoid;
                    width: 100%;
                }
                .venue-info {
                    position: absolute;
                    bottom: 0;
                    left: 0;
                    right: 0;
                    background: linear-gradient(transparent, rgba(0,0,0,0.8));
                    padding: 20px;
                    color: white;
                }
                @media (max-width: 1200px) {
                    .wedding-showcase {
                        grid-template-columns: repeat(3, 1fr);
                    }
                }
                @media (max-width: 768px) {
                    .wedding-showcase {
                        grid-template-columns: repeat(2, 1fr);
                    }
                }

                /* Modal styles */
                .modal {
                    display: none;
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0,0,0,0.9);
                    z-index: 1000;
                    cursor: pointer;
                }
                .modal-content {
                    position: absolute;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    max-width: 90%;
                    max-height: 90vh;
                }
                .modal-close {
                    position: absolute;
                    top: 20px;
                    right: 20px;
                    color: white;
                    font-size: 30px;
                    cursor: pointer;
                }
                .venue-image img {
                    cursor: pointer;
                    transition: transform 0.3s ease;
                }
                .venue-image img:hover {
                    transform: scale(1.05);
                }
            </style>

            <!-- Modal -->
            <div id="imageModal" class="modal">
                <span class="modal-close">&times;</span>
                <img class="modal-content" id="modalImage">
            </div>

            <div class="wedding-showcase">
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php 
                    $delay = 0;
                    while($spot = $result->fetch_assoc()): 
                        $randomHeight = rand(200, 500);
                        $randomSpan = rand(1, 3);
                    ?>
                        <div class="venue-image" 
                             data-aos="fade-up"
                             data-aos-delay="<?php echo $delay; ?>"
                             style="height: <?php echo $randomHeight; ?>px; grid-column: span <?php echo $randomSpan; ?>;">
                            <img src="../<?php echo htmlspecialchars($spot['foto']); ?>" 
                                 
                                 onclick="openModal(this.src)"
                                 style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                    <?php 
                        $delay += 100;
                    endwhile; 
                    ?>

                    <script>
                        const modal = document.getElementById('imageModal');
                        const modalImg = document.getElementById('modalImage');

                        function openModal(imgSrc) {
                            modal.style.display = "block";
                            modalImg.src = imgSrc;
                        }

                        modal.onclick = function() {
                            modal.style.display = "none";
                        }

                        document.querySelector('.modal-close').onclick = function() {
                            modal.style.display = "none";
                        }
                    </script>
                <?php else: ?>
                    <p>No pre-wedding spots available at the moment.</p>
                <?php endif; ?>
            </div>
            
        </div>
    </section>

<?php include 'includes/footer.php'; ?>