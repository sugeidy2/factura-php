<?php
declare(strict_types=1);

const TARIFA_DIURNA = 675.0;
const TARIFA_VESPERTINA = 700.0;
const TARIFA_NOCTURNA = 956.23;

function escalaRetenciones(float $bruto): array
{
    if ($bruto < 85000) {
        return ['ahorro' => 0.001, 'seguro' => 0.0015];
    }
    if ($bruto <= 150000) {
        return ['ahorro' => 0.0015, 'seguro' => 0.002];
    }
    return ['ahorro' => 0.003, 'seguro' => 0.0025];
}

function formato(float $valor): string
{
    return number_format($valor, 2, ',', '.') . ' Bs.';
}

$errores = [];
$resultado = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim((string) ($_POST['nombre'] ?? ''));
    $cedula = trim((string) ($_POST['cedula'] ?? ''));
    $horasDiurna = (float) ($_POST['horas_diurna'] ?? 0);
    $horasVespertina = (float) ($_POST['horas_vespertina'] ?? 0);
    $horasNocturna = (float) ($_POST['horas_nocturna'] ?? 0);

    if ($nombre === '') {
        $errores[] = 'El nombre es obligatorio.';
    }
    if ($cedula === '') {
        $errores[] = 'La cédula es obligatoria.';
    }
    if ($horasDiurna < 0 || $horasVespertina < 0 || $horasNocturna < 0) {
        $errores[] = 'Las horas trabajadas no pueden ser negativas.';
    }

    if (!$errores) {
        $pagoDiurna = $horasDiurna * TARIFA_DIURNA;
        $pagoVespertina = $horasVespertina * TARIFA_VESPERTINA;
        $pagoNocturna = $horasNocturna * TARIFA_NOCTURNA;

        $bruto = $pagoDiurna + $pagoVespertina + $pagoNocturna;

        $escala = escalaRetenciones($bruto);
        $retencionAhorro = $bruto * $escala['ahorro'];
        $retencionSeguro = $bruto * $escala['seguro'];
        $neto = $bruto - $retencionAhorro - $retencionSeguro;

        $resultado = [
            'nombre' => $nombre,
            'cedula' => $cedula,
            'horasDiurna' => $horasDiurna,
            'horasVespertina' => $horasVespertina,
            'horasNocturna' => $horasNocturna,
            'pagoDiurna' => $pagoDiurna,
            'pagoVespertina' => $pagoVespertina,
            'pagoNocturna' => $pagoNocturna,
            'bruto' => $bruto,
            'porcAhorro' => $escala['ahorro'] * 100,
            'porcSeguro' => $escala['seguro'] * 100,
            'retencionAhorro' => $retencionAhorro,
            'retencionSeguro' => $retencionSeguro,
            'neto' => $neto,
        ];
    }
}

