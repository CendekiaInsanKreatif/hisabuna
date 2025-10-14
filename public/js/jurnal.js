$(document).ready(function () {
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    // console.log('CSRF Token:', csrfToken);

    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': csrfToken }
    });

    // Jalankan Jurnal
    $('#jurnal').click(async function () {
        try {
            const response = await $.get('https://app.hisabuna.id/api/jurnal');
            console.log('Data Jurnal:', response);
            alert('Jurnal berhasil dijalankan!');
        } catch (error) {
            console.error('Error:', error);
            alert('Gagal menjalankan Jurnal.');
        }
    });

    // Print Report
    $('#print').click(async function (e) {
        e.preventDefault();
        const a = $('#dari').val();
        const b = $('#sampai').val();

        if (!a || !b) {
            showNotification('Mohon isi range jurnal yang ingin diunduh!', 'warning');
            return;
        }

        if (parseInt(a) > parseInt(b)) {
            showNotification('Nomor jurnal awal tidak boleh lebih besar dari nomor akhir!', 'warning');
            return;
        }

        if (parseInt(a) < 1 || parseInt(b) < 1) {
            showNotification('Nomor jurnal harus lebih besar dari 0!', 'warning');
            return;
        }

        // Show loading overlay
        const printButton = $(this);
        const originalText = printButton.html();

        // Show global loading overlay
        if (typeof showAlert === 'function') {
            showAlert();
        }

        // Also disable button with loading state
        printButton.prop('disabled', true).html(`
            <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            Memuat Data...
        `);

        try {
            const response = await $.ajax({
                url: window.jurnalConfig.printReportUrl,
                type: 'POST',
                data: { a, b, _token: csrfToken },
                xhrFields: { responseType: 'blob' }
            });

            const blob = new Blob([response], { type: 'application/pdf' });
            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = 'daftar_jurnal.pdf';
            document.body.appendChild(link);
            link.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(link);

            // Hide loading overlay
            if (typeof closeAlert === 'function') {
                closeAlert();
            }

            // Reset button state
            printButton.prop('disabled', false).html(originalText);

            // Show success notification
            showNotification('Daftar jurnal berhasil diunduh!', 'success');

            // Close modal after successful download
            setTimeout(() => {
                toggleModal('exampleModal');
                // Reset form fields
                $('#dari').val('');
                $('#sampai').val('');
            }, 800);

        } catch (error) {
            console.error('Error:', error);

            // Hide loading overlay on error
            if (typeof closeAlert === 'function') {
                closeAlert();
            }

            // Reset button state on error
            printButton.prop('disabled', false).html(originalText);

            showNotification('Gagal mengunduh laporan. Silakan coba lagi.', 'error');
        } finally {
            // Fallback: ensure loading overlay is always closed
            setTimeout(() => {
                if (typeof closeAlert === 'function') {
                    closeAlert();
                }
                // Ensure button is reset
                if (printButton.prop('disabled')) {
                    printButton.prop('disabled', false).html(originalText);
                }
            }, 1000);
        }
    });

    // Detail Total Jurnal
    $('#detail').click(async function (e) {
        e.preventDefault();
        const button = $(this);
        const input = $('#total-jurnal');

        // Show loading state
        button.addClass('animate-spin');
        input.val('Memuat data...');

        try {
            const res = await $.get('totalJurnal');
            input.val(res.data);
        } catch (error) {
            console.error('Error:', error);
            input.val('Gagal memuat data');
        } finally {
            button.removeClass('animate-spin');
        }
    });

    // Auto load total jurnal when modal opens
    function loadTotalJurnal() {
        $('#detail').click();
    }

    // Load total jurnal when modal is opened
    $(document).on('click', '[onclick*="exampleModal"]', function () {
        setTimeout(loadTotalJurnal, 500);
    });

    // Cek Trial on load
    (async function cekTrial() {
        try {
            const res = await $.get('cekTrial');
            // console.log('Trial Data:', res.data);
        } catch (error) {
            console.error('Error cekTrial:', error);
        }
    })();
});

