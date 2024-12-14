@extends('layouts.page')
@section('content')
<main id="main" class="main">

    <div class="pagetitle">
        <h1>Settings</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('home')}}">Home</a></li>
                <li class="breadcrumb-item active">Settings</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">


            <div class="col-lg-12">

                <div class="card">
                    <div class="card-title d-flex justify-content-between m-3 mt-0">
                        <h5><b>Manage Settings</b></h5>
                        <a href="{{route('settings.create')}}" class="btn btn-primary"><i class="bi bi-align-middle"></i> <span class="text-white">Add Setting</span></a>
                    </div>
                    <div class="card-body">

                        @include('layouts.partials.messages')
                        <!-- Table with stripped rows -->
                        <table class="table table-striped">
                
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Setting Option</th>
                                    <th scope="col">Setting Option Value</th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($settings as $setting)
                                <tr>
                                    <th scope="row">{{ $settings->firstitem() + $loop->index }}</th>
                                    <td>{{ $setting->option_name }}</td>
                                    <td>{{ $setting->option_value }}</td>
                                    <td>
                                        <a href="{{route('settings.edit', encrypt($setting->id))}}" style="margin-right: 10px;"><i class="bi bi-pencil-square"></i></a>
                                        @role('superadmin')
                                            <a href="javascript:void(0);" onclick="event.preventDefault(); deleteSetting('{{ $setting->id }}');"><i class="bi bi-x-circle"></i></a>
                                        @endrole
                                    </td>

                                    <form method="post" action="{{route('settings.destroy', encrypt($setting->id))}}" style="display:none" id="delete-form-{{$setting->id}}">
                                        @csrf
                                        @method('DELETE')
                                    </form>

                                </tr>
                                @endforeach

                            </tbody>
                        </table>
                        {{ $settings->onEachSide(5)->links() }}
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
    function deleteSetting(id) {

        swal({
                title: "Are you sure ?",
                text: "Do you want to delete this Setting ?",
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