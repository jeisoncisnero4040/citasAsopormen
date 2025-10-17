# 🏥 Backend del Sistema Clínico Asopormen

<p align="center">
  <img src="https://res.cloudinary.com/dxalvdckk/image/upload/v1747435854/descarga_ztjs3h.png" width="200" alt="Logo Asopormen">
</p>

1. [Descripción General](#-descripción-general)  
2. [Arquitectura del Proyecto](#-arquitectura-del-proyecto)  
3. [Tecnologías Utilizadas](#-tecnologías-utilizadas)  
4. [Requisitos Previos](#-requisitos-previos)  
5. [Instalación y Configuración](#-instalación-y-configuración)  
6. [Estructura de Carpetas](#-estructura-de-carpetas)  
7. [Flujos Principales](#-flujos-principales)  
8. [Variables de Entorno](#-variables-de-entorno)  
9. [Base de Datos](#-base-de-datos)  
10. [Servicios Externos](#-servicios-externos)  
11. [Rutas API](#-rutas-api)  
12. [Estándares de Código](#-estándares-de-código)  
13. [Estrategias de Seguridad](#-estrategias-de-seguridad)  
14. [Despliegue](#-despliegue)  
15. [Mantenimiento y Buenas Prácticas](#-mantenimiento-y-buenas-prácticas)  
16. [Autores y Créditos](#-autores-y-créditos)  

## 📜 Descripción General

El **Backend del Sistema Clínico Asopormen** es el núcleo que orquesta la gestión integral de pacientes, citas, autorizaciones, diagnósticos, evoluciones y reportes.  


Principales características:

- Gestión de citas médicas con trazabilidad completa.
- Evolución clínica de pacientes (EVO) totalmente integrada.
- Manejo de autorizaciones y control de diagnósticos.
- Reportes optimizados con procedimientos almacenados.
- Integración con **Redis** para cache y optimización de consultas.

---

## 🏛 Arquitectura del Proyecto

**Patrón principal:** MVC + Arquitectura Limpia.  
Separación clara de capas para garantizar bajo acoplamiento:

- **Service:** Contiene la lógica de negocio, orquesta las operaciones y llama a componentes auxiliares como mappers, validators y utils.
- **Persistence:** Encapsula la lógica de acceso a datos utilizando patrón *Adapter* para desacoplar la fuente de datos.
- **Controllers:** Gestionan la comunicación HTTP con el cliente, aplicando DTOs para estructurar la información de entrada/salida.

**Características adicionales:**
- **Cache:** Redis para almacenamiento temporal y reducción de tiempos de respuesta.
- **Jobs & Listeners:** Para procesamiento asíncrono y eventos del sistema.
- **Validadores dedicados:** Para garantizar la integridad de los datos.

---

## 🛠 Tecnologías Utilizadas

- **Lenguaje:** PHP 8.x
- **Framework:** Laravel 10.x
- **Base de Datos:** SQL Server
- **Cache:** Redis
- **Control de versiones:** Git

**Dependencias clave:**
- `laravel/framework`
- `predis/predis`
- `nesbot/carbon`

---

## 📦 Requisitos Previos

- PHP 8.1+
- Composer 2.x
- Redis
- SQL Server
- Extensiones PHP necesarias:
  - `pdo_sqlsrv`
  - `redis`

---

## ⚙ Instalación y Configuración

bash
# 1. Clonar repositorio
git clone https://github.com/usuario/backend-asopormen.git

# 2. Entrar en el proyecto
cd backend-asopormen

# 3. Instalar dependencias
composer install

# 4. Configurar variables de entorno
cp .env.example .env

# 5. Generar key de la aplicación
php artisan key:generate


## 📂 Estructura de Carpetas
app/
 ├── Constants/
 ├── Dtos/
 ├── Http/
 │    ├── Controllers/
 │    ├── Middleware/
 ├── Events/
 ├── Exceptions/
 ├── Services/
 ├── Mappers/
 ├── Interfaces/
 ├── Jobs/
 ├── Listeners/
 ├── Mail/
 ├── Repositories/
 ├── Utils/
 └── Models/

routes/
 ├── api.php
 ├── web.php
 

## 🔄 Flujos Principales

Gestión de Citas:
    permite consultar citas por profesional 
    permite marcar asistencia de cita, creando admision y reap electronico automatico

Evolución Médica (EVO)


Autorizaciones

## 🔐 Variables de Entorno
Variable	Descripción
DB_CONNECTION	Tipo de base de datos (sqlsrv, mysql)
REDIS_HOST	Host de Redis
APP_ENV	Entorno (local, production)
🗄 Base de Datos

Migraciones: Laravel Migrations

Modelos: Eloquent ORM

Procedimientos Almacenados: Uso para reportes complejos.

## 🌐 Rutas API

Ver documentación detallada en /api/documentaion o en la colección Postman.

## 🛡 Estrategias de Seguridad

Autenticación con JWT
Implementacion endpoint a endpoint con sistema roles y permisos ABAC

Validación de entradas con Form Requests

Escapado de datos para prevenir inyecciones



## 🚀 Despliegue

## 👑 Autores y Créditos

Area Sistemas Asopormen