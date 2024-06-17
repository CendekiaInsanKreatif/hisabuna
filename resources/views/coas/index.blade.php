<x-app-layout>
    @section('content')
        <div class="px-4 pt-4">
            <p class="font-medium text-emerald-500 text-lg">Chart Of Accounts</p>
            <div class="mb-4">
                <input type="text" id="search-input" class="form-input block w-full mt-1" placeholder="Cari Nama Akun COA" oninput="searchTable()">
            </div>
        </div>
        <div class="card bg-gray-50 rounded-xl border border-zinc-200">
            <div class="card-body">
                <table class="w-full" id="coaTable">
                    <thead>
                        <tr>
                            <th class="bg-gray-100 px-4 py-2 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider cursor-pointer" onclick="sortTableByColumn('nomor_akun')">
                                <div class="flex items-center">
                                    Nomer Akun
                                    <span class="ml-2">
                                        <img src="{{ asset('images/icons/ic-sort.svg') }}" class="w-4 h-4 sort-icon" data-sort="none">
                                    </span>
                                </div>
                            </th>
                            <th class="bg-gray-100 px-4 py-2 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                                <div class="flex items-center">
                                    Nama Akun
                                    <span class="ml-2">
                                        <img src="{{ asset('images/icons/ic-sort.svg') }}" class="w-4 h-4 sort-icon" data-sort="none">
                                    </span>
                                </div>
                            </th>
                            <th class="bg-gray-100 px-4 py-2 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                                <div class="flex items-center">
                                    Level Akun
                                    <span class="ml-2">
                                        <img src="{{ asset('images/icons/ic-sort.svg') }}" class="w-4 h-4 sort-icon" data-sort="none">
                                    </span>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="coaTableBody">
                    </tbody>
                </table>
                <div class="pagination flex justify-center p-4 space-x-2">
                    <button class="prev bg-emerald-600 text-white py-1 px-3 rounded" onclick="prevPage()">Previous</button>
                    <div id="pageNumbers" class="flex space-x-2"></div>
                    <button class="next bg-emerald-600 text-white py-1 px-3 rounded" onclick="nextPage()">Next</button>
                </div>
            </div>
        </div>
    @endsection
    @push('script')
        <script type="module">
            let currentPage = 1;
            const rowsPerPage = 11;
            const tableBody = document.getElementById('coaTableBody');
            let totalRows = 0;
            let totalPage = 0;
            const pageNumbers = document.getElementById('pageNumbers');
            let sortDirection = 'asc';
            let filter = 'all';
            let searchInput = document.getElementById('search-input');
            const overlay = document.getElementById('overlay');


            async function fetchCoaData() {
                overlay.style.display = 'flex';
                await new Promise(resolve => setTimeout(resolve, 2000));
                try {
                    const response = await fetch('/api/coas');
                    const data = await response.json();
                    renderCoaTable(data);
                    overlay.style.display = 'none';
                } catch (error) {
                    console.error('Error fetching COA data:', error);
                    overlay.style.display = 'none';
                }
            }

            function renderCoaTable(data) {
                tableBody.innerHTML = '';
                data.forEach(coa => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="text-left px-4 py-1">${coa.nomor_akun}</td>
                        <td class="text-left px-4 py-1">${coa.nama_akun}</td>
                        <td class="text-left px-4 py-1">${coa.level}</td>
                    `;
                    row.addEventListener('mouseover', function() {
                        this.classList.add('hover:bg-gray-100');
                    });
                    row.addEventListener('mouseout', function() {
                        this.classList.remove('hover:bg-gray-100');
                    });
                    tableBody.appendChild(row);
                });
                totalRows = data.length;
                totalPage = Math.ceil(totalRows / rowsPerPage);
                setupPagination();
            }

            function prevPage() {
                if (currentPage > 1) {
                    currentPage--;
                    changePage(currentPage);
                }
            }

            function nextPage() {
                if (currentPage < totalPage) {
                    currentPage++;
                    changePage(currentPage);
                }
            }

            function changePage(page) {
                currentPage = page;
                document.querySelectorAll('.page-number').forEach(el => el.classList.remove('bg-emerald-600', 'text-gray-600'));
                document.getElementById('page-' + page).classList.add('bg-emerald-600', 'text-gray-600');

                const start = (page - 1) * rowsPerPage;
                const end = start + rowsPerPage;

                Array.from(tableBody.children).forEach((row, index) => {
                    row.style.display = (index >= start && index < end) ? '' : 'none';
                });
            }

            function setupPagination() {
                pageNumbers.innerHTML = '';
                for (let i = 1; i <= totalPage; i++) {
                    const pageButton = document.createElement('button');
                    pageButton.id = 'page-' + i;
                    pageButton.className = 'page-number py-1 px-3 rounded';
                    pageButton.textContent = i;
                    pageButton.onclick = () => changePage(i);
                    pageNumbers.appendChild(pageButton);
                }
                changePage(1);
            }

            function sortTableByColumn(column) {
                const columnIndex = column === 'nomor_akun' ? 0 : column === 'nama_akun' ? 1 : 2;
                Array.from(tableBody.children).sort((a, b) => {
                    const cellA = a.children[columnIndex].textContent;
                    const cellB = b.children[columnIndex].textContent;

                    if (sortDirection === 'asc') {
                        if (cellA < cellB) return -1;
                        if (cellA > cellB) return 1;
                    } else {
                        if (cellA > cellB) return -1;
                        if (cellA < cellB) return 1;
                    }
                    return 0;
                });

                sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
                Array.from(tableBody.children).forEach(row => tableBody.appendChild(row));
                changePage(1);
            }

            function filterTable(filterType) {
                filter = filterType;
                Array.from(tableBody.children).forEach(row => {
                    const rowType = row.getAttribute('data-type');
                    row.style.display = filter === 'all' || rowType === filter ? '' : 'none';
                });
                setupPagination();
            }

            function searchTable() {
                const searchTerm = searchInput.value.toLowerCase();
                Array.from(tableBody.children).forEach(row => {
                    const rowText = row.textContent.toLowerCase();
                    row.style.display = rowText.includes(searchTerm) ? '' : 'none';
                });
                setupPagination();
            }

            fetchCoaData();
        </script>
    @endpush
</x-app-layout>



