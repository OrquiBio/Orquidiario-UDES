#include <ESP8266WiFi.h>
#include <DHT.h>

// Configuración de red WiFi
const char* ssid = "NOMBRE_DE_TU_RED";  // Cambia por el nombre de tu red WiFi
const char* password = "CONTRASEÑA_DE_TU_RED";  // Cambia por la contraseña de tu WiFi

// Cambiar por la IP local de tu computadora
const char* server = "192.168.X.X";  // Reemplazar con la IP del computador
const int port = 80;  // Puerto de tu servidor (por defecto 80 para HTTP)

// Configuración del sensor DHT
#define DHTPIN 2  // Pin al que conectas el sensor DHT
#define DHTTYPE DHT11  // O DHT22 si usas ese sensor

DHT dht(DHTPIN, DHTTYPE);
WiFiClient client;

void setup() {
  Serial.begin(115200);
  dht.begin();

  // Conectar a la red WiFi
  Serial.println();
  Serial.println("Conectando a la red WiFi...");
  WiFi.begin(ssid, password);

  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println();
  Serial.println("Conectado a la red WiFi.");
  Serial.print("Dirección IP del ESP8266: ");
  Serial.println(WiFi.localIP());
}

void loop() {
  // Leer los valores del sensor DHT
  float temperature = dht.readTemperature();
  float humidity = dht.readHumidity();

  if (isnan(temperature) || isnan(humidity)) {
    Serial.println("Error al leer del sensor DHT");
    return;
  }

  // Conectar al servidor
  if (client.connect(server, port)) {
    // Crear el JSON con los datos de temperatura y humedad
    String postData = "{\"humedad\": " + String(humidity) + ", \"temperatura\": " + String(temperature) + "}";

    // Enviar la solicitud HTTP POST
    client.println("POST /receive_data.php HTTP/1.1");
    client.println("Host: " + String(server));
    client.println("Content-Type: application/json");
    client.println("Content-Length: " + String(postData.length()));
    client.println();
    client.println(postData);

    // Leer la respuesta del servidor
    while (client.available()) {
      String response = client.readString();
      Serial.println("Respuesta del servidor: " + response);
    }

    client.stop();  // Cerrar la conexión
  } else {
    Serial.println("Error al conectar con el servidor");
  }

  delay(1800000);  // Esperar 30 minutos antes de enviar otra lectura
}
