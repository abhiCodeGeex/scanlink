<?php	 		 	
/*
 * PHP QR Code encoder
 *
 * Image output of code using GD2
 *
 * PHP QR Code is distributed under LGPL 3
 * Copyright (C) 2010 Dominik Dzienia <deltalab at poczta dot fm>
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 */

define ( 'QR_IMAGE', true );

class QRimage {
	
	//----------------------------------------------------------------------
	public static function png($frame, $filename = false, $pixelPerPoint = 4, $outerFrame = 4, $saveandprint = FALSE, $bg, $fg,$cb_top_left_ext_color,$cb_top_left_int_color,$cb_top_right_ext_color,$cb_top_right_int_color,$cb_bottom_left_ext_color,$cb_bottom_left_int_color,$block_style) {
		$image = self::image ( $frame, $pixelPerPoint, $outerFrame, $bg, $fg, $cb_top_left_ext_color,$cb_top_left_int_color,$cb_top_right_ext_color,$cb_top_right_int_color,$cb_bottom_left_ext_color,$cb_bottom_left_int_color,$block_style,$filename );
		
		if ($filename === false) {
			Header ( "Content-type: image/png" );
			ImagePng ( $image );
		} else {
			if ($saveandprint === TRUE) {
				ImagePng ( $image, $filename );
				header ( "Content-type: image/png" );
				ImagePng ( $image );
			} else {
				ImagePng ( $image, $filename );
			}
		}
		
		ImageDestroy ( $image );
	}
	
	//----------------------------------------------------------------------
	public static function jpg($frame, $filename = false, $pixelPerPoint = 8, $outerFrame = 4, $q = 85) {
		$image = self::image ( $frame, $pixelPerPoint, $outerFrame );
		
		if ($filename === false) {
			Header ( "Content-type: image/jpeg" );
			ImageJpeg ( $image, null, $q );
		} else {
			ImageJpeg ( $image, $filename, $q );
		}
		
		ImageDestroy ( $image );
	}
	