function e(?string $valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sueldo Quincenal</title>
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
            max-width: 780px;
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
        .fila > div { flex: 1; min-width: 180px; }
        button {
            margin-top: 20px;
            background: #2563eb;
            color: #fff;
            border: none;
            padding: 12px 22px;
            border-radius: 6px;
            font-size: 1rem;
            cursor: pointer;
        }
        button:hover { background: #1d4ed8; }
        .errores {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 16px;
        }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { text-align: left; padding: 8px 10px; border-bottom: 1px solid #e5e7eb; }
        th { background: #f8fafc; }
        td.num, th.num { text-align: right; font-variant-numeric: tabular-nums; }
        .total { font-weight: 700; background: #f1f5f9; }
        .neto { font-weight: 700; color: #065f46; background: #ecfdf5; }
        .dato { margin: 4px 0; }
    </style>
</head>
<body>
<div class="contenedor">
    <h1>Cálculo de Sueldo Quincenal</h1>

    <?php if ($errores): ?>
        <div class="errores">
            <?php foreach ($errores as $error): ?>
                <div><?= e($error) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="post" action="">
        <h2>Datos personales</h2>
        <div class="fila">
            <div>
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" value="<?= e($_POST['nombre'] ?? '') ?>" required>
            </div>
            <div>
                <label for="cedula">Cédula</label>
                <input type="text" id="cedula" name="cedula" value="<?= e($_POST['cedula'] ?? '') ?>" required>
            </div>
        </div>

        <h2>Horas trabajadas por turno</h2>
        <div class="fila">
            <div>
                <label for="horas_diurna">Diurna (675 Bs./h)</label>
                <input type="number" step="0.01" min="0" id="horas_diurna" name="horas_diurna"
                       value="<?= e($_POST['horas_diurna'] ?? '0') ?>" required>
            </div>
            <div>
                <label for="horas_vespertina">Vespertina (700 Bs./h)</label>
                <input type="number" step="0.01" min="0" id="horas_vespertina" name="horas_vespertina"
                       value="<?= e($_POST['horas_vespertina'] ?? '0') ?>" required>
            </div>
            <div>
                <label for="horas_nocturna">Nocturna (956,23 Bs./h)</label>
                <input type="number" step="0.01" min="0" id="horas_nocturna" name="horas_nocturna"
                       value="<?= e($_POST['horas_nocturna'] ?? '0') ?>" required>
            </div>
        </div>

        <button type="submit">Calcular sueldo quincenal</button>
    </form>

    <?php if ($resultado !== null): ?>
        <h2>Resumen del cálculo</h2>
        <p class="dato"><strong>Nombre:</strong> <?= e($resultado['nombre']) ?></p>
        <p class="dato"><strong>Cédula:</strong> <?= e($resultado['cedula']) ?></p>

        <table>
            <thead>
                <tr>
                    <th>Turno</th>
                    <th class="num">Horas</th>
                    <th class="num">Tarifa</th>
                    <th class="num">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Diurna</td>
                    <td class="num"><?= number_format($resultado['horasDiurna'], 2, ',', '.') ?></td>
                    <td class="num"><?= formato(TARIFA_DIURNA) ?></td>
                    <td class="num"><?= formato($resultado['pagoDiurna']) ?></td>
                </tr>
                <tr>
                    <td>Vespertina</td>
                    <td class="num"><?= number_format($resultado['horasVespertina'], 2, ',', '.') ?></td>
                    <td class="num"><?= formato(TARIFA_VESPERTINA) ?></td>
                    <td class="num"><?= formato($resultado['pagoVespertina']) ?></td>
                </tr>
                <tr>
                    <td>Nocturna</td>
                    <td class="num"><?= number_format($resultado['horasNocturna'], 2, ',', '.') ?></td>
                    <td class="num"><?= formato(TARIFA_NOCTURNA) ?></td>
                    <td class="num"><?= formato($resultado['pagoNocturna']) ?></td>
                </tr>
            </tbody>
        </table>

        <table>
            <thead>
                <tr>
                    <th>Concepto</th>
                    <th class="num">Porcentaje</th>
                    <th class="num">Monto</th>
                </tr>
            </thead>
            <tbody>
                <tr class="total">
                    <td>Sueldo bruto</td>
                    <td class="num">—</td>
                    <td class="num"><?= formato($resultado['bruto']) ?></td>
                </tr>
                <tr>
                    <td>Retención Ahorro Habitacional</td>
                    <td class="num"><?= number_format($resultado['porcAhorro'], 2, ',', '.') ?>%</td>
                    <td class="num">- <?= formato($resultado['retencionAhorro']) ?></td>
                </tr>
                <tr>
                    <td>Retención Seguro Social</td>
                    <td class="num"><?= number_format($resultado['porcSeguro'], 2, ',', '.') ?>%</td>
                    <td class="num">- <?= formato($resultado['retencionSeguro']) ?></td>
                </tr>
                <tr class="neto">
                    <td>Sueldo quincenal neto</td>
                    <td class="num">—</td>
                    <td class="num"><?= formato($resultado['neto']) ?></td>
                </tr>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</body>
</html>
