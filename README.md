# 🛡️ SSTI Masterclass Suite (10 Levels)

> **⚠️ WARNING: EDUCATIONAL PURPOSES ONLY**
> This repository contains intentionally vulnerable code designed for security training and educational purposes. **DO NOT** deploy this code in a production environment. Hosting this environment on a public-facing server without strict isolation (e.g., containerization, network segmentation) is dangerous and can lead to server compromise.

---

[![Security Level: Educational](https://img.shields.io/badge/Security_Level-Educational-blue.svg)]()
[![PHP](https://img.shields.io/badge/Language-PHP-777bb4.svg)]()

Welcome to the **SSTI Masterclass Lab Suite**. This comprehensive, self-contained training arena contains 10 levels of increasing complexity, taking you from basic arithmetic expression evaluation to Smarty-style tag injections, Twig-style object traversal, keyword blacklist bypasses, object reflection, Python MRO simulations, blind SSTI time delays, sandbox escapes, and Remote Code Execution.

---

## 🎯 Lab Overview

| Level | Name | Primary Vulnerability |
| :--- | :--- | :--- |
| 🟢 | **Level 1** | Basic Expression Evaluation (`eval`) |
| 🟢 | **Level 2** | Smarty-Style Tag Injection (`{php}`) |
| 🟡 | **Level 3** | Twig-Style Object Traversal (`_self.env`) |
| 🟡 | **Level 4** | Filter & Function Callback Abuse |
| 🔴 | **Level 5** | Blacklist Keyword Bypass (Concatenation/Hex) |
| 🔴 | **Level 6** | Object Reflection & Sinks |
| 🔴 | **Level 7** | Python MRO-Style Traversal Simulation |
| 🔴 | **Level 8** | Blind SSTI (Time Delays & OOB) |
| 🟣 | **Level 9** | Sandbox Escape |
| 🟣 | **Level 10** | SSTI to Full Server Takeover (Final Boss) |

---

## 🚀 Quick Start

Ensure you have [PHP](https://www.php.net/) installed on your system.

```bash
# 1. Clone the repository (or copy the files)
git clone https://github.com/zoly-zoly/SSTI-Lab.git
cd ssti-lab

# 2. Start the local server
# (You can use any available port)
php -S localhost:8080
```

👉 **Access the lab at:** `http://localhost:8080`

---

## 📓 Level Breakdown & Solutions

### 🟢 Level 1: Basic Expression Evaluation
*   **Vulnerability:** Naive template engine evaluates expressions inside `{{ ... }}` via `eval()`.
*   **Payload:** `{{ 7*7 }}` or `{{ system('id') }}`

### 🟢 Level 2: Smarty-Style Tag Injection
*   **Vulnerability:** Unescaped template execution tags like `{php} ... {/php}`.
*   **Payload:** `{php} system('id'); {/php}`

### 🟡 Level 3: Twig-Style Object Traversal
*   **Vulnerability:** Exposing application context objects allowing method invocation.
*   **Payload:** `user.executeCommand('id')`

### 🟡 Level 4: Filter & Function Callback Abuse
*   **Vulnerability:** Passing user-supplied strings into callback execution sinks (`call_user_func`).
*   **Payload:** `system`

### 🔴 Level 5: Blacklist Keyword Bypass
*   **Vulnerability:** Weak string blacklist blocking terms like `system`.
*   **Payload:** `('sys'.'tem')('id')`

### 🔴 Level 6: Object Reflection & Sinks
*   **Vulnerability:** Unsafe dynamic class instantiation and object reflection.
*   **Payload:** `ReflectionFunction`

### 🔴 Level 7: Python MRO-Style Traversal Simulation
*   **Vulnerability:** Resolving magic attribute inheritance chains without sandbox boundaries.
*   **Payload:** `user.__class__.__mro__[1].__subclasses__()`

### 🔴 Level 8: Blind SSTI (Time Delays)
*   **Vulnerability:** Silent template evaluation without output reflection.
*   **Payload:** `sleep(3)`

### 🟣 Level 9: Sandbox Escape
*   **Vulnerability:** Sandbox validation checks literal function names but permits dynamic invocation via `call_user_func`.
*   **Payload:** `call_user_func('system', 'id')`

### 🟣 Level 10: SSTI to Full Server Takeover (Final Boss)
*   **Vulnerability:** Enterprise profile management portal evaluating bios dynamically without sandboxing.
*   **Payload:** `{{ system('id') }}` or `{{ file_get_contents('session_helper.php') }}`

---

## 🛡️ Security Policy & Disclaimer
Please see [SECURITY.md](SECURITY.md) for licensing and ethical usage instructions.

---
*Created with ❤️ by **Zoly** for Bug Bounty Mastery.*
