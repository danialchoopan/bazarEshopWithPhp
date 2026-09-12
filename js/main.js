/**
 * Modern UI JavaScript for Bazar Shop
 * 
 * This file contains all interactive UI features including:
 * - Smooth scrolling animations
 * - Cart management with localStorage
 * - Product quick view modal
 * - Search with live results
 * - Admin dashboard charts
 * - Payment gateway integration
 * 
 * @version 3.0
 * @author Bazar Shop Team
 */

(function() {
    'use strict';

    // ========================================================================
    // CONFIGURATION
    // ========================================================================
    
    const CONFIG = {
        animationDuration: 300,
        scrollThreshold: 100,
        cartStorageKey: 'bazar_cart',
        apiEndpoint: '/api/',
        zarinPalGateway: 'https://www.zarinpal.com/pg/startPay/'
    };

    // ========================================================================
    // UTILITY FUNCTIONS
    // ========================================================================
    
    /**
     * Debounce function to limit rapid function calls
     * @param {Function} func - Function to debounce
     * @param {number} wait - Wait time in milliseconds
     * @returns {Function} Debounced function
     */
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

    /**
     * Format price with commas (Persian style)
     * @param {number} price - Price to format
     * @returns {string} Formatted price
     */
    function formatPrice(price) {
        return new Intl.NumberFormat('fa-IR').format(price);
    }

    /**
     * Show toast notification
     * @param {string} message - Message to display
     * @param {string} type - Type: 'success', 'error', 'warning', 'info'
     */
    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `toast-notification toast-${type}`;
        toast.innerHTML = `
            <div class="toast-content">
                <span class="toast-icon">${getToastIcon(type)}</span>
                <span class="toast-message">${message}</span>
            </div>
        `;
        
        document.body.appendChild(toast);
        
        // Animate in
        setTimeout(() => toast.classList.add('show'), 10);
        
        // Remove after 3 seconds
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    /**
     * Get icon for toast notification
     * @param {string} type - Toast type
     * @returns {string} Icon HTML
     */
    function getToastIcon(type) {
        const icons = {
            success: '✓',
            error: '✕',
            warning: '⚠',
            info: 'ℹ'
        };
        return icons[type] || icons.info;
    }

    // ========================================================================
    // SCROLL ANIMATIONS
    // ========================================================================
    
    /**
     * Initialize scroll animations for elements
     */
    function initScrollAnimations() {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        // Observe all animated elements
        document.querySelectorAll('.animate-on-scroll').forEach(el => {
            observer.observe(el);
        });
    }

    /**
     * Initialize smooth scroll to top button
     */
    function initScrollToTop() {
        const scrollBtn = document.querySelector('.scroll-to-top');
        if (!scrollBtn) return;

        window.addEventListener('scroll', debounce(() => {
            if (window.scrollY > CONFIG.scrollThreshold) {
                scrollBtn.classList.add('visible');
            } else {
                scrollBtn.classList.remove('visible');
            }
        }, 100));

        scrollBtn.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    // ========================================================================
    // CART MANAGEMENT
    // ========================================================================
    
    /**
     * Cart Manager Class
     * Handles all cart operations with localStorage persistence
     */
    class CartManager {
        constructor() {
            this.cart = this.loadCart();
            this.init();
        }

        /**
         * Load cart from localStorage
         * @returns {Array} Cart items
         */
        loadCart() {
            const stored = localStorage.getItem(CONFIG.cartStorageKey);
            return stored ? JSON.parse(stored) : [];
        }

        /**
         * Save cart to localStorage
         */
        saveCart() {
            localStorage.setItem(CONFIG.cartStorageKey, JSON.stringify(this.cart));
            this.updateCartUI();
        }

        /**
         * Initialize cart event listeners
         */
        init() {
            this.updateCartUI();
            
            // Listen for add to cart buttons
            document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    const productId = btn.dataset.productId;
                    const productName = btn.dataset.productName;
                    const productPrice = parseFloat(btn.dataset.productPrice);
                    const productImage = btn.dataset.productImage;
                    
                    this.addItem({
                        id: productId,
                        name: productName,
                        price: productPrice,
                        image: productImage,
                        quantity: 1
                    });
                    
                    showToast('محصول به سبد خرید اضافه شد', 'success');
                });
            });
        }

        /**
         * Add item to cart
         * @param {Object} item - Item to add
         */
        addItem(item) {
            const existingItem = this.cart.find(i => i.id === item.id);
            
            if (existingItem) {
                existingItem.quantity++;
            } else {
                this.cart.push(item);
            }
            
            this.saveCart();
        }

        /**
         * Remove item from cart
         * @param {number} itemId - Item ID to remove
         */
        removeItem(itemId) {
            this.cart = this.cart.filter(item => item.id !== itemId);
            this.saveCart();
            showToast('محصول از سبد خرید حذف شد', 'info');
        }

        /**
         * Update item quantity
         * @param {number} itemId - Item ID
         * @param {number} quantity - New quantity
         */
        updateQuantity(itemId, quantity) {
            const item = this.cart.find(i => i.id === itemId);
            if (item) {
                if (quantity <= 0) {
                    this.removeItem(itemId);
                } else {
                    item.quantity = quantity;
                    this.saveCart();
                }
            }
        }

        /**
         * Get cart total
         * @returns {number} Total price
         */
        getTotal() {
            return this.cart.reduce((total, item) => total + (item.price * item.quantity), 0);
        }

        /**
         * Get cart items count
         * @returns {number} Items count
         */
        getCount() {
            return this.cart.reduce((count, item) => count + item.quantity, 0);
        }

        /**
         * Clear cart
         */
        clear() {
            this.cart = [];
            this.saveCart();
        }

        /**
         * Update cart UI elements
         */
        updateCartUI() {
            // Update cart badge
            const cartBadge = document.querySelector('.cart-badge');
            if (cartBadge) {
                const count = this.getCount();
                cartBadge.textContent = count;
                cartBadge.style.display = count > 0 ? 'flex' : 'none';
            }

            // Update cart total
            const cartTotal = document.querySelector('.cart-total');
            if (cartTotal) {
                cartTotal.textContent = `${formatPrice(this.getTotal())} تومان`;
            }

            // Update cart items display
            const cartItemsContainer = document.querySelector('.cart-items');
            if (cartItemsContainer) {
                this.renderCartItems(cartItemsContainer);
            }
        }

        /**
         * Render cart items in container
         * @param {HTMLElement} container - Container element
         */
        renderCartItems(container) {
            if (this.cart.length === 0) {
                container.innerHTML = `
                    <div class="empty-cart-message">
                        <img src="/imgLocal/cart.png" alt="سبد خالی" class="empty-cart-img">
                        <p>سبد خرید شما خالی است</p>
                    </div>
                `;
                return;
            }

            container.innerHTML = this.cart.map(item => `
                <div class="cart-item" data-id="${item.id}">
                    <img src="${item.image || '/imgLocal/default-product.jpg'}" alt="${item.name}" class="cart-item-image">
                    <div class="cart-item-details">
                        <h4 class="cart-item-name">${item.name}</h4>
                        <p class="cart-item-price">${formatPrice(item.price)} تومان</p>
                        <div class="cart-item-quantity">
                            <button class="qty-btn minus" onclick="window.cartManager.updateQuantity(${item.id}, ${item.quantity - 1})">-</button>
                            <span class="qty-value">${item.quantity}</span>
                            <button class="qty-btn plus" onclick="window.cartManager.updateQuantity(${item.id}, ${item.quantity + 1})">+</button>
                        </div>
                    </div>
                    <button class="remove-item-btn" onclick="window.cartManager.removeItem(${item.id})">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M18 6L6 18M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            `).join('');
        }
    }

    // ========================================================================
    // PRODUCT QUICK VIEW
    // ========================================================================
    
    /**
     * Initialize product quick view modal
     */
    function initQuickView() {
        document.querySelectorAll('.quick-view-btn').forEach(btn => {
            btn.addEventListener('click', async (e) => {
                e.preventDefault();
                const productId = btn.dataset.productId;
                
                try {
                    // Fetch product details
                    const response = await fetch(`${CONFIG.apiEndpoint}products/${productId}`);
                    const product = await response.json();
                    
                    // Show modal with product details
                    showQuickViewModal(product);
                } catch (error) {
                    console.error('Error fetching product:', error);
                    showToast('خطا در دریافت اطلاعات محصول', 'error');
                }
            });
        });
    }

    /**
     * Show quick view modal
     * @param {Object} product - Product data
     */
    function showQuickViewModal(product) {
        const modal = document.createElement('div');
        modal.className = 'quick-view-modal';
        modal.innerHTML = `
            <div class="modal-overlay"></div>
            <div class="modal-content">
                <button class="modal-close">&times;</button>
                <div class="modal-body">
                    <div class="modal-image">
                        <img src="${product.photo || '/imgLocal/default-product.jpg'}" alt="${product.name}">
                    </div>
                    <div class="modal-info">
                        <h2>${product.name}</h2>
                        <p class="modal-price">${formatPrice(product.price)} تومان</p>
                        <p class="modal-description">${product.description}</p>
                        <div class="modal-actions">
                            <button class="btn btn-primary add-to-cart-btn" 
                                    data-product-id="${product.id}"
                                    data-product-name="${product.name}"
                                    data-product-price="${product.price}"
                                    data-product-image="${product.photo}">
                                افزودن به سبد خرید
                            </button>
                            <button class="btn btn-outline-secondary">مشاهده جزئیات کامل</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.body.appendChild(modal);
        
        // Animate in
        setTimeout(() => modal.classList.add('show'), 10);
        
        // Close handlers
        modal.querySelector('.modal-close').addEventListener('click', () => closeQuickView(modal));
        modal.querySelector('.modal-overlay').addEventListener('click', () => closeQuickView(modal));
        
        // Re-initialize cart buttons
        if (window.cartManager) {
            window.cartManager.init();
        }
    }

    /**
     * Close quick view modal
     * @param {HTMLElement} modal - Modal element
     */
    function closeQuickView(modal) {
        modal.classList.remove('show');
        setTimeout(() => modal.remove(), 300);
    }

    // ========================================================================
    // LIVE SEARCH
    // ========================================================================
    
    /**
     * Initialize live search functionality
     */
    function initLiveSearch() {
        const searchInput = document.querySelector('.live-search-input');
        const searchResults = document.querySelector('.search-results');
        
        if (!searchInput || !searchResults) return;

        searchInput.addEventListener('input', debounce(async (e) => {
            const query = e.target.value.trim();
            
            if (query.length < 2) {
                searchResults.classList.remove('active');
                return;
            }

            try {
                const response = await fetch(`${CONFIG.apiEndpoint}search?q=${encodeURIComponent(query)}`);
                const results = await response.json();
                
                displaySearchResults(results, searchResults);
            } catch (error) {
                console.error('Search error:', error);
            }
        }, 300));

        // Close search results when clicking outside
        document.addEventListener('click', (e) => {
            if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                searchResults.classList.remove('active');
            }
        });
    }

    /**
     * Display search results
     * @param {Array} results - Search results
     * @param {HTMLElement} container - Results container
     */
    function displaySearchResults(results, container) {
        if (results.length === 0) {
            container.innerHTML = '<div class="no-results">محصولی یافت نشد</div>';
        } else {
            container.innerHTML = results.slice(0, 5).map(product => `
                <a href="/product.php?id=${product.id}" class="search-result-item">
                    <img src="${product.photo || '/imgLocal/default-product.jpg'}" alt="${product.name}">
                    <div class="result-info">
                        <h4>${product.name}</h4>
                        <span>${formatPrice(product.price)} تومان</span>
                    </div>
                </a>
            `).join('');
        }
        
        container.classList.add('active');
    }

    // ========================================================================
    // ADMIN DASHBOARD CHARTS
    // ========================================================================
    
    /**
     * Initialize admin dashboard charts
     */
    function initDashboardCharts() {
        const salesChart = document.querySelector('#salesChart');
        const ordersChart = document.querySelector('#ordersChart');
        
        if (salesChart) {
            initSalesChart(salesChart);
        }
        
        if (ordersChart) {
            initOrdersChart(ordersChart);
        }
    }

    /**
     * Initialize sales chart (pure CSS implementation)
     * @param {HTMLElement} canvas - Canvas element
     */
    function initSalesChart(canvas) {
        // Sample data - in production, fetch from API
        const data = [
            { month: 'فروردین', sales: 45000000 },
            { month: 'اردیبهشت', sales: 52000000 },
            { month: 'خرداد', sales: 38000000 },
            { month: 'تیر', sales: 61000000 },
            { month: 'مرداد', sales: 55000000 },
            { month: 'شهریور', sales: 68000000 }
        ];

        const maxSales = Math.max(...data.map(d => d.sales));
        
        canvas.innerHTML = `
            <div class="chart-container">
                <div class="chart-bars">
                    ${data.map((item, index) => {
                        const height = (item.sales / maxSales) * 100;
                        return `
                            <div class="chart-bar-wrapper">
                                <div class="chart-bar" style="height: ${height}%"></div>
                                <div class="chart-label">${item.month}</div>
                                <div class="chart-value">${formatPrice(item.sales / 1000000)}M</div>
                            </div>
                        `;
                    }).join('')}
                </div>
            </div>
        `;
    }

    /**
     * Initialize orders chart
     * @param {HTMLElement} canvas - Canvas element
     */
    function initOrdersChart(canvas) {
        const data = [
            { status: 'تکمیل شده', count: 145, color: '#28a745' },
            { status: 'در حال پردازش', count: 32, color: '#ffc107' },
            { status: 'لغو شده', count: 8, color: '#dc3545' },
            { status: 'منتظر پرداخت', count: 15, color: '#17a2b8' }
        ];

        const total = data.reduce((sum, item) => sum + item.count, 0);

        canvas.innerHTML = `
            <div class="donut-chart">
                ${data.map((item, index) => {
                    const percentage = (item.count / total) * 100;
                    return `
                        <div class="donut-segment" 
                             style="--percentage: ${percentage}; --color: ${item.color}"
                             title="${item.status}: ${item.count}">
                        </div>
                    `;
                }).join('')}
                <div class="donut-center">
                    <span class="total-count">${total}</span>
                    <span class="total-label">سفارش</span>
                </div>
            </div>
            <div class="chart-legend">
                ${data.map(item => `
                    <div class="legend-item">
                        <span class="legend-color" style="background: ${item.color}"></span>
                        <span class="legend-text">${item.status}</span>
                        <span class="legend-value">${item.count}</span>
                    </div>
                `).join('')}
            </div>
        `;
    }

    // ========================================================================
    // PAYMENT GATEWAY (ZARINPAL)
    // ========================================================================
    
    /**
     * Payment Manager Class
     * Handles ZarinPal payment integration
     */
    class PaymentManager {
        constructor() {
            this.merchantID = null; // Set from server config
        }

        /**
         * Initialize payment for order
         * @param {Object} orderData - Order information
         * @returns {Promise} Payment redirect URL
         */
        async initiatePayment(orderData) {
            try {
                const response = await fetch('/api/payment/request', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(orderData)
                });

                const result = await response.json();

                if (result.success) {
                    // Redirect to ZarinPal gateway
                    window.location.href = `${CONFIG.zarinPalGateway}${result.authority}`;
                } else {
                    showToast('خطا در ایجاد درخواست پرداخت', 'error');
                }

                return result;
            } catch (error) {
                console.error('Payment error:', error);
                showToast('خطا در ارتباط با درگاه پرداخت', 'error');
            }
        }

        /**
         * Verify payment after callback from ZarinPal
         * @param {string} authority - Transaction authority
         * @returns {Promise} Verification result
         */
        async verifyPayment(authority) {
            try {
                const response = await fetch('/api/payment/verify', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ authority })
                });

                const result = await response.json();
                
                if (result.success) {
                    showToast('پرداخت با موفقیت انجام شد', 'success');
                    if (window.cartManager) {
                        window.cartManager.clear();
                    }
                } else {
                    showToast('پرداخت ناموفق بود', 'error');
                }

                return result;
            } catch (error) {
                console.error('Verification error:', error);
                showToast('خطا در تایید پرداخت', 'error');
            }
        }
    }

    // ========================================================================
    // IMAGE LAZY LOADING
    // ========================================================================
    
    /**
     * Initialize lazy loading for images
     */
    function initLazyLoading() {
        const imageObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.add('loaded');
                    imageObserver.unobserve(img);
                }
            });
        });

        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }

    // ========================================================================
    // MOBILE MENU TOGGLE
    // ========================================================================
    
    /**
     * Initialize mobile menu toggle
     */
    function initMobileMenu() {
        const menuToggle = document.querySelector('.mobile-menu-toggle');
        const navMenu = document.querySelector('.nav-menu');
        
        if (!menuToggle || !navMenu) return;

        menuToggle.addEventListener('click', () => {
            navMenu.classList.toggle('active');
            menuToggle.classList.toggle('active');
        });

        // Close menu when clicking outside
        document.addEventListener('click', (e) => {
            if (!menuToggle.contains(e.target) && !navMenu.contains(e.target)) {
                navMenu.classList.remove('active');
                menuToggle.classList.remove('active');
            }
        });
    }

    // ========================================================================
    // PRODUCT FILTER & SORT
    // ========================================================================
    
    /**
     * Initialize product filtering and sorting
     */
    function initProductFilters() {
        const filterButtons = document.querySelectorAll('.filter-btn');
        const sortSelect = document.querySelector('.sort-select');
        const productsGrid = document.querySelector('.products-grid');
        
        if (!productsGrid) return;

        // Filter by category
        filterButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                const category = btn.dataset.category;
                
                // Update active state
                filterButtons.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                
                // Filter products
                const products = productsGrid.querySelectorAll('.product-card');
                products.forEach(product => {
                    if (category === 'all' || product.dataset.category === category) {
                        product.style.display = 'block';
                        setTimeout(() => product.classList.add('animate-in'), 50);
                    } else {
                        product.style.display = 'none';
                        product.classList.remove('animate-in');
                    }
                });
            });
        });

        // Sort products
        if (sortSelect) {
            sortSelect.addEventListener('change', (e) => {
                const sortBy = e.target.value;
                sortProducts(productsGrid, sortBy);
            });
        }
    }

    /**
     * Sort products by criteria
     * @param {HTMLElement} grid - Products grid
     * @param {string} sortBy - Sort criteria
     */
    function sortProducts(grid, sortBy) {
        const products = Array.from(grid.querySelectorAll('.product-card'));
        
        products.sort((a, b) => {
            const priceA = parseFloat(a.dataset.price);
            const priceB = parseFloat(b.dataset.price);
            const nameA = a.dataset.name;
            const nameB = b.dataset.name;
            
            switch (sortBy) {
                case 'price_asc':
                    return priceA - priceB;
                case 'price_desc':
                    return priceB - priceA;
                case 'name_asc':
                    return nameA.localeCompare(nameB, 'fa');
                case 'name_desc':
                    return nameB.localeCompare(nameA, 'fa');
                default:
                    return 0;
            }
        });

        products.forEach(product => grid.appendChild(product));
    }

    // ========================================================================
    // INITIALIZATION
    // ========================================================================
    
    /**
     * Initialize all modules when DOM is ready
     */
    function init() {
        // Initialize cart manager globally
        window.cartManager = new CartManager();
        
        // Initialize payment manager globally
        window.paymentManager = new PaymentManager();
        
        // Initialize all features
        initScrollAnimations();
        initScrollToTop();
        initQuickView();
        initLiveSearch();
        initDashboardCharts();
        initLazyLoading();
        initMobileMenu();
        initProductFilters();

        console.log('✨ Bazar Shop UI initialized successfully');
    }

    // Run initialization when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();
