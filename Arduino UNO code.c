#include <ESP8266WiFi.h>
#include <DHT.h>

// Definir tu red WiFi
const char* ssid = "NOMBRE_DE_TU_RED";  // Cambia por el nombre de tu red WiFi
const char* password = "CONTRASEÑA_DE_TU_RED";  // Cambia por la contraseña de tu WiFi

//Esto de servidor y endpoint no lo modifiques. 
const char* server = "orquibio.online"; 
const int port = 80; 

// Configurar el sensor DHT
//ACA COLOCA EL PIN AL QUE CONECTASTE EL DHT . . . 
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
}

void loop() {
  // Leer los valores del sensor DHT
  float temperature = dht.readTemperature();
  float humidity = dht.readHumidity();

  // Verificar si hay error al leer el sensor
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

  delay(10000);  // Esperar 10 segundos antes de enviar otra lectura
}
