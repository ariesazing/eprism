<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'E-PRISM') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Manrope:wght@600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --brand-700: #6a11cb;
            --brand-600: #7b2fd0;
            --brand-500: #9476da;
            --ink-900: #191336;
            --ink-700: #423a66;
            --ink-500: #6b648d;
            --surface: #ffffff;
            --line: #e9e3f5;
            --ok: #18794e;
            --warn: #b26a00;
            --danger: #b42318;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            color: var(--ink-900);
            background:
                radial-gradient(circle at 92% 10%, rgba(123, 47, 208, 0.14), transparent 44%),
                radial-gradient(circle at 8% 86%, rgba(148, 118, 218, 0.18), transparent 40%),
                linear-gradient(160deg, #f8f3ff 0%, #fdfbff 38%, #ffffff 100%);
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 22px;
        }

        .topbar {
            position: sticky;
            top: 0;
            z-index: 40;
            backdrop-filter: blur(8px);
            background: rgba(255, 255, 255, 0.75);
            border-bottom: 1px solid rgba(233, 227, 245, 0.9);
        }

        .topbar-inner {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
            min-height: 74px;
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            color: var(--ink-900);
            font-weight: 700;
            text-decoration: none;
        }

        .brand-mark {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            display: grid;
            place-items: center;
            color: #fff;
            font-weight: 800;
            font-family: 'Manrope', sans-serif;
            background: linear-gradient(140deg, var(--brand-700), #8e4edd);
            box-shadow: 0 10px 24px rgba(106, 17, 203, 0.26);
        }

        .brand-copy strong {
            display: block;
            letter-spacing: 0.2px;
            font-size: 15px;
        }

        .brand-copy span {
            display: block;
            color: var(--ink-500);
            font-size: 12px;
            font-weight: 500;
        }

        .top-actions {
            display: flex;
            gap: 9px;
            flex-wrap: wrap;
        }

        .btn {
            text-decoration: none;
            border-radius: 12px;
            padding: 11px 16px;
            font-weight: 600;
            font-size: 13px;
            border: 1px solid transparent;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: transform .2s ease, box-shadow .25s ease, background .25s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .btn-secondary {
            border-color: #d8ccf0;
            color: var(--brand-700);
            background: #f8f4ff;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--brand-700), #7b2fd0 62%, #8a44d7);
            color: #fff;
            box-shadow: 0 14px 30px rgba(106, 17, 203, 0.3);
        }

        .btn-primary:hover {
            box-shadow: 0 16px 34px rgba(106, 17, 203, 0.36);
        }

        .hero {
            display: block;
            padding-top: 34px;
        }

        .hero-left {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 24px;
            box-shadow: 0 18px 48px rgba(26, 17, 56, 0.1);
            padding: 34px;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border-radius: 999px;
            border: 1px solid #dbcdf6;
            background: #fbf8ff;
            color: var(--brand-700);
            padding: 7px 12px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.3px;
        }

        .eyebrow-dot {
            width: 7px;
            height: 7px;
            border-radius: 999px;
            background: #8c56da;
        }

        h1 {
            margin: 14px 0 10px;
            font-family: 'Manrope', sans-serif;
            font-weight: 800;
            font-size: clamp(34px, 4.3vw, 56px);
            line-height: 1.02;
            letter-spacing: -0.03em;
        }

        .lead {
            margin: 0;
            color: var(--ink-700);
            font-size: clamp(15px, 1.7vw, 18px);
            line-height: 1.67;
            max-width: 56ch;
        }

        .hero-cta {
            margin-top: 24px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .trust-row {
            margin-top: 16px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
        }

        .trust-pill,
        .compliance-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            font-weight: 600;
            padding: 7px 11px;
            border-radius: 999px;
            border: 1px solid #ddd2f4;
            background: #fff;
            color: #4d4674;
        }

        .compliance-pill {
            background: #f8f4ff;
            color: #4a3483;
        }

        .preview-shell {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .preview-head {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            gap: 12px;
        }

        .preview-head h2 {
            margin: 0;
            font-family: 'Manrope', sans-serif;
            font-size: 20px;
        }

        .preview-head span {
            color: var(--ink-500);
            font-size: 13px;
        }

        .progress-card,
        .mini-metric,
        .activity-card,
        .badge-card {
            background: #fbf9ff;
            border: 1px solid var(--line);
            border-radius: 18px;
            padding: 14px;
        }

        .progress-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
            font-size: 13px;
            color: var(--ink-700);
            font-weight: 600;
        }

        .progress-track {
            height: 10px;
            background: #eae4f8;
            border-radius: 999px;
            overflow: hidden;
        }

        .progress-fill {
            width: 72%;
            height: 100%;
            background: linear-gradient(90deg, var(--brand-700), #8d4fdd);
            border-radius: inherit;
        }

        .preview-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .mini-metric strong {
            display: block;
            font-size: 24px;
            font-family: 'Manrope', sans-serif;
            margin-bottom: 2px;
        }

        .mini-metric span {
            font-size: 12px;
            color: var(--ink-500);
        }

        .activity-card h3,
        .badge-card h3 {
            margin: 0 0 10px;
            font-size: 14px;
            color: var(--ink-700);
        }

        .activity-list {
            margin: 0;
            padding: 0;
            list-style: none;
            display: grid;
            gap: 8px;
            font-size: 13px;
            color: #544d78;
        }

        .activity-list li {
            display: flex;
            justify-content: space-between;
            gap: 8px;
        }

        .status-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 7px;
        }

        .badge {
            font-size: 11px;
            font-weight: 700;
            border-radius: 999px;
            padding: 6px 9px;
            border: 1px solid transparent;
        }

        .badge-pending {
            color: #6a4a00;
            background: #fff4db;
            border-color: #f7ddaa;
        }

        .badge-review {
            color: #24508f;
            background: #e8f2ff;
            border-color: #bdd8ff;
        }

        .badge-revision {
            color: #8a2e2e;
            background: #ffecef;
            border-color: #ffcad3;
        }

        .badge-approved {
            color: #1b6a48;
            background: #e8f8ef;
            border-color: #b9e5ca;
        }

        .section {
            margin-top: 24px;
            padding: 0 0 4px;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
        }

        .feature-card {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 22px;
            padding: 20px;
            box-shadow: 0 12px 32px rgba(26, 17, 56, 0.08);
            transition: transform .22s ease, box-shadow .22s ease;
        }

        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 18px 35px rgba(26, 17, 56, 0.12);
        }

        .feature-icon {
            width: 42px;
            height: 42px;
            border-radius: 14px;
            display: grid;
            place-items: center;
            background: #f2ebff;
            color: var(--brand-700);
            margin-bottom: 12px;
        }

        .feature-card h3 {
            margin: 0 0 6px;
            font-size: 18px;
            font-family: 'Manrope', sans-serif;
        }

        .feature-card p {
            margin: 0;
            color: var(--ink-700);
            line-height: 1.6;
            font-size: 14px;
        }

        .stats-strip {
            margin-top: 20px;
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 20px;
            box-shadow: 0 12px 32px rgba(26, 17, 56, 0.08);
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0;
            overflow: hidden;
        }

        .stat-item {
            padding: 18px 20px;
            text-align: center;
        }

        .stat-item + .stat-item {
            border-left: 1px solid var(--line);
        }

        .stat-item strong {
            display: block;
            font-family: 'Manrope', sans-serif;
            font-size: 30px;
            line-height: 1;
            color: var(--brand-700);
            margin-bottom: 4px;
        }

        .stat-item span {
            color: var(--ink-600);
            font-size: 13px;
            font-weight: 600;
        }

        .workflow {
            margin-top: 24px;
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 22px;
            box-shadow: 0 12px 32px rgba(26, 17, 56, 0.08);
            padding: 22px;
        }

        .workflow h2 {
            margin: 0 0 16px;
            font-family: 'Manrope', sans-serif;
            font-size: 22px;
        }

        .workflow-steps {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
        }

        .step {
            background: #faf7ff;
            border: 1px solid #e7ddf8;
            border-radius: 16px;
            padding: 14px;
            font-size: 14px;
            font-weight: 700;
            color: #463f69;
            text-align: center;
            position: relative;
        }

        .step::after {
            content: '';
            position: absolute;
            right: -8px;
            top: 50%;
            width: 16px;
            height: 2px;
            background: #d6c8f3;
            transform: translateY(-50%);
        }

        .step:last-child::after {
            display: none;
        }

        .footer {
            margin-top: 26px;
            padding: 20px 0 30px;
            color: #5e577f;
            font-size: 13px;
        }

        .footer-shell {
            border-top: 1px solid #e7dff5;
            padding-top: 16px;
            display: flex;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }

        .footer strong {
            color: #3b3360;
        }

        @media (max-width: 1040px) {
            .feature-grid {
                grid-template-columns: 1fr;
            }

            .workflow-steps {
                grid-template-columns: 1fr 1fr;
            }

            .step::after {
                display: none;
            }
        }

        @media (max-width: 740px) {
            .topbar-inner {
                min-height: auto;
                padding: 12px 0;
                align-items: flex-start;
                flex-direction: column;
            }

            .hero-left {
                padding: 24px;
                border-radius: 20px;
            }

            .preview-grid,
            .stats-strip,
            .workflow-steps {
                grid-template-columns: 1fr;
            }

            .stat-item + .stat-item {
                border-left: none;
                border-top: 1px solid var(--line);
            }

            .hero-cta {
                flex-direction: column;
            }

            .hero-cta .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <header class="topbar">
        <div class="container topbar-inner">
            <a href="{{ url('/') }}" class="brand" aria-label="E-PRISM Home">
                <span class="brand-mark">E</span>
                <span class="brand-copy">
                    <strong>E-PRISM Portal</strong>
                    <span>Schools Division Office of Santiago City</span>
                </span>
            </a>

            @if (Route::has('login'))
                <nav class="top-actions" aria-label="Primary Navigation">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn btn-primary">Open Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-secondary">Login</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn btn-primary">Create Account</a>
                        @endif
                    @endauth
                </nav>
            @endif
        </div>
    </header>

    <main class="container">
        <section class="hero">
            <div class="hero-left">
                <span class="eyebrow">
                    <span class="eyebrow-dot" aria-hidden="true"></span>
                    DepEd Research Submission and Review Management Portal
                </span>

                <h1>E-PRISM streamlining DepEd research workflows.</h1>

                <p class="lead">
                    Submit manuscripts, manage reviews, track revisions, and process approvals in one centralized portal built for researchers, reviewers, and administrators.
                </p>

                <div class="hero-cta">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn btn-primary">Login</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary">Login</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn btn-secondary">Create Account</a>
                        @endif
                    @endauth
                </div>

            </div>
        </section>

        <section class="section" aria-label="Key Features">
            <div class="feature-grid">
                <article class="feature-card">
                    <div class="feature-icon" aria-hidden="true">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M6 3.75h9l3 3v13.5H6V3.75z" stroke="currentColor" stroke-width="1.8"/>
                            <path d="M15 3.75v3h3" stroke="currentColor" stroke-width="1.8"/>
                        </svg>
                    </div>
                    <h3>Submission</h3>
                    <p>Upload and track research manuscripts with clear routing and transparent status updates.</p>
                </article>

                <article class="feature-card">
                    <div class="feature-icon" aria-hidden="true">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M8 10h8M8 14h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                            <path d="M4.5 5.25h15v13.5h-15z" stroke="currentColor" stroke-width="1.8"/>
                        </svg>
                    </div>
                    <h3>Review</h3>
                    <p>Provide structured evaluation, consolidated comments, and actionable review feedback.</p>
                </article>

                <article class="feature-card">
                    <div class="feature-icon" aria-hidden="true">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M6 12.5l3.2 3.2L18 7.9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            <rect x="3.75" y="3.75" width="16.5" height="16.5" rx="2.5" stroke="currentColor" stroke-width="1.8"/>
                        </svg>
                    </div>
                    <h3>Approval</h3>
                    <p>Approve, archive, and generate permanent records aligned to DepEd governance standards.</p>
                </article>
            </div>
        </section>

        <section class="workflow" aria-label="Workflow Process">
            <h2>Research Process Workflow</h2>
            <div class="workflow-steps">
                <div class="step">Submit</div>
                <div class="step">Review</div>
                <div class="step">Revise</div>
                <div class="step">Approve</div>
            </div>
        </section>

        <footer class="footer">
            <div class="footer-shell">
                <div>
                    <strong>E-PRISM | E-Portal for Research Initiative Submission Management</strong>
                    <div>Schools Division Office of Santiago City, Department of Education</div>
                </div>
                <div>
                    <strong>DepEd Alignment</strong>
                    <div>Research governance, compliance tracking, and transparent academic records management.</div>
                </div>
            </div>
        </footer>
    </main>
</body>
</html>
