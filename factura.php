<?php
declare(strict_types=1);

const IVA = 0.12;
const UMBRAL_DESCUENTO = 150.0;
const TASA_DESCUENTO = 0.15;

function formato(float $valor): string
{
    return '$' . number_format($valor, 2, ',', '.');
}

function e(?string $valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

$errores = [];
$factura = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $descripcion = trim((string) ($_POST['descripcion'] ?? ''));
    $cantidad = (float) ($_POST['cantidad'] ?? 0);
    $precioUnitario = (float) ($_POST['precio_unitario'] ?? 0);

    if ($descripcion === '') {
        $errores[] = 'La descripción del artículo es obligatoria.';
    }
    if ($cantidad <= 0) {
        $errores[] = 'La cantidad debe ser mayor que cero.';
    }
    if ($precioUnitario <= 0) {
        $errores[] = 'El precio unitario debe ser mayor que cero.';
    }

    if (!$errores) {
        $subtotal = $cantidad * $precioUnitario;
        $montoIva = $subtotal * IVA;
        $totalConIva = $subtotal + $montoIva;

        $aplicaDescuento = $totalConIva > UMBRAL_DESCUENTO;
        $montoDescuento = $aplicaDescuento ? $totalConIva * TASA_DESCUENTO : 0.0;
        $totalNeto = $totalConIva - $montoDescuento;

        $factura = [
            'descripcion' => $descripcion,
            'cantidad' => $cantidad,
            'precioUnitario' => $precioUnitario,
            'subtotal' => $subtotal,
            'montoIva' => $montoIva,
            'totalConIva' => $totalConIva,
            'aplicaDescuento' => $aplicaDescuento,
            'montoDescuento' => $montoDescuento,
            'totalNeto' => $totalNeto,
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura de Compra</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: system-ui, -apple-system, "Segoe UI", Arial, sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 24px;
            color: #1f2937;
        }
        .contenedor {
            max-width: 720px;
            margin: 0 auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .08);
            padding: 28px;
        }
        h1 { font-size: 1.4rem; margin-top: 0; }
        h2 { font-size: 1.1rem; border-bottom: 2px solid #e5e7eb; padding-bottom: 6px; }
        label { display: block; font-weight: 600; margin: 12px 0 4px; font-size: .9rem; }
        input {
            width: 100%;
            padding: 9px 10px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 1rem;
        }
        .fila { display: flex; gap: 16px; flex-wrap: wrap; }
        .fila > div { flex: 1; min-width: 160px; }
        button {
            margin-top: 20px;
            background: #0f766e;
            color: #fff;
            border: none;
            padding: 12px 22px;
            border-radius: 6px;
            font-size: 1rem;
            cursor: pointer;
        }
        button:hover { background: #115e59; }
        .errores {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 16px;
        }
        .factura {
            margin-top: 20px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            overflow: hidden;
        }
        .factura-cab {
            background: #0f766e;
            color: #fff;
            padding: 12px 16px;
            font-weight: 700;
        }
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; padding: 9px 16px; border-bottom: 1px solid #e5e7eb; }
        td.num, th.num { text-align: right; font-variant-numeric: tabular-nums; }
        .subtotal { font-weight: 600; }
        .descuento { color: #b91c1c; }
        .aviso {
            padding: 9px 16px;
            font-size: .88rem;
            background: #f8fafc;
            color: #475569;
            border-bottom: 1px solid #e5e7eb;
        }
        .total { font-weight: 700; background: #ecfdf5; color: #065f46; font-size: 1.1rem; }
    </style>
</head>
<body>
<div class="contenedor">
    <h1>Emisión de Factura</h1>

    <?php if ($errores): ?>
        <div class="errores">
            <?php foreach ($errores as $error): ?>
                <div><?= e($error) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="post" action="">
        <h2>Datos de la compra</h2>
        <label for="descripcion">Descripción del artículo</label>
        <input type="text" id="descripcion" name="descripcion"
               value="<?= e($_POST['descripcion'] ?? '') ?>" required>

        <div class="fila">
            <div>
                <label for="cantidad">Cantidad</label>
                <input type="number" step="1" min="1" id="cantidad" name="cantidad"
                       value="<?= e($_POST['cantidad'] ?? '') ?>" required>
            </div>
            <div>
                <label for="precio_unitario">Precio unitario ($)</label>
                <input type="number" step="0.01" min="0.01" id="precio_unitario" name="precio_unitario"
                       value="<?= e($_POST['precio_unitario'] ?? '') ?>" required>
            </div>
        </div>

        <button type="submit">Emitir factura</button>
    </form>

    <?php if ($factura !== null): ?>
        <div class="factura">
            <div class="factura-cab">Factura &mdash; <?= e($factura['descripcion']) ?></div>
            <table>
                <tbody>
                    <tr>
                        <td>Cantidad</td>
                        <td class="num"><?= number_format($factura['cantidad'], 2, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <td>Precio unitario</td>
                        <td class="num"><?= formato($factura['precioUnitario']) ?></td>
                    </tr>
                    <tr class="subtotal">
                        <td>Subtotal (precio bruto)</td>
                        <td class="num"><?= formato($factura['subtotal']) ?></td>
                    </tr>
                    <tr>
                        <td>IVA (12%)</td>
                        <td class="num"><?= formato($factura['montoIva']) ?></td>
                    </tr>
                    <tr>
                        <td>Total con IVA</td>
                        <td class="num"><?= formato($factura['totalConIva']) ?></td>
                    </tr>
                    <?php if ($factura['aplicaDescuento']): ?>
                        <tr class="descuento">
                            <td>Descuento (15% por superar $150,00)</td>
                            <td class="num">- <?= formato($factura['montoDescuento']) ?></td>
                        </tr>
                    <?php endif; ?>
                    <tr class="total">
                        <td>Total neto a pagar</td>
                        <td class="num"><?= formato($factura['totalNeto']) ?></td>
                    </tr>
                </tbody>
            </table>
            <div class="aviso">
                <?php if ($factura['aplicaDescuento']): ?>
                    Se aplicó un descuento del 15% porque el total con IVA supera los $150,00.
                <?php else: ?>
                    No aplica descuento: el total con IVA (<?= formato($factura['totalConIva']) ?>) no supera los $150,00.
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
