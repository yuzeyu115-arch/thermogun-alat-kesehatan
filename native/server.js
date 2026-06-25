const express = require('express');
const app = express();
const http = require('http').createServer(app);
const io = require('socket.io')(http);
const { SerialPort } = require('serialport');
const { ReadlineParser } = require('@serialport/parser-readline');

// ==========================================
// PENTING: SESUAIKAN PORT COM ARDUINO KAMU
// ==========================================
// Cek di Arduino IDE atau Device Manager laptopmu, Arduino terdeteksi di COM berapa.
// Contoh: 'COM3' (Windows) atau '/dev/ttyUSB0' (Linux/Mac)
const ARDUINO_PORT = 'COM3';

// Inisialisasi koneksi ke Serial Port Arduino
const port = new SerialPort({
    path: ARDUINO_PORT,
    baudRate: 9600 // Pastikan baudRate ini sama dengan Serial.begin(9600) di Arduino IDE
});

// Gunakan parser agar data dibaca baris per baris (\n) bukan pecahan buffer/byte
const parser = port.pipe(new ReadlineParser({ delimiter: '\r\n' }));

// Sajikan folder 'public' agar file index.html bisa diakses oleh browser
app.use(express.static('public'));

// Logika ketika ada koneksi dari browser (frontend)
io.on('connection', (socket) => {
    console.log('🔗 Browser terhubung ke server WebSocket');

    socket.on('disconnect', () => {
        console.log('❌ Browser memutuskan koneksi');
    });
});

// Membaca data yang dikirim oleh Arduino secara berkala
parser.on('data', (data) => {
    // Bersihkan spasi kosong dan konversi string dari Arduino menjadi angka desimal (float)
    const suhu = parseFloat(data.trim());

    // Validasi apakah data yang masuk benar-benar angka yang valid
    if (!isNaN(suhu)) {
        console.log(`[Arduino Data] Suhu masuk: ${suhu}°C`);

        // Broadcast / Kirim data suhu ini ke semua halaman web yang sedang terbuka
        io.emit('dataSuhu', suhu);
    } else {
        console.log(`[Peringatan] Data diterima bukan angka valid: ${data}`);
    }
});

// Menangani error jika port serial gagal dibuka atau terputus tengah jalan
port.on('error', (err) => {
    console.error(`🚨 Error pada Serial Port (${ARDUINO_PORT}): `, err.message);
    console.log('👉 Tips: Pastikan Arduino sudah dicolok dan tidak sedang membuka Serial Monitor di Arduino IDE.');
});

// Menjalankan server lokal pada port 3000
const PORT_SERVER = 3000;
http.listen(PORT_SERVER, () => {
    console.log('==================================================');
    console.log(`🚀 Server monitoring aktif!`);
    console.log(`🌐 Silakan buka browser dan akses: http://localhost:${PORT_SERVER}`);
    console.log(`🔌 Membaca data Arduino via Port: ${ARDUINO_PORT}`);
    console.log('==================================================');
});