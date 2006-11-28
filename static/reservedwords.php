<?php
		$typo3_reservedFields = array(
		    'uid',
			'pid',
			'endtime',
			'starttime',
			'sorting',
			'fe_group',
			'hidden',
			'deleted',
			'cruser_id',
			'crdate',
			'tstamp');
		 
		$mysql_reservedFields = array(
		    'data',
			'table',
			'field',
			'key',
			'desc');
		 
		$mysql_reservedWords = array(
		    'all',
			'and',
			'asensitive',
			'bigint',
			'both',
			'cascade',
			'char',
			'collate',
			'connection',
			'convert',
			'current_date',
			'current_user',
			'databases',
			'day_minute',
			'decimal',
			'delayed',
			'describe',
			'distinctrow',
			'drop',
			'else',
			'escaped',
			'explain',
			'float',
			'for',
			'from',
			'group',
			'hour_microsecond',
			'if',
			'index',
			'inout',
			'int',
			'int3',
			'integer',
			'is',
			'key',
			'leading',
			'like',
			'load',
			'lock',
			'longtext',
			'match',
			'mediumtext',
			'minute_second',
			'natural',
			'null',
			'optimize',
			'or',
			'outer',
			'primary',
			'raid0',
			'real',
			'release',
			'replace',
			'return',
			'rlike',
			'second_microsecond',
			'separator',
			'smallint',
			'specific',
			'sqlstate',
			'sql_cal_found_rows',
			'starting',
			'terminated',
			'tinyint',
			'trailing',
			'undo',
			'unlock',
			'usage',
			'utc_date',
			'values',
			'varcharacter',
			'where',
			'write',
			'year_month',
			'asensitive',
			'call',
			'condition',
			'connection',
			'continue',
			'cursor',
			'declare',
			'deterministic',
			'each',
			'elseif',
			'exit',
			'fetch',
			'goto',
			'inout',
			'insensitive',
			'iterate',
			'label',
			'leave',
			'loop',
			'modifies',
			'out',
			'reads',
			'release',
			'repeat',
			'return',
			'schema',
			'schemas',
			'sensitive',
			'specific',
			'sql',
			'sqlexception',
			'sqlstate',
			'sqlwarning',
			'trigger',
			'undo',
			'upgrade',
			'while');

$reservedFields = array_unique(array_merge ($typo3_reservedFields,$mysql_reservedFields,$mysql_reservedWords));

?>
