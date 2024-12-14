@extends('layouts.page')
@section('content')
<main id="main" class="main">

    <div class="pagetitle">
        <h1>Schemes</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('home')}}">Home</a></li>
                <li class="breadcrumb-item active">Schemes</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">

            <div class="col-lg-12">

                <div class="card">
                    <div class="card-title d-flex justify-content-between mt-0 m-3">
                        <h5><b>Manage Schemes</b></h5>
                        <a href="{{route('schemes.create')}}" class="btn btn-primary"><i class="bi bi-align-middle"></i> <span class="text-white">Add Schemes</span></a>
                    </div>
                    <div class="card-body">


                        @include('layouts.partials.messages')
                        <!-- Table with stripped rows -->
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Scheme</th>
                                    <th scope="col">Scheme Type</th>
                                    <th scope="col">Total Period (months)</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($schemes) > 0)
                                @foreach($schemes as $scheme)
                                <tr>
                                    <th scope="row">{{ $loop->iteration }}</th>
                                    <td>{{$scheme->title}}</td>
                                    <td>{{ $scheme->schemeType?->title }}</td>
                                    <td>{{$scheme->total_period}}</td>
                                    <td>
                                        <a href="{{ route('schemes.edit', encrypt($scheme->id)) }}" style="margin-right: 10px;"><i class="bi bi-pencil-square"></i></a>
                                        @role('superadmin')
                                            <a href="javascript:void(0);" onclick="event.preventDefault(); deleteScheme('{{ $scheme->id }}');"><i class="bi bi-x-circle"></i></a>
                                        @endrole
                                    </td>
                                    <form method="post" action="{{ route('schemes.destroy', encrypt($scheme->id)) }}" style="display:none" id="delete-form-{{ $scheme->id }}">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td colspan="5">No Records available in table</td>
                                </tr>
                                @endif
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
    function deleteScheme(id) {

        swal({
                title: "Are you sure ?",
                text: "Do you want to delete this Scheme ?",
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