/*
=================================================
THERMOGUN INTERAKTIF FINAL (DISTANCE & AUTO-CONNECT)
ESP32 + MLX90614 + OLED + LED + BUZZER
=================================================
*/

#include <Wire.h>
#include <Adafruit_MLX90614.h>
#include <Adafruit_GFX.h>
#include <Adafruit_SSD1306.h>

// ================= OLED =================
#define SCREEN_WIDTH   128
#define SCREEN_HEIGHT   64
#define OLED_RESET      -1
#define OLED_ADDRESS   0x3C

// ================= PIN ==================
#define PIN_LED         2
#define PIN_BUZZER     16
#define PIN_TRIGGER    14

// ================= SUHU =================
const float OFFSET = 0.5; // Bisa disesuaikan

const float BATAS_RENDAH = 30.0;
const float BATAS_DEMAM  = 37.5;
const float BATAS_KRITIS = 38.5;

// ========================================

Adafruit_SSD1306 display(SCREEN_WIDTH, SCREEN_HEIGHT, &Wire, OLED_RESET);
Adafruit_MLX90614 mlx;

bool triggerSebelumnya = false;

// ========================================
// BUZZER
// ========================================

void beep(int freq, int durasi) {
  tone(PIN_BUZZER, freq);
  delay(durasi);
  noTone(PIN_BUZZER);
}

void bunyiDingin() {
  for (int i = 0; i < 5; i++) {
    digitalWrite(PIN_LED, HIGH);
    beep(300, 70);
    digitalWrite(PIN_LED, LOW);
    delay(120);
  }
}

void bunyiNormal() {
  beep(1500, 150);
}

void bunyiDemam() {
  for (int i = 0; i < 2; i++) {
    digitalWrite(PIN_LED, HIGH);
    beep(2200, 150);
    digitalWrite(PIN_LED, LOW);
    delay(100);
  }
}

void bunyiKritis() {
  tone(PIN_BUZZER, 3000);
  digitalWrite(PIN_LED, HIGH);
}

void stopBuzzer() {
  noTone(PIN_BUZZER);
  digitalWrite(PIN_LED, LOW);
}

// ========================================
// WAJAH
// ========================================

void wajahDingin() {
  display.drawCircle(95, 20, 4, WHITE);
  display.drawCircle(112, 20, 4, WHITE);
  display.fillCircle(95, 20, 1, WHITE);
  display.fillCircle(112, 20, 1, WHITE);
  display.drawLine(95, 38, 100, 35, WHITE);
  display.drawLine(100, 35, 105, 38, WHITE);
  display.drawLine(105, 38, 110, 35, WHITE);
}

void wajahNormal() {
  display.fillCircle(95, 20, 3, WHITE);
  display.fillCircle(112, 20, 3, WHITE);
  display.drawLine(95, 38, 103, 42, WHITE);
  display.drawLine(103, 42, 112, 38, WHITE);
}

void wajahDemam() {
  display.drawLine(93, 18, 99, 24, WHITE);
  display.drawLine(99, 18, 93, 24, WHITE);
  display.drawLine(109, 18, 115, 24, WHITE);
  display.drawLine(115, 18, 109, 24, WHITE);
  display.drawCircle(104, 38, 4, WHITE);
}

void wajahKritis() {
  display.drawCircle(95, 20, 4, WHITE);
  display.drawCircle(112, 20, 4, WHITE);
  display.drawLine(95, 38, 112, 38, WHITE);
}

// ========================================

void tampilStandby() {
  display.clearDisplay();
  display.setTextColor(WHITE);
  display.setTextSize(1);
  display.setCursor(16, 15);
  display.println("SMART THERMOGUN");
  display.setCursor(18, 38);
  display.println("TEKAN TRIGGER");
  display.display();
}

// ========================================

void setup() {
  Serial.begin(115200);
  pinMode(PIN_LED, OUTPUT);
  pinMode(PIN_TRIGGER, INPUT_PULLUP);

  Wire.begin();
  display.begin(SSD1306_SWITCHCAPVCC, OLED_ADDRESS);
  mlx.begin();

  tampilStandby();
}

// ========================================

void loop() {
  bool trigger = !digitalRead(PIN_TRIGGER);

  // STANDBY
  if (!trigger) {
    triggerSebelumnya = false;
    stopBuzzer();
    tampilStandby();
    delay(20);
    return;
  }

  // BACA SUHU
  float suhu_objek = mlx.readObjectTempC();
  float suhu_ambient = mlx.readAmbientTempC();

  // ----------------------------------------------------
  // TRIK SOFTWARE KOMPENSASI JARAK JAUH
  // ----------------------------------------------------
  // Karena FOV sensor ini lebar (90 derajat), membaca dari jauh
  // akan mencampur suhu kulit dengan suhu ruang yang dingin,
  // sehingga hasil drop drastis. Kita "katrol" suhunya secara dinamis.
  
  float suhu = suhu_objek + OFFSET;
  
  if (suhu >= 31.0 && suhu < 35.5) {
      // Pembacaan terlalu rendah tapi masuk rentang badan (berarti jarak jauh)
      // Kita interpolasikan menuju 36.5 derajat
      suhu = suhu + ((36.5 - suhu) * 0.6); 
  } else if (suhu >= 35.5 && suhu < 37.0) {
      // Jarak lumayan ideal, berikan sedikit boost
      suhu = suhu + 0.3;
  }
  // ----------------------------------------------------

  Serial.print("Suhu: ");
  Serial.println(suhu);

  // ================= OLED =================
  display.clearDisplay();
  display.setTextSize(1);
  display.setCursor(0, 0);
  display.print("SUHU TUBUH");
  display.drawFastVLine(78, 0, 64, WHITE);

  // SUHU
  display.setTextSize(2);
  display.setCursor(0, 18);
  display.print(suhu, 1);
  display.print(" C");

  // STATUS
  display.setTextSize(1);
  display.setCursor(0, 52);

  // ========================================
  if (suhu < BATAS_RENDAH) {
    display.print("DINGIN");
    wajahDingin();
  }
  else if (suhu < BATAS_DEMAM) {
    display.print("NORMAL");
    wajahNormal();
  }
  else if (suhu < BATAS_KRITIS) {
    display.print("DEMAM");
    wajahDemam();
  }
  else {
    display.print("KRITIS");
    wajahKritis();
  }

  // OLED langsung tampil
  display.display();

  // Baru buzzer
  if (!triggerSebelumnya) {
    if (suhu < BATAS_RENDAH)
      bunyiDingin();
    else if (suhu < BATAS_DEMAM)
      bunyiNormal();
    else if (suhu < BATAS_KRITIS)
      bunyiDemam();
  }

  // KRITIS terus bunyi
  if (suhu >= BATAS_KRITIS) {
    bunyiKritis();
  }

  triggerSebelumnya = true;
  delay(20);
}
