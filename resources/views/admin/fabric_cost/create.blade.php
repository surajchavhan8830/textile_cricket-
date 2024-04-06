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

            <a href="#"><button type="button" class="btn btn-primary"><i
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
                        <form action="{{ route('fabricCost.store')}}" method="post">
                            @csrf
                            <div class="row mb-3">
                                <label for="inputNumber" class="col-sm-2 col-form-label">Fabric Name</label>
                                <div class="col-sm-10">
                                    <input class="form-control" id="fabric_name" type="text" name="fabric_name"
                                        placeholder="Enter Fabric Name" required>
                                    @error('fabric_name')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                            </div>
                            <div class="row mb-3">
                                <label for="inputText" class="col-sm-2 col-form-label">Warp Yarn</label>
                                <div class="col-sm-10">
                                    <input class="form-control" id="warp_yarn" type="text" name="warp_yarn"
                                        placeholder="Enter Number of Warp Yarn" required>
                                    @error('warp_yarn')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="weft_yarn" class="col-sm-2 col-form-label">Weft Yarn</label>
                                <div class="col-sm-10">
                                <input class="form-control" id="weft_yarn" type="text" name="weft_yarn"
                                        placeholder="Enter Number of Weft Yarn" required>
                                    @error('weft_yarn')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                          


                            <div class="row mb-3">
                                <label for="width" class="col-sm-2 col-form-label">Fabric width</label>
                                <div class="col-sm-10">
                                <input class="form-control" id="width" type="text" name="width"
                                        placeholder="Enter Fabric Width in Inch" required>
                                    @error('width')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="final_ppi" class="col-sm-2 col-form-label">Cost of Per Final PPI</label>
                                <div class="col-sm-10">
                                <input class="form-control" id="final_ppi" type="text" name="final_ppi"
                                        placeholder="Enter Number of Cost of Per Final PPI" required>
                                    @error('final_ppi')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>



                            <div class="row mb-3">
                                <label for="warp_wastage" class="col-sm-2 col-form-label">Warp Westage</label>
                                <div class="col-sm-10">
                                <input class="form-control" id="warp_wastage" type="text" name="warp_wastage"
                                        placeholder="Enter Warp Wastage In % on Warp Amount" required>
                                    @error('warp_wastage')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="Weft Westage" class="col-sm-2 col-form-label">Weft Westage</label>
                                <div class="col-sm-10">
                                <input class="form-control" id="weft_wastage" type="text" name="weft_wastage"
                                        placeholder="Enter Weft Wastage In % on Warp Amount" required>
                                    @error('Weft Westage')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>



                            <div class="row mb-3">
                                <label for="butta_cutting_cost" class="col-sm-2 col-form-label">Butta Cutting Cost</label>
                                <div class="col-sm-10">
                                <input class="form-control" id="butta_cutting_cost" type="text" name="butta_cutting_cost"
                                        placeholder="Enter Number of Weft Yarn" required>
                                    @error('butta_cutting_cost')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="additional_cost" class="col-sm-2 col-form-label">Any Additional Cost</label>
                                <div class="col-sm-10">
                                <input class="form-control" id="additional_cost" type="text" name="additional_cost"
                                        placeholder="Enter Number of Weft Yarn" required>
                                    @error('additional_cost')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>



                            <div class="row mb-3">
                                <label for="warp_wastage" class="col-sm-2 col-form-label">Warp Westage</label>
                                <div class="col-sm-10">
                                <input class="form-control" id="warp_wastage" type="text" name="warp_wastage"
                                        placeholder="Enter Number of Weft Yarn" required>
                                    @error('warp_wastage')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="fabric_category_id" class="col-sm-2 col-form-label">Select Yarn Category</label>
                                <div class="col-sm-10">
                                    <select class="form-select" name="fabric_category_id" id="fabric_category_id">
                                        <option selected>Category Name</option>
                                        @foreach(App\Models\YarnCategory::all() as $yarn_category)
                                        <option value="{{ $yarn_category->id }}">{{ $yarn_category->yarn_category }}
                                        </option>

                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <!-- <div class="row mb-3">
                                <label for="inputPassword" class="col-sm-2 col-form-label">Enter Yarn Rate <Address>
                                    </Address>
                                </label>
                                <div class="col-sm-10">
                                    <input class="form-control" id="yarn_rate" type="text" name="yarn_rate"
                                        placeholder="Enter Yarn Rate (Including GST)" required>

                                </div>
                            </div> -->







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