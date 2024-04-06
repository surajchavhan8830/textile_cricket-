@extends('layouts.admin')

@section('content')




<main id="main" class="main">
    <div class="row">
        <div class="pagetitle col-lg-6 pt-2">
            <h1>Add Yarn</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                    <li class="breadcrumb-item">Yarn</li>
                    <li class="breadcrumb-item active">Add Yarn</li>
                </ol>
            </nav>
        </div>

        <div class="pagetitle col-lg-6 text-end pt-2 justify-content-center">

            <a href="{{ route('yarn.index')}}"><button type="button" class="btn btn-primary"><i
                        class="bi bi-arrow-left-square"></i> Back</button></a>

        </div>

    </div>
    <!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-lg-12">

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Create Yarn</h5>

                        <!-- General Form Elements -->
                        <form action="{{ route('customer.update', $customer->id)}}" method="post">
                            @csrf
                            @method('PUT')
                            <div class="row mb-3">
                                <label for="inputNumber" class="col-sm-2 col-form-label">Customer Name</label>
                                <div class="col-sm-10">
                                    <input class="form-control" id="yarn_name" type="text" name="name"
                                        value="{{ $customer->name}}" required>

                                    <!-- @error('company_name')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror -->
                                </div>

                            </div>
                            <div class="row mb-3">
                                <label for="inputText" class="col-sm-2 col-form-label">Customer Email</label>
                                <div class="col-sm-10">
                                    <input class="form-control" id="yarn_denier" type="text" name="email"
                                        value="{{ $customer->email }}" required>
                                </div>
                            </div>

                            <!-- <div class="row mb-3">
                                <label for="inputText" class="col-sm-2 col-form-label">Password</label>
                                <div class="col-sm-10">
                                    <input class="form-control" id="yarn_denier" type="text" name="password"
                                        value="123456" required>
                                </div>
                            </div> -->

                            <div class="row mb-3">
                                <label for="inputText" class="col-sm-2 col-form-label">mobile Number</label>
                                <div class="col-sm-10">
                                    <input class="form-control" id="yarn_denier" type="text" name="mobile_number"
                                        value="{{ $customer->mobile_number}}" required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label"></label>
                                <div class="col-sm-10">
                                    <button type="submit" class="btn btn-primary">Submit Form</button>
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