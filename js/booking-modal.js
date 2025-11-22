// Booking Modal JavaScript
class BookingModal {
    constructor() {
        this.modal = null;
        this.currentProgram = null;
        this.programs = {
            'weekend': {
                title: 'Weekend Wellness Yoga Retreat',
                duration: '2 Days & 1 Night',
                durationDays: 1,
                prices: {
                    'garden_single': 10000,
                    'garden_double': 8000,
                    'premium_single': 12000,
                    'premium_double': 10000
                }
            },
            '3day': {
                title: '3-Day Wellness & Retreat',
                duration: '3 Days & 2 Nights',
                durationDays: 2,
                prices: {
                    'garden_single': 16000,
                    'garden_double': 14000,  // Updated to match your requirement
                    'premium_single': 18000,
                    'premium_double': 16000
                }
            },

            '7day': {
                title: '7 Days Yoga & Wellness Detox Retreat',
                duration: '7 Days & 6 Nights',
                durationDays: 6,
                prices: {
                    'garden_single': 35000,
                    'garden_double': 30000,
                    'premium_single': 39000,
                    'premium_double': 34000
                }
            },
            'online': {
                title: 'Online Yoga at Home',
                duration: 'Flexible Schedule',
                prices: {
                    'weekly': 1499,
                    'monthly': 3999,
                    'quarterly': 9999,
                    'flexible': 500
                }
            }
        };
        this.init();
    }

    init() {
        this.createModal();
        this.attachEventListeners();
    }

