@extends('layouts.admin')

@section('content')


<!-- main -->

<main id="main" class="main">
    <div class="row">
        <div class="pagetitle col-lg-6 pt-2">
            <h1>Customer List</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard')}}">Home</a></li>
                    <li class="breadcrumb-item">Customer List</li>
                    <!-- <li class="breadcrumb-item active">Data</li> -->
                </ol>
            </nav>
        </div>

        <div class="pagetitle col-lg-6 text-end pt-2 justify-content-center">
            <a href="{{ route('customer.create')}}"><button type="button" class="btn btn-primary"><i class="bi bi-plus-square"></i> Add</button></a>
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
                                    <th>Email</th>
                                    <th>Password</th>
                                    <th>Mobile Number</th>
                                    <th>Create at</th>
                                    <th>Packages</th>
                                    <th colspan="2" class="text-center" style="width: 10%;">Action</th>

                                </tr>
                            </thead>
                            @if(session('id') == 11)
                            <tbody>
                                @php
                                $i = 1;
                                @endphp
                                @foreach($customers as $customer)
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    <td>{{ $customer->name }}</td>
                                    <td>{{ $customer->email}}</td>
                                    <td>123456</td>
                                    <td>{{ $customer->mobile_number}}</td>
                                    <td>{{ $customer->created_at}}</td>

                                    <td class="text-center">
                                        <a class="dropdown-item" href="{{ route('packagelist.user_package', $customer->id )}}"><i class="bi bi-eye me-3 "></i></a>

                                    </td>
                                    <td class="text-center">
                                        <a class="dropdown-item" href="{{ route('customer.edit', $customer->id )}}"><i class="bi bi-pencil-square me-3 "></i></a>

                                    </td>
                                    <td class="text-center">
                                        <form action="{{route('customer.destroy', $customer ->id)}}" method="post">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item btn-remove"> <i class="bi bi-trash me-3"></i>
                                            </button>
                                        </form>
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
        </div>
    </section>

</main><!-- End #main -->


@endsection