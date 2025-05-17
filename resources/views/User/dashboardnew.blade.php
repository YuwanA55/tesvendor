@extends('layoutUser.body')
@section('konten')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Main Content -->
<div class="container-fluid p-4">

    <!-- Grid Section for Temperature and Humidity -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div class="d-flex flex-column align-items-center">
                        <img src="{{ asset('images/suhu.png') }}" alt="Temperature Icon" class="pp mb-2">
                        <div class="text-center">
                            <p id="tempValue" class="h4 text-dark">Loading...</p>
                            <p class="text-muted">Temperature</p>
                        </div>
                    </div>
                    <span id="statusLabel" class="badge bg-success text-white position-absolute top-0 end-0 m-2">Normal</span>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div class="d-flex flex-column align-items-center">
                        <img src="{{ asset('images/humadity.png') }}" alt="Humidity Icon" class="pp mb-2">
                        <div class="text-center">
                            <p id="humidityValue" class="h4 text-dark">Loading...</p>
                            <p class="text-muted">Humidity</p>
                        </div>
                    </div>
                    <span class="badge bg-primary text-white position-absolute top-0 end-0 m-2">Normal</span>
                </div>
            </div>
        </div>
    </div>

    <style>
        .pp {
            width: 30px;
            height: 30px;
        }
    </style>

    <!-- Charts Section -->
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h3 class="h6 text-dark">Temperature & Humidity Trends</h3>
                        <select class="form-select form-select-sm w-auto">
                            <option>Last 24 Hours...</option>
                        </select>
                    </div>
                    <div class="h-100 d-flex align-items-center justify-content-center bg-light rounded">
                        <p class="text-muted">Chart Placeholder</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h3 class="h6 text-dark">Light & CO₂ Analysis</h3>
                        <select class="form-select form-select-sm w-auto">
                            <option>Last 24 Hours...</option>
                        </select>
                    </div>
                    <div class="h-100 d-flex align-items-center justify-content-center bg-light rounded">
                        <p class="text-muted">Chart Placeholder</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
  const ctx = document.getElementById('tempChart').getContext('2d');
  const maxDataPoints = 20;
  const tempData = {
    labels: [],
    datasets: [{
      label: 'Temperature',
      data: [],
      borderColor: 'rgba(75, 192, 192, 1)',       // default hijau
      backgroundColor: 'rgba(75, 192, 192, 0.2)', // default hijau transparan
      fill: true,
      tension: 0.4,
      pointRadius: 0
    }]
  };

  const tempChart = new Chart(ctx, {
    type: 'line',
    data: tempData,
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false }
      },
      scales: {
        x: { display: false },
        y: { display: true, min: 20, max: 50 }
      }
    }
  });

  async function fetchSensorData() {
    try {
      const response = await fetch('https://testsuhu-845a0-default-rtdb.firebaseio.com/sensorData.json');
      const data = await response.json();

      const temp = data.temperature;
      const humidity = data.humidity;
      const now = new Date().toLocaleTimeString();

      // Update chart data
      tempData.labels.push(now);
      tempData.datasets[0].data.push(temp);
      if (tempData.labels.length > maxDataPoints) {
        tempData.labels.shift();
        tempData.datasets[0].data.shift();
      }

      // Ganti warna chart berdasarkan suhu
      if (temp > 30) {
        tempData.datasets[0].borderColor = 'rgba(255, 99, 132, 1)';      // merah
        tempData.datasets[0].backgroundColor = 'rgba(255, 99, 132, 0.2)';
      } else {
        tempData.datasets[0].borderColor = 'rgba(75, 192, 192, 1)';      // hijau
        tempData.datasets[0].backgroundColor = 'rgba(75, 192, 192, 0.2)';
      }

      tempChart.update();

      // Update value text
      document.getElementById('tempValue').textContent = temp + '°C';
      document.getElementById('humidityValue').textContent = humidity + '%';
      document.getElementById('lastUpdate').textContent = 'Last updated: ' + now;

      // Update status label
      const label = document.getElementById('statusLabel');
      if (temp > 30) {
        label.textContent = 'Panas';
        label.classList.remove('bg-success');
        label.classList.add('bg-danger');
      } else {
        label.textContent = 'Normal';
        label.classList.remove('bg-danger');
        label.classList.add('bg-success');
      }

    } catch (error) {
      console.error('Error fetching data:', error);
    }
  }

  // Ambil data setiap 2 detik
  setInterval(fetchSensorData, 2000);
  fetchSensorData();
</script>

@endsection
