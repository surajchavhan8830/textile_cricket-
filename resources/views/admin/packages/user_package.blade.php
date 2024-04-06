@extends('layouts.admin')

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

        <div class="pagetitle col-lg-6 text-end pt-2 justify-content-center">
            <a href="{{ route('packagelist.create')}}"><button type="button" class="btn btn-primary"><i
                        class="bi bi-plus-square"></i> Add</button></a>
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

                                    <th>Sr.no</th>
                                    <th>User Name</th>
                                    <th>Package Name</th>
                                    <th>Amount Rate</th>
                                    <th>Starting Date</th>
                                    <th>Ending Date</th>
                                    <th>Notes</th>
                                    <th>Method</th>
                                    <th>Status</th>
                                    <th colspan="2" style="width: 10%;" class="text-center">Action</th>

                                </tr>
                            </thead>

                            <tbody>
                               @php
                               $i = 1;
                               @endphp
                                <tr>

                                    <td>
                                        {{  $i++ }}
                                    </td>
                                    <td><a href="#" class="text-reset">{{ $package->user->name }}</a></td>

                                  
                                    <td>{{ $package->package->name }}</td>
                                    <td>{{ $package->package->amount }}</td>
                                    <td>{{ date('d-m-y', strtotime( $package->starting_date))}}</td>
                                    <td>{{ date('d-m-y', strtotime( $package->ending_date))}}</td>
                                    <td>{{$package->notes}}</td>
                                    <td>{{ $package->payment_method }}</td>
                                    
                                    <td>
                                        <span class="badge bg-primary text-light">Active</span>
                                    </td>
                                    <td class="text-center">
                                        <a class="dropdown-item"
                                            href="{{ route('packagelist.edit', $package->id )}}"><i
                                                class="bi bi-pencil-square me-3 "></i></a>

                                    </td>

                                    <td  class="text-center">
                                        <form action="{{ route('packagelist.destroy', $package->id)}}"
                                            method="post">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item btn-remove"> <i
                                                    class="bi bi-trash me-3"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
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