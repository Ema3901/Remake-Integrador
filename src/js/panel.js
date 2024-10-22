        // Datos ficticios para el gráfico de ventas del día
        const dailySalesData = {
            labels: ['Hora 1', 'Hora 2', 'Hora 3', 'Hora 4', 'Hora 5', 'Hora 6', 'Hora 7', 'Hora 8', 'Hora 9', 'Hora 10', 'Hora 11', 'Hora 12',],
            datasets: [{
                label: 'Ventas ($ MXN)',
                data: [500, 1000, 750, 1250, 1500, 200, 500, 1200, 900, 400, 100, 800],
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                fill: true,
                tension: 0.4,
            }]
        };

        const dailySalesConfig = {
            type: 'line',
            data: dailySalesData,
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        };

        // Inicialización del gráfico de ventas del día
        const dailySalesChart = new Chart(
            document.getElementById('dailySalesChart'),
            dailySalesConfig
        );

        // Datos ficticios para el gráfico de ventas del mes
        const monthlySalesData = {
            labels: ['Semana 1', 'Semana 2', 'Semana 3', 'Semana 4'],
            datasets: [{
                label: 'Ventas ($ MXN)',
                data: [12000, 14000, 11000, 13000],
                borderColor: 'rgba(255, 99, 132, 1)',
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                fill: true,
                tension: 0.4,
            }]
        };

        const monthlySalesConfig = {
            type: 'line',
            data: monthlySalesData,
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        };

        // Inicialización del gráfico de ventas del mes
        const monthlySalesChart = new Chart(
            document.getElementById('monthlySalesChart'),
            monthlySalesConfig
        );

// version 0.0.3