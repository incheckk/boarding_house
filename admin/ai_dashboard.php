<?php
$pageTitle = "AI Analytics Dashboard";
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/php/admin-header.php';
require_once __DIR__ . '/php/admin-sidebar.php';

// === Fetch Room Analytics ===
$sql = "
    SELECT 
        r.room_size,
        r.room_number,
        COUNT(CASE WHEN el.event_type = 'view' THEN 1 END) AS views,
        COUNT(CASE WHEN el.event_type = 'reserve' THEN 1 END) AS reservations
    FROM room r
    LEFT JOIN event_logs el ON r.room_id = el.room_id
    GROUP BY r.room_id
    ORDER BY r.room_size, r.room_number
";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$analytics = $stmt->fetchAll(PDO::FETCH_ASSOC);

// === Group by Room Type ===
$grouped_analytics = [];
foreach ($analytics as $row) {
    $grouped_analytics[$row['room_size']][] = $row;
}

// === Basic Stats ===
$total_views = array_sum(array_column($analytics, 'views'));
$total_reservations = array_sum(array_column($analytics, 'reservations'));

$max_views = $analytics ? max(array_column($analytics, 'views')) : 0;
$most_viewed = array_filter($analytics, fn($r) => $r['views'] == $max_views);
$most_viewed_room = $most_viewed ? reset($most_viewed)['room_number'] : '—';
$most_viewed_display = $max_views > 0 ? "Room $most_viewed_room ($max_views views)" : "No views yet";

// === CALL PYTHON SCRIPT (100% WORKING ON WINDOWS/XAMPP) ===
$insights = null;
$python_script = __DIR__ . '/analyze_rooms.py';

if (file_exists($python_script) && !empty($analytics)) {
    $json_data = json_encode($analytics, JSON_UNESCAPED_SLASHES);

    $descriptor = [
        0 => ['pipe', 'r'],  // stdin
        1 => ['pipe', 'w'],  // stdout
        2 => ['pipe', 'w']   // stderr
    ];

    $process = proc_open('python -u "' . $python_script . '"', $descriptor, $pipes);

    if (is_resource($process)) {
        fwrite($pipes[0], $json_data);
        fclose($pipes[0]);

        $output = stream_get_contents($pipes[1]);
        fclose($pipes[1]);

        $error = stream_get_contents($pipes[2]);
        fclose($pipes[2]);

        proc_close($process);

        if ($error) {
            $insights = ['error' => 'Python Error: ' . trim($error)];
        } else {
            $insights = json_decode($output, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $insights = ['error' => 'Invalid JSON from Python: ' . json_last_error_msg()];
            }
        }
    } else {
        $insights = ['error' => 'Failed to start Python process'];
    }
} else {
    $insights = empty($analytics) ? ['info' => 'No data to analyze yet'] : ['error' => 'analyze_rooms.py not found'];
}
?>

