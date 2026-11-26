<?php
const POSTER_MAX_BYTES = 5 * 1024 * 1024;   // NFR-03

function poster_dir(): string {
  return dirname(__DIR__, 2) . '/public/uploads';
}

// Checks, resizes and re-saves an uploaded poster.
// Returns [file name, ''] on success, or [null, error message].
function save_poster(array $file): array {
  $err = is_int($file['error'] ?? null) ? $file['error'] : UPLOAD_ERR_NO_FILE;

  if ($err === UPLOAD_ERR_INI_SIZE || $err === UPLOAD_ERR_FORM_SIZE) {
    return [null, 'The poster is larger than 5MB.'];
  }
  if ($err !== UPLOAD_ERR_OK
      || !is_string($file['tmp_name'] ?? null)
      || !is_uploaded_file($file['tmp_name'])) {
    return [null, 'The poster could not be uploaded. Please try again.'];
  }
  if (($file['size'] ?? 0) > POSTER_MAX_BYTES) {
    return [null, 'The poster is larger than 5MB.'];
  }
  if (!is_dir(poster_dir()) || !is_writable(poster_dir())) {
    return [null, 'The uploads folder is missing or not writable.'];
  }

  // Trust the file's content, never its name or the browser's claim
  $info  = @getimagesize($file['tmp_name']);
  $types = [
    'image/jpeg' => 'imagecreatefromjpeg',
    'image/png'  => 'imagecreatefrompng',
    'image/webp' => 'imagecreatefromwebp',
  ];
  if (!$info || !isset($types[$info['mime']]) || !function_exists($types[$info['mime']])) {
    return [null, 'The poster must be a JPG, PNG or WebP image.'];
  }
  [$w, $h] = $info;
  if ($w < 1 || $h < 1 || $w > 8000 || $h > 8000 || $w * $h > 25000000) {
    return [null, 'The poster dimensions are too large. Please use a smaller image.'];
  }

  $load = $types[$info['mime']];
  $src  = @$load($file['tmp_name']);
  if (!$src) {
    return [null, 'The poster could not be read. Try saving it again as a JPG or PNG.'];
  }

  // Phone photos store their rotation in EXIF, so apply it (needs extension=exif)
  if ($info['mime'] === 'image/jpeg' && function_exists('exif_read_data')) {
    $exif  = @exif_read_data($file['tmp_name']);
    $angle = [3 => 180, 6 => -90, 8 => 90][$exif['Orientation'] ?? 1] ?? 0;
    if ($angle) {
      $rotated = imagerotate($src, $angle, 0);
      if ($rotated) {
        $src = $rotated;
      }
    }
  }

  // Shrink to at most 1200 x 1600, never enlarge
  $w     = imagesx($src);
  $h     = imagesy($src);
  $scale = min(1, 1200 / $w, 1600 / $h);
  $nw    = max(1, (int) round($w * $scale));
  $nh    = max(1, (int) round($h * $scale));

  $webp = function_exists('imagewebp');
  $dst  = imagecreatetruecolor($nw, $nh);
  if ($webp) {
    imagealphablending($dst, false);
    imagesavealpha($dst, true);
    imagefill($dst, 0, 0, imagecolorallocatealpha($dst, 255, 255, 255, 127));
  } else {
    imagefill($dst, 0, 0, imagecolorallocate($dst, 255, 255, 255));
  }
  imagecopyresampled($dst, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);

  // A random name with a fixed extension, so the upload can never run as code
  $name = bin2hex(random_bytes(16)) . ($webp ? '.webp' : '.jpg');
  $path = poster_dir() . '/' . $name;
  $ok   = $webp ? imagewebp($dst, $path, 80) : imagejpeg($dst, $path, 82);

  if (!$ok) {
    return [null, 'The poster could not be saved. Please try again.'];
  }
  return [$name, ''];
}

function delete_poster(?string $name): void {
  if ($name && preg_match('/^[a-f0-9]{32}\.(webp|jpg)$/', $name)) {
    $file = poster_dir() . '/' . $name;
    if (is_file($file)) {
      unlink($file);
    }
  }
}