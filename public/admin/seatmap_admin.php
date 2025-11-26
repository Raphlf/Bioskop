<?php
// public/admin/seatmap_admin.php
include __DIR__ . '/../../config.php';

$film_id = (int)($_GET['film_id'] ?? 0);
if (!$film_id) { header("Location: films_manage.php"); exit; }

$film = $db->query("SELECT * FROM films WHERE id=$film_id")->fetch_assoc();
$studio = $db->query("SELECT * FROM studios WHERE id={$film['studio_id']}")->fetch_assoc();

// seats per studio
$seats_res = $db->query("SELECT * FROM seats WHERE studio_id={$studio['id']} ORDER BY row_label, col_num");
$seats = [];
while ($s = $seats_res->fetch_assoc()) $seats[$s['row_label']][$s['col_num']] = $s;

// booked seats for this film
$booked_res = $db->query("SELECT seat_id FROM bookings WHERE film_id=$film_id AND status='booked'");
$booked_map = [];
while ($b = $booked_res->fetch_assoc()) $booked_map[$b['seat_id']] = true;
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Seatmap - <?= htmlspecialchars($film['judul']) ?></title>
<style>
body{font-family:Arial;background:#f7f8fb;padding:20px}
.container{max-width:1000px;margin:0 auto}
.screen{width:60%;margin:12px auto;background:#222;color:#fff;padding:6px;text-align:center;border-radius:6px}
.seat{width:44px;height:44px;border-radius:6px;display:flex;align-items:center;justify-content:center;cursor:default}
.available{background:#28a745;color:#fff}
.booked{background:#e53935;color:#fff}
.blocked{background:#6c757d;color:#fff}
.vip{box-shadow:0 0 0 3px rgba(241,196,15,0.12)}
.legend{display:flex;gap:12px;margin-top:14px}
.legend div{padding:6px 10px;border-radius:6px;color:#fff}
.btn{padding:8px 12px;border-radius:6px;background:#333;color:#fff;text-decoration:none}
</style>
</head>
<body>
<div class="container">
<a class="btn" href="films_manage.php">⬅ Kembali</a>
<h2>Seatmap – <?= htmlspecialchars($film['judul']) ?> (<?= htmlspecialchars($studio['nama_studio']) ?>)</h2>

<div class="screen">LAYAR</div>

<div style="display:flex;justify-content:center;margin-top:12px">
    <div style="width:100%;max-width:920px">
        <?php
        $rows = $studio['baris'];
        $cols = $studio['kolom'];
        echo '<div style="display:grid;grid-template-columns:repeat(' . $cols . ', 50px);gap:8px;justify-content:center">';
        for ($r = 0; $r < $rows; $r++) {
            $row_label = chr(65 + $r);
            for ($c = 1; $c <= $cols; $c++) {
                if (!isset($seats[$row_label][$c])) {
                    echo "<div style='width:50px;height:50px'></div>";
                    continue;
                }
                $s = $seats[$row_label][$c];
                $cls = 'seat ';
                if (isset($booked_map[$s['id']])) $cls .= 'booked';
                else if ($s['status'] === 'blocked') $cls .= 'blocked';
                else $cls .= 'available';

                if ($s['type'] === 'vip') $vip = 'vip'; else $vip = '';
                echo "<div class='$cls $vip' title='{$s['seat_name']} ({$s['type']})'>{$s['seat_name']}</div>";
            }
        }
        echo '</div>';
        ?>
    </div>
</div>

<div class="legend">
    <div style="background:#28a745">Available</div>
    <div style="background:#e53935">Booked</div>
    <div style="background:#6c757d">Blocked</div>
    <div style="background:#f1c40f;color:#222">VIP</div>
</div>

</div>
</body>
</html>
