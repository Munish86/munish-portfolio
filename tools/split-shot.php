<?php
// Splits a tall screenshot into parts of $h pixels for review: php tools/split-shot.php file.png [height]
$f = $argv[1]; $h = (int) ( $argv[2] ?? 1800 );
$im = imagecreatefrompng( $f ); $W = imagesx( $im ); $H = imagesy( $im );
for ( $i = 0, $y = 0; $y < $H; $y += $h, $i++ ) {
	$ch = min( $h, $H - $y ); $p = imagecreatetruecolor( $W, $ch );
	imagecopy( $p, $im, 0, 0, 0, $y, $W, $ch ); imagepng( $p, preg_replace( '/\.png$/', "-part$i.png", $f ) );
}
echo "$i parts\n";
