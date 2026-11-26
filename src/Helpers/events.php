<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/towns.php';

function event_defaults(): array {
  return [
    'title' => '', 'category_id' => 0, 'description' => '',
    'video_url' => '', 'ticket_url' => '', 'ticket_note' => '',
    'venue_name' => '', 'address' => '', 'town' => '',
    'start_date' => '', 'start_time' => '', 'end_date' => '', 'end_time' => '',
    'price_type' => '', 'price_min' => '', 'price_max' => '',
  ];
}

function event_input_from_post(): array {
  return [
    'title'       => trim(post_str('title')),
    'category_id' => (int) post_str('category_id'),
    'description' => trim(post_str('description')),
    'video_url'   => trim(post_str('video_url')),
    'ticket_url'  => trim(post_str('ticket_url')),
    'ticket_note' => trim(post_str('ticket_note')),
    'venue_name'  => trim(post_str('venue_name')),
    'address'     => trim(post_str('address')),
    'town'        => post_str('town'),
    'start_date'  => post_str('start_date'),
    'start_time'  => post_str('start_time'),
    'end_date'    => post_str('end_date'),
    'end_time'    => post_str('end_time'),
    'price_type'  => post_str('price_type'),
    'price_min'   => trim(post_str('price_min')),
    'price_max'   => trim(post_str('price_max')),
  ];
}

function valid_http_url(string $u): bool {
  return mb_strlen($u) <= 500
    && preg_match('#^https?://#i', $u) === 1
    && filter_var($u, FILTER_VALIDATE_URL) !== false;
}

// "80", "80.5" or "80,50" become "80.00"; anything else becomes null
function parse_money(string $s): ?string {
  $s = str_replace([' ', ','], ['', '.'], trim($s));
  if (!preg_match('/^\d{1,8}(\.\d{1,2})?$/', $s)) {
    return null;
  }
  return number_format((float) $s, 2, '.', '');
}

// Returns '' when everything is fine, otherwise the first error message.
// On success it also fills start_datetime, end_datetime, price_min_db and price_max_db.
function validate_event(array &$f, array $validCategoryIds): string {
  if ($f['title'] === '' || mb_strlen($f['title']) > 200) {
    return 'Please enter a title (max 200 characters).';
  }
  if (!in_array($f['category_id'], $validCategoryIds, true)) {
    return 'Please choose a category.';
  }
  if ($f['description'] === '' || mb_strlen($f['description']) > 5000) {
    return 'Please enter a description (max 5000 characters).';
  }
  if ($f['venue_name'] === '' || mb_strlen($f['venue_name']) > 200) {
    return 'Please enter the venue name (max 200 characters).';
  }
  if ($f['address'] === '' || mb_strlen($f['address']) > 300) {
    return 'Please enter the full address (max 300 characters).';
  }
  if (!in_array($f['town'], TOWNS, true)) {
    return 'Please choose a town from the list.';
  }
  if ($f['video_url'] !== '' && !valid_http_url($f['video_url'])) {
    return 'The video link must start with http:// or https://.';
  }
  if ($f['ticket_url'] !== '' && !valid_http_url($f['ticket_url'])) {
    return 'The ticket link must start with http:// or https://.';
  }
  if (mb_strlen($f['ticket_note']) > 300) {
    return 'The ticket note is too long (max 300 characters).';
  }

  // Dates and times, Africa/Windhoek (BR-13)
  $tz        = new DateTimeZone('Africa/Windhoek');
  $startText = $f['start_date'] . ' ' . $f['start_time'];
  $endText   = $f['end_date'] . ' ' . $f['end_time'];
  $start = DateTimeImmutable::createFromFormat('!Y-m-d H:i', $startText, $tz);
  $end   = DateTimeImmutable::createFromFormat('!Y-m-d H:i', $endText, $tz);

  if (!$start || !$end
      || $start->format('Y-m-d H:i') !== $startText
      || $end->format('Y-m-d H:i') !== $endText) {
    return 'Please enter valid start and end dates and times.';
  }
  if ($start < new DateTimeImmutable('now', $tz)) {
    return 'The start date and time cannot be in the past.';
  }
  if ($end < $start) {
    return 'The event cannot end before it starts.';
  }
  $f['start_datetime'] = $start->format('Y-m-d H:i:00');
  $f['end_datetime']   = $end->format('Y-m-d H:i:00');

  // Price (BR-20); the database CHECK rule expects exactly these combinations
  $min = parse_money($f['price_min']);
  $max = parse_money($f['price_max']);

  switch ($f['price_type']) {
    case 'free':
      $min = $max = null;
      break;
    case 'amount':
      if ($min === null) {
        return 'Please enter a valid price, for example 80 or 80.50.';
      }
      if ((float) $min <= 0) {
        return 'Please enter a price above 0, or choose Free.';
      }
      $max = null;
      break;
    case 'range':
      if ($min === null || $max === null) {
        return 'Please enter both ends of the price range, for example 50 and 120.';
      }
      if ((float) $max < (float) $min) {
        return 'The highest price cannot be lower than the lowest price.';
      }
      break;
    default:
      return 'Please choose a price: Free, Amount or Range.';
  }
  $f['price_min_db'] = $min;
  $f['price_max_db'] = $max;

  return '';
}