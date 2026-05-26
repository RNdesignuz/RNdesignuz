/**
 * ShakarqamishMFY.uz - Main JavaScript
 * Professional frontend functionality
 */

// Initialize AOS Animation
AOS.init({
    duration: 800,
    easing: 'ease-in-out',
    once: true,
    offset: 100
});

// Document Ready
document.addEventListener('DOMContentLoaded', function() {
    
    // Back to Top Button
    const backToTop = document.getElementById('backToTop');
    
    if (backToTop) {
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                backToTop.classList.add('show');
            } else {
                backToTop.classList.remove('show');
            }
        });
        
        backToTop.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }
    
    // Navbar Scroll Effect
    const navbar = document.querySelector('.navbar');
    if (navbar) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                navbar.classList.add('shadow');
            } else {
                navbar.classList.remove('shadow');
            }
        });
    }
    
    // Form Validation
    const forms = document.querySelectorAll('.needs-validation');
    forms.forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });
    
    // Auto-hide Alerts
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
    
    // Smooth Scroll for Anchor Links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href !== '#' && document.querySelector(href)) {
                e.preventDefault();
                document.querySelector(href).scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });
    
    // Lazy Loading Images
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
    
    // Counter Animation
    const counters = document.querySelectorAll('[data-count]');
    const counterObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const counter = entry.target;
                const target = parseInt(counter.dataset.count);
                const duration = 2000;
                const increment = target / (duration / 16);
                let current = 0;
                
                const updateCounter = () => {
                    current += increment;
                    if (current < target) {
                        counter.textContent = Math.floor(current).toLocaleString();
                        requestAnimationFrame(updateCounter);
                    } else {
                        counter.textContent = target.toLocaleString();
                    }
                };
                
                updateCounter();
                observer.unobserve(counter);
            }
        });
    });
    
    counters.forEach(counter => counterObserver.observe(counter));
    
    // Search Functionality
    const searchInput = document.getElementById('searchInput');
    const searchResults = document.getElementById('searchResults');
    
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();
            
            if (query.length >= 2) {
                searchTimeout = setTimeout(() => {
                    performSearch(query);
                }, 300);
            } else if (searchResults) {
                searchResults.innerHTML = '';
                searchResults.classList.remove('show');
            }
        });
    }
    
    // Modal Image Gallery
    const galleryItems = document.querySelectorAll('.gallery-item');
    galleryItems.forEach(item => {
        item.addEventListener('click', function() {
            const img = this.querySelector('img');
            const title = this.querySelector('h5')?.textContent || '';
            
            // Create modal dynamically
            const modalHtml = `
                <div class="modal fade" id="imageModal" tabindex="-1">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">${title}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body text-center p-4">
                                <img src="${img.src}" alt="${title}" class="img-fluid rounded">
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.insertAdjacentHTML('beforeend', modalHtml);
            const modal = new bootstrap.Modal(document.getElementById('imageModal'));
            modal.show();
            
            document.getElementById('imageModal').addEventListener('hidden.bs.modal', function() {
                this.remove();
            });
        });
    });
    
    // Weather Widget (Simple Implementation)
    const weatherWidget = document.getElementById('weatherWidget');
    if (weatherWidget) {
        fetchWeatherData();
    }
    
    // Current Date and Time
    const dateTimeElement = document.getElementById('currentDateTime');
    if (dateTimeElement) {
        updateDateTime();
        setInterval(updateDateTime, 1000);
    }
    
    // Appeal Form File Preview
    const fileInput = document.getElementById('appealAttachment');
    const filePreview = document.getElementById('filePreview');
    
    if (fileInput && filePreview) {
        fileInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const fileSize = (file.size / 1024).toFixed(2);
                filePreview.innerHTML = `
                    <div class="alert alert-info d-flex align-items-center">
                        <i class="bi bi-file-earmark me-2"></i>
                        <span>${file.name} (${fileSize} KB)</span>
                    </div>
                `;
            } else {
                filePreview.innerHTML = '';
            }
        });
    }
    
});

// Search Function
function performSearch(query) {
    fetch(`/public/api/search.php?q=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            displaySearchResults(data);
        })
        .catch(error => console.error('Search error:', error));
}

function displaySearchResults(results) {
    const searchResults = document.getElementById('searchResults');
    if (!searchResults) return;
    
    if (results.length === 0) {
        searchResults.innerHTML = '<div class="p-3 text-muted">Hech narsa topilmadi</div>';
    } else {
        searchResults.innerHTML = results.map(item => `
            <a href="${item.url}" class="search-result-item d-block p-3 border-bottom">
                <h6 class="mb-1">${item.title}</h6>
                <small class="text-muted">${item.type} • ${item.date}</small>
            </a>
        `).join('');
    }
    
    searchResults.classList.add('show');
}

// Weather Data Fetcher
function fetchWeatherData() {
    // This is a placeholder - implement actual weather API
    const weatherData = {
        temp: 25,
        condition: 'Quyoshli',
        icon: 'bi-sun'
    };
    
    const widget = document.getElementById('weatherWidget');
    if (widget) {
        widget.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="bi ${weatherData.icon} fs-3 me-2"></i>
                <div>
                    <div class="fw-bold">${weatherData.temp}°C</div>
                    <small class="text-muted">${weatherData.condition}</small>
                </div>
            </div>
        `;
    }
}

// Update Date Time Display
function updateDateTime() {
    const element = document.getElementById('currentDateTime');
    if (element) {
        const now = new Date();
        const options = { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        };
        element.textContent = now.toLocaleDateString('uz-UZ', options);
    }
}

// Like Post Function
function likePost(postId) {
    fetch('/public/api/like.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ post_id: postId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const likeBtn = document.querySelector(`[data-post-id="${postId}"]`);
            if (likeBtn) {
                likeBtn.innerHTML = `<i class="bi bi-heart-fill"></i> ${data.likes}`;
            }
        }
    })
    .catch(error => console.error('Like error:', error));
}

// Share Function
function sharePost(url, title) {
    if (navigator.share) {
        navigator.share({
            title: title,
            url: url
        }).catch(error => console.error('Share error:', error));
    } else {
        // Fallback: Copy to clipboard
        navigator.clipboard.writeText(url).then(() => {
            alert('Havola nusxalandi!');
        });
    }
}

// Print Function
function printPage() {
    window.print();
}

// Export to PDF (requires html2pdf library)
function exportToPDF(elementId, filename) {
    if (typeof html2pdf !== 'undefined') {
        const element = document.getElementById(elementId);
        html2pdf().from(element).save(filename);
    } else {
        alert('PDF export funksiyasi mavjud emas');
    }
}

// Notification Permission Request
function requestNotificationPermission() {
    if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission();
    }
}

// Show Browser Notification
function showNotification(title, body, icon = '/public/assets/images/icon.png') {
    if ('Notification' in window && Notification.permission === 'granted') {
        new Notification(title, {
            body: body,
            icon: icon
        });
    }
}

// Local Storage Helper
const storage = {
    get: (key) => {
        try {
            return JSON.parse(localStorage.getItem(key));
        } catch {
            return null;
        }
    },
    set: (key, value) => {
        localStorage.setItem(key, JSON.stringify(value));
    },
    remove: (key) => {
        localStorage.removeItem(key);
    }
};

// Debounce Function
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

// Console Branding
console.log('%c ShakarqamishMFY.uz ', 'background: #0d6efd; color: white; font-size: 20px; padding: 10px;');
console.log('%c Professional Mahalla Portal System ', 'color: #6c757d; font-size: 14px;');
