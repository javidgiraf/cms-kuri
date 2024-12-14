@extends('layouts.page')
@section('content')
<main id="main" class="main">

    <div class="pagetitle">
        <h1>Countries</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('home')}}">Home</a></li>
                <li class="breadcrumb-item active">Countries</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">


            <div class="col-lg-12">

                <div class="card">
                    <div class="card-title d-flex justify-content-between m-3 mt-0">
                        <h5><b>Manage Countries</b></h5>
                        <a href="{{route('countries.create')}}" class="btn btn-primary"><i class="bi bi-align-middle"></i> <span class="text-white">Add Countries</span></a>
                    </div>
                    <div class="card-body">

                        @include('layouts.partials.messages')
                        <!-- Table with stripped rows -->
                        <table class="table table-striped">
                    
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Country</th>
                                    <th scope="col">Code</th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($countries as $country)
                                <tr>
                                    <th scope="row">{{ $loop->iteration }}</th>
                                    <td>{{$country->name}}</td>
                                    <td>{{$country->code}}</td>

                                    <td><a href="{{route('countries.edit',encrypt($country->id))}}" style="margin-right: 10px;"><i class="bi bi-pencil-square"></i></a>
                                        <a href="javascript:void(0);" onclick="event.preventDefault(); deleteCountry('{{ $country->id }}');"><i class="bi bi-x-circle"></i></a>
                                    </td>

                                    <form method="post" action="{{route('countries.destroy', encrypt($country->id))}}" style="display:none" id="delete-form-{{$country->id}}">
                                        @csrf
                                        @method('DELETE')
                                    </form>

                                </tr>
                                @endforeach

                            </tbody>
                        </table>
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
    function deleteCountry(id) {

        swal({
                title: "Are you sure ?",
                text: "Do you want to delete this country ?",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
            .then((willDelete) => {
                if (willDelete) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
    }
</script>
@endpush