document.addEventListener('DOMContentLoaded', function () {
    const cartCounter = document.getElementById('cart-counter');

    // Load cart items from localStorage
    let cartItems = JSON.parse(localStorage.getItem('cartItems')) || [];

    function updateCounter() {
        const count = cartItems.length;
        if (cartCounter) cartCounter.innerText = count;
        localStorage.setItem('cartCount', count);
    }

    updateCounter();

    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function () {
            // Try to find product info in DOM
            const productEl = this.closest('.product');
            let name = 'Unknown product';
            let price = 0;
            if (productEl) {
                const nameEl = productEl.querySelector('h3');
                const priceEl = productEl.querySelector('p');
                if (nameEl) name = nameEl.innerText.trim();
                if (priceEl) {
                    // Extract digits from price text e.g. 'Rs 180000' or 'Rs. 200000'
                    const num = priceEl.innerText.replace(/[^0-9]/g, '');
                    price = parseInt(num) || 0;
                }
            }

            cartItems.push({ name: name, price: price });
            localStorage.setItem('cartItems', JSON.stringify(cartItems));
            updateCounter();
            alert(name + ' has been added to your cart!');
        });
    });

    // Expose cartItems helper for pages that may want to re-render
    window.__getCartItems = function () {
        return JSON.parse(localStorage.getItem('cartItems')) || [];
    };
});