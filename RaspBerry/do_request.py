import urequests
import machine
import time
from machine import Pin, I2C
from bme280 import *
import bme280
import json

i2c=I2C(0,sda=Pin(0), scl=Pin(1), freq=400000)
bme = BME280(i2c=i2c)
    # Set up the headers and URL
headers = {'Content-Type': 'application/json'}
url = 'http://151.80.61.248/'

    # Read the sensor data
temp, pressure, humidity = bme.read_compensated_data()

    # Set up the data to send
capteurs = {
    'temperature': temp / 100,
    'humidity': humidity / 1024,
    'pressure': pressure / 25600
}
def do_request(url):
    # Make the POST request
    reponse = urequests.post(url, json=capteurs, headers=headers)

    # Print the response
    print(reponse.content)

    # Close the response
    reponse.close()
    return reponse