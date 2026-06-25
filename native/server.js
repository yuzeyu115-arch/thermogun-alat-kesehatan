const express = require('express');
const app = express();
const http = require('http').createServer(app);
const io = require('socket.io')(http);
const { SerialPort } = require('serialport');
const { ReadlineParser } = require('@serialport/parser-readline');

app.use(express.static('public'));

io.on('connection', (socket) => {
    console.log('🔗 Browser terhubung ke server WebSocket');
    socket.on('disconnect', () => {
        console.log('❌ Browser memutuskan koneksi');
    });
});

let port;
let parser;

// Fungsi untuk mencari dan menyambung otomatis ke ESP32
async function autoConnect() {
    try {
        const ports = await SerialPort.list();
        // Cari port yang memiliki informasi USB (seperti ESP32/Arduino)
        // Mengecualikan port bawaan sistem yang biasanya tidak punya manufacturer
        const arduinoPort = ports.find(p => p.manufacturer || p.vendorId || p.pnpId);
        
        if (arduinoPort) {
            console.log(`\n🔌 Ditemukan perangkat USB di port: ${arduinoPort.path}`);
            connectToPort(arduinoPort.path);
        } else {
            console.log('⏳ Menunggu ESP32 disambungkan via kabel Type-C...');
            setTimeout(autoConnect, 2500); // Cek lagi tiap 2.5 detik
        }
    } catch (err) {
        console.error('Error mencari port:', err.message);
        setTimeout(autoConnect, 2500);
    }
}

function connectToPort(portPath) {
    port = new SerialPort({
        path: portPath,
        baudRate: 115200 // Sesuai dengan Serial.begin(115200) di kode ESP32
    });

    parser = port.pipe(new ReadlineParser({ delimiter: '\r\n' }));

    parser.on('data', (data) => {
        // Teks dari ESP32 bentuknya "Suhu: 36.50"
        if (data.includes("Suhu:")) {
            const suhuStr = data.replace("Suhu:", "").trim();
            const suhu = parseFloat(suhuStr);

            if (!isNaN(suhu)) {
                console.log(`[Sensor ESP32] Suhu: ${suhu}°C`);
                io.emit('dataSuhu', suhu);
            }
        } else {
            console.log(`[Log ESP32] ${data}`);
        }
    });

    port.on('open', () => {
        console.log(`✅ Berhasil terhubung ke ESP32 di port ${portPath}!`);
    });

    port.on('error', (err) => {
        console.error(`🚨 Error Serial Port: `, err.message);
        // Jika gagal buka port (misal sedang dipakai aplikasi lain), coba cari lagi
        if (err.message.includes('Access denied')) {
             console.log('👉 Tips: Pastikan Serial Monitor di Arduino IDE sudah ditutup.');
        }
    });

    port.on('close', () => {
        console.log(`\n❌ ESP32 Terputus! Kabel Type-C dicabut?`);
        // Mulai pencarian otomatis lagi
        setTimeout(autoConnect, 2000);
    });
}

// Menjalankan server lokal pada port 3000
const PORT_SERVER = 3000;
http.listen(PORT_SERVER, () => {
    console.log('==================================================');
    console.log(`🚀 Server monitoring aktif!`);
    console.log(`🌐 Buka browser dan akses: http://localhost:${PORT_SERVER}`);
    console.log('==================================================');
    
    // Mulai proses auto-connect saat server baru nyala
    autoConnect();
});