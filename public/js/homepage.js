document.addEventListener('DOMContentLoaded', function() {
    // Initialize all tooltips
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(
        tooltipTriggerEl));

    // Optional: Destroy tooltips before carousel slide to prevent hanging tooltips
    const carousel = document.getElementById('packageCarousel');
    carousel.addEventListener('slide.bs.carousel', function() {
        tooltipList.forEach(tooltip => tooltip.hide());
    });
});
document.addEventListener('DOMContentLoaded', () => {
    const pesanSekarangButtons = document.querySelectorAll('.pesan-sekarang');

    pesanSekarangButtons.forEach(button => {
        button.addEventListener('click', (event) => {
            const isLoggedIn = button.dataset.login === 'true';
            const loginUrl = button.dataset.loginUrl;

            if (!isLoggedIn) {
                event.preventDefault(); // Cegah aksi default pada tombol
                Swal.fire({
                    icon: 'warning',
                    title: 'Anda harus login',
                    text: 'Harap login terlebih dahulu untuk memesan produk.',
                    confirmButtonText: 'Login'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href =
                            loginUrl; // Redirect ke halaman login
                    }
                });
            }
        });
    });
});

const swiper = new Swiper('.swiper-container', {
    slidesPerView: 1,
    spaceBetween: 20,
    pagination: {
      el: '.swiper-pagination',
      clickable: true,
    },
    breakpoints: {
      640: {
        slidesPerView: 2,
      },
      968: {
        slidesPerView: 3,
      }
    }
  });
