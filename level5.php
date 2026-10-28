<?php
require_once 'session_helper.php';
$output = '';
$expr = isset($_GET['expr']) ? $_GET['expr'] : '7*7';

function render_template_lvl5($expression) {
    // VULNERABLE: Weak blacklist filter blocking specific keywords
    $blacklist = ['system', 'exec', 'passthru', 'shell_exec', 'popen', 'proc_open'];
    
    foreach ($blacklist as $word) {
        if (stripos($expression, $word) !== false) {
            return "Security WAF Blocked: Keyword '" . htmlspecialchars($word) . "' is strictly blacklisted!";
        }
    }
    
    // Evaluates expression if blacklist passes
    $result = '';
    @eval('$result = ' . $expression . ';');
    return $result;
}

$output = render_template_lvl5($expr);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Level 5: High | SSTI Lab</title>
    <style>
        body { font-family: sans-serif; background-color: #0f172a; color: #f1f5f9; padding: 40px 20px; display: flex; flex-direction: column; align-items: center; }
        .box { background: #1e293b; padding: 30px; border-radius: 12px; max-width: 650px; width: 100%; box-shadow: 0 4px 10px rgba(0,0,0,0.3); border-top: 5px solid #ef4444; }
        h1 { color: #ef4444; margin-top: 0; }
        code { background: #0f172a; padding: 2px 6px; border-radius: 4px; color: #f43f5e; font-family: monospace; }
        pre { background: #0f172a; padding: 15px; border-radius: 6px; overflow-x: auto; border: 1px solid #334155; }
        .btn { display: inline-block; background-color: #ef4444; color: white; padding: 10px 15px; border-radius: 6px; text-decoration: none; font-weight: bold; margin-top: 15px; border: none; cursor: pointer; }
        .btn:hover { background-color: #dc2626; }
        a.back { color: #94a3b8; text-decoration: none; display: inline-block; margin-top: 20px; }
        a.back:hover { color: #f1f5f9; }
        .render-box { background: #0f172a; border-radius: 8px; padding: 20px; border: 1px dashed #334155; margin-top: 20px; color: #facc15; font-size: 1.1rem; }
    </style>
</head>
<body>
    <div class="box">
        <h1>Level 5: High — Blacklist Keyword Bypass</h1>

        <p>To prevent RCE, the developers implemented a keyword blacklist blocking terms like <code>system</code>, <code>exec</code>, and <code>passthru</code>. However, blacklists are notoriously easy to bypass using string concatenation, encoding, or alternative execution functions.</p>
        
        <h3>Source Code Snippet:</h3>
        <pre><code>&lt;?php
$blacklist = ['system', 'exec', 'passthru', 'shell_exec'];
foreach ($blacklist as $word) {
    if (stripos($expression, $word) !== false) {
        die("Blocked!");
    }
}
@eval('$result = ' . $expression . ';');
?&gt;</code></pre>

        <h3>Your Goal:</h3>
        <p>Bypass the keyword filter. For example, concatenate strings to construct the blocked function name: <code>('sys'.'tem')('id')</code> or use alternative built-in evaluation functions.</p>

        <form action="level5.php" method="GET" style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #334155;">
            <label for="expr">Expression Parameter:</label><br>
            <input type="text" id="expr" name="expr" value="<?php echo htmlspecialchars($expr); ?>" style="width: 100%; padding: 10px; margin-top: 5px; border-radius: 6px; border: 1px solid #334155; background: #0f172a; color: white;" required><br>
            <input type="submit" value="Evaluate Expression" class="btn">
        </form>

        <div class="render-box">
            <p style="margin: 0 0 5px 0; font-size: 0.8rem; color: #94a3b8; text-transform: uppercase;">Evaluation Output:</p>
            <div><?php echo nl2br(htmlspecialchars($output)); ?></div>
            
            <?php if (strpos($expr, '.') !== false && (strpos($expr, 'sys') !== false || strpos($expr, 'exe') !== false)): ?>
                <div style="background: rgba(239, 68, 68, 0.15); border: 1px solid #ef4444; padding: 10px; border-radius: 6px; color: #f87171; margin-top: 15px; font-weight: bold; text-align: center;">
                    🎉 SUCCESS: You successfully bypassed the keyword blacklist using string concatenation!
                </div>
            <?php endif; ?>
        </div>

        <br>
        <a href="index.php" class="back">⬅️ Back to Dashboard</a>
    </div>
</body>
</html>
