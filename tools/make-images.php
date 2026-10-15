<?php
/**
 * Draws the sample images (content/images/*.png): project covers as website / dashboard / shop / mobile mock-ups,
 * gallery screens (design-system board, phones) and blog covers. Drawn at 2x and scaled down for smooth edges.
 * Needs PHP GD with FreeType and the Segoe UI fonts that come with Windows.
 *
 * Usage: php tools/make-images.php
 */

$root = dirname( __DIR__ );
$out  = $root . '/content/images';
if ( ! is_dir( $out ) ) {
	mkdir( $out, 0777, true );
}
define( 'F_BOLD', 'C:/Windows/Fonts/segoeuib.ttf' );
define( 'F_SEMI', 'C:/Windows/Fonts/seguisb.ttf' );
define( 'F_REG', 'C:/Windows/Fonts/segoeui.ttf' );
define( 'S', 2 ); // Supersampling factor.

/** Colour from hex, with alpha 0 (solid) .. 127 (clear). */
function c( $im, $hex, $alpha = 0 ) {
	$hex = ltrim( $hex, '#' );
	return imagecolorallocatealpha( $im, hexdec( substr( $hex, 0, 2 ) ), hexdec( substr( $hex, 2, 2 ) ), hexdec( substr( $hex, 4, 2 ) ), $alpha );
}

/** Filled rounded rectangle (coordinates in 1x units). */
function rr( $im, $x1, $y1, $x2, $y2, $r, $col ) {
	$x1 *= S; $y1 *= S; $x2 *= S; $y2 *= S; $r = min( $r * S, ( $x2 - $x1 ) / 2, ( $y2 - $y1 ) / 2 );
	// Pieces do not overlap, so semi-transparent colours blend evenly.
	$x1 = (int) $x1; $y1 = (int) $y1; $x2 = (int) $x2; $y2 = (int) $y2; $r = (int) $r;
	if ( $r < 1 ) {
		imagefilledrectangle( $im, $x1, $y1, $x2, $y2, $col );
		return;
	}
	imagefilledrectangle( $im, $x1 + $r, $y1, $x2 - $r, $y2, $col );
	imagefilledrectangle( $im, $x1, $y1 + $r, $x1 + $r - 1, $y2 - $r, $col );
	imagefilledrectangle( $im, $x2 - $r + 1, $y1 + $r, $x2, $y2 - $r, $col );
	// Quarter circles, drawn row by row outside the rectangles.
	for ( $dy = 0; $dy < $r; $dy++ ) {
		$dx = (int) round( sqrt( $r * $r - ( $r - $dy - 0.5 ) ** 2 ) );
		if ( $dx < 1 ) {
			continue;
		}
		imageline( $im, $x1 + $r - $dx, $y1 + $dy, $x1 + $r - 1, $y1 + $dy, $col );
		imageline( $im, $x2 - $r + 1, $y1 + $dy, $x2 - $r + $dx, $y1 + $dy, $col );
		imageline( $im, $x1 + $r - $dx, $y2 - $dy, $x1 + $r - 1, $y2 - $dy, $col );
		imageline( $im, $x2 - $r + 1, $y2 - $dy, $x2 - $r + $dx, $y2 - $dy, $col );
	}
}

/** Rounded rectangle outline, by drawing a bigger and a smaller filled one. */
function rro( $im, $x1, $y1, $x2, $y2, $r, $col, $fill, $w = 2 ) {
	rr( $im, $x1, $y1, $x2, $y2, $r, $col );
	rr( $im, $x1 + $w, $y1 + $w, $x2 - $w, $y2 - $w, max( 0, $r - $w ), $fill );
}

function circle( $im, $x, $y, $d, $col ) {
	imagefilledellipse( $im, (int) ( $x * S ), (int) ( $y * S ), (int) ( $d * S ), (int) ( $d * S ), $col );
}

/** Text at x, baseline y (1x units). Returns its width. */
function tx( $im, $size, $x, $y, $col, $font, $text ) {
	$box = imagettftext( $im, $size * S, 0, (int) ( $x * S ), (int) ( $y * S ), $col, $font, $text );
	return ( $box[2] - $box[0] ) / S;
}

function tw( $size, $font, $text ) {
	$b = imagettfbbox( $size * S, 0, $font, $text );
	return ( $b[2] - $b[0] ) / S;
}

/** Soft shadow under a rounded box. */
function shadow( $im, $x1, $y1, $x2, $y2, $r, $hex = '#000000', $strength = 6 ) {
	for ( $i = 14; $i >= 1; $i-- ) {
		rr( $im, $x1 - $i * 2, $y1 - $i * 2 + 18, $x2 + $i * 2, $y2 + $i * 2 + 18, $r + $i * 2, c( $im, $hex, 127 - $strength ) );
	}
}

/** Soft glow circle. */
function glow( $im, $x, $y, $d, $hex, $strength = 3 ) {
	for ( $i = 0; $i < 30; $i++ ) {
		circle( $im, $x, $y, $d * ( 1 - $i / 30 ), c( $im, $hex, 127 - $strength ) );
	}
}

/** Text bar placeholder (a "line of text"). */
function bar( $im, $x, $y, $w, $h, $col ) {
	rr( $im, $x, $y, $x + $w, $y + $h, $h / 2, $col );
}

function canvas( $w, $h, $bg ) {
	$im = imagecreatetruecolor( $w * S, $h * S );
	imagealphablending( $im, true );
	imagesavealpha( $im, false );
	imagefilledrectangle( $im, 0, 0, $w * S, $h * S, c( $im, $bg ) );
	return $im;
}

function save( $im, $w, $h, $file ) {
	$small = imagecreatetruecolor( $w, $h );
	imagecopyresampled( $small, $im, 0, 0, 0, 0, $w, $h, $w * S, $h * S );
	imagepng( $small, $file, 8 );
	imagedestroy( $small );
	imagedestroy( $im );
	echo 'wrote ' . basename( $file ) . "\n";
}