<link rel="stylesheet" href="admin_style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<div class="dashboard-container">
    <main class="main-content">
        <div class="page-header">
            <h1>AI Analytics Dashboard</h1>
            <p>Real-time insights powered by machine learning</p>
        </div>

        <!-- Summary Cards -->
        <div class="stats-grid">
            <div class="stat-card animated fadeInUp">
                <div class="stat-card-icon blue"><i class="fas fa-eye"></i></div>
                <h3>Total Views</h3>
                <div class="number"><?= $total_views ?></div>
            </div>
            <div class="stat-card animated fadeInUp" style="animation-delay:0.1s">
                <div class="stat-card-icon green"><i class="fas fa-calendar-check"></i></div>
                <h3>Total Inquiries</h3>
                <div class="number"><?= $total_reservations ?></div>
            </div>
            <div class="stat-card animated fadeInUp" style="animation-delay:0.2s">
                <div class="stat-card-icon orange"><i class="fas fa-star"></i></div>
                <h3>Most Viewed Room</h3>
                <div class="number"><?= htmlspecialchars($most_viewed_display) ?></div>
            </div>
        </div>

        <!-- Room Analytics by Type (Accordion) -->
        <section class="section">
            <div class="section-header"><h2>Room Analytics by Type</h2></div>

            <?php if (empty($grouped_analytics)): ?>
                <p class="no-data">No room data available yet.</p>
            <?php else: ?>
                <div class="accordion">
                    <?php $delay = 0; foreach ($grouped_analytics as $type => $rooms): ?>
                        <?php
                        $type_views = array_sum(array_column($rooms, 'views'));
                        $type_reservations = array_sum(array_column($rooms, 'reservations'));
                        ?>
                        <div class="accordion-item animated fadeInUp" style="animation-delay:<?= $delay ?>s">
                            <div class="accordion-header">
                                <h3><?= htmlspecialchars($type) ?></h3>
                                <div class="accordion-summary">
                                    <span>Views: <?= $type_views ?></span>
                                    <span>Inquiries: <?= $type_reservations ?></span>
                                </div>
                                <i class="fas fa-chevron-down accordion-icon"></i>
                            </div>
                            <div class="accordion-body">
                                <div class="rooms-grid">
                                    <?php foreach ($rooms as $row): ?>
                                        <div class="room-card">
                                            <h4>Room <?= htmlspecialchars($row['room_number']) ?></h4>
                                            <div class="stat-box">
                                                <i class="fas fa-eye"></i>
                                                <div><strong><?= $row['views'] ?></strong><span>Views</span></div>
                                            </div>
                                            <div class="stat-box">
                                                <i class="fas fa-calendar-check"></i>
                                                <div><strong><?= $row['reservations'] ?></strong><span>Inquiries</span></div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <?php $delay += 0.1; endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <!-- AI Insights Section -->
        <section class="section" style="margin-top:50px;">
            <div class="section-header">
                <h2>AI-Powered Insights & Predictions</h2>
            </div>

            <?php if (isset($insights['error'])): ?>
                <div class="no-data" style="background:#ffebee;color:#c62828;padding:20px;border-radius:12px;">
                    <strong>AI Engine Error:</strong><br>
                    <?= nl2br(htmlspecialchars($insights['error'])) ?>
                </div>
            <?php elseif (isset($insights['info'])): ?>
                <p class="no-data"><?= htmlspecialchars($insights['info']) ?></p>
            <?php elseif ($insights): ?>
                <div class="insights-grid">
                    <div class="stat-card">
                        <h3>Views → Inquiries Correlation</h3>
                        <div class="number" style="font-size:3rem;color:#ff9500"><?= $insights['correlation'] ?></div>
                        <p>Closer to 1 = Views strongly predict bookings</p>
                    </div>
                    <div class="stat-card">
                        <h3>Predicted Bookings</h3>
                        <div class="number" style="font-size:3rem;color:#34c759">≈ <?= $insights['predicted_reservations'] ?></div>
                        <p>For a room with <strong>10 more views</strong> than average</p>
                    </div>
                </div>

                <h3 style="margin:40px 0 15px;font-size:1.4rem;">Recommended Rooms to Promote</h3>
                <div class="rooms-grid">
                    <?php foreach ($insights['top_rooms'] as $room): ?>
                        <div class="stat-card" style="text-align:center;padding:25px;">
                            <h4 style="color:#ff9500;margin:0 0 10px;">Room <?= htmlspecialchars($room['room_number']) ?></h4>
                            <p style="margin:8px 0;font-size:1.1rem;">
                                <strong><?= $room['views'] ?></strong> views → 
                                <strong><?= $room['reservations'] ?></strong> inquired
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>

                <h3 style="margin:50px 0 15px;font-size:1.4rem;">Average Performance by Room Type</h3>
                <table class="data-table">
                    <thead>
                        <tr><th>Room Type</th><th>Avg Views</th><th>Avg Inquiries</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($insights['group_stats'] as $type => $stats): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($type) ?></strong></td>
                                <td><?= number_format($stats['views'], 2) ?></td>
                                <td><?= number_format($stats['reservations'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>
    </main>
</div>

<!-- Styles -->
<style>
    .stats-grid, .insights-grid, .rooms-grid, .accordion { gap:20px; }
    .accordion { margin-top:25px; display:flex; flex-direction:column; }
    .accordion-item { background:#fff; border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,0.05); overflow:hidden; }
    .accordion-header { display:flex; justify-content:space-between; align-items:center; padding:20px; background:#f5f5f7; cursor:pointer; transition:.3s; }
    .accordion-header:hover { background:#e8e8ed; }
    .accordion-header h3 { margin:0; font-size:18px; font-weight:600; }
    .accordion-summary { display:flex; gap:25px; color:#666; font-size:14px; }
    .accordion-icon { transition:transform .3s; }
    .accordion-item.active .accordion-icon { transform:rotate(180deg); }
    .accordion-body { max-height:0; overflow:hidden; padding:0 20px; transition:max-height .4s ease, padding .4s ease; }
    .accordion-item.active .accordion-body { max-height:2000px; padding:20px; }
    .room-card, .stat-card { transition:transform .3s; }
    .room-card:hover, .stat-card:hover { transform:translateY(-5px); }
    .room-card { background:#f9f9f9; padding:20px; border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,0.05); }
    .room-card h4 { margin:0 0 15px 0; font-size:16px; font-weight:600; }
    .stat-box { display:flex; align-items:center; gap:15px; padding:10px 0; border-left:4px solid #ff9500; }
    .no-data { text-align:center; padding:30px; background:#fff; border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,0.05); color:#666; }
</style>

<!-- Accordion Toggle Script -->
<script>
    document.querySelectorAll('.accordion-header').forEach(header => {
        header.addEventListener('click', () => {
            const item = header.parentElement;
            const body = item.querySelector('.accordion-body');
            item.classList.toggle('active');
            body.style.maxHeight = item.classList.contains('active') ? body.scrollHeight + 'px' : '0';
        });
    });
</script>

<