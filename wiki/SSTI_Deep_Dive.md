# SSTI Deep-Dive: Theoretical Mastery & Secure Template Architecture

Welcome to the official Wiki for the **SSTI Masterclass Lab Suite**. This wiki provides advanced theoretical understanding of Server-Side Template Injection (SSTI), how template engines evaluate expressions internally, object reflection mechanics, sandbox escapes, and secure template rendering models.

---

## 🔬 Part 1: What is Server-Side Template Injection (SSTI)?

Modern web applications frequently use template engines (such as Twig in PHP, Jinja2 in Python, Thymeleaf in Java, or EJS in Node.js) to separate business logic from HTML presentation. 

### 1. Template Rendering vs. Plain String Concatenation
*   **Static/Safe:** The template engine takes a pre-compiled template file and injects data variables into predefined placeholders (e.g., `Hello {{ username }}`).
*   **Vulnerable (SSTI):** When user input is concatenated directly into the *template string itself* before compilation or evaluation (e.g., rendering a template dynamically from user profile bios or error messages), the engine treats the user input not as mere data, but as **executable template code**.

### 2. The Impact: From Math Injection to RCE
If an attacker discovers that user input is evaluated by a template engine, they can test expressions:
*   `{{ 7*7 }}` evaluates to `49` -> **Confirms SSTI.**
*   From there, attackers traverse engine-specific internal objects, call reflection sinks, invoke system execution functions (`system`, `passthru`, `popen`), and achieve **Remote Code Execution (RCE)**.

---

## 🎯 Part 2: Advanced SSTI Attack Vectors

### 1. Object Traversal & Reflection (Level 3 & 6)
Template engines often expose internal execution contexts or environment objects (such as Twig's `_self.env`).
*   By accessing these objects, attackers can traverse methods to alter configuration parameters (e.g., changing cache directories or loading arbitrary loaders) or invoke callback filters that execute arbitrary code.

### 2. Keyword Blacklist Bypasses (Level 5)
Developers often attempt to secure template engines by blacklisting dangerous strings like `system`, `exec`, `passthru`, or `shell_exec`.
*   **Bypass Techniques:** Attackers use string concatenation (`sys` . `tem`), dynamic function execution (`$fn('id')`), character encoding (hex or base64 decoding), or alternative PHP functions like `assert`, `eval`, or `call_user_func`.

### 3. Sandbox Escapes (Level 9)
Many enterprise template engines implement "sandboxed" modes to restrict file system access and function execution.
*   **The Flaw:** Sandboxes rely on static method whitelists or syntax restrictions. If an attacker can find an un-sanitized object method or constructor that allows instantiating reflection classes or calling arbitrary static methods, they can break out of the sandbox confinement.

---

## 🛡️ Part 3: Defensive Architecture & Secure Rendering

To secure applications against SSTI, developers must adhere to strict separation of data and code:

```
                  ┌────────────────────────────────────────┐
                  │          User Provided Input           │
                  └───────────────────┬────────────────────┘
                                      │
                                      ▼
                        [ Treat Input Strictly as Data ]
                       (Never concatenate into templates)
                                      │
                                      ▼
                        [ Compile Predefined Files ]
                   (Use static .twig / .html template files)
                                      │
                                      ▼
                           [ Safe Render Output ]
```

### 1. Never Concatenate User Input into Templates
Never dynamically generate template strings from user input. Always load static template files and pass user input strictly as **context variables**:

```php
// SECURE: User input is passed as context data, not evaluated as code
echo $twig->render('profile.twig', [
    'username' => $_GET['username'] // Safe! Even if it contains {{ 7*7 }}, it prints literally.
]);
```

### 2. Enable Strict Sandboxing
If dynamic templates must be evaluated (e.g., in a CMS where trusted administrators write templates), ensure the template engine's sandbox mode is fully enabled with a strict policy that disables reflection and system calls.

---
*Documentation curated by **Zoly** for the advancement of secure application architecture.*
