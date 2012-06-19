<?php

/**
 * Transform Eloquent models to a JSON object.
 *
 * @param  Eloquent|array  $models
 * @return object
 */
function to_array($models)
{
	if ($models instanceof Laravel\Database\Eloquent)
	{
		return $models->to_array();
	}

	return array_map(function($m) { return $m->to_array(); }, $models);
}

function single_line_array()
{
	return "array(".substr(str_replace(array("\n", "  "), array("", " "), var_export($a, true)), 8, -2).")";
}

/**
* Indents a flat JSON string to make it more human-readable.
*
* @param string $json The original JSON string to process.
* @return string Indented version of the original JSON string.
*/
function indent($json)
{
	$result = '';
	$pos = 0;
	$strLen = strlen($json);
	$indentStr = "\t";
	$newLine = "\n";

	for ($i = 0; $i < $strLen; $i++)
	{
		// Grab the next character in the string.
		$char = $json[$i];

		// Are we inside a quoted string?
		if ($char == '"')
		{
			// search for the end of the string (keeping in mind of the escape sequences)
			if ( ! preg_match('`"(\\\\\\\\|\\\\"|.)*?"`s', $json, $m, null, $i))
				return $json;

			// add extracted string to the result and move ahead
			$result .= $m[0];
			$i += strLen($m[0]) - 1;
			continue;
		}
		
		elseif ($char == '}' || $char == ']')
		{
			$result .= $newLine;
			$pos --;
			$result .= str_repeat($indentStr, $pos);
		}

		// Add the character to the result string.
		$result .= $char;

		// If the last character was the beginning of an element,
		// output a new line and indent the next line.
		if ($char == ',' || $char == '{' || $char == '[')
		{
			$result .= $newLine;
			if ($char == '{' || $char == '[')
			{
				$pos ++;
			}

			$result .= str_repeat($indentStr, $pos);
		}
	}

	return $result;
}