$(document).on("change", ".uploadAudioCheck", function(){
    if ($(this).is(":checked"))
    {
      var ac = $("#access_type").find(':selected').val();
      if(ac == 2)
      {
        $(".previewSection").removeClass('d-none');
      }
      else
      {
        $(".previewSection").addClass('d-none');
      }
      $(".comicAudioSection, .comicTimerSection").removeClass('d-none');
    }
    else
    {
      $(".comicAudioSection, .comicTimerSection, .previewSection").addClass('d-none');
    }
  });
  $(document).on("change", ".uploadPreviewCheck", function(){
    if ($(this).is(":checked"))
    {
      $(".previewPagesSection").removeClass('d-none');
    }
    else
    {
      $(".previewPagesSection").addClass('d-none');
    }
  });

  $(document).on("change", "#access_type", function(){
    var th = $(this).val();
    if(th == 2)
    {
      if($('.uploadAudioCheck').is(":checked"))
      {
        $(".previewSection").removeClass('d-none');
      }
      else
      {
        $(".previewSection").addClass('d-none');
      }
    }
    else
    {
      $(".previewSection").addClass('d-none');
    }

  });


// $(document).on("change", ".autioTimer", function(){

//     var autioTimer = $(this).val();

//     console.log(autioTimer)

//     var time = autioTimer;
//    // var newtime = new Date('1970-01-01 '+time);

//     var newtime = new Date('Tue Jul 06 2021 '+ time);
// console.log({
//   newtime,
//   str: 'Tue Jul 06 2021'+ time
// })

//     newtime.setSeconds(newtime.getSeconds() + 1);
//     var hours = newtime.getHours();
//     var minutes = newtime.getMinutes();
//     var seconds = newtime.getSeconds();
//     hours = (hours > 9) ? hours : '0'+hours;
//     minutes = (minutes > 9) ? minutes : '0'+minutes;
//     seconds = (seconds > 9) ? seconds : '0'+seconds;
//     var thisVal = hours+':'+minutes+':'+seconds;

//     console.log({thisVal})


//     $(this).removeClass('border-red');
//     var checkTime = validate_time(autioTimer)
//     if(!checkTime)
//     {
//       alert("please check time format");
//       $(this).addClass('border-red');
//       $(".data-submit").prop('disabled', true);
//     }
//     else
//     {
//       $(".data-submit").prop('disabled', false);
//     }
//     $(this).closest('.comicTimerMain').next('.comicTimerMain').find('.startTime').val(thisVal);
// });

var delimiterMask = $('.autioTimer');
if (delimiterMask.length) {
    delimiterMask.each(function(){
        new Cleave($(this), {
          delimiter: ':',
          numeralPositiveOnly: true,
          blocks: [2, 2, 2],
          uppercase: true
        });
    });
}
function validate_time(time){
     const myArray = time.split(":");
     var hours = myArray[0];
     var minute = myArray[1];
     var second = myArray[2];

     if((myArray[1] >= 60 || myArray[2] >= 60) || (time == "00:00:00")){
        return false
       }
      return true;
}