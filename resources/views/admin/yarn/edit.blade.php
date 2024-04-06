@extends('layouts.admin')

@section('content')


 <main id="main" class="main">
    <div class="row">
        <div class="pagetitle col-lg-6 pt-2">
            <h1>Edit Yarn</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                    <li class="breadcrumb-item">Yarn</li>
                    <li class="breadcrumb-item active">Edit Yarn</li>
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
                        <h5 class="card-title">Edit Yarn</h5>

                        <!-- General Form Elements -->
                        <form action="{{ route('yarn.update', $yarn->id)}}" method="post">
                            @csrf
                            @method('PUT')
                            <div class="row mb-3">
                                <label for="inputNumber" class="col-sm-2 col-form-label">Yarn Name</label>
                                <div class="col-sm-10">
                                <input class="form-control" id="yarn_name" type="text" name="yarn_name"
                                        value="{{ $yarn->yarn_name }}" required>
                                    @error('yarn_name')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                            </div>
                            <div class="row mb-3">
                                <label for="inputText" class="col-sm-2 col-form-label">Yarn Denier</label>
                                <div class="col-sm-10">
                                <input class="form-control" id="yarn_denier" type="text" name="yarn_denier"
                                    value="{{ $yarn->yarn_denier }}" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="inputEmail" class="col-sm-2 col-form-label">Select Yarn Category</label>
                                <div class="col-sm-10">
                                <select class="form-select" name="category_id" id="category_id">
                                            <option value="{{$yarn->id}}">{{$yarn->yarn_category}}</option>
                                           @foreach(App\Models\YarnCategory::all() as $yarn_category)
                                           <option value="{{ $yarn_category->id }}">{{ $yarn_category->yarn_category }}</option>
                                           @endforeach
                                        </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="inputPassword" class="col-sm-2 col-form-label">Enter Yarn Rate <Address></Address>
                                </label>
                                <div class="col-sm-10">
                                <input class="form-control" id="yarn_rate" type="text" name="yarn_rate"
                                        value="{{ $yarn->yarn_rate }}" required>
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