/** Browser window; returns the content box [x1, y1, x2, y2]. */
function browser( $im, $x1, $y1, $x2, $y2, $p ) {
	shadow( $im, $x1, $y1, $x2, $y2, 26, '#000000', 5 );
	rr( $im, $x1, $y1, $x2, $y2, 26, c( $im, $p['page'] ) );
	rr( $im, $x1, $y1, $x2, $y1 + 70, 26, c( $im, $p['chrome'] ) );
	imagefilledrectangle( $im, $x1 * S, ( $y1 + 40 ) * S, $x2 * S, ( $y1 + 70 ) * S, c( $im, $p['chrome'] ) );
	foreach ( array( '#FF5F57', '#FEBC2E', '#28C840' ) as $i => $dot ) {
		circle( $im, $x1 + 34 + $i * 26, $y1 + 35, 15, c( $im, $dot ) );
	}
	rr( $im, $x1 + 180, $y1 + 18, $x2 - 180, $y1 + 52, 17, c( $im, $p['urlbar'] ) );
	tx( $im, 13, $x1 + 205, $y1 + 41, c( $im, $p['muted'] ), F_REG, $p['url'] );
	return array( $x1, $y1 + 70, $x2, $y2 );
}

/** Website nav bar inside a page. */
function nav( $im, $x1, $y, $x2, $p ) {
	rr( $im, $x1, $y - 18, $x1 + 36, $y + 18, 10, c( $im, $p['accent'] ) );
	tx( $im, 20, $x1 + 50, $y + 8, c( $im, $p['ink'] ), F_BOLD, $p['brand'] );
	$x = $x2 - 520;
	foreach ( array( 'Home', 'Work', 'About', 'Journal' ) as $item ) {
		$x += tx( $im, 14, $x, $y + 6, c( $im, $p['muted'] ), F_SEMI, $item ) + 34;
	}
	rr( $im, $x2 - 150, $y - 22, $x2, $y + 22, 22, c( $im, $p['ink'] ) );
	tx( $im, 14, $x2 - 122, $y + 6, c( $im, $p['page'] ), F_SEMI, 'Get started' );
}

/** Decorative picture block (gradient-ish with shapes). */
function picture( $im, $x1, $y1, $x2, $y2, $r, $p, $seed = 0 ) {
	// Drawn on its own image so the glow stays inside the block, then copied with rounded corners.
	$w   = $x2 - $x1;
	$h   = $y2 - $y1;
	$tmp = imagecreatetruecolor( (int) ( $w * S ), (int) ( $h * S ) );
	imagealphablending( $tmp, true );
	imagefilledrectangle( $tmp, 0, 0, (int) ( $w * S ), (int) ( $h * S ), c( $tmp, $p['accent'] ) );
	glow( $tmp, $w * ( 0.75 - 0.4 * ( $seed % 2 ) ), $h * 0.3, max( $w, $h ) * 1.1, $p['accent2'], 6 );
	circle( $tmp, $w * 0.32, $h * 0.58, min( $w, $h ) * 0.42, c( $tmp, '#FFFFFF', 95 ) );
	rr( $tmp, $w * 0.5, $h * 0.22, $w * 0.86, $h * 0.78, 22, c( $tmp, '#FFFFFF', 100 ) );
	circle( $tmp, $w * 0.68, $h * 0.5, min( $w, $h ) * 0.18, c( $tmp, '#FFFFFF', 40 ) );
	$rs = (int) ( $r * S );
	$tw = (int) ( $w * S );
	$th = (int) ( $h * S );
	$ox = (int) ( $x1 * S );
	$oy = (int) ( $y1 * S );
	for ( $yy = 0; $yy < $th; $yy++ ) {
		for ( $xx = 0; $xx < $tw; $xx++ ) {
			// Skip pixels outside the rounded corners (the target keeps its own colour there).
			$cx = $xx < $rs ? $rs : ( $xx >= $tw - $rs ? $tw - $rs - 1 : $xx );
			$cy = $yy < $rs ? $rs : ( $yy >= $th - $rs ? $th - $rs - 1 : $yy );
			if ( ( $cx !== $xx || $cy !== $yy ) && ( ( $xx - $cx ) ** 2 + ( $yy - $cy ) ** 2 ) > $rs * $rs ) {
				continue;
			}
			imagesetpixel( $im, $ox + $xx, $oy + $yy, imagecolorat( $tmp, $xx, $yy ) );
		}
	}
	imagedestroy( $tmp );
}

/* ---------------- Layouts ---------------- */

