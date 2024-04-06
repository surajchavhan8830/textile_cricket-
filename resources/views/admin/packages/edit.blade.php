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

            <a href="#"><button type="button" class="btn btn-primary"><i class="bi bi-arrow-left-square"></i> Back</button></a>

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
                        <form action="{{ route('packagelist.update', $package->id)}}" method="post">
                            @csrf
                            @method('PUT')

                            <div class="row mb-3">
                                <label for="inputText" class="col-sm-2 col-form-label">User Name</label>
                                <div class="col-sm-10">
                                    <input class="form-control" id="warp_yarn" type="text" name="description" placeholder="Description" value="{{$package->user->name}}" 
                                    disabled>
                                    @error('description')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="inputNumber" class="col-sm-2 col-form-label">Package Name</label>
                                <div class="col-sm-10">
                                    <select class="form-control" id="package_id" name="package_id">
                                        @foreach(App\Models\Packagelist::all() as $pkg)
                                        <option value="{{ $pkg->id }}" @if($pkg->id == $package->package_id) selected @endif>
                                            {{ $pkg->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="inputText" class="col-sm-2 col-form-label">Strating Date</label>
                                <div class="col-sm-10">
                                    <input class="form-control" id="warp_yarn" type="date" name="starting_date" placeholder="Description" value="{{ $package->starting_date }}" required>
                                    @error('starting_date')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="inputText" class="col-sm-2 col-form-label">Ending Date</label>
                                <div class="col-sm-10">
                                    <input class="form-control" id="warp_yarn" type="date" name="ending_date" placeholder="Description" value="{{ $package->ending_date }}" required>
                                    @error('starting_date')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="weft_yarn" class="col-sm-2 col-form-label">Notes</label>
                                <div class="col-sm-10">
                                    <input class="form-control" id="weft_yarn" type="text" name="notes" placeholder="Amount" value="{{ $package->notes}}" required>
                                    @error('amount')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>


                            <div class="row mb-3">
                                <label for="width" class="col-sm-2 col-form-label">payment Method</label>
                                <div class="col-sm-10">
                                    <input class="form-control" id="width" type="text" name="payment_method" placeholder="Days" value="{{ $package->payment_method }}" required>
                                    @error('days')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
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