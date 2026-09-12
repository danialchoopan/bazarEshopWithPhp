/**
 * Main JavaScript Application
 * 
 * Handles cart management, UI interactions, and API calls.
 */

// ============================================
// Configuration
// ============================================
const APP_CONFIG = {
    apiBaseUrl: '/api',
    storageKeys: {
        cart: 'bazar_cart',
        darkMode: 'bazar_dark_mode'
    }
};

// ============================================
// Toast Notification System
// ============================================
class ToastManager {
    constructor() {
        this.container = null;
        this.init();
    }

    init() {
        this.container = document.createElement('div');
        this.container.className = 'toast-container';
        this.container.style.cssText = `
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
            max-width: 400px;
            width: 90%;
        `;
        document.body.appendChild(this.container);
    }

    show(message, type = 'info', duration = 3000) {
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        
        const icons = {
            success: '✓',
            error: '✕',
            warning: '⚠',
            info: 'ℹ'
        };

        const colors = {
            success: '#10b981',
            error: '#ef4444',
            warning: '#f59e0b',
            info: '#6366f1'
        };

        toast.style.cssText = `
            background: white;
            padding: 1rem 1.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            gap: 12px;
            animation: slideInDown 0.3s ease-out;
            border-right: 4px solid ${colors[type]};
            direction: rtl;
        `;

        toast.innerHTML = `
            <span style="font-size: 1.2rem; color: ${colors[type]}">${icons[type]}</span>
            <span style="flex: 1; font-size: 0.9rem;">${message}</span>
        `;

        this.container.appendChild(toast);

        setTimeout(() => {
            toast.style.animation = 'fadeInUp 0.3s ease-out reverse';
            setTimeout(() => toast.remove(), 300);
        }, duration);
    }

    success(message) { this.show(message, 'success'); }
    error(message) { this.show(message, 'error'); }
    warning(message) { this.show(message, 'warning'); }
    info(message) { this.show(message, 'info'); }
}

// Initialize global toast manager
window.toast = new ToastManager();

// ============================================
// Shopping Cart Manager
// ============================================
class CartManager {
    constructor() {
        this.items = this.loadCart();
        this.updateCartUI();
    }

    loadCart() {
        try {
            const cart = localStorage.getItem(APP_CONFIG.storageKeys.cart);
            return cart ? JSON.parse(cart) : [];
        } catch (e) {
            console.error('Failed to load cart:', e);
            return [];
        }
    }

    saveCart() {
        try {
            localStorage.setItem(APP_CONFIG.storageKeys.cart, JSON.stringify(this.items));
            this.updateCartUI();
        } catch (e) {
            console.error('Failed to save cart:', e);
            window.toast?.error('خطا در ذخیره سبد خرید');
        }
    }

    addItem(product) {
        const existingItem = this.items.find(item => item.id === product.id);
        
        if (existingItem) {
            existingItem.quantity += 1;
            window.toast?.success('تعداد محصول افزایش یافت');
        } else {
            this.items.push({
                id: product.id,
                name: product.name,
                price: product.price,
                image: product.image || '/placeholder.jpg',
                quantity: 1
            });
            window.toast?.success('محصول به سبد خرید اضافه شد');
        }

        this.saveCart();
    }

    removeItem(productId) {
        this.items = this.items.filter(item => item.id !== productId);
        this.saveCart();
        window.toast?.info('محصول از سبد خرید حذف شد');
    }

    updateQuantity(productId, quantity) {
        const item = this.items.find(item => item.id === productId);
        
        if (item) {
            if (quantity <= 0) {
                this.removeItem(productId);
            } else {
                item.quantity = quantity;
                this.saveCart();
            }
        }
    }

    getItems() {
        return this.items;
    }

    getCount() {
        return this.items.reduce((sum, item) => sum + item.quantity, 0);
    }

    getTotal() {
        return this.items.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    }

    clear() {
        this.items = [];
        this.saveCart();
    }

    updateCartUI() {
        // Update cart count badge
        const countElements = document.querySelectorAll('.cart-count');
        const count = this.getCount();
        
        countElements.forEach(el => {
            el.textContent = count;
            el.style.display = count > 0 ? 'inline-block' : 'none';
        });

        // Update cart total if element exists
        const totalElement = document.getElementById('cart-total');
        if (totalElement) {
            totalElement.textContent = this.getTotal().toLocaleString('fa-IR') + ' تومان';
        }

        // Dispatch event for other components
        window.dispatchEvent(new CustomEvent('cart:updated', { detail: { count: this.getTotal, items: this.items } }));
    }
}

// Initialize global cart manager
window.cart = new CartManager();

// ============================================
// Dark Mode Toggle
// ============================================
class DarkModeManager {
    constructor() {
        this.isDark = this.loadPreference();
        this.init();
    }

    loadPreference() {
        const saved = localStorage.getItem(APP_CONFIG.storageKeys.darkMode);
        return saved ? JSON.parse(saved) : false;
    }

    savePreference() {
        localStorage.setItem(APP_CONFIG.storageKeys.darkMode, JSON.stringify(this.isDark));
    }

    init() {
        if (this.isDark) {
            document.body.classList.add('dark-mode');
        }

        // Add toggle button if not exists
        this.createToggleButton();
    }

