import time
from machine import Pin, I2C        #importing relevant modules & classes
from time import sleep
import utime
import socket
import network
import bme280
from ssd1306 import SSD1306_I2C
from lcd_api import LcdApi
from pico_i2c_lcd import I2cLcd, utime
import ubinascii
import urequests
import json
from do_request import *
#importing BME280 library

I2C_ADDR = 0x27
I2C_NUM_ROWS = 2
I2C_NUM_COLS = 16
 
i2c=I2C(0,sda=Pin(0), scl=Pin(1), freq=400000)    #initializing the I2C method
lcd = I2cLcd(i2c, I2C_ADDR, I2C_NUM_ROWS, I2C_NUM_COLS)
url = 'http://151.80.61.248/'

wlan = network.WLAN(network.STA_IF)
wlan.active(True)
wlan.connect("","")
 
def web_page():
    bme = bme280.BME280(i2c=i2c)          #BME280 object created
    html = """<html><head><meta http-equiv="refresh" content="5"><meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" href="data:,"><style>body { text-align: center; font-family: "Helvetica", Arial;}
  table { border-collapse: collapse; width:55%; margin-left:auto; margin-right:auto; }
  th { padding: 12px; background-color: #038771; color: white; }
  tr { border: 2px solid #000556; padding: 12px; }
  tr:hover { background-color: #bcbcbc; }
  td { border: none; padding: 14px; }
  .sensor { color:DarkBlue; font-weight: bold; background-color: #ffffff; padding: 1px;  
  </style></head><body><h1>GROUPE 1 - CUBE 2</h1> <br><h2>STATION METEO</h2></br>
  <table><tr><th>Parametres</th><th>Valeurs</th></tr>
  <tr><td>Temperature</td><td><span class="sensor">""" + str(bme.values[0]) + """</span></td></tr>
  <tr><td>Pression</td><td><span class="sensor">""" + str(bme.values[1]) + """</span></td></tr>
  <tr><td>Humidite</td><td><span class="sensor">""" + str(bme.values[2]) + """</span></td></tr> 
  
  <head><meta http-equiv="refresh" content="5"><meta name="viewport" content="width=device-width, initial-scale=1"><style>img{display: block; margin-left: auto; margin-right: auto;}</style></head><body><img src="https://uxwing.com/wp-content/themes/uxwing/download/weather/weather-icon.svg" alt="pico" style="max-width:15%; padding-bottom: 50px">
  </body></html>"""
    return html


  
# Wait for connect or fail
wait = 10
while wait > 0:
    if wlan.status() < 0 or wlan.status() >= 3:
        break
    wait -= 1
    print('waiting for connection...')
    time.sleep(1)
 
# Handle connection error
if wlan.status() != 3:
    raise RuntimeError('wifi connection failed')
else:
    print('connected')
    ip=wlan.ifconfig()[0]
    mac = ubinascii.hexlify(network.WLAN().config('mac'),':').decode()
    print('IP: ', ip)
    print('MAC: ', mac)
    
# Open socket
addr = socket.getaddrinfo('172.20.10.10', 80)[0][-1]
server = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
server.bind(('', 80))
server.listen(5)
print('listening on', addr)
bme = bme280.BME280(i2c=i2c)
temperature = bme.values[0]         #reading the value of temperature
humidity = bme.values[2]            #reading the value of humidity
pressure = bme.values[1]


while True:
    lcd.backlight_on()
    lcd.move_to(1,0)
    lcd.putstr('temp = ' + temperature)
    lcd.move_to(1,1)
    lcd.putstr('hum = ' + humidity)
    sleep(3)
    lcd.clear()
    lcd.move_to(1,0)
    lcd.putstr('pre =' + pressure)
    sleep(2)
    lcd.clear()
    json = do_request(url)
    print(json)
    time.sleep(3)

while True:
    try:
        conn, addr = server.accept()
        conn.settimeout(3.0)
        print('client connected from', addr)
        request = conn.recv(1024)
        conn.settimeout(None)
        # HTTP-Request receive           
        # HTTP-Response send
        response = web_page()
        conn.send('HTTP/1.0 200 OK\r\nContent-type: text/html\r\n\r\n')
        conn.sendall(response)
        conn.close()
    except OSError as e:
        conn.close()
        print('connection closed')