
// Cart data
let cartCount = 0;
let cartItems = JSON.parse(localStorage.getItem('cartItems')) || [];


// Sample products data
const products = {
    'Phantom T-Shirt': { price: 250 },
    'Band Cap': { price: 150 },
    'Album CD': { price: 120 },
    'Hoodie': { price: 350 },
    'Sticker Pack': { price: 50 },
    'Poster': { price: 80 }
};


// Add to cart functionality
document.querySelectorAll('.add-to-cart').forEach(button => {
    button.addEventListener('click', function() {
        const productCard = this.closest('.product-card');
        const productName = productCard.querySelector('h3').textContent;
        
        // Add to cart
        const existingItem = cartItems.find(item => item.name === productName);
        if (existingItem) {
            existingItem.quantity += 1;
        } else {
            cartItems.push({ name: productName, quantity: 1 });
        }
        
        cartCount = cartItems.reduce((total, item) => total + item.quantity, 0);
        
        // Save to localStorage
        localStorage.setItem('cartItems', JSON.stringify(cartItems));
        localStorage.setItem('cartCount', cartCount);
        
        updateCartCount();
        alert(`${productName} added to cart!`);
    });
});


function updateCartCount() {
    const cartLinks = document.querySelectorAll('.nav-link[href="cart.html"]');
    cartLinks.forEach(link => {
        link.textContent = `Cart (${cartCount})`;
    });
}


// Initialize cart count on page load
document.addEventListener('DOMContentLoaded', function() {
    cartCount = parseInt(localStorage.getItem('cartCount')) || 0;
    cartItems = JSON.parse(localStorage.getItem('cartItems')) || [];
    updateCartCount();
});


document.querySelectorAll('.rsvp-button').forEach(button => {
    button.addEventListener('click', function() {
        alert('Thank you for RSVPing! We look forward to seeing you.');
    });
});


document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth'
            });
        }
    });
});


