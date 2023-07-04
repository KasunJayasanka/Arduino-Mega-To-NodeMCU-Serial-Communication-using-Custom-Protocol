#include <ArduinoJson.h>
#include <Arduino.h>
#include <ESP8266WiFi.h>
#include <ESP8266WiFiMulti.h>
#include <ESP8266HTTPClient.h>
#include <WiFiClient.h>
#include "setting.h"

/****************** GLOBAL VERIABLES ****************************/
//StaticJsonDocument<3072> doc;
DynamicJsonDocument doc(512);
DeserializationError error;
ESP8266WiFiMulti WiFiMulti;
HTTPClient http;
WiFiClient client;

// preper jason payload
String getSerialPayload() {

  String serialPayLoad="";
  unsigned long time = millis();

  if(Serial.available()){ 
    while (1) {                            
      if(Serial.available()) {         
        char temp = char(Serial.read());    
        if (temp=='*') { // detect the start serial charactor
           serialPayLoad = "";
        } else if(temp == '$') {  // detect the end of serial character
          break;
        } else {
          serialPayLoad += temp;
        }
      }
      

      // we will only wait limited time in serial loop
      // Otherwise if we not get the start or end characters we will stuck in the loop forever
      if(time + SERIAL_WAITING_TIME < millis()) {
        Serial.println(F("Serial Read timeout"));
        serialPayLoad = "";
        break;
      }
    }

  }
  return serialPayLoad;
}

String makeHttpPayload(String serialPayload) {

    String jsonString;

    if(serialPayload!=""){
      StaticJsonDocument<512> payLoadJSONDoc;
      
      // Add data to the JSON object
      payLoadJSONDoc["X-API-KEY"] = API_KEY;
      payLoadJSONDoc["serialPayload"] = serialPayload;

      //Convert the JSON object to a string
      
      serializeJson(payLoadJSONDoc, jsonString);
      
      return jsonString;
    }
    else
    {
      jsonString="";
      return jsonString;
    }
    
    
}

void setup()
{
  Serial.begin(9600);
  WifiInit();
}

void loop()                                                                                
{
  // get data
  String serialPayLoad = getSerialPayload();
  String httpPayLoad=makeHttpPayload(serialPayLoad);

  if(httpPayLoad != "") {
    // Print Arduino data to serial monitor for DEBUG
    Serial.println(httpPayLoad);
    //we have payload send data to server
    //post data to server and get response
    error = deserializeJson(doc, SendDataToServer(httpPayLoad));
    if (error)
    {
      // print error to serial monitor
      Serial.print(F("deserializeJson() failed: "));
      Serial.println(error.c_str());
      return;
    }

  // // Get the root object in the document
  // JsonObject root = doc.as<JsonObject>();
  // String success = root["success"];
  // String massage = root["massage"];
  //String massage = root["msg"];
  } else {
    // we do not have payload
  }
  // sleep for 5s
  delay(5000);

}

/****************** HTTP  REQUEST AND WIFI INITIALIZATION ****************************/


void WifiInit() {

  Serial.println();
  Serial.println();
  Serial.println();

  for (uint8_t t = 4; t > 0; t--) {
    // Serial.printf("[SETUP] WAIT %d...\n", t);
    // Serial.flush();
    delay(1000);
  }

  WiFi.mode(WIFI_STA);
  connectToWifi();
}

String SendDataToServer(String data) {
  if(WiFi.status() == WL_CONNECTED){                            // check wifi cinnection status

    http.begin(client, API);                                    // Specify request destination
    
    http.addHeader("content-type", "application/json");          // Specify content-type header
    //https.addHeader("X-API-KEY", API_KEY);          // Add api key here
    int httpCode = http.POST(data);                             // send the request
    

    if (httpCode > 0) {
        // HTTP header has been send and Server response header has been handled
        Serial.print(F("[HTTP] POST... code: ")); Serial.println(httpCode);
        // We have response from server
        if (httpCode == HTTP_CODE_OK || httpCode == HTTP_CODE_MOVED_PERMANENTLY) {
          String payload = http.getString();
          return payload;
        }
      } else {
        Serial.print(F("[HTTP] POST... failed, error: ")); Serial.println(http.errorToString(httpCode).c_str());
        return ERROR_MSG;
      }
    http.end(); // Close connectiong
    return ERROR_MSG;
  }
  else{
    connectToWifi();
    return ERROR_MSG_WIFI;
  }
}

void connectToWifi(void){

    while((WiFiMulti.run() != WL_CONNECTED)){
        WiFiMulti.addAP(SSID,PASSWORD);
        Serial.println(F("Waiting for connection"));
        delay(5000);
    }
    Serial.println(F("connected"));
}


