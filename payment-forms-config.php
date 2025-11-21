<?php
/**
 * Cashfree Payment Forms Configuration
 * Maps program combinations to specific Cashfree payment form URLs
 */

class PaymentFormsConfig {
    
    /**
     * Get all payment form mappings
     * Format: 'program_accommodation_occupancy' => 'cashfree_form_url'
     */
    public static function getPaymentForms() {
        return [
            // 3-Day Wellness & Retreat Programs
            '3-Day Wellness & Retreat_garden_cottage_single' => 'https://payments.cashfree.com/forms/3days-garden-cottage',
            '3-Day Wellness & Retreat_garden_cottage_double' => 'https://payments.cashfree.com/forms/3-days-Garden-Cottage-Double',
            '3-Day Wellness & Retreat_premium_cottage_single' => 'https://payments.cashfree.com/forms/3-days-Premium-Cottage-Single',
            '3-Day Wellness & Retreat_premium_cottage_double' => 'https://payments.cashfree.com/forms/3days-Premium-Cottage',
            

            // 7 Days Yoga & Wellness Detox Retreat
            '7 Days Yoga & Wellness Detox Retreat_garden_cottage_single' => 'https://payments.cashfree.com/forms/1-Week-Garden-Cottage-Single',
            '7 Days Yoga & Wellness Detox Retreat_garden_cottage_double' => 'https://payments.cashfree.com/forms/1-week-Garden-Cottage-Double',
            '7 Days Yoga & Wellness Detox Retreat_premium_cottage_single' => 'https://payments.cashfree.com/forms/1-week-Premium-Cottage-Single',
            '7 Days Yoga & Wellness Detox Retreat_premium_cottage_double' => 'https://payments.cashfree.com/forms/1-week-Premium-Cottage-Double',
            
            // Weekend Wellness Yoga Retreat (2 Days)
            'Weekend Wellness Yoga Retreat_garden_cottage_single' => 'https://payments.cashfree.com/forms/Weekend-Wellness-Garden-Cottage-Single-Occupancy',
            'Weekend Wellness Yoga Retreat_garden_cottage_double' => 'https://payments.cashfree.com/forms/Weekend-Wellness-Garden-Cottage-Double-Occupancy',
            'Weekend Wellness Yoga Retreat_premium_cottage_single' => 'https://payments.cashfree.com/forms/Weekend-Wellness-Premium-Cottage-Single-Occupancy',
            'Weekend Wellness Yoga Retreat_premium_cottage_double' => 'https://payments.cashfree.com/forms/Weekend-Wellness-Premium-Cottage-Double-Occupancy',
            
            // Online Yoga at Home - Membership Plans
            'Online Yoga at Home_weekly_online' => 'https://payments.cashfree.com/forms/Weekly-Membership-Plan',
            'Online Yoga at Home_monthly_online' => 'https://payments.cashfree.com/forms/Monthly-Membership-Plan',
            'Online Yoga at Home_quarterly_online' => 'https://payments.cashfree.com/forms/Quarterly-Membership-Plan',
            'Online Yoga at Home_flexible_online' => 'https://payments.cashfree.com/forms/Flexible-Yoga-Plan'
        ];
    }
    
    /**
     * Get payment form URL for specific program combination
     */
    public static function getPaymentFormUrl($program, $accommodation, $occupancy) {
        $forms = self::getPaymentForms();
        $lookup_key = $program . '_' . $accommodation . '_' . $occupancy;
        
        if (isset($forms[$lookup_key])) {
            return $forms[$lookup_key];
        }
        
        // Try fallback mappings for common variations
        $fallbacks = self::getFallbackMappings();
        foreach ($fallbacks as $pattern => $form_url) {
            if (strpos($lookup_key, $pattern) !== false) {
                return $form_url;
            }
        }
        
        // Default fallback
        return 'https://payments.cashfree.com/forms/3days-garden-cottage';
    }
    
    /**
     * Fallback mappings for partial matches
     */
    private static function getFallbackMappings() {
        return [
            '3-Day_garden_single' => 'https://payments.cashfree.com/forms/3days-garden-cottage',
            '3-Day_garden_double' => 'https://payments.cashfree.com/forms/3-days-Garden-Cottage-Double',
            '3-Day_premium_single' => 'https://payments.cashfree.com/forms/3-days-Premium-Cottage-Single',
            '3-Day_premium_double' => 'https://payments.cashfree.com/forms/3days-Premium-Cottage',
            
            '7_garden_single' => 'https://payments.cashfree.com/forms/1-Week-Garden-Cottage-Single',
            '7_garden_double' => 'https://payments.cashfree.com/forms/1-week-Garden-Cottage-Double',
            '7_premium_single' => 'https://payments.cashfree.com/forms/1-week-Premium-Cottage-Single',
            '7_premium_double' => 'https://payments.cashfree.com/forms/1-week-Premium-Cottage-Double',
            
            'Weekend_garden_single' => 'https://payments.cashfree.com/forms/Weekend-Wellness-Garden-Cottage-Single-Occupancy',
            'Weekend_garden_double' => 'https://payments.cashfree.com/forms/Weekend-Wellness-Garden-Cottage-Double-Occupancy',
            'Weekend_premium_single' => 'https://payments.cashfree.com/forms/Weekend-Wellness-Premium-Cottage-Single-Occupancy',
            'Weekend_premium_double' => 'https://payments.cashfree.com/forms/Weekend-Wellness-Premium-Cottage-Double-Occupancy'
        ];
    }
    
    /**
     * Get all available programs
     */
    public static function getAvailablePrograms() {
        return [
            'Weekend Wellness Yoga Retreat',
            '3-Day Wellness & Retreat',
            '7 Days Yoga & Wellness Detox Retreat',
            'Online Yoga at Home'
        ];
    }
    
    /**
     * Get all available accommodations
     */
    public static function getAvailableAccommodations() {
        return [
            'garden_cottage',
            'premium_cottage'
        ];
    }
    
    /**
     * Get all available occupancy types
     */
    public static function getAvailableOccupancies() {
        return [
            'single',
            'double'
        ];
    }
    
    /**
     * Validate if a combination exists
     */
    public static function isValidCombination($program, $accommodation, $occupancy) {
        $forms = self::getPaymentForms();
        $lookup_key = $program . '_' . $accommodation . '_' . $occupancy;
        return isset($forms[$lookup_key]);
    }
    
    /**
     * Get debug information for a lookup
     */
    public static function getDebugInfo($program, $accommodation, $occupancy) {
        $lookup_key = $program . '_' . $accommodation . '_' . $occupancy;
        $forms = self::getPaymentForms();
        
        return [
            'lookup_key' => $lookup_key,
            'exists' => isset($forms[$lookup_key]),
            'url' => self::getPaymentFormUrl($program, $accommodation, $occupancy),
            'all_keys' => array_keys($forms)
        ];
    }
}
?>