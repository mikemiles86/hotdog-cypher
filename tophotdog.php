<?php
$alpha = str_split("ABCDEFGHIJKLMNOPQRSTUVWXYZ", 1);

if (isset($_POST['cypher'])) {
  $cypher = str_split(cleanup($_POST['cypher']), 1);
  $key1 = str_split(key_adjust(cleanup($_POST['key1'], '/[^\d]+/'), count($cypher)), 1);
  $key2 = str_split(key_adjust(cleanup($_POST['key2'], '/[^a-zA-Z]+/'), count($cypher)), 1);

  if ((count($cypher)>1) && (count($key1)>1) && (count($key2)>1)) {
    $ceaser = ceaser($cypher, $key1);
    $vig    = vignette($ceaser, $key2);
  }
}
/*
if (isset($_POST['old_cyphers'])) {
  $cyphers = explode(',', cleanup($_POST['old_cyphers'],'/\s+/'));

  $c_len = $_POST['trim'] ? $_POST['trim'] : strlen($cyphers[0]);
  $combo_prefix = $_POST['combo_prefix'] ? $_POST['combo_prefix'] : '';
  $brutes = array();


  for ($c=0; $c < $c_len; $c++) {
    $brutes[$c] = implode($alpha);
  }

  foreach ($cyphers as $cypher) {
    $cypher = array_slice(str_split($cypher, 1), strlen($combo_prefix), $c_len);

    foreach ($cypher as $x => $c) {
      $brutes[$x] = str_replace($c, '', $brutes[$x]);
    }
  }

  foreach ($brutes as &$b) {
    $b = str_split($b, 1);
    if ($_POST['trim']) {
     $b = array_slice($b, strlen($combo_prefix), $_POST['trim']);
    }
  }


  $combos = buildCombos($brutes, $c_len);

 //$combos = $brutes;
}
*/

function buildCombos($dicts, $size, $prefix = '', $run = 0) {
  global $combo_prefix;
  $combos = array();
  foreach ($dicts as $x => $dict) {
    $subs = array_slice($dicts, ($x+1));
    foreach ($dict as $y => $char) {
      $string = $prefix . $char;
      if (count($subs) > 0) {
        $combos = array_merge($combos, buildCombos($subs, $size, $string, $run+1));
      }
      else if (strlen($string) == $size) {
        $combos[] = $combo_prefix . $string;
      }
    }
  }
  return $combos;
}



function ceaser($cypher, $key) {
  global $alpha;

  $ceaser = array();

  foreach ($cypher as $x => $c) {
    $shift = array_search($c, $alpha) + $key[$x];

    if ($shift > (count($alpha)-1)) {
      $shift -= (count($alpha)-1) - 1;
    }

    $ceaser[$x] = $alpha[$shift];
  }

  return $ceaser;
}

function vignette($cypher, $key) {
  global $alpha;

  $vig = array();
  foreach ($cypher as $x => $c) {
    $v = array_search($c, $alpha) + array_search($key[$x], $alpha);
    $v = $v%count($alpha);
    $vig[$x] = $alpha[$v];
  }

  return $vig;
}

function cleanup($string, $remove_pat = NULL) {
  $string = strtoupper($string);
  $string = trim($string);
  if ($remove_pat) {
    $string = preg_replace($remove_pat, '', $string);
  }

  $string = trim($string);
  return $string;
}

function key_adjust($key, $length) {
  $key .= strrev($key);
  if (strlen($key) > 0) {
    while (strlen($key) < $length) {
      $key .= $key;
    }
  }

  if (strlen($key) > $length) {
    $key = substr($key, 0, $length);
  }

  return $key;
}
?>
<html>
<body>
  <?php if (isset($vig)): ?>
  <table border=1>
      <thead>
        <td>Name</td>
        <td>Value</td>
        <td>Length</td>
      </thead>
    <tr>
      <td>CYPHER</td>
      <td><?php echo implode('', $cypher); ?></td>
      <td><?php echo count($cypher); ?></td>
    </tr>
    <tr>
      <td>KEY 1</td>
      <td><?php echo implode('', $key1); ?></td>
      <td><?php echo count($key1); ?></td>
    </tr>
    <tr>
      <td>KEY 2</td>
      <td><?php echo implode('', $key2); ?></td>
      <td><?php echo count($key2); ?></td>
    </tr>
    <tr>
      <td>CEASER</td>
      <td><?php echo implode('', $ceaser); ?></td>
      <td><?php echo count($ceaser); ?></td>
    </tr>
    <tr>
      <td>VIGN</td>
      <td><?php echo implode('', $vig); ?></td>
      <td><?php echo count($vig); ?></td>
    </tr>
  </table>
  <?php endif; ?>
  <?php if(isset($combos)): ?>
    <table>
      <?php foreach ($combos as $combo): ?>
      <tr>
        <td><?php echo $combo; ?></td>
      </tr>
    <?php endforeach; ?>
    </table>
  <?php endif; ?>
  <br />
  <form method="POST">
    <label for="cypher"><strong>CYPHER</strong></label>
    <input type="textfield" name="cypher" size="100" value="<?php echo isset($_POST['cypher']) ? $_POST['cypher']:''; ?>" />
    <br />
    <label for="key1"><strong>KEY 1</strong></label>
    <input type="textfield" name="key1" size="100" value="<?php echo isset($_POST['key1']) ? $_POST['key1']:''; ?>" />
    <br />
    <label for="key2"><strong>KEY 2</strong></label>
    <input type="textfield" name="key2" size="100" value="<?php echo isset($_POST['key2']) ? $_POST['key2']:''; ?>" />
    <br />
    <label for="old_cyphers"><strong>PREVIOUS CYPHERS</strong></label><br />
    <textarea name="old_cyphers" cols=80 rows=20><?php echo isset($_POST['old_cyphers']) ? $_POST['old_cyphers']:''; ?></textarea>
    <br />
    <label for="trim"><strong>trim</strong></label>
    <input type="textfield" name="trim" size="100" value="<?php echo isset($_POST['trim']) ? $_POST['trim']:''; ?>" />
    <br />
    <label for="combo_prefix"><strong>PREFIX</strong></label>
    <input type="textfield" name="combo_prefix" size="100" value="<?php echo isset($_POST['combo_prefix']) ? $_POST['combo_prefix']:''; ?>" />
    <br />
    <input type="submit" value="SOLVE!" />
  </form>
</body>
</html>
