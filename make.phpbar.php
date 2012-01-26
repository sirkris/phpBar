<?php

if ( isset( $_GET["percent"] ) && is_numeric( $_GET["percent"] ) )
{
	$percent = ( $_GET["percent"] <= 100 ? $_GET["percent"] : 100 );
}
else
{
	$percent = 0;
}

function LoadPNG( $filename )
{
	$im = @imagecreatefrompng( $filename );
	
	if ( !( $im ) )
	{
		$im = imagecreatetruecolor( 30, 30 );
		$bg = imagecolorallocate( $im, 255, 255, 255 );
		$tc = imagecolorallocate( $im, 255, 0, 0 );
		
		imagefilledrectangle( $im, 0, 0, 30, 30, $bg );
		
		imagestring( $im, 1, 5, 5, "X", $tc );
	}
	
	return $im;
}

header( "Content-Type: image/png" );

/* Load the progress bar pieces.  --Kris */
$progress_bar = LoadPNG( "progress_bar_empty.png" );
$progress_bar_fill = LoadPNG( "progress_bar_fill.png" );
$progress_bar_fill_left = LoadPNG( "progress_bar_fill_left.png" );
$progress_bar_fill_right = LoadPNG( "progress_bar_fill_right.png" );

/* Make the image background transparent.  --Kris */
$bg = imagecolorallocate( $progress_bar, 254, 254, 254 );
imagecolortransparent( $progress_bar, $bg );

if ( $percent > 0 )
{
	$startx = 1;
	$starty = 1;
	
	$currentx = $startx;
	$end = imagesx( $progress_bar ) - 1;
	
	$total = $end - $startx - imagesx( $progress_bar_fill_left ) - imagesx( $progress_bar_fill_right );
	
	imagecopy( $progress_bar, $progress_bar_fill_left, $startx, $starty, 0, 0, imagesx( $progress_bar_fill_left ), imagesy( $progress_bar_fill_left ) );
	
	$currentx += imagesx( $progress_bar_fill_left );
	
	$fillto = round( $total * ($percent / 100) );
	for ( $fill = 1; $fill <= $fillto; $fill++ )
	{
		imagecopy( $progress_bar, $progress_bar_fill, $currentx, $starty, 0, 0, imagesx( $progress_bar_fill ), imagesy( $progress_bar_fill ) );
		
		$currentx++;
	}
	
	imagecopy( $progress_bar, $progress_bar_fill_right, $currentx, $starty, 0, 0, imagesx( $progress_bar_fill_right ), imagesy( $progress_bar_fill_right ) );
}

imagepng( $progress_bar );
