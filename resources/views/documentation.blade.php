<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Natakahii API Documentation</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --dark-blue: #1e3a8a;
            --orange: #f97316;
            --white: #ffffff;
            --light-gray: #f8fafc;
            --border-gray: #e2e8f0;
            --success-green: #10b981;
            --danger-red: #dc2626;
            --warning-yellow: #f59e0b;
            --text-dark: #1f2937;
            --text-light: #6b7280;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: var(--text-dark);
            background: var(--light-gray);
        }

        /* Header Styles */
        .header {
            background: linear-gradient(135deg, var(--dark-blue) 0%, #1e40af 100%);
            color: var(--white);
            padding: 2rem 0;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23f97316' fill-opacity='0.05' fill-rule='evenodd'/%3E%3C/svg%3E");
            opacity: 0.3;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1.5rem;
        }

        .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
            position: relative;
            z-index: 1;
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            background: var(--orange);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo-text {
            font-size: 2rem;
            font-weight: 800;
            color: var(--white);
        }

        .logo-text span {
            color: var(--orange);
        }

        .header-info {
            text-align: right;
        }

        .version {
            background: rgba(255,255,255,0.2);
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.85rem;
            display: inline-block;
            margin-bottom: 0.5rem;
        }

        .base-url {
            background: rgba(0,0,0,0.2);
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-family: 'Courier New', monospace;
            font-size: 0.95rem;
            margin-top: 0.5rem;
            display: inline-block;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.1);
        }

        /* Main Content */
        .main-content {
            display: grid;
            grid-template-columns: 1fr;
            gap: 2rem;
            padding: 2rem 0;
        }

        @media (min-width: 992px) {
            .main-content {
                grid-template-columns: 280px 1fr;
            }
        }

        /* Sidebar Navigation */
        .sidebar {
            position: sticky;
            top: 2rem;
            align-self: start;
            background: var(--white);
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
            max-height: calc(100vh - 4rem);
            overflow-y: auto;
        }

        .sidebar h3 {
            color: var(--dark-blue);
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid var(--orange);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-links {
            list-style: none;
        }

        .nav-item {
            margin-bottom: 0.5rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            color: var(--text-dark);
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .nav-link:hover {
            background: var(--light-gray);
            color: var(--dark-blue);
            border-left-color: var(--orange);
        }

        .nav-link.active {
            background: var(--light-gray);
            color: var(--dark-blue);
            font-weight: 600;
            border-left-color: var(--orange);
        }

        /* Content Area */
        .content-area {
            min-width: 0; /* Fix for flexbox overflow */
        }

        .intro {
            background: var(--white);
            padding: 2rem;
            margin-bottom: 2rem;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
            border-left: 6px solid var(--orange);
        }

        .intro h2 {
            color: var(--dark-blue);
            margin-bottom: 1rem;
            font-size: 1.75rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .intro p {
            color: var(--text-light);
            margin-bottom: 1.5rem;
            font-size: 1.05rem;
        }

        .auth-note {
            background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
            border: 1px solid #fcd34d;
            padding: 1.25rem;
            border-radius: 12px;
            margin-top: 1.5rem;
            display: flex;
            align-items: flex-start;
            gap: 1rem;
        }

        .auth-icon {
            flex-shrink: 0;
            background: var(--warning-yellow);
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .auth-note-content h4 {
            color: var(--dark-blue);
            margin-bottom: 0.5rem;
            font-size: 1.1rem;
        }

        /* Endpoint Groups */
        .endpoint-group {
            background: var(--white);
            border-radius: 16px;
            overflow: hidden;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        }

        .group-header {
            background: linear-gradient(135deg, var(--dark-blue) 0%, #1e40af 100%);
            color: var(--white);
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .group-icon {
            flex-shrink: 0;
            width: 48px;
            height: 48px;
            background: rgba(255,255,255,0.1);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .group-title {
            font-size: 1.5rem;
            font-weight: 700;
        }

        .group-description {
            font-size: 0.95rem;
            opacity: 0.9;
            margin-top: 0.25rem;
        }

        /* Endpoint Cards */
        .endpoint-card {
            border-bottom: 1px solid var(--border-gray);
            transition: background-color 0.3s ease;
        }

        .endpoint-card:last-child {
            border-bottom: none;
        }

        .endpoint-card:hover {
            background: #fef9f3;
        }

        .endpoint-header {
            padding: 1.5rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .endpoint-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            flex: 1;
            min-width: 0;
        }

        @media (max-width: 768px) {
            .endpoint-info {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.75rem;
            }
        }

        .method-badge {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.85rem;
            text-transform: uppercase;
            min-width: 70px;
            text-align: center;
            flex-shrink: 0;
        }

        .method-post {
            background: var(--orange);
            color: var(--white);
        }

        .method-get {
            background: var(--success-green);
            color: var(--white);
        }

        .method-put {
            background: var(--warning-yellow);
            color: var(--white);
        }

        .method-delete {
            background: var(--danger-red);
            color: var(--white);
        }

        .endpoint-details {
            flex: 1;
            min-width: 0;
        }

        .endpoint-name {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 0.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .endpoint-url {
            font-family: 'Courier New', monospace;
            color: var(--dark-blue);
            font-size: 0.9rem;
            word-break: break-all;
            background: var(--light-gray);
            padding: 0.5rem 0.75rem;
            border-radius: 6px;
            margin-top: 0.5rem;
            display: inline-block;
        }

        .auth-badge {
            background: var(--danger-red);
            color: var(--white);
            padding: 0.3rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            margin-left: 0.5rem;
        }

        .endpoint-description {
            color: var(--text-light);
            margin-top: 0.5rem;
            font-size: 0.95rem;
        }

        .toggle-icon {
            transition: transform 0.3s ease;
            color: var(--orange);
            flex-shrink: 0;
        }

        .toggle-icon.active {
            transform: rotate(180deg);
        }

        .endpoint-body {
            padding: 0 1.5rem 1.5rem 1.5rem;
            display: none;
        }

        .endpoint-body.active {
            display: block;
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .section-title {
            color: var(--dark-blue);
            font-weight: 600;
            margin: 1.5rem 0 1rem;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .section-title::before {
            content: '';
            width: 6px;
            height: 24px;
            background: var(--orange);
            border-radius: 3px;
        }

        /* Tables */
        .param-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin: 1rem 0;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .param-table th {
            background: var(--dark-blue);
            color: var(--white);
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .param-table td {
            padding: 1rem;
            border-bottom: 1px solid var(--border-gray);
            vertical-align: top;
        }

        .param-table tr:last-child td {
            border-bottom: none;
        }

        .param-table tr:hover {
            background: var(--light-gray);
        }

        .param-name {
            font-family: 'Courier New', monospace;
            color: var(--dark-blue);
            font-weight: 600;
            font-size: 0.9rem;
        }

        /* Code Blocks */
        .code-block {
            background: #1e293b;
            color: #e2e8f0;
            padding: 1.5rem;
            border-radius: 12px;
            overflow-x: auto;
            margin: 1rem 0;
            font-family: 'Courier New', monospace;
            font-size: 0.9rem;
            line-height: 1.6;
            position: relative;
        }

        .code-block::before {
            content: 'JSON';
            position: absolute;
            top: 0.5rem;
            right: 1rem;
            font-size: 0.75rem;
            color: #94a3b8;
            font-weight: 600;
        }

        .code-block pre {
            margin: 0;
            white-space: pre-wrap;
            word-break: break-all;
        }

        /* Footer */
        .footer {
            background: var(--dark-blue);
            color: var(--white);
            padding: 3rem 0 2rem;
            margin-top: 3rem;
            text-align: center;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1.5rem;
        }

        .footer-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }

        .footer-logo .logo-icon {
            width: 32px;
            height: 32px;
        }

        .footer-text {
            font-size: 1.5rem;
            font-weight: 700;
        }

        .footer-text span {
            color: var(--orange);
        }

        .copyright {
            opacity: 0.8;
            margin-top: 1rem;
            font-size: 0.9rem;
        }

        .footer-stack {
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255,255,255,0.1);
            font-size: 0.85rem;
            opacity: 0.7;
        }

        /* Mobile Menu Toggle */
        .mobile-menu-toggle {
            display: none;
            background: var(--orange);
            color: white;
            border: none;
            padding: 0.75rem;
            border-radius: 8px;
            cursor: pointer;
            align-items: center;
            gap: 0.5rem;
            font-weight: 600;
        }

        @media (max-width: 991px) {
            .mobile-menu-toggle {
                display: flex;
            }
            
            .sidebar {
                position: fixed;
                top: 0;
                left: -300px;
                width: 280px;
                height: 100vh;
                z-index: 1000;
                transition: left 0.3s ease;
                border-radius: 0;
                margin: 0;
            }
            
            .sidebar.active {
                left: 0;
            }
            
            .overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0,0,0,0.5);
                z-index: 999;
            }
            
            .overlay.active {
                display: block;
            }
        }

        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                text-align: center;
                gap: 1.5rem;
            }
            
            .header-info {
                text-align: center;
            }
            
            .logo-container {
                justify-content: center;
            }
            
            .base-url {
                font-size: 0.85rem;
                padding: 0.5rem 1rem;
            }
            
            .param-table {
                display: block;
                overflow-x: auto;
            }
            
            .intro {
                padding: 1.5rem;
            }
            
            .endpoint-header {
                padding: 1.25rem;
            }
            
            .endpoint-body {
                padding: 0 1.25rem 1.25rem;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 0 1rem;
            }
            
            .method-badge {
                min-width: 60px;
                padding: 0.4rem 0.75rem;
                font-size: 0.8rem;
            }
            
            .auth-note {
                flex-direction: column;
                gap: 0.75rem;
            }
            
            .code-block {
                padding: 1rem;
                font-size: 0.85rem;
            }
        }

        /* Utility Classes */
        .icon {
            width: 20px;
            height: 20px;
            fill: currentColor;
        }

        .icon-sm {
            width: 16px;
            height: 16px;
        }

        .icon-lg {
            width: 24px;
            height: 24px;
        }

        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }
    </style>
</head>
<body>
    <!-- Mobile Menu Toggle -->
    <button class="mobile-menu-toggle" id="menuToggle">
        <svg class="icon" viewBox="0 0 24 24">
            <path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"/>
        </svg>
        Menu
    </button>

    <!-- Overlay for Mobile -->
    <div class="overlay" id="overlay"></div>

    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo-container">
                    <div class="logo-icon">
                        <svg viewBox="0 0 24 24" fill="white" width="24" height="24">
                            <path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/>
                        </svg>
                    </div>
                    <div class="logo-text">Nata<span>kahii</span></div>
                </div>
                <div class="header-info">
                    <div class="version">v1.0.0</div>
                    <div class="base-url">{{ url('/api') }}</div>
                </div>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="main-content">
            <!-- Sidebar Navigation -->
            <nav class="sidebar" id="sidebar">
                <h3>
                    <svg class="icon icon-lg" viewBox="0 0 24 24">
                        <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
                    </svg>
                    API Endpoints
                </h3>
                <ul class="nav-links" id="navLinks">
                    <!-- Navigation links will be generated by JavaScript -->
                </ul>
            </nav>

            <!-- Main Content Area -->
            <div class="content-area">
                <div class="intro">
                    <h2>
                        <svg class="icon icon-lg" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>
                        </svg>
                        Welcome to Natakahii API Documentation
                    </h2>
                    <p>This documentation provides comprehensive information about all available API endpoints, request formats, and response structures. Our API uses JWT (JSON Web Tokens) for authentication and follows RESTful principles.</p>
                    
                    <div class="auth-note">
                        <div class="auth-icon">
                            <svg class="icon" fill="white" viewBox="0 0 24 24">
                                <path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/>
                            </svg>
                        </div>
                        <div class="auth-note-content">
                            <h4>Authentication Required</h4>
                            <p>Endpoints marked with <span class="auth-badge">
                                <svg class="icon icon-sm" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/>
                                </svg>
                                Auth Required
                            </span> need a valid JWT token in the Authorization header:</p>
                            <div class="code-block" style="margin-top: 0.75rem;">
                                <pre>Authorization: Bearer YOUR_JWT_TOKEN</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dynamic Content -->
                @foreach($endpoints as $group)
                    <div class="endpoint-group" id="group-{{ Str::slug($group['group']) }}">
                        <div class="group-header">
                            <div class="group-icon">
                                @if($group['group'] == 'Authentication')
                                    <svg class="icon icon-lg" fill="white" viewBox="0 0 24 24">
                                        <path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2z"/>
                                    </svg>
                                @elseif($group['group'] == 'Products')
                                    <svg class="icon icon-lg" fill="white" viewBox="0 0 24 24">
                                        <path d="M16 6l2.29 2.29-4.88 4.88-4-4L2 16.59 3.41 18l6-6 4 4 6.3-6.29L22 12V6z"/>
                                    </svg>
                                @elseif($group['group'] == 'Users')
                                    <svg class="icon icon-lg" fill="white" viewBox="0 0 24 24">
                                        <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                                    </svg>
                                @else
                                    <svg class="icon icon-lg" fill="white" viewBox="0 0 24 24">
                                        <path d="M4 6h18V4H4c-1.1 0-2 .9-2 2v11H0v3h14v-3H4V6zm19 2h-6c-.55 0-1 .45-1 1v10c0 .55.45 1 1 1h6c.55 0 1-.45 1-1V9c0-.55-.45-1-1-1zm-1 9h-4v-7h4v7z"/>
                                    </svg>
                                @endif
                            </div>
                            <div>
                                <div class="group-title">{{ $group['group'] }}</div>
                                <div class="group-description">{{ $group['description'] ?? 'API endpoints for ' . $group['group'] }}</div>
                            </div>
                        </div>
                        
                        @foreach($group['endpoints'] as $endpoint)
                            <div class="endpoint-card">
                                <div class="endpoint-header" onclick="toggleEndpoint(this)" data-group="{{ Str::slug($group['group']) }}">
                                    <div class="endpoint-info">
                                        <span class="method-badge method-{{ strtolower($endpoint['method']) }}">
                                            {{ $endpoint['method'] }}
                                        </span>
                                        <div class="endpoint-details">
                                            <div class="endpoint-name">
                                                {{ $endpoint['name'] }}
                                                @if($endpoint['auth_required'])
                                                    <span class="auth-badge">
                                                        <svg class="icon icon-sm" viewBox="0 0 24 24" fill="currentColor">
                                                            <path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2z"/>
                                                        </svg>
                                                        Auth Required
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="endpoint-url">{{ $endpoint['url'] }}</div>
                                            <div class="endpoint-description">{{ $endpoint['description'] }}</div>
                                        </div>
                                    </div>
                                    <span class="toggle-icon">
                                        <svg class="icon" viewBox="0 0 24 24">
                                            <path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"/>
                                        </svg>
                                    </span>
                                </div>
                                
                                <div class="endpoint-body">
                                    @if(count($endpoint['request']) > 0)
                                        <div class="section-title">
                                            <svg class="icon" viewBox="0 0 24 24">
                                                <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>
                                            </svg>
                                            Request Parameters
                                        </div>
                                        <table class="param-table">
                                            <thead>
                                                <tr>
                                                    <th>Parameter</th>
                                                    <th>Type</th>
                                                    <th>Validation Rules</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($endpoint['request'] as $param => $rules)
                                                    <tr>
                                                        <td class="param-name">{{ $param }}</td>
                                                        <td>
                                                            @if(str_contains(strtolower($rules), 'string')) String
                                                            @elseif(str_contains(strtolower($rules), 'integer')) Integer
                                                            @elseif(str_contains(strtolower($rules), 'boolean')) Boolean
                                                            @elseif(str_contains(strtolower($rules), 'array')) Array
                                                            @elseif(str_contains(strtolower($rules), 'email')) Email
                                                            @elseif(str_contains(strtolower($rules), 'password')) Password
                                                            @else String
                                                            @endif
                                                        </td>
                                                        <td>{{ $rules }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @endif

                                    <div class="section-title">
                                        <svg class="icon" viewBox="0 0 24 24">
                                            <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/>
                                        </svg>
                                        Response Example
                                    </div>
                                    <div class="code-block">
                                        <pre>{{ json_encode($endpoint['response'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="footer-content">
            <div class="footer-logo">
                <div class="logo-icon">
                    <svg viewBox="0 0 24 24" fill="white" width="20" height="20">
                        <path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/>
                    </svg>
                </div>
                <div class="footer-text">Nata<span>kahii</span></div>
            </div>
            <p class="copyright">&copy; {{ date('Y') }} Natakahii. All rights reserved.</p>
            <p class="footer-stack">Built with Laravel & JWT Authentication</p>
        </div>
    </footer>

    <script>
        // Toggle endpoint details
        function toggleEndpoint(header) {
            const body = header.parentElement.querySelector('.endpoint-body');
            const icon = header.querySelector('.toggle-icon');
            
            body.classList.toggle('active');
            icon.classList.toggle('active');
            
            // Close other open endpoints in the same group
            const group = header.getAttribute('data-group');
            const groupElement = document.getElementById(`group-${group}`);
            const allEndpoints = groupElement.querySelectorAll('.endpoint-header');
            
            allEndpoints.forEach(otherHeader => {
                if (otherHeader !== header) {
                    const otherBody = otherHeader.parentElement.querySelector('.endpoint-body');
                    const otherIcon = otherHeader.querySelector('.toggle-icon');
                    if (otherBody.classList.contains('active')) {
                        otherBody.classList.remove('active');
                        otherIcon.classList.remove('active');
                    }
                }
            });
        }

        // Generate navigation links
        function generateNavigation() {
            const groups = document.querySelectorAll('.endpoint-group');
            const navLinks = document.getElementById('navLinks');
            
            groups.forEach(group => {
                const groupId = group.id;
                const groupTitle = group.querySelector('.group-title').textContent;
                const groupIcon = group.querySelector('.group-icon svg').outerHTML;
                
                // Create group link
                const li = document.createElement('li');
                li.className = 'nav-item';
                li.innerHTML = `
                    <a href="#${groupId}" class="nav-link" onclick="closeMobileMenu()">
                        ${groupIcon}
                        <span>${groupTitle}</span>
                    </a>
                `;
                navLinks.appendChild(li);
            });
        }

        // Mobile menu functionality
        const menuToggle = document.getElementById('menuToggle');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');

        function toggleMobileMenu() {
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
            document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : '';
        }

        function closeMobileMenu() {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        }

        menuToggle.addEventListener('click', toggleMobileMenu);
        overlay.addEventListener('click', closeMobileMenu);

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;
                
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 20,
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Initialize navigation and add active class to current section
        document.addEventListener('DOMContentLoaded', function() {
            generateNavigation();
            
            // Set first nav link as active
            const firstNavLink = document.querySelector('.nav-link');
            if (firstNavLink) {
                firstNavLink.classList.add('active');
            }
            
            // Update active nav link on scroll
            window.addEventListener('scroll', function() {
                const scrollPosition = window.scrollY + 100;
                const groups = document.querySelectorAll('.endpoint-group');
                
                groups.forEach(group => {
                    const groupTop = group.offsetTop;
                    const groupBottom = groupTop + group.offsetHeight;
                    const groupId = group.id;
                    
                    if (scrollPosition >= groupTop && scrollPosition < groupBottom) {
                        document.querySelectorAll('.nav-link').forEach(link => {
                            link.classList.remove('active');
                        });
                        const activeLink = document.querySelector(`.nav-link[href="#${groupId}"]`);
                        if (activeLink) {
                            activeLink.classList.add('active');
                        }
                    }
                });
            });
            
            // Close mobile menu on escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeMobileMenu();
                }
            });
            
            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth > 991) {
                    closeMobileMenu();
                }
            });
        });
    </script>
</body>
</html>