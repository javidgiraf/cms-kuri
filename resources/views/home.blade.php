@extends('layouts.page')
@section('content')
<main id="main" class="main">

    <div class="pagetitle">
        <h1>Dashboard</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('home')}}">Home</a></li>
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
        <div class="row">

            <!-- Left side columns -->
            <div class="col-lg-8">
                <div class="row">
                    <!-- Revenue Card -->
                    <div class="col-xxl-4 col-md-6">
                        <div class="card info-card customers-card">



                            <div class="card-body">
                                <h5 class="card-title">{{ __('Customers') }}</h5>

                                <div class="d-flex align-items-center">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="bi bi-people"></i>
                                    </div>
                                    <div class="ps-3">
                                        <h6>{{ $activeUsersCount }}</h6>
                                        <span class="text-muted small pt-2 ps-1">{{ __('Active Customers') }}</span>

                                    </div>
                                </div>

                            </div>

                        </div>
                    </div><!-- End Revenue Card -->

                    <div class="col-xxl-4 col-md-6">
                        <div class="card info-card revenue-card">



                            <div class="card-body">
                                <h5 class="card-title">{{ __('Schemes') }}</h5>

                                <div class="d-flex align-items-center">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="bi bi-clipboard"></i>
                                    </div>
                                    <div class="ps-3">
                                        <h6>{{ $schemesCount }}</h6>
                                        <span class="text-muted small pt-2 ps-1">{{ __('Active Schemes') }}</span>

                                    </div>
                                </div>

                            </div>

                        </div>
                    </div><!-- End Revenue Card -->



                    <div class="col-xxl-4 col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Scheme Chart</h5>

                                <!-- Column Chart -->
                                <div id="columnChart"></div>

                                <script>
                                    document.addEventListener("DOMContentLoaded", () => {
                                        // Fetch the chart data from the backend
                                        fetch('/scheme-chart-data') // Replace with your actual endpoint
                                            .then(response => response.json())
                                            .then(data => {
                                                // Render the grouped bar chart with fetched data
                                                new ApexCharts(document.querySelector("#columnChart"), {
                                                    series: data.chartData, // Array containing data for each scheme
                                                    chart: {
                                                        type: 'bar', // Bar chart type
                                                        height: 350, // Chart height
                                                    },
                                                    plotOptions: {
                                                        bar: {
                                                            horizontal: false, // Vertical bars
                                                            columnWidth: '55%', // Column width
                                                            endingShape: 'rounded', // Rounded corners
                                                        },
                                                    },
                                                    dataLabels: {
                                                        enabled: false, // Disable data labels on the bars
                                                    },
                                                    stroke: {
                                                        show: true, // Show stroke on bars
                                                        width: 2, // Stroke width
                                                        colors: ['transparent'], // Stroke color
                                                    },
                                                    xaxis: {
                                                        categories: data.categories, // The months (Jan - Dec)
                                                        title: {
                                                            text: "Months"
                                                        },
                                                    },
                                                    yaxis: {
                                                        title: {
                                                            text: "Total Scheme Amount (<?= \App\Models\Setting::CURRENCY ?>)" // Y-axis label
                                                        }
                                                    },
                                                    fill: {
                                                        opacity: 1, // Bar fill opacity
                                                    },
                                                    tooltip: {
                                                        y: {
                                                            formatter: function(val) {
                                                                return "<?= \App\Models\Setting::CURRENCY ?> " + val.toLocaleString(); // Format tooltip
                                                            }
                                                        }
                                                    },
                                                    legend: {
                                                        position: 'top', // Position of the legend
                                                        horizontalAlign: 'center', // Horizontal alignment
                                                    }
                                                }).render();
                                            })
                                            .catch(error => console.error('Error fetching chart data:', error));
                                    });
                                </script>
                                <!-- End Column Chart -->

                            </div>
                        </div>
                    </div>



                    <!-- Upcoming Payments -->
                    <div class="col-12">
                        <div class="card recent-sales overflow-auto">

                            <div class="card-body">
                                <h5 class="card-title">Latest Payments</h5>

                                <table class="table table-borderless">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Customer</th>
                                            <th scope="col">Scheme</th>
                                            <th scope="col">Date</th>
                                            <th scope="col">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($latestPayments as $latestPayment)
                                        <tr>
                                            <th scope="row">{{ $loop->iteration }}</th>
                                            <td>{{ $latestPayment->userSubscription->user->name }}</td>
                                            <td>{{ $latestPayment->userSubscription->scheme->title }}</td>
                                            <td>{{ date('d/m/Y', strtotime($latestPayment->paid_at)) }}</td>
                                            <td>{{ \App\Models\Setting::CURRENCY }} {{ number_format($latestPayment->total_scheme_amount, 2) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                            </div>

                        </div>
                    </div><!-- End Upcoming Payments -->

                    
                </div>
            </div><!-- End Left side columns -->

            <!-- Right side columns -->
            <div class="col-lg-4">

                <!-- Recent Activity -->
                <div class="card">
                    <div class="filter">
                        <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                            <li class="dropdown-header text-start">
                                <h6>Filter</h6>
                            </li>

                            <li><a class="dropdown-item" href="#">Today</a></li>
                            <li><a class="dropdown-item" href="#">This Month</a></li>
                            <li><a class="dropdown-item" href="#">This Year</a></li>
                        </ul>
                    </div>

                    <div class="card-body">
                        <h5 class="card-title">Recent Activity <span>| Today</span></h5>

                        <div class="activity">
                            @foreach ($logactivities as $activity)
                            <div class="activity-item d-flex">
                                <div class="activite-label">{{ DateDifference::dateDifference($activity->created_at) }}</div>
                                <i class='bi bi-circle-fill activity-badge text-success align-self-start'></i>
                                <div class="activity-content">
                                    <h6 class="fw-bold">{{ $activity->subject }}</h6>
                                </div>
                            </div><!-- End activity item-->
                            @endforeach










                        </div>

                    </div>
                </div><!-- End Recent Activity -->

                <!-- Website Traffic -->
                <div class="card">


                    <div class="card-body pb-0">
                        <h5 class="card-title">Scheme Chart</h5>

                        <div id="trafficChart" style="min-height: 400px;" class="echart"></div>

                        <script>
                            document.addEventListener("DOMContentLoaded", () => {
                                const schemes = @json($schemes); // Pass the PHP array to JavaScript

                                echarts.init(document.querySelector("#trafficChart")).setOption({
                                    tooltip: {
                                        trigger: 'item'
                                    },
                                    legend: {
                                        top: '5%',
                                        left: 'center'
                                    },
                                    series: [{
                                        name: 'Total Amount Received',
                                        type: 'pie',
                                        radius: ['40%', '70%'],
                                        avoidLabelOverlap: false,
                                        label: {
                                            show: false,
                                            position: 'center'
                                        },
                                        emphasis: {
                                            label: {
                                                show: true,
                                                fontSize: '18',
                                                fontWeight: 'bold'
                                            }
                                        },
                                        labelLine: {
                                            show: false
                                        },
                                        data: schemes // Use the data from the backend
                                    }]
                                });
                            });
                        </script>
                    </div>

                    <!-- News & Updates Traffic -->
                    {{-- <div class="card">
                    <div class="filter">
                        <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                            <li class="dropdown-header text-start">
                                <h6>Filter</h6>
                            </li>

                            <li><a class="dropdown-item" href="#">Today</a></li>
                            <li><a class="dropdown-item" href="#">This Month</a></li>
                            <li><a class="dropdown-item" href="#">This Year</a></li>
                        </ul>
                    </div>

                    <div class="card-body pb-0">
                        <h5 class="card-title">News &amp; Updates <span>| Today</span></h5>

                        <div class="news">
                            <div class="post-item clearfix">
                                <img src="assets/img/news-1.jpg" alt="">
                                <h4><a href="#">Nihil blanditiis at in nihil autem</a></h4>
                                <p>Sit recusandae non aspernatur laboriosam. Quia enim eligendi sed ut harum...</p>
                            </div>

                            <div class="post-item clearfix">
                                <img src="assets/img/news-2.jpg" alt="">
                                <h4><a href="#">Quidem autem et impedit</a></h4>
                                <p>Illo nemo neque maiores vitae officiis cum eum turos elan dries werona nande...</p>
                            </div>

                            <div class="post-item clearfix">
                                <img src="assets/img/news-3.jpg" alt="">
                                <h4><a href="#">Id quia et et ut maxime similique occaecati ut</a></h4>
                                <p>Fugiat voluptas vero eaque accusantium eos. Consequuntur sed ipsam et totam...</p>
                            </div>

                            <div class="post-item clearfix">
                                <img src="assets/img/news-4.jpg" alt="">
                                <h4><a href="#">Laborum corporis quo dara net para</a></h4>
                                <p>Qui enim quia optio. Eligendi aut asperiores enim repellendusvel rerum cuder...</p>
                            </div>

                            <div class="post-item clearfix">
                                <img src="assets/img/news-5.jpg" alt="">
                                <h4><a href="#">Et dolores corrupti quae illo quod dolor</a></h4>
                                <p>Odit ut eveniet modi reiciendis. Atque cupiditate libero beatae dignissimos eius...</p>
                            </div>

                        </div><!-- End sidebar recent posts-->

                    </div>
                </div> --}}<!-- End News & Updates -->

                </div><!-- End Right side columns -->

            </div>
    </section>

</main><!-- End #main -->

@endsection

@push('scripts')
<script>

</script>
@endpush