@extends('layouts.page')
@section('content')
<main id="main" class="main">

  <div class="pagetitle">
    <h1>Scheme Settings</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('home')}}">Home</a></li>
        <li class="breadcrumb-item active">Scheme Settings</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->

  <section class="section">
    <div class="row">


      <div class="col-lg-12">

        <div class="card">
          <div class="card-title d-flex justify-content-between m-3 mt-0">
            <h5><b>Manage Scheme Settings</b></h5>
            <a href="{{route('scheme-settings.create')}}" class="btn btn-primary"><i class="bi bi-align-middle"></i> <span class="text-white">Add Scheme Setting</span></a>
          </div>
          <div class="card-body">

            @include('layouts.partials.messages')
            <!-- Table with stripped rows -->
            <table class="table table-striped">

              <thead>
                <tr>
                  <th>No</th>
                  <th scope="col">Scheme</th>
                  <th scope="col">Max Payable</th>
                  <th scope="col">Min Payable</th>
                  <th scope="col">Denomination</th>
                  <th scope="col">Due Duration</th>
                  <th scope="col">Status</th>
                  <th scope="col"></th>
                </tr>
              </thead>
              <tbody>
                @foreach ($schemeSettings as $setting)
                <tr>
                  <td>{{ $loop->iteration + ($schemeSettings->currentPage() - 1) * $schemeSettings->perPage() }}</td>
                  <td>{{ $setting->scheme->title }}</td>
                  <td>{{ \App\Models\Setting::CURRENCY }} {{ number_format($setting->max_payable_amount, 2) }}</td>
                  <td>{{ \App\Models\Setting::CURRENCY }} {{ number_format($setting->min_payable_amount, 2) }}</td>
                  <td>{{ \App\Models\Setting::CURRENCY }} {{ number_format($setting->denomination, 2) }}</td>
                  <td>{{ $setting->due_duration }}</td>
                  <td>{{ ($setting->status == true) ? __('Active') : __('Inactive') }}</td>
                  <td>
                    <a href="{{route('scheme-settings.edit', $setting->id)}}" style="margin-right: 10px;"><i class="bi bi-pencil-square"></i></a>
                    @role('superadmin')
                      <a href="javascript:void(0);" onclick="event.preventDefault(); deleteSchemeSetting('{{ $setting->id }}');"><i class="bi bi-x-circle"></i></a>
                    @endrole
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
            {{ $schemeSettings->onEachSide(5)->links() }}
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
  function deleteSchemeSetting(id) {

    swal({
        title: "Are you sure ?",
        text: "Do you want to delete this Scheme Setting ?",
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