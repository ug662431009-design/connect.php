// Toggle menu mobile
const toggler = document.querySelector('.navbar-toggler');
const menu = document.querySelector('#navbarMenu');

toggler.addEventListener('click', () => {
    menu.classList.toggle('show');
});

// Alert เมื่อเพิ่มสินค้าสำเร็จ
function showAlert(message) {
    const alertBox = document.createElement('div');
    alertBox.className = 'alert alert-success';
    alertBox.innerText = message;
    document.body.prepend(alertBox);
    setTimeout(() => alertBox.remove(), 3000);
}

// Confirm ลบสินค้า
function confirmDelete(productName) {
    return confirm(`Are you sure you want to delete "${productName}"?`);
}
