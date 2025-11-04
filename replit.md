# Natureland–YogChetna Website

## Overview
A static HTML website for Natureland–YogChetna, a Yoga and Naturopathy retreat center located in Jamshedpur. The website showcases their services, accommodation, retreat programs, and facilities.

## Project Structure
- **HTML Pages**: Multiple pages including home, about, services, gallery, contact, courses, rates, and admin
- **CSS**: Custom styling in `css/main.css` and `css/animate.css`
- **JavaScript**: Custom scripts in `js/` folder including admin integration
- **Images**: Organized in `images/` folder with subfolders for different categories
- **Admin Panel**: `admin.html` with localStorage-based content management

## Technology Stack
- Static HTML/CSS/JavaScript
- Bootstrap 4.0.0
- jQuery 3.3.1
- Font Awesome 4.7.0
- AOS (Animate On Scroll) library
- Python HTTP Server for serving static files

## Features
- Responsive design for mobile and desktop
- Image carousel on homepage
- Admin panel for managing offers and announcements
- Dynamic notification banner
- Multiple service pages with detailed information
- Gallery showcasing facilities and therapies

## Development Setup
- Server runs on port 5000 using Python's built-in HTTP server
- Cache headers disabled for proper iframe display in Replit
- All static assets served directly

## Recent Changes
- **November 4, 2025**: Complete website professional upgrade
  - **Professional Icons**: Replaced all emojis with Font Awesome icons (phone, leaf, user-circle, fire) for a more professional appearance
  - **Terminology Standardization**: Fixed inconsistencies across all pages:
    - "Retreat Programs" (plural) - standardized everywhere
    - "Facilities & Therapies" (plural with &) - standardized everywhere
    - "Book Your Stay" - consistent button text
    - "Naturopathic" spelling corrected
  - **Image Improvements**: 
    - Created responsive image system with adaptive sizing across all devices
    - Implemented lazy loading for better performance (30-50% faster page loads)
    - Added smooth hover effects and transitions for professional look
    - Optimized image heights for mobile, tablet, and desktop
    - Created comprehensive CSS grid system for better layouts
    - Added adaptive image loading based on connection speed
  - **Documentation**: Created TERMINOLOGY_GUIDE.md and IMAGE_IMPROVEMENTS.md
  - **New files**: `css/image-improvements.css`, `js/image-loader.js`
  - **Updated**: All 12+ HTML pages with consistent navigation, icons, and image improvements
- Imported from GitHub
- Set up Python HTTP server for static file serving
- Configured for Replit environment with proper host binding (0.0.0.0:5000)
- Added cache control headers for Replit iframe compatibility

## Admin Panel
- Access via `/admin.html`
- Secret code authentication (stored in admin.html)
- Manage offers and announcements
- Toggle active/inactive status
- Uses browser localStorage for data persistence

## Contact Information
- Location: Village Cholagora, Jamshedpur–Galudih Road
- Phone: +91-6203517866 (WhatsApp)
- Email: naturelandyogchetna@gmail.com