	private static function rgb2array($rgb) {
		return array (
			base_convert ( substr ( $rgb, 0, 2 ), 16, 10 ), 
			base_convert ( substr ( $rgb, 2, 2 ), 16, 10 ), 
			base_convert ( substr ( $rgb, 4, 2 ), 16, 10 ) 
			);
	}
	//----------------------------------------------------------------------
	private static function image($frame, $pixelPerPoint = 4, $outerFrame = 4, $bg, $fg,$cb_top_left_ext_color,$cb_top_left_int_color,$cb_top_right_ext_color,$cb_top_right_int_color,$cb_bottom_left_ext_color,$cb_bottom_left_int_color,$block_style,$filename) {
		
		$h = count ( $frame );
		$w = strlen ( $frame [0] );
		
		//$pixelPerPoint = 22;
		$imgW = $w + 2 * $outerFrame;
		$imgH = $h + 2 * $outerFrame;
		
		
		if($block_style != "circle")
		{
			$base_image = ImageCreate ( $imgW, $imgH );
		}
		else
		{
			$base_image = ImageCreateTrueColor ( $imgW * $pixelPerPoint, $imgH * $pixelPerPoint );		
		}
		
		
		$bg_rgb = QRimage::rgb2array($bg);
		$bg_red = $bg_rgb[0];
		$bg_green = $bg_rgb[1];
		$bg_blue = $bg_rgb[2];
		
		
		$fg_rgb = QRimage::rgb2array($fg);
		$fg_red = $fg_rgb[0];
		$fg_green = $fg_rgb[1];
		$fg_blue = $fg_rgb[2];
		
		/**
		 * Edited By Piyush Parmar 
		 * A place to set Color of corner boxes of QR code image
		 * **/
		 
		///// Top left external box color ///////////////////////////////////////////////
		if($cb_top_left_ext_color != "")
		{
			$cb_top_left_ext_rgb = QRimage::rgb2array($cb_top_left_ext_color);
			$cb_top_left_ext_red = $cb_top_left_ext_rgb[0];
			$cb_top_left_ext_green = $cb_top_left_ext_rgb[1];
			$cb_top_left_ext_blue = $cb_top_left_ext_rgb[2];
		}
		else
		{
			$cb_top_left_ext_red = $fg_rgb[0];
			$cb_top_left_ext_green = $fg_rgb[1];
			$cb_top_left_ext_blue = $fg_rgb[2];
		}
		
		////////////////////////////////////////////////////////////////////////////////
		
		///// Top left internal box color ///////////////////////////////////////////////
		if($cb_top_left_int_color != "")
		{
			$cb_top_left_int_rgb = QRimage::rgb2array($cb_top_left_int_color);
			$cb_top_left_int_red = $cb_top_left_int_rgb[0];
			$cb_top_left_int_green = $cb_top_left_int_rgb[1];
			$cb_top_left_int_blue = $cb_top_left_int_rgb[2];
		}
		else
		{
			$cb_top_left_int_red = $fg_rgb[0];
			$cb_top_left_int_green = $fg_rgb[1];
			$cb_top_left_int_blue = $fg_rgb[2];
		}
		////////////////////////////////////////////////////////////////////////////////
		
		
		///// Top right external box color ///////////////////////////////////////////////
		if($cb_top_right_ext_color != "")
		{
			$cb_top_right_ext_rgb = QRimage::rgb2array($cb_top_right_ext_color);
			$cb_top_right_ext_red = $cb_top_right_ext_rgb[0];
			$cb_top_right_ext_green = $cb_top_right_ext_rgb[1];
			$cb_top_right_ext_blue = $cb_top_right_ext_rgb[2];
		}
		else
		{
			$cb_top_right_ext_red = $fg_rgb[0];
			$cb_top_right_ext_green = $fg_rgb[1];
			$cb_top_right_ext_blue = $fg_rgb[2];
		}
		////////////////////////////////////////////////////////////////////////////////
		
		
		///// Top right internal box color ///////////////////////////////////////////////
		if($cb_top_right_int_color != "")
		{
			$cb_top_right_int_rgb = QRimage::rgb2array($cb_top_right_int_color);
			$cb_top_right_int_red = $cb_top_right_int_rgb[0];
			$cb_top_right_int_green = $cb_top_right_int_rgb[1];
			$cb_top_right_int_blue = $cb_top_right_int_rgb[2];
		}
		else
		{
			$cb_top_right_int_red = $fg_rgb[0];
			$cb_top_right_int_green = $fg_rgb[1];
			$cb_top_right_int_blue = $fg_rgb[2];
		}
		////////////////////////////////////////////////////////////////////////////////
		
		
		///// Bottom left external box color ///////////////////////////////////////////////
		if($cb_bottom_left_ext_color != "")
		{
			$cb_bottom_left_ext_rgb = QRimage::rgb2array($cb_bottom_left_ext_color);
			$cb_bottom_left_ext_red = $cb_bottom_left_ext_rgb[0];
			$cb_bottom_left_ext_green = $cb_bottom_left_ext_rgb[1];
			$cb_bottom_left_ext_blue = $cb_bottom_left_ext_rgb[2];
		}
		else
		{
			$cb_bottom_left_ext_red = $fg_rgb[0];
			$cb_bottom_left_ext_green = $fg_rgb[1];
			$cb_bottom_left_ext_blue = $fg_rgb[2];
		}
		////////////////////////////////////////////////////////////////////////////////
		
		
		///// Bottom left internal box color ///////////////////////////////////////////////
		if($cb_bottom_left_int_color != "")
		{
			$cb_bottom_left_int_rgb = QRimage::rgb2array($cb_bottom_left_int_color);
			$cb_bottom_left_int_red = $cb_bottom_left_int_rgb[0];
			$cb_bottom_left_int_green = $cb_bottom_left_int_rgb[1];
			$cb_bottom_left_int_blue = $cb_bottom_left_int_rgb[2];
		}
		else
		{
			$cb_bottom_left_int_red = $fg_rgb[0];
			$cb_bottom_left_int_green = $fg_rgb[1];
			$cb_bottom_left_int_blue = $fg_rgb[2];
		}
		
		/**
		 * Edited By Paresh Rathod 
		 * A place to set Color of QR code image
		 * $col[0] = background color
		 * $col[1] = fore color
		 * **/
		$col [0] = ImageColorAllocate ( $base_image, $bg_red, $bg_green, $bg_blue );
		$col [1] = ImageColorAllocate ( $base_image, $fg_red, $fg_green, $fg_blue );
		
		$cb_top_left_ext = ImageColorAllocate ( $base_image, $cb_top_left_ext_red, $cb_top_left_ext_green, $cb_top_left_ext_blue );
		$cb_top_left_int = ImageColorAllocate ( $base_image, $cb_top_left_int_red, $cb_top_left_int_green, $cb_top_left_int_blue );
		
		$cb_top_right_ext = ImageColorAllocate ( $base_image, $cb_top_right_ext_red, $cb_top_right_ext_green, $cb_top_right_ext_blue );
		$cb_top_right_int = ImageColorAllocate ( $base_image, $cb_top_right_int_red, $cb_top_right_int_green, $cb_top_right_int_blue );
		
		$cb_bottom_left_ext = ImageColorAllocate ( $base_image, $cb_bottom_left_ext_red, $cb_bottom_left_ext_green, $cb_bottom_left_ext_blue );
		$cb_bottom_left_int = ImageColorAllocate ( $base_image, $cb_bottom_left_int_red, $cb_bottom_left_int_green, $cb_bottom_left_int_blue );
		//print_r($frame);exit;
		imagefill ( $base_image, 0, 0, $col [0] );
		
		$total_plotting = count($frame);
		if($block_style != "circle")
		{
			
			for($y = 0; $y < $h; $y ++) {
				for($x = 0; $x < $w; $x ++) {
					if ($frame [$y] [$x] == '1') {
						if($y<=6)
						{
							if($x<=6)
							{
								if($y==0 && $x<=6)
								{
									ImageSetPixel ( $base_image, $x + $outerFrame, $y + $outerFrame, $cb_top_left_ext );
								}
								elseif($y>0 && $y<6)
								{
									
									if($x==0 || $x==6)
									{
										ImageSetPixel ( $base_image, $x + $outerFrame, $y + $outerFrame, $cb_top_left_ext );
									}
									else
									{
										ImageSetPixel ( $base_image, $x + $outerFrame, $y + $outerFrame, $cb_top_left_int );
									}
								}
								elseif($y==6 && $x<=6)
								{
									ImageSetPixel ( $base_image, $x + $outerFrame, $y + $outerFrame, $cb_top_left_ext );
								}
								
							}
							elseif($x>=($total_plotting-7))
							{
								
								if($y==0 && $x<=$total_plotting)
								{
									ImageSetPixel ( $base_image, $x + $outerFrame, $y + $outerFrame, $cb_top_right_ext );
								}
								elseif($y>0 && $y<6)
								{
									if($x==($total_plotting-7) || $x==($total_plotting-1))
									{
										ImageSetPixel ( $base_image, $x + $outerFrame, $y + $outerFrame, $cb_top_right_ext );
									}
									else
									{
										ImageSetPixel ( $base_image, $x + $outerFrame, $y + $outerFrame, $cb_top_right_int );
									}
								}
								elseif($y==6 && ($x>=($total_plotting-7) || $x<=$total_plotting))
								{
									ImageSetPixel ( $base_image, $x + $outerFrame, $y + $outerFrame, $cb_top_right_ext );
								}
									
							}
							else
							{
								ImageSetPixel ( $base_image, $x + $outerFrame, $y + $outerFrame, $col [1] );
							}
						}
						elseif($y>=($total_plotting-7))
						{
							if($x<=6)
							{
								if($y==($total_plotting-7) && $x<=6)
								{
									ImageSetPixel ( $base_image, $x + $outerFrame, $y + $outerFrame, $cb_bottom_left_ext );
								}
								elseif($y>($total_plotting-7) && $y<($total_plotting-1))
								{
									if($x==0 || $x==6)
									{
										ImageSetPixel ( $base_image, $x + $outerFrame, $y + $outerFrame, $cb_bottom_left_ext );
									}
									else
									{
										ImageSetPixel ( $base_image, $x + $outerFrame, $y + $outerFrame, $cb_bottom_left_int );
									}
								}
								elseif($y==($total_plotting-1) && $x<=6)
								{
									ImageSetPixel ( $base_image, $x + $outerFrame, $y + $outerFrame, $cb_bottom_left_ext );
								}
								
							}
							else
							{
								ImageSetPixel ( $base_image, $x + $outerFrame, $y + $outerFrame, $col [1] );	
							}
						}
						else
						{
							ImageSetPixel ( $base_image, $x + $outerFrame, $y + $outerFrame, $col [1] );
						}
					}
				}
			}
			
			$target_image = ImageCreateTrueColor ( $imgW * $pixelPerPoint, $imgH * $pixelPerPoint );	
			ImageCopyResized ( $target_image, $base_image, 0, 0, 0, 0, $imgW * $pixelPerPoint, $imgH * $pixelPerPoint, $imgW, $imgH );
			ImageDestroy ( $base_image );
			return $target_image;
		}
		else
		{
				for($y = 0; $y < $h; $y ++) {
				for($x = 0; $x < $w; $x ++) {
					if ($frame [$y] [$x] == '1') {
						if($x<=6)
						{
							if($y<=6)
							{
								if($x==0 && $y<=6)
								{
									//ImageFilledEllipse($base_image, ($y+2)*$pixelPerPoint+5, ($x+2)*$pixelPerPoint+5, $pixelPerPoint+2, $pixelPerPoint+2, $cb_top_left_ext);
									ImageFilledEllipse($base_image, ($y+$outerFrame)*$pixelPerPoint+5, ($x+$outerFrame)*$pixelPerPoint+5, $pixelPerPoint+2, $pixelPerPoint+2, $cb_top_left_ext);
								}
								elseif($x>0 && $y<6)
								{
									
									if($y==0 || $x==6)
									{
										ImageFilledEllipse($base_image, ($y+$outerFrame)*$pixelPerPoint+5, ($x+$outerFrame)*$pixelPerPoint+5, $pixelPerPoint+2, $pixelPerPoint+2, $cb_top_left_ext);
									}
									else
									{
										ImageFilledEllipse($base_image, ($y+$outerFrame)*$pixelPerPoint+5, ($x+$outerFrame)*$pixelPerPoint+5, $pixelPerPoint+2, $pixelPerPoint+2, $cb_top_left_int);
									}
								}
								elseif($x<=6 && $y<=6)
								{
									
									ImageFilledEllipse($base_image, ($y+$outerFrame)*$pixelPerPoint+5, ($x+$outerFrame)*$pixelPerPoint+5, $pixelPerPoint+2, $pixelPerPoint+2, $cb_top_left_ext);
								}
								
							}
							elseif($y>=($total_plotting-7))
							{
							
								if($x==0 && $y<=$total_plotting)
								{
									ImageFilledEllipse($base_image, ($y+$outerFrame)*$pixelPerPoint+5, ($x+$outerFrame)*$pixelPerPoint+5, $pixelPerPoint+2, $pixelPerPoint+2, $cb_top_right_ext);
								}
								elseif($x>0 && $x<6)
								{
									if($y==($total_plotting-7) || $y==($total_plotting-1))
									{
										ImageFilledEllipse($base_image, ($y+$outerFrame)*$pixelPerPoint+5, ($x+$outerFrame)*$pixelPerPoint+5, $pixelPerPoint+2, $pixelPerPoint+2, $cb_top_right_ext);
									}
									else
									{
										ImageFilledEllipse($base_image, ($y+$outerFrame)*$pixelPerPoint+5, ($x+$outerFrame)*$pixelPerPoint+5, $pixelPerPoint+2, $pixelPerPoint+2, $cb_top_right_int);
									}
								}
								elseif($x==6)
								{
									ImageFilledEllipse($base_image, ($y+$outerFrame)*$pixelPerPoint+5, ($x+$outerFrame)*$pixelPerPoint+5, $pixelPerPoint+2, $pixelPerPoint+2, $cb_top_right_ext);
								}
									
							}
							else
							{
								ImageFilledEllipse($base_image, ($y+$outerFrame)*$pixelPerPoint+5, ($x+$outerFrame)*$pixelPerPoint+5, $pixelPerPoint+2, $pixelPerPoint+2, $col [1]);
							}
						}
						elseif($x>=($total_plotting-7))
						{	
							if($y<=6)
							{
								if($x==($total_plotting-7) && $y<=6)
								{
									ImageFilledEllipse($base_image, ($y+$outerFrame)*$pixelPerPoint+5, ($x+$outerFrame)*$pixelPerPoint+5, $pixelPerPoint+2, $pixelPerPoint+2, $cb_bottom_left_ext);
								}
								elseif($x>($total_plotting-7) && $x<($total_plotting-1))
								{
									if($y==0 || $y==6)
									{
										ImageFilledEllipse($base_image, ($y+$outerFrame)*$pixelPerPoint+5, ($x+$outerFrame)*$pixelPerPoint+5, $pixelPerPoint+2, $pixelPerPoint+2, $cb_bottom_left_ext);
									}
									else
									{
										ImageFilledEllipse($base_image, ($y+$outerFrame)*$pixelPerPoint+5, ($x+$outerFrame)*$pixelPerPoint+5, $pixelPerPoint+2, $pixelPerPoint+2, $cb_bottom_left_int);
									}
								}
								elseif($x==($total_plotting-1) && $y<=6)
								{
									ImageFilledEllipse($base_image, ($y+$outerFrame)*$pixelPerPoint+5, ($x+$outerFrame)*$pixelPerPoint+5, $pixelPerPoint+2, $pixelPerPoint+2, $cb_bottom_left_ext);				
								}
								
							}
							else
							{
								ImageFilledEllipse($base_image, ($y+$outerFrame)*$pixelPerPoint+5, ($x+$outerFrame)*$pixelPerPoint+5, $pixelPerPoint+2, $pixelPerPoint+2, $col [1]);
							}
						}
						else
						{
							ImageFilledEllipse($base_image, ($y+$outerFrame)*$pixelPerPoint+5, ($x+$outerFrame)*$pixelPerPoint+5, $pixelPerPoint+2, $pixelPerPoint+2, $col [1]);
						}
					}
				}
			}
			return $base_image;
		}
	}
}