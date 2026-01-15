const kecepatan = 3,
  gravitasi = 0.5,
  lompat = -7.6,
  jarak = 35,
  jeda = 115;

const burung = document.querySelector(".bird");
const gambar = document.getElementById("bird-1");
const layar = document.querySelector(".background").getBoundingClientRect();
const skor = document.querySelector(".score_val");
const pesan = document.querySelector(".message");
const judulSkor = document.querySelector(".score_title");
const suaraPoint = new Audio("sounds effect/point.mp3");
const suaraMati = new Audio("sounds effect/kalah.mp3");
const suaraModeNaik = new Audio("sounds effect/point.mp3");

let status = "Start",
  turun = 0,
  hitung = 0;

let kecepatanSekarang = kecepatan;
let modeSekarang = "NORMAL";
let warnaModeSekarang = "#4caf50";
let sudahPeringatanMode = false;

gambar.style.display = "none";

document.addEventListener("keydown", (e) => {
  if (e.key === "Enter" && status !== "Play") mulai();
  if ((e.key === "ArrowUp" || e.key === " ") && status === "Play") {
    gambar.src = "images/Bird-2.png";
    turun = lompat;
  }
});

document.addEventListener("keyup", (e) => {
  if ((e.key === "ArrowUp" || e.key === " ") && status === "Play")
    gambar.src = "images/Bird.png";
});

function mulai() {
  document.querySelectorAll(".pipe_sprite").forEach((e) => e.remove());
  burung.style.top = "40vh";
  turun = hitung = 0;
  gambar.style.display = "block";

  kecepatanSekarang = kecepatan;
  modeSekarang = "NORMAL";
  warnaModeSekarang = "#4caf50";
  sudahPeringatanMode = false;

  document.querySelector(".score").style.display = "block";

  let modeIndicator = document.getElementById("modeIndicator");
  if (!modeIndicator) {
    modeIndicator = document.createElement("div");
    modeIndicator.id = "modeIndicator";
    modeIndicator.className = "mode-indicator";
    document.body.appendChild(modeIndicator);
  }
  modeIndicator.style.display = "block";
  updateModeIndicator();

  pesan.innerHTML = "";
  pesan.classList.remove("messageStyle");
  judulSkor.innerHTML = "Skor: ";
  skor.innerHTML = "0";
  status = "Play";
  requestAnimationFrame(loop);
}

function updateModeIndicator() {
  const modeIndicator = document.getElementById("modeIndicator");
  if (modeIndicator) {
    modeIndicator.innerHTML = `MODE: ${modeSekarang}`;
    modeIndicator.style.background = warnaModeSekarang;
  }
}

function tampilkanPeringatan(skor) {
  if (skor === 3 && !sudahPeringatanMode) {
    sudahPeringatanMode = true;
    tampilkanNotifikasiPeringatan("PIPA AKAN BERGERAK DALAM 2 POIN!");
  } else if (skor === 18 && modeSekarang === "PIPA BERGERAK") {
    sudahPeringatanMode = true;
    tampilkanNotifikasiPeringatan("MODE KACAU SEGERA AKTIF!");
  }
}

function tampilkanNotifikasiPeringatan(pesan) {
  let warning = document.getElementById("warningNotification");

  if (!warning) {
    warning = document.createElement("div");
    warning.id = "warningNotification";
    warning.className = "warning-notification";
    document.body.appendChild(warning);
  }

  warning.innerHTML = pesan;
  warning.style.display = "block";
  warning.style.animation = "warningPulse 0.5s ease-out";

  setTimeout(() => {
    warning.style.animation = "fadeOut 0.5s ease-out";
    setTimeout(() => {
      warning.style.display = "none";
    }, 500);
  }, 1500);
}

function cekDanGantiMode(skorSekarang) {
  let modeBaru = "";
  let warnaBaru = "";

  if (skorSekarang >= 15) {
    modeBaru = "KACAU";
    warnaBaru = "#e74c3c";
    kecepatanSekarang = kecepatan + 2.5;
  } else if (skorSekarang >= 5) {
    modeBaru = "PIPA BERGERAK";
    warnaBaru = "#ff9800";
    kecepatanSekarang = kecepatan + 0.8;
  } else {
    modeBaru = "NORMAL";
    warnaBaru = "#4caf50";
    kecepatanSekarang = kecepatan;
  }

  if (modeBaru !== modeSekarang) {
    modeSekarang = modeBaru;
    warnaModeSekarang = warnaBaru;
    updateModeIndicator();
    tampilkanNotifikasiMode();
    efekLayarBerkedip();
    suaraModeNaik.play();
    sudahPeringatanMode = false;
  }

  tampilkanPeringatan(skorSekarang);
}

