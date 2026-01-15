<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" type="image/png" href="images/favicon.ico" />
    <link rel="stylesheet" href="style.css" />
    <title>FlapGo - Selamat Datang</title>
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

        <div class="container">
            <h1>FlapGo</h1>

            <form method="POST" action="index.php" id="nameForm">
                <div class="form-group">
                    <label for="playerName">Nama Pemain:</label>
                    <input type="text" id="playerName" name="name" placeholder="Masukkan nama" maxlength="50" required
                        autocomplete="off" />
                    <div class="error" id="errorMsg">Nama minimal 2 karakter!</div>
                </div>

                <button type="submit" class="btn-play" id="btnPlay">
                    Mulai Bermain
                </button>
            </form>
        </div>
    </div>

    <script>
        const form = document.getElementById('nameForm');
        const input = document.getElementById('playerName');
        const errorMsg = document.getElementById('errorMsg');
        const btnPlay = document.getElementById('btnPlay');

        input.addEventListener('input', function () {
            if (this.value.trim().length >= 2) {
                errorMsg.style.display = 'none';
                btnPlay.disabled = false;
            } else {
                btnPlay.disabled = true;
            }
        });

        form.addEventListener('submit', function (e) {
            const name = input.value.trim();

            if (name.length < 2) {
                e.preventDefault();
                errorMsg.style.display = 'block';
                input.focus();
                return false;
            }

            btnPlay.innerHTML = 'Loading...';
            btnPlay.disabled = true;
        });

        window.addEventListener('load', function () {
            input.focus();
        });
    </script>
</body>

</html>