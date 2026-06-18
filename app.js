// Global Toast Notification System
window.showToast = function(message, type = 'success') {
    let toastContainer = document.getElementById('toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        toastContainer.className = 'fixed top-10 left-1/2 transform -translate-x-1/2 z-[10000] flex flex-col gap-3 pointer-events-none';
        document.body.appendChild(toastContainer);
    }
    
    const toast = document.createElement('div');
    const icons = {
        'success': '<i data-lucide="check-circle" class="w-5 h-5 mr-3 text-white"></i>',
        'error': '<i data-lucide="alert-circle" class="w-5 h-5 mr-3 text-white"></i>',
        'info': '<i data-lucide="info" class="w-5 h-5 mr-3 text-white"></i>'
    };
    const bgs = {
        'success': 'bg-green-600/90 shadow-green-500/20',
        'error': 'bg-red-500/90 shadow-red-500/20',
        'info': 'bg-amber-600/90 shadow-amber-500/20'
    };
    
    toast.className = `${bgs[type]} backdrop-blur-md text-white px-6 py-4 rounded-2xl shadow-2xl flex items-center justify-center transform transition-all duration-500 scale-90 opacity-0 pointer-events-auto border border-white/10`;
    toast.innerHTML = `${icons[type] || ''}<span class="font-medium tracking-wide">${message}</span>`;
    
    toastContainer.appendChild(toast);
    if (window.lucide) lucide.createIcons();
    
    // Animate In
    setTimeout(() => {
        toast.classList.remove('scale-90', 'opacity-0');
        toast.classList.add('scale-100', 'opacity-100');
    }, 10);
    
    // Animate Out & Remove
    setTimeout(() => {
        toast.classList.remove('scale-100', 'opacity-100');
        toast.classList.add('scale-90', 'opacity-0');
        setTimeout(() => toast.remove(), 500);
    }, 4000);
};

// Simple HTML Escaper for XSS protection
window.escapeHTML = function(str) {
    if (!str) return '';
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
};

// Global API Fetch wrapper (Automatically sets rigorous CGI Auth Headers)
window.apiFetch = async function(url, options = {}) {
    const token = localStorage.getItem('token');
    const headers = {
        'Content-Type': 'application/json',
        ...(options.headers || {})
    };
    if (token) {
        headers['Authorization'] = 'Bearer ' + token;
        headers['X-Auth-Token'] = token; // Fallback for strict Apache configs
    }
    
    return fetch(url, { ...options, headers });
};