function layout_landing( $p, $W, $H ) {
	$im = canvas( $W, $H, $p['bg'] );
	glow( $im, $W * 0.9, 80, 700, $p['accent2'], 4 );
	glow( $im, 80, $H, 600, $p['accent'], 4 );
	list( $x1, $y1, $x2 ) = browser( $im, 110, 120, $W - 110, $H + 60, $p );
	nav( $im, $x1 + 56, $y1 + 62, $x2 - 56, $p );
	// Hero copy.
	$hy = $y1 + 190;
	rr( $im, $x1 + 56, $hy - 30, $x1 + 56 + tw( 12, F_SEMI, $p['tag'] ) + 36, $hy + 6, 18, c( $im, $p['accent'], 105 ) );
	tx( $im, 12, $x1 + 74, $hy - 6, c( $im, $p['accent'] ), F_SEMI, $p['tag'] );
	$ly = $hy + 70;
	foreach ( $p['headline'] as $line ) {
		tx( $im, 44, $x1 + 52, $ly, c( $im, $p['ink'] ), F_BOLD, $line );
		$ly += 64;
	}
	bar( $im, $x1 + 56, $ly - 10, 470, 14, c( $im, $p['muted'], 80 ) );
	bar( $im, $x1 + 56, $ly + 18, 390, 14, c( $im, $p['muted'], 80 ) );
	rr( $im, $x1 + 56, $ly + 66, $x1 + 246, $ly + 122, 28, c( $im, $p['accent'] ) );
	tx( $im, 15, $x1 + 92, $ly + 101, c( $im, '#FFFFFF' ), F_SEMI, $p['cta'] );
	rro( $im, $x1 + 262, $ly + 66, $x1 + 432, $ly + 122, 28, c( $im, $p['line'] ), c( $im, $p['page'] ) );
	tx( $im, 15, $x1 + 298, $ly + 101, c( $im, $p['ink'] ), F_SEMI, 'Learn more' );
	// Hero picture.
	$px1 = $x1 + ( $x2 - $x1 ) * 0.55;
	picture( $im, $px1, $y1 + 130, $x2 - 56, $y1 + 560, 28, $p, 1 );
	// Floating stat card.
	shadow( $im, $px1 - 60, $y1 + 470, $px1 + 200, $y1 + 570, 18, '#000000', 4 );
	rr( $im, $px1 - 60, $y1 + 470, $px1 + 200, $y1 + 570, 18, c( $im, $p['card'] ) );
	circle( $im, $px1 - 12, $y1 + 520, 52, c( $im, $p['accent2'] ) );
	tx( $im, 22, $px1 + 30, $y1 + 515, c( $im, $p['ink'] ), F_BOLD, $p['stat'] );
	tx( $im, 12, $px1 + 30, $y1 + 542, c( $im, $p['muted'] ), F_REG, $p['statLabel'] );
	// Feature cards row.
	$cy = $y1 + 640;
	$cw = ( $x2 - $x1 - 112 - 48 ) / 3;
	for ( $i = 0; $i < 3; $i++ ) {
		$cx = $x1 + 56 + $i * ( $cw + 24 );
		rr( $im, $cx, $cy, $cx + $cw, $cy + 260, 22, c( $im, $p['card'] ) );
		rr( $im, $cx + 28, $cy + 28, $cx + 84, $cy + 84, 16, c( $im, 0 === $i ? $p['accent'] : $p['accent'], 0 === $i ? 0 : 100 ) );
		tx( $im, 18, $cx + 28, $cy + 130, c( $im, $p['ink'] ), F_BOLD, $p['features'][ $i ] );
		bar( $im, $cx + 28, $cy + 156, $cw - 80, 11, c( $im, $p['muted'], 85 ) );
		bar( $im, $cx + 28, $cy + 178, $cw - 130, 11, c( $im, $p['muted'], 85 ) );
	}
	return $im;
}

function layout_shop( $p, $W, $H ) {
	$im = canvas( $W, $H, $p['bg'] );
	glow( $im, $W * 0.1, 60, 700, $p['accent2'], 4 );
	list( $x1, $y1, $x2 ) = browser( $im, 110, 120, $W - 110, $H + 60, $p );
	nav( $im, $x1 + 56, $y1 + 62, $x2 - 56, $p );
	// Banner.
	$by = $y1 + 120;
	rr( $im, $x1 + 56, $by, $x2 - 56, $by + 330, 26, c( $im, $p['accent'] ) );
	circle( $im, $x2 - 260, $by + 165, 320, c( $im, $p['accent2'], 40 ) );
	circle( $im, $x2 - 260, $by + 165, 210, c( $im, '#FFFFFF', 90 ) );
	rr( $im, $x2 - 300, $by + 70, $x2 - 220, $by + 250, 30, c( $im, $p['page'] ) );
	rr( $im, $x2 - 290, $by + 50, $x2 - 230, $by + 80, 8, c( $im, $p['ink'] ) );
	tx( $im, 12, $x1 + 100, $by + 80, c( $im, '#FFFFFF', 30 ), F_SEMI, strtoupper( $p['tag'] ) );
	$ly = $by + 150;
	foreach ( $p['headline'] as $line ) {
		tx( $im, 40, $x1 + 96, $ly, c( $im, '#FFFFFF' ), F_BOLD, $line );
		$ly += 58;
	}
	rr( $im, $x1 + 100, $ly + 6, $x1 + 280, $ly + 58, 26, c( $im, '#FFFFFF' ) );
	tx( $im, 15, $x1 + 136, $ly + 39, c( $im, $p['ink'] ), F_SEMI, $p['cta'] );
	// Products.
	$py = $by + 380;
	$cw = ( $x2 - $x1 - 112 - 72 ) / 4;
	$colors = array( $p['accent2'], $p['card'], $p['accent'], $p['card'] );
	foreach ( $p['products'] as $i => $prod ) {
		$cx = $x1 + 56 + $i * ( $cw + 24 );
		rr( $im, $cx, $py, $cx + $cw, $py + 300, 22, c( $im, $p['card'] ) );
		circle( $im, $cx + $cw / 2, $py + 150, 190, c( $im, $colors[ $i ], 0 === $i % 2 ? 60 : 0 ) );
		rr( $im, $cx + $cw / 2 - 40, $py + 80, $cx + $cw / 2 + 40, $py + 230, 22, c( $im, 0 === $i % 2 ? $p['accent'] : $p['accent2'] ) );
		rr( $im, $cx + $cw / 2 - 30, $py + 62, $cx + $cw / 2 + 30, $py + 90, 6, c( $im, $p['ink'] ) );
		tx( $im, 16, $cx + 4, $py + 340, c( $im, $p['ink'] ), F_SEMI, $prod[0] );
		tx( $im, 15, $cx + 4, $py + 368, c( $im, $p['muted'] ), F_REG, $prod[1] );
	}
	return $im;
}

