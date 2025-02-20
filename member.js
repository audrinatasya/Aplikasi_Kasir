document.getElementById('searchPelanggan').addEventListener('input', function() {
    var searchQuery = this.value;
    if (searchQuery.length > 2) { // Mulai pencarian setelah 3 karakter
        fetch('get_pelanggan.php?search=' + searchQuery)
            .then(response => response.json())
            .then(data => {
                var results = document.getElementById('pelangganResults');
                results.innerHTML = '';
                data.forEach(function(pelanggan) {
                    var div = document.createElement('div');
                    div.textContent = pelanggan.nama_pelanggan;
                    div.setAttribute('data-id', pelanggan.Id_pelanggan);
                    div.setAttribute('data-alamat', pelanggan.alamat);
                    div.setAttribute('data-no_telepon', pelanggan.no_telepon);
                    div.addEventListener('click', function() {
                        document.getElementById('searchPelanggan').value = this.textContent;
                        document.getElementById('alamat_member').value = this.getAttribute('data-alamat');
                        document.getElementById('nomor_telepon_member').value = this.getAttribute('data-no_telepon');
                 
                        document.getElementById('nama_pelanggan_member').value = this.getAttribute('data-id');
                        results.style.display = 'none';
                    });
                    results.appendChild(div);
                });
                results.style.display = 'block';
            });
    } else {
        document.getElementById('pelangganResults').style.display = 'none';
    }
});

document.addEventListener('click', function(event) {
    if (!event.target.matches('#searchPelanggan')) {
        document.getElementById('pelangganResults').style.display = 'none';
    }
});