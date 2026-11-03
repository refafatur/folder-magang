<?php 
require_once 'config/database.php';
include 'includes/header.php';

// Fetch VIP rooms from database
try {
    $sql = "SELECT * FROM tbl_vip_rooms";
    $result = $conn->query($sql);
    if (!$result) {
        throw new Exception("Error fetching rooms: " . $conn->error);
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    $result = false;
}
?>

<header class="hero" style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('assets/rooms/1.avif'); background-position: center; background-size: cover; height: 100vh;padding: 0; display: flex; align-items: center; justify-content: center;">
        <div class="hero-content" data-aos="fade-up" data-aos-duration="1000">
            <span class="section-subtitle" style="font-size: 2em; margin-bottom: 100px; margin: 0 auto; color: white;">VIP room and Matana</span>
        </div>
    </header>


    <!-- Room Section -->
    <section class="promotions-section">
        <div class="container">
            <div class="intro-text text-center" data-aos="fade-up">
                <span class="section-subtitle">LUXURY ACCOMMODATIONS</span>
                <h2 class="section-title" style="color: black;">The most comfortable room to meeting & event</h2>
                <p class="intro-description" style="color: black">
                    The VIP Pavilion of Klub Golf Bogor Raya is unmatched anywhere in Indonesia. This facility offers exclusive treatment for those who like to be pampered like a king. Upon arriving at the club, one is ushered straight into the pavilion, which includes lounge and dining area with private locker rooms. Equipped with TV and personalized service, the VIP pavilion is ideal for functions and group golfing getaways. With direct access to the course, one need not enter the club house at all and this creates feeling of golfing on your very own private golf course. There is 6 VIP Room with different capacities (8 – 40 pax) that suit for your private group.
                </p>
            </div>

            <div class="rooms-grid">
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php 
                    $delay = 0;
                    while($room = $result->fetch_assoc()): 
                    ?>
                        <div class="room-card" data-aos="fade-up" data-aos-delay="<?php echo $delay; ?>">
                            <div class="room-image">
                                <img src="../<?php echo htmlspecialchars($room['image']); ?>" alt="<?php echo htmlspecialchars($room['name']); ?>">
                            </div>
                            <div class="room-details">
                                <h3><?php echo htmlspecialchars($room['name']); ?></h3>
                                <p class="room-description"><?php echo htmlspecialchars($room['description']); ?></p>
                                <!-- <div class="room-footer">
                                    <a href="#" class="btn-book">Reserve Now</a>
                                </div> -->
                            </div>
                        </div>
                    <?php 
                        $delay += 100;
                    endwhile; 
                    ?>
                <?php else: ?>
                    <p class="no-rooms" data-aos="fade-up">No VIP rooms available at the moment.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

<?php include 'includes/footer.php'; ?>