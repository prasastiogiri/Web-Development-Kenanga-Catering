<link rel="stylesheet" href="<?php echo e(asset('css/footer.css')); ?>">

<!-- HTML -->
<footer class="footer">
    <div class="footer-container">
        <div class="footer-section about">
            <h2 class="footer-title">Tentang Kami</h2>
            <p class="footer-description">
                Kami menyediakan berbagai paket terbaik untuk kebutuhan Anda. Pesan sekarang dan rasakan pengalaman
                terbaik bersama kami.
            </p>
            <h2 class="footer-title mt-4">Jelajahi Situs</h2>
            <ul class="footer-links">
                <li><a href="<?php echo e(route('homepage')); ?>" class="foot-link"><span>Beranda</span></a></li>
                <li><a href="<?php echo e(route('produkUser.index')); ?>" class="foot-link"><span>Produk</span></a></li>
                <li><a href="<?php echo e(route('paketUser.index')); ?>" class="foot-link"><span>Paket</span></a></li>
            </ul>
        </div>
        <div class="footer-section">
            <h2 class="footer-title">Maps</h2>
            <div class="map-container">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3955.910623092753!2d112.43869237574626!3d-7.475121773701022!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e780d9aac5c70c9%3A0xdcf1b06430d35d35!2sKenanga%20Catering!5e0!3m2!1sid!2sid!4v1736672641729!5m2!1sid!2sid"
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
        </div>
        <div class="footer-section contact">
            <h2 class="footer-title">Hubungi Kami</h2>
            <div class="contact-info">
                <p class="contact-item">
                    <i class="fa fa-map-marker-alt contact-icon"></i>
                    <span>Jl. Miji Baru 3 Gang Kenanga No. 22, Kota Mojokerto, Jawa Timur 61322</span>
                </p>
                <a href="https://wa.me/6281331081676" class="contact-item contact-link">
                    <i class="fab fa-whatsapp contact-icon"></i>
                    <span>+62 813-3108-1676</span>
                </a>
                <a href="https://instagram.com/kenangacatering" class="contact-item contact-link">
                    <i class="fab fa-instagram contact-icon"></i>
                    <span>@kenangacatering</span>
                </a>
                <a href="https://tiktok.com/@kenangacatering" class="contact-item contact-link">
                    <i class="fab fa-tiktok contact-icon"></i>
                    <span>@kenangacatering</span>
                </a>
                <a href="mailto:adminkenangacatering@gmail.com" class="contact-item contact-link">
                    <i class="bi bi-envelope contact-icon"></i>
                    <span>adminkenangacatering@gmail.com</span>
                </a>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; 2025 Kenanga Catering. All rights reserved.</p>
    </div>
</footer>
<?php /**PATH C:\laragon\www\Kenanga-Catering\resources\views/user/homepage/footer.blade.php ENDPATH**/ ?>