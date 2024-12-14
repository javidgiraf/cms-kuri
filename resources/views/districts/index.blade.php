@extends('layouts.page')
@section('content')
<main id="main" class="main">

    <div class="pagetitle">
        <h1>Districts</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('home')}}">Home</a></li>
                <li class="breadcrumb-item active">District</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">


            <div class="col-lg-12">

                <div class="card">
                    <div class="card-title d-flex justify-content-between m-3 mt-0">
                        <h5><b>Manage Districts</b></h5>
                        <a href="{{route('districts.create')}}" class="btn btn-primary"><i class="bi bi-align-middle"></i> <span class="text-white">Add District</span></a>
                    </div>
                    <div class="card-body">

                        @include('layouts.partials.messages')
                        <!-- Table with stripped rows -->
                        <table class="table table-striped">
                            
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">State</th>
                                    <th scope="col">District</th>
                                    <th scope="col">Code</th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($districts as $district)
                                <tr>
                                    <th scope="row">{{ $districts->firstItem() + $loop->index }}</th>
                                    <td>{{ ($district->state) ? $district->state->name : '' }}</td>
                                    <td>{{$district->name}}</td>
                                    <td>{{$district->code}}</td>

                                    <td><a href="{{route('districts.edit',encrypt($district->id))}}" style="margin-right: 10px;"><i class="bi bi-pencil-square"></i></a>
                                        <a href="javascript:void(0);" onclick="event.preventDefault(); deleteDistrict('{{ $district->id }}');"><i class="bi bi-x-circle"></i></a>
                                    </td>

                                    <form method="post" action="{{route('districts.destroy', encrypt($district->id))}}" style="display:none" id="delete-form-{{$district->id}}">
                                        @csrf
                                        @method('DELETE')
                                    </form>

                                </tr>
                                @endforeach

                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="5">
                                        {{ $districts->links() }}
                                    </td>
                                </tr>
                            </tfoot>
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
    function deleteDistrict(id) {

        swal({
                title: "Are you sure ?",
                text: "Do you want to delete this district ?",
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