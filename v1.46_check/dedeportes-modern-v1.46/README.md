# Dedeportes Modern Theme

Este repositorio contiene el código fuente del tema personalizado "Dedeportes Modern" para el sitio [dedeportes.cl](https://dedeportes.cl).

## Propósito del Proyecto
Este entorno local se utiliza para **desarrollar, iterar y generar versiones** del tema. No es el sitio en vivo. El flujo de trabajo consiste en editar el código aquí, generar una nueva versión (ZIP) y subirla al entorno de WordPress en producción.

## Estructura de Archivos Clave
- `style.css`: Hoja de estilos principal y declaración de la versión del tema.
- `functions.php`: Lógica del tema, registro de sidebars y carga de scripts.
- `page-*.php`: Plantillas de página personalizadas (ej. Liga de Ascenso, Copa Chile).
- `dedeportes-modern-v*.zip`: Artefactos generados listos para despliegue (se recomienda no versionar estos binarios en git, sino generarlos bajo demanda).

## Comandos Rápidos
Para generar un ZIP de la versión actual:
```bash
git archive --format=zip --output=dedeportes-modern-deploy.zip --prefix=dedeportes-modern/ HEAD
```
