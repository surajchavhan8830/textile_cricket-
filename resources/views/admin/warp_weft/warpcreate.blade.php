@extends('layouts.admin')

@section('content')

<main id="main" class="main">

    <div class="pagetitle">
        <h1>WARP</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                <li class="breadcrumb-item">Fabric</li>
                <li class="breadcrumb-item active">Warp</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-lg-12">

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">WARP</h5>
                      
                        <div class="row">
                            <div class="col-lg-12 col-12">

                                @php
                                $x = 1;
                                @endphp

                                <form action="{{ route('fabricCost.added')}}" method="post">
                                    @csrf
                                    @for ($i = 0; $i < $warp_yarn->warp_yarn; $i++)

                                        <!-- card -->
                                        <div class="card mb-6 shadow border-0">
                                            <!-- card body -->
                                            <div class="card-body p-6 ">
                                                <div class="mb-4 d-flex">
                                                </div>
                                                <h4 class="mb-4 h5 mt-5">WARP-{{$x++}}</h4>

                                                <div class="row">
                                                    <!-- input -->
                                                    <div class="row">
                                                        <div class="mb-3 col-lg-6 pb-2 pt-2">
                                                            <label for="category_id">Select Yarn Category</label>
                                                            
                                                            <select class="form-select fabric_category_id" name="fabric_category_id[]"
                                                                id="fabric_category_id">
                                                                <option selected>Yarn Name</option>
                                                                @forelse($yarns as $yarn)
                                                                <option value="{{$yarn->yarn_name}}" 
                                                                    {{ Request()->fabric_category_id == $yarn->id ? 'selected' : '' }}>
                                                                    {{$yarn->yarn_name}}</option>
                                                                @empty
                                                                <option value="0">Default</option>
                                                                @endforelse
                                                            </select>
                                                        </div>

                                                        <!-- input -->
                                                        <div class="mb-3 col-lg-6 pb-2 pt-2">
                                                            <label for="yarn_denier">Enter Ends (Taar) </label>
                                                            <input class="form-control" id="warp_yarn" type="text"
                                                                name="ends[]" placeholder="Enter Number of Warp Yarn"
                                                                required>

                                                        </div>

                                                        <div class="mb-3 col-lg-6 pb-2 pt-2">
                                                            <input class="form-control" id="warp_yarn" type="text"
                                                                name="fabric_cost_id[]" value="{{ $warp_yarn->id }}"
                                                                placeholder="Enter Number of Warp Yarn" required>

                                                        </div>

                                                        <div class="mb-3 col-lg-6 pb-2 pt-2">
                                                            <input class="form-control" id="warp_yarn" type="text"
                                                                name="fabric_name[]"
                                                                value="{{ $warp_yarn->fabric_name }}"
                                                                placeholder="Enter Number of Warp Yarn" required>

                                                        </div>

                                                       

                                                    </div>

                                                    <div>

                                                    </div>
                                                    <!-- input -->




                                                </div>
                                            </div>

                                        </div>
                                        @endfor
                                        <div class="col-lg-12">
                                            <button type="submit" class="btn btn-primary form-control">Save
                                                Yarn</button>

                                        </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

   

</main><!-- End #main -->


@endsection


@section('js')

<script type="text/javascript">
  $('.fabric_category_id').on('change', function(){
  });
</script>

@endsection