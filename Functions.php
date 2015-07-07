<?php

function var_dump_return($obj) {
	var_dump($obj);
	return $obj;
}

function array_flatten($array) {
	if (!is_array($array))
		return $ret;
	$ret = array();
	foreach ($array as $val) {
		if (is_array($val)) {
			$val = array_flatten($val);
			foreach ($val as $val2)
				array_push($ret, $val2);
		} else {
			array_push($ret, $val);
		}
	}
	return $ret;
}

function has(&$o) {
	if ($o === null)
		return false;
	if (is_array($o))
		return !empty($o);
	return true;
}

function str_startswith($str, $needle) {
	if ($needle === '')
		return true;
	return substr($str, 0, strlen($needle)) === $needle;
}

function str_endswith($str, $needle) {
	if ($needle === '')
		return true;
	return substr($str, -strlen($needle)) === $needle;
}

function is_empty($value) {
	if ($value instanceof \Countable)
		return count($value) == 0;
	return empty($value);
}

function value(&$array, $key, $or = null) {
	return array_key_exists($key, $array) ? $array[$key] : $or;
}

function longest_common_substring($string_1, $string_2) {
	$string_1_length = strlen($string_1);
	$string_2_length = strlen($string_2);
	$return          = '';

	if ($string_1_length === 0 || $string_2_length === 0)
	{
		// No similarities
		return $return;
	}

	$longest_common_subsequence = array();

	// Initialize the CSL array to assume there are no similarities
	$longest_common_subsequence = array_fill(0, $string_1_length, array_fill(0, $string_2_length, 0));

	$largest_size = 0;

	for ($i = 0; $i < $string_1_length; $i++)
	{
		for ($j = 0; $j < $string_2_length; $j++)
		{
			// Check every combination of characters
			if ($string_1[$i] === $string_2[$j])
			{
				// These are the same in both strings
				if ($i === 0 || $j === 0)
				{
					// It's the first character, so it's clearly only 1 character long
					$longest_common_subsequence[$i][$j] = 1;
				}
				else
				{
					// It's one character longer than the string from the previous character
					$longest_common_subsequence[$i][$j] = $longest_common_subsequence[$i - 1][$j - 1] + 1;
				}

				if ($longest_common_subsequence[$i][$j] > $largest_size)
				{
					// Remember this as the largest
					$largest_size = $longest_common_subsequence[$i][$j];
					// Wipe any previous results
					$return       = '';
					// And then fall through to remember this new value
				}

				if ($longest_common_subsequence[$i][$j] === $largest_size)
				{
					// Remember the largest string(s)
					$return = substr($string_1, $i - $largest_size + 1, $largest_size);
				}
			}
			// Else, $CSL should be set to 0, which it was already initialized to
		}
	}

	// Return the list of matches
	return $return;
}

function similarity($actual, $lookup, $ratioLevenshtein = 0.2, $ratioSimilarText = 0.2, $ratioLCS = 0.6) {
	$similarity1 = 0;
	$similarity2 = 0;
	$similarity3 = 0;

	if ($ratioLevenshtein != 0) {
		$similarity1 = levenshtein($actual, $lookup);
		$similarity1 /= strlen($actual);
		$similarity1 = 1 - $similarity1;
	}

	if ($ratioSimilarText != 0) {
		$similarity2 = null;
		similar_text($actual, $lookup, $similarity2);
		$similarity2 /= 100;
	}

	if ($ratioLCS)
		$similarity3 = $lookup === '' ? 1 : strlen(longest_common_substring($actual, $lookup)) / strlen($lookup);

	return $similarity1 * $ratioLevenshtein + $similarity2 * $ratioSimilarText + $similarity3 * $ratioLCS;
}

function pretty_json($json) {

    $result      = '';
    $pos         = 0;
    $strLen      = strlen($json);
    $indentStr   = '  ';
    $newLine     = "\n";
    $prevChar    = '';
    $outOfQuotes = true;

    for ($i=0; $i<=$strLen; $i++) {

        // Grab the next character in the string.
        $char = substr($json, $i, 1);

        // Are we inside a quoted string?
        if ($char == '"' && $prevChar != '\\') {
            $outOfQuotes = !$outOfQuotes;

        // If this character is the end of an element,
        // output a new line and indent the next line.
        } else if(($char == '}' || $char == ']') && $outOfQuotes) {
            $result .= $newLine;
            $pos --;
            for ($j=0; $j<$pos; $j++) {
                $result .= $indentStr;
            }
        }

        // Add the character to the result string.
        $result .= $char;

        // If the last character was the beginning of an element,
        // output a new line and indent the next line.
        if (($char == ',' || $char == '{' || $char == '[') && $outOfQuotes) {
            $result .= $newLine;
            if ($char == '{' || $char == '[') {
                $pos ++;
            }

            for ($j = 0; $j < $pos; $j++) {
                $result .= $indentStr;
            }
        }

        $prevChar = $char;
    }

    return $result;
}

?>
