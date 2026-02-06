@php
/**
 * Syntax-highlight a JSON string with span classes.
 */
if (! function_exists('syntaxHighlight')) {
    function syntaxHighlight(string $json): string {
        $json = htmlspecialchars($json, ENT_QUOTES);
        // Keys
        $json = preg_replace('/&quot;([^&]+?)&quot;\s*:/', '<span class="json-key">&quot;$1&quot;</span>:', $json);
        // String values
        $json = preg_replace('/:\s*&quot;(.*?)&quot;/', ': <span class="json-string">&quot;$1&quot;</span>', $json);
        // Numbers
        $json = preg_replace('/:\s*(\d+)/', ': <span class="json-number">$1</span>', $json);
        // Booleans
        $json = preg_replace('/:\s*(true|false)/', ': <span class="json-bool">$1</span>', $json);
        // Null
        $json = preg_replace('/:\s*null/', ': <span class="json-null">null</span>', $json);
        // Braces and brackets
        $json = str_replace(['{', '}', '[', ']'], [
            '<span class="json-brace">{</span>',
            '<span class="json-brace">}</span>',
            '<span class="json-brace">[</span>',
            '<span class="json-brace">]</span>',
        ], $json);
        return $json;
    }
}
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Natakahii API Documentation</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --navy: #0f172a;
            --navy-light: #1e293b;
            --navy-mid: #334155;
            --accent: #f97316;
            --accent-hover: #ea580c;
            --accent-soft: rgba(249, 115, 22, 0.1);
            --green: #22c55e;
            --green-soft: rgba(34, 197, 94, 0.1);
            --blue: #3b82f6;
            --blue-soft: rgba(59, 130, 246, 0.1);
            --red: #ef4444;
            --red-soft: rgba(239, 68, 68, 0.1);
            --yellow: #eab308;
            --yellow-soft: rgba(234, 179, 8, 0.1);
            --purple: #a855f7;
            --surface: #ffffff;
            --surface-raised: #f8fafc;
            --border: #e2e8f0;
            --border-light: #f1f5f9;
            --text: #0f172a;
            --text-secondary: #475569;
            --text-muted: #94a3b8;
            --radius: 12px;
            --radius-sm: 8px;
            --radius-xs: 6px;
            --shadow-sm: 0 1px 2px rgba(0,0,0,0.05);
            --shadow: 0 1px 3px rgba(0,0,0,0.1), 0 1px 2px rgba(0,0,0,0.06);
            --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -2px rgba(0,0,0,0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -4px rgba(0,0,0,0.1);
            --font-sans: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            --font-mono: 'JetBrains Mono', 'Fira Code', 'Consolas', monospace;
        }

        body {
            font-family: var(--font-sans);
            background: var(--surface-raised);
            color: var(--text);
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        /* ── Header ───────────────────────────────────────── */
        .header {
            background: var(--navy);
            color: white;
            position: sticky;
            top: 0;
            z-index: 100;
            border-bottom: 3px solid var(--accent);
        }

        .header-inner {
            max-width: 1360px;
            margin: 0 auto;
            padding: 1rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .brand { display: flex; align-items: center; gap: .75rem; text-decoration: none; }
        .brand-icon {
            width: 36px; height: 36px;
            background: var(--accent);
            border-radius: var(--radius-sm);
            display: grid; place-items: center;
        }
        .brand-text { font-size: 1.35rem; font-weight: 800; color: white; letter-spacing: -.02em; }
        .brand-text span { color: var(--accent); }

        .header-meta { display: flex; align-items: center; gap: 1rem; }
        .version-badge {
            font-size: .75rem; font-weight: 600; letter-spacing: .04em; text-transform: uppercase;
            padding: .3rem .7rem; border-radius: 20px;
            background: rgba(255,255,255,.1); color: rgba(255,255,255,.8);
        }
        .base-url-chip {
            font-family: var(--font-mono); font-size: .82rem; font-weight: 500;
            padding: .4rem 1rem; border-radius: var(--radius-sm);
            background: rgba(255,255,255,.08); color: rgba(255,255,255,.85);
            border: 1px solid rgba(255,255,255,.1);
            cursor: pointer; transition: background .2s;
            display: flex; align-items: center; gap: .5rem;
        }
        .base-url-chip:hover { background: rgba(255,255,255,.14); }
        .base-url-chip svg { width: 14px; height: 14px; opacity: .6; }

        /* ── Layout ───────────────────────────────────────── */
        .layout {
            max-width: 1360px;
            margin: 0 auto;
            padding: 2rem;
            display: grid;
            grid-template-columns: 260px 1fr;
            gap: 2rem;
            align-items: start;
        }

        @media (max-width: 1024px) {
            .layout { grid-template-columns: 1fr; padding: 1.25rem; }
        }

        /* ── Sidebar ──────────────────────────────────────── */
        .sidebar {
            position: sticky; top: calc(68px + 1.5rem);
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 1.25rem;
            max-height: calc(100vh - 100px);
            overflow-y: auto;
        }

        .sidebar-title {
            font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: .08em;
            color: var(--text-muted); margin-bottom: 1rem; padding: 0 .5rem;
        }

        .nav-group { margin-bottom: 1.25rem; }
        .nav-group-label {
            font-size: .82rem; font-weight: 700; color: var(--text);
            padding: .4rem .5rem; display: flex; align-items: center; gap: .5rem;
        }
        .nav-group-label::before {
            content: ''; width: 3px; height: 14px; border-radius: 2px; background: var(--accent);
        }

        .nav-items { list-style: none; margin-top: .35rem; }
        .nav-items li a {
            display: flex; align-items: center; gap: .6rem;
            padding: .45rem .5rem .45rem 1.1rem;
            font-size: .82rem; color: var(--text-secondary);
            text-decoration: none; border-radius: var(--radius-xs);
            transition: all .15s;
        }
        .nav-items li a:hover { background: var(--surface-raised); color: var(--text); }
        .nav-items li a.active { background: var(--accent-soft); color: var(--accent); font-weight: 600; }
        .nav-method {
            font-family: var(--font-mono); font-size: .65rem; font-weight: 700;
            min-width: 34px; text-align: center; padding: .15rem .3rem;
            border-radius: 4px; letter-spacing: .02em;
        }
        .nav-method.get { background: var(--green-soft); color: var(--green); }
        .nav-method.post { background: var(--accent-soft); color: var(--accent); }
        .nav-method.put { background: var(--blue-soft); color: var(--blue); }
        .nav-method.delete { background: var(--red-soft); color: var(--red); }

        @media (max-width: 1024px) {
            .sidebar { display: none; position: fixed; top: 0; left: 0; width: 300px; height: 100vh; z-index: 200; border-radius: 0; }
            .sidebar.open { display: block; }
            .mobile-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.4); z-index: 199; }
            .mobile-overlay.open { display: block; }
        }

        .mobile-toggle {
            display: none; position: fixed; bottom: 1.5rem; right: 1.5rem; z-index: 150;
            width: 48px; height: 48px; border-radius: 50%; border: none;
            background: var(--accent); color: white; cursor: pointer;
            box-shadow: var(--shadow-lg);
        }
        @media (max-width: 1024px) { .mobile-toggle { display: grid; place-items: center; } }

        /* ── Content ──────────────────────────────────────── */
        .content { min-width: 0; }

        /* Intro Card */
        .intro-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .intro-card h1 {
            font-size: 1.6rem; font-weight: 800; letter-spacing: -.02em;
            margin-bottom: .5rem;
        }
        .intro-card p { color: var(--text-secondary); font-size: .95rem; max-width: 640px; }

        .info-banner {
            margin-top: 1.5rem; padding: 1rem 1.25rem;
            background: var(--yellow-soft); border: 1px solid rgba(234,179,8,.25);
            border-radius: var(--radius-sm);
            display: flex; align-items: flex-start; gap: .75rem;
            font-size: .88rem; color: var(--text);
        }
        .info-banner svg { flex-shrink: 0; margin-top: 2px; color: var(--yellow); }
        .info-banner code {
            font-family: var(--font-mono); font-size: .82rem;
            background: rgba(0,0,0,.06); padding: .15rem .45rem; border-radius: 4px;
        }

        /* ── Endpoint Group ───────────────────────────────── */
        .endpoint-group { margin-bottom: 2.5rem; }
        .group-heading {
            display: flex; align-items: center; gap: .75rem;
            margin-bottom: 1rem; padding-bottom: .75rem;
            border-bottom: 2px solid var(--border);
        }
        .group-heading-icon {
            width: 36px; height: 36px; border-radius: var(--radius-sm);
            background: var(--navy); color: white;
            display: grid; place-items: center; flex-shrink: 0;
        }
        .group-heading h2 { font-size: 1.2rem; font-weight: 700; }
        .group-heading p { font-size: .85rem; color: var(--text-secondary); }

        /* ── Endpoint Card ────────────────────────────────── */
        .ep-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            margin-bottom: 1rem;
            overflow: hidden;
            transition: box-shadow .2s;
        }
        .ep-card:hover { box-shadow: var(--shadow-md); }
        .ep-card.open { border-color: var(--accent); box-shadow: 0 0 0 1px var(--accent), var(--shadow-md); }

        .ep-trigger {
            width: 100%; background: none; border: none; cursor: pointer;
            padding: 1rem 1.25rem;
            display: flex; align-items: center; gap: 1rem;
            text-align: left; font-family: inherit;
        }

        .method-pill {
            font-family: var(--font-mono); font-size: .75rem; font-weight: 700;
            padding: .35rem .7rem; border-radius: var(--radius-xs);
            min-width: 54px; text-align: center; color: white; flex-shrink: 0;
        }
        .method-pill.get { background: var(--green); }
        .method-pill.post { background: var(--accent); }
        .method-pill.put { background: var(--blue); }
        .method-pill.delete { background: var(--red); }

        .ep-meta { flex: 1; min-width: 0; }
        .ep-title {
            font-size: .95rem; font-weight: 600; color: var(--text);
            display: flex; align-items: center; gap: .5rem; flex-wrap: wrap;
        }
        .ep-url {
            font-family: var(--font-mono); font-size: .82rem; color: var(--text-secondary);
            margin-top: .25rem; word-break: break-all;
        }
        .ep-desc { font-size: .82rem; color: var(--text-muted); margin-top: .2rem; }

        .auth-chip {
            font-size: .65rem; font-weight: 700; text-transform: uppercase; letter-spacing: .04em;
            padding: .2rem .55rem; border-radius: 4px;
            background: var(--red-soft); color: var(--red);
            display: inline-flex; align-items: center; gap: .25rem;
        }
        .auth-chip svg { width: 10px; height: 10px; }

        .ep-chevron {
            width: 20px; height: 20px; color: var(--text-muted);
            transition: transform .25s; flex-shrink: 0;
        }
        .ep-card.open .ep-chevron { transform: rotate(180deg); color: var(--accent); }

        /* ── Endpoint Detail Panel ────────────────────────── */
        .ep-panel {
            display: none;
            border-top: 1px solid var(--border);
            padding: 1.5rem 1.25rem;
            background: var(--surface-raised);
        }
        .ep-card.open .ep-panel { display: block; animation: fadeSlide .25s ease-out; }

        @keyframes fadeSlide {
            from { opacity: 0; transform: translateY(-6px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Section Labels */
        .section-label {
            font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .08em;
            color: var(--text-muted); margin-bottom: .75rem; margin-top: 1.5rem;
            display: flex; align-items: center; gap: .5rem;
        }
        .section-label:first-child { margin-top: 0; }
        .section-label svg { width: 14px; height: 14px; }

        /* ── Headers Table ────────────────────────────────── */
        .headers-table {
            width: 100%; border-collapse: collapse;
            font-size: .85rem; margin-bottom: .5rem;
        }
        .headers-table th {
            text-align: left; font-size: .72rem; font-weight: 700; text-transform: uppercase;
            letter-spacing: .06em; color: var(--text-muted); padding: .5rem .75rem;
            border-bottom: 1px solid var(--border);
        }
        .headers-table td {
            padding: .6rem .75rem; border-bottom: 1px solid var(--border-light);
            vertical-align: top;
        }
        .headers-table tr:last-child td { border-bottom: none; }

        /* ── Params Table ─────────────────────────────────── */
        .params-table {
            width: 100%; border-collapse: collapse; font-size: .85rem;
        }
        .params-table th {
            text-align: left; font-size: .72rem; font-weight: 700; text-transform: uppercase;
            letter-spacing: .06em; color: var(--text-muted); padding: .6rem .75rem;
            background: var(--surface); border-bottom: 2px solid var(--border);
        }
        .params-table td {
            padding: .7rem .75rem;
            border-bottom: 1px solid var(--border-light);
            vertical-align: top;
        }
        .params-table tr:last-child td { border-bottom: none; }
        .params-table tr:hover td { background: rgba(0,0,0,.015); }

        .param-name-cell {
            display: flex; align-items: center; gap: .4rem;
        }
        .param-name-text {
            font-family: var(--font-mono); font-size: .82rem; font-weight: 600;
            color: var(--navy); background: var(--surface-raised);
            padding: .2rem .5rem; border-radius: 4px; border: 1px solid var(--border);
        }
        .copy-btn {
            background: none; border: none; cursor: pointer; padding: 2px;
            color: var(--text-muted); border-radius: 4px; transition: all .15s;
            display: inline-flex; align-items: center;
        }
        .copy-btn:hover { color: var(--accent); background: var(--accent-soft); }
        .copy-btn svg { width: 13px; height: 13px; }
        .copy-btn.copied { color: var(--green); }

        .type-badge {
            font-size: .72rem; font-weight: 600; padding: .15rem .5rem;
            border-radius: 4px; text-transform: lowercase;
            background: var(--blue-soft); color: var(--blue);
        }

        .required-dot {
            display: inline-block; width: 6px; height: 6px;
            border-radius: 50%; background: var(--red); margin-left: .25rem;
            vertical-align: middle;
        }
        .optional-text { font-size: .72rem; color: var(--text-muted); font-style: italic; }

        .rules-text {
            font-family: var(--font-mono); font-size: .75rem; color: var(--text-secondary);
        }
        .param-desc { font-size: .8rem; color: var(--text-secondary); margin-top: .25rem; }

        /* ── Code Block ───────────────────────────────────── */
        .code-wrapper {
            position: relative; margin-top: .5rem; margin-bottom: .5rem;
            border-radius: var(--radius-sm); overflow: hidden;
            border: 1px solid var(--navy-mid);
        }
        .code-toolbar {
            display: flex; align-items: center; justify-content: space-between;
            padding: .4rem .75rem;
            background: var(--navy-mid); font-size: .7rem;
        }
        .code-lang {
            font-family: var(--font-mono); font-weight: 600; color: var(--text-muted);
            text-transform: uppercase; letter-spacing: .06em;
        }
        .code-copy-btn {
            background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.1);
            color: rgba(255,255,255,.65); padding: .25rem .6rem;
            border-radius: 4px; cursor: pointer; font-size: .72rem;
            font-family: var(--font-sans); font-weight: 500;
            display: flex; align-items: center; gap: .3rem; transition: all .15s;
        }
        .code-copy-btn:hover { background: rgba(255,255,255,.15); color: white; }
        .code-copy-btn.copied { background: var(--green-soft); color: var(--green); border-color: transparent; }
        .code-copy-btn svg { width: 12px; height: 12px; }

        .code-block {
            background: var(--navy);
            padding: 1rem 1.25rem;
            overflow-x: auto;
            font-family: var(--font-mono);
            font-size: .82rem;
            line-height: 1.7;
            color: #e2e8f0;
        }
        .code-block pre { margin: 0; white-space: pre; }

        /* JSON Syntax Colors */
        .json-key { color: #7dd3fc; }
        .json-string { color: #86efac; }
        .json-number { color: #fbbf24; }
        .json-bool { color: #c084fc; }
        .json-null { color: #f87171; }
        .json-brace { color: #94a3b8; }

        /* ── Response Tabs ────────────────────────────────── */
        .response-tabs {
            display: flex; gap: .25rem; margin-bottom: .5rem;
        }
        .response-tab {
            font-family: var(--font-sans); font-size: .78rem; font-weight: 600;
            padding: .4rem .85rem; border-radius: var(--radius-xs);
            border: 1px solid var(--border); background: var(--surface);
            color: var(--text-secondary); cursor: pointer; transition: all .15s;
            display: flex; align-items: center; gap: .35rem;
        }
        .response-tab:hover { border-color: var(--text-muted); }
        .response-tab.active { border-color: var(--accent); background: var(--accent-soft); color: var(--accent); }
        .response-tab .status-dot {
            width: 6px; height: 6px; border-radius: 50%;
        }
        .response-tab .status-dot.success { background: var(--green); }
        .response-tab .status-dot.error { background: var(--red); }

        .response-panel { display: none; }
        .response-panel.active { display: block; }

        .status-badge {
            display: inline-flex; align-items: center; gap: .3rem;
            font-family: var(--font-mono); font-size: .75rem; font-weight: 700;
            padding: .2rem .55rem; border-radius: 4px; margin-bottom: .4rem;
        }
        .status-badge.s2xx { background: var(--green-soft); color: var(--green); }
        .status-badge.s4xx { background: var(--red-soft); color: var(--red); }

        .error-desc { font-size: .82rem; color: var(--text-secondary); margin-bottom: .5rem; }

        /* ── Footer ───────────────────────────────────────── */
        .footer {
            background: var(--navy); color: rgba(255,255,255,.6);
            text-align: center; padding: 2.5rem 2rem;
            margin-top: 2rem; font-size: .85rem;
        }
        .footer-brand { display: flex; align-items: center; justify-content: center; gap: .5rem; margin-bottom: .75rem; }
        .footer-brand span { font-size: 1.15rem; font-weight: 700; color: white; }
        .footer-brand span em { font-style: normal; color: var(--accent); }
        .footer-divider { width: 40px; height: 2px; background: var(--accent); margin: 1rem auto; border-radius: 1px; }

        /* ── Toast Notification ───────────────────────────── */
        .toast {
            position: fixed; bottom: 2rem; left: 50%; transform: translateX(-50%) translateY(100px);
            background: var(--navy); color: white; padding: .6rem 1.25rem;
            border-radius: var(--radius-sm); font-size: .82rem; font-weight: 500;
            box-shadow: var(--shadow-lg); z-index: 300; opacity: 0;
            transition: transform .3s, opacity .3s;
            display: flex; align-items: center; gap: .5rem;
        }
        .toast.show { transform: translateX(-50%) translateY(0); opacity: 1; }
        .toast svg { color: var(--green); width: 16px; height: 16px; }

        /* Scrollbar */
        .sidebar::-webkit-scrollbar { width: 4px; }
        .sidebar::-webkit-scrollbar-track { background: transparent; }
        .sidebar::-webkit-scrollbar-thumb { background: var(--border); border-radius: 4px; }

        .code-block::-webkit-scrollbar { height: 4px; }
        .code-block::-webkit-scrollbar-track { background: transparent; }
        .code-block::-webkit-scrollbar-thumb { background: var(--navy-mid); border-radius: 4px; }

        @media (max-width: 640px) {
            .header-inner { flex-direction: column; align-items: flex-start; padding: 1rem 1.25rem; }
            .header-meta { flex-wrap: wrap; }
            .intro-card { padding: 1.5rem; }
            .intro-card h1 { font-size: 1.3rem; }
            .ep-trigger { flex-direction: column; align-items: flex-start; gap: .6rem; }
            .method-pill { align-self: flex-start; }
            .params-table { display: block; overflow-x: auto; }
        }
    </style>
</head>
<body>

<!-- Toast -->
<div class="toast" id="toast">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
    <span id="toastText">Copied!</span>
</div>

<!-- Mobile Toggle -->
<button class="mobile-toggle" id="mobileToggle" aria-label="Toggle navigation">
    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
</button>
<div class="mobile-overlay" id="mobileOverlay"></div>

<!-- Header -->
<header class="header">
    <div class="header-inner">
        <a href="#" class="brand">
            <div class="brand-icon">
                <svg viewBox="0 0 24 24" fill="white" width="20" height="20"><path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49A1 1 0 0020 4H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/></svg>
            </div>
            <div class="brand-text">Nata<span>kahii</span></div>
        </a>
        <div class="header-meta">
            <span class="version-badge">v1.0</span>
            <span class="base-url-chip" onclick="copyText('{{ url('/api/v1') }}', this)" title="Click to copy">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>
                {{ url('/api/v1') }}
            </span>
        </div>
    </div>
</header>

<div class="layout">
    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-title">Navigation</div>
        @foreach($endpoints as $group)
            <div class="nav-group">
                <div class="nav-group-label">{{ $group['group'] }}</div>
                <ul class="nav-items">
                    @foreach($group['endpoints'] as $ep)
                        <li>
                            <a href="#{{ Str::slug($ep['name']) }}" onclick="closeMobile()">
                                <span class="nav-method {{ strtolower($ep['method']) }}">{{ $ep['method'] }}</span>
                                {{ $ep['name'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </nav>

    <!-- Content -->
    <main class="content">
        <!-- Intro -->
        <div class="intro-card">
            <h1>API Reference</h1>
            <p>Complete reference for the Natakahii REST API. All endpoints accept and return JSON. Use the navigation to jump to any endpoint.</p>

            <div class="info-banner">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <div>
                    <strong>Authentication</strong> — Endpoints marked with a red <strong>AUTH</strong> chip require a valid JWT token.
                    Include it as <code>Authorization: Bearer &lt;token&gt;</code> in every authenticated request.
                </div>
            </div>
        </div>

        @foreach($endpoints as $group)
            <section class="endpoint-group" id="group-{{ Str::slug($group['group']) }}">
                <div class="group-heading">
                    <div class="group-heading-icon">
                        @if($group['group'] === 'Authentication')
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                        @else
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
                        @endif
                    </div>
                    <div>
                        <h2>{{ $group['group'] }}</h2>
                        <p>{{ $group['description'] ?? '' }}</p>
                    </div>
                </div>

                @foreach($group['endpoints'] as $epIndex => $ep)
                    <div class="ep-card" id="{{ Str::slug($ep['name']) }}">
                        <button class="ep-trigger" onclick="toggleCard(this)">
                            <span class="method-pill {{ strtolower($ep['method']) }}">{{ $ep['method'] }}</span>
                            <div class="ep-meta">
                                <div class="ep-title">
                                    {{ $ep['name'] }}
                                    @if($ep['auth_required'])
                                        <span class="auth-chip">
                                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V10a2 2 0 00-2-2z"/></svg>
                                            Auth
                                        </span>
                                    @endif
                                </div>
                                <div class="ep-url">{{ $ep['url'] }}</div>
                                <div class="ep-desc">{{ $ep['description'] }}</div>
                            </div>
                            <svg class="ep-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                        </button>

                        <div class="ep-panel">
                            {{-- Headers --}}
                            @if(!empty($ep['headers']))
                                <div class="section-label">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
                                    Headers
                                </div>
                                <table class="headers-table">
                                    <thead><tr><th>Header</th><th>Value</th></tr></thead>
                                    <tbody>
                                        @foreach($ep['headers'] as $hName => $hValue)
                                            <tr>
                                                <td>
                                                    <span class="param-name-cell">
                                                        <span class="param-name-text">{{ $hName }}</span>
                                                        <button class="copy-btn" onclick="event.stopPropagation(); copyText('{{ $hName }}', this)" title="Copy">
                                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>
                                                        </button>
                                                    </span>
                                                </td>
                                                <td><code style="font-family:var(--font-mono);font-size:.82rem;color:var(--text-secondary)">{{ $hValue }}</code></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif

                            {{-- Request Parameters --}}
                            @if(count($ep['request']) > 0)
                                <div class="section-label">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                    Request Body <span style="font-weight:400;color:var(--text-muted);text-transform:none;letter-spacing:0;margin-left:.25rem">application/json</span>
                                </div>
                                <div style="overflow-x:auto">
                                    <table class="params-table">
                                        <thead>
                                            <tr>
                                                <th style="width:28%">Parameter</th>
                                                <th style="width:12%">Type</th>
                                                <th style="width:10%">Required</th>
                                                <th style="width:22%">Rules</th>
                                                <th>Description</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($ep['request'] as $param)
                                                <tr>
                                                    <td>
                                                        <span class="param-name-cell">
                                                            <span class="param-name-text">{{ $param['name'] }}</span>
                                                            <button class="copy-btn" onclick="event.stopPropagation(); copyText('{{ $param['name'] }}', this)" title="Copy parameter name">
                                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>
                                                            </button>
                                                        </span>
                                                    </td>
                                                    <td><span class="type-badge">{{ $param['type'] }}</span></td>
                                                    <td>
                                                        @if($param['required'])
                                                            <span style="color:var(--red);font-size:.78rem;font-weight:600">Yes <span class="required-dot"></span></span>
                                                        @else
                                                            <span class="optional-text">Optional</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($param['rules'])
                                                            <span class="rules-text">{{ $param['rules'] }}</span>
                                                        @else
                                                            <span style="color:var(--text-muted)">—</span>
                                                        @endif
                                                    </td>
                                                    <td><span class="param-desc">{{ $param['description'] }}</span></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif

                            {{-- Responses --}}
                            <div class="section-label" style="margin-top:1.5rem">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                                Responses
                            </div>

                            @php
                                $cardId = Str::slug($ep['name']);
                                $hasErrors = !empty($ep['error_responses']);
                            @endphp

                            <div class="response-tabs">
                                <button class="response-tab active" onclick="switchTab(this, '{{ $cardId }}-success')">
                                    <span class="status-dot success"></span>
                                    {{ $ep['success_response']['status'] }} Success
                                </button>
                                @if($hasErrors)
                                    @foreach($ep['error_responses'] as $errIdx => $err)
                                        <button class="response-tab" onclick="switchTab(this, '{{ $cardId }}-error-{{ $errIdx }}')">
                                            <span class="status-dot error"></span>
                                            {{ $err['status'] }} {{ $err['description'] }}
                                        </button>
                                    @endforeach
                                @endif
                            </div>

                            {{-- Success Response --}}
                            <div class="response-panel active" id="{{ $cardId }}-success">
                                <span class="status-badge s2xx">{{ $ep['success_response']['status'] }} OK</span>
                                @php $json = json_encode($ep['success_response']['body'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES); @endphp
                                <div class="code-wrapper">
                                    <div class="code-toolbar">
                                        <span class="code-lang">JSON</span>
                                        <button class="code-copy-btn" onclick="copyCode(this)" data-code="{{ htmlspecialchars($json) }}">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>
                                            Copy
                                        </button>
                                    </div>
                                    <div class="code-block"><pre>{!! syntaxHighlight($json) !!}</pre></div>
                                </div>
                            </div>

                            {{-- Error Responses --}}
                            @if($hasErrors)
                                @foreach($ep['error_responses'] as $errIdx => $err)
                                    <div class="response-panel" id="{{ $cardId }}-error-{{ $errIdx }}">
                                        <span class="status-badge s4xx">{{ $err['status'] }}</span>
                                        <p class="error-desc">{{ $err['description'] }}</p>
                                        @php $errJson = json_encode($err['body'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES); @endphp
                                        <div class="code-wrapper">
                                            <div class="code-toolbar">
                                                <span class="code-lang">JSON</span>
                                                <button class="code-copy-btn" onclick="copyCode(this)" data-code="{{ htmlspecialchars($errJson) }}">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>
                                                    Copy
                                                </button>
                                            </div>
                                            <div class="code-block"><pre>{!! syntaxHighlight($errJson) !!}</pre></div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                @endforeach
            </section>
        @endforeach
    </main>
</div>

<footer class="footer">
    <div class="footer-brand"><span>Nata<em>kahii</em></span></div>
    <div class="footer-divider"></div>
    <p>&copy; {{ date('Y') }} Natakahii. All rights reserved.</p>
    <p style="margin-top:.4rem;font-size:.78rem;opacity:.6">Built with Laravel &amp; JWT Authentication</p>
</footer>

<script>
    /* ── Toggle Endpoint Card ─────────────────────────── */
    function toggleCard(trigger) {
        const card = trigger.closest('.ep-card');
        card.classList.toggle('open');
    }

    /* ── Copy Text to Clipboard ───────────────────────── */
    function copyText(text, el) {
        navigator.clipboard.writeText(text).then(() => {
            showToast('Copied: ' + text);
            if (el) {
                el.classList.add('copied');
                setTimeout(() => el.classList.remove('copied'), 1200);
            }
        });
    }

    /* ── Copy Code Block ──────────────────────────────── */
    function copyCode(btn) {
        const code = btn.getAttribute('data-code');
        const decoded = new DOMParser().parseFromString(code, 'text/html').body.textContent;
        navigator.clipboard.writeText(decoded).then(() => {
            showToast('JSON copied to clipboard');
            btn.classList.add('copied');
            const original = btn.innerHTML;
            btn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:12px;height:12px"><path d="M20 6 9 17l-5-5"/></svg> Copied!';
            setTimeout(() => { btn.classList.remove('copied'); btn.innerHTML = original; }, 1500);
        });
    }

    /* ── Toast ────────────────────────────────────────── */
    let toastTimer;
    function showToast(message) {
        const toast = document.getElementById('toast');
        document.getElementById('toastText').textContent = message;
        toast.classList.add('show');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => toast.classList.remove('show'), 2000);
    }

    /* ── Response Tabs ────────────────────────────────── */
    function switchTab(btn, panelId) {
        const container = btn.closest('.ep-panel');
        container.querySelectorAll('.response-tab').forEach(t => t.classList.remove('active'));
        container.querySelectorAll('.response-panel').forEach(p => p.classList.remove('active'));
        btn.classList.add('active');
        document.getElementById(panelId).classList.add('active');
    }

    /* ── Mobile Navigation ────────────────────────────── */
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('mobileOverlay');

    document.getElementById('mobileToggle').addEventListener('click', () => {
        sidebar.classList.toggle('open');
        overlay.classList.toggle('open');
    });
    overlay.addEventListener('click', closeMobile);

    function closeMobile() {
        sidebar.classList.remove('open');
        overlay.classList.remove('open');
    }

    /* ── Scroll Spy ───────────────────────────────────── */
    const navLinks = document.querySelectorAll('.nav-items li a');
    window.addEventListener('scroll', () => {
        const scrollY = window.scrollY + 120;
        document.querySelectorAll('.ep-card').forEach(card => {
            const top = card.offsetTop;
            const bottom = top + card.offsetHeight;
            if (scrollY >= top && scrollY < bottom) {
                navLinks.forEach(l => l.classList.remove('active'));
                const match = document.querySelector('.nav-items li a[href="#' + card.id + '"]');
                if (match) match.classList.add('active');
            }
        });
    });

    /* ── Smooth scroll ────────────────────────────────── */
    document.querySelectorAll('a[href^="#"]').forEach(a => {
        a.addEventListener('click', e => {
            e.preventDefault();
            const target = document.querySelector(a.getAttribute('href'));
            if (target) {
                window.scrollTo({ top: target.offsetTop - 80, behavior: 'smooth' });
            }
        });
    });

    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeMobile(); });
</script>

</body>
</html>
