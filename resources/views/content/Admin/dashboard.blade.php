
@extends('layouts/contentLayoutMaster')

@section('title', 'Dashboard')

@section('vendor-style')
  {{-- vendor css files --}}
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/charts/apexcharts.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/flatpickr/flatpickr.min.css')) }}">
@endsection
@section('page-style')
  {{-- Page css files --}}
  <link rel="stylesheet" href="{{ asset(mix('css/base/pages/dashboard-ecommerce.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/charts/chart-apex.css')) }}">
  <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/pickers/form-flat-pickr.css')) }}">

@endsection

@section('content')
<!-- Dashboard Ecommerce Starts -->
<section id="dashboard-ecommerce">
  <div class="row match-height">
    <!-- Statistics Card -->
    <div class="col-xl-12 col-md-12 col-12">
      <div class="card card-statistics">
        <div class="card-header">
          <h4 class="card-title">Statistics</h4>
          <div class="d-flex align-items-center">
            <!-- <p class="card-text font-small-2 me-25 mb-0">Updated 1 month ago</p> -->
          </div>
        </div>
        <div class="card-body statistics-body">
          <div class="row">
            <div class="col-xl-2 col-sm-6 col-12 mb-2 mb-xl-0">
              <div class="d-flex flex-row">
                <div class="avatar bg-light-primary me-2">
                  <div class="avatar-content">
                    <i data-feather="book" class="avatar-icon"></i>
                  </div>
                </div>
                <div class="my-auto">
                  <h4 class="fw-bolder mb-0">{{$allComic ?? ''}}</h4>
                  <p class="card-text font-small-3 mb-0">Comics</p>
                </div>
              </div>
            </div>
            <div class="col-xl-2 col-sm-6 col-12 mb-2 mb-xl-0">
              <div class="d-flex flex-row">
                <div class="avatar bg-light-primary me-2">
                  <div class="avatar-content">
                    <i data-feather="book-open" class="avatar-icon"></i>
                  </div>
                </div>
                <div class="my-auto">
                  <h4 class="fw-bolder mb-0">{{ $allEpisode ?? ''}}</h4>
                  <p class="card-text font-small-3 mb-0">Episode</p>
                </div>
              </div>
            </div>
            <div class="col-xl-2 col-sm-6 col-12 mb-2 mb-xl-0">
              <div class="d-flex flex-row">
                <div class="avatar bg-light-info me-2">
                  <div class="avatar-content">
                    <i data-feather="user" class="avatar-icon"></i>
                  </div>
                </div>
                <div class="my-auto">
                  <h4 class="fw-bolder mb-0">{{$allUser ?? ''}}</h4>
                  <p class="card-text font-small-3 mb-0">Customers</p>
                </div>
              </div>
            </div>
            <div class="col-xl-2 col-sm-6 col-12">
              <div class="d-flex flex-row">
                <div class="avatar bg-light-success me-2">
                  <div class="avatar-content">
                    <i data-feather="user-check" class="avatar-icon"></i>
                  </div>
                </div>
                <div class="my-auto">
                  <h4 class="fw-bolder mb-0">{{$allPaidUser ?? ''}}</h4>
                  <p class="card-text font-small-3 mb-0">Paid User</p>
                </div>
              </div>
            </div>
           
            <div class="col-xl-2 col-sm-6 col-12 mb-2 mb-sm-0">
              <div class="d-flex flex-row">
                <div class="avatar bg-light-primary me-2">
                  <div class="avatar-content">
                    <i data-feather="dollar-sign" class="avatar-icon"></i>
                  </div>
                </div>
                <div class="my-auto">
                  <h4 class="fw-bolder mb-0">{{ Helper::makeCurrency($storeRevenue ?? '')}}</h4>
                  <p class="card-text font-small-3 mb-0">Store Revenue</p>
                </div>
              </div>
            </div>
            <div class="col-xl-2 col-sm-6 col-12 mb-2 mb-xl-0">
              <div class="d-flex flex-row">
                <div class="avatar bg-light-primary me-2">
                  <div class="avatar-content">
                    <i data-feather="dollar-sign" class="avatar-icon"></i>
                  </div>
                </div>
                <div class="my-auto">
                  <h4 class="fw-bolder mb-0">{{Helper::makeCurrency($comicRevenue ?? '')}}</h4>
                  <p class="card-text font-small-3 mb-0">Comics Revenue</p>
                </div>
              </div>
            </div>
            
          </div>
        </div>
      </div>
    </div>
    <!--/ Statistics Card -->
  </div>

    <!-- Area Chart starts -->
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div
            class="
              card-header
              d-flex
              flex-sm-row flex-column
              justify-content-md-between
              align-items-start
              justify-content-start
            "
          >
            <div>
              <h4 class="card-title">User Chart</h4>
              <!-- <span class="card-subtitle text-muted">Commercial networks</span> -->
            </div>
            <div class="d-flex align-items-center">
              <!-- <i class="font-medium-2" data-feather="calendar"></i> -->
              <!-- <input
                type="text"
                class="form-control flat-picker bg-transparent border-0 shadow-none"
                placeholder="YYYY-MM-DD"
              /> -->
            </div>
          </div>
          <div class="card-body">
            <div id="line-area-chart"></div>
          </div>
        </div>
      </div>
    </div>
    <!-- Area Chart ends -->



    <div class="row">
         <!-- Company Table Card -->
    <div class="col-lg-7 col-12">
      <div class="card card-company-table">
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table">
              <thead>
                <tr>
                  <th>Top Comics</th>
                  <th>View</th>
                </tr>
              </thead>
              <tbody>
                @foreach($cs as $s)
                <tr>
                  <td>
                    <div class="d-flex align-items-center">
                      <span>{{ $s->name ?? '' }}</span>
                    </div>
                  </td>
                  <td>
                    <div class="d-flex align-items-center">
                      <span>{{ $s->view ?? '' }}</span>
                    </div>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
      <!-- Donut Chart Starts-->
      <div class="col-lg-5 col-12">
      <div class="card">
        <div class="card-header flex-column align-items-start">
          <h4 class="card-title mb-75">Revenue Ratio</h4>
          <span class="card-subtitle text-muted">Revenue generated from different platforms </span>
        </div>
        <div class="card-body">
          <div id="donut-chart"></div>
        </div>
      </div>
    </div>
    <!-- Donut Chart Ends-->
    <!--/ Company Table Card -->
    </div>

