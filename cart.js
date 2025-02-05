function addToCart(productId) {
    const card = document.querySelector(`.card[data-id='${productId}']`);
    const productName = card.getAttribute('data-name');
    const price = parseFloat(card.getAttribute('data-price'));
    const quantityInput = document.getElementById('quantity-' + productId);
    const quantity = parseInt(quantityInput.value);

    if (quantity <= 0) {
        alert('Jumlah harus lebih dari 0!');
        return;
    }

    const cart = document.getElementById('cart-items');
    const noSelectionText = document.getElementById('no-selection-text');
    if (noSelectionText) noSelectionText.remove();

    let existingProduct = cart.querySelector(`p[data-id='${productId}']`);
    if (existingProduct) {
        let currentQuantity = parseInt(existingProduct.getAttribute('data-quantity'));
        currentQuantity += quantity;
        existingProduct.setAttribute('data-quantity', currentQuantity);
        existingProduct.firstChild.nodeValue = `${productName} - Jumlah: ${currentQuantity} `;
    } else {
        const productElement = document.createElement('p');
        productElement.textContent = `${productName} - Jumlah: ${quantity} `;
        productElement.setAttribute('data-id', productId);
        productElement.setAttribute('data-quantity', quantity);
        
        const cancelButton = document.createElement('button');
        cancelButton.textContent = 'Batal';
        cancelButton.classList.add('btn-cancel');
        cancelButton.onclick = function () {
            removeProductFromCart(productElement);
        };

        productElement.appendChild(cancelButton);
        cart.appendChild(productElement);
    }

    saveCartToSession(productId, productName, price, quantity);
    updateCheckoutButtonVisibility();
}

function saveCartToSession(productId, productName, price, quantity) {
    fetch('save_cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            Id_produk: productId,
            nama_produk: productName,
            harga: price,
            jumlah: quantity
        }),
    });
}

function removeProductFromCart(productElement) {
    const productId = productElement.getAttribute('data-id');
    const cart = document.getElementById('cart-items');
    cart.removeChild(productElement);

    fetch('remove_cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ Id_produk: productId }),
    }).then(response => response.json())
      .then(data => {
          if (!data.success) {
              alert('Gagal menghapus produk dari keranjang.');
          }
      });

    updateCheckoutButtonVisibility();
}

function updateCheckoutButtonVisibility() {
    const cart = document.getElementById('cart-items');
    const checkoutButton = document.getElementById('checkout-button');
    if (cart.children.length > 0) {
        checkoutButton.style.display = 'block';
    } else {
        checkoutButton.style.display = 'none';
        if (!cart.querySelector('#no-selection-text')) {
            const noSelectionText = document.createElement('span');
            noSelectionText.id = 'no-selection-text';
            noSelectionText.textContent = 'Belum ada yang dipilih';
            cart.appendChild(noSelectionText);
        }
    }
}

function redirectToCheckout() {
    window.location.href = 'transaksi.php';
}
