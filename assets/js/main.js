/* Main JavaScript for Crew of Experts Website */

// Global variables
let currentLanguage = 'de';
let bookingCalendar = null;
let selectedDate = null;
let selectedTime = null;

// DOM Ready
document.addEventListener('DOMContentLoaded', function() {
    initializeApp();
});

// Initialize Application
function initializeApp() {
    initializeNavbar();
    initializeAnimations();
    initializeForms();
    initializeBookingSystem();
    initializeTooltips();
    initializeModals();
    initializeSearchFilters();
    initializeImageUpload();
    initializeRichTextEditor();
}

// Navbar functionality
function initializeNavbar() {
    const navbar = document.querySelector('.navbar');
    const navbarToggler = document.querySelector('.navbar-toggler');
    const navbarCollapse = document.querySelector('.navbar-collapse');
    
    // Navbar scroll effect
    window.addEventListener('scroll', function() {
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });
    
    // Close mobile menu when clicking on links
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', function() {
            if (navbarCollapse.classList.contains('show')) {
                navbarToggler.click();
            }
        });
    });
}

// Initialize animations
function initializeAnimations() {
    // Intersection Observer for scroll animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-in');
            }
        });
    }, observerOptions);
    
    // Observe elements for animation
    document.querySelectorAll('.service-card, .process-step, .job-card').forEach(el => {
        observer.observe(el);
    });
}

// Form handling
function initializeForms() {
    // Contact form
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        contactForm.addEventListener('submit', handleContactSubmit);
    }
    
    // Booking form
    const bookingForm = document.getElementById('bookingForm');
    if (bookingForm) {
        bookingForm.addEventListener('submit', handleBookingSubmit);
    }
    
    // Newsletter form
    const newsletterForm = document.getElementById('newsletterForm');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', handleNewsletterSubmit);
    }
    
    // Job application form
    const applicationForm = document.getElementById('applicationForm');
    if (applicationForm) {
        applicationForm.addEventListener('submit', handleApplicationSubmit);
    }
    
    // Form validation
    document.querySelectorAll('.needs-validation').forEach(form => {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });
}

// Booking system
function initializeBookingSystem() {
    const bookingContainer = document.getElementById('bookingCalendar');
    if (bookingContainer) {
        loadBookingCalendar();
    }
}

function loadBookingCalendar() {
    const today = new Date();
    const currentMonth = today.getMonth();
    const currentYear = today.getFullYear();
    
    generateCalendar(currentYear, currentMonth);
    loadAvailableTimeSlots();
}

