function showNotification(message, type) {
    const notification = document.getElementById('notification');
    notification.className = `alert ${type}`;
    notification.innerHTML = message;
    notification.style.display = 'block';

    setTimeout(() => {
        notification.style.display = 'none';
    }, 5000);
}

const urlParams = new URLSearchParams(window.location.search);
const message = urlParams.get('message');
const type = message && (message.includes('salah') || message.includes('tidak ditemukan')) ? 'alert-danger' : 'alert-success';

if (message) {
    showNotification(decodeURIComponent(message), type);
}


