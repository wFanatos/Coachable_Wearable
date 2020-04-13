<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/png" href="/Img/whistle.png">
    <title>{{ config('app.name', 'Coachable') }} </title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js" charset="utf-8"></script>
    
    <script type="text/javascript">
      let charts = new Map();

      function removeClass(id, count, classes) {
        for (var i = 0; i < count; i++) {
          var tempID = id + i;
          var element = document.getElementById(tempID);
          
          for (var j = 0; j < classes.length; j++) {
            if (element.classList.contains(classes[j])) {
              element.classList.remove(classes[j]);
            }
          }
        }
      }

      function clearCharts(chart1ID, chart2ID) {
        if (charts.get(chart1ID)) {
          charts.get(chart1ID).destroy();
        }
        if (charts.get(chart2ID)) {
          charts.get(chart2ID).destroy();
        }
      }
      
      function createCharts(chart1ID, chart2ID, timeArr, spdArr, altArr) {
        var ctx1 = document.getElementById(chart1ID).getContext('2d');
        var ctx2 = document.getElementById(chart2ID).getContext('2d');

        clearCharts(chart1ID, chart2ID);
            
        chart1 = new Chart(ctx1, {
          type: 'line',
          
          data: {
            labels: timeArr,
            datasets: [{
              backgroundColor: ['rgba(249, 157, 50, 0.75)'],
              label: 'Speed',
              data: spdArr
            }]
          },
          options: {
              maintainAspectRatio: false,
            legend: {
              display: false
            },
            scales: {
              yAxes: [{
                ticks: {
                  beginAtZero: true
                },
                scaleLabel: {
                  display: true,
                  labelString: 'Speed (km/h)',
                }
              }],
              xAxes: [{
                scaleLabel: {
                  display: true,
                  labelString: 'Time (Seconds)',
                }
              }]
            },
            title: {
              display: true,
              text: 'Speed over Time',
              fontSize: 20
            }
          }
        });
            
        chart2 = new Chart(ctx2, {
          type: 'line',
          data: {
            labels: timeArr,
            datasets: [{
              backgroundColor: ['rgba(249, 157, 50, 0.75)'],
              label: 'Altitude',
              data: altArr
            }]
          },
          options: {
              maintainAspectRatio: false,
            legend: {
              display: false
            },
            scales: {
              yAxes: [{
                scaleLabel: {
                  display: true,
                  labelString: 'Altitude (meters)',
                }
              }],
              xAxes: [{
                scaleLabel: {
                  display: true,
                  labelString: 'Time (Seconds)',
                }
              }]
            },
            title: {
              display: true,
              text: 'Altitude over Time',
              fontSize: 20
            }
          } 
        });

        charts.set(chart1ID, chart1);
        charts.set(chart2ID, chart2);
      }
    </script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-dark" style="background-color: #0a4b5c;">
            <div class="container">
                <a class="navbar-brand" href="{{ route('home') }}">
                  <img src="/Img/whistle.png" height="15%" width="15%">
                  {{ config('app.name', 'Coachable') }}
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>                         
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" style="color: #f99d32" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('home') }}">{{ __('Dashboard') }}</a>
                                    <a class="dropdown-item" href="{{ route('settings') }}">{{ __('Settings') }}</a>

                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>                           
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>             
    </div>
</body>
</html>
