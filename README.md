# Proyecto E-Commerce - Farmacia

E-commerce modular para una farmacia con gestión de almacenes centralizados, múltiples puntos de venta (sedes) y plataforma web de compras para clientes.

## 📋 Descripción del Proyecto

El sistema está diseñado bajo una arquitectura monolítica escalable que permite:

- **Gestión Centralizada de Almacenes**: CRUD completo de inventario y productos con sincronización en tiempo real.
- **Múltiples Puntos de Venta (Sedes)**: Cada sede consume la API del almacén central para gestionar su inventario local.
- **Plataforma Web de Compras**: Los clientes pueden realizar compras seleccionando el punto de venta (sede) desde el cual desean comprar.
- **API RESTful**: Arquitectura backend que permite comunicación entre componentes de forma segura y escalable.

## 🏗️ Arquitectura de Componentes

```
Almacén Central (API)
    ↓
Múltiples Sedes (Points of Sale)
    ↓
Plataforma Web de Compras
    ↓
Clientes
```

## 🛠️ Stack Tecnológico

### Backend
- **PHP**: 8.5
- **Laravel Framework**: v13
- **Laravel Fortify**: v1 - Autenticación y seguridad
- **Laravel Boost**: v2 - Optimizaciones y herramientas
- **Laravel Wayfinder**: v0 - Generación de rutas tipadas
- **Laravel Pail**: v1 - Monitoreo de logs
- **Laravel Pint**: v1 - Formateador de código
- **Laravel Sail**: v1 - Ambiente de desarrollo con Docker
- **Laravel MCP**: v0 - Model Context Protocol

### Frontend
- **Vue.js**: v3 - Framework de UI reactiva
- **Inertia.js**: v3 - SPA server-side rendering
- **@inertiajs/vue3**: v3 - Integración Vue con Inertia
- **@laravel/vite-plugin-wayfinder**: v0 - Plugin Vite para rutas tipadas
- **Tailwind CSS**: v4 - Utilidades de estilos
- **TypeScript**: Tipado estático
- **Vite**: Bundler de módulos

### Testing & Quality
- **Pest PHP**: v4 - Framework de testing
- **PHPUnit**: v12 - Testing framework
- **ESLint**: v9 - Linter JavaScript/TypeScript
- **Prettier**: v3 - Formateador de código

### Base de Datos
- **MySQL/MariaDB** - A través de configuración Laravel (migraciones disponibles)

### Herramientas Adicionales
- **Composer**: Gestor de dependencias PHP
- **npm/pnpm**: Gestor de dependencias Node.js
