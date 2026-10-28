<?php
require_once 'session_helper.php';
$output = '';
$code = isset($_GET['code']) ? $_GET['code'] : '7*7';

// VULNERABLE: Sandbox Escape Simulator
// The template engine runs inside a restricted sandbox that blocks dangerous functions.
// However, the sandbox validation contains a logic flaw: it checks only the function name string 
// but fails to restrict dynamic variable function calls or callable arrays.
function render_template_lvl9($user_code) {
    // Attempt at sandboxing: block literal function names
    $blocked = ['system', 'exec', 'passthru', 'shell_exec'];
    foreach ($blocked as $b) {
        if (stripos($user_code, $b) !== false) {
            // Logic flaw: Can be bypassed by constructing the function dynamically or using call_user_func
            if (strpos($user_code, 'call_user_func') === false && strpos($user_code, '$') === false) {
                return "Sandbox Enforced: Function '" . htmlspecialchars($b) . "' is blocked by sandbox policy.";
            }
        }
    }
    
    $result = '';
    @eval('$result = ' . $user_code . ';');
    return $result;
}

$output = render_template_lvl9($code);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Level 9: Critical | SSTI Lab</title>
    <style>
        body { font-family: sans-serif; background-color: #0f172a; color: #f1f5f9; padding: 40px 20px; display: flex; flex-direction: column; align-items: center; }
        .box { background: #1e293b; padding: 30px; border-radius: 12px; max-width: 650px; width: 100%; box-shadow: 0 4px 10px rgba(0,0,0,0.3); border-top: 5px solid #8b5cf6; }
        h1 { color: #8b5cf6; margin-top: 0; }
        code { background: #0f172a; padding: 2px 6px; border-radius: 4px; color: #f43f5e; font-family: monospace; }
        pre { background: #0f172a; padding: 15px; border-radius: 6px; overflow-x: auto; border: 1px solid #334155; }
        .btn { display: inline-block; background-color: #8b5cf6; color: white; padding: 10px 15px; border-radius: 6px; text-decoration: none; font-weight: bold; margin-top: 15px; border: none; cursor: pointer; }
        .btn:hover { background-color: #7c3aed; }
        a.back { color: #94a3b8; text-decoration: none; display: inline-block; margin-top: 20px; }
        a.back:hover { color: #f1f5f9; }
        .render-box { background: #0f172a; border-radius: 8px; padding: 20px; border: 1px dashed #334155; margin-top: 20px; color: #facc15; font-size: 1.1rem; }
    </style>
</head>
<body>
    <div class="box">
        <h1>Level 9: Critical — Sandbox Escape</h1>

        <p>Enterprise template engines often run inside a restricted **sandbox environment** designed to block file system access and system execution. However, sandbox filters frequently suffer from logic flaws, such as checking only literal function strings while permitting dynamic invocation (e.g., <code>call_user_func</code> or variable functions).</p>
        
        <h3>Source Code Snippet:</h3>
        <pre><code>&lt;?php
// Sandbox policy blocks literal function names unless called dynamically
if (stripos($code, 'system') !== false && strpos($code, 'call_user_func') === false) {
    die("Sandbox Violation!");
}
@eval('$result = ' . $code . ';');
?&gt;</code></pre>

        <h3>Your Goal:</h3>
        <p>Escape the sandbox. Use dynamic execution or callback functions to invoke restricted system commands, such as <code>call_user_func('system', 'id')</code>.</p>

        <form action="level9.php" method="GET" style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #334155;">
            <label for="code">Sandbox Expression Parameter:</label><br>
            <input type="text" id="code" name="code" value="<?php echo htmlspecialchars($code); ?>" style="width: 100%; padding: 10px; margin-top: 5px; border-radius: 6px; border: 1px solid #334155; background: #0f172a; color: white;" required><br>
            <input type="submit" value="Evaluate in Sandbox" class="btn">
        </form>

        <div class="render-box">
            <p style="margin: 0 0 5px 0; font-size: 0.8rem; color: #94a3b8; text-transform: uppercase;">Sandbox Output:</p>
            <div><?php echo nl2br(htmlspecialchars($output)); ?></div>
            
            <?php if (strpos($code, 'call_user_func') !== false): ?>
                <div style="background: rgba(139, 92, 246, 0.15); border: 1px solid #8b5cf6; padding: 10px; border-radius: 6px; color: #a78bfa; margin-top: 15px; font-weight: bold; text-align: center;">
                    🎉 SUCCESS: You successfully escaped the template engine sandbox!
                </div>
            <?php endif; ?>
        </div>

        <br>
        <a href="index.php" class="back">⬅️ Back to Dashboard</a>
    </div>
</body>
</html>
