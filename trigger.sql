DELIMITER $$
CREATE TRIGGER prevent_duplicate_membership
BEFORE INSERT ON membership
FOR EACH ROW
BEGIN
    -- Check if the student is already a member of the requested club
    IF EXISTS (
        SELECT 1 
        FROM membership 
        WHERE student_id = NEW.student_id 
          AND club_id = NEW.club_id
    ) THEN
        -- Raise an error and pass the custom message
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'You have already joined this club.';
    END IF;
END$$
DELIMITER ;
