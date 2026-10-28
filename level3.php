<?php
require_once 'session_helper.php';
$output = '';
$query = isset($_GET['q']) ? $_GET['q'] : 'user.username';

// Mock Object Context for Twig-Style Traversal
class MockApplicationContext {
    public $username = 'Alice';
    public $role = 'Administrator';
    
    public function getSystemInfo() {
        return "PHP Version: " . phpversion() . " | OS: " . php_uname();
    }
    
    public function executeCommand($cmd) {
        return shell_exec($cmd);
    }
}

function render_template_lvl3($template_string, $context) {
    // Replace {{ object.property }} or {{ object.method() }}
    $rendered = preg_replace_callback('/\{\{(.*?)\}\}/', function($matches) use ($context) {
        $expr = trim($matches[1]);
        
        // Simple object property/method traversal simulation
        if ($expr === 'user.username') return $context->username;
        if ($expr === 'user.role') return $context->role;
        if ($expr === 'user.getSystemInfo()') return $context->getSystemInfo();
        
        // VULNERABLE: If user can call executeCommand or traverse arbitrary functions
        if (strpos($expr, 'user.executeCommand') !== false) {
            // Extract command string inside quotes
            if (preg_match('/executeCommand\([\'"](.*?)[\'"]\)/', $expr, $cmd_matches)) {
                return $context->executeCommand($cmd_matches[1]);
            }
        }
        
        return "[Undefined Property/Method]";
    }, $template_string);
    
    return $rendered;
}

$template = "Query Result for expression: {{ " . $query . " }}";
$output = render_template_lvl3($template, new MockApplicationContext());
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Level 3: Medium | SSTI Lab</title>
    <style>
        body { font-family: sans-serif; background-color: #0f172a; color: #f1f5f9; padding: 40px 20px; display: flex; flex-direction: column; align-items: center; }
        .box { background: #1e293b; padding: 30px; border-radius: 12px; max-width: 650px; width: 100%; box-shadow: 0 4px 10px rgba(0,0,0,0.3); border-top: 5px solid #f59e0b; }
        h1 { color: #f59e0b; margin-top: 0; }
        code { background: #0f172a; padding: 2px 6px; border-radius: 4px; color: #f43f5e; font-family: monospace; }
        pre { background: #0f172a; padding: 15px; border-radius: 6px; overflow-x: auto; border: 1px solid #334155; }
        .btn { display: inline-block; background-color: #f59e0b; color: white; padding: 10px 15px; border-radius: 6px; text-decoration: none; font-weight: bold; margin-top: 15px; border: none; cursor: pointer; }
        .btn:hover { background-color: #d97706; }
        a.back { color: #94a3b8; text-decoration: none; display: inline-block; margin-top: 20px; }
        a.back:hover { color: #f1f5f9; }
        .render-box { background: #0f172a; border-radius: 8px; padding: 20px; border: 1px dashed #334155; margin-top: 20px; color: #facc15; font-size: 1.1rem; }
    </style>
</head>
<body>
    <div class="box">
        <h1>Level 3: Medium — Twig-Style Object Traversal</h1>

        <p>In modern template engines (like Twig), expression syntax allows traversing object properties and executing public methods. If an application exposes a powerful context object to the template environment, attackers can traverse methods to achieve remote code execution.</p>
        
        <h3>Source Code Snippet:</h3>
        <pre><code>&lt;?php
// VULNERABLE: Exposes application context object allowing method invocation
$rendered = preg_replace_callback('/\{\{(.*?)\}\}/', function($matches) use ($context) {
    // If expression evaluates object methods without authorization checks
    return evaluate_object_expression($matches[1], $context);
}, $template);
?&gt;</code></pre>

        <h3>Your Goal:</h3>
        <p>Traverse the context object to call internal helper methods. Try querying <code>user.getSystemInfo()</code> or invoking command execution via <code>user.executeCommand('id')</code>.</p>

        <form action="level3.php" method="GET" style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #334155;">
            <label for="q">Object Query Parameter:</label><br>
            <input type="text" id="q" name="q" value="<?php echo htmlspecialchars($query); ?>" style="width: 100%; padding: 10px; margin-top: 5px; border-radius: 6px; border: 1px solid #334155; background: #0f172a; color: white;" required><br>
            <input type="submit" value="Evaluate Object Expression" class="btn">
        </form>

        <div class="render-box">
            <p style="margin: 0 0 5px 0; font-size: 0.8rem; color: #94a3b8; text-transform: uppercase;">Rendered Output:</p>
            <div><?php echo $output; ?></div>
            
            <?php if (strpos($query, 'executeCommand') !== false): ?>
                <div style="background: rgba(245, 158, 11, 0.15); border: 1px solid #f59e0b; padding: 10px; border-radius: 6px; color: #fbbf24; margin-top: 15px; font-weight: bold; text-align: center;">
                    🎉 SUCCESS: You successfully traversed object methods via template injection!
                </div>
            <?php endif; ?>
        </div>

        <br>
        <a href="index.php" class="back">⬅️ Back to Dashboard</a>
    </div>
</body>
</html>