// Show Notification Function
function showNotification(message, type = 'info') {
    // Remove existing notification if any
    const existingNotification = document.querySelector('.notification-toast');
    if (existingNotification) {
        existingNotification.remove();
    }

    const colors = {
        success: 'bg-success-500 border-success-600',
        error: 'bg-danger-500 border-danger-600',
        warning: 'bg-warning-500 border-warning-600',
        info: 'bg-info-500 border-info-600'
    };

    const icons = {
        success: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>`,
        error: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>`,
        warning: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                  </svg>`,
        info: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
               </svg>`
    };

    const notification = document.createElement('div');
    notification.className = `notification-toast fixed top-4 right-4 z-[9999] ${colors[type]} text-white px-4 py-3 rounded-lg shadow-lg border-l-4 flex items-center gap-3 transform translate-x-full opacity-0 transition-all duration-300`;
    notification.innerHTML = `
        ${icons[type]}
        <span class="font-medium">${message}</span>
        <button onclick="this.parentElement.remove()" class="ml-2 text-white hover:text-gray-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    `;

    document.body.appendChild(notification);

    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full', 'opacity-0');
    }, 100);

    // Auto remove after 4 seconds
    setTimeout(() => {
        notification.classList.add('translate-x-full', 'opacity-0');
        setTimeout(() => notification.remove(), 300);
    }, 4000);
}

// Toggle Modal Function
function toggleModal(modalID) {
    const modal = document.getElementById(modalID);
    if (modal) {
        const isHidden = modal.classList.contains('hidden');

        if (isHidden) {
            // Opening modal
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // Prevent background scrolling

            // Load total jurnal when opening the modal
            if (modalID === 'exampleModal') {
                setTimeout(() => {
                    $('#detail').click();
                }, 300);
            }
        } else {
            // Closing modal
            modal.classList.add('hidden');
            document.body.style.overflow = ''; // Restore scrolling

            // Reset form when closing
            if (modalID === 'exampleModal') {
                setTimeout(() => {
                    $('#dari').val('');
                    $('#sampai').val('');
                    $('#total-jurnal').val('');
                }, 200);
            }
        }
    } else {
        console.warn(`Modal dengan ID "${modalID}" tidak ditemukan.`);
    }
}

