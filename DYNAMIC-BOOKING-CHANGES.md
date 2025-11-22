# Dynamic Booking System Changes

## Problem Solved
Made the booking system dynamic so that:
- **For booking time slots (retreat programs)**: Accommodation is included (garden cottage, premium cottage with occupancy)
- **For membership plans (online classes)**: No accommodation - only membership plans are shown

## Changes Made

### 1. Updated `js/booking-modal.js`
- **`populateAccommodationOptions()`**: Now dynamically shows either accommodation options OR membership plans based on program type
- **`updatePriceSummary()`**: Updates labels to show "Accommodation:" for retreats and "Membership Plan:" for online classes
- **`validateForm()`**: Dynamic validation messages based on program type
- **`handleWhatsAppBooking()`**: Different WhatsApp messages for online vs retreat bookings
- **`handleProgramChange()`**: Properly updates section labels when switching between program types

### 2. Updated `payment-forms-config.php`
- Added payment form mappings for all online class types (Meditation, Therapeutic, Ashtanga)
- Maintains separate payment forms for retreat accommodations vs online membership plans

### 3. Updated `booking-system.php`
- Enhanced payment link generation to handle online classes vs retreats differently
- For online classes: uses membership plan as accommodation with 'online' occupancy
- For retreats: uses actual accommodation and occupancy

## How It Works Now

### For Online Classes:
- Shows "Select Membership Plan *" instead of accommodation
- Options: Weekly, Monthly, Quarterly, Flexible plans
- No date selection (dates are disabled/hidden)
- Price summary shows "Membership Plan:" instead of "Accommodation:"
- WhatsApp message mentions "no accommodation required"

### For Retreat Programs:
- Shows "Accommodation Type & Occupancy *"
- Options: Garden/Premium Cottage with Single/Double occupancy
- Date selection is required and enabled
- Price summary shows "Accommodation:" 
- WhatsApp message mentions "includes accommodation and all retreat activities"

## Benefits
1. **Clear User Experience**: Users see relevant options based on their selection
2. **No Confusion**: Online classes don't show irrelevant accommodation options
3. **Dynamic Labels**: Interface adapts to show appropriate terminology
4. **Proper Data Handling**: Backend correctly processes different booking types
5. **Accurate Messaging**: WhatsApp and email messages reflect the booking type

The system now intelligently adapts based on whether the user is booking:
- A physical retreat (needs accommodation)
- An online membership (no accommodation needed)