function layout_dashboard( $p, $W, $H ) {
	$im = canvas( $W, $H, $p['bg'] );
	glow( $im, $W * 0.85, 40, 760, $p['accent'], 5 );
	glow( $im, 100, $H - 100, 500, $p['accent2'], 3 );
	list( $x1, $y1, $x2, $y2 ) = browser( $im, 110, 120, $W - 110, $H + 60, $p );
	// Sidebar.
	imagefilledrectangle( $im, $x1 * S, $y1 * S, ( $x1 + 230 ) * S, ( $H ) * S, c( $im, $p['sidebar'] ) );
	rr( $im, $x1 + 30, $y1 + 34, $x1 + 64, $y1 + 68, 10, c( $im, $p['accent'] ) );
	tx( $im, 18, $x1 + 76, $y1 + 59, c( $im, $p['ink'] ), F_BOLD, $p['brand'] );
	foreach ( array( 'Overview', 'Reports', 'Customers', 'Revenue', 'Settings' ) as $i => $item ) {
		$iy = $y1 + 130 + $i * 54;
		if ( 0 === $i ) {
			rr( $im, $x1 + 18, $iy - 30, $x1 + 212, $iy + 14, 12, c( $im, $p['accent'], 90 ) );
		}
		rr( $im, $x1 + 32, $iy - 18, $x1 + 50, $iy, 5, c( $im, 0 === $i ? $p['accent'] : $p['muted'], 0 === $i ? 0 : 60 ) );
		tx( $im, 14, $x1 + 64, $iy - 2, c( $im, 0 === $i ? $p['ink'] : $p['muted'] ), F_SEMI, $item );
	}
	$cx1 = $x1 + 270;
	$cx2 = $x2 - 40;
	tx( $im, 26, $cx1, $y1 + 66, c( $im, $p['ink'] ), F_BOLD, 'Good morning, Sara' );
	tx( $im, 13, $cx1, $y1 + 94, c( $im, $p['muted'] ), F_REG, 'Here is what happened with your product this week.' );
	rr( $im, $cx2 - 260, $y1 + 40, $cx2, $y1 + 84, 22, c( $im, $p['card'] ) );
	tx( $im, 13, $cx2 - 232, $y1 + 68, c( $im, $p['muted'] ), F_REG, 'Search reports...' );
	// KPI cards.
	$kw = ( $cx2 - $cx1 - 60 ) / 4;
	foreach ( $p['kpis'] as $i => $k ) {
		$kx = $cx1 + $i * ( $kw + 20 );
		$ky = $y1 + 130;
		rr( $im, $kx, $ky, $kx + $kw, $ky + 150, 20, c( $im, $p['card'] ) );
		tx( $im, 13, $kx + 24, $ky + 40, c( $im, $p['muted'] ), F_SEMI, $k[0] );
		tx( $im, 28, $kx + 24, $ky + 92, c( $im, $p['ink'] ), F_BOLD, $k[1] );
		rr( $im, $kx + 24, $ky + 110, $kx + 24 + tw( 12, F_SEMI, $k[2] ) + 20, $ky + 134, 12, c( $im, '#22C55E', 95 ) );
		tx( $im, 12, $kx + 34, $ky + 127, c( $im, '#16A34A' ), F_SEMI, $k[2] );
	}
	// Line chart card.
	$gy = $y1 + 310;
	$gx2 = $cx1 + ( $cx2 - $cx1 ) * 0.64;
	rr( $im, $cx1, $gy, $gx2, $gy + 420, 22, c( $im, $p['card'] ) );
	tx( $im, 17, $cx1 + 28, $gy + 46, c( $im, $p['ink'] ), F_BOLD, 'Active users' );
	tx( $im, 13, $cx1 + 28, $gy + 72, c( $im, $p['muted'] ), F_REG, 'Last 12 weeks' );
	for ( $g = 0; $g < 4; $g++ ) {
		imagefilledrectangle( $im, ( $cx1 + 28 ) * S, (int) ( ( $gy + 130 + $g * 70 ) * S ), (int) ( ( $gx2 - 28 ) * S ), (int) ( ( $gy + 130 + $g * 70 ) * S ) + 1, c( $im, $p['muted'], 105 ) );
	}
	$vals  = array( 0.3, 0.42, 0.38, 0.5, 0.47, 0.6, 0.55, 0.68, 0.64, 0.78, 0.74, 0.9 );
	$pts   = array();
	$step  = ( $gx2 - $cx1 - 56 ) / ( count( $vals ) - 1 );
	foreach ( $vals as $i => $v ) {
		$pts[] = array( $cx1 + 28 + $i * $step, $gy + 380 - $v * 240 );
	}
	$poly = array();
	foreach ( $pts as $pt ) {
		$poly[] = (int) ( $pt[0] * S );
		$poly[] = (int) ( $pt[1] * S );
	}
	$poly[] = (int) ( end( $pts )[0] * S );
	$poly[] = (int) ( ( $gy + 390 ) * S );
	$poly[] = (int) ( $pts[0][0] * S );
	$poly[] = (int) ( ( $gy + 390 ) * S );
	imagefilledpolygon( $im, $poly, c( $im, $p['accent'], 100 ) );
	imagesetthickness( $im, 5 * S );
	for ( $i = 1; $i < count( $pts ); $i++ ) {
		imageline( $im, (int) ( $pts[ $i - 1 ][0] * S ), (int) ( $pts[ $i - 1 ][1] * S ), (int) ( $pts[ $i ][0] * S ), (int) ( $pts[ $i ][1] * S ), c( $im, $p['accent'] ) );
	}
	imagesetthickness( $im, 1 );
	foreach ( $pts as $i => $pt ) {
		if ( 9 === $i ) {
			circle( $im, $pt[0], $pt[1], 22, c( $im, $p['card'] ) );
			circle( $im, $pt[0], $pt[1], 14, c( $im, $p['accent'] ) );
			rr( $im, $pt[0] - 60, $pt[1] - 70, $pt[0] + 60, $pt[1] - 26, 12, c( $im, $p['ink'] ) );
			tx( $im, 14, $pt[0] - 40, $pt[1] - 41, c( $im, $p['card'] ), F_SEMI, '12,480' );
		}
	}
	// Donut card.
	$dx1 = $gx2 + 20;
	rr( $im, $dx1, $gy, $cx2, $gy + 420, 22, c( $im, $p['card'] ) );
	tx( $im, 17, $dx1 + 28, $gy + 46, c( $im, $p['ink'] ), F_BOLD, 'Traffic sources' );
	$dcx = ( $dx1 + $cx2 ) / 2;
	$dcy = $gy + 210;
	$segs = array( array( 0, 150, $p['accent'] ), array( 150, 250, $p['accent2'] ), array( 250, 320, '#22C55E' ), array( 320, 360, $p['muted'] ) );
	foreach ( $segs as $sg ) {
		imagefilledarc( $im, (int) ( $dcx * S ), (int) ( $dcy * S ), 220 * S, 220 * S, $sg[0] - 90, $sg[1] - 90, c( $im, $sg[2] ), IMG_ARC_PIE );
	}
	circle( $im, $dcx, $dcy, 140, c( $im, $p['card'] ) );
	tx( $im, 24, $dcx - tw( 24, F_BOLD, '68%' ) / 2, $dcy + 10, c( $im, $p['ink'] ), F_BOLD, '68%' );
	$lx = $dx1 + 30;
	foreach ( array( array( 'Organic', $p['accent'] ), array( 'Direct', $p['accent2'] ), array( 'Referral', '#22C55E' ) ) as $l ) {
		circle( $im, $lx + 6, $gy + 366, 12, c( $im, $l[1] ) );
		$lx += tx( $im, 12, $lx + 18, $gy + 371, c( $im, $p['muted'] ), F_SEMI, $l[0] ) + 40;
	}
	// Table.
	$ty = $gy + 450;
	rr( $im, $cx1, $ty, $cx2, $ty + 300, 22, c( $im, $p['card'] ) );
	foreach ( array( 'Customer', 'Plan', 'Status', 'MRR' ) as $i => $h ) {
		tx( $im, 12, $cx1 + 28 + $i * ( ( $cx2 - $cx1 ) / 4 ), $ty + 44, c( $im, $p['muted'] ), F_SEMI, strtoupper( $h ) );
	}
	$rows = array( array( 'Northwind', 'Scale', '$2,400' ), array( 'Acme Inc.', 'Growth', '$890' ), array( 'Globex', 'Scale', '$1,760' ) );
	foreach ( $rows as $i => $row ) {
		$ry = $ty + 100 + $i * 62;
		circle( $im, $cx1 + 44, $ry - 6, 32, c( $im, 0 === $i % 2 ? $p['accent'] : $p['accent2'], 70 ) );
		tx( $im, 14, $cx1 + 72, $ry, c( $im, $p['ink'] ), F_SEMI, $row[0] );
		tx( $im, 14, $cx1 + 28 + ( $cx2 - $cx1 ) / 4, $ry, c( $im, $p['ink'] ), F_REG, $row[1] );
		rr( $im, $cx1 + 28 + ( $cx2 - $cx1 ) / 2, $ry - 22, $cx1 + 110 + ( $cx2 - $cx1 ) / 2, $ry + 6, 14, c( $im, '#22C55E', 95 ) );
		tx( $im, 12, $cx1 + 44 + ( $cx2 - $cx1 ) / 2, $ry - 3, c( $im, '#16A34A' ), F_SEMI, 'Active' );
		tx( $im, 14, $cx1 + 28 + 3 * ( $cx2 - $cx1 ) / 4, $ry, c( $im, $p['ink'] ), F_SEMI, $row[2] );
	}
	return $im;
}

