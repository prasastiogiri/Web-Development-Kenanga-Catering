// Event Delegation untuk Update Jumlah dan Hapus Item
document.addEventListener('DOMContentLoaded', function () {
    // Handle klik tombol + dan -
    document.body.addEventListener('click', function (e) {
        if (e.target.classList.contains('update-jumlah')) {
            const itemId = e.target.dataset.itemId;
            const change = parseInt(e.target.dataset.change);
            updateJumlah(itemId, change);
        }

        // Handle klik tombol hapus (termasuk icon di dalam button)
        if (e.target.classList.contains('delete-item') || e.target.closest('.delete-item')) {
            const button = e.target.classList.contains('delete-item') ?
                e.target :
                e.target.closest('.delete-item');
            const itemId = button.dataset.itemId;
            hapusItem(itemId);
        }
    });
});

// Fungsi Update Jumlah
async function updateJumlah(itemId, change) {
    try {
        const jumlahInput = document.getElementById(`jumlah-${itemId}`);
        const currentJumlah = parseInt(jumlahInput.value);
        const itemElement = document.querySelector(`[data-id="${itemId}"]`);
        const itemName = itemElement.querySelector('.cart-item-details p').textContent.trim();

        // Konfigurasi produk khusus
        const specialProducts = ["Kambing Guling", "Sate Kambing 500 Tusuk + Gulai Kambing 1 Panci"];
        const isSpecialProduct = specialProducts.includes(itemName);
        const adjustedChange = isSpecialProduct ? (change > 0 ? 1 : -1) : change;
        const minQuantity = isSpecialProduct ? 1 : 100;
        const maxQuantity = isSpecialProduct ? 5 : Infinity;
        const newJumlah = currentJumlah + adjustedChange;

        if (newJumlah < minQuantity) {
            Swal.fire({
                title: 'Jumlah terlalu sedikit',
                text: isSpecialProduct ? 'Minimal pemesanan 1 porsi.' : 'Minimal pemesanan 100 porsi.',
                icon: 'warning',
            });
            return;
        }
        if (isSpecialProduct && newJumlah > maxQuantity) {
            Swal.fire({
                title: 'Jumlah terlalu banyak',
                text: 'Maksimal pemesanan hanya 5 porsi untuk produk ini.',
                icon: 'warning',
            });
            return;
        }

        const response = await fetch(`/keranjang/update-jumlah/${itemId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ jumlah: newJumlah }),
        });

        const data = await response.json();

        if (data.success) {
            jumlahInput.value = newJumlah;

            // Update total harga item
            const totalPriceElement = document.getElementById(`total-price-${itemId}`);
            if (totalPriceElement) {
                totalPriceElement.innerText = `Rp${data.totalHarga.toLocaleString('id-ID')}`;
            }

            // Update total keseluruhan
            await updateTotalHarga();

            // Update badge keranjang
            updateCartBadge(data.cartItemCount);
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire({
            title: 'Error',
            text: 'Terjadi kesalahan saat mengupdate jumlah',
            icon: 'error',
        });
    }
}

// Fungsi Update Total Harga
async function updateTotalHarga() {
    try {
        const response = await fetch('/keranjang/get-items', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });
        const data = await response.json();

        if (data.success) {
            const totalHargaElement = document.getElementById('total-harga');
            if (totalHargaElement) {
                totalHargaElement.innerText = `Rp${data.totalHarga.toLocaleString('id-ID')}`;
            }
        }
    } catch (error) {
        console.error('Error updating total harga:', error);
    }
}

// Fungsi Update Badge Keranjang
function updateCartBadge(count) {
    const badge = document.getElementById('cart-item-count');
    if (badge) {
        badge.textContent = count;
        badge.style.display = count > 0 ? 'inline-block' : 'none';
    }
}

// Fungsi Hapus Item
async function hapusItem(itemId) {
    try {
        const result = await Swal.fire({
            title: 'Apakah Anda yakin?',
            text: 'Item akan dihapus dari keranjang!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal',
        });

        if (result.isConfirmed) {
            const response = await fetch(`/keranjang/${itemId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if (data.success) {
                // Hapus element item dari DOM
                const itemElement = document.querySelector(`.cart-item[data-id="${itemId}"]`);
                if (itemElement) {
                    itemElement.remove();
                }

                // Update total dan badge
                await updateTotalHarga();
                updateCartBadge(data.cartItemCount);

                // Tampilkan pesan sukses
                await Swal.fire({
                    title: 'Berhasil',
                    text: 'Item berhasil dihapus dari keranjang',
                    icon: 'success',
                });

                // Refresh halaman jika keranjang kosong
                if (data.cartItemCount === 0) {
                    window.location.reload();
                }
            }
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire({
            title: 'Error',
            text: 'Terjadi kesalahan saat menghapus item',
            icon: 'error',
        });
    }
}

async function updateKeranjangDisplay() {
    try {
        const response = await fetch('/keranjang/get-items', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });
        const data = await response.json();

        if (data.success) {
            const cartList = document.querySelector('.cart-popup ul');
            if (!cartList) {
                location.reload();
                return;
            }

            // Bersihkan keranjang
            cartList.innerHTML = '';

            // Tambahkan item-item baru
            let totalHarga = 0;
            data.items.forEach(item => {
                const li = document.createElement('li');
                li.className = 'cart-item';
                li.setAttribute('data-id', item.id);

                li.innerHTML = `
                    <div class="cart-item-img">
                        <img src="${item.gambar}" alt="${item.nama}" class="img-fluid">
                    </div>
                    <div class="cart-item-details">
                        <p>${item.nama}</p>
                        <div class="input-group">
                            <button type="button" class="btn update-jumlah" data-item-id="${item.id}" data-change="-10">-</button>
                            <input type="number" name="jumlah" id="jumlah-${item.id}" class="form-input" value="${item.jumlah}" min="0" step="10" required readonly>
                            <button type="button" class="btn update-jumlah" data-item-id="${item.id}" data-change="10">+</button>
                        </div>
                        <p id="total-price-${item.id}" data-harga="${item.harga}">
                            Rp${item.subtotal.toLocaleString('id-ID')}
                        </p>
                    </div>
                    <div class="cart-item-actions">
                        <button class="btn btn-danger delete-item" data-item-id="${item.id}">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                `;

                cartList.appendChild(li);
                totalHarga += item.subtotal;
            });

            // Update total harga
            const totalHargaElement = document.getElementById('total-harga');
            if (totalHargaElement) {
                totalHargaElement.innerText = `Rp${totalHarga.toLocaleString('id-ID')}`;
            }

            // Update tampilan keranjang kosong jika tidak ada item
            const emptyCart = document.querySelector('.cart-popup .empty');
            const emptyIcon = document.querySelector('.cart-popup .empty-icon');
            const cartTotal = document.querySelector('.cart-total');
            const orderButton = document.querySelector('.cart-popup .btn-primary');

            if (data.items.length === 0) {
                cartList.style.display = 'none';
                if (emptyIcon) emptyIcon.style.display = 'block';
                if (emptyCart) emptyCart.style.display = 'block';
                if (cartTotal) cartTotal.style.display = 'none';
                if (orderButton) orderButton.style.display = 'none';
            } else {
                cartList.style.display = 'block';
                if (emptyIcon) emptyIcon.style.display = 'none';
                if (emptyCart) emptyCart.style.display = 'none';
                if (cartTotal) cartTotal.style.display = 'flex';
                if (orderButton) orderButton.parentElement.style.display = 'block';
            }
        }
    } catch (error) {
        console.error('Error updating keranjang display:', error);
    }
}
// Modifikasi fungsi addToCart untuk memperbarui tampilan secara real-time
async function addToCart(form) {
    try {
        const formData = new FormData(form);
        const response = await fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const data = await response.json();

        if (response.status === 401) {
            Swal.fire({
                icon: 'warning',
                title: 'Login Required',
                text: 'Please login to add items to cart',
                confirmButtonText: 'Login'
            }).then(() => {
                window.location.href = '/login';
            });
            return;
        }

        if (data.success) {
            // Update UI
            await updateKeranjangDisplay();
            updateCartBadge(data.cartItemCount);

            // Close modal
            bootstrap.Modal.getInstance(document.getElementById('cartModal'))?.hide();

            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message,
                timer: 2000
            });
        } else {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: data.message || 'Gagal menambahkan ke keranjang',
                confirmButtonText: 'OK'
            });
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Terjadi kesalahan sistem',
            confirmButtonText: 'OK'
        });
    }
}

// Tambahkan event listener untuk modal cart
document.addEventListener('DOMContentLoaded', function () {
    const cartModal = document.getElementById('cartModal');
    if (cartModal) {
        const form = cartModal.querySelector('form');
        if (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                addToCart(this);
            });
        }
    }
});
