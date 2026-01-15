<?php
session_start();

if (isset($_POST['name']) && !empty(trim($_POST['name']))) {
  $_SESSION['player_name'] = htmlspecialchars(trim($_POST['name']));
}
if (!isset($_SESSION['player_name']) || empty(trim($_SESSION['player_name']))) {
  header('Location: landing.php');
  exit;
}
$playerName = $_SESSION['player_name'];

$isGameOver = isset($_SESSION['game_over']) && $_SESSION['game_over'] === true;
$lastScore = $_SESSION['last_score'] ?? 0;

if ($isGameOver) {
  unset($_SESSION['game_over']);
}

require_once 'koneksi.php';
$leaderboard = [];
$query = "
    SELECT u.name, MAX(us.skor) as best_score 
    FROM user_score us
    JOIN user u ON us.id_user = u.id_user
    GROUP BY u.id_user, u.name
    ORDER BY best_score DESC
    LIMIT 5
";
$result = $koneksi->query($query);
if ($result) {
  while ($row = $result->fetch_assoc()) {
    $leaderboard[] = $row;
  }
}

?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="icon" type="image/png" href="images/favicon.ico" />
  <title>FlapGo -
    <?php echo htmlspecialchars($playerName); ?>
  </title>
  <link rel="stylesheet" href="style.css" />
</head>

<body>

  <div class="background">
    <div class="sun"></div>
    <div class="mountain mountain1"></div>
    <div class="mountain mountain2"></div>
    <div class="mountain mountain3"></div>

    <div class="cloud cloud1"></div>
    <div class="cloud cloud2"></div>
    <div class="cloud cloud3"></div>
    <div class="ground"></div>
  </div>

  <img src="images/Bird.png" alt="gambar-burung" class="bird" id="bird-1" />

  <div class="leaderboard">
    <h2>Top 5</h2>
    <div class="leaderboard-list">
      <?php if (empty($leaderboard)): ?>
        <div class="leaderboard-empty">Belum ada skor</div>
      <?php else: ?>
        <?php foreach ($leaderboard as $index => $player): ?>
          <div class="leaderboard-item <?php echo ($player['name'] === $playerName) ? 'highlight' : ''; ?>">
            <span class="rank">
              <?php echo $index + 1; ?>
            </span>
            <span class="player-name-lb">
              <?php echo htmlspecialchars($player['name']); ?>
            </span>
            <span class="player-score">
              <?php echo $player['best_score']; ?>
            </span>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>

  <div class="message" <?php echo $isGameOver ? '' : 'style="display:block;"'; ?>>
    <div class="message-content">
      <?php if ($isGameOver): ?>
        <?php
        require_once 'koneksi.php';

        $stmt = $koneksi->prepare("
          SELECT MAX(us.skor) as best_score 
          FROM user_score us
          JOIN user u ON us.id_user = u.id_user
          WHERE u.name = ?
        ");
        $stmt->bind_param("s", $playerName);
        $stmt->execute();
        $result = $stmt->get_result();
        $bestScore = $result->fetch_assoc()['best_score'] ?? 0;
        $stmt->close();
        $koneksi->close();

        $isNewRecord = ($lastScore >= $bestScore && $lastScore > 0);
        ?>

        <h1 style="color: #e74c3c;">Game Over!</h1>

        <div style="display: flex; justify-content: space-around; margin: 20px 0;">
          <div style="text-align: center;">
            <p style="font-size: 0.9em; color: #666;">Skor Anda</p>
            <p style="font-size: 2em; font-weight: bold; color: #667eea; margin: 5px 0;">
              <?php echo $lastScore; ?>
            </p>
          </div>
          <div style="text-align: center;">
            <p style="font-size: 0.9em; color: #666;">Best Score</p>
            <p style="font-size: 2em; font-weight: bold; color: #f39c12; margin: 5px 0;">
              <?php echo $bestScore; ?>
            </p>
          </div>
        </div>

        <?php if ($isNewRecord): ?>
          <div class="new-record" style="display: inline-block;">
            REKOR BARU!
          </div>
        <?php endif; ?>

        <p class="start-text">
          Tekan <span class="key">Enter</span> Untuk Main Lagi
        </p>
        <a href="logout.php"
          style="color: #666; text-decoration: none; font-size: 0.9em; margin-top: 10px; display: inline-block;">
          👤 Ganti Pemain
        </a>
      <?php else: ?>
        <h1>FlapGo</h1>
        <p class="player-name">Pemain: <strong>
            <?php echo htmlspecialchars($playerName); ?>
          </strong></p>
        <p class="start-text">
          Tekan <span class="key">Enter</span> Untuk Mulai
        </p>
        <p class="control-text">
          <span class="arrow">↑</span> Panah Atas Untuk Kontrol
        </p>
      <?php endif; ?>
    </div>
  </div>

  <div class="score">
    <span class="score_title">Skor: </span>
    <span class="score_val">0</span>
  </div>

  <form id="formSkor" method="POST" action="save_score.php" style="display:none;">
    <input type="hidden" name="player_name" value="<?php echo htmlspecialchars($playerName); ?>">
    <input type="hidden" name="score" id="scoreInput" value="0">
  </form>

  <script>
    const playerName = <?php echo json_encode($playerName); ?>;
  </script>
  <script src="script-flapgo.js"></script>
</body>

</html>