function generateCalendar(year, month) {
    const calendarContainer = document.getElementById('bookingCalendar');
    const monthNames = [
        'Januar', 'Februar', 'März', 'April', 'Mai', 'Juni',
        'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember'
    ];
    const dayNames = ['Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa', 'So'];
    
    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);
    const daysInMonth = lastDay.getDate();
    const startingDayOfWeek = (firstDay.getDay() + 6) % 7; // Monday = 0
    
    let calendarHTML = `
        <div class="booking-calendar">
            <div class="calendar-header p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <button type="button" class="btn btn-sm btn-outline-light" onclick="changeMonth(-1)">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <h5 class="mb-0">${monthNames[month]} ${year}</h5>
                    <button type="button" class="btn btn-sm btn-outline-light" onclick="changeMonth(1)">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
            <div class="calendar-body p-3">
                <div class="row text-center mb-2">
    `;
    
    // Day headers
    dayNames.forEach(day => {
        calendarHTML += `<div class="col fw-bold text-muted">${day}</div>`;
    });
    
    calendarHTML += '</div>';
    
    // Calendar days
    let dayCount = 1;
    for (let week = 0; week < 6; week++) {
        calendarHTML += '<div class="row mb-1">';
        
        for (let day = 0; day < 7; day++) {
            const cellIndex = week * 7 + day;
            
            if (cellIndex < startingDayOfWeek || dayCount > daysInMonth) {
                calendarHTML += '<div class="col"></div>';
            } else {
                const currentDate = new Date(year, month, dayCount);
                const today = new Date();
                const isToday = currentDate.toDateString() === today.toDateString();
                const isPast = currentDate < today;
                const isWeekend = day >= 5; // Saturday = 5, Sunday = 6
                
                let classes = 'calendar-day p-2 text-center rounded';
                let clickable = true;
                
                if (isPast) {
                    classes += ' disabled';
                    clickable = false;
                }
                
                if (isWeekend) {
                    classes += ' disabled';
                    clickable = false;
                }
                
                if (isToday && !isPast && !isWeekend) {
                    classes += ' border-primary';
                }
                
                const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(dayCount).padStart(2, '0')}`;
                const clickHandler = clickable ? `onclick="selectDate('${dateStr}')"` : '';
                
                calendarHTML += `
                    <div class="col">
                        <div class="${classes}" ${clickHandler} data-date="${dateStr}">
                            ${dayCount}
                        </div>
                    </div>
                `;
                dayCount++;
            }
        }
        
        calendarHTML += '</div>';
        
        if (dayCount > daysInMonth) break;
    }
    
    calendarHTML += `
            </div>
        </div>
        <div id="timeSlots" class="mt-4" style="display: none;">
            <h6 class="fw-bold mb-3">Verfügbare Zeiten:</h6>
            <div id="timeSlotsContainer" class="row g-2"></div>
        </div>
    `;
    
    calendarContainer.innerHTML = calendarHTML;
}

function changeMonth(direction) {
    const today = new Date();
    let month = parseInt(document.querySelector('.calendar-header h5').textContent.split(' ')[1]);
    let year = parseInt(document.querySelector('.calendar-header h5').textContent.split(' ')[0]);
    
    // Convert month name to number
    const monthNames = [
        'Januar', 'Februar', 'März', 'April', 'Mai', 'Juni',
        'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember'
    ];
    month = monthNames.indexOf(document.querySelector('.calendar-header h5').textContent.split(' ')[0]);
    
    month += direction;
    
    if (month < 0) {
        month = 11;
        year--;
    } else if (month > 11) {
        month = 0;
        year++;
    }
    
    // Don't allow going to past months
    if (year < today.getFullYear() || (year === today.getFullYear() && month < today.getMonth())) {
        return;
    }
    
    generateCalendar(year, month);
}

function selectDate(dateStr) {
    // Remove previous selection
    document.querySelectorAll('.calendar-day.selected').forEach(el => {
        el.classList.remove('selected');
    });
    
    // Add selection to clicked date
    const selectedElement = document.querySelector(`[data-date="${dateStr}"]`);
    if (selectedElement) {
        selectedElement.classList.add('selected');
        selectedDate = dateStr;
        loadTimeSlots(dateStr);
    }
}

function loadTimeSlots(date) {
    const timeSlotsContainer = document.getElementById('timeSlotsContainer');
    const timeSlotsSection = document.getElementById('timeSlots');
    
    // Show loading
    timeSlotsContainer.innerHTML = '<div class="col-12 text-center"><div class="loading-spinner"></div></div>';
    timeSlotsSection.style.display = 'block';
    
    // Simulate API call to get available time slots
    setTimeout(() => {
        const timeSlots = generateTimeSlots(date);
        renderTimeSlots(timeSlots);
    }, 500);
}

function generateTimeSlots(date) {
    const slots = [];
    const startHour = 9;
    const endHour = 17;
    const slotDuration = 30; // minutes
    
    for (let hour = startHour; hour < endHour; hour++) {
        for (let minute = 0; minute < 60; minute += slotDuration) {
            const timeStr = `${String(hour).padStart(2, '0')}:${String(minute).padStart(2, '0')}`;
            
            // Simulate some slots being unavailable
            const isAvailable = Math.random() > 0.3;
            
            slots.push({
                time: timeStr,
                available: isAvailable
            });
        }
    }
    
    return slots;
}

function renderTimeSlots(slots) {
    const timeSlotsContainer = document.getElementById('timeSlotsContainer');
    
    let slotsHTML = '';
    
    slots.forEach(slot => {
        const classes = slot.available ? 'time-slot btn btn-outline-primary' : 'time-slot btn btn-outline-secondary unavailable';
        const clickHandler = slot.available ? `onclick="selectTime('${slot.time}')"` : '';
        const disabled = slot.available ? '' : 'disabled';
        
        slotsHTML += `
            <div class="col-6 col-md-3">
                <button type="button" class="${classes}" ${clickHandler} ${disabled} data-time="${slot.time}">
                    ${slot.time}
                </button>
            </div>
        `;
    });
    
    timeSlotsContainer.innerHTML = slotsHTML;
}

function selectTime(time) {
    // Remove previous selection
    document.querySelectorAll('.time-slot.selected').forEach(el => {
        el.classList.remove('selected');
    });
    
    // Add selection to clicked time
    const selectedElement = document.querySelector(`[data-time="${time}"]`);
    if (selectedElement) {
        selectedElement.classList.add('selected');
        selectedTime = time;
        
        // Update hidden form fields
        const dateInput = document.querySelector('input[name="booking_date"]');
        const timeInput = document.querySelector('input[name="booking_time"]');
        
        if (dateInput) dateInput.value = selectedDate;
        if (timeInput) timeInput.value = selectedTime;
        
        // Show booking form
        const bookingFormSection = document.getElementById('bookingFormSection');
        if (bookingFormSection) {
            bookingFormSection.style.display = 'block';
            bookingFormSection.scrollIntoView({ behavior: 'smooth' });
        }
    }
}

// Form submission handlers
async function handleContactSubmit(event) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    
    try {
        showLoading(form);
        
        const response = await fetch('api/contact.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showNotification('Ihre Nachricht wurde erfolgreich gesendet!', 'success');
            form.reset();
        } else {
            showNotification(result.message || 'Ein Fehler ist aufgetreten.', 'error');
        }
    } catch (error) {
        showNotification('Ein Fehler ist aufgetreten. Bitte versuchen Sie es später erneut.', 'error');
    } finally {
        hideLoading(form);
    }
}

async function handleBookingSubmit(event) {
    event.preventDefault();
    
    if (!selectedDate || !selectedTime) {
        showNotification('Bitte wählen Sie ein Datum und eine Uhrzeit aus.', 'warning');
        return;
    }
    
    const form = event.target;
    const formData = new FormData(form);
    
    // Add selected date and time
    formData.append('booking_date', selectedDate);
    formData.append('booking_time', selectedTime);
    
    try {
        showLoading(form);
        
        const response = await fetch('api/booking.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showNotification('Ihr Termin wurde erfolgreich gebucht!', 'success');
            form.reset();
            resetBookingSelection();
        } else {
            showNotification(result.message || 'Ein Fehler ist aufgetreten.', 'error');
        }
    } catch (error) {
        showNotification('Ein Fehler ist aufgetreten. Bitte versuchen Sie es später erneut.', 'error');
    } finally {
        hideLoading(form);
    }
}

async function handleNewsletterSubmit(event) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    
    try {
        showLoading(form);
        
        const response = await fetch('api/newsletter.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showNotification('Sie wurden erfolgreich für unseren Newsletter angemeldet!', 'success');
            form.reset();
        } else {
            showNotification(result.message || 'Ein Fehler ist aufgetreten.', 'error');
        }
    } catch (error) {
        showNotification('Ein Fehler ist aufgetreten. Bitte versuchen Sie es später erneut.', 'error');
    } finally {
        hideLoading(form);
    }
}

async function handleApplicationSubmit(event) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    
    try {
        showLoading(form);
        
        const response = await fetch('api/application.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showNotification('Ihre Bewerbung wurde erfolgreich gesendet!', 'success');
            form.reset();
        } else {
            showNotification(result.message || 'Ein Fehler ist aufgetreten.', 'error');
        }
    } catch (error) {
        showNotification('Ein Fehler ist aufgetreten. Bitte versuchen Sie es später erneut.', 'error');
    } finally {
        hideLoading(form);
    }
}

// Utility functions
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = `
        <div class="d-flex justify-content-between align-items-center">
            <span>${message}</span>
            <button type="button" class="btn-close btn-close-white" onclick="this.parentElement.parentElement.remove()"></button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Show notification
    setTimeout(() => {
        notification.classList.add('show');
    }, 100);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 300);
    }, 5000);
}

