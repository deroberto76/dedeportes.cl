# Guía para Agentes (AGENT_GUIDE)

Esta guía define el protocolo de trabajo para cualquier agente de IA que colabore en este proyecto. **Sigue estas reglas estrictamente.**

## 1. Contexto y Objetivo
Estamos desarrollando el tema "Dedeportes Modern". El objetivo es siempre **mantener el orden**, **versionar correctamente** y **generar entregables funcionales**.

## 2. Reglas de Oro
1.  **Git es la Verdad**: Antes de empezar, asegura estar en `main` y con los últimos cambios (`git pull`). Al terminar, SIEMPRE haz `git push`.
2.  **Versionado Semántico**: Cada cambio funcional requiere subir la versión en `style.css` y `functions.php` (ej. 1.16.0 -> 1.17.0).
3.  **Sin Estilos en Línea**: No uses `style="..."`. Crea clases utilitarias en `style.css` si es necesario (ej. `.u-pt-2`).
4.  **Generación de ZIP**: El usuario necesita un archivo `.zip` para subir a WordPress. Genéralo siempre al finalizar una tarea importante.

## 3. Flujo de Trabajo Estándar (The Loop)
Para cada solicitud del usuario:
1.  **Leer y Planificar**: Entiende el requerimiento. Si implica código nuevo, revisa si afecta `functions.php` o `style.css`.
2.  **Implementar**: Modifica el código.
3.  **Verificar**: Revisa sintaxis y coherencia.
4.  **Versionar**: Incrementa `Version:` en `style.css` y `DEDEPORTES_VERSION` en `functions.php`.
5.  **Commit y Tag**:
    ```bash
    git add .
    git commit -m "Feat: Descripción del cambio"
    git tag v1.XX.X
    ```
6.  **Generar Artefacto**:
    ```bash
    git archive --format=zip --output=../dedeportes-modern-v1.XX.zip --prefix=dedeportes-modern/ v1.XX.X
    ```
    (Nota: El ZIP se guarda en el directorio padre `../` para no ensuciar el repo).
7.  **Sincronizar (Push)**:
    ```bash
    git push origin main --tags
    ```
8.  **Notificar**: Informa al usuario la ubicación del ZIP y el estado del repositorio.

## 4. Notas Técnicas
- **Sidebars**: Se registran en `functions.php`. Asegura IDs únicos.
- **Fuentes**: Usamos 'Outfit' (títulos) e 'Inter' (cuerpo).
- **Colores**: Las variables CSS están en `:root` en `style.css`. Úsalas.
