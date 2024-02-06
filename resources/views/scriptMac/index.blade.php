@extends('layouts.app')


@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between">

                            {{ __('MAC ADDRESS LIST') }}
                            <button type="button" class="btn btn-primary btn-sm"
                                onclick="addForm(`{{ route('script.store') }}`)">ADD</button>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped" width="100%">
                                <thead>
                                    <tr>
                                        <th>NO</th>
                                        <th>Mac Address</th>
                                        <th>Computer Name</th>
                                        <th>Nama Lengkap</th>
                                        <th>Lan/Wifi</th>
                                        <th>Status</th>
                                        <th>Script</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- <div>{{ isEmpty($output) ? '' : $output}}</div> --}}
    @include('scriptMac.form')
@endsection

@push('css')
    {{-- datatables --}}
    <link href="{{ asset('assets/css/datatables/DataTables-1.13.4/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/responsive.dataTables.min.css') }}" rel="stylesheet">
@endpush

@push('script')
    <script src="{{ asset('assets/js/dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/js/dataTables.responsive.min.js') }}"></script>
    <script>
        let modal = '#modal-form';
        var dataTable = $(".table").DataTable({
            serverSide: false,
            ajax: {
                url: "{{ url()->current() }}",
            },
            columns: [{
                    data: "id",
                    name: "id",
                    searchable: false,
                    sortable: false,
                }, {
                    data: "mac_address",
                    name: "mac_address",
                    searchable: false,
                    sortable: false,
                },
                {
                    data: "computer_name",
                    name: "computer_name",
                },
                {
                    data: "username",
                    name: "username",
                },
                {
                    data: "lan_wan",
                    name: "lan_wan",
                },
                {
                    data: "status",
                    name: "status",
                },
                {
                    data: "update_script",
                    name: "update_script",
                },
                {
                    data: "aksi",
                    searchable: false,
                    sortable: false,
                    width: "150px",
                },
            ],
            responsive: true,
            autoWidth: false,
            scrollX: true,
            scrollCollapse: true,
            language: {
                paginate: {
                    next: ">", // or '→'
                    previous: "<", // or '←'
                },
            },
            error: function(xhr, error, thrown) {
                // Handle errors or exceptions that may occur during the AJAX request or table rendering process
                console.log("Error:", error);
            },
        });

        function allowData(url) {
            $.get({
                    url: url,
                    // data: new FormData(originalForm),
                })
                .done(response => {
                    Swal.fire(response.meta.message, "You clicked the button!", "success");
                    datatable.ajax.reload();
                })
                .fail(errors => {
                    if (errors.status == 422) {
                        loopErrors(errors.responseJSON.errors);
                        return;
                    }
                });
        }

        function blockData(url) {
            $.get({
                    url: url,
                    // data: new FormData(originalForm),
                })
                .done(response => {
                    Swal.fire(response.meta.message, "You clicked the button!", "success");
                    datatable.ajax.reload();
                })
                .fail(errors => {
                    if (errors.status == 422) {
                        loopErrors(errors.responseJSON.errors);
                        return;
                    }
                });
        }

        function addForm(url, title = 'Tambah') {
            $(modal).modal('show');
            $(`${modal} .modal-title`).text(title);
            $(`${modal} form`).attr('action', url);
            $(`${modal} [name=_method]`).val('post');

        }

        function editForm(url, title = 'Edit') {
            $.get(url)
                .done(response => {
                    $(modal).modal('show');
                    $(`${modal} .modal-title`).text(title);
                    $(`${modal} form`).attr('action', url);
                    $(`${modal} [name=_method]`).val('put');

                    resetForm(`${modal} form`);

                    console.log(response.data)
                    loopForm(response.data);

                    // $('#level_id')
                    // .val(response.data.level.id)
                    // .trigger('change');


                })
                .fail(errors => {
                    // Swal.fire("Something went wrong.", "You clicked the button!", "error");
                    return;
                });
        }

        $("#mac_address").on("keydown", function(event) {
            const BACKSPACE_KEY = 8;
            const COLON_KEY = 186;
            const _colonPositions = [2, 5, 8, 11, 14];
            const _newValue = $(this).val().trim();
            const _currentPosition = _newValue.length;
            if (event.keyCode === COLON_KEY) {
                event.preventDefault();
            }
            if (event.keyCode !== BACKSPACE_KEY) {
                if (_colonPositions.some((position) => position === _currentPosition)) {
                    $("#mac_address").val(_newValue.concat("-"));
                }
            }
        });


        function submitForm(originalForm) {
            $.post({
                    url: $(originalForm).attr('action'),
                    data: new FormData(originalForm),
                    dataType: 'json',
                    contentType: false,
                    cache: false,
                    processData: false
                })
                .done(response => {
                    $(modal).modal('hide');
                    Swal.fire(response.meta.message, "You clicked the button!", "success");
                    dataTable.ajax.reload();
                })
                .fail(errors => {
                    if (errors.status == 422) {
                        loopErrors(errors.responseJSON.errors);
                        return;
                    }
                });
        }

        function deleteData(url) {
            Swal.fire({
                title: "Delete?",
                text: "Please ensure and then confirm!",
                type: "warning",
                showCancelButton: !0,
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel!",
                reverseButtons: !0
            }).then(function(e) {

                if (e.value === true) {
                    $.post(url, {
                            '_method': 'delete',
                            '_token': `{{ csrf_token() }}`
                        })
                        .done(response => {
                            dataTable.ajax.reload();
                        })
                        .fail(errors => {
                            Swal.fire("Something went wrong.", "You clicked the button!", "error");
                            return;
                        });
                } else {
                    e.dismiss;
                }

            }, function(dismiss) {
                return false;
            })
        }

        function updateScript(url)
        {
            $.get(url)
                .done(response => {


                })
                .fail(errors => {
                    // Swal.fire("Something went wrong.", "You clicked the button!", "error");
                    return;
                });
        }

        function loopForm(originalForm) {
            for (field in originalForm) {

                $(`[name=${field}]`).val(originalForm[field]);

                $('select').trigger('change');
            }
        }

        function resetForm(selector) {
            $(selector)[0].reset();

            $('select').trigger('change');
            $('.form-control').removeClass('is-invalid');
            $('.invalid-feedback').remove();
        }

        function loopErrors(errors) {
            $('.invalid-feedback').remove();

            if (errors == undefined) {
                return;
            }

            for (error in errors) {
                $(`[name=${error}]`).addClass('is-invalid');

                if ($(`[name=${error}]`).hasClass('select2')) {
                    $(`<span class="error invalid-feedback">${errors[error][0]}</span>`)
                        .insertAfter($(`[name=${error}]`).next());
                } else if ($(`[name=${error}]`).hasClass('form-select')) {
                    $(`<span class="error invalid-feedback">${errors[error][0]}</span>`)
                        .insertAfter($(`[name=${error}]`).next());
                } else {
                    if ($(`[name=${error}]`).length == 0) {
                        $(`[name="${error}[]"]`).addClass('is-invalid');
                        $(`<span class="error invalid-feedback">${errors[error][0]}</span>`)
                            .insertAfter($(`[name="${error}[]"]`).next());
                    } else {
                        $(`<span class="error invalid-feedback">${errors[error][0]}</span>`)
                            .insertAfter($(`[name=${error}]`));
                    }
                }
            }
        }
    </script>
@endpush
