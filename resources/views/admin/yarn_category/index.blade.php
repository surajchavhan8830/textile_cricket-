@extends('layouts.admin')

@section('content')


<!-- main -->

<main id="main" class="main">
    <div class="row">
        <div class="pagetitle col-lg-6 pt-2">
            <h1>Yarn List</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                    <li class="breadcrumb-item">Yarn List</li>
                    <!-- <li class="breadcrumb-item active">Data</li> -->
                </ol>
            </nav>
        </div>

        <div class="pagetitle col-lg-6 text-end pt-2 justify-content-center">
            <a href="{{ route('yarn_category.create')}}"><button type="button" class="btn btn-primary"><i
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
                                    <th style="width: 2%;">Sr#</th>
                                    <th>Name</th>
                                    <th>Create at</th>
                                    <th>Status</th>

                                    <th colspan="2" class="text-center" style="width: 10%;">Action</th>

                                </tr>
                            </thead>
                            @php
                                    $i = 1;
                                    @endphp

                                    @foreach($categorys as $category)



                                    <tr>

                                        <!-- <td>
                          <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="productTen">
                            <label class="form-check-label" for="productTen">

                            </label>
                          </div>
                        </td> -->
                                        <td>
                                            {{  $i++ }}
                                        </td>   
                                        <td><a href="#" class="text-reset">{{ $category->yarn_category }}</a></td>

                                     
                                        <td>{{ date('d-m-y', strtotime($category->created_at))}}</td>
                                        <td>
                                            <span class="badge bg-primary text-light">Active</span>
                                        </td>
                                        <td class="text-center">
                                        <a class="dropdown-item" href="{{ route('yarn_category.edit', $category->id )}}"><i
                                                                class="bi bi-pencil-square me-3 "></i></a>
                                        
                                        </td>
                                        <td class="text-center">
                                        <form
                                            action="{{route('yarn_category.destroy', $category->id)}}" method="post">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="dropdown-item btn-remove">  <a class="dropdown-item" href="#"><i
                                                                class="bi bi-trash me-3"></i></a>

                                        </form>
                                        <!-- <a class="dropdown-item" href="#"><i
                                                                class="bi bi-trash me-3"></i></a> -->
                                        </td>
                                    </tr>
                                    @endforeach



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