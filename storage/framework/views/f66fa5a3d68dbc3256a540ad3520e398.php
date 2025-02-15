
<style>
    /* Tambahkan CSS untuk peta */
    #mapContainer {
        height: 300px;
        width: 100%;
        border-radius: 8px;
        margin-top: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .leaflet-control-geocoder {
        margin-top: 10px;
    }
</style>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder@2.3.0/dist/Control.Geocoder.css" />

<div class="modal fade" id="orderModal" tabindex="-1" data-bs-backdrop="false">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="bi bi-cart-check me-2"></i>Form Pemesanan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Progress Bar -->
                <div class="progress mb-4" style="height: 3px;">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: 100%"></div>
                </div>

                <!-- Data Pesanan -->
                <div class="mb-4">
                    <h6 class="d-flex align-items-center mb-3">
                        <i class="bi bi-bag-check me-2"></i>Data Pesanan
                    </h6>
                    <?php if(auth()->check() && auth()->user()->keranjangItems->count() > 0): ?>
                        <?php
                            $totalHarga = 0;
                            $validItems = false;
                        ?>

                        <?php $__currentLoopData = auth()->user()->keranjangItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($item->paket || $item->produk): ?>
                                <?php
                                    $validItems = true;
                                    $harga = $item->paket ? $item->paket->harga : ($item->produk ? $item->produk->harga : 0);
                                    $totalHarga += $harga * $item->jumlah;
                                ?>
                                <div id="cart-items-container">
                                 </div>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                </div>

                <!-- Detail Pemesanan -->
                <div>
                    <h6 class="d-flex align-items-center mb-3">
                        <i class="bi bi-card-checklist me-2"></i>Detail Pemesanan
                    </h6>
                    <form id="pemesananForm" action="<?php echo e(route('pemesanan.store')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="card border-0 shadow-sm p-3">
                            <div class="mb-3">
                                <label for="userName" class="form-label text-muted">Nama Pemesan</label>
                                <input type="text" class="form-control bg-light" id="userName" value="<?php echo e(auth()->user()->nama); ?>" readonly>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="eventDate" class="form-label text-muted">Tanggal Acara</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-primary text-white">
                                                <i class="bi bi-calendar-event"></i>
                                            </span>
                                            <input type="date" class="form-control" id="eventDate" name="event_date" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="eventPlace" class="form-label text-muted">Lokasi Acara</label>
                                        <div class="input-group mb-2">
                                            <span class="input-group-text bg-primary text-white">
                                                <i class="bi bi-geo-alt"></i>
                                            </span>
                                            <input type="text" id="eventPlace" name="event_place" class="form-control"
                                                   placeholder="Cari dan pilih lokasi" readonly required>
                                        </div>
                                        <input type="hidden" id="latitude" name="latitude">
                                        <input type="hidden" id="longitude" name="longitude">
                                        <div id="mapContainer"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="paymentMethod" class="form-label text-muted">Metode Pembayaran</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary text-white">
                                        <i class="bi bi-credit-card"></i>
                                    </span>
                                    <select class="form-select" id="paymentMethod" name="payment_method" required>
                                        <option value="">Pilih metode pembayaran</option>
                                        <option value="credit_card">Kartu Kredit</option>
                                        <option value="bca_va">BCA Virtual Account</option>
                                        <option value="bni_va">BNI Virtual Account</option>
                                        <option value="bri_va">BRI Virtual Account</option>
                                        <option value="GoPay">GoPay</option>
                                        <option value="shopeepay">ShopeePay</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-2"></i>Tutup
                </button>
                <button type="submit" form="pemesananForm" class="btn btn-primary">
                    <i class="bi bi-wallet2 me-2"></i>Lanjutkan ke Pembayaran
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-control-geocoder@2.3.0/dist/Control.Geocoder.js"></script>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const pemesananForm = document.getElementById('pemesananForm');
    const eventPlaceInput = document.getElementById('eventPlace');
    const latitudeInput = document.getElementById('latitude');
    const longitudeInput = document.getElementById('longitude');
    const eventDateInput = document.getElementById('eventDate');

    // Koordinat Surabaya
    const surabayaCoords = [-7.2575, 112.7521];

    // Set default tanggal acara (7 hari dari hari ini)
    const today = new Date();
    const nextWeek = new Date(today);
    nextWeek.setDate(today.getDate() + 7);
    const formattedDate = nextWeek.toISOString().split('T')[0];
    eventDateInput.value = formattedDate;
    eventDateInput.setAttribute('min', formattedDate);

    // Inisialisasi peta dengan koordinat Surabaya
    const map = L.map('mapContainer').setView(surabayaCoords, 13);

    // Tambahkan layer peta
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Marker yang bisa dipindahkan
    const marker = L.marker(surabayaCoords, {
        draggable: true
    }).addTo(map);

    // Geocoder untuk pencarian
    const geocoder = L.Control.geocoder({
        defaultMarkGeocode: false,
        position: 'topright',
        placeholder: 'Cari lokasi di Surabaya...'
    }).addTo(map);

    // Event ketika marker dipindahkan
    marker.on('dragend', function(e) {
        const latlng = e.target.getLatLng();
        updateLocationDetails(latlng);
    });

    // Event pencarian
    geocoder.on('markgeocode', function(e) {
        const latlng = e.geocode.center;
        map.setView(latlng, 15);
        marker.setLatLng(latlng);
        updateLocationDetails(latlng);
    });

    // Fungsi update detail lokasi
    function updateLocationDetails(latlng) {
        latitudeInput.value = latlng.lat;
        longitudeInput.value = latlng.lng;

        // Reverse geocoding sederhana
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${latlng.lat}&lon=${latlng.lng}`)
            .then(response => response.json())
            .then(data => {
                eventPlaceInput.value = data.display_name || `${latlng.lat}, ${latlng.lng}`;
            });
    }

    // Function to update order modal content
    async function updateOrderModal() {
        try {
            const response = await fetch('/keranjang/get-items', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            const data = await response.json();
            if (data.success) {
                const cartItemsContainer = document.getElementById('cart-items-container');
                let cartContent = '';
                let totalHarga = 0;
                data.items.forEach(item => {
                    const subtotal = item.harga * item.jumlah;
                    totalHarga += subtotal;
                    cartContent += `
                        <div class="card mb-3 border-0 shadow-sm cart-item-modal" data-item-id="${item.id}">
                            <div class="row g-0">
                                <div class="col-md-3">
                                    <img src="${item.gambar}" class="img-fluid rounded-start h-100 object-fit-cover" alt="${item.nama}">
                                </div>
                                <div class="col-md-9">
                                    <div class="card-body">
                                        <h6 class="card-title fw-bold text-primary">${item.nama}</h6>
                                        <div class="row g-2">
                                            <div class="col-sm-4">
                                                <p class="card-text mb-1">
                                                    <small class="text-muted">Harga:</small><br>
                                                    <span class="fw-semibold price-display">
                                                        Rp${item.harga.toLocaleString('id-ID')}
                                                    </span>
                                                </p>
                                            </div>
                                            <div class="col-sm-4">
                                                <p class="card-text mb-1">
                                                    <small class="text-muted">Jumlah:</small><br>
                                                    <span class="fw-semibold quantity-display">${item.jumlah} Porsi</span>
                                                </p>
                                            </div>
                                            <div class="col-sm-4">
                                                <p class="card-text mb-1">
                                                    <small class="text-muted">Subtotal:</small><br>
                                                    <span class="fw-semibold text-primary subtotal-display">
                                                        Rp${subtotal.toLocaleString('id-ID')}
                                                    </span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });

                // Add total price card
                cartContent += `
                    <div class="card bg-light border-0 p-3 mt-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Total Harga:</h6>
                            <h5 class="mb-0 text-primary fw-bold total-price-modal">
                                Rp${totalHarga.toLocaleString('id-ID')}
                            </h5>
                        </div>
                    </div>
                `;

                if (cartItemsContainer) {
                    cartItemsContainer.innerHTML = cartContent;
                }
            }
        } catch (error) {
            console.error('Error updating order modal:', error);
        }
    }

    // Event Listeners
    const orderModal = document.getElementById('orderModal');
    if (orderModal) {
        orderModal.addEventListener('shown.bs.modal', function() {
            map.invalidateSize();
            updateOrderModal();
        });
    }

    // Update modal when cart changes
    document.body.addEventListener('cartUpdated', updateOrderModal);

    if (pemesananForm) {
        pemesananForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // Validasi form
            const eventDate = eventDateInput.value;
            const eventPlace = eventPlaceInput.value;
            const paymentMethod = document.getElementById('paymentMethod').value;

            if (!eventDate || !eventPlace || !paymentMethod) {
                Swal.fire({
                    title: 'Error!',
                    text: 'Harap lengkapi semua kolom yang diperlukan!',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return;
            }

            // Submit form using fetch
            fetch(pemesananForm.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: new FormData(pemesananForm)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Store snap token if needed for payment processing
                    if (data.snap_token) {
                        sessionStorage.setItem('snap_token', data.snap_token);
                    }

                    // Redirect to the specified URL
                    if (data.redirect_url) {
                        window.location.href = data.redirect_url;
                    }
                } else {
                    // Handle error
                    Swal.fire({
                        title: 'Error!',
                        text: data.message || 'Terjadi kesalahan saat memproses pesanan.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'Terjadi kesalahan saat mengirim pesanan.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
        });
    }
});
</script>
<?php /**PATH C:\laragon\www\Kenanga-Catering\resources\views/layouts/user/partials/pesan-sekarang-modal.blade.php ENDPATH**/ ?>