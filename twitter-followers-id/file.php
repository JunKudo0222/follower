<?php
$file = fopen('test.txt', 'a');

        fwrite($file, 'aaaaa'."\n"  );
fclose($file);