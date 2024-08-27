document.addEventListener('DOMContentLoaded', function() {
    console.log('DOMContentLoaded event fired');
    fetchReservationsPerMonth();
    fetchTravelTypes();
    fetchAvailableDrivers();
    fetchAvailableVehicles();
});

function fetchReservationsPerMonth() {
    console.log('Fetching reservations per month');
    fetch('/api/reservations-per-month')
        .then(response => response.json())
        .then(data => {
            console.log('Reservations per month data:', data);
            createReservationsChart(data);
        })
        .catch(error => console.error('Error fetching reservations data:', error));
}

function createReservationsChart(data) {
    const ctx = document.getElementById('reservationsChart').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.labels,
            datasets: [{
                label: 'Reservations per Month',
                data: data.values,
                backgroundColor: 'rgba(75, 192, 192, 0.6)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                },
                x: {
                    ticks: {
                        autoSkip: false,
                        maxRotation: 90,
                        minRotation: 90
                    }
                }
            }
        }
    });

    // Add month selection
    const monthSelect = document.createElement('select');
    monthSelect.id = 'monthSelect';
    monthSelect.innerHTML = `
        <option value="all">All Months</option>
        ${data.labels.map((month, index) => `<option value="${index}">${month}</option>`).join('')}
    `;
    monthSelect.addEventListener('change', function() {
        const selectedIndex = this.value;
        if (selectedIndex === 'all') {
            chart.data.labels = data.labels;
            chart.data.datasets[0].data = data.values;
        } else {
            chart.data.labels = [data.labels[selectedIndex]];
            chart.data.datasets[0].data = [data.values[selectedIndex]];
        }
        chart.update();
    });

    const chartContainer = document.getElementById('reservationsChartContainer');
    chartContainer.insertBefore(monthSelect, chartContainer.firstChild);
}

function fetchTravelTypes() {
    console.log('Fetching travel types');
    fetch('/api/travel-types')
        .then(response => response.json())
        .then(data => {
            console.log('Travel types data:', data);
            createTravelTypesChart(data);
        })
        .catch(error => console.error('Error fetching travel types data:', error));
}

function createTravelTypesChart(data) {
    const ctx = document.getElementById('travelTypesChart').getContext('2d');
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Daily Transport', 'Outside Province Transport', 'Within Province Transport'],
            datasets: [{
                data: [data.daily, data.outside, data.within],
                backgroundColor: ['rgba(255, 99, 132, 0.6)', 'rgba(54, 162, 235, 0.6)', 'rgba(75, 192, 192, 0.6)']
            }]
        }
    });
}

function fetchAvailableDrivers() {
    const dateInput = document.getElementById('driverDate');
    const date = dateInput.value || new Date().toISOString().split('T')[0];
    dateInput.value = date;  // Set the input value if it was empty
    fetch(`/api/available-drivers?date=${date}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log('Available drivers data:', data);
            createAvailableDriversTable(data);
        })
        .catch(error => {
            console.error('Error fetching available drivers data:', error);
            document.getElementById('availableDriversTable').innerHTML = 'Error loading data';
        });
}

function createAvailableDriversTable(data) {
    const table = document.getElementById('availableDriversTable');
    table.innerHTML = `
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Status</th>
                </tr>
            </thead>
        </table>
        <div class="table-body-scroll">
            <table class="table">
                <tbody>
                    ${data.map(driver => `
                        <tr>
                            <td>${driver.name}</td>
                            <td>${driver.status}</td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
    `;
}

function fetchAvailableVehicles() {
    const dateInput = document.getElementById('vehicleDate');
    const date = dateInput.value || new Date().toISOString().split('T')[0];
    dateInput.value = date;  // Set the input value if it was empty
    fetch(`/api/available-vehicles?date=${date}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log('Available vehicles data:', data);
            createAvailableVehiclesTable(data);
        })
        .catch(error => {
            console.error('Error fetching available vehicles data:', error);
            document.getElementById('availableVehiclesTable').innerHTML = 'Error loading data';
        });
}

function createAvailableVehiclesTable(data) {
    const table = document.getElementById('availableVehiclesTable');
    table.innerHTML = `
        <table class="table">
            <thead>
                <tr>
                    <th>Vehicle</th>
                    <th>Status</th>
                </tr>
            </thead>
        </table>
        <div class="table-body-scroll">
            <table class="table">
                <tbody>
                    ${data.map(vehicle => `
                        <tr>
                            <td>${vehicle.name}</td>
                            <td>${vehicle.status}</td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
    `;
}