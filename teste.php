<?php
file_put_contents(__DIR__ . '/teste_webhook.log', "[" . date('Y-m-d H:i:s') . "] Teste\n", FILE_APPEND);
echo "OK";
