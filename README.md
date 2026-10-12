# Calculadora de Sueldo Quincenal

Aplicación web en PHP que calcula el sueldo quincenal bruto y neto de un trabajador a partir de sus horas laboradas en turnos diurno, vespertino y nocturno, aplicando las retenciones de ley correspondientes.

## Funcionamiento
1. **Ingreso de datos:** Se capturan el nombre, cédula y horas trabajadas por jornada.
2. **Cálculo de sueldo bruto:** Multiplica las horas por la tarifa de cada turno:
   - Diurno: 675,00 Bs.
   - Vespertino: 700,00 Bs.
   - Nocturno: 956,23 Bs.
3. **Aplicación de deducciones:** Calcula el Ahorro Habitacional (AH) y Seguro Social (SSO) evaluando el sueldo bruto en tres escalas:
   - **Menor a 85.000 Bs.:** AH = 0.1% | SSO = 0.15%
   - **Entre 85.000 Bs. y 150.000 Bs.:** AH = 0.15% | SSO = 0.20%
   - **Mayor a 150.000 Bs.:** AH = 0.30% | SSO = 0.25%
4. **Resultado:** Muestra los datos del trabajador, el desglose de subtotales, el sueldo bruto, el monto descontado por cada concepto y el sueldo neto final.

## Instrucciones de Ejecución

### En XAMPP
1. Guardar la carpeta en `C:\xampp\htdocs\sugeidy\`.
2. Iniciar el servicio Apache en XAMPP.
3. Abrir en el navegador: `http://localhost/sugeidy/sueldo_quincenal.php`

### Con Servidor Integrado de PHP
1. Abrir la terminal en la carpeta del proyecto.
2. Ejecutar:
   ```bash
   php -S localhost:8000