function showLoading(form) {
    const submitBtn = form.querySelector('button[type="submit"]');
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="loading-spinner me-2"></span>Wird gesendet...';
    }
}

function hideLoading(form) {
    const submitBtn = form.querySelector('button[type="submit"]');
    if (submitBtn) {
        submitBtn.disabled = false;
        // Restore original text based on button context
        const originalTexts = {
            'contactForm': '<i class="fas fa-paper-plane me-2"></i>Nachricht senden',
            'bookingForm': '<i class="fas fa-calendar-check me-2"></i>Termin buchen',
            'newsletterForm': '<i class="fas fa-envelope me-2"></i>Anmelden',
            'applicationForm': '<i class="fas fa-file-upload me-2"></i>Bewerbung senden'
        };
        
        const formId = form.getAttribute('id');
        submitBtn.innerHTML = originalTexts[formId] || 'Senden';
    }
}

function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function validatePhone(phone) {
    const phoneRegex = /^[\+]?[1-9][\d]{0,15}$/;
    return phoneRegex.test(phone.replace(/[\s\-\(\)]/g, ''));
}

function resetBookingSelection() {
    selectedDate = null;
    selectedTime = null;
    
    document.querySelectorAll('.calendar-day.selected, .time-slot.selected').forEach(el => {
        el.classList.remove('selected');
    });
    
    const timeSlotsSection = document.getElementById('timeSlots');
    const bookingFormSection = document.getElementById('bookingFormSection');
    
    if (timeSlotsSection) timeSlotsSection.style.display = 'none';
    if (bookingFormSection) bookingFormSection.style.display = 'none';
}

