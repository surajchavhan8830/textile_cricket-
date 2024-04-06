@extends('layouts.admin')

@section('content')

<main id="main" class="main">

<div class="pagetitle">
  <h1>Dashboard</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.html">Home</a></li>
      <li class="breadcrumb-item">Dashboard</li>
      <!-- <li class="breadcrumb-item active">Blank</li> -->
    </ol>
  </nav>
</div><!-- End Page Title -->

<section class="section">
  <div class="row">
    <div class="col-lg-12">

      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Dashboard  </h5>
          @if (!session('is_customer') == 0) 
            <h5 class="card-title text-danger text-center py-5">"Hurry! Your free trial expires in " <b>{{ $daysRemaining }}</b>   " days."  </h5>
          @endif

          &nbsp; <br>
          &nbsp; <br>

          &nbsp; <br>

          &nbsp; <br>
          &nbsp; <br>

          &nbsp; <br>
          &nbsp; <br>
          &nbsp; <br>
          &nbsp; <br>
          &nbsp; <br>
          &nbsp; <br>
          &nbsp; <br>
          &nbsp; <br>
          &nbsp; <br>
          &nbsp; <br>
          &nbsp; <br>
          &nbsp; <br>
          &nbsp; <br>
          &nbsp; <br>
          &nbsp; <br>
          &nbsp; <br>
          &nbsp; <br>


        </div>
      </div>

    </div>

    
  </div>
</section>

</main><!-- End #main -->


@endsection