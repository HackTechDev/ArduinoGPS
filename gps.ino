/*
GPRMC & GPGGA decoder
Link: https://rl.se/gprmc
*/

#include <SoftwareSerial.h>
SoftwareSerial SoftSerial(2, 3);
unsigned char buffer[64];                   // buffer array for data receive over serial port
int count=0;                                // counter for buffer array

// Log file base name.  Must be six characters or less.
#define FILE_BASE_NAME "gps"

#include <SPI.h>
#include "SdFat.h"

// SD chip select pin.  Be sure to disable any other SPI devices such as Enet.
const uint8_t chipSelect = SS;

SdFat sd;
SdFile myFile;

// Error messages stored in flash.
#define error(msg) sd.errorHalt(F(msg))

void setup() {
  SoftSerial.begin(9600);                 // the SoftSerial baud rate
  Serial.begin(9600);                     // the Serial port of Arduino baud rate.

  const uint8_t BASE_NAME_SIZE = sizeof(FILE_BASE_NAME) - 1;
  char fileName[13] = FILE_BASE_NAME "00.csv";
  
  while (!Serial) {
    ; // wait for serial port to connect. Needed for native USB port only
  }

  Serial.print("Initializing SD card...");

  // Initialize at the highest speed supported by the board that is
  // not over 50 MHz. Try a lower speed if SPI errors occur.
  if (!sd.begin(chipSelect, SD_SCK_MHZ(50))) {
    sd.initErrorHalt();
  }

   
  Serial.println("initialization done.");

  // Find an unused file name.
  if (BASE_NAME_SIZE > 6) {
    error("FILE_BASE_NAME too long");
  }
  while (sd.exists(fileName)) {
    if (fileName[BASE_NAME_SIZE + 1] != '9') {
      fileName[BASE_NAME_SIZE + 1]++;
    } else if (fileName[BASE_NAME_SIZE] != '9') {
      fileName[BASE_NAME_SIZE + 1] = '0';
      fileName[BASE_NAME_SIZE]++;
    } else {
      error("Can't create file name");
    }
  }
  
  if (!myFile.open(fileName, O_WRONLY | O_CREAT | O_EXCL)) {
    error("file.open");
  }


}

void loop() {
    if (SoftSerial.available()) {                     // if date is coming from software serial port ==> data is coming from SoftSerial shield
        while(SoftSerial.available()) {              // reading data into char array        
            buffer[count++]=SoftSerial.read();      // writing data into array
            if(count == 64)
              break;
        }
        Serial.write(buffer,count);                 // if no data transmission ends, write buffer to hardware serial port

        myFile.write(buffer,count);    

        // Force data to SD and update the directory entry to avoid data loss.
        if (!myFile.sync() || myFile.getWriteError()) {
          error("write error");
        }
 
        clearBufferArray();                         // call clearBufferArray function to clear the stored data from the array
        count = 0;                                  // set counter of while loop to zero 
    }
    
    if (Serial.available()) {                // if data is available on hardware serial port ==> data is coming from PC or notebook
      SoftSerial.write(Serial.read());        // write it to the SoftSerial shield
        // Close file and stop.
    myFile.close();
    Serial.println(F("Done"));
    SysCall::halt();
    }
}


void clearBufferArray() {                    // function to clear buffer array
    for (int i=0; i<count;i++) {
        buffer[i]=NULL;
    }                      // clear all index of array with command NULL
}
