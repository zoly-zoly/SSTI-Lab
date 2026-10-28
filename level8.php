<?php
require_once 'session_helper.php';
$executed = false;
$payload = isset($_GET['payload']) ? $_GET['payload'] : '7*7';

// VULNERABLE: Blind SSTI. Evaluates template input silently without rendering output.
if (isset($_GET['payload']) && !empty($_GET['payload'])) {
    $executed = true;
    @eval($payload . ';');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Level 8: High | SSTI Lab</title>
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
        <h1>Level 8: High — Blind SSTI (Time Delays)</h1>

        <p>In a **Blind SSTI** scenario, the template engine processes user input and executes code, but **never reflects the output back on the page**. To confirm and exploit blind vulnerabilities, researchers must rely on out-of-band (OOB) HTTP callbacks or time-based delays (e.g., forcing the server to sleep).</p>
        
        <h3>Source Code Snippet:</h3>
        <pre><code>&lt;?php
// VULNERABLE: Evaluates payload silently without echoing the result
@eval($_GET['payload'] . ';');
// Renders static thank you message only
echo "Thank you for updating your bio profile!";
?&gt;</code></pre>

        <h3>Your Goal:</h3>
        <p>Confirm code execution blindly. Use a time delay function such as <code>sleep(3)</code> in your payload and observe the server response delay!</p>

        <form action="level8.php" method="GET" style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #334155;">
            <label for="payload">Silent Payload Parameter:</label><br>
            <input type="text" id="payload" name="payload" value="<?php echo htmlspecialchars($payload); ?>" style="width: 100%; padding: 10px; margin-top: 5px; border-radius: 6px; border: 1px solid #334155; background: #0f172a; color: white;" required><br>
            <input type="submit" value="Submit Blind Payload" class="btn">
        </form>

        <div class="render-box">
            <p style="margin: 0 0 5px 0; font-size: 0.8rem; color: #94a3b8; text-transform: uppercase;">Server Response Status:</p>
            <div><?php echo $executed ? "✅ Template evaluated silently. Thank you for your update!" : "Awaiting payload submission..."; ?></div>
            
            <?php if (strpos($payload, 'sleep') !== false): ?>
                <div style="background: rgba(239, 68, 68, 0.15); border: 1px solid #ef4444; padding: 10px; border-radius: 6px; color: #f87171; margin-top: 15px; font-weight: bold; text-align: center;">
                    🎉 SUCCESS: Server response delay confirmed! You successfully triggered Blind SSTI.
                </div>
            <?php endif; ?>
        </div>

        <br>
        <a href="index.php" class="back">⬅️ Back to Dashboard</a>
    </div>
</body>
</html>
