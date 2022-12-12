@extends('layouts/contentLayoutMaster')

@section('title', 'Add Product')

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
          <form class="form"  id="insertForm">
            <div class="row">
                <div class="col-md-6 col-12">
                    <div class="row">
                       <div class="mb-1">
                             <label class="form-label">Product Type</label>
                             <select class="form-control" id="product_type" name="product_type">
                                <option disabled selected>Select Product Type</option>
                                <option value="1">Normal</option>
                                <option value="2">Configurable Product</option>
                             </select>
                        </div>
                    </div>
                    <div class="row">
                       <div class="mb-1">
                             <label class="form-label">Product Name</label>
                             <input type="text" class="form-control dt-full-name" name="product_name" placeholder="Product name" />
                        </div>
                    </div>
                    <div class="row">
                      <div class="mb-1">
                         <label class="form-label">Cover image [ Recommended size 800 * 1200 ]</label>
                         <input type="file" class="form-control dt-full-name" name="featured_Image" placeholder="Featured Image" accept="image/png, image/gif, image/jpeg, image/jpg" />
                      </div>
                    </div>
                    <div class="row">
                     <div class="mb-1">
                        <label class="form-label">Price</label>
                         <input type="text" class="form-control dt-full-name preventCharacters" id="price" name="price" placeholder="Price" />
                     </div>
                    </div>
                    <div class="row">
                     <div class="mb-1">
                         <label class="form-label">Special Price</label>
                         <input type="text" class="form-control dt-full-name preventCharacters" name="special_price" placeholder="Special Price" />
                     </div>
                    </div>
                    <div class="row">
                     <div class="mb-1">
                         <label class="form-label">Available Qty </label>
                          <input type="text" class="form-control dt-full-name preventCharacters" name="available_qty" placeholder="Available Qty" />
                      </div>
                    </div>
                </div>
                <div class="col-md-6 col-12">
                    <div class="row">
                      <div class="mb-1">
                          <label class="form-label">Description</label>
                          <textarea class="form-control" rows="6" placeholder="" name="description" aria-invalid="Description"></textarea>
                        </div>
                    </div>

                    <div class="row">
                      <div class="mb-1">
                          <label class="form-label">Additional Images [ Recommended size 800 * 1200 ]</label>
                            <input type="file" multiple="multiple" name="additional_images[]" class="form-control dt-full-name" placeholder="Additional Images" accept="image/png, image/gif, image/jpeg, image/jpg"/>
                      </div>
                    </div>

                    <div class="row configProductSec">
                        <div class="mb-1">
                            <label class="form-label">Choose Comic</label>
                              <select class="form-control select2 form-select"  name="comic[]" multiple>

                              </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-1">
                              <label class="form-label">Min Comics </label>
                                <input type="text" class="form-control dt-full-name preventCharactersAndDot" name="min_comics" placeholder="Min Comics" />
                            </div>
                        </div>
                        <div class="col-md-6">
                          <div class="mb-1">
                              <label class="form-label">Max Comics </label>
                                <input type="text" class="form-control dt-full-name preventCharactersAndDot" name="max_comics" placeholder="Max Comics" />
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


@endsection
