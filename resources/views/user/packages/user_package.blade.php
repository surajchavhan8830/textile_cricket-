@extends('layouts.user')

@section('content')


<!-- main -->

<main id="main" class="main">
    <div class="row">
        <div class="pagetitle col-lg-6 pt-2">
            <h1>Package List</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                    <li class="breadcrumb-item">Package List</li>
                    <!-- <li class="breadcrumb-item active">Data</li> -->
                </ol>
            </nav>
        </div>

        @if(session('id') == 0)
        <div class="pagetitle col-lg-6 text-end pt-2 justify-content-center">
            <button type="button" class="btn btn-primary add_package" id="add_package"><i
                    class="bi bi-plus-square"></i>Add</button>
        </div>
        @else
        <div class="pagetitle col-lg-6 text-end pt-2 justify-content-center">
            <a href="{{ route('packagelist.create')}}"><button type="button" class="btn btn-primary"><i
                        class="bi bi-plus-square"></i> Add</button></a>
        </div>
        @endif


    </div>
    <!-- End Page Title -->

    <section class="section">

        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <table class="table">
                        <tbody>
                            <tr>
                                <th scope="row">Customer Name</th>
                                <td>{{$user->name}}</td>
                            </tr>
                            <tr>
                                <th scope="row">Company Name</th>
                                <td>{{ $user->company_name }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Email</th>
                                <td>{{ $user->email}}</td>
                            </tr>
                            <tr>
                                <th scope="row">Phone</th>
                                <td>{{ $user->mobile_number}}</td>
                            </tr>
                            <tr>
                                <th scope="row">Location</th>
                                <td>{{$user->city}}</td>
                            </tr>

                        </tbody>
                    </table>
                </div>

            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card p-2 pt-4">
                    <div class="card-body">
                        <table
                            class="table data-table table-striped table-bordered table-smx table-hover table-responsive-sm">
                            <thead>
                                <tr>
                                    <th>Sr.no</th>
                                    <th>User Name</th>
                                    <th>Package Name</th>
                                    <th>Amount Rate</th>
                                    <th>Starting Date</th>
                                    <th>Ending Date</th>
                                    <th>Notes</th>
                                    <th>Method</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                    <!-- <th colspan="2" style="width: 10%;" class="text-center">Action</th> -->
                                </tr>
                            </thead>

                            <tbody>
                                @php
                                $i = 1;
                                @endphp
                                @foreach($packages as $package)
                                @php
                                $today = \Illuminate\Support\Carbon::now()->toDateString();
                                @endphp
                                <tr>
                                    <td>
                                        {{ $i++ }}
                                    </td>
                                    <td><a href="#" class="text-reset">{{ $package->user->name }}</a></td>
                                    <td>{{ $package->package->name }}</td>
                                    <td>{{ $package->package->amount }}</td>
                                    <td>{{ date('d-m-y H:i:s', strtotime( $package->starting_date))}}</td>
                                    <td>{{ date('d-m-y H:i:s', strtotime( $package->ending_date))}}</td>
                                    <td>{{$package->notes}}</td>
                                    <td>{{ $package->payment_method }}</td>
                                    <td>
                                            @if($package->starting_date <= $today && $package->ending_date >= $today)
                                            <span class="badge bg-success text-light">Active</span>
                                            @else
                                            <span class="badge bg-warning text-warning">Active</span>
                                            @endif
                                    </td>
                                    <td>
                                    <!-- <button type="button" class="btn btn-primary btn-xs">Xl</button> -->
                                   <a href="{{route('edit_package', $package->id)}}"> <span class="badge bg-info bg-danger">Edit</span> </a>

                                    </td>
                                    @endforeach

                                </tr>
                            </tbody>
                        </table>
                        <!-- End Table with stripped rows -->

                    </div>
                </div>
                <script type="text/javascript">
                var url = "{{ route('create_package', $user->id) }}";
                $("#add_package").click(function() {
                    window.location.href = url + "?package=" + 1;
                    // window.location.href = url + "?lang=" + selectedLang;
                });
                </script>
            </div>
        </div>

    </section>

</main><!-- End #main -->


@endsection