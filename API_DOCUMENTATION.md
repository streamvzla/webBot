# 🤖 BotCodigo - Documentación Oficial de la API

La API de **BotCodigo** está dividida en dos módulos principales:
1. **API Pública de Licencias y Franquicias** (Para integración con la tienda online y validación del plugin/bot de escritorio).
2. **API de Consultas (v1)** (Para integración en páginas web, tiendas de reventa o bots externos que necesitan extraer códigos de verificación).

---

## 🔐 1. API Pública de Franquicias y Licencias

Estos endpoints no requieren token Bearer, pero los administrativos (como `create-auto`, `renew` y `suspend`) están protegidos mediante el secreto compartido (`X-BotCodigo-Secret`).

### 1.1 Crear Franquicia y Usuario Automáticamente
*Llamado por la tienda cuando un cliente realiza una nueva compra.*
- **Método:** `POST`
- **Ruta:** `/api/v1/license/create-auto`
- **Headers:** `X-BotCodigo-Secret: <TU_SECRET_KEY>`
- **Body JSON:**
  ```json
  {
    "client_name": "Jesus Garcia",
    "client_email": "jesus@gmail.com",
    "plan": "Franquicia Básica",
    "days": 31
  }
  ```
- **Respuesta (`200 OK`):**
  ```json
  {
    "success": true,
    "license_key": "TCD-XXXX-YYYY-ZZZZ",
    "license_id": 12,
    "status": "active",
    "user_email": "jesus@tu-codigo.com",
    "user_password": "Xy7@Nm3k",
    "user_name": "jesus",
    "panel_url": "https://tu-codigo.com/login",
    "expires_at": "2026-08-27",
    "days": 31,
    "message": "Franquicia activada por 31 días. Vence el 2026-08-27."
  }
  ```

### 1.2 Renovar Membresía
*Llamado por la tienda para extender la vigencia al registrar un nuevo pago.*
- **Método:** `POST`
- **Ruta:** `/api/v1/license/renew`
- **Headers:** `X-BotCodigo-Secret: <TU_SECRET_KEY>`
- **Body JSON:**
  ```json
  {
    "license_key": "TCD-XXXX-YYYY-ZZZZ",
    "days": 31
  }
  ```
- **Respuesta (`200 OK`):**
  ```json
  {
    "success": true,
    "license_key": "TCD-XXXX-YYYY-ZZZZ",
    "days_added": 31,
    "expires_at": "2026-09-27",
    "message": "Membresía renovada por 31 días. Nuevo vencimiento: 2026-09-27."
  }
  ```

### 1.3 Suspender Franquicia
*Llamado por la tienda al expirar el tiempo sin renovación o al cancelar una suscripción.*
- **Método:** `POST`
- **Ruta:** `/api/v1/license/suspend`
- **Headers:** `X-BotCodigo-Secret: <TU_SECRET_KEY>`
- **Body JSON:**
  ```json
  {
    "license_key": "TCD-XXXX-YYYY-ZZZZ"
  }
  ```

### 1.4 Validar Licencia (Tatuaje Digital / Desktop App)
*Verifica la licencia y vincula el dominio de manera vitalicia, devolviendo un JWT firmado.*
- **Método:** `POST`
- **Ruta:** `/api/v1/license/validate`
- **Body JSON:**
  ```json
  {
    "license_key": "TCD-XXXX-YYYY-ZZZZ",
    "domain": "mitienda.com"
  }
  ```

### 1.5 Heartbeat de Licencia
*Consulta periódica desde las instancias del cliente para verificar que sigan activas y no hayan sido suspendidas.*
- **Método:** `POST`
- **Ruta:** `/api/v1/license/heartbeat`
- **Body JSON:**
  ```json
  {
    "license_key": "TCD-XXXX-YYYY-ZZZZ",
    "domain": "mitienda.com"
  }
  ```

---

## ⚡ 2. API de Consultas de Códigos (v1 - Reventa / Clientes)

Esta API permite a los dueños de franquicias (o sus clientes) consultar los códigos de verificación extraídos por el sistema IMAP en tiempo real.
Todos los endpoints requieren autenticación mediante un Token API (Sanctum).
- **Headers requeridos en todas las peticiones:**
  - `Accept: application/json`
  - `Authorization: Bearer <TU_API_TOKEN_DE_SANCTUM>`

> [!IMPORTANT]
> **Control de Vencimiento Automatizado:**
> Si la franquicia propietaria del API Token tiene su membresía vencida (`subscription_ends_at` en el pasado), cualquier petición a este grupo retornará un error `403 Forbidden` con el código `SUBSCRIPTION_EXPIRED`.

### 2.1 Consultar Código en Tiempo Real (IMAP On-Demand)
Realiza una búsqueda directa y en vivo en los buzones de correo asociados a la franquicia para extraer el código más reciente.
- **Método:** `POST`
- **Ruta:** `/api/v1/query`
- **Body JSON:**
  ```json
  {
    "email": "cliente@streamvzla.com",
    "platform_id": 1
  }
  ```
- **Respuesta Exitoso (`200 OK`):**
  ```json
  {
    "success": true,
    "message": "Código encontrado exitosamente.",
    "data": {
      "email": "cliente@streamvzla.com",
      "platform": "Netflix",
      "code": "849201",
      "query_id": 405,
      "time_ms": 342
    }
  }
  ```
- **Respuesta No Encontrado (`404 Not Found`):**
  ```json
  {
    "success": false,
    "message": "No se encontró ningún código reciente para este correo y plataforma.",
    "query_id": 406
  }
  ```

### 2.2 Obtener Perfil y Estadísticas del Franquiciado
- **Método:** `GET`
- **Ruta:** `/api/v1/profile`
- **Respuesta (`200 OK`):**
  ```json
  {
    "success": true,
    "data": {
      "franchise_name": "Jesus Garcia",
      "email": "jesus@tu-codigo.com",
      "role": "admin",
      "stats": {
        "total_clients": 15,
        "total_queries_today": 84
      }
    }
  }
  ```

### 2.3 Listar Plataformas Disponibles
Devuelve el catálogo de plataformas de streaming con soporte activo para esta franquicia.
- **Método:** `GET`
- **Ruta:** `/api/v1/platforms`

### 2.4 Listar Buzones Maestros Activos
Devuelve la lista de cuentas de correo IMAP conectadas y operativas en esta franquicia.
- **Método:** `GET`
- **Ruta:** `/api/v1/emails`

### 2.5 Listar Correos Asignados/Publicados Requetecientes
- **Método:** `GET`
- **Ruta:** `/api/v1/emails/recent?limit=50`

---

## 🛠️ Códigos de Error Comunes

| Código HTTP | Error Code | Descripción |
| :---: | :--- | :--- |
| `401` | `UNAUTHORIZED` | Token Bearer faltante o inválido, o cuenta de franquicia desactivada. |
| `403` | `SUBSCRIPTION_EXPIRED` | La membresía del franquiciado expiró y debe ser renovada para seguir usando la API. |
| `403` | `FORBIDDEN` | Intento de consultar una plataforma o correo que no pertenece a esta franquicia. |
| `404` | `NOT_FOUND` | No se encontró el código de verificación en el tiempo estipulado. |
