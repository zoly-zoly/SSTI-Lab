<?php
require_once 'session_helper.php';
$output = '';
$mro_query = isset($_GET['mro']) ? $_GET['mro'] : 'user.__class__';

class MockPythonStyleObject {
    public function __toString() {
        return "MockPythonBaseObject";
    }
}

function render_template_lvl7($query) {
    // VULNERABLE: Simulates MRO/Subclasses attribute traversal
    if ($query === 'user.__class__') {
        return "Class: MockPythonStyleObject";
    }
    if ($query === 'user.__class__.__mro__[1]') {
        return "BaseClass: object";
    }
    if (strpos($query, '__subclasses__') !== false) {
        return "Subclasses discovered: [Popen, FileReader, Evaluator, ReflectionSink]";
    }
    if (strpos($query, 'Popen') !== false || strpos($query, 'system') !== false) {
        return "Command Output: uid=33(www-data) gid=33(www-data) groups=33(www-data)";
    }
    return "[Invalid Attribute Resolution]";
}

$output = render_template_lvl7($mro_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Level 7: High | SSTI Lab</title>
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
        <h1>Level 7: High — Python MRO-Style Traversal Simulation</h1>

        <p>In Python template engines like Jinja2, attackers bypass sandboxes by traversing object inheritance using Method Resolution Order (MRO): <code>__class__.__mro__[1].__subclasses__()</code>. This level simulates MRO attribute resolution inside a template evaluation context.</p>
        
        <h3>Source Code Snippet:</h3>
        <pre><code>&lt;?php
// VULNERABLE: Resolves magic attribute chains without sandbox boundaries
$result = resolve_attribute_chain($mro_query, $context);
?&gt;</code></pre>

        <h3>Your Goal:</h3>
        <p>Traverse object classes to locate execution subclasses. Try querying <code>user.__class__.__mro__[1]</code>, listing <code>.__subclasses__()</code>, or calling execution helpers like <code>Popen</code>.</p>

        <form action="level7.php" method="GET" style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #334155;">
            <label for="mro">MRO Query Parameter:</label><br>
            <input type="text" id="mro" name="mro" value="<?php echo htmlspecialchars($mro_query); ?>" style="width: 100%; padding: 10px; margin-top: 5px; border-radius: 6px; border: 1px solid #334155; background: #0f172a; color: white;" required><br>
            <input type="submit" value="Resolve MRO Chain" class="btn">
        </form>

        <div class="render-box">
            <p style="margin: 0 0 5px 0; font-size: 0.8rem; color: #94a3b8; text-transform: uppercase;">Resolution Output:</p>
            <div><?php echo nl2br(htmlspecialchars($output)); ?></div>
            
            <?php if (strpos($mro_query, '__subclasses__') !== false || strpos($mro_query, 'Popen') !== false): ?>
                <div style="background: rgba(239, 68, 68, 0.15); border: 1px solid #ef4444; padding: 10px; border-radius: 6px; color: #f87171; margin-top: 15px; font-weight: bold; text-align: center;">
                    🎉 SUCCESS: You successfully traversed Python-style MRO subclasses to execute commands!
                </div>
            <?php endif; ?>
        </div>

        <br>
        <a href="index.php" class="back">⬅️ Back to Dashboard</a>
    </div>
</body>
</html>
