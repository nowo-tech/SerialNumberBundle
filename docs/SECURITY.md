# Revisión de seguridad – SerialNumberBundle

## Resumen

El bundle **no expone controladores ni rutas HTTP**. Solo ofrece un servicio (`SerialNumberGenerator`), una extensión Twig (`serial_number`, `serial_number_mask`) y configuración. La superficie de ataque es reducida.

Se han aplicado correcciones para evitar **abuso por consumo de recursos (DoS)** y se documentan buenas prácticas para **XSS** cuando los datos vienen de usuario.

---

## 1. Riesgos abordados (corregidos en código)

### 1.1 DoS por `str_repeat` en el enmascarado

- **Problema:** Si en Twig se pasaba `visibleLast` negativo (p. ej. `-1000000`), `maskLength = length - visible` podía ser enorme y `str_repeat($char, $maskLength)` consumía mucha memoria/CPU.
- **Corrección:** Se fuerza `visible = max(0, $visible)` en `SerialNumberTwigExtension::maskSerialNumber()`.

### 1.2 DoS por serial muy largo

- **Problema:** Un serial de millones de caracteres (p. ej. desde base de datos o variable de plantilla) hacía que `str_repeat` generara una cadena gigante.
- **Corrección:** Se limita la longitud del serial a **2048** caracteres (`MAX_SERIAL_LENGTH`). Por encima se trunca antes de enmascarar.

### 1.3 DoS por `mask_char` de varios caracteres

- **Problema:** Si en configuración o en Twig se usaba una cadena larga como carácter de máscara, `str_repeat($char, $maskLength)` multiplicaba la longitud de salida.
- **Corrección:** En `maskSerialNumber()` se usa solo el primer carácter (multibyte-safe con `mb_substr`). En `Configuration` se valida que `mask_char` sea un solo carácter.

### 1.4 DoS por `idPadding` muy grande

- **Problema:** `str_pad($idStr, $idPadding, '0', STR_PAD_LEFT)` con `$idPadding` enorme (p. ej. desde plantilla o configuración) podía consumir mucha memoria.
- **Corrección:** Se limita el padding a **32** caracteres (`MAX_ID_PADDING`) en `SerialNumberGenerator::generate()`.

---

## 2. XSS y datos de usuario

Las funciones/filtros Twig están declarados con **`is_safe => ['html']`**, es decir, Twig no escapa la salida. Eso es adecuado cuando los datos son **controlados por la aplicación** (por ejemplo, números de factura generados por el propio sistema).

- **Recomendación:** No pasar a `serial_number()` o `serial_number_mask()` datos que provengan directamente de entrada de usuario (formularios, query string, etc.) sin sanitizar o sin escapar la salida.
- Si en la aplicación el serial o el contexto pueden contener contenido de usuario, hay que:
  - Escapar en plantilla (p. ej. `{{ serial|serial_number_mask(4)|e }}` si no se usa `is_safe` para ese valor), o
  - Asegurar que el valor ya viene sanitizado antes de pasarlo al bundle.

El bundle no realiza escape HTML; delega en la aplicación el uso seguro en contexto HTML.

---

## 3. Otros aspectos revisados

| Aspecto | Estado |
|--------|--------|
| Inyección SQL | No aplica: el bundle no ejecuta SQL. |
| Control de acceso | No aplica: no hay rutas ni controladores. |
| Secretos en configuración | No se almacenan contraseñas ni tokens. `mask_char` y `mask_visible_last` son opciones de presentación. |
| Dependencias | Solo Symfony (config, DI, HttpKernel) y Twig. Revisar con `composer audit` en el proyecto que use el bundle. |
| Carga de configuración | La configuración se carga desde YAML del bundle y de la app; no hay carga desde input de usuario. |

---

## 4. Tests de seguridad

En los tests se han añadido casos que garantizan:

- `visibleLast` negativo se trata como 0 (full mask).
- Serial muy largo se trunca (límite 2048).
- `maskChar` de varios caracteres se reduce al primer carácter.
- `idPadding` excesivo se limita a 32.

Ejecutar la suite completa con `composer test` (o `./vendor/bin/phpunit`) para validar que las protecciones siguen activas tras cambios en el código.
