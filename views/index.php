<?php
require 'function/function.php';
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>dynamic courses</title>
    <link href="views/assets/css/bootstrap.css" rel="stylesheet">
</head>

<body>
<nav class="navbar navbar-expand-md navbar-dark  bg-dark">
    <a class="navbar-brand" href="">dynamic courses</a>
</nav>
<div class="container">
    <div class="row">
        <main role="main" class="col-md-12 ml-sm-auto col-lg-12 px-4">
            <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Выберите период динамики курса</h1>
                    <form action="#" method="post">
                        <div class="form-row">
                            <div class="col">
                                <input type="date" class="form-control" name="ot" required placeholder="01.01.2019">
                            </div>
                            <div class="col">
                                <input type="date" class="form-control" name="do"required placeholder="01.01.2019">
                            </div>
                            <button type="submit" class="btn btn-primary" name="go">Посмотреть</button>
                        </div>
                    </form>
            </div>

            <canvas class="my-4 w-100 chartjs-render-monitor" id="myChart" width="2304" height="972" style="display: block; height: 486px; width: 1152px;">


            </canvas>
        </main>
    </div>
</div>
<script src="views/assets/jquery-3.4.1.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script>window.jQuery || document.write('<script src="views/assets/jquery-3.4.1.js"><\/script>')</script><script src="/docs/4.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-xrRywqdh3PHs8keKZN+8zzc5TX0GRTLCcmivcbNJWm2rs5C8PRhcEn3czEjhAO9o" crossorigin="anonymous"></script>
<script src="views/assets/js/feather.js"></script>
<script src="views/assets/js/chart.js"></script>
<?php
$dynamicCourseOfDate = dynamicCourses($_POST);
?>
<script>

    (function () {
        'use strict'

        feather.replace()
        var ctx = document.getElementById('myChart')
        var myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [
                    <?php
                        foreach ($dynamicCourseOfDate as $date)
                        {
                            echo "'" . $date['date'] . "',";
                        }
                    ?>
                ],
                datasets: [{
                    data: [

                        <?php
                        foreach ($dynamicCourseOfDate as $course)
                        {
                            echo $course['course'] . ',';
                        }
                        ?>
                    ],
                    lineTension: 0,
                    backgroundColor: 'transparent',
                    borderColor: '#007bff',
                    borderWidth: 4,
                    pointBackgroundColor: '#007bff'
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: false
                        }
                    }]
                },
                legend: {
                    display: false
                }
            }
        })
    }())

</script>
</body>
</html>