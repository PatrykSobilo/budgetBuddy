<div class="d-flex justify-content-center mb-4 mt-5">
    <form method="GET" action="" class="d-flex" style="max-width: 400px;">
      <input type="text" name="s" class="form-control me-2" placeholder="Search..." aria-label="Search" value="<?php echo htmlspecialchars($_GET['s'] ?? ''); ?>">
      <button class="btn btn-primary" type="submit">Search</button>
    </form>
</div>