/** One phone with an app screen. */
function phone( $im, $x, $y, $w, $h, $p, $variant ) {
	shadow( $im, $x, $y, $x + $w, $y + $h, 54, '#000000', 7 );
	rr( $im, $x, $y, $x + $w, $y + $h, 54, c( $im, '#0B0B10' ) );
	rr( $im, $x + 12, $y + 12, $x + $w - 12, $y + $h - 12, 44, c( $im, $p['page'] ) );
	rr( $im, $x + $w / 2 - 50, $y + 26, $x + $w / 2 + 50, $y + 52, 13, c( $im, '#0B0B10' ) );
	$ix = $x + 40;
	$iw = $w - 80;
	tx( $im, 12, $ix, $y + 46, c( $im, $p['ink'] ), F_SEMI, '9:41' );
	if ( 0 === $variant ) {
		tx( $im, 13, $ix, $y + 112, c( $im, $p['muted'] ), F_REG, 'Today' );
		tx( $im, 24, $ix, $y + 146, c( $im, $p['ink'] ), F_BOLD, 'Hi, Riya' );
		$cx = $x + $w / 2;
		$cy = $y + 300;
		imagefilledarc( $im, (int) ( $cx * S ), (int) ( $cy * S ), 230 * S, 230 * S, -90, 190, c( $im, $p['accent'] ), IMG_ARC_PIE );
		imagefilledarc( $im, (int) ( $cx * S ), (int) ( $cy * S ), 230 * S, 230 * S, 190, 270, c( $im, $p['accent'], 100 ), IMG_ARC_PIE );
		circle( $im, $cx, $cy, 180, c( $im, $p['page'] ) );
		tx( $im, 30, $cx - tw( 30, F_BOLD, '7,842' ) / 2, $cy + 6, c( $im, $p['ink'] ), F_BOLD, '7,842' );
		tx( $im, 12, $cx - tw( 12, F_REG, 'steps of 10k' ) / 2, $cy + 32, c( $im, $p['muted'] ), F_REG, 'steps of 10k' );
		foreach ( array( array( 'Calories', '486' ), array( 'Minutes', '52' ) ) as $i => $k ) {
			$kx = $ix + $i * ( $iw / 2 + 6 );
			rr( $im, $kx, $y + 460, $kx + $iw / 2 - 6, $y + 560, 20, c( $im, 0 === $i ? $p['accent2'] : $p['card'], 0 === $i ? 0 : 0 ) );
			tx( $im, 12, $kx + 18, $y + 492, c( $im, 0 === $i ? '#FFFFFF' : $p['muted'] ), F_SEMI, $k[0] );
			tx( $im, 26, $kx + 18, $y + 538, c( $im, 0 === $i ? '#FFFFFF' : $p['ink'] ), F_BOLD, $k[1] );
		}
		rr( $im, $ix, $y + 580, $ix + $iw, $y + 660, 20, c( $im, $p['card'] ) );
		circle( $im, $ix + 40, $y + 620, 40, c( $im, $p['accent'], 80 ) );
		bar( $im, $ix + 74, $y + 604, $iw - 120, 10, c( $im, $p['ink'], 60 ) );
		bar( $im, $ix + 74, $y + 626, $iw - 160, 10, c( $im, $p['muted'], 80 ) );
	} elseif ( 1 === $variant ) {
		tx( $im, 22, $ix, $y + 120, c( $im, $p['ink'] ), F_BOLD, 'Workouts' );
		$cats = array( 'All', 'Yoga', 'HIIT' );
		$cx   = $ix;
		foreach ( $cats as $i => $cat ) {
			$wdt = tw( 12, F_SEMI, $cat ) + 30;
			rr( $im, $cx, $y + 140, $cx + $wdt, $y + 174, 17, c( $im, 0 === $i ? $p['ink'] : $p['card'] ) );
			tx( $im, 12, $cx + 15, $y + 162, c( $im, 0 === $i ? $p['page'] : $p['muted'] ), F_SEMI, $cat );
			$cx += $wdt + 8;
		}
		foreach ( array( $p['accent'], $p['accent2'], $p['accent'] ) as $i => $col ) {
			$cy = $y + 200 + $i * 160;
			rr( $im, $ix, $cy, $ix + $iw, $cy + 144, 24, c( $im, $col, 1 === $i ? 0 : ( 0 === $i ? 0 : 90 ) ) );
			circle( $im, $ix + $iw - 50, $cy + 72, 120, c( $im, '#FFFFFF', 100 ) );
			tx( $im, 17, $ix + 22, $cy + 48, c( $im, 2 === $i ? $p['ink'] : '#FFFFFF' ), F_BOLD, array( 'Morning flow', 'Core burn', 'Deep stretch' )[ $i ] );
			tx( $im, 12, $ix + 22, $cy + 74, c( $im, 2 === $i ? $p['muted'] : '#FFFFFF', 2 === $i ? 0 : 30 ), F_REG, array( '20 min · Beginner', '15 min · Hard', '25 min · All levels' )[ $i ] );
			rr( $im, $ix + 22, $cy + 94, $ix + 102, $cy + 124, 15, c( $im, '#FFFFFF', 2 === $i ? 0 : 20 ) );
			tx( $im, 11, $ix + 42, $cy + 114, c( $im, $p['ink'] ), F_SEMI, 'Start' );
		}
	} else {
		tx( $im, 22, $ix, $y + 120, c( $im, $p['ink'] ), F_BOLD, 'Progress' );
		rr( $im, $ix, $y + 150, $ix + $iw, $y + 400, 24, c( $im, $p['card'] ) );
		$bw = ( $iw - 60 ) / 7;
		foreach ( array( 0.4, 0.7, 0.5, 0.9, 0.6, 0.8, 0.95 ) as $i => $v ) {
			$bx = $ix + 20 + $i * ( $bw + 3.3 );
			rr( $im, $bx, $y + 370 - 180 * $v, $bx + $bw - 6, $y + 370, 8, c( $im, 6 === $i ? $p['accent2'] : $p['accent'], 6 === $i ? 0 : 50 ) );
		}
		foreach ( array( 'Weekly goal', 'Best streak', 'Avg. sleep' ) as $i => $label ) {
			$ry = $y + 430 + $i * 74;
			rr( $im, $ix, $ry, $ix + $iw, $ry + 60, 18, c( $im, $p['card'] ) );
			rr( $im, $ix + 14, $ry + 14, $ix + 46, $ry + 46, 10, c( $im, $p['accent'], 80 ) );
			tx( $im, 13, $ix + 60, $ry + 36, c( $im, $p['ink'] ), F_SEMI, $label );
			tx( $im, 13, $ix + $iw - 60, $ry + 36, c( $im, $p['muted'] ), F_SEMI, array( '82%', '12 d', '7.4 h' )[ $i ] );
		}
	}
	// Tab bar.
	rr( $im, $ix - 10, $y + $h - 100, $ix + $iw + 10, $y + $h - 40, 30, c( $im, $p['card'] ) );
	for ( $i = 0; $i < 4; $i++ ) {
		circle( $im, $ix + 30 + $i * ( ( $iw - 40 ) / 3 ), $y + $h - 70, 18, c( $im, 0 === $i ? $p['accent'] : $p['muted'], 0 === $i ? 0 : 70 ) );
	}
}

