<?php
    
// create a template visualisation

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';
auth();

$filename = pathinfo($_POST['img'], PATHINFO_FILENAME);
$filename = ifEmpty($filename, "template") . '.svg';

if ($_POST['type'] == 'png') {
    $svg = base64_encode($_POST['svg']);
    echo "    
<html><script>
    var image = new Image();
    image.onload = function() {
      var canvas = document.createElement('canvas');
      canvas.width = image.width;
      canvas.height = image.height;
      var context = canvas.getContext('2d');
      context.drawImage(image, 0, 0);
     
      var a = document.createElement('a');
      a.download = '{$filename}.png';
      a.href = canvas.toDataURL('image/png');
      document.body.appendChild(a);
      a.click();
    }
    
    image.src = 'data:image/svg+xml;base64,{$svg}';
</script></html>";
} else {
    header('Content-Type: image/svg+xml');
    header('Content-disposition: attachment; filename=' . $filename);
    
    echo $_POST['svg'];
}

exit;
?>