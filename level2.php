<?php
require_once 'session_helper.php';
$output = '';
$bio = isset($_GET['bio']) ? $_GET['bio'] : 'Hello world!';

// VULNERABLE LEVEL 2: Smarty-Style Tag Injection Simulator
// Simulates a template engine processing unescaped Smarty-style tags like {php} ... {/php} or {eval ...}
function render_template_lvl2($template_string, $data) {
    $rendered = str_replace('{$username}', $data['username'], $template_string);
    
    // VULNERABLE: Evaluates code inside {php} ... {/php} tags
    $rendered = preg_replace_callback('/\{php\}(.*?)\{\/php\}/s', function($matches) {
        $code = trim($matches[1]);
        $result = '';
        @eval($code . '; $result = $output;');
        return $result;
    }, $rendered);
    
    return $rendered;
}

$template = "User Bio: {$username} | Custom Bio: {php} echo 'Evaluated Code: '; " . $bio . "; {/php}";
$output = render_template_lvl2($template, $_SESSION['ssti_user']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Level 2: Low | SSTI Lab</title>
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
        <h1>Level 2: Low — Smarty-Style Tag Injection</h1>

        <p>Some template engines (like Smarty) historically supported embedded execution tags such as <code>{php} ... {/php}</code>. If an application allows users to control text rendered inside these execution tags, it leads directly to arbitrary code execution.</p>
        
        <h3>Source Code Snippet:</h3>
        <pre><code>&lt;?php
// VULNERABLE Smarty-style tag processor
$rendered = preg_replace_callback('/\{php\}(.*?)\{\/php\}/s', function($matches) {
    @eval($matches[1]);
}, $template);
?&gt;</code></pre>

        <h3>Your Goal:</h3>
        <p>Inject PHP code inside Smarty execution tags. For example, submit: <code>system('id')</code></p>

        <form action="level2.php" method="GET" style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #334155;">
            <label for="bio">Custom Bio Parameter:</label><br>
            <input type="text" id="bio" name="bio" value="<?php echo htmlspecialchars($bio); ?>" style="width: 100%; padding: 10px; margin-top: 5px; border-radius: 6px; border: 1px solid #334155; background: #0f172a; color: white;" required><br>
            <input type="submit" value="Render Template" class="btn">
        </form>

        <div class="render-box">
            <p style="margin: 0 0 5px 0; font-size: 0.8rem; color: #94a3b8; text-transform: uppercase;">Rendered Output:</p>
            <div><?php echo $output; ?></div>
            
            <?php if (strpos($bio, 'system') !== false || strpos($bio, 'phpinfo') !== false): ?>
                <div style="background: rgba(16, 185, 129, 0.15); border: 1px solid #10b981; padding: 10px; border-radius: 6px; color: #34d399; margin-top: 15px; font-weight: bold; text-align: center;">
                    🎉 SUCCESS: You successfully executed code via Smarty-style tag injection!
                </div>
            <?php endif; ?>
        </div>

        <br>
        <a href="index.php" class="back">⬅️ Back to Dashboard</a>
    </div>
</body>
</html>