function layout_mobile( $p, $W, $H ) {
	$im = canvas( $W, $H, $p['bg'] );
	glow( $im, $W / 2, $H / 2, 1000, $p['accent'], 3 );
	glow( $im, $W * 0.85, 100, 500, $p['accent2'], 4 );
	$pw = 340;
	$ph = 720;
	$gap = 60;
	$x0 = ( $W - ( 3 * $pw + 2 * $gap ) ) / 2;
	phone( $im, $x0, ( $H - $ph ) / 2 + 60, $pw, $ph, $p, 1 );
	phone( $im, $x0 + 2 * ( $pw + $gap ), ( $H - $ph ) / 2 + 60, $pw, $ph, $p, 2 );
	phone( $im, $x0 + $pw + $gap, ( $H - $ph ) / 2 - 30, $pw, $ph, $p, 0 );
	return $im;
}

/** Design-system board: colours, type, buttons, inputs, a card. */
function layout_system( $p, $W, $H ) {
	$im = canvas( $W, $H, $p['bg'] );
	glow( $im, $W, 0, 700, $p['accent2'], 3 );
	$x = 110;
	rr( $im, $x, 100, $W - 110, $H - 100, 32, c( $im, $p['page'] ) );
	tx( $im, 14, $x + 60, 170, c( $im, $p['accent'] ), F_SEMI, strtoupper( $p['brand'] . ' design system' ) );
	tx( $im, 40, $x + 56, 230, c( $im, $p['ink'] ), F_BOLD, 'Components & tokens' );
	// Colours.
	$sw = array( array( $p['accent'], 'Primary' ), array( $p['accent2'], 'Accent' ), array( $p['ink'], 'Ink' ), array( $p['muted'], 'Muted' ), array( $p['card'], 'Surface' ) );
	foreach ( $sw as $i => $s ) {
		$sx = $x + 60 + $i * 150;
		rr( $im, $sx, 290, $sx + 126, 400, 20, c( $im, $s[0] ) );
		if ( 'Surface' === $s[1] ) {
			rro( $im, $sx, 290, $sx + 126, 400, 20, c( $im, $p['line'] ), c( $im, $s[0] ) );
		}
		tx( $im, 14, $sx, 432, c( $im, $p['ink'] ), F_SEMI, $s[1] );
		tx( $im, 12, $sx, 454, c( $im, $p['muted'] ), F_REG, strtoupper( $s[0] ) );
	}
	// Type.
	$tx0 = $x + 860;
	rr( $im, $tx0, 290, $W - 170, 460, 24, c( $im, $p['card'] ) );
	tx( $im, 80, $tx0 + 36, 410, c( $im, $p['ink'] ), F_BOLD, 'Aa' );
	tx( $im, 16, $tx0 + 200, 360, c( $im, $p['ink'] ), F_BOLD, 'Display / Bold' );
	tx( $im, 13, $tx0 + 200, 388, c( $im, $p['muted'] ), F_REG, 'H1 56 · H2 40 · H3 28' );
	tx( $im, 13, $tx0 + 200, 412, c( $im, $p['muted'] ), F_REG, 'Body 16 / 1.6' );
	// Buttons.
	$by = 540;
	rr( $im, $x + 60, $by, $x + 250, $by + 58, 29, c( $im, $p['accent'] ) );
	tx( $im, 15, $x + 100, $by + 36, c( $im, '#FFFFFF' ), F_SEMI, 'Primary' );
	rro( $im, $x + 270, $by, $x + 460, $by + 58, 29, c( $im, $p['ink'] ), c( $im, $p['page'] ) );
	tx( $im, 15, $x + 306, $by + 36, c( $im, $p['ink'] ), F_SEMI, 'Secondary' );
	rr( $im, $x + 480, $by, $x + 640, $by + 58, 29, c( $im, $p['card'] ) );
	tx( $im, 15, $x + 520, $by + 36, c( $im, $p['muted'] ), F_SEMI, 'Disabled' );
	// Toggle + checkbox.
	rr( $im, $x + 680, $by + 8, $x + 760, $by + 50, 21, c( $im, $p['accent'] ) );
	circle( $im, $x + 738, $by + 29, 34, c( $im, '#FFFFFF' ) );
	rr( $im, $x + 790, $by + 9, $x + 830, $by + 49, 10, c( $im, $p['accent'] ) );
	imagesetthickness( $im, 4 * S );
	imageline( $im, (int) ( ( $x + 800 ) * S ), (int) ( ( $by + 29 ) * S ), (int) ( ( $x + 808 ) * S ), (int) ( ( $by + 38 ) * S ), c( $im, '#FFFFFF' ) );
	imageline( $im, (int) ( ( $x + 808 ) * S ), (int) ( ( $by + 38 ) * S ), (int) ( ( $x + 822 ) * S ), (int) ( ( $by + 20 ) * S ), c( $im, '#FFFFFF' ) );
	imagesetthickness( $im, 1 );
	// Input.
	$iy = 640;
	tx( $im, 13, $x + 60, $iy, c( $im, $p['ink'] ), F_SEMI, 'Email address' );
	rro( $im, $x + 60, $iy + 14, $x + 640, $iy + 74, 16, c( $im, $p['accent'] ), c( $im, $p['page'] ), 3 );
	tx( $im, 15, $x + 84, $iy + 52, c( $im, $p['muted'] ), F_REG, 'you@company.com' );
	rr( $im, $x + 60, $iy + 100, $x + 640, $iy + 160, 16, c( $im, $p['card'] ) );
	tx( $im, 15, $x + 84, $iy + 138, c( $im, $p['muted'] ), F_REG, 'Search...' );
	// Sample card.
	$cx = $x + 700;
	shadow( $im, $cx, 610, $W - 170, $H - 140, 24, '#000000', 1 );
	rro( $im, $cx, 610, $W - 170, $H - 140, 24, c( $im, $p['line'] ), c( $im, $p['page'] ) );
	picture( $im, $cx + 18, 628, $W - 188, 768, 16, $p, 0 );
	tx( $im, 18, $cx + 30, 808, c( $im, $p['ink'] ), F_BOLD, $p['features'][0] );
	bar( $im, $cx + 30, 826, 300, 10, c( $im, $p['muted'], 80 ) );
	return $im;
}

