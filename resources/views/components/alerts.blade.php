<!-- SweetAlert2 Toasts - Dark Violet/Magenta Premium -->
<style>
    .swal2-popup.swal2-toast {
        background: linear-gradient(135deg, rgba(15,10,40,0.98) 0%, rgba(10,5,30,0.99) 100%) !important;
        border: 1px solid rgba(168,85,247,0.25) !important;
        border-radius: 0.875rem !important;
        box-shadow: 0 20px 50px rgba(0,0,0,0.6), 0 0 0 1px rgba(168,85,247,0.05), 0 0 30px rgba(168,85,247,0.06) !important;
        padding: 0.875rem 1.25rem !important;
        backdrop-filter: blur(20px) !important;
    }
    .swal2-popup.swal2-toast .swal2-title {
        font-size: 0.875rem !important;
        font-weight: 600 !important;
        color: rgba(226,232,240,0.95) !important;
        font-family: 'Inter', 'Poppins', sans-serif !important;
    }
    .swal2-popup.swal2-toast .swal2-timer-progress-bar {
        background: linear-gradient(90deg, #7c3aed, #a855f7, #ec4899) !important;
        height: 2px !important;
    }
    .swal2-popup.swal2-toast.swal2-icon-success {
        border-color: rgba(52,211,153,0.3) !important;
        border-left: 3px solid #34d399 !important;
    }
    .swal2-popup.swal2-toast.swal2-icon-error {
        border-color: rgba(239,68,68,0.3) !important;
        border-left: 3px solid #ef4444 !important;
    }
    .swal2-popup.swal2-toast.swal2-icon-warning {
        border-color: rgba(251,191,36,0.3) !important;
        border-left: 3px solid #fbbf24 !important;
    }
    .swal2-popup.swal2-toast.swal2-icon-info {
        border-color: rgba(168,85,247,0.3) !important;
        border-left: 3px solid #a855f7 !important;
    }
    .swal2-icon.swal2-success .swal2-success-ring { border-color: rgba(52,211,153,0.25) !important; }
    .swal2-icon.swal2-success [class^=swal2-success-line] { background-color: #34d399 !important; }
</style>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const Toast = Swal.mixin({
        toast: true,
        position: 'bottom-end',
        showConfirmButton: false,
        timer: 5000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });

    @if(session('success'))
        Toast.fire({ icon: 'success', title: '{{ addslashes(session('success')) }}' });
    @endif

    @if(session('error'))
        Toast.fire({ icon: 'error', title: '{{ addslashes(session('error')) }}' });
    @endif

    @if(session('warning'))
        Toast.fire({ icon: 'warning', title: '{{ addslashes(session('warning')) }}' });
    @endif

    @if(session('info'))
        Toast.fire({ icon: 'info', title: '{{ addslashes(session('info')) }}' });
    @endif

    // Livewire toast events
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('toast', (event) => {
            Toast.fire({
                icon: event[0].type || 'success',
                title: event[0].message
            });
        });
    });
</script>
