<?php include 'includes/header.php'; ?>
<?php require_once 'config/database.php'; ?>

<header class="hero" style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('assets/meet/1.avif'); background-position: center; background-size: cover; height: 100vh;padding: 0; display: flex; align-items: center; justify-content: center;">
    <div class="hero-content" data-aos="fade-up" data-aos-duration="1000">
        <span class="section-subtitle" style="font-size: 2em; margin-bottom: 100px; margin: 0 auto; color: white;">Meeting & Gathering</span>
    </div>
    </header>


    <!-- Meeting & Gathering Section -->
    <section class="promotions-section">
        <div class="container">
            <div class="intro-text text-center mb-5" data-aos="fade-up">
                <h2 class="section-subtitle mb-4">Meet in a convenient location</h2>
                <p class="lead mx-auto">
                    Besides golf, we have room facilities available for meetings and gatherings. Our private rooms for company meetings 
                    are fully equipped with essential meeting equipment (screen, projector, notepad, mineral water).
                </p>
                <p class="lead mx-auto">
                    Klub Golf Bogor Raya offers both outdoor and indoor venues for your company or family gatherings. 
                    Our spaces can accommodate 100 - 300 persons, providing both a comfortable environment and beautiful views.
                </p>
            </div>
<br><br>
            <div class="facilities-header text-center mb-4" data-aos="fade-up">
                <span class="section-subtitle">PHOTOS</span>
            </div>

            <style>
            .gallery-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
                grid-auto-rows: 300px;
                grid-auto-flow: dense;
                gap: 5px;
                padding: 5px;
            }

            .gallery-item {
                position: relative;
                overflow: hidden;
                width: 100%;
                height: 100%;
            }

            .gallery-item.wide {
                grid-column: span 2;
            }

            .gallery-item.tall {
                grid-row: span 2;
            }

            .gallery-item img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                transition: transform 0.3s;
            }

            .gallery-item:hover img {
                transform: scale(1.1);
            }
            </style>

            <div class="gallery-grid">
                <?php 
                $sql = "SELECT * FROM tbl_meet_gathering ORDER BY RAND()";
                $result = $conn->query($sql);

                if ($result && $result->num_rows > 0): 
                    $delay = 0;
                    while($row = $result->fetch_assoc()): 
                        $randomSize = rand(1, 3);
                        $classes = 'gallery-item';
                        switch($randomSize) {
                            case 1: break;
                            case 2: $classes .= ' wide'; break;
                            case 3: $classes .= ' tall'; break;
                        }
                ?>
                    <div class="<?php echo $classes; ?>" 
                         data-aos="zoom-in"
                         data-aos-delay="<?php echo $delay; ?>">
                        <img src="../<?php echo htmlspecialchars($row['image']); ?>" 
                             loading="lazy"
                             alt="Meet & Gathering">
                    </div>
                <?php 
                    $delay += 100;
                    endwhile;
                else: ?>
                    <p class="text-center">No photos available</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

<?php include 'includes/footer.php'; ?>