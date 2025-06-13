// script.js

// === Pengecekan dan Permintaan Izin Notifikasi ===
if ('Notification' in window && Notification.permission !== 'granted') {
    Notification.requestPermission().then(function(permission) {
      console.log('Notification permission:', permission);
    });
  }
  
  // Fungsi untuk menyimpan aktivitas menggunakan AJAX
  function saveActivity(type, details) {
    fetch('save_activity.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      body: new URLSearchParams({
        type: type,
        details: details
      })
    })
    .then(response => response.text())
    .then(data => {
      console.log("Activity saved: ", data);
    })
    .catch(error => {
      console.error("Error saving activity: ", error);
    });
  }
  
  // === Kalkulator Diet ===
  document.getElementById('dietForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Mengambil nilai input
    const age = parseFloat(document.getElementById('age').value);
    const gender = document.querySelector('input[name="gender"]:checked').value;
    const weight = parseFloat(document.getElementById('weight').value);
    const height = parseFloat(document.getElementById('height').value);
    const activityFactor = parseFloat(document.getElementById('activity').value);
    
    // Menghitung BMR menggunakan Rumus Mifflin-St Jeor
    let bmr;
    if (gender === 'male') {
      bmr = (10 * weight) + (6.25 * height) - (5 * age) + 5;
    } else {
      bmr = (10 * weight) + (6.25 * height) - (5 * age) - 161;
    }
    
    // Menghitung Total Daily Energy Expenditure (TDEE)
    const tdee = bmr * activityFactor;
    
    // Menghitung BMI
    const bmi = weight / ((height/100) ** 2);
    let bmiCategory;
    if (bmi < 18.5) {
      bmiCategory = 'Kurus (Underweight)';
    } else if (bmi < 25) {
      bmiCategory = 'Normal (Healthy Weight)';
    } else if (bmi < 30) {
      bmiCategory = 'Gemuk (Overweight)';
    } else {
      bmiCategory = 'Obesitas (Obese)';
    }
    
    // Menghitung Berat Badan Ideal (Formula Broca)
    let idealWeight;
    if (gender === 'male') {
      idealWeight = (height - 100) * 0.9;
    } else {
      idealWeight = (height - 100) * 0.85;
    }
    
    // Membuat rekomendasi
    const recommendations = [];
    
    // Rekomendasi berdasarkan aktivitas
    const activityLevel = document.getElementById('activity').selectedOptions[0].text;
    recommendations.push(`🔹 Aktivitas ${activityLevel.split(' ')[0]}: ${activityLevel.split('(')[1].replace(')','')}`);
    
    if (activityFactor === 1.2) {
      recommendations.push("Tambahkan jalan kaki 30 menit/hari untuk meningkatkan aktivitas");
    } else if (activityFactor >= 1.725) {
      recommendations.push("Pastikan waktu pemulihan dan tidur yang cukup");
    }
    
    // Rekomendasi berdasarkan BMI
    if (bmi < 18.5) {
      recommendations.push("Konsultasi dengan ahli gizi untuk menaikkan berat badan secara sehat");
      recommendations.push("Fokus pada makanan padat nutrisi dan latihan kekuatan");
    } else if (bmi >= 25) {
      recommendations.push("Kurangi makanan olahan dan tinggi gula");
      recommendations.push("Tingkatkan intensitas olahraga secara bertahap");
    } else {
      recommendations.push("Pertahankan pola makan seimbang dan rutin berolahraga");
    }
    
    // Menampilkan hasil
    const resultHTML = `
      <div class="alert alert-info">
        <h5>Hasil Perhitungan:</h5>
        <ul class="mb-0">
          <li>BMR: <strong>${bmr.toFixed(2)}</strong> kalori/hari</li>
          <li>TDEE: <strong>${tdee.toFixed(2)}</strong> kalori/hari</li>
          <li>BMI: <strong>${bmi.toFixed(1)}</strong> (${bmiCategory})</li>
          <li>Berat Badan Ideal: <strong>${idealWeight.toFixed(1)} kg</strong></li>
        </ul>
      </div>
      <div class="alert alert-success mt-3">
        <h5>Rekomendasi Pola Hidup Sehat:</h5>
        <ul class="mb-0">
          ${recommendations.map(r => `<li>${r}</li>`).join('')}
        </ul>
      </div>
    `;
    document.getElementById('dietResult').innerHTML = resultHTML;
    
    // Simpan aktivitas kalkulator diet
    const details = `Usia: ${age}, Gender: ${gender}, Berat: ${weight}kg, Tinggi: ${height}cm, BMR: ${bmr.toFixed(2)}, TDEE: ${tdee.toFixed(2)}, BMI: ${bmi.toFixed(1)} (${bmiCategory}), Ideal: ${idealWeight.toFixed(1)}kg`;
    saveActivity('Diet Calculator', details);
  });
  
  // === Atur Pengingat ===
  let alarmTime = null;
  let reminderType = null;
  
  // Fungsi untuk mengupdate jam setiap detik
  function updateClock() {
    const clockElement = document.getElementById('clock');
    const now = new Date();
    const formattedTime = now.toLocaleTimeString();
    clockElement.textContent = formattedTime;
    
    // Mengecek apakah waktunya pengingat
    if (alarmTime) {
      const currentTime = now.toTimeString().slice(0,5);
      if (currentTime === alarmTime) {
        triggerAlarm();
      }
    }
  }
  
  // Update jam setiap 1 detik
  setInterval(updateClock, 1000);
  
  // Mengatur pengingat dan menampilkan notifikasi saat pengingat diset
  document.getElementById('alarmForm').addEventListener('submit', function(e) {
    e.preventDefault();
    alarmTime = document.getElementById('alarmTime').value;
    reminderType = document.getElementById('reminderType').value;
    document.getElementById('alarmStatus').innerHTML = `<div class="alert alert-success">Pengingat <strong>${reminderType}</strong> diset pada: <strong>${alarmTime}</strong></div>`;
    
    // Menampilkan notifikasi bahwa pengingat telah diset
    if ('Notification' in window && Notification.permission === 'granted') {
      new Notification('Pengingat Diset!', { body: `Pengingat ${reminderType} diset pada ${alarmTime}` });
    }
    
    // Simpan aktivitas pengaturan pengingat
    const details = `Pengingat ${reminderType} diset pada ${alarmTime}`;
    saveActivity('Set Reminder', details);
  });
  
  // Menghapus pengingat
  document.getElementById('clearAlarm').addEventListener('click', function() {
    alarmTime = null;
    reminderType = null;
    document.getElementById('alarmStatus').innerHTML = `<div class="alert alert-warning">Pengingat dibatalkan.</div>`;
  });
  

