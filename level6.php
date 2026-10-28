<?php
require_once 'session_helper.php';
$output = '';
$class_name = isset($_GET['class']) ? $_GET['class'] : 'StdClass';

// VULNERABLE: Allows instantiating arbitrary classes via reflection based on user input
function render_template_lvl6($input_class) {
    $result = '';
    try {
        if (class_exists($input_class)) {
            $reflector = new ReflectionClass($input_class);
            $result = "Successfully reflected class: " . $reflector->getName() . " | Methods count: " . count($reflector->getMethods());
        } else {
            // Unsafe dynamic instantiation sink
            @$obj = new $input_class();
            $result = "Instantiated object of type: " . get_class($obj);
        }
    } catch (Exception $e) {
        $result = "Reflection Error: " . $e->getMessage();
    }
    return $result;
}

$output = render_template_lvl6($class_name);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Level 6: High | SSTI Lab</title>
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
        <h1>Level 6: High — Object Reflection & Sinks</h1>

        <p>Advanced template injection often involves object reflection, where attackers inspect available classes, methods, and constructors to discover internal execution sinks or instantiate administrative utilities.</p>
        
        <h3>Source Code Snippet:</h3>
        <pre><code>&lt;?php
$class_name = $_GET['class'];
// VULNERABLE: Unsafe dynamic class instantiation or reflection based on user input
$reflector = new ReflectionClass($class_name);
?&gt;</code></pre>

        <h3>Your Goal:</h3>
        <p>Inspect internal reflection properties or invoke classes such as <code>ReflectionFunction</code> or built-in system helper classes.</p>

        <form action="level6.php" method="GET" style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #334155;">
            <label for="class">Class Name Parameter:</label><br>
            <input type="text" id="class" name="class" value="<?php echo htmlspecialchars($class_name); ?>" style="width: 100%; padding: 10px; margin-top: 5px; border-radius: 6px; border: 1px solid #334155; background: #0f172a; color: white;" required><br>
            <input type="submit" value="Inspect Class via Reflection" class="btn">
        </form>

        <div class="render-box">
            <p style="margin: 0 0 5px 0; font-size: 0.8rem; color: #94a3b8; text-transform: uppercase;">Reflection Output:</p>
            <div><?php echo nl2br(htmlspecialchars($output)); ?></div>
            
            <?php if (strpos($class_name, 'Reflection') !== false): ?>
                <div style="background: rgba(239, 68, 68, 0.15); border: 1px solid #ef4444; padding: 10px; border-radius: 6px; color: #f87171; margin-top: 15px; font-weight: bold; text-align: center;">
                    🎉 SUCCESS: You successfully utilized object reflection sinks inside the template context!
                </div>
            <?php endif; ?>
        </div>

        <br>
        <a href="index.php" class="back">⬅️ Back to Dashboard</a>
    </div>
</body>
</html>