    createModal() {
        const modalHTML = `
            <div id="bookingModal" class="booking-modal">
                <div class="booking-modal-content">
                    <div class="booking-modal-header">
                        <span class="close-modal">&times;</span>
                        <h2>Book Your Retreat</h2>
                        <p>Choose: Pay Now (Instant) or WhatsApp (Manual)</p>
                    </div>
                    <div class="booking-modal-body">
                        <form id="bookingForm" class="booking-form">
                            <!-- Personal Information -->
                            <div class="form-group full-width">
                                <label>Select Program *</label>
                                <small class="form-text text-muted mb-2">Choose your preferred retreat program</small>
                                <div class="program-selection" id="programSelection">
                                    <!-- Programs will be populated here -->
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="guestName">Full Name *</label>
                                    <input type="text" id="guestName" name="name" required>
                                </div>
                                <div class="form-group">
                                    <label for="guestEmail">Email Address *</label>
                                    <input type="email" id="guestEmail" name="email" required>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="guestPhone">Phone Number *</label>
                                    <input type="tel" id="guestPhone" name="phone" required>
                                </div>
                                <div class="form-group">
                                    <label for="emergencyContact">Emergency Contact</label>
                                    <input type="tel" id="emergencyContact" name="emergency_contact">
                                </div>
                            </div>

                            <!-- Accommodation Selection -->
                            <div class="form-group full-width" id="accommodationSection">
                                <label>Accommodation Type & Occupancy *</label>
                                <small class="form-text text-muted mb-2">Choose your accommodation and occupancy preference</small>
                                <div class="accommodation-options" id="accommodationOptions">
                                    <!-- Accommodation options will be populated here -->
                                </div>
                            </div>

                            <!-- Date Selection -->
                            <div class="form-row" id="dateSection">
                                <div class="form-group">
                                    <label for="checkInDate">Check-in Date *</label>
                                    <input type="date" id="checkInDate" name="check_in_date" required>
                                    <small class="form-text text-muted">Select your arrival date</small>
                                </div>
                                <div class="form-group">
                                    <label for="checkOutDate">Check-out Date *</label>
                                    <input type="date" id="checkOutDate" name="check_out_date" required>
                                    <small class="form-text text-muted" id="checkOutHelper">Will be calculated automatically based on program duration</small>
                                </div>
                            </div>

                            <!-- Special Requirements -->
                            <div class="form-group full-width">
                                <label for="specialRequirements">Special Requirements / Dietary Restrictions</label>
                                <textarea id="specialRequirements" name="special_requirements" placeholder="Please mention any dietary restrictions, health conditions, or special requests..."></textarea>
                            </div>

                            <!-- Price Summary -->
                            <div class="price-summary" id="priceSummary">
                                <h4>Booking Summary</h4>
                                <div class="price-breakdown">
                                    <span>Program:</span>
                                    <span id="selectedProgram">-</span>
                                </div>
                                <div class="price-breakdown" id="accommodationBreakdown">
                                    <span>Accommodation:</span>
                                    <span id="selectedAccommodation">-</span>
                                </div>
                                <div class="price-breakdown">
                                    <span>Duration:</span>
                                    <span id="selectedDuration">-</span>
                                </div>
                                <div class="total-amount">
                                    <span>Total Amount:</span>
                                    <span id="totalAmount">₹0</span>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="booking-actions">
                                <button type="button" class="btn-cancel" id="cancelBooking">Cancel</button>
                                <button type="button" class="btn-whatsapp" id="whatsappBooking">
                                    <i class="fa fa-whatsapp"></i> Book via WhatsApp
                                </button>
                                <button type="submit" class="btn-book" id="submitBooking">
                                    <i class="fa fa-credit-card"></i> Pay Now
                                </button>
                            </div>
                        </form>

                        <!-- Loading State -->
                        <div class="loading-spinner" id="loadingSpinner">
                            <div class="spinner"></div>
                            <p>Processing your booking...</p>
                        </div>

                        <!-- Success State -->
                        <div class="success-message" id="successMessage">
                            <i class="fa fa-check-circle"></i>
                            <h3>Booking Created Successfully!</h3>
                            <p>You will be redirected to the payment page shortly.</p>
                            <p><strong>Booking ID:</strong> <span id="bookingId"></span></p>
                        </div>

                        <!-- Error Message -->
                        <div class="error-message" id="errorMessage">
                            <i class="fa fa-exclamation-triangle"></i>
                            <span id="errorText"></span>
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHTML);
        this.modal = document.getElementById('bookingModal');
    }

    attachEventListeners() {
        // Close modal events
        const closeBtn = this.modal.querySelector('.close-modal');
        const cancelBtn = this.modal.querySelector('#cancelBooking');
        const whatsappBtn = this.modal.querySelector('#whatsappBooking');
        
        closeBtn.addEventListener('click', () => this.closeModal());
        cancelBtn.addEventListener('click', () => this.closeModal());
        whatsappBtn.addEventListener('click', () => this.handleWhatsAppBooking());
        
        // Close on outside click
        this.modal.addEventListener('click', (e) => {
            if (e.target === this.modal) {
                this.closeModal();
            }
        });

        // Form submission
        const form = this.modal.querySelector('#bookingForm');
        form.addEventListener('submit', (e) => this.handleFormSubmit(e));

        // Program and accommodation selection is now handled by click handlers

        // Date validation and auto-calculation
        const checkInDate = this.modal.querySelector('#checkInDate');
        const checkOutDate = this.modal.querySelector('#checkOutDate');
        
        checkInDate.addEventListener('change', () => {
            this.calculateCheckOutDate();
            this.validateDates();
        });
        checkOutDate.addEventListener('change', () => {
            this.validateDates();
            this.handleManualDateChange();
        });

        // Set minimum date to today
        const today = new Date().toISOString().split('T')[0];
        checkInDate.min = today;
        checkOutDate.min = today;
    }

    openModal(programType = null) {
        this.currentProgram = programType;
        this.populatePrograms();
        this.populateAccommodationOptions();
        this.modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
        
        // Pre-select program if specified
        if (programType && this.programs[programType]) {
            const programRadio = this.modal.querySelector(`input[value="${programType}"]`);
            if (programRadio) {
                programRadio.checked = true;
                this.handleProgramChange(programType);
            }
        }
    }

    closeModal() {
        this.modal.style.display = 'none';
        document.body.style.overflow = 'auto';
        this.resetForm();
    }

    populatePrograms() {
        const container = this.modal.querySelector('#programSelection');
        container.innerHTML = '';

        Object.keys(this.programs).forEach(key => {
            const program = this.programs[key];
            const programHTML = `
                <div class="program-option" data-program="${key}">
                    <input type="radio" name="program" value="${key}" required>
                    <div class="program-title">${program.title}</div>
                    <div class="program-duration">${program.duration}</div>
                    <div class="program-price">Starting from ₹${Math.min(...Object.values(program.prices)).toLocaleString()}</div>
                    <button type="button" class="read-more-btn" data-program="${key}">Read More</button>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', programHTML);
        });
        
        // Add click event listeners for program options
        this.addProgramClickHandlers();
        // Add click event listeners for read more buttons
        this.addReadMoreHandlers();
    }

    addProgramClickHandlers() {
        const programOptions = this.modal.querySelectorAll('.program-option');
        programOptions.forEach(option => {
            option.addEventListener('click', () => {
                // Check the radio button
                const radio = option.querySelector('input[type="radio"]');
                if (radio) {
                    radio.checked = true;
                    
                    // Update visual selection
                    programOptions.forEach(opt => opt.classList.remove('selected'));
                    option.classList.add('selected');
                    
                    // Handle program change
                    const programKey = option.dataset.program;
                    this.handleProgramChange(programKey);
                }
            });
        });
    }

    addAccommodationClickHandlers() {
        const accommodationOptions = this.modal.querySelectorAll('.accommodation-option');
        accommodationOptions.forEach(option => {
            option.addEventListener('click', () => {
                // Check the radio button
                const radio = option.querySelector('input[type="radio"]');
                if (radio) {
                    radio.checked = true;
                    
                    // Update visual selection
                    accommodationOptions.forEach(opt => opt.classList.remove('selected'));
                    option.classList.add('selected');
                    
                    // Update price summary
                    this.updatePriceSummary();
                }
            });
        });
    }

    addReadMoreHandlers() {
        const readMoreButtons = this.modal.querySelectorAll('.read-more-btn');
        readMoreButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                e.stopPropagation(); // Prevent triggering program selection
                const programKey = button.dataset.program;
                this.showProgramDetails(programKey);
            });
        });
    }

    showProgramDetails(programKey) {
        const program = this.programs[programKey];
        const programDetails = this.getProgramDetails(programKey);
        
        // Create details modal
        const detailsModal = document.createElement('div');
        detailsModal.className = 'program-details-modal';
        detailsModal.innerHTML = `
            <div class="program-details-content">
                <div class="program-details-header">
                    <h3>${program.title}</h3>
                    <span class="close-details">&times;</span>
                </div>
                <div class="program-details-body">
                    <div class="program-overview">
                        <h4>Program Overview</h4>
                        <p><strong>Duration:</strong> ${program.duration}</p>
                        <p><strong>Starting Price:</strong> ₹${Math.min(...Object.values(program.prices)).toLocaleString()}</p>
                    </div>
                    
                    <div class="program-description">
                        <h4>What's Included</h4>
                        <ul>
                            ${programDetails.includes.map(item => `<li>${item}</li>`).join('')}
                        </ul>
                    </div>
                    
                    <div class="program-benefits">
                        <h4>Benefits</h4>
                        <ul>
                            ${programDetails.benefits.map(benefit => `<li>${benefit}</li>`).join('')}
                        </ul>
                    </div>
                    
                    <div class="program-pricing">
                        <h4>Pricing Options</h4>
                        <div class="pricing-grid">
                            ${Object.entries(program.prices).map(([key, price]) => `
                                <div class="price-item">
                                    <span class="price-label">${this.getPriceLabel(key, programKey)}</span>
                                    <span class="price-amount">₹${price.toLocaleString()}</span>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                    
                    <div class="program-actions">
                        <button type="button" class="btn-select-program" data-program="${programKey}">
                            Select This Program
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(detailsModal);
        
        // Add event listeners
        const closeBtn = detailsModal.querySelector('.close-details');
        const selectBtn = detailsModal.querySelector('.btn-select-program');
        
        closeBtn.addEventListener('click', () => {
            document.body.removeChild(detailsModal);
        });
        
        selectBtn.addEventListener('click', () => {
            // Select the program and close details modal
            const programRadio = this.modal.querySelector(`input[value="${programKey}"]`);
            if (programRadio) {
                programRadio.checked = true;
                this.handleProgramChange(programKey);
            }
            document.body.removeChild(detailsModal);
        });
        
        // Close on outside click
        detailsModal.addEventListener('click', (e) => {
            if (e.target === detailsModal) {
                document.body.removeChild(detailsModal);
            }
        });
    }

    getProgramDetails(programKey) {
        const details = {
            'weekend': {
                includes: [
                    'Welcome drink and orientation session',
                    '4 guided yoga sessions (morning & evening)',
                    '2 meditation sessions',
                    'Healthy vegetarian meals (3 meals)',
                    'Accommodation for 1 night',
                    'Nature walks and relaxation time',
                    'Yoga mat and props provided',
                    'Certificate of participation'
                ],
                benefits: [
                    'Perfect introduction to yoga retreat experience',
                    'Stress relief and mental clarity',
                    'Improved flexibility and strength',
                    'Better sleep quality',
                    'Connection with nature',
                    'Meet like-minded people',
                    'Learn basic yoga techniques'
                ]
            },
            '3day': {
                includes: [
                    'Welcome ceremony and orientation',
                    '6 guided yoga sessions (morning & evening)',
                    '3 meditation and pranayama sessions',
                    'Ayurvedic consultation',
                    'Healthy vegetarian meals (9 meals total)',
                    'Accommodation for 2 nights',
                    'Nature walks and outdoor activities',
                    'Yoga philosophy workshop',
                    'Relaxation and spa time',
                    'All yoga equipment provided',
                    'Certificate of completion'
                ],
                benefits: [
                    'Deep relaxation and stress reduction',
                    'Improved physical fitness and flexibility',
                    'Enhanced mental focus and clarity',
                    'Better understanding of yoga principles',
                    'Detoxification of body and mind',
                    'Improved sleep patterns',
                    'Lasting friendships and connections',
                    'Personal transformation experience'
                ]
            },
            '7day': {
                includes: [
                    'Comprehensive welcome package',
                    '14 guided yoga sessions (morning & evening)',
                    '7 meditation and pranayama sessions',
                    'Personal Ayurvedic consultation and treatment',
                    'All vegetarian meals (21 meals total)',
                    'Accommodation for 6 nights',
                    'Daily nature walks and outdoor activities',
                    'Yoga philosophy and lifestyle workshops',
                    'Spa treatments and massage therapy',
                    'Cooking classes (healthy vegetarian)',
                    'Personal yoga practice development',
                    'All equipment and materials provided',
                    'Detailed progress assessment',
                    'Certificate of completion',
                    'Take-home wellness kit'
                ],
                benefits: [
                    'Complete physical and mental transformation',
                    'Significant improvement in flexibility and strength',
                    'Deep stress relief and emotional healing',
                    'Established daily yoga practice',
                    'Better understanding of Ayurvedic principles',
                    'Improved digestion and metabolism',
                    'Enhanced sleep quality and energy levels',
                    'Lasting lifestyle changes',
                    'Strong foundation in yoga philosophy',
                    'Personal growth and self-discovery',
                    'Lifelong wellness habits'
                ]
            },
            'online': {
                includes: [
                    'Live interactive yoga sessions via Zoom',
                    'Recorded sessions for practice anytime',
                    'Personal guidance from certified instructors',
                    'WhatsApp group support community',
                    'Weekly progress tracking',
                    'Customized yoga sequences',
                    'Meditation and breathing exercises',
                    'Yoga philosophy discussions',
                    'Flexible scheduling options',
                    'Digital certificates',
                    'Access to yoga resource library'
                ],
                benefits: [
                    'Practice from comfort of your home',
                    'Flexible timing to fit your schedule',
                    'Cost-effective compared to studio classes',
                    'Personal attention from instructors',
                    'Build consistent daily practice',
                    'Connect with global yoga community',
                    'Improve physical and mental health',
                    'Learn proper alignment and techniques',
                    'Reduce stress and anxiety',
                    'Better work-life balance'
                ]
            }
        };
        
        return details[programKey] || { includes: [], benefits: [] };
    }

    getPriceLabel(priceKey, programKey) {
        if (programKey === 'online') {
            const labels = {
                'weekly': 'Weekly Plan (5 sessions)',
                'monthly': 'Monthly Plan (20+ classes)',
                'quarterly': 'Quarterly Plan (20 classes/month)',
                'flexible': 'Flexible Plan (per session)'
            };
            return labels[priceKey] || priceKey;
        } else {
            const labels = {
                'garden_single': 'Garden Cottage - Single',
                'garden_double': 'Garden Cottage - Double',
                'premium_single': 'Premium Cottage - Single',
                'premium_double': 'Premium Cottage - Double'
            };
            return labels[priceKey] || priceKey;
        }
    }

    populateAccommodationOptions() {
        const container = this.modal.querySelector('#accommodationOptions');
        const accommodationSection = this.modal.querySelector('#accommodationSection');
        const dateSection = this.modal.querySelector('#dateSection');
        const checkInDate = this.modal.querySelector('#checkInDate');
        const checkOutDate = this.modal.querySelector('#checkOutDate');
        
        // For online classes, show membership plans (NO accommodation)
        if (this.currentProgram === 'online') {
            // Update section labels to reflect membership plans
            const sectionLabel = accommodationSection.querySelector('label');
            const sectionHelper = accommodationSection.querySelector('.form-text');
            
            if (sectionLabel) sectionLabel.textContent = 'Select Membership Plan *';
            if (sectionHelper) sectionHelper.textContent = 'Choose your preferred online yoga membership plan';
            
            accommodationSection.style.display = 'block';
            dateSection.style.display = 'none'; // Hide date section for online
            
            // Completely disable and clear date fields for online yoga
            if (checkInDate) {
                checkInDate.removeAttribute('required');
                checkInDate.disabled = true;
                checkInDate.value = '';
                checkInDate.style.display = 'none';
            }
            if (checkOutDate) {
                checkOutDate.removeAttribute('required');
                checkOutDate.disabled = true;
                checkOutDate.value = '';
                checkOutDate.style.display = 'none';
            }

            // Show membership plans (not accommodation)
            container.innerHTML = `
                <div class="accommodation-option" data-value="weekly">
                    <input type="radio" name="accommodation" value="weekly" required>
                    <strong>Weekly Membership Plan</strong><br>
                    <small>₹1,499 - 5 live sessions</small>
                </div>
                <div class="accommodation-option" data-value="monthly">
                    <input type="radio" name="accommodation" value="monthly" required>
                    <strong>Monthly Membership Plan</strong><br>
                    <small>₹3,999 - 20+ live classes</small>
                </div>
                <div class="accommodation-option" data-value="quarterly">
                    <input type="radio" name="accommodation" value="quarterly" required>
                    <strong>Quarterly Membership Plan</strong><br>
                    <small>₹9,999 - 20 classes/month</small>
                </div>
                <div class="accommodation-option" data-value="flexible">
                    <input type="radio" name="accommodation" value="flexible" required>
                    <strong>Flexible Yoga Plan</strong><br>
                    <small>₹500 per session</small>
                </div>
            `;
            
            // Add click event listeners
            this.addAccommodationClickHandlers();
            return; // Important: return here to prevent the regular accommodation setup
        } 
        
        // For retreat programs, show accommodation with occupancy
        else {
            // Update section labels to reflect accommodation
            const sectionLabel = accommodationSection.querySelector('label');
            const sectionHelper = accommodationSection.querySelector('.form-text');
            
            if (sectionLabel) sectionLabel.textContent = 'Accommodation Type & Occupancy *';
            if (sectionHelper) sectionHelper.textContent = 'Choose your accommodation and occupancy preference';
            
            accommodationSection.style.display = 'block';
            dateSection.style.display = 'block';
            
            // Re-enable and show date fields for retreat programs
            if (checkInDate) {
                checkInDate.setAttribute('required', 'required');
                checkInDate.disabled = false;
                checkInDate.style.display = 'block';
            }
            if (checkOutDate) {
                checkOutDate.setAttribute('required', 'required');
                checkOutDate.disabled = false;
                checkOutDate.style.display = 'block';
            }

            // Show accommodation options
            container.innerHTML = `
                <div class="accommodation-option" data-value="garden_single">
                    <input type="radio" name="accommodation" value="garden_single" required>
                    <strong>Garden Cottage</strong><br>
                    <small>Single Occupancy</small>
                </div>
                <div class="accommodation-option" data-value="garden_double">
                    <input type="radio" name="accommodation" value="garden_double" required>
                    <strong>Garden Cottage</strong><br>
                    <small>Double Occupancy</small>
                </div>
                <div class="accommodation-option" data-value="premium_single">
                    <input type="radio" name="accommodation" value="premium_single" required>
                    <strong>Premium Cottage</strong><br>
                    <small>Single Occupancy</small>
                </div>
                <div class="accommodation-option" data-value="premium_double">
                    <input type="radio" name="accommodation" value="premium_double" required>
                    <strong>Premium Cottage</strong><br>
                    <small>Double Occupancy</small>
                </div>
            `;
            
            // Add click event listeners
            this.addAccommodationClickHandlers();
        }
    }

    handleProgramChange(programKey) {
        this.currentProgram = programKey;
        
        // Update program selection UI
        const programOptions = this.modal.querySelectorAll('.program-option');
        programOptions.forEach(option => {
            option.classList.remove('selected');
            const radio = option.querySelector('input[type="radio"]');
            if (radio && radio.value === programKey) {
                option.classList.add('selected');
            }
        });

        // Update accommodation section label based on program type
        const accommodationLabel = this.modal.querySelector('#accommodationSection label');
        const accommodationHelper = this.modal.querySelector('#accommodationSection .form-text');
        
        if (programKey === 'online' || programKey === 'Online Yoga at Home') {
            if (accommodationLabel) accommodationLabel.textContent = 'Select Membership Plan *';
            if (accommodationHelper) accommodationHelper.textContent = 'Choose your preferred online yoga membership plan';
        } else {
            if (accommodationLabel) accommodationLabel.textContent = 'Accommodation Type & Occupancy *';
            if (accommodationHelper) accommodationHelper.textContent = 'Choose your accommodation and occupancy preference';
        }

        this.populateAccommodationOptions();
        
        // Update the price summary label immediately, even if no accommodation is selected yet
        const accommodationBreakdown = this.modal.querySelector('#accommodationBreakdown span:first-child') || 
                                     this.modal.querySelector('.price-breakdown span:first-child');
        
        if (accommodationBreakdown) {
            if (programKey === 'online' || programKey === 'Online Yoga at Home') {
                accommodationBreakdown.textContent = 'Membership Plan:';
                console.log('Program changed to online - updated label to: Membership Plan:');
            } else {
                accommodationBreakdown.textContent = 'Accommodation:';
                console.log('Program changed to retreat - updated label to: Accommodation:');
            }
        }
        
        this.updatePriceSummary();
        
        // Auto-calculate check-out date if check-in is already selected (only for non-online programs)
        if (programKey !== 'online' && programKey !== 'Online Yoga at Home') {
            this.calculateCheckOutDate();
        }
    }

    updatePriceSummary() {
        const selectedProgram = this.modal.querySelector('input[name="program"]:checked');
        const selectedAccommodation = this.modal.querySelector('input[name="accommodation"]:checked');

        console.log('updatePriceSummary called:', {
            selectedProgram: selectedProgram?.value,
            selectedAccommodation: selectedAccommodation?.value,
            currentProgram: this.currentProgram
        });

        if (!selectedProgram || !selectedAccommodation) {
            console.log('Missing selection, returning early');
            return;
        }

        const program = this.programs[selectedProgram.value];
        const accommodationType = selectedAccommodation.value;
        const price = program.prices[accommodationType] || 0;

        // Update summary display
        this.modal.querySelector('#selectedProgram').textContent = program.title;
        this.modal.querySelector('#selectedDuration').textContent = program.duration;
        
        // Update the accommodation/plan text based on program type
        let accommodationText = '';
        
        // Find the accommodation breakdown element more reliably
        const accommodationBreakdown = this.modal.querySelector('#accommodationBreakdown span:first-child') || 
                                     this.modal.querySelector('.price-breakdown span:first-child');
        
        console.log('Found accommodationBreakdown element:', accommodationBreakdown);
        
        if (this.currentProgram === 'online') {
            // For online classes, show membership plan (no accommodation)
            accommodationText = selectedAccommodation.parentElement.querySelector('strong').textContent;
            if (accommodationBreakdown) {
                accommodationBreakdown.textContent = 'Membership Plan:';
                console.log('Updated label to: Membership Plan:');
            }
        } else {
            // For retreat programs, show accommodation details
            const parts = accommodationType.split('_');
            accommodationText = `${parts[0].charAt(0).toUpperCase() + parts[0].slice(1)} Cottage (${parts[1].charAt(0).toUpperCase() + parts[1].slice(1)} Occupancy)`;
            if (accommodationBreakdown) {
                accommodationBreakdown.textContent = 'Accommodation:';
                console.log('Updated label to: Accommodation:');
            }
        }
        
        this.modal.querySelector('#selectedAccommodation').textContent = accommodationText;
        this.modal.querySelector('#totalAmount').textContent = `₹${price.toLocaleString()}`;

        console.log('Updated summary:', {
            program: program.title,
            accommodationText,
            price: `₹${price.toLocaleString()}`
        });

        // Update accommodation selection UI
        const accommodationOptions = this.modal.querySelectorAll('.accommodation-option');
        accommodationOptions.forEach(option => {
            option.classList.remove('selected');
            const radio = option.querySelector('input[type="radio"]');
            if (radio && radio.checked) {
                option.classList.add('selected');
            }
        });
    }

    calculateCheckOutDate() {
        const checkInInput = this.modal.querySelector('#checkInDate');
        const checkOutInput = this.modal.querySelector('#checkOutDate');
        const selectedProgram = this.modal.querySelector('input[name="program"]:checked');
        
        if (!selectedProgram) return;
        
        const program = this.programs[selectedProgram.value];
        
        if (selectedProgram.value === 'online' || selectedProgram.value === 'Online Yoga at Home') {
            // For online classes, completely disable date fields
            checkOutInput.value = '';
            checkOutInput.style.display = 'none';
            checkOutInput.parentElement.style.display = 'none';
            checkOutInput.classList.remove('auto-calculated');
            checkOutInput.removeAttribute('required');
            checkOutInput.disabled = true;
            
            // Also disable check-in date for online classes
            checkInInput.value = '';
            checkInInput.style.display = 'none';
            checkInInput.parentElement.style.display = 'none';
            checkInInput.removeAttribute('required');
            checkInInput.disabled = true;
            
            // Reset helper text
            const helper = this.modal.querySelector('#checkOutHelper');
            if (helper) {
                helper.textContent = 'Not applicable for online classes';
                helper.style.color = '#6c757d';
                helper.style.fontWeight = 'normal';
            }
            return;
        }
        
        if (!checkInInput.value) return;
        
        const checkInDate = new Date(checkInInput.value);
        
        // Get duration from program data
        let durationDays = program.durationDays || 1;
        
        // Calculate check-out date
        const checkOutDate = new Date(checkInDate);
        checkOutDate.setDate(checkInDate.getDate() + durationDays);
        
        // Format date as YYYY-MM-DD for input
        const formattedDate = checkOutDate.toISOString().split('T')[0];
        checkOutInput.value = formattedDate;
        
        // Show check-out date field for non-online programs
        checkOutInput.style.display = 'block';
        checkOutInput.parentElement.style.display = 'block';
        checkOutInput.setAttribute('required', 'required');
        
        // Add visual styling to indicate auto-calculation
        checkOutInput.classList.remove('manually-changed');
        checkOutInput.classList.add('auto-calculated');
        
        // Update helper text
        const helper = this.modal.querySelector('#checkOutHelper');
        if (helper) {
            helper.textContent = `✅ Auto-calculated: ${program.duration}`;
            helper.style.color = '#28a745';
            helper.style.fontWeight = '600';
        }
    }

    handleManualDateChange() {
        const checkOutInput = this.modal.querySelector('#checkOutDate');
        const helper = this.modal.querySelector('#checkOutHelper');
        
        if (checkOutInput.classList.contains('auto-calculated')) {
            // User manually changed the auto-calculated date
            checkOutInput.classList.remove('auto-calculated');
            checkOutInput.classList.add('manually-changed');
            
            if (helper) {
                helper.textContent = '⚠️ Manually modified - may affect program schedule';
                helper.style.color = '#ffc107';
                helper.style.fontWeight = '600';
            }
        }
    }

    validateForm(form) {
        // Clear previous error states
        this.clearValidationErrors();

        // Check required radio button selections first
        const selectedProgram = this.modal.querySelector('input[name="program"]:checked');
        const selectedAccommodation = this.modal.querySelector('input[name="accommodation"]:checked');

        if (!selectedProgram) {
            this.addValidationError('#programSelection', 'Please select a retreat program');
            return false;
        }

        if (!selectedAccommodation) {
            const errorMessage = this.currentProgram === 'online' ? 
                'Please select a membership plan' : 
                'Please select accommodation type and occupancy';
            this.addValidationError('#accommodationOptions', errorMessage);
            return false;
        }

        // For online programs, completely disable date validation
        if (selectedProgram.value === 'online' || selectedProgram.value === 'Online Yoga at Home') {
            const checkInDate = this.modal.querySelector('#checkInDate');
            const checkOutDate = this.modal.querySelector('#checkOutDate');
            
            // Completely disable date fields for online programs
            if (checkInDate) {
                checkInDate.removeAttribute('required');
                checkInDate.disabled = true;
                checkInDate.value = '';
            }
            if (checkOutDate) {
                checkOutDate.removeAttribute('required');
                checkOutDate.disabled = true;
                checkOutDate.value = '';
            }
            
            // Validate only non-date fields manually
            const requiredFields = form.querySelectorAll('input[required]:not([type="date"]), select[required], textarea[required]');
            for (let field of requiredFields) {
                if (!field.value.trim()) {
                    field.focus();
                    this.showError(`Please fill in the ${field.labels?.[0]?.textContent || field.name || 'required field'}`);
                    return false;
                }
            }
            
            // Check email format
            const email = form.querySelector('#guestEmail');
            if (email && email.value && !email.checkValidity()) {
                email.focus();
                this.showError('Please enter a valid email address');
                return false;
            }
            
            return true;
        }

        // For retreat programs, re-enable date fields and validate normally
        const checkInDate = this.modal.querySelector('#checkInDate');
        const checkOutDate = this.modal.querySelector('#checkOutDate');
        
        if (checkInDate) {
            checkInDate.disabled = false;
            checkInDate.setAttribute('required', 'required');
        }
        if (checkOutDate) {
            checkOutDate.disabled = false;
            checkOutDate.setAttribute('required', 'required');
        }

        // Check HTML5 validation for retreat programs
        if (!form.checkValidity()) {
            form.reportValidity();
            return false;
        }

        // Additional date validation for retreat programs
        const checkIn = checkInDate?.value;
        const checkOut = checkOutDate?.value;

        if (!checkIn) {
            this.addValidationError('#checkInDate', 'Please select a check-in date');
            return false;
        }

        if (!checkOut) {
            this.addValidationError('#checkOutDate', 'Please select a check-out date');
            return false;
        }

        // Validate date logic
        const checkInDateObj = new Date(checkIn);
        const checkOutDateObj = new Date(checkOut);

        if (checkOutDateObj <= checkInDateObj) {
            this.addValidationError('#checkOutDate', 'Check-out date must be after check-in date');
            return false;
        }

        return true;
    }

    clearValidationErrors() {
        // Remove error classes from all elements
        this.modal.querySelectorAll('.validation-error').forEach(element => {
            element.classList.remove('validation-error');
        });
        
        // Hide error message
        this.modal.querySelector('#errorMessage').style.display = 'none';
    }

    addValidationError(selector, message) {
        const element = this.modal.querySelector(selector);
        if (element) {
            element.classList.add('validation-error');
            
            // Scroll to the error element
            element.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        
        this.showError(message);
    }

    validateDates() {
        const checkIn = this.modal.querySelector('#checkInDate').value;
        const checkOut = this.modal.querySelector('#checkOutDate').value;

        if (checkIn && checkOut) {
            const checkInDate = new Date(checkIn);
            const checkOutDate = new Date(checkOut);

            if (checkOutDate <= checkInDate) {
                this.modal.querySelector('#checkOutDate').setCustomValidity('Check-out date must be after check-in date');
            } else {
                this.modal.querySelector('#checkOutDate').setCustomValidity('');
            }
        }
    }

    async handleFormSubmit(e) {
        e.preventDefault();
        
        // Validate required fields first
        const form = e.target;
        if (!this.validateForm(form)) {
            return;
        }
        
        const formData = new FormData(form);
        const bookingData = Object.fromEntries(formData.entries());
        
        // Debug: Log form data
        console.log('Form data entries:', Array.from(formData.entries()));
        console.log('Booking data from form:', bookingData);
        
        // Add calculated amount and parse accommodation data
        const selectedProgram = this.modal.querySelector('input[name="program"]:checked');
        const selectedAccommodation = this.modal.querySelector('input[name="accommodation"]:checked');
        
        if (selectedProgram && selectedAccommodation) {
            const program = this.programs[selectedProgram.value];
            bookingData.amount = program.prices[selectedAccommodation.value];
            bookingData.program = program.title;
            
            // Parse accommodation value to separate accommodation and occupancy
            const accommodationValue = selectedAccommodation.value;
            console.log('Selected accommodation value:', accommodationValue);
            console.log('Selected program value:', selectedProgram.value);
            
            if (selectedProgram.value === 'online') {
                // For online classes, determine the specific class type based on current page or selection
                let classType = 'Online Yoga at Home'; // default
                
                // Check if getCurrentClassType function exists (from online-classes.html page)
                if (typeof getCurrentClassType === 'function') {
                    classType = getCurrentClassType();
                } else {
                    // Fallback: Check which tab is currently active to determine class type
                    const activeTab = document.querySelector('.class-tab.selected');
                    if (activeTab) {
                        const tabId = activeTab.id;
                        if (tabId && tabId.includes('meditation')) {
                            classType = 'Online Meditation Class';
                        } else if (tabId && tabId.includes('therapeutic')) {
                            classType = 'Online Therapeutic Yoga';
                        } else if (tabId && tabId.includes('ashtanga')) {
                            classType = 'Online Ashtanga Yoga';
                        }
                    }
                }
                
                bookingData.class_type = classType;
                bookingData.membership_plan = accommodationValue; // weekly, monthly, quarterly, flexible
                bookingData.accommodation = accommodationValue; // for compatibility
                bookingData.occupancy = 'online'; // for online classes
                
                console.log('Class Type:', classType);
                console.log('Membership Plan:', accommodationValue);
            } else {
                // For retreat programs: garden_single, garden_double, premium_single, premium_double
                const parts = accommodationValue.split('_');
                if (parts.length >= 2) {
                    bookingData.accommodation = parts[0] + '_cottage'; // garden_cottage, premium_cottage
                    bookingData.occupancy = parts[1]; // single, double
                } else {
                    // Fallback if split doesn't work as expected
                    bookingData.accommodation = accommodationValue;
                    bookingData.occupancy = 'single'; // default
                }
                bookingData.class_type = program.title; // For retreats, class_type is same as program
                bookingData.membership_plan = null; // Not applicable for retreats
            }
            
            console.log('Final accommodation:', bookingData.accommodation);
            console.log('Final occupancy:', bookingData.occupancy);
            console.log('Final class_type:', bookingData.class_type);
            console.log('Final membership_plan:', bookingData.membership_plan);
        }

        // Debug: Log the data being sent
        console.log('Booking data being sent:', bookingData);

        this.showLoading();

        try {
            console.log('Making request to booking-system.php...');
            const response = await fetch('booking-system.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    action: 'create_booking',
                    ...bookingData
                })
            });

            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const responseText = await response.text();
            console.log('Raw response:', responseText);

            let result;
            try {
                // Try to extract JSON from response (in case there are PHP warnings)
                const jsonMatch = responseText.match(/\{.*\}$/);
                const jsonString = jsonMatch ? jsonMatch[0] : responseText;
                result = JSON.parse(jsonString);
            } catch (parseError) {
                console.error('JSON parse error:', parseError);
                console.error('Response text:', responseText);
                
                // Check if response contains success indicators
                if (responseText.includes('"success":true') && responseText.includes('booking_id')) {
                    // Try to extract the JSON part manually
                    const jsonStart = responseText.lastIndexOf('{"success"');
                    if (jsonStart !== -1) {
                        try {
                            result = JSON.parse(responseText.substring(jsonStart));
                        } catch (e) {
                            throw new Error('Could not parse JSON response');
                        }
                    } else {
                        throw new Error('Invalid JSON response from server');
                    }
                } else {
                    throw new Error('Invalid JSON response from server');
                }
            }

            console.log('Parsed result:', result);

            if (result.success) {
                this.showSuccess(result.booking_id);
                
                // Redirect to payment after 3 seconds
                setTimeout(() => {
                    window.open(result.payment_link, '_blank');
                    this.closeModal();
                }, 3000);
            } else {
                this.showError(result.message || 'Unknown error occurred');
            }
        } catch (error) {
            console.error('Fetch error:', error);
            this.showError(`Network error: ${error.message}. Please check if booking-system.php exists and is accessible.`);
        }
    }

    showLoading() {
        this.modal.querySelector('#bookingForm').style.display = 'none';
        this.modal.querySelector('#loadingSpinner').style.display = 'block';
        this.modal.querySelector('#successMessage').style.display = 'none';
        this.modal.querySelector('#errorMessage').style.display = 'none';
    }

    showSuccess(bookingId) {
        this.modal.querySelector('#bookingForm').style.display = 'none';
        this.modal.querySelector('#loadingSpinner').style.display = 'none';
        this.modal.querySelector('#successMessage').style.display = 'block';
        this.modal.querySelector('#bookingId').textContent = bookingId;
        this.modal.querySelector('#errorMessage').style.display = 'none';
    }

    showError(message) {
        this.modal.querySelector('#bookingForm').style.display = 'block';
        this.modal.querySelector('#loadingSpinner').style.display = 'none';
        this.modal.querySelector('#successMessage').style.display = 'none';
        this.modal.querySelector('#errorMessage').style.display = 'block';
        this.modal.querySelector('#errorText').textContent = message;
    }



    handleWhatsAppBooking() {
        const form = this.modal.querySelector('#bookingForm');
        
        // Validate required fields
        if (!this.validateForm(form)) {
            return;
        }
        
        const formData = new FormData(form);
        const selectedProgram = this.modal.querySelector('input[name="program"]:checked');
        const selectedAccommodation = this.modal.querySelector('input[name="accommodation"]:checked');
        
        // Get booking details
        const program = this.programs[selectedProgram.value];
        const amount = program.prices[selectedAccommodation.value];
        const name = formData.get('name');
        const email = formData.get('email');
        const phone = formData.get('phone');
        const checkIn = formData.get('check_in_date');
        const checkOut = formData.get('check_out_date');
        const specialRequirements = formData.get('special_requirements');
        
        // Create WhatsApp message based on program type
        let message = '';
        
        if (selectedProgram.value === 'online') {
            // Get specific class type
            let classType = 'Online Yoga at Home';
            if (typeof getCurrentClassType === 'function') {
                classType = getCurrentClassType();
            } else {
                const activeTab = document.querySelector('.class-tab.selected');
                if (activeTab) {
                    const tabId = activeTab.id;
                    if (tabId && tabId.includes('meditation')) {
                        classType = 'Online Meditation Class';
                    } else if (tabId && tabId.includes('therapeutic')) {
                        classType = 'Online Therapeutic Yoga';
                    } else if (tabId && tabId.includes('ashtanga')) {
                        classType = 'Online Ashtanga Yoga';
                    }
                }
            }
            
            // Online Yoga booking message
            message = `🧘‍♀️ *${classType} - Booking Request*\n\n`;
            message += `👤 *Name:* ${name}\n`;
            message += `📧 *Email:* ${email}\n`;
            message += `📱 *Phone:* ${phone}\n\n`;
            message += `🎯 *Class Type:* ${classType}\n`;
            message += `💻 *Membership Plan:* ${this.getAccommodationText(selectedAccommodation.value)}\n`;
            message += `💰 *Amount:* ₹${amount.toLocaleString()}\n\n`;
            if (specialRequirements) message += `📝 *Special Requirements:* ${specialRequirements}\n\n`;
            message += `Please confirm my online yoga membership and share:\n`;
            message += `• Payment details\n`;
            message += `• Class schedule\n`;
            message += `• Joining instructions\n`;
            message += `• WhatsApp group link\n\n`;
            message += `*Note:* This is for online classes only - no accommodation required.\n\n`;
            message += `Thank you! 🙏`;
        } else {
            // Retreat booking message
            message = `🧘‍♀️ *Yoga Retreat Booking Request*\n\n`;
            message += `👤 *Name:* ${name}\n`;
            message += `📧 *Email:* ${email}\n`;
            message += `📱 *Phone:* ${phone}\n\n`;
            message += `🏛️ *Program:* ${program.title}\n`;
            message += `🏠 *Accommodation:* ${this.getAccommodationText(selectedAccommodation.value)}\n`;
            message += `💰 *Amount:* ₹${amount.toLocaleString()}\n\n`;
            
            if (checkIn) message += `📅 *Check-in:* ${checkIn}\n`;
            if (checkOut) message += `📅 *Check-out:* ${checkOut}\n`;
            if (specialRequirements) message += `📝 *Special Requirements:* ${specialRequirements}\n`;
            
            message += `\n*Note:* This includes accommodation and all retreat activities.\n\n`;
            message += `Please confirm my booking and share payment details. Thank you! 🙏`;
        }
        
        // Open WhatsApp
        const whatsappNumber = '918969464548'; // Your WhatsApp number
        const whatsappUrl = `https://wa.me/${whatsappNumber}?text=${encodeURIComponent(message)}`;
        
        window.open(whatsappUrl, '_blank');
        this.closeModal();
    }
    
    getAccommodationText(accommodationType) {
        if (this.currentProgram === 'online') {
            const option = this.modal.querySelector(`input[value="${accommodationType}"]`).parentElement;
            return option.querySelector('strong').textContent;
        } else {
            const parts = accommodationType.split('_');
            return `${parts[0].charAt(0).toUpperCase() + parts[0].slice(1)} Cottage (${parts[1].charAt(0).toUpperCase() + parts[1].slice(1)} Occupancy)`;
        }
    }

    resetForm() {
        this.modal.querySelector('#bookingForm').reset();
        this.modal.querySelector('#bookingForm').style.display = 'block';
        this.modal.querySelector('#loadingSpinner').style.display = 'none';
        this.modal.querySelector('#successMessage').style.display = 'none';
        this.modal.querySelector('#errorMessage').style.display = 'none';
        
        // Reset UI selections
        this.modal.querySelectorAll('.program-option').forEach(option => {
            option.classList.remove('selected');
        });
        this.modal.querySelectorAll('.accommodation-option').forEach(option => {
            option.classList.remove('selected');
        });
        
        // Reset price summary
        this.modal.querySelector('#selectedProgram').textContent = '-';
        this.modal.querySelector('#selectedAccommodation').textContent = '-';
        this.modal.querySelector('#selectedDuration').textContent = '-';
        this.modal.querySelector('#totalAmount').textContent = '₹0';
    }
}

