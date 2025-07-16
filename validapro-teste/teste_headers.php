<?php
if (headers_sent($file, $line)) {
    echo "Headers já enviados em $file na linha $line";
} else {
    echo "Headers ainda NÃO foram enviados.";
}
?> 