// Deklarasi audio
const alarmSound = new Audio('alarm.mp3');
const notificationSound = new Audio('notification.mp3');

let isAlarmActive = false;
let checkAlarmInterval = null;
let currentNotification = null;
let currentReminderType = null;
let currentAlarmTime = null;

// Fungsi untuk memperbarui jam digital
function updateClock() {
  const clock = document.getElementById('clock');
  if (clock) {
    const now = new Date();
    clock.textContent = now.toLocaleTimeString();
  }
}
setInterval(updateClock, 1000);
updateClock();

// Fungsi untuk memulai alarm
function startAlarm(reminderType, alarmTime) {
  currentReminderType = reminderType;
  currentAlarmTime = alarmTime; // Misalnya "05:36"

  if (checkAlarmInterval) {
    clearInterval(checkAlarmInterval);
  }

  checkAlarmInterval = setInterval(() => {
    const now = new Date();
    const currentTime = now.getHours().toString().padStart(2, '0') + ':' + 
                       now.getMinutes().toString().padStart(2, '0');
    
    console.log(`Membandingkan: ${currentTime} dengan ${currentAlarmTime}`);
    
    // Bandingkan waktu saat ini dengan waktu alarm
    if (currentTime === currentAlarmTime && !isAlarmActive) {
      triggerAlarm(currentReminderType, currentAlarmTime);
    }
  }, 1000);
}