// Initialize booking modal when DOM is loaded
let bookingModal;
document.addEventListener('DOMContentLoaded', function() {
    try {
        bookingModal = new BookingModal();
    } catch (error) {
        console.error('Error initializing BookingModal:', error);
    }
    
    // Add click handlers to existing booking buttons (exclude navigation links)
    document.querySelectorAll('a[href*="courses.html"], a[href*="contact.html"]').forEach(link => {
        // Skip navigation menu links
        if (link.closest('nav, .navbar, .navigation, .nav-item, .sidenav')) {
            return;
        }
        
        if (link.textContent.toLowerCase().includes('book') || 
            link.textContent.toLowerCase().includes('retreat') ||
            link.textContent.toLowerCase().includes('join')) {
            
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Determine program type based on context
                let programType = null;
                const text = this.textContent.toLowerCase();
                const parentText = this.closest('.course-card, .program-card, .circle')?.textContent.toLowerCase() || '';
                
                if (text.includes('weekend') || parentText.includes('weekend')) {
                    programType = 'weekend';
                } else if (text.includes('3-day') || parentText.includes('3-day')) {
                    programType = '3day';
                } else if (text.includes('7 day') || text.includes('week') || parentText.includes('7 day')) {
                    programType = '7day';
                } else if (text.includes('online') || parentText.includes('online')) {
                    programType = 'online';
                }
                
                bookingModal.openModal(programType);
            });
        }
    });
});

// Global function to open booking modal (can be called from anywhere)
function openBookingModal(programType = null) {
    if (bookingModal) {
        bookingModal.openModal(programType);
    } else {
        // Try to initialize if not done
        setTimeout(() => {
            if (typeof BookingModal !== 'undefined') {
                bookingModal = new BookingModal();
                bookingModal.openModal(programType);
            } else {
                alert('Booking system is loading. Please try again in a moment.');
            }
        }, 100);
    }
}

// Fallback initialization for window load
window.addEventListener('load', function() {
    if (!bookingModal) {
        try {
            bookingModal = new BookingModal();
        } catch (error) {
            console.error('Fallback error initializing BookingModal:', error);
        }
    }
});