// Initialize tooltips
function initializeTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

// Initialize modals
function initializeModals() {
    // Job details modal
    const jobModals = document.querySelectorAll('.job-details-modal');
    jobModals.forEach(modal => {
        modal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const jobId = button.getAttribute('data-job-id');
            loadJobDetails(jobId, modal);
        });
    });
}

async function loadJobDetails(jobId, modal) {
    const modalBody = modal.querySelector('.modal-body');
    
    try {
        modalBody.innerHTML = '<div class="text-center"><div class="loading-spinner"></div></div>';
        
        const response = await fetch(`api/job-details.php?id=${jobId}`);
        const result = await response.json();
        
        if (result.success) {
            renderJobDetails(result.job, modalBody);
        } else {
            modalBody.innerHTML = '<div class="alert alert-danger">Fehler beim Laden der Stellendetails.</div>';
        }
    } catch (error) {
        modalBody.innerHTML = '<div class="alert alert-danger">Ein Fehler ist aufgetreten.</div>';
    }
}

function renderJobDetails(job, container) {
    const jobTypes = {
        'full_time': 'Vollzeit',
        'part_time': 'Teilzeit',
        'contract': 'Befristet',
        'internship': 'Praktikum'
    };
    
    container.innerHTML = `
        <div class="job-details">
            <div class="row mb-4">
                <div class="col-md-8">
                    <h4 class="fw-bold">${job.title}</h4>
                    ${job.company ? `<p class="text-muted mb-1"><i class="fas fa-building me-2"></i>${job.company}</p>` : ''}
                    ${job.location ? `<p class="text-muted mb-1"><i class="fas fa-map-marker-alt me-2"></i>${job.location}</p>` : ''}
                    ${job.job_type ? `<p class="text-muted mb-1"><i class="fas fa-clock me-2"></i>${jobTypes[job.job_type] || job.job_type}</p>` : ''}
                    ${job.salary_range ? `<p class="text-primary fw-bold"><i class="fas fa-euro-sign me-2"></i>${job.salary_range}</p>` : ''}
                </div>
                <div class="col-md-4 text-end">
                    ${job.featured ? '<span class="badge bg-warning text-dark mb-2"><i class="fas fa-star me-1"></i>Featured</span><br>' : ''}
                    <small class="text-muted">Veröffentlicht: ${formatDate(job.created_at)}</small>
                </div>
            </div>
            
            ${job.description ? `
                <div class="mb-4">
                    <h6 class="fw-bold">Stellenbeschreibung</h6>
                    <div class="job-description">${job.description}</div>
                </div>
            ` : ''}
            
            ${job.requirements ? `
                <div class="mb-4">
                    <h6 class="fw-bold">Anforderungen</h6>
                    <div class="job-requirements">${job.requirements}</div>
                </div>
            ` : ''}
            
            ${job.benefits ? `
                <div class="mb-4">
                    <h6 class="fw-bold">Was wir bieten</h6>
                    <div class="job-benefits">${job.benefits}</div>
                </div>
            ` : ''}
            
            <div class="text-center mt-4">
                <button type="button" class="btn btn-primary btn-lg" onclick="openApplicationModal('${job.id}')">
                    <i class="fas fa-file-upload me-2"></i>Jetzt bewerben
                </button>
            </div>
        </div>
    `;
}