function tampilkanNotifikasiMode() {
  let notifikasi = document.getElementById("modeNotification");

  if (!notifikasi) {
    notifikasi = document.createElement("div");
    notifikasi.id = "modeNotification";
    notifikasi.className = "mode-notification";
    document.body.appendChild(notifikasi);
  }

  let pesanNotifikasi = "";
  switch (modeSekarang) {
    case "PIPA BERGERAK":
      pesanNotifikasi = "PIPA MULAI BERGERAK!";
      break;
    case "KACAU":
      pesanNotifikasi = "MODE KACAU AKTIF!";
      break;
  }

  notifikasi.innerHTML = pesanNotifikasi;
  notifikasi.style.background = warnaModeSekarang;
  notifikasi.style.display = "block";
  notifikasi.style.animation = "slideInMode 0.5s ease-out";

  setTimeout(() => {
    notifikasi.style.animation = "slideOutMode 0.5s ease-out";
    setTimeout(() => {
      notifikasi.style.display = "none";
    }, 500);
  }, 2500);
}

function efekLayarBerkedip() {
  const layarBg = document.querySelector(".background");
  layarBg.style.animation = "flashScreen 0.3s ease-out";
  setTimeout(() => {
    layarBg.style.animation = "";
  }, 300);
}

function gerakkanPipa(pipa, skorSekarang) {
  if (skorSekarang >= 5) {
    if (!pipa.arahGerak) {
      pipa.arahGerak = Math.random() > 0.5 ? 1 : -1;
      pipa.posisiAwal = parseFloat(pipa.style.top);
      if (skorSekarang >= 15) {
        pipa.kecepatanVertikal = 0.18;
      } else {
        pipa.kecepatanVertikal = 0.12;
      }
      pipa.jarakMaksimal = 4;
    }

    const posSekarang = parseFloat(pipa.style.top);
    const selisihDariAwal = Math.abs(posSekarang - pipa.posisiAwal);
    if (selisihDariAwal >= pipa.jarakMaksimal) {
      pipa.arahGerak *= -1;
    }

    const gerakanBaru = posSekarang + pipa.arahGerak * pipa.kecepatanVertikal;
    pipa.style.top = gerakanBaru + "vh";
  }
}

function loop() {
  if (status !== "Play") return;

  turun += gravitasi;
  const posB = burung.getBoundingClientRect();

  if (posB.top <= 0 || posB.bottom >= layar.bottom) {
    selesai();
    return;
  }

  burung.style.top = posB.top + turun + "px";

  const skorSekarang = parseInt(skor.innerHTML);
  cekDanGantiMode(skorSekarang);

  document.querySelectorAll(".pipe_sprite").forEach((pipa) => {
    const posP = pipa.getBoundingClientRect();

    if (posP.right <= 0) {
      pipa.remove();
      return;
    }

    if (
      posB.left < posP.right &&
      posB.right > posP.left &&
      posB.top < posP.bottom &&
      posB.bottom > posP.top
    ) {
      selesai();
      return;
    }

    if (
      pipa.increase_score === "1" &&
      posP.right < posB.left &&
      posP.right + kecepatanSekarang >= posB.left
    ) {
      skor.innerHTML = skorSekarang + 1;
      suaraPoint.play();
    }

    pipa.style.left = posP.left - kecepatanSekarang + "px";
    gerakkanPipa(pipa, skorSekarang);
  });

  if (hitung++ > jeda) {
    hitung = 0;
    let jarakPipa = jarak;

    if (skorSekarang >= 20) {
      jarakPipa = jarak - 2;
    }

    const pos = Math.floor(Math.random() * 43) + 8;
    buatPipa(pos - 70, false);
    buatPipa(pos + jarakPipa, true);
  }

  requestAnimationFrame(loop);
}

function buatPipa(atas, kasihSkor) {
  const pipa = document.createElement("div");
  pipa.className = "pipe_sprite";
  pipa.style.top = atas + "vh";
  pipa.style.left = "100vw";
  if (kasihSkor) pipa.increase_score = "1";
  document.body.appendChild(pipa);
}

function selesai() {
  status = "End";
  gambar.style.display = "none";

  const modeIndicator = document.getElementById("modeIndicator");
  if (modeIndicator) {
    modeIndicator.style.display = "none";
  }

  const warning = document.getElementById("warningNotification");
  if (warning) {
    warning.style.display = "none";
  }

  suaraMati.pause();
  suaraMati.currentTime = 0;
  suaraMati.onended = null;

  suaraMati.play();

  document.getElementById("scoreInput").value = skor.innerHTML;

  suaraMati.onended = () => {
    document.getElementById("formSkor").submit();
  };
}
