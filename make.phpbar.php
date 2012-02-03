<?php

if ( isset( $_GET["percent"] ) && is_numeric( $_GET["percent"] ) )
{
	$percent = ( $_GET["percent"] <= 100 ? $_GET["percent"] : 100 );
}
else
{
	$percent = 0;
}

if ( isset( $_GET["total_stages"] ) && is_numeric( $_GET["total_stages"] ) && $_GET["total_stages"] > 0 )
{
	$total_stages = $_GET["total_stages"];
}
else
{
	$total_stages = 1;
}

if ( isset( $_GET["cur_stage"] ) && is_numeric( $_GET["cur_stage"] ) && $_GET["cur_stage"] > 0 )
{
	$cur_stage = ( $_GET["cur_stage"] <= $total_stages ? $_GET["cur_stage"] : $total_stages );
}
else
{
	$cur_stage = 1;
}

$timings = FALSE;
if ( isset( $_GET["timings"] ) )
{
	if ( !isset( $_SESSION ) )
	{
		session_start();
	}
	
	if ( !isset( $_SESSION["phpbar_timings"] ) || !is_array( $_SESSION["phpbar_timings"] ) || isset( $_GET["init"] ) )
	{
		$_SESSION["phpbar_timings"] = array();
	}
	
	$_SESSION["phpbar_timings"][] = array( "start" => NULL, "end" => NULL );
	
	$timings = TRUE;
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

if ( $timings == TRUE )
{
	$_SESSION["phpbar_timings"][count( $_SESSION["phpbar_timings"] ) - 1]["start"] = microtime( TRUE );
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

/* If the progress bar is divided into multiple stages, convert the percentage accordingly.  --Kris */
$base_percent = ($cur_stage - 1) / $total_stages;

if ( ($base_percent + $percent) > 0 )
{
	$startx = 1;
	$starty = 1;
	
	$currentx = $startx;
	$end = imagesx( $progress_bar ) - 1;
	
	$total = $end - $startx - imagesx( $progress_bar_fill_left ) - imagesx( $progress_bar_fill_right );
	
	/* The maximum amount that can be filled within each stage.  --Kris */
	$total /= $total_stages;
	
	imagecopy( $progress_bar, $progress_bar_fill_left, $startx, $starty, 0, 0, imagesx( $progress_bar_fill_left ), imagesy( $progress_bar_fill_left ) );
	
	$currentx += imagesx( $progress_bar_fill_left );
	for ( $stageloop = 1; $stageloop <= $cur_stage; $stageloop++ )
	{
		$stage_percent = ( $stageloop < $cur_stage ? 100 : $percent );
		
		$fillto = round( $total * ($stage_percent / 100) );
		for ( $fill = 1; $fill <= $fillto; $fill++ )
		{
			imagecopy( $progress_bar, $progress_bar_fill, $currentx, $starty, 0, 0, imagesx( $progress_bar_fill ), imagesy( $progress_bar_fill ) );
			
			$currentx++;
		}
	}
	
	imagecopy( $progress_bar, $progress_bar_fill_right, $currentx, $starty, 0, 0, imagesx( $progress_bar_fill_right ), imagesy( $progress_bar_fill_right ) );
}

/* Draw the stage divider lines.  --Kris */
if ( $total_stages > 1 )
{
	$startx = 1;
	$starty = 1;
	
	$currentx = $startx;
	$end = imagesx( $progress_bar ) - 1;
	
	$stage_divider = imagecolorallocate( $progress_bar, 190, 190, 0 );
	$total = $end - $startx - imagesx( $progress_bar_fill_left ) - imagesx( $progress_bar_fill_right );
	
	/* If specified, move the goal posts to the right so that the tip of the fill touches it at 100%.  --Kris */
	if ( isset( $_GET["modstages"] ) && $_GET["modstages"] == 1 )
	{
		$mod = imagesx( $progress_bar_fill_right );
	}
	else
	{
		$mod = 0;
	}
	
	$currentx += imagesx( $progress_bar_fill_left );
	for ( $stageloop = 1; $stageloop < $total_stages; $stageloop++ )
	{
		$line_x = round( $stageloop * ($total / $total_stages) ) + $mod;
		
		imageline( $progress_bar, $line_x, $starty, $line_x, $starty + imagesy( $progress_bar_fill ) - 1, $stage_divider );
	}
}

imagepng( $progress_bar );

if ( $timings == TRUE )
{
	$_SESSION["phpbar_timings"][count( $_SESSION["phpbar_timings"] ) - 1]["end"] = microtime( TRUE );
}
