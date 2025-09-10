document.addEventListener('DOMContentLoaded', function() {

    // --- Bagian untuk menu mobile (biarkan seperti adanya) ---
    const menuToggle = document.querySelector('.menu-toggle');
    const sidebar = document.querySelector('.sidebar');

    if (menuToggle && sidebar) {
        menuToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
        });

        // Event listener untuk menutup sidebar saat klik di luar area
        document.addEventListener('click', function(event) {
            if (window.innerWidth < 992 &&
                !sidebar.contains(event.target) &&
                !menuToggle.contains(event.target) &&
                sidebar.classList.contains('active')) {
                sidebar.classList.remove('active');
            }
        });
    }

    // --- KODE UNTUK KARTU LAYANAN ---
    // Kode ini hanya akan memilih kartu yang memiliki class "service-card" DAN "disabled"
    const disabledCards = document.querySelectorAll('.service-card.disabled');

    disabledCards.forEach(card => {
        card.addEventListener('click', function(event) {
            // Mencegah link (yang href="#") pindah halaman
            event.preventDefault();

            // Mengambil nama fitur dari tag <span> di dalam kartu
            const featureName = card.querySelector('span').textContent;

            // Menampilkan pesan alert yang informatif
            alert(`Fitur "${featureName}" akan segera hadir!`);
        });
    });

    // Kode untuk efek ripple (opsional, bisa ditambahkan jika Anda punya CSS-nya)
    document.querySelectorAll('.service-card').forEach(card => {
        card.addEventListener('click', function(event) {
            // Jangan jalankan preventDefault jika kartu tidak di-disable
            if (this.classList.contains('disabled')) {
                event.preventDefault();
            }

            const ripple = document.createElement('span');
            ripple.classList.add('ripple-effect');
            this.appendChild(ripple);

            const x = event.clientX - this.getBoundingClientRect().left;
            const y = event.clientY - this.getBoundingClientRect().top;

            ripple.style.left = `${x}px`;
            ripple.style.top = `${y}px`;

            setTimeout(() => {
                ripple.remove();
            }, 500);
        });
    });
});