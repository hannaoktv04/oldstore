@extends('layouts.portal')

@section('content')
<div class="container">
  <div class="center-align mb-4">
    <h5 class="mt-1 mb-3"><strong>SIGMA Portal</strong></h5>
  </div>

  @php
    use Illuminate\Support\Facades\Auth;

    $user = Auth::user();
    $route = 'home'; // default
    if ($user && $user->hasRole('admin')) {
        $route = 'admin.dashboard.index';
    } elseif ($user && $user->hasRole('staff_pengiriman')) {
        $route = 'staff-pengiriman.dashboard';
    }

    $apps = [
        [
            'title' => 'E-Persediaan',
            'image' => asset('assets/img/peri.png'),
            'url' => route($route),
            'color' => 'purple'
        ],
        ['title' => 'Lorem', 'icon' => 'ri-school-line', 'url' => '#', 'color' => 'teal'],
        ['title' => 'Lorem', 'icon' => 'ri-time-line', 'url' => '#', 'color' => 'deep-orange'],
        ['title' => 'Lorem', 'icon' => 'ri-bar-chart-2-line', 'url' => '#', 'color' => 'green'],
        ['title' => 'Lorem', 'icon' => 'ri-folder-line', 'url' => '#', 'color' => 'blue'],
        ['title' => 'Lorem', 'icon' => 'ri-mail-send-line', 'url' => '#', 'color' => 'indigo'],
        ['title' => 'Lorem', 'icon' => 'ri-user-settings-line', 'url' => '#', 'color' => 'brown'],
        ['title' => 'Lorem', 'icon' => 'ri-service-line', 'url' => '#', 'color' => 'pink'],
    ];
  @endphp

  <div class="container">
    @foreach(array_chunk($apps, 4) as $chunk)
      <div class="row mb-4">
        @foreach($chunk as $app)
          <div class="col s12 m6 l3">
            <a href="{{ $app['url'] }}" class="app-link">
              <div class="card hoverable app-card">
                <div class="card-content center-align d-flex-column">
                  @if(isset($app['image']))
                    <img src="{{ $app['image'] }}" alt="{{ $app['title'] }}" class="app-icon-img">
                  @else
                    <i class="{{ $app['icon'] }} ri-3x {{ $app['color'] }}-text text-darken-2"></i>
                  @endif
                  <p class="app-title" style="margin-top: 12px;">{{ $app['title'] }}</p>
                </div>
              </div>
            </a>
          </div>
        @endforeach
      </div>
    @endforeach
  </div>

  <style>
    .app-card {
      height: 170px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 12px;
      transition: all 0.25s ease;
      background-color: #fcfcfc;
    }

    .app-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 6px 16px rgba(0,0,0,0.1);
    }

    .card-content.d-flex-column {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
    }

    .app-title {
      font-weight: 600;
      font-size: 0.9rem;
      color: #263238;
      margin-top: 10px;
      text-align: center;
    }

    .app-link {
      text-decoration: none;
    }

    .app-icon-img {
      height: 48px;
      width: auto;
    }

    @media only screen and (max-width: 600px) {
      .col.s12.m6.l3 {
        width: 50% !important;
      }
    }
  </style>
</div>
@endsection
