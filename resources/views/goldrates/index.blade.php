@extends('layouts.page')
@section('content')

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Todays Gold Rate</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('home')}}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{route('goldrates.index')}}">Today Gold Rate</a></li>
                <li class="breadcrumb-item active">Update Gold Rate</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->
    <section class="section">
        <div class="row">
            <div class="col-lg-12">


                <div class="card">

                    <div class="card-body">
                        <h5 class="card-title"><strong>Update</strong> Gold Rate</h5>

                        {{-- <div style='text-align: end' ;><a href="{{route('goldrates.index')}}" class="btn btn-primary"><i class="zmdi zmdi-arrow-left" style="padding-right: 6px;"></i><span>Back</span></a>
                    </div><br>--}}
                    @include('layouts.partials.messages')
                    <!-- General Form Elements -->
                    <form method="post" enctype="multipart/form-data" action="{{ route('goldrates.update', encrypt($goldrate->id)) }}">
                        @csrf
                        @method('patch')
                        <div class="row mb-3">
                            <label for="inputText" class="col-sm-2 col-form-label">Gold Per Gram <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" id="title" name="per_gram" class="form-control @error('per_gram') is-invalid @enderror" value="{{ old('per_gram', $goldrate->per_gram) }}" placeholder="Gold Per Gram">
                                @error('per_gram')
                                <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="inputText" class="col-sm-2 col-form-label">Gold Per Pavan <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" id="title" name="per_pavan" class="form-control @error('per_pavan') is-invalid @enderror" value="{{ old('per_pavan', $goldrate->per_pavan) }}" placeholder="Gold Per Pavan">
                                @error('per_pavan')
                                <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <fieldset class="row mb-3">
                            <legend class="col-form-label col-sm-2 pt-0">Status <span class="text-danger">*</span></legend>
                            <div class="col-sm-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="status" id="gridRadios1" value="1" {{($goldrate->status=='1'?'checked':'')}}>
                                    <label class="form-check-label" for="gridRadios1">
                                        Active
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="status" id="gridRadios2" value="0" {{($goldrate->status=='0'?'checked':'')}}>
                                    <label class="form-check-label" for="gridRadios2">
                                        Inactive
                                    </label>
                                </div>

                            </div>
                        </fieldset>
                        <div class="row mb-3">

                            <div class="col-sm-10">
                                <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
                            </div>
                        </div>

                    </form><!-- End General Form Elements -->

                </div>
            </div>
        </div>
        </div>
    </section>
    <section class="section">
        <div class="row">


            <div class="col-lg-12">

                <div class="card">
                    <div class="card-body">

                        <h5 class="card-title">List of Gold Rates</h5>

                        <!-- Table with stripped rows -->
                        

                            <form action="{{ route('goldrates.index') }}" method="GET" id="filterForm">
                                <div class="row">
                                    <div class="col-lg-3 form-group">
                                        <label class="text-muted">From Date</label>
                                        <input type="date" name="from_date" class="form-control"
                                            value="{{ request('from_date') && strtotime(request('from_date')) ? date('Y-m-d', strtotime(request('from_date'))) : date('Y-m-01') }}">
                                    </div>
                                    <div class="col-lg-3 form-group">
                                        <label class="text-muted">To Date</label>
                                        <input type="date" name="to_date" class="form-control"
                                            value="{{ request('to_date') && strtotime(request('to_date')) ? date('Y-m-d', strtotime(request('to_date'))) : date('Y-m-t') }}">
                                    </div>
                                    <div class="col-lg-3 form-group mt-4">
                                        <label></label>
                                        <button type="submit" class="btn btn-primary">Search</button>
                                        <button type="button" class="btn btn-secondary resetBtn">Clear</button>
                                    </div>
                                </div>
                            </form>


                        
                        <table class="table table-striped mt-4">

                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Gold Rate Per Gram</th>
                                    <th scope="col">Gold Rate Per Pavan</th>



                                </tr>
                            </thead>
                            <tbody>
                                @if(count($goldrates) > 0)
                                @foreach($goldrates as $goldrate)
                                <tr>
                                    <th scope="row">{{ $goldrates->firstItem() + $loop->index }}</th>
                                    <td>{{ date('d-m-Y', strtotime($goldrate->date_on)) }}</td>
                                    <td>{{ \App\Models\Setting::CURRENCY }} {{ number_format($goldrate->per_gram, 2) }}</td>
                                    <td>{{ \App\Models\Setting::CURRENCY }} {{ number_format($goldrate->per_pavan, 2) }}</td>



                                </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td colspan="4">No Records available in table</td>
                                </tr>
                                @endif

                            </tbody>
                        </table>

                        {{ $goldrates->onEachSide(5)->appends([
                                    'from_date' => request('from_date'), 
                                    'to_date' => request('to_date')
                                ])->links() }}
                        <!-- End Table with stripped rows -->

                    </div>
                </div>







            </div>
        </div>
    </section>
</main>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $(document).on('click', '.resetBtn', function() {
            $("input[name='from_date']").val('');
            $("input[name='to_date']").val('');
        });
    });
</script>
@endpush