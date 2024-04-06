@extends('layouts.user')
<!-- main -->

<main id="main" class="main">
    <div class="row">
        <div class="pagetitle col-lg-4 pt-2">
            <h1>Package List</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard')}}">Home</a></li>
                    <li class="breadcrumb-item">Customer List</li>
                    <!-- <li class="breadcrumb-item active">Data</li> -->
                </ol>
            </nav>
        </div>

        <div class="col-md-4 pt-3">

            <select class="form-select" id="status_id" name="status_id">
                <option value="2" {{ Request()->status_id == 2 ? 'selected' : '' }}>Active & Expired</option>
                <option value="0" {{ Request()->status_id == 0 ? 'selected' : '' }}>Active  </option>
                <option value="1" {{ Request()->status_id == 1 ? 'selected' : '' }}>Expired</option>
            </select>

            <!-- <button ><i class="bi bi-search"></i></button> -->
        </div>

        <div class="pagetitle col-lg-4 text-end pt-3 justify-content-center">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Search" name="search" id="search">
                <a href="{{ route('user_package')}}"><button class="btn btn-primary" type="button">Refresh</button></a>
            </div>
        </div>
        <!-- <div class="pagetitle col-lg-6 text-end pt-2 justify-content-center">
            <a href="{{ route('customer.create')}}"><button type="button" class="btn btn-primary"><i
                        class="bi bi-plus-square"></i> Add</button></a>
        </div> -->
    </div>

    </div>
    <!-- End Page Title -->

    <section class="section">



        <div class="row">
            <div class="col-lg-12">

                <div class="card p-2 pt-4">
                    <div class="card-body">
                        <!-- <p>Add lightweight datatables to your project with using the <a
                                href="https://github.com/fiduswriter/Simple-DataTables" target="_blank">Simple
                                DataTables</a> library. Just add <code>.datatable</code> class name to any table you
                            wish to conver to a datatable</p> -->

                        <!-- Table with stripped rows -->
                        <table class="table data-table table-striped table-bordered table-smx table-hover">
                            <thead>
                                <tr>
                                    <th style="width:2%;">Sr#</th>
                                    <th>Name</th>
                                    <th class="text-center">Starting Date</th>
                                    <th class="text-center">End Date</th>
                                    <!-- <th>Mobile Number</th> -->
                                    <th>Status</th>
                                    <!--<th>A/C Status</th>-->
                                    <th>Remaining Days</th>
                                    <th>Packages</th>
                                    <!-- <th colspan="2" class="text-center" style="width: 10%;">Action</th> -->
                                </tr>
                            </thead>
                            @if(session('id') == 0)
                            <tbody id="tbody">
                                @php
                                $i = 1;
                                @endphp
                                @foreach($packages as $package)
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    <td>{{ $package->name }}</td>
                                    <td class="text-center">{{ date('d-m-y H:i', strtotime($package->starting_date)) }}</td>
                                    <td class="text-center">{{ date('d-m-y H:i', strtotime($package->ending_date)) }}</td>

                                    @php
                                    $startDate = \Carbon\Carbon::createFromFormat('d-m-y', date('d-m-y',
                                    strtotime($package->ending_date)));
                                    $newDate = $startDate->copy()->subDays(15);
                                    $today = \Carbon\Carbon::now();


                                    $daysRemaining = $today->diffInDays($startDate);
                                    $isExpired = $startDate->isPast();
                                    $isExpiringSoon = $today->between($startDate, $newDate);
                                    @endphp

                                    @if($isExpired)
                                    <td><span class="badge bg-danger">Expired</span></td>
                                    @elseif($isExpiringSoon)
                                    <td><span class="badge bg-warning">Expiring soon</span></td>
                                    @else
                                    <td><span class="badge bg-success">Active</span></td>
                                    @endif
                                    <!--<td>-->
                                    <!--    @if($package->is_delete == 0 )-->
                                    <!--<span class="badge bg-danger">Deleted</span>-->
                                    <!--    @else-->
                                    <!--    <span class="badge bg-success">Active</span>-->
                                    <!--    @endif-->
                                    <!--</td>-->

                                    <td class="text-center">
                                        {{ $daysRemaining }}
                                    </td>

                                    <td class="text-center">
                                        <a class="dropdown-item"
                                            href="{{ route('package_single', $package->user_id )}}"><i
                                                class="bi bi-eye me-3 "></i></a>

                                    </td>

                                </tr>
                                @endforeach
                                @else
                                <h1>no data</h1>

                                @endif
                            </tbody>

                        </table>
                        <!-- End Table with stripped rows -->

                    </div>
                </div>

            </div>


            <!-- <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
            <script src="text/javascript">
                $(document).ready(function(){
                    $('#search').on('keyup', function(){
                        alert("dsfdsf");
                    });
                });
            </script> -->

            <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
            <script type="text/javascript">
            var url = "{{ route('user_package') }}";

            $(document).ready(function() {
                $("#search").on('keyup', function() {

                    var value = $(this).val();

                    var minDigits = 10;

                    if (value.length >= minDigits) {
                        window.location.href = url + "?mobile=" + value;
                    }

                });
            });

            $('#status_id').on('change', function() {

                var redirectUrl = "{{ route('user_package') }}?status_id=" + $('#status_id').val();
                window.location.replace(redirectUrl);

            });
            </script>
        </div>
    </section>
</main><!-- End #main -->
@section('content')
@endsection