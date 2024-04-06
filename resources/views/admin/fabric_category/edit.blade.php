@extends('layouts.admin')

@section('content')




<main id="main" class="main">
    <div class="row">
        <div class="pagetitle col-lg-6 pt-2">
            <h1>Edit Yarn</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                    <li class="breadcrumb-item">Yarn Category</li>
                    <li class="breadcrumb-item active">Edit Yarn Category</li>
                </ol>
            </nav>
        </div>

        <div class="pagetitle col-lg-6 text-end pt-2 justify-content-center">

            <a href="{{ route('fabric_category.index')}}"><button type="button" class="btn btn-primary"><i
                        class="bi bi-arrow-left-square"></i> Back</button></a>

        </div>

    </div>
    <!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-lg-12">

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Update Yarn Category</h5>

                        <!-- General Form Elements -->
                        <form action="{{ route('fabric_category.update', $category->id)}}" method="post">
                            @csrf
                            @method('PUT')
                            <div class="row mb-3 pt-5">
                                <label for="inputNumber" class="col-sm-2 col-form-label">Fabric Category Name</label>
                                <div class="col-sm-10">
                                <input class="form-control" id="fabric_category" type="text" name="fabric_category"
                                     value="{{ $category->fabric_category }}" required>


                                    @error('yarn_category')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label"></label>
                                <div class="col-sm-10">
                                    <button type="submit" class="btn btn-primary">Update Form</button>
                                </div>
                            </div>

                        </form><!-- End General Form Elements -->

                    </div>
                </div>

            </div>


        </div>
    </section>

</main><!-- End #main -->

@endsection