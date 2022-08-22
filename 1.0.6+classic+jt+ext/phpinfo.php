<?php
phpinfo();
echo exec("whoami");
chdir("/tmp");
echo getcwd()."\n";
#$p = fopen("/var/tmp/bar/foo", "w");
#fwrite($p, "bar");
#fclose($p);
?>
