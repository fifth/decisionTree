<?php
	$fileName = $_REQUEST['sample'];
	$fileHandle = fopen('./sample/'.$fileName.'.name','r');
	$dataName = explode(',', trim(fgets($fileHandle)));
	$decision = array_search(trim(fgets($fileHandle)), $dataName);
	fclose($fileHandle);
	$fileHandle = fopen('./sample/'.$fileName.'.data','r');
	while (!feof($fileHandle)) {
		$dataSet[] = explode(',',trim(fgets($fileHandle)));
	}
	fclose($fileHandle);

	function entropy($arr) {
		if (count($arr)==0) {
			return 0;
		}
		foreach ($arr as $key => $value) {
			$tmp[$GLOBALS['dataSet'][$value][$GLOBALS['decision']]]++;
		}
		$result = 0;
		foreach ($tmp as $key => $value) {
			$result += -($value / count($arr)) * log($value / count($arr), 2);
		}
		return $result;
	}

	function gain($arr, $attr) {
		$result = entropy($arr);
		foreach ($arr as $key => $value) {
			$tmp[$GLOBALS['dataSet'][$value][$attr]][] = $value	;
		}
		foreach ($tmp as $key => $value) {
			$result -= (count($value) / count($arr)) * entropy($value);
		}
		return $result;
	}
	function outputUnique($id){
		$flag = $GLOBALS['dataSet'][reset($id)][$GLOBALS['decision']];
		foreach ($id as $key => $value) {
			if ($GLOBALS['dataSet'][$value][$GLOBALS['decision']]!=$flag) {
				return 0;
			}
		}
		return 1;
	}
	function inputUnique($id, $name){
		foreach ($name as $key => $value) {
			$flag = $GLOBALS['dataSet'][reset($id)][$key];
			foreach ($id as $k => $v) {
				if ($GLOBALS['dataSet'][$v][$key]!=$flag) {
					return 0;
				}
			}
		}
		return 1;
	}
	function main($deep, $node, $id, $name){
		for ($i = 0; $i < $deep; $i++) { 
			echo "--------";
		}
		echo "$deep:$node";
		if ((inputUnique($id, $name))||(outputUnique($id))||(count($name) <= 1)) {
			pri($id);
		} else {
			$maxGain = 0;
			$mark = 0;
			foreach ($name as $key => $value) {
				if (gain($id, $key)>=$maxGain) {
					$mark = $key;
					$maxGain = gain($id, array_search($name[$key], $GLOBALS['dataName']));
				}
			}
			foreach ($id as $key => $value) {
				$tmp[$GLOBALS['dataSet'][$value][$mark]][] = $value;
			}
			echo "<br>";
			for ($i = 0; $i < $deep; $i++) { 
				echo "--------";
			}
			echo "/".$GLOBALS['dataName'][$mark];
			foreach ($tmp as $key => $value) {	
				// var_dump($tmp);
				echo "<br>";
				main($deep+1, $key, $value, array_diff_key($name, array_flip(array($mark))));
				
			}
		}
	}
	function pri($id){
		echo "(";
		foreach ($id as $key => $value) {
			$output[$GLOBALS['dataSet'][$value][$GLOBALS['decision']]]++;
		}
		foreach ($output as $key => $value) {
			echo "$key:$value;";
		}
		echo ")";
	}


	main(0, 'root', array_keys($dataSet), array_diff_key($dataName, array_flip(array($decision))));
	// main(1, 'marital-status', array(13583,19454,20500,28094,28760), array('5'=>'race', '6'=>'sex', '7'=>'native-country'));
?>