// Fungsi untuk memicu alarm
function triggerAlarm(reminderType, alarmTime) {
  if (isAlarmActive) {
    console.log("Alarm sudah aktif, tidak dipanggil ulang");
    return;
  }

  isAlarmActive = true;
  console.log("triggerAlarm() dipanggil");

  alarmSound.volume = 1.0;
  notificationSound.volume = 1.0;
  alarmSound.loop = true;

  playAudioWithInteraction(alarmSound, "alarmSound");
  playAudioWithInteraction(notificationSound, "notificationSound");

  if ('Notification' in window && Notification.permission === 'granted') {
    currentNotification = new Notification('Pengingat!', {
      body: `Waktunya ${reminderType || 'melakukan aktivitas'}! (Klik untuk menghentikan alarm)`,
      requireInteraction: true
    });

    currentNotification.onclick = function(event) {
      console.log("Notifikasi diklik");
      stopAlarm(currentNotification);
    };
  } else if ('Notification' in window && Notification.permission !== 'denied') {
    Notification.requestPermission().then(permission => {
      if (permission === 'granted') {
        currentNotification = new Notification('Pengingat!', {
          body: `Waktunya ${reminderType || 'melakukan aktivitas'}! (Klik untuk menghentikan alarm)`,
          requireInteraction: true
        });
        currentNotification.onclick = function(event) {
          console.log("Notifikasi diklik");
          stopAlarm(currentNotification);
        };
      } else {
        alert(`Waktunya ${reminderType || 'melakukan aktivitas'}!`);
      }
    });
  } else {
    alert(`Waktunya ${reminderType || 'melakukan aktivitas'}!`);
  }

  document.getElementById('alarmStatus').innerHTML = `<div class="alert alert-danger">Pengingat berbunyi!</div>`;
}

// Fungsi untuk memutar audio dengan interaksi pengguna
function playAudioWithInteraction(audio, audioName) {
  audio.play()
    .then(() => {
      console.log(`${audioName} sedang diputar`);
    })
    .catch(error => {
      console.error(`Gagal memainkan ${audioName}:`, error);
      const userInteraction = confirm("Audio tidak dapat diputar otomatis. Klik OK untuk mengaktifkan suara.");
      if (userInteraction) {
        audio.play()
          .then(() => {
            console.log(`${audioName} diputar setelah interaksi pengguna`);
          })
          .catch(err => {
            console.error(`Masih gagal memainkan ${audioName}:`, err);
          });
      }
    });
}

// Fungsi untuk menghentikan alarm
function stopAlarm(notification) {
  console.log("stopAlarm() dipanggil");

  alarmSound.pause();
  alarmSound.currentTime = 0;
  alarmSound.loop = false;
  notificationSound.pause();
  notificationSound.currentTime = 0;

  if (notification) {
    notification.close();
    currentNotification = null;
  }

  if (checkAlarmInterval) {
    clearInterval(checkAlarmInterval);
    checkAlarmInterval = null;
    console.log("Interval dihentikan");
  }

  isAlarmActive = false;
  currentReminderType = null;
  currentAlarmTime = null;
  document.getElementById('alarmStatus').innerHTML = `<div class="alert alert-success">Pengingat dihentikan!</div>`;
  console.log("Alarm dihentikan");
}

// Event listener untuk form
document.getElementById('alarmForm').addEventListener('submit', function(e) {
  e.preventDefault();
  const reminderType = document.getElementById('reminderType').value;
  const alarmTime = document.getElementById('alarmTime').value; // Format HH:mm (misalnya "05:36")
  
  if (!reminderType || !alarmTime) {
    alert("Harap pilih jenis pengingat dan waktu!");
    return;
  }

  startAlarm(reminderType, alarmTime);
  document.getElementById('alarmStatus').innerHTML = `Pengingat ${reminderType} diset untuk ${alarmTime}`;
});

// Event listener untuk tombol "Clear Pengingat"
document.getElementById('clearAlarm').addEventListener('click', function() {
  console.log("Tombol Clear Pengingat diklik");
  stopAlarm(currentNotification);
});