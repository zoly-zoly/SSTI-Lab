<?php
require_once 'session_helper.php';
$output = '';
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'id';

function render_template_lvl4($filter_input) {
    // VULNERABLE: Allows passing user-supplied strings into call_user_func or array mapping
    // E.g., user supplies 'system' to be executed on a mock array item 'id'
    $result = '';
    if (is_callable($filter_input) || function_exists($filter_input)) {
        @$result = call_user_func($filter_input, 'id');
    } else {
        @eval('$result = ' . $filter_input . '("id");');
    }
    return $result;
}

$output = render_template_lvl4($filter);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Level 4: Medium | SSTI Lab</title>
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
        <h1>Level 4: Medium — Filter & Function Callback Abuse</h1>

        <p>Template engines often provide array mapping or filter mechanisms (e.g., <code>map</code>, <code>filter</code>, <code>reduce</code>). If the engine allows passing arbitrary function names as callback filters without restriction, attackers can invoke native system functions directly.</p>
        
        <h3>Source Code Snippet:</h3>
        <pre><code>&lt;?php
// VULNERABLE: Passes user input directly into callback execution sinks
$callback = $_GET['filter'];
$result = call_user_func($callback, 'id');
?&gt;</code></pre>

        <h3>Your Goal:</h3>
        <p>Abuse the filter callback mechanism by passing a system execution function name, such as <code>system</code> or <code>passthru</code>.</p>

        <form action="level4.php" method="GET" style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #334155;">
            <label for="filter">Filter Callback Function:</label><br>
            <input type="text" id="filter" name="filter" value="<?php echo htmlspecialchars($filter); ?>" style="width: 100%; padding: 10px; margin-top: 5px; border-radius: 6px; border: 1px solid #334155; background: #0f172a; color: white;" required><br>
            <input type="submit" value="Execute Callback Filter" class="btn">
        </form>

        <div class="render-box">
            <p style="margin: 0 0 5px 0; font-size: 0.8rem; color: #94a3b8; text-transform: uppercase;">Execution Result:</p>
            <div><?php echo nl2br(htmlspecialchars($output)); ?></div>
            
            <?php if ($filter === 'system' || $filter === 'passthru'): ?>
                <div style="background: rgba(245, 158, 11, 0.15); border: 1px solid #f59e0b; padding: 10px; border-radius: 6px; color: #fbbf24; margin-top: 15px; font-weight: bold; text-align: center;">
                    🎉 SUCCESS: You successfully abused function callback filters to achieve RCE!
                </div>
            <?php endif; ?>
        </div>

        <br>
        <a href="index.php" class="back">⬅️ Back to Dashboard</a>
    </div>
</body>
</html>
