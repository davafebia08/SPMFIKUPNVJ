<!-- resources/views/admin/reports/chart.blade.php -->
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Chart</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        #chartContainer {
            width: 800px;
            height: 400px;
        }
    </style>
</head>

<body>
    <div id="chartContainer">
        <canvas id="chart"></canvas>
    </div>

    <script>
        // Data untuk chart
        const data = {
            labels: {!! json_encode($labels) !!},
            datasets: [{
                label: '{{ $title }}',
                data: {!! json_encode($data) !!},
                backgroundColor: {!! json_encode($backgroundColors) !!},
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(75, 192, 192, 1)'
                ],
                borderWidth: 1
            }]
        };

        // Konfigurasi
        const config = {
            type: 'bar',
            data: data,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: '{{ $title }}'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        };

        // Render chart
        const chartCanvas = document.getElementById('chart');
        new Chart(chartCanvas, config);
    </script>
</body>

</html>
