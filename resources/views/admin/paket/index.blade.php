@extends('layouts.admin.navbar')

@section('content')
    <div class="container-fluid px-4 py-4">

        <h2 class="mb-5"
            style="font-size: clamp(2rem, 4vw, 3.125rem);
        font-weight: 700;
        color: #9a7726;
        margin-bottom: 2rem;
        text-align: left;">
            Daftar Paket
        </h2>


        <div class="card shadow border-0 rounded-4">
            <div class="card-header py-3 bg-gradient d-flex justify-content-between align-items-center"
                style="background: linear-gradient(45deg, #926c15, #bd9542)">
                <h4 class="mb-0 text-white fw-semibold">Data Paket</h4>
                <button type="button" class="btn btn-outline-primary px-4 py-2 rounded" data-bs-toggle="modal"
                    data-bs-target="#createPaketModal">
                    <i class="fas fa-plus me-1"></i> Tambah Paket
                </button>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="paketTable">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center">No</th>
                                <th>Nama Paket</th>
                                <th>Deskripsi</th>
                                <th class="text-center">Harga</th>
                                <th class="text-center">Foto</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pakets as $index => $paket)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-box fa-lg me-2 text-secondary"></i>
                                            {{ $paket->nama }}
                                        </div>
                                    </td>
                                    <td>{{ $paket->deskripsi }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-success px-3 py-2">
                                            <i class="fas fa-tag me-1"></i>
                                            Rp {{ number_format($paket->harga, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if ($paket->foto)
                                            <img src="{{ asset('storage/' . $paket->foto) }}" alt="{{ $paket->nama }}"
                                                width="50" height="50" class="rounded-circle cursor-pointer"
                                                data-bs-toggle="modal" data-bs-target="#imageModal-{{ $paket->id }}">
                                        @else
                                            <span class="badge bg-secondary px-3 py-2">
                                                <i class="fas fa-image me-1"></i>
                                                No Image
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2 justify-content-center">
                                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                                data-bs-target="#editPaketModal-{{ $paket->id }}">
                                                <i class="fas fa-edit me-1"></i> Edit
                                            </button>
                                            <form action="{{ route('admin-paket.destroy', $paket->id) }}" method="POST"
                                                class="form-hapus d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash me-1"></i> Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Image Modal -->
                                <div class="modal fade" id="imageModal-{{ $paket->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">{{ $paket->nama }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body text-center">
                                                <img src="{{ asset('storage/' . $paket->foto) }}"
                                                    alt="{{ $paket->nama }}" class="img-fluid rounded">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Edit Modal -->
                                <div class="modal fade" id="editPaketModal-{{ $paket->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Paket</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="{{ route('admin-paket.update', $paket->id) }}" method="POST"
                                                    enctype="multipart/form-data">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="mb-3">
                                                        <label class="form-label">Nama Paket</label>
                                                        <input type="text" class="form-control" name="nama"
                                                            value="{{ $paket->nama }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Deskripsi</label>
                                                        <textarea class="form-control" name="deskripsi" rows="3" required>{{ $paket->deskripsi }}</textarea>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Harga</label>
                                                        <input type="number" class="form-control" name="harga"
                                                            value="{{ $paket->harga }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Foto Paket</label>
                                                        <input type="file" class="form-control" name="foto">
                                                        @if ($paket->foto)
                                                            <div class="mt-2">
                                                                <small class="text-muted">Current Image:</small>
                                                                <img src="{{ asset('storage/' . $paket->foto) }}"
                                                                    alt="Current" class="d-block mt-2" width="100">
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="d-flex gap-2">
                                                        <button type="submit" class="btn btn-success">
                                                            <i class="fas fa-save me-1"></i> Simpan
                                                        </button>
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">
                                                            <i class="fas fa-times me-1"></i> Batal
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <div class="modal fade" id="createPaketModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Paket</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin-paket.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Nama Paket</label>
                            <input type="text" class="form-control" name="nama" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea class="form-control" name="deskripsi" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Harga</label>
                            <input type="number" class="form-control" name="harga" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Foto Paket</label>
                            <input type="file" class="form-control" name="foto">
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-1"></i> Simpan
                            </button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-1"></i> Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.7.0/sweetalert2.min.css">
    <style>
        .card {
            transition: all 0.3s ease;
        }

        .btn {
            transition: all 0.2s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .badge {
            font-weight: 500;
        }

        .table> :not(caption)>*>* {
            padding: 1rem 0.75rem;
        }

        .dataTables_wrapper .dataTables_length select,
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 0.375rem 1.5rem;
        }

        .dataTables_wrapper .dataTables_length select:focus,
        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: #926c15;
            box-shadow: 0 0 0 0.25rem rgba(146, 108, 21, 0.25);
        }

        .cursor-pointer {
            cursor: pointer;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.7.0/sweetalert2.all.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            var table = $('#paketTable').DataTable({
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Tidak ada data yang ditampilkan",
                    infoFiltered: "(difilter dari _MAX_ total data)",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: ">>",
                        previous: "<<"
                    }
                }
            });

            // SweetAlert for delete confirmation
            $('.form-hapus').on('submit', function(e) {
                e.preventDefault();
                const form = this;

                Swal.fire({
                    title: "Konfirmasi",
                    text: "Apakah Anda yakin ingin menghapus paket ini?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#dc3545",
                    cancelButtonColor: "#6c757d",
                    confirmButtonText: "Ya, Hapus!",
                    cancelButtonText: "Batal"
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            // Display success messages with SweetAlert
            @if (session('success'))
                Swal.fire({
                    title: "Berhasil!",
                    text: "{{ session('success') }}",
                    icon: "success",
                    timer: 2000,
                    showConfirmButton: false
                });
            @endif
        });
    </script>
@endpush
