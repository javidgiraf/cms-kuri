@extends('layouts.page')
@section('content')

<main id="main" class="main">
  <div class="pagetitle">
    <h1>Schemes</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('home')}}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{route('schemes.index')}}">Schemes</a></li>
        <li class="breadcrumb-item active">Update Scheme</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->
  <section class="section">
    <div class="row">
      <div class="col-lg-12">

        <div class="card">
          <div class="card-title d-flex justify-content-between m-3 mt-0">
            <h5><strong>Update</strong> Scheme</h5>
            <a href="{{route('schemes.index')}}" class="btn btn-primary"><i class="zmdi zmdi-arrow-left" style="padding-right: 6px;"></i><span class="text-white">Back</span></a>
          </div>
          <div class="card-body">

            <!-- General Form Elements -->
            <form method="post" enctype="multipart/form-data" action="{{ route('schemes.update', encrypt($scheme->id)) }}">
              @csrf
              @method('patch')
              <div class="row mb-3">
                <label for="inputText" class="col-sm-2 col-form-label">Title <span class="text-danger">*</span></label>
                <div class="col-sm-10">
                  <input type="text" id="title" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $scheme->title) }}" placeholder="Enter New Scheme">
                  @error('title')
                  <span class="invalid-feedback">{{ $message }}</span>
                  @enderror
                </div>
              </div>

              <div class="row mb-3">
                <label for="inputText" class="col-sm-2 col-form-label">Total Period (in Months) <span class="text-danger">*</span></label>
                <div class="col-sm-10">
                  <input type="text" id="total_period" name="total_period" class="form-control @error('total_period') is-invalid @enderror" value="{{ old('total_period', $scheme->total_period) }}" placeholder="Enter Total Period (in Months)">
                  @error('total_period')
                  <span class="invalid-feedback">{{ $message }}</span>
                  @enderror
                </div>
              </div>

              <div class="row mb-3">
                <label for="inputText" class="col-sm-2 col-form-label">Scheme Type <span class="text-danger">*</span></label>
                <div class="col-sm-10">
                  <select class="form-control select2 @error('scheme_type_id') is-invalid @enderror" name="scheme_type_id" required>
                    <option selected disabled>Select a Scheme Type</option>
                    @foreach($schemeTypes as $value)
                    <option value="{{ $value->id }}" {{ $scheme->scheme_type_id == $value->id ? 'selected' : '' }}>
                      {{ $value->title }}
                    </option>
                    @endforeach
                  </select>
                  @error('scheme_type_id')
                  <span class="invalid-feedback">{{ $message }}</span>
                  @enderror
                </div>
              </div>

              <div class="row mb-3">
                <label for="inputText" class="col-sm-2 col-form-label">Upload File </label>
                <div class="col-sm-10">
                  <div class="mb-3">

                    <input class="form-control @error('pdf_file') is-invalid @enderror" type="file" name="pdf_file" id="schemeFile">
                  </div>
                  <a href="{{ route('viewPdf', $scheme->id) }}" target="_blank">View PDF</a>
                  @error('pdf_file')
                    <span class="invalid-feedback">{{ $message }}</span>
                  @enderror
                </div>
              </div>

              <div class="row mb-3">
                <label for="inputText" class="col-sm-2 col-form-label">Description</label>
                <div class="col-sm-10">
                  <textarea id="title" name="description" class="form-control basic-example" placeholder="Description"> {!! $scheme->description !!}</textarea>
                </div>
              </div>
              <fieldset class="row mb-3">
                <legend class="col-form-label col-sm-2 pt-0">Status <span class="text-danger">*</span></legend>
                <div class="col-sm-2">
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="status" id="gridRadios1" value="1" {{($scheme->status=='1'?'checked':'')}}>
                    <label class="form-check-label" for="gridRadios1">
                      Active
                    </label>
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="status" id="gridRadios2" value="0" {{($scheme->status=='0'?'checked':'')}}>
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
</main>
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.7.2/tinymce.min.js"></script>
<script>
  tinymce.init({
    selector: 'textarea.basic-example',
    plugins: 'preview importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help charmap quickbars emoticons accordion',
    menubar: 'file edit view insert format tools table help',
    toolbar: "undo redo | accordion accordionremove | blocks fontfamily fontsize | bold italic underline strikethrough | align numlist bullist | link image | table media | lineheight outdent indent| forecolor backcolor removeformat | charmap emoticons | code fullscreen preview | save print | pagebreak anchor codesample | ltr rtl",

    menubar: false,
    toolbar_items_size: 'small',

    style_formats: [{
        title: 'Bold text',
        inline: 'b'
      },
      {
        title: 'Red text',
        inline: 'span',
        styles: {
          color: '#ff0000'
        }
      },
      {
        title: 'Red header',
        block: 'h1',
        styles: {
          color: '#ff0000'
        }
      },
      {
        title: 'Example 1',
        inline: 'span',
        classes: 'example1'
      },
      {
        title: 'Example 2',
        inline: 'span',
        classes: 'example2'
      },
      {
        title: 'Table styles'
      },
      {
        title: 'Table row 1',
        selector: 'tr',
        classes: 'tablerow1'
      }
    ],

    templates: [{
        title: 'Test template 1',
        content: 'Test 1'
      },
      {
        title: 'Test template 2',
        content: 'Test 2'
      }
    ],
    setup: function(ed) {
      ed.on('change', function(e) {
        tinyMCE.triggerSave();
      });
    }
  });

  $(function() {
    $(".select2").select2();
  });
</script>
@endpush
@endsection