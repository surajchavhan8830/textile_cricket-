@extends('layouts.admin')

@section('content')




<main id="main" class="main">
    <div class="row">
        <div class="pagetitle col-lg-6 pt-2">
            <h1>Add Yarn</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                    <li class="breadcrumb-item">Package</li>
                    <li class="breadcrumb-item active">Add Package</li>
                </ol>
            </nav>
        </div>

        <div class="pagetitle col-lg-6 text-end pt-2 justify-content-center">

            <a href="{{ route('packagelist.index')}}"><button type="button" class="btn btn-primary"><i
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
                        <form action="{{ route('packagelist.store')}}" method="post">
                            @csrf

                            <div class="row mb-3">
                                <label for="inputNumber" class="col-sm-2 col-form-label">Package</label>
                                <div class="col-sm-10">
                                <select class="form-control" id="user_id" name="user_id">
                                    <option value="0" selected>Select Package</option>
                                        @foreach(App\Models\User::all() as $user)
                                        <option value="{{ $user->id }}">
                                            {{ $user->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="inputNumber" class="col-sm-2 col-form-label">Package</label>
                                <div class="col-sm-10">
                                <select class="form-control" id="package_id" name="package_id">
                                    <option value=" " selected>Select Package</option>
                                        @foreach(App\Models\Packagelist::all() as $pkg)
                                        <option value="{{ $pkg->id }}">
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
                                    <input class="form-control" id="warp_yarn" type="date" name="starting_date" placeholder="Strating Date" required>
                                    @error('starting_date')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            

                            <div class="row mb-3">
                                <label for="inputText" class="col-sm-2 col-form-label">Ending Date</label>
                                <div class="col-sm-10">
                                    <input class="form-control" id="warp_yarn" type="date" name="ending_date" placeholder="Ending Date"  required>
                                    @error('starting_date')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="inputText" class="col-sm-2 col-form-label">Payment Method</label>
                                <div class="col-sm-10">
                                    <input class="form-control" id="warp_yarn" type="text" name="payment_method"
                                        placeholder="Payment Method" required>
                                    @error('description')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="weft_yarn" class="col-sm-2 col-form-label">Amount</label>
                                <div class="col-sm-10">
                                <input class="form-control" id="weft_yarn" type="text" name="amount"
                                        placeholder="Amount" required>
                                    @error('amount')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                          


                            <div class="row mb-3">
                                <label for="width" class="col-sm-2 col-form-label">Notes</label>
                                <div class="col-sm-10">
                                <input class="form-control" id="width" type="text" name="notes"
                                        placeholder="Notes" required>
                                    @error('notes')
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