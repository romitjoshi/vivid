@extends('layouts/contentLayoutMaster')

@section('title', 'Edit Product')

@section('vendor-style')
<link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
@endsection
@section('page-style')
  {{-- Page Css files --}}
  <link rel="stylesheet" href="{{ URL::asset('js/custom/library/toastr.min.css') }}">
@endsection
@section('content')

<!-- Basic multiple Column Form section start -->
<section id="multiple-column-form">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <!-- <h4 class="card-title">Multiple Column</h4> -->
        </div>
        <div class="card-body">
          <form class="form"  id="updateForm">
          <input type="hidden" name="product_id"  value="{{ $getData->id }}">
            <div class="row">
                <div class="col-md-6 col-12">
                 <div class="row">
                       <div class="mb-1">
                             <label class="form-label">Product Type</label>
                             <select class="form-control pe-none" id="product_type" name="product_type" readonly>
                                <option disabled selected>Select Product Type</option>
                                <option value="1" @if($getData->type == 1) selected @endif>Normal</option>
                                <option value="2" @if($getData->type == 2) selected @endif>Configurable Product</option>
                             </select>
                        </div>
                    </div>
                    <div class="row">
                       <div class="mb-1">
                             <label class="form-label">Product Name</label>
                             <input type="text" class="form-control dt-full-name" name="product_name" id="product_name" placeholder="product name" value="{{ $getData->product_name ?? ''}}"/>
                        </div>
                    </div>
                    <div class="row">
                      <div class="mb-1">
                         <label class="form-label">Cover image [ Recommended size 800 * 1200 ]</label>
                         <input type="file" class="form-control dt-full-name" id="featured_Image" name="featured_Image" placeholder="featured Image" accept="image/png, image/gif, image/jpeg, image/jpg" />
                      </div>
                      <div class="col-2">
                                <div class="previewImageSection2 text-end w-100">
                                  <img src="{{ env('IMAGE_PATH_medium').$getData->featured_Image }}" class="img-fluid rounded" alt="avatar img">
                                </div>
                              </div>
                    </div>
                    <div class="row">
                     <div class="mb-1">
                        <label class="form-label">Price</label>
                         <input type="text" class="form-control dt-full-name preventCharacters" name="price" id="price" placeholder="price" value="{{ $getData->price ?? ''}}"/>
                     </div>
                    </div>
                    <div class="row">
                     <div class="mb-1">
                         <label class="form-label">Special Price</label>
                         <input type="text" class="form-control dt-full-name preventCharacters" name="special_price" id="special_price" placeholder="special price" value="@if($getData->is_special > 0){{$getData->special_price ?? ''}} @endif"/>
                     </div>
                    </div>
                    <div class="row">
                     <div class="mb-1">
                         <label class="form-label">Available Qty </label>
                          <input type="text" class="form-control dt-full-name preventCharacters" id="available_qty" name="available_qty" placeholder="available qty" value="{{ $getData->available_qty ?? ''}}" />
                      </div>
                    </div>
                </div>
                <div class="col-md-6 col-12">
                    <div class="row">
                      <div class="mb-1">
                          <label class="form-label">Description</label>
                          <textarea class="form-control" rows="6" placeholder="" id="description" name="description" aria-invalid="Description" >{{ $getData->description ?? ''}}</textarea>
                        </div>
                      </div>
                      <div class="row">
                          <div class="mb-1">
                              <label class="form-label">Additional Images [ Recommended size 800 * 1200 ]</label>
                              <input type="file" rel="icon"  class="form-control dt-full-name" name="additional_images[]" placeholder="additional images" accept="image/png, image/gif, image/jpeg, image/jpg"  multiple>
                          </div>
                            <!-- <div class="col-2">  -->
                                <div class ="row">
                                    <div class="col-md-12" >
                                    <div class="previewImageSection3 text-end w-100 d-flex">
                                  @php
                                  $pica=json_decode($getData->additional_images);
                                  @endphp
                                      @foreach($pica as $pic)
                                        <div class="deleteImageSection">
                                            <i data-feather='x' imagename="{{ $pic }}" product_id="{{ $getData->id }}" class="deleteImages" ></i>
                                            <img src="{{ env('IMAGE_PATH_medium').$pic}}" class="img-fluid rounded product_img" alt="avatar img" >
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            <!-- </div> -->
                          </div>

                      </div>


                      <div class="configProductSec mt-3 @if($getData->type == 1) d-none @endif">
                        <div class="row">
                            <div class="mb-1">
                                <label class="form-label">Choose Comic [Select Multiple Comics]</label>
                                  <select class="form-control select2 form-select"  name="comic[]" multiple>
                                      @php
                                        foreach($getComicData as $cid)
                                        {
                                          $arrayId = [];
                                          if(!empty($getData->comic_series_id))
                                          $arrayId = json_decode($getData->comic_series_id);

                                          if(in_array($cid->id, $arrayId))
                                          {
                                            echo '<option value="'.$cid->id.'" selected>'.$cid->name.'</option>';
                                          }
                                          else
                                          {
                                            echo '<option value="'.$cid->id.'">'.$cid->name.'</option>';
                                          }

                                        }
                                        @endphp
                                  </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-1">
                                  <label class="form-label">Min Comics </label>
                                    <input type="text" class="form-control dt-full-name preventCharactersAndDot" name="min_comics" placeholder="Min Comics" value="{{ $getData->min_comics ?? ''}}" />
                                </div>
                            </div>
                            <div class="col-md-6">
                              <div class="mb-1">
                                  <label class="form-label">Max Comics </label>
                                    <input type="text" class="form-control dt-full-name preventCharactersAndDot" name="max_comics" placeholder="Max Comics" value="{{ $getData->max_comics ?? ''}}" />
                                </div>
                            </div>
                        </div>
                    </div>





                </div>
              <div class="button-section mt-5">
                <button type="submit" class="btn btn-primary data-submit me-1"><div class="spinner-border text-light importloader dNone" role="status"></div>Submit</button>
                <button type="reset" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
              </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- Basic Floating Label Form section end -->

@endsection

@section('vendor-script')
  <script src="{{ asset(mix('vendors/js/forms/validation/jquery.validate.min.js'))}}"></script>
  <script src="{{ URL::asset('js/custom/library/toastr.min.js') }}"></script>
  <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
@endsection
@section('page-script')
  {{-- Page js files --}}

  <script src="{{ asset(mix('js/scripts/forms/form-select2.js')) }}"></script>
  <script src="{{ asset('js/custom/admin/product.js') }}"></script>
  <script src="{{ asset('js/custom/admin/common.js') }}"></script>
   <script>



   </script>

@endsection
