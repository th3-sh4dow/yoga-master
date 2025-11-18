# Online Yoga Booking System - Complete Setup Guide

## 🎯 Overview
Complete booking system for Online Yoga at Home membership plans with 4 different pricing tiers.

## 📋 Membership Plans Available

### 1. Weekly Membership Plan - ₹1,499
- 5 live sessions (1–2 hours each)
- Session recordings sent on WhatsApp or Email
- Live class link shared 15 minutes before session
- Expert support anytime
- Basic counselling + diet suggestions

### 2. Monthly Membership Plan - ₹3,999
- 20+ live classes (1–2 hours each)
- All session recordings for revision
- Anytime expert help
- Personalised guidance & counselling
- Diet plans for better physical & mental health

### 3. Quarterly Membership Plan - ₹9,999
- 20 live classes every month (1–2 hours each)
- Full access to recorded sessions
- Direct expert support whenever needed
- Special counselling sessions
- Holistic guidance on diet & lifestyle
- Structured plan for your continuous growth

### 4. Flexible Yoga Plan - ₹500/session
- Session as per your need (1–2 hours)
- Expert-led guidance
- Focus on your specific goal or health condition

## 🔧 Technical Setup

### Files Updated:
1. **online-classes.html** - Main membership plans page
2. **js/booking-modal.js** - Booking system with online yoga support
3. **payment-forms-config.php** - Payment form mappings
4. **admin-payment-forms.php** - Admin panel payment forms
5. **test-payment-forms.php** - Testing configuration
6. **admin-bookings.html** - Admin booking management

### Payment Form URLs (Cashfree):
- Weekly: `https://payments.cashfree.com/forms/online-yoga-weekly-1499`
- Monthly: `https://payments.cashfree.com/forms/online-yoga-monthly-3999`
- Quarterly: `https://payments.cashfree.com/forms/online-yoga-quarterly-9999`
- Flexible: `https://payments.cashfree.com/forms/online-yoga-flexible-500`

## 🚀 How to Use

### For Customers:
1. Visit `online-classes.html`
2. Choose a membership plan
3. Click "Book Now" button
4. Fill booking form (no dates needed for online)
5. Choose payment method:
   - **Pay Now**: Redirects to Cashfree payment
   - **WhatsApp**: Sends booking request via WhatsApp

### For Admin:
1. Visit `admin.html`
2. Go to "Bookings" section
3. Filter by "Online Yoga at Home"
4. Manage bookings and payment status

## 📱 WhatsApp Integration
- Automatic WhatsApp message generation
- Different message format for online vs retreat bookings
- Includes membership plan details and pricing
- Contact: +91-8969464548

## 📧 Email Notifications
- Booking confirmation emails
- Payment success/failure notifications
- Owner notifications for new bookings

## 🧪 Testing
- Use `test-online-booking.html` to test the booking modal
- Use `test-payment-forms.php` to verify payment form mappings
- Check `admin-bookings.html` for booking management

## 📊 Database Structure
Online yoga bookings are stored with:
- Program: "Online Yoga at Home"
- Accommodation: weekly/monthly/quarterly/flexible
- Occupancy: "online"
- No check-in/check-out dates required

## 🎨 UI Features
- Color-coded membership plans
- Professional badges (STARTER, POPULAR, BEST VALUE, FLEXIBLE)
- Responsive design
- Gradient buttons and pricing sections
- Clean, modern layout

## 📞 Support
- WhatsApp: +91-8969464548
- Email: naturelandyogchetna@gmail.com

## 🔄 Next Steps
1. Create actual Cashfree payment forms with the URLs mentioned above
2. Test the complete booking flow
3. Set up email templates for online yoga bookings
4. Configure WhatsApp Business API if needed
5. Add analytics tracking for booking conversions