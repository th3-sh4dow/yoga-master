-- Update database to support specific online class types
USE yoga_retreat_bookings;

-- Add new columns to bookings table for better tracking
ALTER TABLE bookings 
ADD COLUMN class_type VARCHAR(100) AFTER program,
ADD COLUMN membership_plan VARCHAR(50) AFTER class_type;

-- Update existing online yoga bookings to have proper class_type
UPDATE bookings 
SET class_type = 'Online Yoga at Home',
    membership_plan = accommodation
WHERE program = 'Online Yoga at Home';

-- Create index for better performance
CREATE INDEX idx_class_type ON bookings(class_type);
CREATE INDEX idx_membership_plan ON bookings(membership_plan);