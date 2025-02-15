{{-- resources/views/user/pemesanan/partials/status-badge.blade.php --}}
@switch($status)
    @case('pending')
        <span class="badge py-2 bg-warning">Menunggu Pembayaran</span>
        @break
    @case('settlement')
        <span class="badge py-2 bg-success">Pembayaran Berhasil</span>
        @break
    @case('capture')
        <span class="badge py-2 bg-success">Pembayaran Berhasil</span>
        @break
    @case('deny')
        <span class="badge py-2 bg-danger">Pembayaran Ditolak</span>
        @break
    @case('cancel')
        <span class="badge py-2 bg-danger">Pembayaran Dibatalkan</span>
        @break
    @case('expire')
        <span class="badge py-2 bg-secondary">Pembayaran Kedaluwarsa</span>
        @break
    @default
        <span class="badge py-2 bg-info">{{ ucfirst($status) }}</span>
@endswitch
