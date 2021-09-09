<?php
require('fpdf.php');

class PDF_Rotate extends FPDF
{
var $angle=0;

function Rotate($angle,$x=-1,$y=-1)
{
	if($x==-1)
		$x=$this->x;
	if($y==-1)
		$y=$this->y;
	if($this->angle!=0)
		$this->_out('Q');
	$this->angle=$angle;
	if($angle!=0)
	{
		$angle*=M_PI/180;
		$c=cos($angle);
		$s=sin($angle);
		$cx=$x*$this->k;
		$cy=($this->h-$y)*$this->k;
		$this->_out(sprintf('q %.5F %.5F %.5F %.5F %.2F %.2F cm 0.5 0 0 1 %.2F %.2F cm',$c,$s,-$s,$c,$cx,$cy,-$cx,-$cy));
	}
}

function _endpage()
{
	if($this->angle!=0)
	{
		$this->angle=0;
		$this->_out('Q');
	}
	parent::_endpage();
}
function EAN13($x, $y, $barcode, $h=20, $w=.50)
	{
		$this->Barcode($x,$y,$barcode,$h,$w,13);
	}

	function UPC_A($x, $y, $barcode, $h=20, $w=.50)
	{
		$this->Barcode($x,$y,$barcode,$h,$w,12);
	}

	function GetCheckDigit($barcode)
	{
		//Compute the check digit
		$sum=0;
		for($i=1;$i<=11;$i+=2)
			$sum+=3*$barcode[$i];
		for($i=0;$i<=10;$i+=2)
			$sum+=$barcode[$i];
		$r=$sum%10;
		if($r>0)
			$r=10-$r;
		return $r;
	}

	function TestCheckDigit($barcode)
	{
		//Test validity of check digit
		$sum=0;
		for($i=1;$i<=11;$i+=2)
			$sum+=3*$barcode[$i];
		for($i=0;$i<=10;$i+=2)
			$sum+=$barcode[$i];
		return ($sum+$barcode[12])%10==0;
	}
	function Barcode($x, $y, $barcode, $h, $w, $len)
	{
		//Padding
		$barcode=str_pad($barcode,$len-3,STR_PAD_LEFT);
		
		//Convert digits to bars
		$codes=array(
			'A'=>array(
				'0'=>'0001101','1'=>'0011001','2'=>'0010011','3'=>'0111101','4'=>'0100011',
				'5'=>'0110001','6'=>'0101111','7'=>'0111011','8'=>'0110111','9'=>'0001011'),
			'B'=>array(
				'0'=>'0100111','1'=>'0110011','2'=>'0011011','3'=>'0100001','4'=>'0011101',
				'5'=>'0111001','6'=>'0000101','7'=>'0010001','8'=>'0001001','9'=>'0010111'),
			'C'=>array(
				'0'=>'1110010','1'=>'1100110','2'=>'1101100','3'=>'1000010','4'=>'1011100',
				'5'=>'1001110','6'=>'1010000','7'=>'1000100','8'=>'1001000','9'=>'1110100')
			);
		$parities=array(
			'0'=>array('A','A','A','A','A','A'),
			'1'=>array('A','A','B','A','B','B'),
			'2'=>array('A','A','B','B','A','B'),
			'3'=>array('A','A','B','B','B','A'),
			'4'=>array('A','B','A','A','B','B'),
			'5'=>array('A','B','B','A','A','B'),
			'6'=>array('A','B','B','B','A','A'),
			'7'=>array('A','B','A','B','A','B'),
			'8'=>array('A','B','A','B','B','A'),
			'9'=>array('A','B','B','A','B','A')
			);
		$code='101';
		$p=$parities[$barcode[0]];
		for($i=1;$i<=6;$i++)
			$code.=$codes[$p[$i-1]][$barcode[$i]];
		$code.='01010';
		for($i=7;$i<=9;$i++)
			$code.=$codes['C'][$barcode[$i]];
		$code.='101';
		//Draw bars
		for($i=0;$i<strlen($code);$i++)
		{
			if($code[$i]=='1')
				$this->Rect($x+$i*$w,$y,$w,$h,'F');
		}
		//Print text uder barcode
		$this->SetFont('Arial','B',12);
		$this->Text($x,$y+$h+11/$this->k,substr($barcode,-$len));
	}
}

?>
