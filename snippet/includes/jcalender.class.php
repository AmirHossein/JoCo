<?php
//--------------------------------------------
//Class Name : Persian Calendar Class
//Published Date: 20 Apr 2005
//Author Email : haghparast@gmail.com
//License : Freeware
//
// Extended and modified by AHHP (Boplo.ir)
// 1387/07/25	1:42 am
//--------------------------------------------
class Calendar
{
	var $year, $month, $day;
	var $monthName;
	
	function Calendar($type=false, $stamp=false)
	{
		if($type)
		{
			$stamp = ($stamp) ? $stamp : time();
			list($gYear, $gMonth, $gDay) = explode("-", date("Y-m-d", $stamp));
			list($this->year, $this->month, $this->day) = $this->gregorian_to_jalali($gYear,$gMonth,$gDay);
			$this->monthName = $this->ReturnMonthName($this->month);
		}
	}
	
	function __constructor($type=false, $stamp=false)
	{
		return $this->Calender($type, $stamp);
	}

	
	function ReturnMonthName($monname)
	{
		switch ($monname)
		{
			case 1:	return "فروردین";	break;
			case 2:	return "اردیبهشت";	break;
			case 3:	return "خرداد";	break;
			case 4:	return "تير";	break;
			case 5:	return "مرداد";	break;
			case 6:	return "شهریور";	break;
			case 7:	return "مهر";	break;
			case 8:	return "آبان";	break;
			case 9:	return "آذر";	break;
			case 10:	return "دى";	break;
			case 11:	return "بهمن";	break;
			case 12:	return "اسفند";	break;
		}
	}

	
	function div($a,$b)
	{
		return (int) ($a / $b);
	}
	
	
// Thanks to Roozbeh Pournader and Mohammad Toosi for their Date Conversion program
	function gregorian_to_jalali ($g_y, $g_m, $g_d)
	{
		$g_days_in_month = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
		$j_days_in_month = array(31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29);
		$gy = $g_y-1600;
		$gm = $g_m-1;
		$gd = $g_d-1;
		$g_day_no = 365*$gy+$this->div($gy+3,4)-$this->div($gy+99,100)+$this->div($gy+399,400);

		for ($i=0; $i < $gm; ++$i)
			$g_day_no += $g_days_in_month[$i];
		
		if ($gm>1 && (($gy%4==0 && $gy%100!=0) || ($gy%400==0)))
			$g_day_no++; /* leap and after Feb */
		
		$g_day_no += $gd;
		$j_day_no = $g_day_no-79;
		$j_np = $this->div($j_day_no, 12053); /* 12053 = 365*33 + 32/4 */
		$j_day_no = $j_day_no % 12053;
		$jy = 979+33*$j_np+4*$this->div($j_day_no,1461); /* 1461 = 365*4 + 4/4 */
		$j_day_no %= 1461;
		
		if($j_day_no >= 366)
		{
			$jy += $this->div($j_day_no-1, 365);
			$j_day_no = ($j_day_no-1)%365;
		}
		
		for($i = 0; $i < 11 && $j_day_no >= $j_days_in_month[$i]; ++$i)
			$j_day_no -= $j_days_in_month[$i];
		
		$jm = $i+1;
		$jd = $j_day_no+1;
		
		return array($jy, $jm, $jd);
	}

	
	function jalali_to_gregorian($j_y, $j_m, $j_d)
	{
		$g_days_in_month = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
		$j_days_in_month = array(31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29);
		$jy = $j_y-979;
		$jm = $j_m-1;
		$jd = $j_d-1;
		$j_day_no = 365*$jy + $this->div($jy, 33)*8 + $this->div($jy%33+3, 4);
		
		for ($i=0; $i < $jm; ++$i)
			$j_day_no += $j_days_in_month[$i];
		
		$j_day_no += $jd;
		$g_day_no = $j_day_no+79;
		$gy = 1600 + 400*$this->div($g_day_no, 146097); /* 146097 = 365*400 + 400/4 - 400/100 + 400/400 */
		$g_day_no = $g_day_no % 146097;
		$leap = true;
		
		if ($g_day_no >= 36525) /* 36525 = 365*100 + 100/4 */
		{
			$g_day_no--;
			$gy += 100*$this->div($g_day_no, 36524); /* 36524 = 365*100 + 100/4 - 100/100 */
			$g_day_no = $g_day_no % 36524;
			if ($g_day_no >= 365)
				$g_day_no++;
			else
			$leap = false;
		}
		
		$gy += 4*$this->div($g_day_no, 1461); /* 1461 = 365*4 + 4/4 */
		$g_day_no %= 1461;
		
		if ($g_day_no >= 366)
		{
			$leap = false;
			$g_day_no--;
			$gy += $this->div($g_day_no, 365);
			$g_day_no = $g_day_no % 365;
		}

		for ($i = 0; $g_day_no >= $g_days_in_month[$i] + ($i == 1 && $leap); $i++)
			$g_day_no -= $g_days_in_month[$i] + ($i == 1 && $leap);
		
		$gm = $i+1;
		$gd = $g_day_no+1;
		
		return array($gy, $gm, $gd);
	}
}
?>