    createToggleButton() {
        let toggle = document.getElementById('dark-mode-toggle');
        
        if (!toggle) {
            toggle = document.createElement('button');
            toggle.id = 'dark-mode-toggle';
            toggle.innerHTML = this.isDark ? '☀️' : '🌙';
            toggle.style.cssText = `
                position: fixed;
                bottom: 20px;
                left: 20px;
                width: 50px;
                height: 50px;
                border-radius: 50%;
                border: none;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                font-size: 1.5rem;
                cursor: pointer;
                box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                z-index: 1000;
                transition: transform 0.3s ease;
            `;
            
            toggle.addEventListener('click', () => this.toggle());
            document.body.appendChild(toggle);
        }
    }

    toggle() {
        this.isDark = !this.isDark;
        
        if (this.isDark) {
            document.body.classList.add('dark-mode');
        } else {
            document.body.classList.remove('dark-mode');
        }

        const toggle = document.getElementById('dark-mode-toggle');
        if (toggle) {
            toggle.innerHTML = this.isDark ? '☀️' : '🌙';
            toggle.style.transform = 'scale(0.9)';
            setTimeout(() => toggle.style.transform = 'scale(1)', 150);
        }

        this.savePreference();
        window.toast?.info(this.isDark ? 'حالت شب فعال شد' : 'حالت روز فعال شد');
    }
}

// Initialize dark mode
window.darkMode = new DarkModeManager();

// ============================================
// Search with Debounce
// ============================================
class SearchManager {
    constructor() {
        this.debounceTimer = null;
        this.init();
    }

    init() {
        const searchInput = document.getElementById('search-input');
        const searchResults = document.getElementById('search-results');

        if (searchInput && searchResults) {
            searchInput.addEventListener('input', (e) => {
                clearTimeout(this.debounceTimer);
                
                this.debounceTimer = setTimeout(() => {
                    this.search(e.target.value);
                }, 300);
            });

            // Close results when clicking outside
            document.addEventListener('click', (e) => {
                if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                    searchResults.style.display = 'none';
                }
            });
        }
    }

    async search(query) {
        if (query.length < 2) {
            document.getElementById('search-results').style.display = 'none';
            return;
        }

        try {
            const response = await fetch(`${APP_CONFIG.apiBaseUrl}/search.php?q=${encodeURIComponent(query)}`);
            const products = await response.json();

            this.displayResults(products);
        } catch (e) {
            console.error('Search failed:', e);
        }
    }

    displayResults(products) {
        const container = document.getElementById('search-results');
        
        if (!products || products.length === 0) {
            container.style.display = 'none';
            return;
        }

        container.innerHTML = products.map(product => `
            <div class="search-result-item" data-product-id="${product.id}" style="
                padding: 10px;
                display: flex;
                align-items: center;
                gap: 10px;
                cursor: pointer;
                border-bottom: 1px solid #eee;
            ">
                <img src="${product.image || '/placeholder.jpg'}" alt="${product.name}" 
                     style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                <div>
                    <div style="font-weight: bold;">${product.name}</div>
                    <div style="color: #667eea;">${product.price.toLocaleString('fa-IR')} تومان</div>
                </div>
            </div>
        `).join('');

        container.style.display = 'block';

        // Add click handlers
        container.querySelectorAll('.search-result-item').forEach(item => {
            item.addEventListener('click', () => {
                const productId = item.dataset.productId;
                window.location.href = `/product.php?id=${productId}`;
            });
        });
    }
}

// Initialize search
window.searchManager = new SearchManager();

// ============================================
// Scroll Animations (Intersection Observer)
// ============================================
document.addEventListener('DOMContentLoaded', () => {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Observe elements with fade-in class
    document.querySelectorAll('.fade-in').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(el);
    });
});

// ============================================
// Form Validation Helper
// ============================================
function validateForm(formId, rules) {
    const form = document.getElementById(formId);
    if (!form) return false;

    let isValid = true;

    Object.keys(rules).forEach(field => {
        const input = form.querySelector(`[name="${field}"]`);
        if (!input) return;

        const value = input.value.trim();
        const ruleSet = rules[field].split('|');
        let errorMessage = '';

        ruleSet.forEach(rule => {
            const [ruleName, param] = rule.split(':');

            switch (ruleName) {
                case 'required':
                    if (!value) {
                        errorMessage = 'این فیلد الزامی است';
                    }
                    break;
                case 'email':
                    if (value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                        errorMessage = 'ایمیل نامعتبر است';
                    }
                    break;
                case 'min':
                    if (value && value.length < parseInt(param)) {
                        errorMessage = `حداقل ${param} کاراکتر لازم است`;
                    }
                    break;
                case 'numeric':
                    if (value && isNaN(value)) {
                        errorMessage = 'فقط عدد مجاز است';
                    }
                    break;
            }
        });

        if (errorMessage) {
            isValid = false;
            input.style.borderColor = '#ef4444';
            
            // Show error message
            let errorEl = input.parentElement.querySelector('.error-message');
            if (!errorEl) {
                errorEl = document.createElement('div');
                errorEl.className = 'error-message';
                errorEl.style.color = '#ef4444';
                errorEl.style.fontSize = '0.85rem';
                errorEl.style.marginTop = '4px';
                input.parentElement.appendChild(errorEl);
            }
            errorEl.textContent = errorMessage;

            input.addEventListener('input', () => {
                input.style.borderColor = '';
                if (errorEl) errorEl.textContent = '';
            }, { once: true });
        }
    });

    return isValid;
}

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { ToastManager, CartManager, DarkModeManager, SearchManager };
}
