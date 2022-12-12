@extends('content.front.layouts.master')
@section('title','Home')
@section('content')
<section class="deliverydetailpage bg-dark p-10">
	<div class="container textwhite">
    <div class="row p-10">
     <h1 class="searchhead">Shipping, Returns, Refund and
            Cancellation Policy</h1>
            <nav aria-label="breadcrumb">
         <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home-page') }}">Home</a></li>
            <!-- <li class="breadcrumb-item"><a href="{{route('about')}}">About Us</a></li> -->
            <li class="breadcrumb-item active" aria-current="page">Cancellation Policy</li>

         </ol>
      </nav>
      </div>

            <p class=MsoNormal><b><u><span >Shipping</span></u></b></p>

            <p class=MsoNormal style='text-align:justify'><span >All
            orders would be shipped within three (3) days from the order confirmation date.
            We request you to provide the correct address and mobile number so that the
            delivery partner can contact you. In case of an unsuccessful delivery, return
            charges will apply as a return-to-origin charge. </span></p>

            <p class=MsoNormal style='text-align:justify'><b><u><span style='font-family:
            "Times New Roman",serif'>Return</span></u></b></p>

            <p class=MsoNormal style='text-align:justify'><span >Orders
            can be returned within six (6) days from the date of receiving the product.
            Please contact us at support@vpcomix.com stating the order number and the
            reason for the return.</span></p>

            <p class=MsoNormal style='text-align:justify'><span >We
            accept returns within six (6) days of receipt, so long as the products aren’t
            damaged or the wrong product delivered. Returns post this period won’t be entertained.
            </span></p>

            <p class=MsoNormal style='text-align:justify'><span >Please
            don’t write or scribble anything in the book or on the cover. Please notify us
            and return the product in its original packaging. In such instances, we will endeavor
            to send you another product<span style='color:red'> </span>or refund the
            payment.</span></p>

            <p class=MsoNormal style='text-align:justify'><span >Shipping
            &amp; Handling fees are non-refundable.</span></p>

            <p class=MsoNormal style='text-align:justify'><span >Please
            note that we reserve the right to refuse any returned shipments if the product has
            been damaged or tampered with.</span></p>

            <p class=MsoNormal style='text-align:justify'><b><u><span style='font-family:
            "Times New Roman",serif'>Refunds</span></u></b></p>

            <p class=MsoNormal style='text-align:justify'><a><span >Refunds
            will be processed according to the original mode of payment. If your original
            payment method was Cash on Delivery the amount will be refunded as store credit
            only. Refund will be processed within forty-eight (48) hours once we receive
            and inspect the returned item. We may refuse the return of items if the product
            doesn’t meet the return criteria. We will inform you accordingly via email in such
            a case.</span></a><span class=MsoCommentReference><span style='font-size:8.0pt;
            line-height:107%'><a class=msocomanchor id="_anchor_1"
            onmouseover="msoCommentShow('_anchor_1','_com_1')"
            onmouseout="msoCommentHide('_com_1')" href="#_msocom_1" language=JavaScript
            name="_msoanchor_1"></a>&nbsp;</span></span></p>

            <p class=MsoNormal style='text-align:justify'><b><u><span style='font-family:
            "Times New Roman",serif'>Cancellation</span></u></b></p>

            <p class=MsoNormal style='text-align:justify'><span >If
            the cancellation is requested within three (3) days of purchase of the
            subscription, it is eligible for a 100% refund. All cancellations made beyond
            the three (3) days period are not eligible for any refund. The refund would be
            processed within <span>forty-eight (48) hours</span>
            of cancellation and would be processed as per your original mode of payment.</span></p>

    </div>
</section>



@endsection
@section('scripts')

@endsection