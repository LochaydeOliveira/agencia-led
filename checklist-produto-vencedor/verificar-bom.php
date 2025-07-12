<?php
echo "<h2>Verificação de Caracteres BOM e Espaços</h2>";

$files = [
    'includes/db.php',
    'includes/auth.php',
    'dashboard.php',
    'index.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $first_chars = substr($content, 0, 10);
        $hex_chars = bin2hex($first_chars);
        
        echo "<h3>Arquivo: $file</h3>";
        echo "<p>Primeiros 10 caracteres (hex): $hex_chars</p>";
        
        // Verificar BOM UTF-8
        if (substr($content, 0, 3) === "\xEF\xBB\xBF") {
            echo "<p style='color: red;'>❌ BOM UTF-8 detectado!</p>";
        } else {
            echo "<p style='color: green;'>✅ Sem BOM UTF-8</p>";
        }
        
        // Verificar se começa com <?php
        if (strpos(trim($content), '<?php') === 0) {
            echo "<p style='color: green;'>✅ Começa corretamente com &lt;?php</p>";
        } else {
            echo "<p style='color: red;'>❌ Não começa com &lt;?php</p>";
        }
        
        // Verificar espaços antes de <?php
        $trimmed = ltrim($content);
        if (strlen($content) === strlen($trimmed)) {
            echo "<p style='color: green;'>✅ Sem espaços antes de &lt;?php</p>";
        } else {
            echo "<p style='color: red;'>❌ Espaços antes de &lt;?php detectados</p>";
        }
        
        echo "<hr>";
    } else {
        echo "<p style='color: red;'>❌ Arquivo $file não existe</p>";
    }
}

echo "<p><a href='teste-limpo.php'>← Teste Limpo</a></p>";
echo "<p><a href='dashboard.php'>← Dashboard</a></p>";
?> 