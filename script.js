document.querySelectorAll('.order-button').forEach(button => {
    button.addEventListener('click', function() {
        const productId = this.dataset.productId;
        document.getElementById('product-id').value = productId;
        document.getElementById('products').style.display = 'none';
        document.getElementById('order-form').style.display = 'block';

         // Scroll ke formulir pemesanan (opsional)
        document.getElementById('order-form').scrollIntoView({ behavior: 'smooth' });
    });
});

document.getElementById('cancel-order').addEventListener('click', function(){
    document.getElementById('products').style.display = 'block';
    document.getElementById('order-form').style.display = 'none';
});