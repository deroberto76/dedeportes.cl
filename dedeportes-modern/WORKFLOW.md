# Workflow de Desarrollo y Liberación

Sigue estos pasos para liberar una nueva versión del tema.

## Paso 1: Preparación
```bash
git branch --show-current # Asegura que estás en 'main'
git pull origin main      # Trae los últimos cambios
```

## Paso 2: Desarrollo
- Realiza tus cambios en los archivos PHP/CSS.
- **IMPORTANTE**: Actualiza la versión en:
  - `style.css` (Línea `Version: X.XX.X`)
  - `functions.php` (Constante `DEDEPORTES_VERSION`)

## Paso 3: Commit y Tag
```bash
git add .
git commit -m "Release vX.XX.X: Descripción breve"
git tag vX.XX.X
```

## Paso 4: Generación del ZIP (Entregable)
Genera el ZIP usando `git archive` para asegurar que solo empaquetas archivos controlados por git (evitando basura local).
```bash
# Reemplaza vX.XX.X con la versión actual (ej. v1.17.0)
git archive --format=zip --output=dedeportes-modern-vX.XX.zip --prefix=dedeportes-modern/ vX.XX.X
```

## Paso 5: Sincronización
```bash
git push origin main --tags
```

---
**Nota**: Si el usuario pide un ZIP rápido sin taggear, usa `HEAD` en lugar de la versión:
```bash
git archive --format=zip --output=dedeportes-modern-wip.zip --prefix=dedeportes-modern/ HEAD
```
