    @extends('layouts.user')
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

            <div class="pagetitle col-lg-6 text-end pt-3 justify-content-center">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search by Mobile Number" name="search" id="search">
                </div>
            </div>

        </div>
        <!-- End Page Title -->

        <section class="section">
            <div class="row">
                <div class="col-lg-12">

                    <div class="card p-2 pt-4">
                        <div class="card-body">

                            <table class="table data-table table-striped table-bordered table-smx table-hover table-responsive">
                                <thead style="">
                                    <tr>
                                        <th>Sr#</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Mobile Number</th>
                                        <th>Company Name</th>
                                        <th>Location</th>
                                        <th>Create at</th>
                                        <th>Active Status</th>
                                        <th>Packages</th>
                                    </tr>
                                </thead>
                                @if(session('id') == 0)
                                <tbody id="tbody">
                                    @php
                                    $i = 1;
                                    @endphp
                                    @foreach($customers as $customer)
                                    <tr>
                                        <td>{{ $i++ }}</td>
                                        <td>{{ $customer->name }}</td>
                                        <td>{{ $customer->email}}</td>
                                        <td>{{ $customer->mobile_number}}</td>
                                        <td>{{ $customer->company_name}}</td>
                                        <td>{{ $customer->city}}</td>
                                        <td>{{ $customer->created_at}}</td>
                                        <td>{{$customer->updated_at}}</td>
                                        <td class="text-center">
                                            <a class="dropdown-item" href="{{ route('package_single', $customer->id )}}"><i
                                                    class="bi bi-eye me-3 "></i></a>

                                        </td>

                                    </tr>
                                    @endforeach
                                    @else
                                    <h1>no data</h1>

                                    @endif
                                </tbody>

                            </table>

                        </div>
                    </div>

                </div>
                <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
                <script type="text/javascript">
                $(document).ready(function() {
                    $("#search").on('keyup', function() {
                        var value = $(this).val();
                        $.ajax({
                            url: "{{route('user_customer')}}",
                            type: "GET",
                            data: {
                                'search': value
                            },
                            success: function(data) {
                                var customers = data.customers;
                                var html = '';
                                // alert(customers.lenght);
                                // alert(customers.length);
                                if (customers.length > 0) {
                                    for (let i = 0; i < customers.length; i++) {
                                        var show = '/users_package/' + customers[i]['id'];

                                        

                                        html += '<tr>\
                                        <td>' + customers[i]['id'] + '</td>\
                                        <td>' + customers[i]['name'] + '</td>\
                                        <td>' + customers[i]['email'] + '</td>\
                                        <td>' + customers[i]['mobile_number'] + '</td>\
                                        <td>' + customers[i]['company_name'] + '</td>\
                                        <td>' + customers[i]['city'] + '</td>\
                                        <td>' + customers[i]['created_at'] + '</td>\
                                        <td class="text-center">\
                                        <a href="' + show + '" ><i class="bi bi-eye me-3 "></i></a>\
                                        </td>'

                                    } 
                                }else {
                                        html += '<tr>\
                                        <td colspan="9" class="text-center">No Customer Found</td>\
                                    </tr>'
                                    }
                                    $("#tbody").html(html);
                            }
                        });
                    });
                });
                </script>
            </div>

        </section>


    </main><!-- End #main -->
    @section('content')

    @endsection