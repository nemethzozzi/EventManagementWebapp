<div align="center">

# Event Management System

<br/>

---

## **LARAVEL · VUE · TYPESCRIPT · BOOTSTRAP · POSTGRESQL**

<br/>

--- 

### **JWT AUTHENTICATION · WEBSOCKET (REAL-TIME UPDATES)**

</div>

--- 

## Requirements:
- **PHP** (Laravel compatible version)
- **Composer**
- **Node.js + npm**


<p align="center">
  <span style="color:red"><b>Before start the project copy both of the .env.examples in the backend and the frontend folders</b></span>
</p>

### Install dependency's:
```bash
# backend folder
composer install
php artisan key:generate
```
```bash
# frontend folder
npm i
```



### Start the backend:
```bash
# backend folder
php artisan serve
```
### Start Websocket:
```bash
# backend folder
php artisan reverb:start
```
### Create Dump Data:
```bash
# backend folder
php artisan db:seed
```
### Start the frontend:
```bash
# frontend folder
npm run dev
```