/** Blog cover: big title on a coloured, shaped background. */
function layout_cover( $title, $tag, $p, $W, $H ) {
	$im = canvas( $W, $H, $p['bg'] );
	glow( $im, $W * 0.9, $H * 0.1, 900, $p['accent2'], 6 );
	glow( $im, $W * 0.05, $H, 800, $p['accent'], 7 );
	$grid = c( $im, '#FFFFFF', 118 );
	for ( $gx = 0; $gx < $W; $gx += 64 ) {
		imagefilledrectangle( $im, $gx * S, 0, $gx * S + 1, $H * S, $grid );
	}
	for ( $gy = 0; $gy < $H; $gy += 64 ) {
		imagefilledrectangle( $im, 0, $gy * S, $W * S, $gy * S + 1, $grid );
	}
	// Shapes on the right.
	rr( $im, $W - 520, 170, $W - 140, 560, 40, c( $im, '#FFFFFF', 108 ) );
	rr( $im, $W - 470, 220, $W - 190, 300, 20, c( $im, '#FFFFFF', 30 ) );
	circle( $im, $W - 330, 440, 170, c( $im, $p['accent2'] ) );
	rr( $im, $W - 640, 520, $W - 400, 720, 32, c( $im, $p['accent'] ) );
	circle( $im, $W - 520, 620, 70, c( $im, '#FFFFFF', 40 ) );
	// Tag + title.
	$tw = tw( 16, F_SEMI, strtoupper( $tag ) );
	rr( $im, 110, 150, 110 + $tw + 44, 196, 23, c( $im, '#FFFFFF', 100 ) );
	tx( $im, 16, 132, 181, c( $im, '#FFFFFF' ), F_SEMI, strtoupper( $tag ) );
	$y     = 300;
	$lines = array();
	$line  = '';
	foreach ( explode( ' ', $title ) as $word ) {
		$try = '' === $line ? $word : "$line $word";
		if ( tw( 64, F_BOLD, $try ) > 820 && '' !== $line ) {
			$lines[] = $line;
			$line    = $word;
		} else {
			$line = $try;
		}
	}
	$lines[] = $line;
	foreach ( $lines as $l ) {
		tx( $im, 64, 104, $y, c( $im, '#FFFFFF' ), F_BOLD, $l );
		$y += 92;
	}
	return $im;
}

/* ---------------- The samples ---------------- */

