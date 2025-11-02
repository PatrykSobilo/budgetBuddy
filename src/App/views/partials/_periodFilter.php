<?php
/**
 * Period Filter Component
 * 
 * @param string $action Form action URL
 * @param string $method Form method (GET or POST)
 * @param string $currentPeriod Currently selected period
 * @param array $customDates Custom start/end dates (optional)
 * @param string $submitLabel Submit button label (default: "Apply Filter")
 */

$action = $action ?? '';
$method = $method ?? 'GET';
$currentPeriod = $currentPeriod ?? ($_GET['period'] ?? $_POST['period'] ?? 'all');
$customDates = $customDates ?? [];
$submitLabel = $submitLabel ?? 'Apply Filter';
$startDateName = $startDateName ?? 'start_date';
$endDateName = $endDateName ?? 'end_date';
$startDateValue = $customDates['start'] ?? $_GET[$startDateName] ?? $_POST[$startDateName] ?? '';
$endDateValue = $customDates['end'] ?? $_GET[$endDateName] ?? $_POST[$endDateName] ?? '';
$csrfToken = $csrfToken ?? null;
$onChangeHandler = $onChangeHandler ?? null;
?>

<div class="row mb-4">
    <div class="col-md-12">
        <form method="<?php echo strtoupper($method); ?>" action="<?php echo htmlspecialchars($action); ?>" class="row g-3">
            <?php if ($method === 'POST' && $csrfToken): ?>
                <?php include $this->resolve("partials/_csrf.php", ['csrfToken' => $csrfToken]); ?>
            <?php endif; ?>
            
            <div class="col-md-4">
                <label for="period" class="form-label">Period</label>
                <select class="form-select" id="period" name="period" <?php if ($onChangeHandler): ?>onchange="<?php echo htmlspecialchars($onChangeHandler); ?>()"<?php endif; ?>>
                    <option value="all" <?php echo $currentPeriod === 'all' ? 'selected' : ''; ?>>All Time</option>
                    <option value="current_month" <?php echo $currentPeriod === 'current_month' ? 'selected' : ''; ?>>Current Month</option>
                    <option value="last_month" <?php echo $currentPeriod === 'last_month' ? 'selected' : ''; ?>>Last Month</option>
                    <option value="last_30_days" <?php echo $currentPeriod === 'last_30_days' ? 'selected' : ''; ?>>Last 30 Days</option>
                    <option value="last_90_days" <?php echo $currentPeriod === 'last_90_days' ? 'selected' : ''; ?>>Last 90 Days</option>
                    <option value="current_year" <?php echo $currentPeriod === 'current_year' ? 'selected' : ''; ?>>Current Year</option>
                    <option value="custom" <?php echo $currentPeriod === 'custom' ? 'selected' : ''; ?>>Custom Range</option>
                </select>
            </div>
            
            <div class="col-md-3" id="startDateDiv" style="display: <?php echo $currentPeriod === 'custom' ? 'block' : 'none'; ?>;">
                <label for="<?php echo htmlspecialchars($startDateName); ?>" class="form-label">Start Date</label>
                <input type="date" class="form-control" id="<?php echo htmlspecialchars($startDateName); ?>" name="<?php echo htmlspecialchars($startDateName); ?>" value="<?php echo htmlspecialchars($startDateValue); ?>">
            </div>
            
            <div class="col-md-3" id="endDateDiv" style="display: <?php echo $currentPeriod === 'custom' ? 'block' : 'none'; ?>;">
                <label for="<?php echo htmlspecialchars($endDateName); ?>" class="form-label">End Date</label>
                <input type="date" class="form-control" id="<?php echo htmlspecialchars($endDateName); ?>" name="<?php echo htmlspecialchars($endDateName); ?>" value="<?php echo htmlspecialchars($endDateValue); ?>">
            </div>
            
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100"><?php echo htmlspecialchars($submitLabel); ?></button>
            </div>
        </form>
    </div>
</div>
