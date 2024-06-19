<x-app-layout>
    @section('content')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 col-xl-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-header">
                                <h5 style="color: #34d49c"><b>Chart Of Accounts</b> <button data-bs-toggle="modal" 
                                    data-bs-target="#modal" class="bg-customGreen text-white py-2 px-4 rounded-lg shadow-none hover:bg-green-500 transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-green-300" style="background-color: #34d49c;">
                                    Tambah Coa</button></h5>
                            </div>
                            <div class="card-body">
                                <table id="table" class="table table-borderless" style="width: 100%; font-size: 12px;">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nomor Akun</th>
                                            <th>Nama Akun</th>
                                            <th>Level</th>
                                            <th>Saldo Normal</th>
                                            <th>Option</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>No</th>
                                            <th>Nomor Akun</th>
                                            <th>Nama Akun</th>
                                            <th>Level</th>
                                            <th>Saldo Normal</th>
                                            <th>Option</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog">
                <div class="modal-content">
                    <div class="modal-header" style="background-color: #34d49c;">
                        <h4 class="modal-title" style="color: white">Add Chart Of Accounts</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body modal-container">
                        <div class="container">
                            <form id="form" method="post" accept-charset="utf-8" autocomplete="off">
                                @csrf
                                <input type="hidden" name="id" id="id">
                                <div class="form-group row">
                                    <label for="Nomor Akun" class="col-xl-4 col-form-label">Nomor Akun</label>
                                    <div class="col-xl-6">
                                       <input for="Nomor Akun" id="nomor_akun" name="nomor_akun" class="form-control" aria-placeholder="Nomor Akun" title="Nomor Akun" required></input>
                                    </div>
                                </div>
                                <div class="form-group row" style="margin-top:10px;">
                                    <label for="Nama Akun" class="col-xl-4 col-form-label">Nama Akun</label>
                                    <div class="col-xl-6">
                                       <input for="Nama Akun" id="nama_akun" name="nama_akun" class="form-control" aria-placeholder="Nama Akun" title="Nama Akun" required></input>
                                    </div>
                                </div>
                                <button id="save" type="submit" hidden></button>
                            </form>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" onclick="$('#save').click()" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
        <link href="https://cdn.datatables.net/v/dt/dt-2.0.8/datatables.min.css" rel="stylesheet">
        <script src="https://cdn.datatables.net/v/dt/dt-2.0.8/datatables.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
        <script>
            $(document).ready(function() {
                $.ajaxSetup({
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                });

                let table;
                table = $('#table').DataTable({
                    serverside: true,
                    ajax:{
                        type:"get",
                        url: '{{ route("listCoa") }}',
                    },
                    language:{
                        zeroRecords: "<center> No Data Available </center>",
                    },
                    responsive: "true",
                })

                $('#form').submit(function(event) {
                    event.preventDefault();
                    if(confirm("Apakah anda yakin dengan data yang anda input ?")) {
                        var id = $('#id').val();
                        if(id) {
                            var url = "";
                        }else{
                            var url = "";
                        }

                        $.post(url,$(this).serialize()).done((res,status,xhr)=> {

                        })
                    }
                })
            })
        </script>
    @endsection
</x-app-layout>



