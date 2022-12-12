@isset($pageConfigs)
{!! Helper::updatePageConfig($pageConfigs) !!}
@endisset

<!DOCTYPE html>
@php
$configData = Helper::applClasses();
@endphp

<html class="loading {{ ($configData['theme'] === 'light') ? '' : $configData['layoutTheme']}}"
lang="@if(session()->has('locale')){{session()->get('locale')}}@else{{$configData['defaultLanguage']}}@endif"
data-textdirection="{{ env('MIX_CONTENT_DIRECTION') === 'rtl' ? 'rtl' : 'ltr' }}"
@if($configData['theme'] === 'dark') data-layout="dark-layout" @endif>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="description" content="Viv Panel.">
  <meta name="keywords" content="admin template, Vivid admin template, dashboard template, flat admin template, responsive admin template, web app">
  <meta name="author" content="PIXINVENT">
  <title>@yield('title') - Vivid Panel</title>
  <link rel="apple-touch-icon" href="{{asset('images/ico/apple-icon-120.png')}}">
  <link rel="shortcut icon" type="image/x-icon" href="{{asset('images/logo/favicon.png')}}">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600" rel="stylesheet">
  <link rel="stylesheet" href="{{ URL::asset('css/custom.css') }}">

  {{-- Include core + vendor Styles --}}
  @include('panels/styles')

</head>
<style>
span.error {color: red;font-size: 12px;}
span.menu-item {font-size: 13px;}
</style>
<?php

$todayDate = date("Y-m-d");
$ajaxUrl = url('/');

?>
<script>
  var todayDate = "<?php echo $todayDate; ?>";
  var ajaxUrl = "<?php echo $ajaxUrl; ?>";
</script>
<!-- END: Head-->

<!-- BEGIN: Body-->
@isset($configData["mainLayoutType"])
@extends((( $configData["mainLayoutType"] === 'horizontal') ? 'layouts.horizontalLayoutMaster' :
'layouts.verticalLayoutMaster' ))
@endisset