// Search and filter functionality
function initializeSearchFilters() {
    const searchInput = document.getElementById('jobSearch');
    const locationFilter = document.getElementById('locationFilter');
    const typeFilter = document.getElementById('typeFilter');
    
    if (searchInput || locationFilter || typeFilter) {
        [searchInput, locationFilter, typeFilter].forEach(element => {
            if (element) {
                element.addEventListener('input', debounce(filterJobs, 300));
                element.addEventListener('change', filterJobs);
            }
        });
    }
}

function filterJobs() {
    const searchTerm = document.getElementById('jobSearch')?.value.toLowerCase() || '';
    const locationFilter = document.getElementById('locationFilter')?.value || '';
    const typeFilter = document.getElementById('typeFilter')?.value || '';
    
    const jobCards = document.querySelectorAll('.job-card');
    
    jobCards.forEach(card => {
        const title = card.querySelector('h3')?.textContent.toLowerCase() || '';
        const company = card.querySelector('.company')?.textContent.toLowerCase() || '';
        const location = card.querySelector('.location')?.textContent || '';
        const type = card.dataset.jobType || '';
        
        const matchesSearch = !searchTerm || title.includes(searchTerm) || company.includes(searchTerm);
        const matchesLocation = !locationFilter || location.includes(locationFilter);
        const matchesType = !typeFilter || type === typeFilter;
        
        if (matchesSearch && matchesLocation && matchesType) {
            card.style.display = 'block';
            card.classList.add('fade-in');
        } else {
            card.style.display = 'none';
            card.classList.remove('fade-in');
        }
    });
    
    // Update results count
    const visibleJobs = document.querySelectorAll('.job-card[style*="block"]').length;
    const resultsCount = document.getElementById('resultsCount');
    if (resultsCount) {
        resultsCount.textContent = `${visibleJobs} Stelle${visibleJobs !== 1 ? 'n' : ''} gefunden`;
    }
}

// Image upload functionality
function initializeImageUpload() {
    const fileInputs = document.querySelectorAll('input[type="file"]');
    
    fileInputs.forEach(input => {
        input.addEventListener('change', handleFileUpload);
        
        // Drag and drop functionality
        const dropArea = input.closest('.file-upload-area');
        if (dropArea) {
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropArea.addEventListener(eventName, preventDefaults, false);
            });
            
            ['dragenter', 'dragover'].forEach(eventName => {
                dropArea.addEventListener(eventName, () => dropArea.classList.add('dragover'), false);
            });
            
            ['dragleave', 'drop'].forEach(eventName => {
                dropArea.addEventListener(eventName, () => dropArea.classList.remove('dragover'), false);
            });
            
            dropArea.addEventListener('drop', handleDrop, false);
        }
    });
}

function handleFileUpload(event) {
    const file = event.target.files[0];
    if (file) {
        validateAndPreviewFile(file, event.target);
    }
}

function handleDrop(event) {
    const files = event.dataTransfer.files;
    const input = event.target.querySelector('input[type="file"]');
    
    if (files.length > 0 && input) {
        input.files = files;
        validateAndPreviewFile(files[0], input);
    }
}

function validateAndPreviewFile(file, input) {
    const maxSize = 5 * 1024 * 1024; // 5MB
    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
    
    if (file.size > maxSize) {
        showNotification('Die Datei ist zu groß. Maximale Größe: 5MB', 'error');
        input.value = '';
        return;
    }
    
    if (!allowedTypes.includes(file.type)) {
        showNotification('Dateityp nicht unterstützt. Erlaubt: JPG, PNG, GIF, PDF, DOC, DOCX', 'error');
        input.value = '';
        return;
    }
    
    // Show preview for images
    if (file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = function(e) {
            showImagePreview(e.target.result, input);
        };
        reader.readAsDataURL(file);
    } else {
        showFileInfo(file, input);
    }
}

