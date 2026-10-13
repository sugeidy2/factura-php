# Sistema de Emisión de Factura de Venta

Aplicación web desarrollada en PHP y HTML para la emisión automatizada de facturas de venta de artículos. El sistema realiza el cálculo del subtotal bruto, aplica el Impuesto al Valor Agregado (IVA) y evalúa la aplicación de un descuento sobre el monto final.

## Reglas de Negocio y Cálculo

1. **Ingreso de datos (Formulario HTML):**
   - Descripción del artículo.
   - Cantidad adquirida.
   - Precio unitario de venta ($).

2. **Cálculos y Lógica del Sistema:**
   - **Precio Bruto (Subtotal Base):** `Cantidad × Precio Unitario`
   - **IVA (12%):** `Precio Bruto × 0.12`
   - **Total de Venta con IVA:** `Precio Bruto + IVA`
   - **Descuento Especial (15%):** Se aplica un **15% de descuento** sobre el monto total con IVA únicamente **si este supera los $150**.
   - **Monto Neto a Pagar:** `(Precio Bruto + IVA) - Descuento`

3. **Desglose de Factura:**
   - Datos del artículo (Descripción, Cantidad, Precio Unitario).
   - Subtotal Bruto.
   - Monto del IVA (12%).
   - Estatus del descuento (*Aplica (15%)* con el monto descontado, o *No Aplica*).
   - Monto total neto final a cancelar.

## Instrucciones de Ejecución

### Opción 1: Servidor Apache (XAMPP)
1. Colocar la carpeta del proyecto en la ruta de XAMPP (`htdocs`).
2. Iniciar el módulo **Apache** desde el panel de control de XAMPP.
3. Abrir en el navegador: `http://localhost/nombre_carpeta/factura.php`

### Opción 2: Servidor Integrado de PHP
1. Abrir la terminal en la carpeta del proyecto.
2. Iniciar el servidor local:
   ```bash
   php -S localhost:8000