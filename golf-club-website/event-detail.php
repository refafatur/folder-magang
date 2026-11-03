<?php
require_once 'config/database.php';

if (!isset($_GET['id'])) {
    header('Location: event.php');
    exit;
}

$event_id = $_GET['id'];
$query = "SELECT * FROM tbl_events WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();
$event = $result->fetch_assoc();

if (!$event) {
    header('Location: event.php');
    exit;
}

?>

<?php include 'includes/header.php'; ?>

<section class="event-detail-section" style="background-color: #f8f9fa; margin-top:150px; padding: 40px 0;">
    <div class="container" style="max-width: 1200px; margin: 0 auto;">
        <div class="event-header" style="text-align: center; margin-bottom: 40px;">
            <h2 style="color:#2c3e50; font-size: 2.5em; margin-bottom: 20px;"><?php echo htmlspecialchars($event['name']); ?></h2>
            <div style="color: #666; font-size: 1.2em;">
                <i class="far fa-calendar-alt"></i> 
                <?php echo date('F d, Y', strtotime($event['event_date'])); ?>
            </div>
        </div>
        
        <div class="event-detail-content" style="background: white; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
            <div class="event-image" data-aos="fade-up" style="width: 100%; max-width: 1000px; height: 500px; overflow: hidden; margin: 0 auto; border-radius: 15px 15px 0 0;">
                <img src="../<?php echo $event['image']; ?>" 
                    alt="<?php echo htmlspecialchars($event['name']); ?>"
                    style="width: 100%; height: 100%; object-fit: cover; object-position: center;">
            </div>
            
            <div class="event-info" style="padding: 40px;">
                <div class="event-description" data-aos="fade-up" data-aos-delay="100" style="margin-bottom: 30px;">
                    <h3 style="color: #2c3e50; font-size: 1.8em; margin-bottom: 20px;">Event Description</h3>
                    <p style="
                        color: #666;
                        line-height: 1.5;
                        font-size: 0.85em;
                        margin: 0;
                        padding: 10px 18px;
                        background: #f4f6f8;
                        border-radius: 8px;
                        box-shadow: 0 2px 8px rgba(44,62,80,0.04);
                        letter-spacing: 0.01em;
                    ">
                        <?php echo nl2br(htmlspecialchars($event['description'])); ?>
                    </p>
                </div>

                <!-- Location Map -->
                <div class="event-location" data-aos="fade-up" data-aos-delay="200" style="margin-bottom: 30px;">
                    <h3 style="color: #2c3e50; font-size: 1.8em; margin-bottom: 20px;">Event Location</h3>
                    <div class="map" style="width: 100%; height: 400px; border-radius: 10px; overflow: hidden;">
                        <iframe 
                            width="100%" 
                            height="100%" 
                            frameborder="0" 
                            style="border:0" 
                             src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3963.389127663056!2d106.83364647453631!3d-6.598466964502125!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69c66bf0d0549b%3A0xacf4c6f2da35672f!2sKlub%20Golf%20Bogor%20Raya!5e0!3m2!1sid!2sus!4v1749613183159!5m2!1sid!2sus" 
                            allowfullscreen>
                        </iframe>
                    </div>
                </div>
                
                <div class="text-center" data-aos="fade-up" data-aos-delay="300" style="text-align: center;">
                    <a href="event.php" class="btn-register" style="text-decoration: none; display: block; text-align: center;">
                        <i class="fas fa-arrow-left"></i> Back to Events
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
