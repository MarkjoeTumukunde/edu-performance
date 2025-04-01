<?php
include 'db_connect.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>All Students Performance Trend - Admin</title>
  <link rel="stylesheet" href="style.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    main {
      padding: 30px;
      margin-top: 100px;
    }
    .filters {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      margin: 20px auto;
      width: 90%;
      justify-content: center;
    }

    .filters input {
      padding: 10px;
      min-width: 300px;
      max-width: 80%;
      flex: 1;
    }

    canvas {
      width: 100% !important;
      max-width: 1000px;
      margin: 40px auto;
      display: block;
      background: #fff;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    .section-title {
      text-align: center;
      margin: 10px;
    }

    @media screen and (max-width: 600px) {
      main {
        padding: 20px;
      }

      .filters input {
        min-width: unset;
        width: 100%;
        margin-left: 0;
      }
    }
  </style>
</head>
<body>
  <header id="header">
    <nav>
      <div class="nav-container">
        <a href="index.html"><img src="Images/home2.png" alt="Logo" id="logo" /></a>
        <span>ADMIN: STUDENT PERFORMANCE TRENDS</span>
        <div class="nav-buttons">
          <a href="admin_view_student_performance.php"><button>← Back</button></a>
        </div>
      </div>
    </nav>
  </header>

  <main>
    <div class="filters">
      <input type="text" id="searchStudent" placeholder="Search by Student ID...">
    </div>

    <canvas id="trendChart"></canvas>
  </main>

  <footer>
    <p>All rights reserved &copy; 2025 Markjoe | Roland</p>
  </footer>

  <script>
    const chartCtx = document.getElementById('trendChart').getContext('2d');
    let chart;

    function loadTrendData() {
      const params = new URLSearchParams({
        student_id: document.getElementById('searchStudent').value
      });

      fetch('fetch_student_trend_data.php?' + params.toString())
        .then(res => res.json())
        .then(data => {
          if (chart) chart.destroy();

          chart = new Chart(chartCtx, {
            type: 'line',
            data: {
              labels: data.labels,
              datasets: data.datasets
            },
            options: {
              responsive: true,
              plugins: {
                title: {
                  display: true,
                  text: data.title
                }
              },
              scales: {
                y: {
                  beginAtZero: true,
                  max: 100,
                  title: {
                    display: true,
                    text: 'Marks'
                  }
                },
                x: {
                  title: {
                    display: true,
                    text: 'Course Units / Time'
                  }
                }
              }
            }
          });
        });
    }

    document.getElementById('searchStudent').addEventListener('input', loadTrendData);

    // Initial load for all students
    loadTrendData();
  </script>
</body>
</html>