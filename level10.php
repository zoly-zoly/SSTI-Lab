<?php
require_once 'session_helper.php';
$output = '';
$bio = isset($_POST['bio']) ? $_POST['bio'] : 'Hello, my name is Alice and I am a system administrator.';

// VULNERABLE: Final Boss Enterprise Profile Portal
// Renders user bios dynamically using an un-sandboxed evaluation template engine.
function render_template_lvl10($template_string, $user_data) {
    // Replace standard placeholders
    $rendered = str_replace(['{{username}}', '{{role}}'], [$user_data['username'], $user_data['role']], $template_string);
    
    // VULNERABLE EVALUATION SINK
    $rendered = preg_replace_callback('/\{\{(.*?)\}\}/', function($matches) {
        $expr = trim($matches[1]);
        $result = '';
        @eval('$result = ' . $expr . ';');
        return $result;
    }, $rendered);
    
    return $rendered;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['ssti_user']['bio'] = $bio;
}

$template_source = "<h3>User Profile Bio</h3><p>" . $_SESSION['ssti_user']['bio'] . "</p>";
$output = render_template_lvl10($template_source, $_SESSION['ssti_user']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Level 10: Critical | SSTI Lab</title>
    <style>
        body { font-family: sans-serif; background-color: #0f172a; color: #f1f5f9; padding: 40px 20px; display: flex; flex-direction: column; align-items: center; }
        .box { background: #1e293b; padding: 30px; border-radius: 12px; max-width: 650px; width: 100%; box-shadow: 0 4px 10px rgba(0,0,0,0.3); border-top: 5px solid #8b5cf6; }
        h1 { color: #8b5cf6; margin-top: 0; }
        code { background: #0f172a; padding: 2px 6px; border-radius: 4px; color: #f43f5e; font-family: monospace; }
        pre { background: #0f172a; padding: 15px; border-radius: 6px; overflow-x: auto; border: 1px solid #334155; }
        .btn { display: inline-block; background-color: #8b5cf6; color: white; padding: 12px 20px; border-radius: 6px; text-decoration: none; font-weight: bold; margin-top: 15px; border: none; cursor: pointer; }
        .btn:hover { background-color: #7c3aed; }
        a.back { color: #94a3b8; text-decoration: none; display: inline-block; margin-top: 20px; }
        a.back:hover { color: #f1f5f9; }
        .render-box { background: #0f172a; border-radius: 8px; padding: 20px; border: 1px dashed #334155; margin-top: 20px; color: #38bdf8; font-size: 1.1rem; }
    </style>
</head>
<body>
    <div class="box">
        <h1>Level 10: Critical — SSTI to Full Server Takeover</h1>

        <p>This is the **Final Boss** scenario. An enterprise user profile portal allows administrators to customize their profile biographies using dynamic template variables. Because the template engine evaluates user input without sandboxing, an attacker can execute arbitrary system commands or read master configuration files, achieving complete server compromise.</p>
        
        <h3>Source Code Snippet:</h3>
        <pre><code>&lt;?php
// VULNERABLE: Evaluates profile bios dynamically via template rendering
$rendered = preg_replace_callback('/\{\{(.*?)\}\}/', function($matches) {
    @eval('$result = ' . $matches[1] . ';');
    return $result;
}, $profile_bio);
?&gt;</code></pre>

        <h3>Your Goal:</h3>
        <p>Achieve full server takeover. Update your profile bio with an SSTI payload that executes system commands (e.g. <code>{{ system('id') }}</code> or reading internal files like <code>{{ file_get_contents(\'session_helper.php\') }}</code>).</p>

        <form action="level10.php" method="POST" style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #334155;">
            <label for="bio">Profile Bio Template:</label><br>
            <textarea id="bio" name="bio" rows="4" style="width: 100%; padding: 10px; margin-top: 5px; border-radius: 6px; border: 1px solid #334155; background: #0f172a; color: white;" required><?php echo htmlspecialchars($bio); ?></textarea><br>
            <input type="submit" value="Update Profile & Render Bio" class="btn">
        </form>

        <div class="render-box">
            <p style="margin: 0 0 5px 0; font-size: 0.8rem; color: #94a3b8; text-transform: uppercase;">Rendered Profile Output:</p>
            <div><?php echo $output; ?></div>
            
            <?php if (strpos($bio, 'system') !== false || strpos($bio, 'file_get_contents') !== false): ?>
                <div style="background: rgba(139, 92, 246, 0.15); border: 1px solid #8b5cf6; padding: 10px; border-radius: 6px; color: #a78bfa; margin-top: 15px; font-weight: bold; text-align: center;">
                    🎉 FINAL BOSS DEFEATED: You successfully achieved Full Server Takeover via SSTI!
                </div>
            <?php endif; ?>
        </div>

        <br>
        <a href="index.php" class="back">⬅️ Back to Dashboard</a>
    </div>
</body>
</html>