// Alpine.js Component for Jurnal Table (Optimized)
document.addEventListener('alpine:init', () => {
    Alpine.data('jurnalTable', () => ({
        // State
        currentPage: 1,
        itemsPerPage: 10,
        totalItems: 0,
        totalPages: 0,
        sortDirection: 'asc',
        filter: 'all',
        selectedCategory: 'all',
        selectedMonth: 'all',
        searchInput: '',
        searchTimeout: null,
        allData: [],
        isLoading: true,
        isSearching: false,
        hasError: false,
        errorMessage: '',
        totalDebit: 0,
        totalCredit: 0,
        months: [],

        // Computed properties
        get selisih() {
            return this.totalDebit - this.totalCredit;
        },

        get paginatedData() {
            const start = (this.currentPage - 1) * this.itemsPerPage;
            const end = start + this.itemsPerPage;
            return this.allData.slice(start, end);
        },

        get pagesToShow() {
            const start = Math.floor((this.currentPage - 1) / 3) * 3 + 1;
            const end = Math.min(start + 2, this.totalPages);
            return Array.from({ length: end - start + 1 }, (_, i) => start + i);
        },

        // Pagination methods
        changePage(page) { this.currentPage = page; },
        prevPage() { if (this.currentPage > 1) this.currentPage--; },
        nextPage() { if (this.currentPage < this.totalPages) this.currentPage++; },

        // Helper methods
        getMonthName(monthValue) {
            if (monthValue === 'all') return 'Semua Bulan';
            const month = this.months.find(m => m.value === monthValue);
            return month ? month.label : monthValue;
        },

        formatCurrency(value) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(value);
        },

        // Optimized totals calculation (use server data when available)
        calculateTotals() {
            // Skip if server already provided totals
            if (this.totalDebit !== 0 || this.totalCredit !== 0) {
                return;
            }

            // Fallback client-side calculation
            this.totalDebit = 0;
            this.totalCredit = 0;

            this.allData.forEach(jurnal => {
                if (jurnal.details?.length) {
                    jurnal.details.forEach(detail => {
                        this.totalDebit += parseFloat(detail.debit || 0);
                        this.totalCredit += parseFloat(detail.credit || 0);
                    });
                }
            });
        },

        // Fetch available months
        async fetchAvailableMonths() {
            try {
                const response = await fetch('/jurnal/months', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json',
                    }
                });

                if (!response.ok) throw new Error(`HTTP ${response.status}`);

                const result = await response.json();

                if (result.status === 'success') {
                    this.months = Array.isArray(result.data) ? result.data : [];
                }
            } catch (error) {
                console.error('Error fetching months:', error);
                // Fallback to current year
                const year = new Date().getFullYear();
                const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                                   'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

                this.months = monthNames.map((name, i) => ({
                    value: `${year}-${String(i + 1).padStart(2, '0')}`,
                    label: `${name} ${year}`
                }));
            }
        },

        // Fetch jurnal data (optimized)
        async fetchJurnalData() {
            this.isLoading = true;
            this.hasError = false;
            this.errorMessage = '';

            try {
                const response = await fetch('/jurnal/data', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json',
                    }
                });

                if (!response.ok) throw new Error(`HTTP ${response.status}`);

                const result = await response.json();

                if (result.status === 'success') {
                    this.allData = Array.isArray(result.data) ? result.data : [];
                    this.totalItems = result.total || 0;
                    this.totalPages = Math.ceil(this.totalItems / this.itemsPerPage);

                    // Use server totals
                    if (result.totals) {
                        this.totalDebit = result.totals.debit || 0;
                        this.totalCredit = result.totals.credit || 0;
                    } else {
                        this.calculateTotals();
                    }
                } else {
                    this.allData = [];
                    this.totalItems = 0;
                    this.totalPages = 0;
                }
            } catch (error) {
                console.error('Error fetching jurnal:', error);
                this.allData = [];
                this.totalItems = 0;
                this.totalPages = 0;
                this.hasError = true;

                if (error.message.includes('401') || error.message.includes('403')) {
                    this.errorMessage = 'Sesi Anda telah berakhir';
                    setTimeout(() => window.location.reload(), 2000);
                } else {
                    this.errorMessage = 'Gagal memuat data jurnal';
                }
            } finally {
                this.isLoading = false;
            }
        },

        // Fetch filtered data (optimized)
        async fetchFilteredData(filters = {}) {
            this.isSearching = true;
            this.hasError = false;

            try {
                const params = new URLSearchParams();

                if (filters.jenis && filters.jenis !== 'all') params.append('jenis', filters.jenis);
                if (filters.search) params.append('search', filters.search);
                if (filters.month && filters.month !== 'all') params.append('month', filters.month);
                if (filters.start_date) params.append('start_date', filters.start_date);
                if (filters.end_date) params.append('end_date', filters.end_date);

                const response = await fetch(`/jurnal/data?${params.toString()}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json',
                    }
                });

                if (!response.ok) throw new Error(`HTTP ${response.status}`);

                const result = await response.json();

                if (result.status === 'success') {
                    this.allData = Array.isArray(result.data) ? result.data : [];
                    this.totalItems = result.total || 0;
                    this.totalPages = Math.ceil(this.totalItems / this.itemsPerPage);
                    this.currentPage = 1;

                    // Use server totals
                    if (result.totals) {
                        this.totalDebit = result.totals.debit || 0;
                        this.totalCredit = result.totals.credit || 0;
                    } else {
                        this.calculateTotals();
                    }
                } else {
                    this.allData = [];
                    this.totalItems = 0;
                    this.totalPages = 0;
                    this.totalDebit = 0;
                    this.totalCredit = 0;
                }
            } catch (error) {
                console.error('Error fetching filtered data:', error);
                this.allData = [];
                this.totalItems = 0;
                this.totalPages = 0;
                this.totalDebit = 0;
                this.totalCredit = 0;

                if (error.message.includes('401') || error.message.includes('403')) {
                    alert('Sesi Anda telah berakhir. Silakan login kembali.');
                    window.location.reload();
                }
            } finally {
                this.isSearching = false;
            }
        },

        // Filter methods
        filterCategory(category) {
            this.selectedCategory = category;
            this.filter = category;
            this.fetchFilteredData({
                jenis: category,
                search: this.searchInput,
                month: this.selectedMonth
            });
        },

        filterByMonth(month) {
            this.selectedMonth = month;
            this.fetchFilteredData({
                jenis: this.filter,
                search: this.searchInput,
                month: month
            });
        },

        // Optimized search with debounce
        searchJurnalTable() {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                if (this.searchInput.length >= 2 || this.searchInput.length === 0) {
                    this.fetchFilteredData({
                        jenis: this.filter,
                        search: this.searchInput,
                        month: this.selectedMonth
                    });
                }
            }, 800);
        },

        // Show jurnal detail
        async showJurnalDetail(jurnalId) {
            try {
                const response = await fetch(`/jurnal/${jurnalId}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json',
                    }
                });

                if (!response.ok) throw new Error(`HTTP ${response.status}`);

                const result = await response.json();

                if (result.success) {
                    this.$dispatch('open-modal', {
                        route: `/jurnal/${jurnalId}`,
                        name: 'jurnal.show',
                        title: `Detail Jurnal - ${result.data.no_urut_transaksi}`,
                        data: result.data,
                        type: 'form',
                        method: 'GET'
                    });
                } else {
                    throw new Error(result.message || 'Failed to fetch detail');
                }
            } catch (error) {
                console.error('Error fetching detail:', error);
                alert('Gagal memuat detail jurnal: ' + error.message);
            }
        },

        // Initialize component
        init() {
            this.fetchAvailableMonths();
            this.fetchJurnalData();
        }
    }));
});
