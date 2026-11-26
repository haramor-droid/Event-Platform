<form method="post" enctype="multipart/form-data">
  <?= csrf_field() ?>

  <div class="mb-3">
    <label for="title" class="form-label">Title</label>
    <input type="text" class="form-control" id="title" name="title" maxlength="200"
           required value="<?= htmlspecialchars($f['title']) ?>">
  </div>

  <div class="mb-3">
    <label for="category_id" class="form-label">Category</label>
    <select class="form-select" id="category_id" name="category_id" required>
      <option value="">Choose a category</option>
      <?php foreach ($categories as $c): $id = (int) $c['category_id']; ?>
        <option value="<?= $id ?>" <?= $id === (int) $f['category_id'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($c['name']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="mb-3">
    <label for="description" class="form-label">Description</label>
    <textarea class="form-control" id="description" name="description" rows="5"
              maxlength="5000" required><?= htmlspecialchars($f['description']) ?></textarea>
  </div>

  <div class="mb-3">
    <label for="poster" class="form-label">Poster <small>(required to submit for approval)</small></label>
    <input type="file" class="form-control" id="poster" name="poster"
           accept="image/jpeg,image/png,image/webp" aria-describedby="poster-help">
    <div id="poster-help" class="form-text">JPG, PNG or WebP, up to 5MB.</div>
  </div>

  <div class="mb-3">
    <label for="video_url" class="form-label">Video link (optional)</label>
    <input type="url" class="form-control" id="video_url" name="video_url" maxlength="500"
           value="<?= htmlspecialchars($f['video_url']) ?>">
  </div>

  <div class="mb-3">
    <label for="ticket_url" class="form-label">Ticket link (optional)</label>
    <input type="url" class="form-control" id="ticket_url" name="ticket_url" maxlength="500"
           value="<?= htmlspecialchars($f['ticket_url']) ?>">
  </div>

  <div class="mb-3">
    <label for="ticket_note" class="form-label">Ticket note (optional)</label>
    <input type="text" class="form-control" id="ticket_note" name="ticket_note" maxlength="300"
           aria-describedby="ticket-help" value="<?= htmlspecialchars($f['ticket_note']) ?>">
    <div id="ticket-help" class="form-text">For example: pay at the door.</div>
  </div>

  <div class="mb-3">
    <label for="venue_name" class="form-label">Venue name</label>
    <input type="text" class="form-control" id="venue_name" name="venue_name" maxlength="200"
           required value="<?= htmlspecialchars($f['venue_name']) ?>">
  </div>

  <div class="mb-3">
    <label for="address" class="form-label">Address</label>
    <input type="text" class="form-control" id="address" name="address" maxlength="300"
           autocomplete="street-address" required value="<?= htmlspecialchars($f['address']) ?>">
  </div>

  <div class="mb-3">
    <label for="town" class="form-label">Town</label>
    <select class="form-select" id="town" name="town" required>
      <option value="">Choose a town</option>
      <?php foreach (TOWNS as $t): ?>
        <option <?= $t === $f['town'] ? 'selected' : '' ?>><?= htmlspecialchars($t) ?></option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="row g-2 mb-3">
    <div class="col-6">
      <label for="start_date" class="form-label">Start date</label>
      <input type="date" class="form-control" id="start_date" name="start_date"
             min="<?= date('Y-m-d') ?>" required value="<?= htmlspecialchars($f['start_date']) ?>">
    </div>
    <div class="col-6">
      <label for="end_date" class="form-label">End date</label>
      <input type="date" class="form-control" id="end_date" name="end_date"
             min="<?= date('Y-m-d') ?>" required value="<?= htmlspecialchars($f['end_date']) ?>">
    </div>
    <div class="col-6">
      <label for="start_time" class="form-label">Start time</label>
      <input type="time" class="form-control" id="start_time" name="start_time"
             required value="<?= htmlspecialchars($f['start_time']) ?>">
    </div>
    <div class="col-6">
      <label for="end_time" class="form-label">End time</label>
      <input type="time" class="form-control" id="end_time" name="end_time"
             required value="<?= htmlspecialchars($f['end_time']) ?>">
    </div>
  </div>

  <fieldset class="mb-3">
    <legend class="h6">Price</legend>
    <div class="btn-group mb-2" role="group" aria-label="Price type">
      <?php foreach (['free' => 'Free', 'amount' => 'Amount', 'range' => 'Range'] as $val => $label): ?>
        <input type="radio" class="btn-check" name="price_type" id="pt_<?= $val ?>"
               value="<?= $val ?>" autocomplete="off" required
               <?= $f['price_type'] === $val ? 'checked' : '' ?>>
        <label class="btn btn-outline-dark" for="pt_<?= $val ?>"><?= $label ?></label>
      <?php endforeach; ?>
    </div>
    <div id="price-min-box" class="mb-2" hidden>
      <label id="price-min-label" for="price_min" class="form-label">Amount (N$)</label>
      <input type="text" inputmode="decimal" class="form-control" id="price_min" name="price_min"
             maxlength="12" value="<?= htmlspecialchars($f['price_min']) ?>">
    </div>
    <div id="price-max-box" class="mb-2" hidden>
      <label for="price_max" class="form-label">To (N$)</label>
      <input type="text" inputmode="decimal" class="form-control" id="price_max" name="price_max"
             maxlength="12" value="<?= htmlspecialchars($f['price_max']) ?>">
    </div>
  </fieldset>

  <!-- Save draft comes first, so pressing Enter in a field saves a draft, never a submission -->
  <div class="row g-2">
    <div class="col-6"><button class="btn btn-outline-dark w-100" name="action" value="draft">Save draft</button></div>
    <div class="col-6"><button class="btn btn-dark w-100" name="action" value="submit">Submit for approval</button></div>
  </div>
</form>

<script>
  (function () {
    var minBox = document.getElementById('price-min-box');
    var maxBox = document.getElementById('price-max-box');
    var minLabel = document.getElementById('price-min-label');
    function update() {
      var checked = document.querySelector('input[name="price_type"]:checked');
      var t = checked ? checked.value : '';
      minBox.hidden = !(t === 'amount' || t === 'range');
      maxBox.hidden = t !== 'range';
      minLabel.textContent = t === 'range' ? 'From (N$)' : 'Amount (N$)';
    }
    document.querySelectorAll('input[name="price_type"]').forEach(function (r) {
      r.addEventListener('change', update);
    });
    update();
  })();
</script>