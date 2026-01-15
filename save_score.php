<?php
session_start();
require_once 'koneksi.php';

if (!isset($_POST['player_name']) || !isset($_POST['score'])) {
    die("Error: Data tidak lengkap!");
}

$playerName = trim($_POST['player_name']);
$score = intval($_POST['score']);

if (empty($playerName) || strlen($playerName) < 2) {
    die("Error: Nama tidak valid! Nama harus minimal 2 karakter.");
}

if ($score < 0) {
    die("Error: Skor tidak valid!");
}

if (!isset($_SESSION['player_name']) || $_SESSION['player_name'] !== $playerName) {
    die("Error: Session tidak valid! Silakan mulai dari awal.");
}

try {
    $stmt = $koneksi->prepare("SELECT id_user FROM user WHERE name = ?");

    if (!$stmt) {
        throw new Exception("Prepare statement gagal: " . $koneksi->error);
    }

    $stmt->bind_param("s", $playerName);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $userId = $row['id_user'];
    } else {
        $stmt->close();
        $stmt = $koneksi->prepare("INSERT INTO user (name) VALUES (?)");

        if (!$stmt) {
            throw new Exception("Prepare insert user gagal: " . $koneksi->error);
        }

        $stmt->bind_param("s", $playerName);

        if (!$stmt->execute()) {
            throw new Exception("Gagal membuat user baru: " . $stmt->error);
        }

        $userId = $koneksi->insert_id;
    }
    $stmt->close();
    $stmt = $koneksi->prepare("INSERT INTO user_score (id_user, skor) VALUES (?, ?)");

    if (!$stmt) {
        throw new Exception("Prepare insert score gagal: " . $koneksi->error);
    }

    $stmt->bind_param("ii", $userId, $score);

    if ($stmt->execute()) {
        $_SESSION['last_score'] = $score;
        $_SESSION['game_over'] = true;
        header("Location: index.php");
        exit;
    } else {
        throw new Exception("Gagal menyimpan skor: " . $stmt->error);
    }

} catch (Exception $e) {
    die("Error: " . $e->getMessage());
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    $koneksi->close();
}
?>