</section>
<!-- Dashboard Ecommerce ends -->
@endsection

@section('vendor-script')
  {{-- vendor files --}}
  <script src="{{ asset(mix('vendors/js/charts/apexcharts.min.js')) }}"></script>
  <script src="{{ asset(mix('vendors/js/pickers/flatpickr/flatpickr.min.js')) }}"></script>
@endsection
@section('page-script')
  {{-- Page js files --}}
  <script src="{{ asset(mix('js/scripts/pages/dashboard-ecommerce.js')) }}"></script>
  <!-- <script src="{{ asset(mix('js/scripts/charts/chart-apex.js')) }}"></script> -->
  <script>
    var categoriesArray = JSON.parse('<?php echo json_encode($categoriesArray); ?>');
    var webusr = JSON.parse('<?php echo json_encode($webusr); ?>');
    var iosusr = JSON.parse('<?php echo json_encode($iosusr); ?>');
    var androidusr = JSON.parse('<?php echo json_encode($androidusr); ?>');
    var comicRevenue = <?php echo $comicRevenue; ?>;

    var webRev = <?php echo $webRev; ?>;
    var iosRev = <?php echo $iosRev; ?>;
    var androidRev = <?php echo $androidRev; ?>;

    $(function () {
      'use strict';

      var flatPicker = $('.flat-picker'),
        isRtl = $('html').attr('data-textdirection') === 'rtl',
        chartColors = {
          column: {
            series1: '#826af9',
            series2: '#d2b0ff',
            bg: '#f8d3ff'
          },
          success: {
            shade_100: '#7eefc7',
            shade_200: '#06774f'
          },
          donut: {
            series1: '#ffe700',
            series2: '#00d4bd',
            series3: '#826bf8',
            series4: '#2b9bf4',
            series5: '#FFA1A1'
          },
          area: {
            series3: '#a4f8cd',
            series2: '#60f2ca',
            series1: '#2bdac7'
          }
        };
     // Area Chart
  // --------------------------------------------------------------------
  var areaChartEl = document.querySelector('#line-area-chart'),
    areaChartConfig = {
      chart: {
        height: 400,
        type: 'area',
        parentHeightOffset: 0,
        toolbar: {
          show: false
        }
      },
      dataLabels: {
        enabled: false
      },
      stroke: {
        show: false,
        curve: 'straight'
      },
      legend: {
        show: true,
        position: 'top',
        horizontalAlign: 'start'
      },
      grid: {
        xaxis: {
          lines: {
            show: true
          }
        }
      },
      colors: [chartColors.area.series3, chartColors.area.series2, chartColors.area.series1],
      series: [
        {
          name: 'Web',
          data: webusr
        },
        {
          name: 'Android',
          data: androidusr
        },
        {
          name: 'Ios',
          data: iosusr
        }
      ],
      xaxis: { 
        categories: categoriesArray
      },
      fill: {
        opacity: 1,
        type: 'solid'
      },
      tooltip: {
        shared: false
      },
      yaxis: {
        opposite: isRtl
      }
    };
  if (typeof areaChartEl !== undefined && areaChartEl !== null) {
    var areaChart = new ApexCharts(areaChartEl, areaChartConfig);
    areaChart.render();
  }



var donutChartEl = document.querySelector('#donut-chart'),
    donutChartConfig = {
      chart: {
        height: 350,
        type: 'donut'
      },
      legend: {
        show: true,
        position: 'bottom'
      },
      labels: ['Web Revenue', 'Ios Revenue', 'Android Revenue'],
      series: [webRev, iosRev, androidRev],
      colors: [
        chartColors.donut.series1,
        chartColors.donut.series5,
        chartColors.donut.series3,
      ],
      dataLabels: {
        enabled: true,
        formatter: function (val, opt) {
          return parseInt(val) + '%';
        }
      },
      plotOptions: {
        pie: {
          donut: {
            labels: {
              show: true,
              name: {
                fontSize: '2rem',
                fontFamily: 'Montserrat'
              },
              value: {
                fontSize: '1rem',
                fontFamily: 'Montserrat',
                formatter: function (val) {
                  return parseInt(val) + '%';
                }
              },
              total: {
                show: true,
                fontSize: '1.5rem',
                label: 'Revenue',
                formatter: function (w) {
                  return "$"+comicRevenue;
                }
              }
            }
          }
        }
      },
      responsive: [
        {
          breakpoint: 992,
          options: {
            chart: {
              height: 380
            }
          }
        },
        {
          breakpoint: 576,
          options: {
            chart: {
              height: 320
            },
            plotOptions: {
              pie: {
                donut: {
                  labels: {
                    show: true,
                    name: {
                      fontSize: '1.5rem'
                    },
                    value: {
                      fontSize: '1rem'
                    },
                    total: {
                      fontSize: '1.5rem'
                    }
                  }
                }
              }
            }
          }
        }
      ]
    };
  if (typeof donutChartEl !== undefined && donutChartEl !== null) {
    var donutChart = new ApexCharts(donutChartEl, donutChartConfig);
    donutChart.render();
  }

});
  </script>
@endsection
