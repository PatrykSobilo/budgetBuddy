<div class="row mb-4">
    <div class="col-md-12">
        <form method="GET" action="" class="row g-3">
            <div class="col-md-8">
                <label for="search" class="form-label">Search</label>
                <input type="text" id="search" name="s" class="form-control" placeholder="Type here to search..." value="<?php echo htmlspecialchars($_GET['s'] ?? ''); ?>">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button class="btn btn-primary w-100 mb-1" type="submit">Search</button>
            </div>
        </form>
    </div>
</div>