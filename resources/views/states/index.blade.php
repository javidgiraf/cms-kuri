@extends('layouts.page')
@section('content')
<main id="main" class="main">

    <div class="pagetitle">
        <h1>States</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('home')}}">Home</a></li>
                <li class="breadcrumb-item active">States</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">


            <div class="col-lg-12">

                <div class="card">
                    <div class="card-title d-flex justify-content-between m-3 mt-0">
                        <h5><b>Manage States</b></h5>
                        <a href="{{route('states.create')}}" class="btn btn-primary"><i class="bi bi-align-middle"></i> <span class="text-white">Add State</span></a>
                    </div>
                    <div class="card-body">
                        @include('layouts.partials.messages')
                        <!-- Table with stripped rows -->
                        <table class="table table-striped">
                            
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Country</th>
                                    <th scope="col">State</th>
                                    <th scope="col">Code</th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($states as $state)

                                <tr>
                                    <th scope="row">{{ $states->firstItem() + $loop->index }}</th>
                                    <td>{{ ($state->country) ? $state->country->name : '' }}</td>
                                    <td>{{$state->name}}</td>
                                    <td>{{$state->code}}</td>

                                    <td><a href="{{route('states.edit',encrypt($state->id))}}" style="margin-right: 10px;"><i class="bi bi-pencil-square"></i></a>
                                        <a href="javascript:void(0);" onclick="event.preventDefault(); deleteState('{{ $state->id }}');"><i class="bi bi-x-circle"></i></a>
                                    </td>

                                    <form method="post" action="{{route('states.destroy', encrypt($state->id))}}" style="display:none" id="delete-form-{{$state->id}}">
                                        @csrf
                                        @method('DELETE')
                                    </form>

                                </tr>
                                @endforeach

                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="5">{{ $states->links() }}</td>
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
    function deleteState(id) {

        swal({
                title: "Are you sure ?",
                text: "Do you want to delete this state ?",
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