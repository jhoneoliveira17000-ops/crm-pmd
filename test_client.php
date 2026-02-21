<?php
$url = 'http://localhost:8000/api/clientes.php';
$data = [
    'nome_empresa' => 'Curl Test Client',
    'valor_mensal' => 500,
    'dia_vencimento' => 15,
    'link_pasta' => 'https://curl-test.com',
    'status_contrato' => 'ativo'
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
// Mock session if needed or just rely on the fact we are on localhost
// Wait, api/clientes.php requires login.
// I need to cookie file or something?
// Or I can just bypass auth for the test if I comment it out? 
// No, I should use the session. 
// Actually, I can just use a PHP script that includes db and does the insert to check syntax.
// Or better: Use the browser subagent to run a Fetch from the console, which has the session.
?>
<script>
    fetch('api/clientes.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'nome_empresa=JS_Test&valor_mensal=100&dia_vencimento=5&link_pasta=http://test.com'
    }).then(r => r.text()).then(console.log).catch(console.error);
</script>
