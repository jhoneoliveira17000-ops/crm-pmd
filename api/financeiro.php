<?php
// PMDCRM/api/financeiro.php
file_put_contents('debug_financeiro_hit.txt', date('[Y-m-d H:i:s] ') . "Hit: " . $_SERVER['REQUEST_METHOD'] . " " . file_get_contents('php://input') . "\n", FILE_APPEND);

require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/utils.php';

require_login();

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

// Utility function to get MRR for a specific period
function calculateMRR($pdo, $start, $end) {
    // Same logic as dashboard: Active contracts overlapping the period
    // Simplification: Check active contracts in that month
    $sql = "
        SELECT SUM(valor_mensal) as mrr 
        FROM clientes 
        WHERE 
            data_inicio_contrato <= ? AND ({get_tenant_condition()})
            AND (data_fim_contrato IS NULL OR data_fim_contrato >= ?)
            AND (
                status_contrato = 'ativo' 
                OR (status_contrato = 'cancelado' AND data_cancelamento >= ?)
            )
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$end, $start, $start]);
    return (float)($stmt->fetch()['mrr'] ?? 0);
}

// === DELETE ===
if ($method === 'DELETE') {
    if (empty($input['id'])) {
        json_response(['success' => false, 'error' => 'ID obrigatório'], 400);
    }
    try {
        $tenantScope = get_tenant_condition();
        $stmt = $pdo->prepare("DELETE FROM despesas WHERE id = ? AND ({$tenantScope})");
        $stmt->execute([(int)$input['id']]);
        json_response(['success' => true, 'message' => 'Despesa excluída com sucesso']);
    } catch (PDOException $e) {
        json_response(['success' => false, 'error' => 'Erro ao excluir: ' . $e->getMessage()], 500);
    }
    exit;
}

