<?php
// translator ready
// addnews ready
// mail ready

// A slightly higher level SQL error reporting function.
function sql_error($sql){
	return output_array(Session::record())."SQL = <pre>$sql</pre>".DB::error(LINK);
}

?>
