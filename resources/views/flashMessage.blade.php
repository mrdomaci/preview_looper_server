@if ($message = Session::get('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
  {{ $message }}
  <button type="button" class="close" style="border: unset;background: unset;color: unset;float: right;" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
@endif

@if ($message = Session::get('error'))
<div class="alert alert-error alert-dismissible fade show" role="alert">
  {{ $message }}
  <button type="button" class="close" style="border: unset;background: unset;color: unset;float: right;" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
@endif

@if ($message = Session::get('warning'))
<div class="alert alert-warning alert-dismissible fade show" role="alert">
  {{ $message }}
  <button type="button" class="close" style="border: unset;background: unset;color: unset;float: right;" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
@endif

@if ($message = Session::get('info'))
<div class="alert alert-info alert-dismissible fade show" role="alert">
  {{ $message }}
  <button type="button" class="close" style="border: unset;background: unset;color: unset;float: right;" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
@endif
