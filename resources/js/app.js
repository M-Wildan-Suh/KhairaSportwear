import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Add to existing app.js

// Smooth scroll to element
function smoothScrollTo(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        element.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }
}

// Debounce function for performance
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

// Format currency
function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(amount);
}

// Format date
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'long',
        year: 'numeric'
    });
}

// Countdown timer
function startCountdown(elementId, targetDate) {
    const countdownElement = document.getElementById(elementId);
    if (!countdownElement) return;

    const target = new Date(targetDate).getTime();

    const timer = setInterval(() => {
        const now = new Date().getTime();
        const distance = target - now;

        if (distance < 0) {
            clearInterval(timer);
            countdownElement.innerHTML = "Waktu habis!";
            return;
        }

        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        countdownElement.innerHTML = `
            <div class="d-flex gap-2 justify-content-center">
                <div class="text-center">
                    <div class="bg-primary text-white rounded p-2">${days}</div>
                    <small>Hari</small>
                </div>
                <div class="text-center">
                    <div class="bg-primary text-white rounded p-2">${hours}</div>
                    <small>Jam</small>
                </div>
                <div class="text-center">
                    <div class="bg-primary text-white rounded p-2">${minutes}</div>
                    <small>Menit</small>
                </div>
                <div class="text-center">
                    <div class="bg-primary text-white rounded p-2">${seconds}</div>
                    <small>Detik</small>
                </div>
            </div>
        `;
    }, 1000);
}

// Lazy load images
function lazyLoadImages() {
    const images = document.querySelectorAll('img[data-src]');
    
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
                observer.unobserve(img);
            }
        });
    });

    images.forEach(img => imageObserver.observe(img));
}

// Copy to clipboard
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        Toast.fire({
            icon: 'success',
            title: 'Tersalin ke clipboard!'
        });
    }).catch(err => {
        console.error('Failed to copy: ', err);
        Toast.fire({
            icon: 'error',
            title: 'Gagal menyalin!'
        });
    });
}

// Initialize tooltips
function initTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

// Initialize popovers
function initPopovers() {
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize components
    lazyLoadImages();
    initTooltips();
    initPopovers();
    
    // Add active class to current nav item
    const currentPath = window.location.pathname;
    document.querySelectorAll('.nav-link').forEach(link => {
        if (link.getAttribute('href') === currentPath) {
            link.classList.add('active');
        }
    });
    
    // Add animation to elements when they come into view
    const animatedElements = document.querySelectorAll('.animate-on-scroll');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animated');
            }
        });
    }, { threshold: 0.1 });
    
    animatedElements.forEach(el => observer.observe(el));
});

// Export functions to global scope
window.smoothScrollTo = smoothScrollTo;
window.formatCurrency = formatCurrency;
window.formatDate = formatDate;
window.startCountdown = startCountdown;
window.copyToClipboard = copyToClipboard;