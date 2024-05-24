<!DOCTYPE html>
<html>
<head>
    <title>Permainan Suit Jawa</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h1>Permainan Suit Jawa</h1>

    <form method="post">
        <p>Pilih salah satu angka dari 1, 2, 3</p>
        <div>
            <label for="pilihan1">
                <img src="Gajah.jpg" alt="Gajah">
                <input type="radio" name="pilihan" value="1" id="pilihan1"> 1 (Gajah)
            </label>
        </div>
        <div>
            <label for="pilihan2">
                <img src="Manusia.jpg" alt="Manusia">
                <input type="radio" name="pilihan" value="2" id="pilihan2"> 2 (Manusia)
            </label>
        </div>
        <div>
            <label for="pilihan3">
                <img src="Semut.jpg" alt="Semut">
                <input type="radio" name="pilihan" value="3" id="pilihan3"> 3 (Semut)
            </label>
        </div>
        <br>
        <input type="submit" value="Kirim">
        <input type="submit" name="reset" value="Mulai Ulang" class="reset-button">
    </form>
</div>

<?php
session_start();

initializeSession();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['pilihan'])) {
    processGame();
} elseif (isset($_POST['reset'])) {
    resetGame();
}

function initializeSession() {
    if (!isset($_SESSION['round'])) {
        $_SESSION['round'] = 1;
    }
    if (!isset($_SESSION['gameCount'])) {
        $_SESSION['gameCount'] = 0;
    }
    if (!isset($_SESSION['wins'])) {
        $_SESSION['wins'] = 0;
    }
    if (!isset($_SESSION['losses'])) {
        $_SESSION['losses'] = 0;
    }
    if (!isset($_SESSION['historyStack'])) {
        $_SESSION['historyStack'] = [];
    }
    if (!isset($_SESSION['resultQueue'])) {
        $_SESSION['resultQueue'] = [];
    }
    if (!isset($_SESSION['currentRoundResults'])) {
        $_SESSION['currentRoundResults'] = [];
    }
}

function processGame() {
    $pilihanPengguna = intval($_POST['pilihan']);
    $pilihan = ['Gajah', 'Manusia', 'Semut'];
    
    $pilihanKomputer = rand(1, 3);
    
    array_push($_SESSION['historyStack'], ['user' => $pilihanPengguna, 'computer' => $pilihanKomputer]);

    if ($pilihanPengguna == $pilihanKomputer) {
        $hasil = "Seri!";
    } elseif (
        ($pilihanPengguna == 1 && $pilihanKomputer == 2) ||  
        ($pilihanPengguna == 2 && $pilihanKomputer == 3) ||  
        ($pilihanPengguna == 3 && $pilihanKomputer == 1)     
    ) {
        $hasil = "Anda menang! " . $pilihan[$pilihanPengguna-1] . " mengalahkan " . $pilihan[$pilihanKomputer-1] . ".";
        $_SESSION['wins']++;
        $_SESSION['currentRoundResults'][] = 'Menang';
    } else {
        $hasil = "Anda kalah! " . $pilihan[$pilihanKomputer-1] . " mengalahkan " . $pilihan[$pilihanPengguna-1] . ".";
        $_SESSION['losses']++;
        $_SESSION['currentRoundResults'][] = 'Kalah';
    }

    array_push($_SESSION['resultQueue'], $hasil);
    
    if (count($_SESSION['resultQueue']) > 5) {
        array_shift($_SESSION['resultQueue']);
    }
    
    $_SESSION['gameCount']++;

    echo "<h2>Pilihan Anda: " . $pilihan[$pilihanPengguna-1] . "</h2>";
    echo "<h2>Pilihan Komputer: " . $pilihan[$pilihanKomputer-1] . "</h2>";
    echo "<h2>Hasil: $hasil</h2>";

    echo "<h2>Riwayat Permainan (Stack)</h2>";
    foreach (array_reverse($_SESSION['historyStack']) as $index => $choice) {
        echo "<p>Permainan " . ($index + 1) . ": Anda memilih " . $pilihan[$choice['user'] - 1] . ", Komputer memilih " . $pilihan[$choice['computer'] - 1] . "</p>";
    }

    echo "<h2>Hasil Terakhir (Queue)</h2>";
    foreach ($_SESSION['resultQueue'] as $index => $result) {
        echo "<p>Hasil " . ($index + 1) . ": $result</p>";
    }

    if ($_SESSION['gameCount'] >= 3) {
        $wins = count(array_filter($_SESSION['currentRoundResults'], fn($result) => $result === 'Menang'));
        $losses = 3 - $wins;

        if ($wins > $losses) {
            echo "<p>Selamat Anda menang ronde ini!</p>";
        } else {
            echo "<p>Anda kalah ronde ini. Coba lagi!</p>";
        }

        resetRound();
    }
}

function resetRound() {
    $_SESSION['round']++;
    $_SESSION['gameCount'] = 0;
    $_SESSION['currentRoundResults'] = [];
}

function resetGame() {
    $_SESSION['round'] = 1;
    $_SESSION['gameCount'] = 0;
    $_SESSION['wins'] = 0;
    $_SESSION['losses'] = 0;
    $_SESSION['historyStack'] = [];
    $_SESSION['resultQueue'] = [];
    $_SESSION['currentRoundResults'] = [];
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>

</body>
</html>
