<?php
require_once 'session_helper.php';
$msg = '';
$output = '';

$name = isset($_GET['name']) ? $_GET['name'] : 'Alice';

// VULNERABLE TEMPLATE ENGINE SIMULATOR (Level 1)
// The engine replaces double curly braces {{ expression }} and evaluates it using eval() directly.
function render_template_lvl1($template_string, $data) {
    // Replace user placeholders
    $rendered = str_replace('{{name}}', $data['username'], $template_string);
    
    // VULNERABLE EVALUATION SINK: Finds {{ ... }} and executes it via eval()
    $rendered = preg_replace_callback('/\{\{(.*?)\}\}/', function($matches) {
        $expr = trim($matches[1]);
        // Evaluate the math or expression
        $result = '';
        @eval('$result = ' . $expr . ';');
        return $result;
    }, $rendered);
    
    return $rendered;
}

$template = "Hello, <b>{{name}}</b>! Welcome back. Your evaluated query result is: {{ " . $name . " }}";
$output = render_template_lvl1($template, $_SESSION['ssti_user']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Level 1: Low | SSTI Lab</title>
    <style>
        body { font-family: sans-serif; background-color: #0f172a; color: #f1f5f9; padding: 40px 20px; display: flex; flex-direction: column; align-items: center; }
        .box { background: #1e293b; padding: 30px; border-radius: 12px; max-width: 650px; width: 100%; box-shadow: 0 4px 10px rgba(0,0,0,0.3); border-top: 5px solid #10b981; }
        h1 { color: #10b981; margin-top: 0; }
        code { background: #0f172a; padding: 2px 6px; border-radius: 4px; color: #f43f5e; font-family: monospace; }
        pre { background: #0f172a; padding: 15px; border-radius: 6px; overflow-x: auto; border: 1px solid #334155; }
        .btn { display: inline-block; background-color: #10b981; color: white; padding: 10px 15px; border-radius: 6px; text-decoration: none; font-weight: bold; margin-top: 15px; border: none; cursor: pointer; }
        .btn:hover { background-color: #059669; }
        a.back { color: #94a3b8; text-decoration: none; display: inline-block; margin-top: 20px; }
        a.back:hover { color: #f1f5f9; }
        .render-box { background: #0f172a; border-radius: 8px; padding: 20px; border: 1px dashed #334155; margin-top: 20px; color: #38bdf8; font-size: 1.1rem; }
    </style>
</head>
<body>
    <div class="box">
        <h1>Level 1: Low — Basic Expression Evaluation</h1>

        <p>This greeting service renders user inputs by evaluating expressions placed inside double curly braces <code>{{ ... }}</code>. However, the template engine passes the inner expression directly into an unsafe evaluation sink (<code>eval()</code>).</p>
        
        <h3>Source Code Snippet:</h3>
        <pre><code>&lt;?php
// VULNERABLE TEMPLATE ENGINE SINK
$rendered = preg_replace_callback('/\{\{(.*?)\}\}/', function($matches) {
    $expr = trim($matches[1]);
    @eval('$result = ' . $expr . ';');
    return $result;
}, $template);
?&gt;</code></pre>

        <h3>Your Goal:</h3>
        <p>Inject a mathematical expression or a system command execution payload. Try inputting basic arithmetic like <code>7*7</code>, or execute PHP system functions such as <code>system('id')</code>.</p>

        <form action="level1.php" method="GET" style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #334155;">
            <label for="name">Greeting Name Parameter:</label><br>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" style="width: 100%; padding: 10px; margin-top: 5px; border-radius: 6px; border: 1px solid #334155; background: #0f172a; color: white;" required><br>
            <input type="submit" value="Render Template" class="btn">
        </form>

        <div class="render-box">
            <p style="margin: 0 0 5px 0; font-size: 0.8rem; color: #94a3b8; text-transform: uppercase;">Rendered Template Output:</p>
            <div><?php echo $output; ?></div>
            
            <?php if (strpos($name, '7*7') !== false || strpos($name, 'system') !== false || strpos($name, 'phpinfo') !== false): ?>
                <div style="background: rgba(16, 185, 129, 0.15); border: 1px solid #10b981; padding: 10px; border-radius: 6px; color: #34d399; margin-top: 15px; font-weight: bold; text-align: center;">
                    🎉 SUCCESS: You successfully evaluated expressions via Server-Side Template Injection!
                </div>
            <?php endif; ?>
        </div>

        <br>
        <a href="index.php" class="back">⬅️ Back to Dashboard</a>
    </div>
</body>
</html>
