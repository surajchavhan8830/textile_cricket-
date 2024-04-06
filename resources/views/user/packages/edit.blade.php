@section('title')
{{ Edit Package }}

@endsection
@extends('layouts.user')

@section('content')




<main id="main" class="main">
    <div class="row">
        <div class="pagetitle col-lg-6 pt-2">
            <h1>Edit Package</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                    <li class="breadcrumb-item">Package</li>
                    <li class="breadcrumb-item active">Edit Package</li>
                </ol>
            </nav>
        </div>


        <div class="pagetitle col-lg-6 text-end pt-2 justify-content-center">

            <a href="{{ route('user_customer')}}"><button type="button" class="btn btn-primary"><i
                        class="bi bi-arrow-left-square"></i> Back</button></a>

        </div>

    </div>
    <!-- End Page Title -->

    <div>
        @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Create</h5>

                        <!-- General Form Elements -->
                        <form action="{{ route('update_package', $package->id)}}" method="post">
                            @csrf
                            <div class="row mb-3">
                                <label for="inputNumber" class="col-sm-2 col-form-label">User</label>
                                <div class="col-sm-10">
                                    <!-- <select class="form-control" id="user_id" name="user_id">
                                    <option value="0" selected>Select Package</option>
                                        @foreach(App\Models\User::where('') as $user)
                                        <option value="{{ $user->id }}">
                                            {{ $user->name }}
                                        </option>
                                        @endforeach
                                </select> -->

                                    <input type="text" value="{{ $user->name }}" class="form-control" disabled>
                                    @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>


                            </div>

                            <div class="row mb-3 d-none">
                                <label for="inputNumber" class="col-sm-2 col-form-label">User</label>
                                <div class="col-sm-10">
                                    <input type="text" value="{{ $user->id }}" class="form-control" name="user_id">
                                    @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- <div class="row mb-3">
                                <label for="inputNumber" class="col-sm-2 col-form-label">Package
                                    {{--$newpackage->id--}}</label>
                                <div class="col-sm-10">
                                    <select class="form-control" id="package_id" name="package_id">
                                        <option value="1">Select Package</option>
                                        <option value="" >
                                        </option>
                                    </select>
                                    @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                            </div> -->

                            <div class="row mb-3">
                                <label for="inputText" class="col-sm-2 col-form-label">Strating Date</label>
                                <div class="col-sm-10">
                                    <input class="form-control datepicker" value="{{ $package->starting_date }}"
                                        name="starting_date" id="starting_date" type="text" placeholder="Strating Date">
                                    @error('starting_date')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>


                            </div>


                            <div class="row mb-10">
                                <label for="inputText" class="col-sm-2 col-form-label">Ending Date</label>
                                <div class="col-sm-5">
                                    <input class="form-control datepicker" id="date" type="text" name="ending_date"
                                        value="{{$package->ending_date}}" placeholder="Ending Date">
                                    @error('ending_date')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                            </div>

                           

                            <div class="row mb-3">
                                <label for="weft_yarn" class="col-sm-2 col-form-label">Amount</label>
                                <div class="col-sm-10">
                                    <input class="form-control" value="{{$package->amount}}" id="weft_yarn" type="text"
                                        name="amount" placeholder="Amount" value="" required>
                                    @error('amount')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="width" class="col-sm-2 col-form-label">Notes</label>
                                <div class="col-sm-10">
                                    <input class="form-control" value="{{$package->notes}}" id="width" type="text"
                                        name="notes" placeholder="Notes">
                                    @error('notes')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>


                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label"></label>
                                <div class="col-sm-10">
                                    <button type="submit" class="btn btn-primary">Update Package</button>
                                </div>
                            </div>

                        </form><!-- End General Form Elements -->

                    </div>
                </div>

            </div>


        </div>


        <script type="text/javascript">
        var url = "{{ route('create_package', $user->id) }}";
        $("#package_id").change(function() {
            window.location.href = url + "?package=" + $(this).val();
            // var selectedLang = $(this).data('lang');
            window.location.href = url + "?lang=" + selectedLang;
        });
        </script>
    </section>

</main><!-- End #main -->

@endsection