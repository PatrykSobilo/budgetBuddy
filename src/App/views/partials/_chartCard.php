<?php
/**
 * Chart Card Component
 * Reusable card for displaying charts
 * 
 * @param string $title Chart title
 * @param string $canvasId Canvas element ID for Chart.js
 * @param string $maxHeight Maximum height (default: 300px)
 * @param string $colClass Bootstrap column class (default: col-md-6)
 */

$title = $title ?? 'Chart';
$canvasId = $canvasId ?? 'chart';
$maxHeight = $maxHeight ?? '300px';
$colClass = $colClass ?? 'col-md-6';
?>

<div class="<?php echo htmlspecialchars($colClass); ?>">
    <div class="card">
        <div class="card-body">
            <div class="fw-bold text-center mb-3" style="font-size: 1.25rem;">
                <?php echo htmlspecialchars($title); ?>
            </div>
            <canvas id="<?php echo htmlspecialchars($canvasId); ?>" style="max-height: <?php echo htmlspecialchars($maxHeight); ?>;"></canvas>
        </div>
    </div>
</div>