// === POST (CREATE or UPDATE) ===
if ($method === 'POST') {
    $id = $input['id'] ?? null;
    $descricao = sanitize_input($input['descricao'] ?? '');
    $categoria = sanitize_input($input['categoria'] ?? 'Outros');
    $tipo = sanitize_input($input['tipo'] ?? '');
    $valor = floatval($input['valor'] ?? 0);
    $data_despesa = $input['data_despesa'] ?? date('Y-m-d');
    $status = sanitize_input($input['status'] ?? 'pago');
    
    // Debug Log
    file_put_contents('debug_financeiro.txt', date('[Y-m-d H:i:s] ') . "Input: " . json_encode($input) . "\n", FILE_APPEND);

    // Recurrence Logic
    $recorrente = !empty($input['recorrente']);
    $parcelas = intval($input['parcelas'] ?? 1);

    if (empty($descricao) || $valor <= 0) {
        json_response(['success' => false, 'error' => 'Descrição e Valor inválidos'], 400);
    }

    try {
        if ($id) {
            // Update single existing expense
            // IMPORTANT: Doesn't update future recurrences automatically to avoid complexity
            $tenantScope = get_tenant_condition();
            $stmt = $pdo->prepare("UPDATE despesas SET descricao=?, categoria=?, tipo=?, valor=?, data_despesa=?, status=? WHERE id=? AND ({$tenantScope})");
            $stmt->execute([$descricao, $categoria, $tipo, $valor, $data_despesa, $status, $id]);
            json_response(['success' => true, 'message' => 'Despesa atualizada']);
        } else {
            // Create New
            $stmt = $pdo->prepare("INSERT INTO despesas (descricao, categoria, tipo, valor, data_despesa, status, recorrente, id_origem_recorrencia, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            // 1. Insert Initial
            $stmt->execute([$descricao, $categoria, $tipo, $valor, $data_despesa, $status, $recorrente ? 1 : 0, null, get_tenant_id()]);
            $firstId = $pdo->lastInsertId();

            // 2. Loop for Recurrence
            if ($recorrente && $parcelas > 1) {
                // Loop starts at 1 because 0 (initial) is already inserted
                for ($i = 1; $i < $parcelas; $i++) {
                    // Calculate next date
                    $nextDate = date('Y-m-d', strtotime($data_despesa . " +$i months"));
                    // Description with installment info (optional, but helpful)
                    // $desc = "$descricao (" . ($i+1) . "/$parcelas)"; 
                    // Keeping original description for cleaner grouping
                    
                    // Future expenses start as 'agendado' or 'pendente' usually, but user might want them 'pago' immediately? 
                    // Logic: If user marks current as 'pago', future ones should probably be 'agendado' or 'pendente'.
                    // Let's default future ones to 'agendado' if current is 'pago', otherwise copy status.
                    $futureStatus = ($status === 'pago') ? 'agendado' : $status;

                    $stmt->execute([$descricao, $categoria, $tipo, $valor, $nextDate, $futureStatus, 1, $firstId, get_tenant_id()]);
                }
            }

            json_response(['success' => true, 'message' => $recorrente ? "Despesa criada ($parcelas recorrências)" : 'Despesa criada', 'id' => $firstId]);
        }
    } catch (PDOException $e) {
        file_put_contents('debug_financeiro_error.txt', date('[Y-m-d H:i:s] ') . "Error: " . $e->getMessage() . "\n", FILE_APPEND);
        json_response(['success' => false, 'error' => 'Erro ao salvar: ' . $e->getMessage()], 500);
    }
    exit;
}

// === GET (LIST & METRICS) ===
if ($method === 'GET') {
    $range = $_GET['range'] ?? 'this_month';
    
    // Default to current month
    $start = date('Y-m-01');
    $end = date('Y-m-t');

    if ($range === 'today') {
        $start = date('Y-m-d');
        $end = date('Y-m-d');
    } elseif ($range === 'yesterday') {
        $start = date('Y-m-d', strtotime('-1 day'));
        $end = date('Y-m-d', strtotime('-1 day'));
    } elseif ($range === 'this_week') {
        $start = date('Y-m-d', strtotime('monday this week'));
        $end = date('Y-m-d', strtotime('sunday this week'));
    } elseif ($range === 'this_month') {
        $start = date('Y-m-01');
        $end = date('Y-m-t'); // Current month view should be just this month
    } elseif ($range === 'last_month') {
        $start = date('Y-m-01', strtotime('first day of last month'));
        $end = date('Y-m-t', strtotime('last day of last month'));
    } elseif ($range === '3months') {
        $start = date('Y-m-d', strtotime('-3 months'));
        // Include future for upcoming bills
        $end = date('Y-12-31', strtotime('+1 year')); 
    } elseif ($range === '6months') {
        $start = date('Y-m-d', strtotime('-6 months'));
        $end = date('Y-12-31', strtotime('+1 year'));
    } elseif ($range === 'this_year') {
        $start = date('Y-01-01');
        $end = date('Y-12-31');
    } elseif ($range === 'all') {
        $start = '2000-01-01';
        $end = date('Y-12-31', strtotime('+10 years'));
    }
    
    // Override if explicit params (custom filter)
    if (!empty($_GET['start'])) $start = $_GET['start'];
    if (!empty($_GET['end'])) $end = $_GET['end'];
    
    // DEBUG GET
    file_put_contents('debug_financeiro_get.txt', date('[Y-m-d H:i:s] ') . "GET Request - Range: $range, Start: $start, End: $end\n", FILE_APPEND);

    try {
        // 1. List Transactions (Despesas)
        $tenantScope = get_tenant_condition();
        $stmt = $pdo->prepare("
            SELECT * FROM despesas 
            WHERE data_despesa BETWEEN ? AND ? AND ({$tenantScope})
            ORDER BY data_despesa DESC, created_at DESC
        ");
        $stmt->execute([$start, $end]);
        $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // DEBUG RESULTS
        file_put_contents('debug_financeiro_get.txt', date('[Y-m-d H:i:s] ') . "Found: " . count($transactions) . " rows\n", FILE_APPEND);

        // 2. Metrics Calculation
        
        // 2. Metrics Calculation
        
        // Initialize
        $revenue = 0;
        $expenses = 0;
        $expensesByCategory = [];

        // Calculate from Transactions
        file_put_contents('debug_financeiro_calc.txt', date('[Y-m-d H:i:s] ') . "Starting Calculation on " . count($transactions) . " items\n", FILE_APPEND);
        
        foreach ($transactions as $t) {
            $val = (float)$t['valor'];
            $tipo = strtolower(trim($t['tipo'] ?? ''));
            
            // Log first few items
            if($val > 0) {
                // file_put_contents('debug_financeiro_calc.txt', "Item: {$t['descricao']} - Type: $tipo - Val: $val\n", FILE_APPEND);
            }

            if ($tipo === 'receita') {
                $revenue += $val;
            } elseif ($tipo === 'despesa') {
                $expenses += $val;
                
                // Category categorization
                $cat = $t['categoria'] ?? 'Outros';
                if (!isset($expensesByCategory[$cat])) $expensesByCategory[$cat] = 0;
                $expensesByCategory[$cat] += $val;
            }
        }
        
        file_put_contents('debug_financeiro_calc.txt', "Final: Rev=$revenue, Exp=$expenses\n", FILE_APPEND);

        // Add MRR from Contracts (Optional: If user wants contracts mixed with manual revenue)
        // For now, let's keep it simple: Financeiro page shows what is in 'despesas' table (Cash Flow).
        // If MRR is needed, we should add it BUT verify if it's already "paid" or just "projected".
        // Assuming 'financeiro.php' is Cash Flow (Realized), so we stick to transactions or we verify 'recorrente'.
        // The previous code called 'calculateMRR', let's see if we should keep it or if it was replacing manual revenue.
        
        // Let's ADD calculated MRR to revenue ONLY if it's not duplicating. 
        // Actually, previous code REPLACED $revenue with calculateMRR. That was the bug!
        // It ignored manual 'receita' transactions.
        
        // Let's get MRR separately if needed, but for "Fluxo de Caixa" usually we want realized transactions.
        // However, if the user hasn't generated transactions for contracts yet, they won't show up.
        // Let's check if calculateMRR function exists and what it does.
        // Recovering it just in case, but adding to manual revenue.
        $contractMRR = 0;
        if(function_exists('calculateMRR')) {
             $contractMRR = calculateMRR($pdo, $start, $end);
             // deciding whether to add it. If contracts generate transactions automatically, we shouldn't add.
             // If they don't, we should.
             // Given the user just fixed "creating transactions", likely they rely on manual entries or generated ones.
             // Safest bet: Sum everything from transactions (Realized) + MRR (Projected)? 
             // No, usually Dashboard = Realized + Projected, Financeiro = Realized.
             // BUT user complained "não contabilizando".
             // checking previous code: $revenue = calculateMRR(...);
             // This suggests it ONLY showed contract value. 
             
             // NEW LOGIC: Revenue = Transactions(receita) + Contracts(active in period) ?? 
             // To avoid double counting, usually systems generates a transaction for each contract payment.
             // If PMDCRM generates transactions for contracts, then summing $transactions is enough.
             // If not, we might miss contract value.
             // Let's assume for now we sum Transactions.
        }
        
        $profit = $revenue - $expenses;
        $margin = $revenue > 0 ? round(($profit / $revenue) * 100, 1) : 0;

        // 3. Charts Data

        // Expenses by Category
        $stmt_cat_query = "
            SELECT categoria, SUM(valor) as total 
            FROM despesas 
            WHERE data_despesa BETWEEN ? AND ? AND ({$tenantScope})
            GROUP BY categoria
        ";
        $catStmt = $pdo->prepare($stmt_cat_query);
        $catStmt->execute([$start, $end]);
        $expensesByCategory = $catStmt->fetchAll(PDO::FETCH_ASSOC);

        // Monthly Flow (Last 6 Months)
        $flowLabels = [];
        $flowRevenue = [];
        $flowExpenses = [];
        
        $history_start = date('Y-m-01', strtotime("-5 months"));
        $history_end = date('Y-m-t');

        // Buscar clientes para calcular MRR Mensal de forma otimizada
        $stmt_clientes = $pdo->prepare("SELECT data_inicio_contrato, data_fim_contrato, status_contrato, data_cancelamento, valor_mensal FROM clientes WHERE data_inicio_contrato <= ? AND ({$tenantScope})");
        $stmt_clientes->execute([$history_end]);
        $history_clientes = $stmt_clientes->fetchAll(PDO::FETCH_ASSOC);

        // Buscar despesas dos últimos 6 meses para fluxo de caixa
        $stmt_despesas = $pdo->prepare("SELECT valor, data_despesa FROM despesas WHERE data_despesa BETWEEN ? AND ? AND ({$tenantScope})");
        $stmt_despesas->execute([$history_start, $history_end]);
        $history_despesas = $stmt_despesas->fetchAll(PDO::FETCH_ASSOC);
        
        for ($i = 5; $i >= 0; $i--) {
            $mStart = date('Y-m-01', strtotime("-$i months"));
            $mEnd = date('Y-m-t', strtotime("-$i months"));
            
            $flowLabels[] = date('M', strtotime($mStart));
            
            // Rev (MRR In-Memory Calculation)
            $m_rev = 0;
            foreach ($history_clientes as $c) {
                if (
                    !empty($c['data_inicio_contrato']) && $c['data_inicio_contrato'] <= $mEnd &&
                    (empty($c['data_fim_contrato']) || $c['data_fim_contrato'] >= $mStart) &&
                    ($c['status_contrato'] === 'ativo' || ($c['status_contrato'] === 'cancelado' && !empty($c['data_cancelamento']) && $c['data_cancelamento'] >= $mStart))
                ) {
                    $m_rev += (float)$c['valor_mensal'];
                }
            }
            $flowRevenue[] = $m_rev;
            
            // Exp (In-Memory Sum)
            $m_exp = 0;
            foreach ($history_despesas as $e) {
                if ($e['data_despesa'] >= $mStart && $e['data_despesa'] <= $mEnd) {
                    $m_exp += (float)$e['valor'];
                }
            }
            $flowExpenses[] = $m_exp;
        }

        json_response([
            'success' => true,
            'transactions' => $transactions,
            'kpi' => [
                'revenue' => $revenue,
                'expenses' => $expenses,
                'profit' => $profit,
                'margin' => round($margin, 1)
            ],
            'charts' => [
                'by_category' => $expensesByCategory,
                'cash_flow' => [
                    'labels' => $flowLabels,
                    'revenue' => $flowRevenue,
                    'expenses' => $flowExpenses
                ]
            ]
        ]);

    } catch (PDOException $e) {
        json_response(['success' => false, 'error' => 'Erro ao carregar dados: ' . $e->getMessage()], 500);
    }
}
?>