function showImagePreview(src, input) {
    const previewContainer = input.closest('.form-group')?.querySelector('.image-preview-container');
    if (previewContainer) {
        previewContainer.innerHTML = `
            <div class="image-preview-wrapper">
                <img src="${src}" alt="Preview" class="image-preview">
                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0" onclick="removeFilePreview(this)">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
    }
}

function showFileInfo(file, input) {
    const previewContainer = input.closest('.form-group')?.querySelector('.file-preview-container');
    if (previewContainer) {
        const fileSize = (file.size / 1024 / 1024).toFixed(2);
        previewContainer.innerHTML = `
            <div class="file-info d-flex align-items-center justify-content-between p-3 bg-light rounded">
                <div>
                    <i class="fas fa-file me-2"></i>
                    <span class="fw-bold">${file.name}</span>
                    <small class="text-muted d-block">${fileSize} MB</small>
                </div>
                <button type="button" class="btn btn-sm btn-danger" onclick="removeFilePreview(this)">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
    }
}

function removeFilePreview(button) {
    const container = button.closest('.image-preview-container, .file-preview-container');
    const input = container?.closest('.form-group')?.querySelector('input[type="file"]');
    
    if (container) container.innerHTML = '';
    if (input) input.value = '';
}

// Rich text editor
function initializeRichTextEditor() {
    const editors = document.querySelectorAll('.rich-text-editor');
    
    editors.forEach(editor => {
        createSimpleEditor(editor);
    });
}

function createSimpleEditor(textarea) {
    const toolbar = document.createElement('div');
    toolbar.className = 'editor-toolbar';
    toolbar.innerHTML = `
        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatText('bold')">
            <i class="fas fa-bold"></i>
        </button>
        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatText('italic')">
            <i class="fas fa-italic"></i>
        </button>
        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatText('underline')">
            <i class="fas fa-underline"></i>
        </button>
        <div class="btn-group">
            <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                <i class="fas fa-heading"></i>
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#" onclick="formatText('formatBlock', 'h1')">Überschrift 1</a></li>
                <li><a class="dropdown-item" href="#" onclick="formatText('formatBlock', 'h2')">Überschrift 2</a></li>
                <li><a class="dropdown-item" href="#" onclick="formatText('formatBlock', 'h3')">Überschrift 3</a></li>
                <li><a class="dropdown-item" href="#" onclick="formatText('formatBlock', 'p')">Normal</a></li>
            </ul>
        </div>
        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatText('insertUnorderedList')">
            <i class="fas fa-list-ul"></i>
        </button>
        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatText('insertOrderedList')">
            <i class="fas fa-list-ol"></i>
        </button>
    `;
    
    const editorContent = document.createElement('div');
    editorContent.className = 'editor-content';
    editorContent.contentEditable = true;
    editorContent.innerHTML = textarea.value;
    
    // Replace textarea with editor
    textarea.style.display = 'none';
    textarea.parentNode.insertBefore(toolbar, textarea);
    textarea.parentNode.insertBefore(editorContent, textarea.nextSibling);
    
    // Sync content back to textarea
    editorContent.addEventListener('input', function() {
        textarea.value = this.innerHTML;
    });
}

function formatText(command, value = null) {
    document.execCommand(command, false, value);
}

// Utility functions
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('de-DE', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

// Application modal
function openApplicationModal(jobId) {
    const modal = new bootstrap.Modal(document.getElementById('applicationModal'));
    document.getElementById('applicationJobId').value = jobId;
    modal.show();
}

// Export functions for global access
window.selectDate = selectDate;
window.selectTime = selectTime;
window.changeMonth = changeMonth;
window.formatText = formatText;
window.removeFilePreview = removeFilePreview;
window.openApplicationModal = openApplicationModal;
window.validateEmail = validateEmail;
window.showNotification = showNotification;