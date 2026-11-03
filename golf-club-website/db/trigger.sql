DELIMITER //

CREATE TRIGGER after_booking_insert 
AFTER INSERT ON bookings
FOR EACH ROW
BEGIN
    INSERT INTO booking_notifications (booking_id, notification_type, created_at)
    VALUES (NEW.id, 'new_booking', NOW());
END//

DELIMITER ;
