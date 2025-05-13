document.addEventListener('DOMContentLoaded', function() {
    // Handle quantity increase and decrease
    const quantityControls = document.querySelectorAll('.quantity-controls');
    
    quantityControls.forEach(function(control) {
        const minusBtn = control.querySelector('.quantity-minus');
        const plusBtn = control.querySelector('.quantity-plus');
        const input = control.querySelector('.quantity-input');
        
        if (minusBtn && input) {
            minusBtn.addEventListener('click', function() {
                const currentValue = parseInt(input.value);
                if (currentValue > 1) {
                    input.value = currentValue - 1;
                    
                    // Find the closest update button and highlight it
                    const updateBtn = control.closest('.quantity-form').querySelector('.update-btn');
                    if (updateBtn) {
                        updateBtn.style.backgroundColor = '#007bff';
                        updateBtn.style.color = 'white';
                        updateBtn.classList.add('pulsate');
                    }
                }
            });
        }
        
        if (plusBtn && input) {
            plusBtn.addEventListener('click', function() {
                const currentValue = parseInt(input.value);
                input.value = currentValue + 1;
                
                // Find the closest update button and highlight it
                const updateBtn = control.closest('.quantity-form').querySelector('.update-btn');
                if (updateBtn) {
                    updateBtn.style.backgroundColor = '#007bff';
                    updateBtn.style.color = 'white';
                    updateBtn.classList.add('pulsate');
                }
            });
        }
        
        if (input) {
            input.addEventListener('change', function() {
                // Ensure minimum value is 1
                if (parseInt(input.value) < 1) {
                    input.value = 1;
                }
                
                // Find the closest update button and highlight it
                const updateBtn = control.closest('.quantity-form').querySelector('.update-btn');
                if (updateBtn) {
                    updateBtn.style.backgroundColor = '#007bff';
                    updateBtn.style.color = 'white';
                    updateBtn.classList.add('pulsate');
                }
            });
        }
    });
    
    // Confirmation for removing items and clearing cart
    const removeForms = document.querySelectorAll('form[action="cart.php"]');
    
    removeForms.forEach(function(form) {
        const actionInput = form.querySelector('input[name="action"]');
        
        if (actionInput && actionInput.value === 'remove') {
            form.addEventListener('submit', function(e) {
                if (!confirm('Are you sure you want to remove this item from your cart?')) {
                    e.preventDefault();
                }
            });
        }
        
        if (actionInput && actionInput.value === 'clear') {
            form.addEventListener('submit', function(e) {
                if (!confirm('Are you sure you want to clear your entire cart?')) {
                    e.preventDefault();
                }
            });
        }
    });
    
    // Auto-hide messages after 5 seconds
    const messages = document.querySelectorAll('.message');
    
    messages.forEach(function(message) {
        setTimeout(function() {
            message.style.opacity = '0';
            setTimeout(function() {
                message.style.display = 'none';
            }, 500);
        }, 5000);
    });
}); 