$base = array( 'page' => '#FFFFFF', 'chrome' => '#F2F2F5', 'urlbar' => '#FFFFFF', 'card' => '#F4F4F7', 'ink' => '#111114', 'muted' => '#6B6B76', 'line' => '#E4E4EA', 'sidebar' => '#F7F7FA' );
$dark = array( 'page' => '#111118', 'chrome' => '#1B1B24', 'urlbar' => '#24242F', 'card' => '#1C1C26', 'ink' => '#F4F4F7', 'muted' => '#8E8E9C', 'line' => '#2C2C38', 'sidebar' => '#15151D' );

$projects = array(
	'northwind' => array_merge( $base, array( 'bg' => '#1F3D2B', 'accent' => '#2F7A4D', 'accent2' => '#E9A23B', 'brand' => 'Northwind', 'url' => 'northwindcoffee.com', 'tag' => 'Freshly roasted', 'headline' => array( 'Coffee that', 'tastes like home.' ), 'cta' => 'Shop beans', 'products' => array( array( 'Ethiopia Yirga', '$18.00' ), array( 'House Blend', '$15.00' ), array( 'Colombia Huila', '$19.00' ), array( 'Decaf Night', '$16.00' ) ), 'features' => array( 'Single origin', 'Subscriptions', 'Brew guides' ), 'stat' => '+38%', 'statLabel' => 'conversion', 'layout' => 'shop' ) ),
	'pulse'     => array_merge( $dark, array( 'bg' => '#16112E', 'accent' => '#7C5CFF', 'accent2' => '#FF7A59', 'brand' => 'Pulse', 'url' => 'app.pulseanalytics.io', 'tag' => 'Analytics', 'headline' => array( 'Know your', 'numbers.' ), 'cta' => 'Start free', 'kpis' => array( array( 'Revenue', '$84.2k', '+12.4%' ), array( 'Active users', '12,480', '+8.1%' ), array( 'Churn', '1.8%', '-0.6%' ), array( 'NPS', '62', '+5' ) ), 'features' => array( 'Live dashboards', 'Smart alerts', 'Team reports' ), 'stat' => '2.3x', 'statLabel' => 'adoption', 'layout' => 'dashboard' ) ),
	'atlas'     => array_merge( $base, array( 'bg' => '#0B4F6C', 'accent' => '#0EA5E9', 'accent2' => '#F59E0B', 'brand' => 'Atlas', 'url' => 'atlastravel.co', 'tag' => 'Trips made simple', 'headline' => array( 'Plan your next', 'adventure.' ), 'cta' => 'Find trips', 'features' => array( 'Smart search', 'Flexible dates', 'Local guides' ), 'stat' => '4.9/5', 'statLabel' => 'app rating', 'layout' => 'landing' ) ),
	'bloom'     => array_merge( $base, array( 'bg' => '#FF6A3D', 'accent' => '#FF6A3D', 'accent2' => '#7C3AED', 'card' => '#FFF4EF', 'brand' => 'Bloom', 'url' => 'bloomstudio.design', 'tag' => 'Creative studio', 'headline' => array( 'Brands that', 'bloom online.' ), 'cta' => 'Our work', 'features' => array( 'Branding', 'Web design', 'Motion' ), 'stat' => '120+', 'statLabel' => 'brands launched', 'layout' => 'landing' ) ),
	'fittrack'  => array_merge( $base, array( 'bg' => '#111827', 'accent' => '#22C55E', 'accent2' => '#6366F1', 'brand' => 'FitTrack', 'url' => 'fittrack.app', 'tag' => 'Fitness', 'headline' => array( 'Move more,', 'every day.' ), 'cta' => 'Download', 'features' => array( 'Daily goals', 'Workouts', 'Progress' ), 'stat' => '50k+', 'statLabel' => 'downloads', 'layout' => 'mobile' ) ),
	'kite'      => array_merge( $base, array( 'bg' => '#E9E2D6', 'accent' => '#1E3A5F', 'accent2' => '#C08A3E', 'card' => '#F6F2EA', 'brand' => 'Kite Legal', 'url' => 'kitelegal.com', 'tag' => 'Law firm', 'headline' => array( 'Clear advice.', 'Real results.' ), 'cta' => 'Book a call', 'features' => array( 'Business law', 'Contracts', 'Disputes' ), 'stat' => '3x', 'statLabel' => 'more enquiries', 'layout' => 'landing' ) ),
);

$W = 1600;
$H = 1200;
foreach ( $projects as $key => $p ) {
	$fn = 'layout_' . $p['layout'];
	save( $fn( $p, $W, $H ), $W, $H, "$out/project-$key.png" );
	// Gallery: design-system board and a second screen.
	save( layout_system( $p, $W, 1000 ), $W, 1000, "$out/project-$key-system.png" );
	$second = 'mobile' === $p['layout'] ? 'landing' : 'mobile';
	$fn2    = 'layout_' . $second;
	save( $fn2( $p, $W, 1000 ), $W, 1000, "$out/project-$key-screens.png" );
}

// Case study covers: same brands, other layouts.
save( layout_landing( $projects['northwind'], $W, $H ), $W, $H, "$out/case-northwind.png" );
save( layout_dashboard( array_merge( $projects['pulse'], array( 'bg' => '#0E0B1F' ) ), $W, $H ), $W, $H, "$out/case-pulse.png" );

// Blog covers.
$covers = array(
	'ui-principles'     => array( '10 UI principles every developer should know', 'UI/UX', array( 'bg' => '#2A1B6E', 'accent' => '#6C4CF5', 'accent2' => '#FF6A3D' ) ),
	'fast-wordpress'    => array( 'How I make WordPress sites load in under a second', 'WordPress', array( 'bg' => '#0F3B33', 'accent' => '#10B981', 'accent2' => '#F59E0B' ) ),
	'figma-to-code'     => array( 'From Figma to code: my hand-off checklist', 'Frontend', array( 'bg' => '#3B0F2E', 'accent' => '#EC4899', 'accent2' => '#6366F1' ) ),
);
foreach ( $covers as $key => $cv ) {
	save( layout_cover( $cv[0], $cv[1], $cv[2], $W, 900 ), $W, 900, "$out/post-$key.png" );
}
