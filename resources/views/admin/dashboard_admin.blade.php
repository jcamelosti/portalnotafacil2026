<x-area-admin-layout title="Dashboard Administrativo">
    @php
        $fmtMoeda = function ($v) { return 'R$ ' . number_format((float) $v, 2, ',', '.'); };
        $fmtNum = function ($v) { return number_format((float) $v, 0, ',', '.'); };
        $fmtDoc = function ($doc) {
            $d = preg_replace('/\D/', '', (string) $doc);
            if (strlen($d) === 14) return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $d);
            if (strlen($d) === 11) return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $d);
            return $doc;
        };
        $nomeEmpresa = function ($e) { return $e->nome_fantasia ?: $e->razao_social; };
        $iniciais = function ($nome) {
            $partes = preg_split('/\s+/', trim((string) $nome));
            $ini = mb_substr($partes[0] ?? '', 0, 1) . mb_substr($partes[1] ?? '', 0, 1);
            return mb_strtoupper($ini ?: '?');
        };

        // Donut de distribuição das licenças
        $segmentos = [
            ['label' => 'Ativas',                         'valor' => $licencas['ativas'],      'cor' => '#10b981'],
            ['label' => "A vencer ({$diasAVencer} dias)", 'valor' => $licencas['a_vencer'],    'cor' => '#f59e0b'],
            ['label' => 'Vencidas',                       'valor' => $licencas['vencidas'],    'cor' => '#ef4444'],
            ['label' => 'Sem licença',                    'valor' => $licencas['sem_licenca'], 'cor' => '#94a3b8'],
        ];
        $circ = 2 * M_PI * 52;
        $offset = 0;
        foreach ($segmentos as $i => $s) {
            $frac = $licencas['total'] > 0 ? $s['valor'] / $licencas['total'] : 0;
            $segmentos[$i]['dash'] = $frac * $circ;
            $segmentos[$i]['offset'] = -$offset;
            $segmentos[$i]['pct'] = round($frac * 100, 1);
            $offset += $frac * $circ;
        }

        // Gráfico mensal
        $maxQtd = max(1, collect($serieMensal)->max('qtd'));
        $maxValor = max(1, collect($serieMensal)->max('valor'));
        $totalQtd12 = collect($serieMensal)->sum('qtd');
        $totalValor12 = collect($serieMensal)->sum('valor');

        $tendencia = function ($v) {
            if ($v === null) return ['classe' => 'up', 'texto' => 'novo'];
            if ($v > 0) return ['classe' => 'up', 'texto' => '+' . number_format($v, 1, ',', '.') . '%'];
            if ($v < 0) return ['classe' => 'down', 'texto' => number_format($v, 1, ',', '.') . '%'];
            return ['classe' => 'flat', 'texto' => '0%'];
        };
        $tQtd = $tendencia($notas['var_qtd']);
        $tValor = $tendencia($notas['var_valor']);

        $statusLicenca = function ($validate) use ($hoje, $diasAVencer) {
            if (!$validate) return ['classe' => 'muted', 'texto' => 'Sem licença'];
            $d = \Carbon\Carbon::parse($validate);
            if ($d->lt($hoje)) return ['classe' => 'danger', 'texto' => 'Vencida'];
            if ($d->lte($hoje->copy()->addDays($diasAVencer))) return ['classe' => 'warn', 'texto' => 'A vencer'];
            return ['classe' => 'ok', 'texto' => 'Ativa'];
        };
    @endphp

    <style>
        .bi { --ink:#0f172a; --ink-2:#334155; --muted:#64748b; --line:#e2e8f0; --card:#fff;
              --brand:#7c3aed; --brand-2:#4f46e5; --ok:#10b981; --warn:#f59e0b; --danger:#ef4444;
              font-family: 'Inter', system-ui, sans-serif; color: var(--ink); }
        .bi * { box-sizing: border-box; }
        .bi-scroll { height: 100%; overflow-y: auto; background: #f1f5f9; }
        .bi-wrap { max-width: 1440px; margin: 0 auto; padding: 0 24px 48px; }

        /* Hero */
        .bi-hero { position: relative; overflow: hidden; color: #fff; padding: 32px 24px 112px;
                   background: radial-gradient(1200px 400px at 85% -10%, rgba(167,139,250,.55), transparent 60%),
                               radial-gradient(800px 300px at 0% 110%, rgba(56,189,248,.35), transparent 60%),
                               linear-gradient(135deg, #1e1b4b 0%, #312e81 45%, #4c1d95 100%); }
        .bi-hero::after { content:''; position:absolute; inset:0; opacity:.12; pointer-events:none;
                          background-image: linear-gradient(rgba(255,255,255,.5) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.5) 1px, transparent 1px);
                          background-size: 40px 40px; mask-image: linear-gradient(to bottom, #000, transparent); -webkit-mask-image: linear-gradient(to bottom, #000, transparent); }
        .bi-hero-in { position: relative; z-index: 1; max-width: 1392px; margin: 0 auto; display:flex; flex-wrap:wrap; gap:24px; align-items:flex-end; justify-content:space-between; }
        .bi-eyebrow { font-size: 11px; letter-spacing: .18em; text-transform: uppercase; color: #c4b5fd; font-weight: 700; }
        .bi-title { font-size: 30px; font-weight: 800; letter-spacing: -.02em; margin: 6px 0 4px; line-height: 1.15; }
        .bi-sub { color: #ddd6fe; font-size: 14px; }
        .bi-hero-stats { display:flex; gap: 12px; flex-wrap: wrap; }
        .bi-pill { background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.18); backdrop-filter: blur(6px);
                   border-radius: 14px; padding: 10px 16px; min-width: 130px; }
        .bi-pill small { display:block; font-size: 11px; color:#c4b5fd; text-transform: uppercase; letter-spacing:.08em; font-weight:600; }
        .bi-pill strong { font-size: 20px; font-weight: 800; }
        .bi-live { display:inline-flex; align-items:center; gap:8px; font-size:12px; color:#e9d5ff; }
        .bi-live i { width:8px; height:8px; border-radius:50%; background:#4ade80; box-shadow:0 0 0 0 rgba(74,222,128,.7); animation: bi-pulse 2s infinite; }
        @keyframes bi-pulse { 0%{box-shadow:0 0 0 0 rgba(74,222,128,.6)} 70%{box-shadow:0 0 0 10px rgba(74,222,128,0)} 100%{box-shadow:0 0 0 0 rgba(74,222,128,0)} }

        /* KPIs */
        .bi-kpis { display:grid; grid-template-columns: repeat(4, minmax(0,1fr)); gap: 16px; margin-top: -84px; position: relative; z-index: 2; }
        .bi-kpi { position:relative; background: var(--card); border-radius: 18px; padding: 18px 20px; overflow:hidden; text-decoration:none; color:inherit;
                  box-shadow: 0 1px 2px rgba(15,23,42,.04), 0 12px 32px -12px rgba(15,23,42,.18); transition: transform .2s, box-shadow .2s; display:block; }
        .bi-kpi:hover { transform: translateY(-3px); box-shadow: 0 2px 4px rgba(15,23,42,.06), 0 22px 40px -14px rgba(15,23,42,.28); }
        .bi-kpi::before { content:''; position:absolute; left:0; top:0; bottom:0; width:4px; background: var(--accent); }
        .bi-kpi-head { display:flex; align-items:center; justify-content:space-between; }
        .bi-kpi-label { font-size: 12px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: .06em; }
        .bi-ico { width: 38px; height: 38px; border-radius: 12px; display:grid; place-items:center; background: var(--accent-soft); color: var(--accent); }
        .bi-ico svg { width: 20px; height: 20px; }
        .bi-kpi-value { font-size: 30px; font-weight: 800; letter-spacing: -.03em; margin-top: 8px; line-height:1.1; }
        .bi-kpi-foot { margin-top: 8px; font-size: 12px; color: var(--muted); display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
        .bi-trend { font-weight: 700; padding: 2px 8px; border-radius: 999px; font-size: 11px; }
        .bi-trend.up { background:#dcfce7; color:#15803d; } .bi-trend.down { background:#fee2e2; color:#b91c1c; } .bi-trend.flat { background:#f1f5f9; color:#475569; }

        /* Grid */
        .bi-grid { display:grid; gap: 20px; margin-top: 20px; }
        .bi-g-2-1 { grid-template-columns: minmax(0,2fr) minmax(0,1fr); }
        .bi-g-1-1 { grid-template-columns: minmax(0,1fr) minmax(0,1fr); }
        .bi-card { background: var(--card); border-radius: 18px; box-shadow: 0 1px 2px rgba(15,23,42,.04), 0 8px 24px -12px rgba(15,23,42,.12); overflow:hidden; }
        .bi-card-h { padding: 18px 22px; display:flex; align-items:center; justify-content:space-between; gap:12px; border-bottom: 1px solid var(--line); }
        .bi-card-h h3 { font-size: 16px; font-weight: 800; margin:0; letter-spacing:-.01em; }
        .bi-card-h p { font-size: 12px; color: var(--muted); margin: 2px 0 0; }
        .bi-card-b { padding: 20px 22px; }
        .bi-link { font-size: 12px; font-weight: 700; color: var(--brand); text-decoration: none; white-space:nowrap; }
        .bi-link:hover { text-decoration: underline; }

        /* Chart */
        .bi-toggle { display:inline-flex; background:#f1f5f9; border-radius: 10px; padding: 3px; }
        .bi-toggle button { border:0; background:transparent; font-size:12px; font-weight:700; color:var(--muted); padding:6px 12px; border-radius:8px; cursor:pointer; }
        .bi-toggle button.on { background:#fff; color: var(--ink); box-shadow: 0 1px 3px rgba(15,23,42,.12); }
        .bi-chart { display:flex; align-items:flex-end; gap: 10px; height: 230px; padding-top: 28px; border-bottom: 1px dashed var(--line);
                    background-image: linear-gradient(var(--line) 1px, transparent 1px); background-size: 100% 25%; background-position: 0 28px; }
        .bi-col { flex:1; height:100%; display:flex; flex-direction:column; justify-content:flex-end; align-items:center; position:relative; }
        .bi-bar { width: 100%; max-width: 44px; border-radius: 10px 10px 4px 4px; min-height: 3px; transition: height .5s cubic-bezier(.2,.8,.2,1);
                  background: linear-gradient(180deg, #a78bfa 0%, #6d28d9 100%); opacity:.55; cursor:pointer; }
        .bi-col.atual .bi-bar { opacity: 1; background: linear-gradient(180deg, #c084fc 0%, #4f46e5 100%); box-shadow: 0 8px 20px -6px rgba(124,58,237,.6); }
        .bi-col:hover .bi-bar { opacity: 1; }
        .bi-tip { position:absolute; bottom: calc(100% + 6px); left:50%; transform: translateX(-50%); background: var(--ink); color:#fff; font-size: 11px;
                  padding: 6px 10px; border-radius: 8px; white-space:nowrap; opacity:0; pointer-events:none; transition: opacity .15s; z-index: 5; line-height:1.4; text-align:center; }
        .bi-col:hover .bi-tip { opacity: 1; }
        .bi-axis { display:flex; gap:10px; margin-top: 8px; }
        .bi-axis span { flex:1; text-align:center; font-size: 11px; color: var(--muted); font-weight:600; }
        .bi-axis span.atual { color: var(--brand); font-weight: 800; }
        .bi-chart-sum { display:flex; gap: 28px; margin-top: 18px; flex-wrap:wrap; }
        .bi-chart-sum div small { display:block; font-size: 11px; color: var(--muted); text-transform:uppercase; letter-spacing:.06em; font-weight:700; }
        .bi-chart-sum div strong { font-size: 18px; font-weight: 800; }

        /* Donut */
        .bi-donut { position:relative; width: 190px; height: 190px; margin: 0 auto; }
        .bi-donut svg { transform: rotate(-90deg); }
        .bi-donut-c { position:absolute; inset:0; display:flex; flex-direction:column; align-items:center; justify-content:center; }
        .bi-donut-c strong { font-size: 34px; font-weight: 800; letter-spacing:-.03em; }
        .bi-donut-c small { font-size: 11px; color: var(--muted); text-transform:uppercase; letter-spacing:.08em; font-weight:700; }
        .bi-legend { margin-top: 18px; display:grid; gap: 10px; }
        .bi-legend a { display:flex; align-items:center; gap:10px; font-size:13px; text-decoration:none; color: var(--ink-2); padding: 6px 8px; border-radius: 8px; }
        .bi-legend a:hover { background:#f8fafc; }
        .bi-legend i { width:10px; height:10px; border-radius:3px; flex-shrink:0; }
        .bi-legend b { margin-left:auto; font-weight:800; color:var(--ink); }
        .bi-legend em { font-style:normal; color: var(--muted); font-size: 12px; width: 48px; text-align:right; }

        /* Tables / lists */
        .bi-table { width:100%; border-collapse: collapse; font-size: 13px; }
        .bi-table th { text-align:left; font-size: 11px; text-transform: uppercase; letter-spacing: .06em; color: var(--muted); font-weight: 700; padding: 10px 22px; background:#f8fafc; }
        .bi-table td { padding: 12px 22px; border-top: 1px solid #f1f5f9; vertical-align: middle; }
        .bi-table tr:hover td { background: #faf5ff; }
        .bi-emp { display:flex; align-items:center; gap: 12px; min-width: 0; }
        .bi-av { width: 36px; height: 36px; border-radius: 11px; display:grid; place-items:center; font-size: 12px; font-weight: 800; color:#fff; flex-shrink:0;
                 background: linear-gradient(135deg, #8b5cf6, #4f46e5); }
        .bi-emp-n { font-weight: 700; color: var(--ink); white-space: nowrap; overflow:hidden; text-overflow: ellipsis; max-width: 260px; }
        .bi-emp-d { font-size: 11px; color: var(--muted); font-family: ui-monospace, SFMono-Regular, Menlo, monospace; }
        .bi-badge { display:inline-flex; align-items:center; gap:6px; font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 999px; white-space:nowrap; }
        .bi-badge::before { content:''; width:6px; height:6px; border-radius:50%; background: currentColor; }
        .bi-badge.ok { background:#dcfce7; color:#15803d; } .bi-badge.warn { background:#fef3c7; color:#b45309; }
        .bi-badge.danger { background:#fee2e2; color:#b91c1c; } .bi-badge.muted { background:#f1f5f9; color:#475569; }
        .bi-num { font-variant-numeric: tabular-nums; font-weight: 700; }
        .bi-right { text-align: right; }
        .bi-mute { color: var(--muted); font-size: 12px; }
        .bi-act { display:inline-flex; align-items:center; gap:4px; font-size: 12px; font-weight: 700; color: var(--brand); text-decoration:none; padding: 6px 10px; border-radius: 8px; border:1px solid #ede9fe; white-space:nowrap; }
        .bi-act:hover { background:#f5f3ff; }
        .bi-empty { padding: 36px 22px; text-align:center; color: var(--muted); font-size: 13px; }
        .bi-empty svg { width: 36px; height: 36px; margin: 0 auto 8px; display:block; color:#cbd5e1; }

        /* Exclusão */
        .bi-danger-card { border: 1px solid #fecaca; }
        .bi-danger-h { background: linear-gradient(135deg, #7f1d1d 0%, #b91c1c 55%, #ef4444 100%); color:#fff; border-bottom: 0; }
        .bi-danger-h p { color: #fecaca; }
        .bi-danger-h .bi-count { background: rgba(255,255,255,.18); border:1px solid rgba(255,255,255,.3); padding: 6px 14px; border-radius: 999px; font-weight: 800; font-size: 14px; white-space:nowrap; }
        .bi-siren { width: 40px; height: 40px; border-radius: 12px; display:grid; place-items:center; background: rgba(255,255,255,.15); flex-shrink:0; }
        .bi-siren svg { width: 22px; height: 22px; animation: bi-shake 2.4s infinite; }
        @keyframes bi-shake { 0%,90%,100%{transform:rotate(0)} 92%{transform:rotate(-12deg)} 94%{transform:rotate(12deg)} 96%{transform:rotate(-8deg)} 98%{transform:rotate(8deg)} }
        .bi-x-list { max-height: 420px; overflow-y: auto; }
        .bi-x-item { display:flex; align-items:center; gap: 14px; padding: 14px 22px; border-top: 1px solid #fef2f2; }
        .bi-x-item:first-child { border-top: 0; }
        .bi-x-item:hover { background: #fff7f7; }
        .bi-x-days { text-align:center; min-width: 64px; padding: 6px 8px; border-radius: 12px; background:#fef2f2; color:#b91c1c; }
        .bi-x-days strong { display:block; font-size: 20px; font-weight: 800; line-height: 1; }
        .bi-x-days small { font-size: 10px; text-transform: uppercase; font-weight:700; letter-spacing:.06em; }
        .bi-x-item.critico .bi-x-days { background:#b91c1c; color:#fff; }
        .bi-x-meta { flex:1; min-width:0; }

        /* Feed */
        .bi-feed-item { display:flex; align-items:center; gap:14px; padding: 13px 22px; border-top: 1px solid #f1f5f9; }
        .bi-feed-item:first-child { border-top: 0; }
        .bi-feed-item:hover { background: #faf5ff; }
        .bi-rank { width: 22px; font-size: 12px; font-weight: 800; color: #cbd5e1; text-align:center; flex-shrink:0; }
        .bi-feed-main { flex:1; min-width:0; }
        .bi-feed-side { text-align:right; flex-shrink:0; }
        .bi-feed-side .bi-num { display:block; }
        .bi-chip { font-size: 11px; font-weight: 700; color: var(--brand); background:#f5f3ff; padding: 2px 8px; border-radius: 6px; }
        .bi-strike { text-decoration: line-through; color: var(--muted); }

        @media (max-width: 1200px) { .bi-kpis { grid-template-columns: repeat(2, minmax(0,1fr)); } .bi-g-2-1, .bi-g-1-1 { grid-template-columns: minmax(0,1fr); } }
        @media (max-width: 640px) { .bi-kpis { grid-template-columns: minmax(0,1fr); } .bi-wrap { padding: 0 12px 32px; } .bi-title { font-size: 24px; }
                                    .bi-table th:nth-child(3), .bi-table td:nth-child(3) { display:none; } }
    </style>

    <div class="bi bi-scroll">
        {{-- ===================== HERO ===================== --}}
        <section class="bi-hero">
            <div class="bi-hero-in">
                <div>
                    <div class="bi-eyebrow">Business Intelligence · Admin</div>
                    <h1 class="bi-title">Visão geral da operação</h1>
                    <div class="bi-sub">
                        <span class="bi-live"><i></i> Atualizado em {{ now()->format('d/m/Y \à\s H:i') }}</span>
                    </div>
                </div>
                <div class="bi-hero-stats">
                    <div class="bi-pill"><small>Empresas</small><strong>{{ $fmtNum($licencas['total']) }}</strong></div>
                    <div class="bi-pill"><small>Notas hoje</small><strong>{{ $fmtNum($notas['hoje']) }}</strong></div>
                    <div class="bi-pill"><small>Emissoras no mês</small><strong>{{ $fmtNum($notas['empresas_mes']) }}</strong></div>
                </div>
            </div>
        </section>

        <div class="bi-wrap">
            {{-- ===================== KPIs ===================== --}}
            <div class="bi-kpis">
                <a href="{{ route('admin.notas.index') }}" class="bi-kpi" style="--accent:#7c3aed; --accent-soft:#f5f3ff;">
                    <div class="bi-kpi-head">
                        <span class="bi-kpi-label">Faturamento no mês</span>
                        <span class="bi-ico"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></span>
                    </div>
                    <div class="bi-kpi-value">{{ $fmtMoeda($notas['valor_mes']) }}</div>
                    <div class="bi-kpi-foot">
                        <span class="bi-trend {{ $tValor['classe'] }}">{{ $tValor['texto'] }}</span>
                        vs. mesmo período do mês anterior
                    </div>
                </a>

                <a href="{{ route('admin.notas.index') }}" class="bi-kpi" style="--accent:#4f46e5; --accent-soft:#eef2ff;">
                    <div class="bi-kpi-head">
                        <span class="bi-kpi-label">Notas emitidas no mês</span>
                        <span class="bi-ico"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></span>
                    </div>
                    <div class="bi-kpi-value">{{ $fmtNum($notas['qtd_mes']) }}</div>
                    <div class="bi-kpi-foot">
                        <span class="bi-trend {{ $tQtd['classe'] }}">{{ $tQtd['texto'] }}</span>
                        Ticket médio {{ $fmtMoeda($notas['ticket_medio']) }}
                    </div>
                </a>

                <a href="{{ route('admin.licencas.index', ['situacao' => 'vencidas']) }}" class="bi-kpi" style="--accent:#ef4444; --accent-soft:#fef2f2;">
                    <div class="bi-kpi-head">
                        <span class="bi-kpi-label">Licenças vencidas</span>
                        <span class="bi-ico"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg></span>
                    </div>
                    <div class="bi-kpi-value">{{ $fmtNum($licencas['vencidas']) }}</div>
                    <div class="bi-kpi-foot">
                        <span class="bi-trend down">{{ $fmtNum($licencas['aviso_exclusao']) }} em aviso de exclusão</span>
                    </div>
                </a>

                <a href="{{ route('admin.licencas.index', ['situacao' => 'a_vencer']) }}" class="bi-kpi" style="--accent:#f59e0b; --accent-soft:#fffbeb;">
                    <div class="bi-kpi-head">
                        <span class="bi-kpi-label">Vencem em {{ $diasAVencer }} dias</span>
                        <span class="bi-ico"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></span>
                    </div>
                    <div class="bi-kpi-value">{{ $fmtNum($licencas['a_vencer']) }}</div>
                    <div class="bi-kpi-foot">Oportunidade de renovação</div>
                </a>
            </div>

            {{-- ===================== GRÁFICO + SAÚDE ===================== --}}
            <div class="bi-grid bi-g-2-1">
                <div class="bi-card" x-data="{ modo: 'qtd' }">
                    <div class="bi-card-h">
                        <div>
                            <h3>Emissões nos últimos 12 meses</h3>
                            <p>Passe o mouse sobre as barras para ver os detalhes</p>
                        </div>
                        <div class="bi-toggle">
                            <button type="button" :class="{ 'on': modo === 'qtd' }" @click="modo = 'qtd'">Quantidade</button>
                            <button type="button" :class="{ 'on': modo === 'valor' }" @click="modo = 'valor'">Faturamento</button>
                        </div>
                    </div>
                    <div class="bi-card-b">
                        <div class="bi-chart">
                            @foreach ($serieMensal as $m)
                                @php
                                    $hq = round($m['qtd'] / $maxQtd * 100, 2);
                                    $hv = round($m['valor'] / $maxValor * 100, 2);
                                @endphp
                                <div class="bi-col {{ $m['atual'] ? 'atual' : '' }}">
                                    <div class="bi-tip">
                                        <b>{{ $m['rotulo'] }}</b><br>
                                        {{ $fmtNum($m['qtd']) }} notas<br>
                                        {{ $fmtMoeda($m['valor']) }}
                                    </div>
                                    <div class="bi-bar" style="height: {{ $hq }}%;"
                                         :style="'height:' + (modo === 'qtd' ? {{ $hq }} : {{ $hv }}) + '%'"></div>
                                </div>
                            @endforeach
                        </div>
                        <div class="bi-axis">
                            @foreach ($serieMensal as $m)
                                <span class="{{ $m['atual'] ? 'atual' : '' }}">{{ $m['rotulo'] }}</span>
                            @endforeach
                        </div>
                        <div class="bi-chart-sum">
                            <div><small>Notas em 12 meses</small><strong>{{ $fmtNum($totalQtd12) }}</strong></div>
                            <div><small>Faturamento em 12 meses</small><strong>{{ $fmtMoeda($totalValor12) }}</strong></div>
                            <div><small>Média mensal</small><strong>{{ $fmtNum($totalQtd12 / max(1, count($serieMensal))) }} notas</strong></div>
                            <div><small>Cancelamento no mês</small><strong>{{ number_format($notas['taxa_cancelamento'], 1, ',', '.') }}%</strong></div>
                        </div>
                    </div>
                </div>

                <div class="bi-card">
                    <div class="bi-card-h">
                        <div>
                            <h3>Saúde das licenças</h3>
                            <p>Distribuição da base de empresas</p>
                        </div>
                        <a href="{{ route('admin.licencas.index') }}" class="bi-link">Gerenciar →</a>
                    </div>
                    <div class="bi-card-b">
                        <div class="bi-donut">
                            <svg width="190" height="190" viewBox="0 0 120 120">
                                <circle cx="60" cy="60" r="52" fill="none" stroke="#f1f5f9" stroke-width="12"/>
                                @foreach ($segmentos as $s)
                                    @if ($s['dash'] > 0)
                                        <circle cx="60" cy="60" r="52" fill="none" stroke="{{ $s['cor'] }}" stroke-width="12"
                                                stroke-dasharray="{{ $s['dash'] }} {{ $circ }}" stroke-dashoffset="{{ $s['offset'] }}"><title>{{ $s['label'] }}: {{ $s['valor'] }}</title></circle>
                                    @endif
                                @endforeach
                            </svg>
                            <div class="bi-donut-c">
                                <strong>{{ number_format($licencas['saude'], 0, ',', '.') }}%</strong>
                                <small>em dia</small>
                            </div>
                        </div>
                        <div class="bi-legend">
                            @php $filtrosLegenda = ['ativas', 'a_vencer', 'vencidas', 'sem_licenca']; @endphp
                            @foreach ($segmentos as $i => $s)
                                <a href="{{ route('admin.licencas.index', ['situacao' => $filtrosLegenda[$i]]) }}">
                                    <i style="background: {{ $s['cor'] }}"></i>
                                    {{ $s['label'] }}
                                    <b>{{ $fmtNum($s['valor']) }}</b>
                                    <em>{{ number_format($s['pct'], 1, ',', '.') }}%</em>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===================== EXCLUSÃO + VENCIDAS ===================== --}}
            <div class="bi-grid bi-g-1-1">
                <div class="bi-card bi-danger-card">
                    <div class="bi-card-h bi-danger-h">
                        <div style="display:flex; align-items:center; gap:14px;">
                            <span class="bi-siren"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg></span>
                            <div>
                                <h3>Avisos de exclusão</h3>
                                <p>Empresas com licença vencida há mais de {{ $diasAvisoExclusao }} dias</p>
                            </div>
                        </div>
                        <span class="bi-count">{{ $fmtNum($avisosExclusao->count()) }}</span>
                    </div>
                    @if ($avisosExclusao->isEmpty())
                        <div class="bi-empty">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Nenhuma empresa em aviso de exclusão.
                        </div>
                    @else
                        <div class="bi-x-list">
                            @foreach ($avisosExclusao as $e)
                                <div class="bi-x-item">
                                    <div class="bi-x-days">
                                        <strong>{{ $e->dias_vencida }}</strong>
                                        <small>dias</small>
                                    </div>
                                    <div class="bi-x-meta">
                                        <div class="bi-emp-n" title="{{ $e->razao_social }}">{{ $nomeEmpresa($e) }}</div>
                                        <div class="bi-emp-d">{{ $fmtDoc($e->cpf_cnpj) }}</div>
                                        <div class="bi-mute">
                                            Venceu em {{ \Carbon\Carbon::parse($e->validate)->format('d/m/Y') }}
                                            @if ($e->responsavel) · {{ $e->responsavel }} @endif
                                        </div>
                                    </div>
                                    <a href="{{ route('admin.licencas.edit', $e->id) }}" class="bi-act">Ver licença</a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="bi-card">
                    <div class="bi-card-h">
                        <div>
                            <h3>Licenças vencidas recentemente</h3>
                            <p>Vencidas há até {{ $diasAvisoExclusao }} dias — prioridade de cobrança</p>
                        </div>
                        <a href="{{ route('admin.licencas.index', ['situacao' => 'vencidas']) }}" class="bi-link">Ver todas →</a>
                    </div>
                    @if ($vencidas->isEmpty())
                        <div class="bi-empty">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Nenhuma licença vencida no período.
                        </div>
                    @else
                        <div style="overflow-x:auto;">
                            <table class="bi-table">
                                <thead>
                                    <tr>
                                        <th>Empresa</th>
                                        <th>Vencida há</th>
                                        <th>Última emissão</th>
                                        <th class="bi-right"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($vencidas as $e)
                                        <tr>
                                            <td>
                                                <div class="bi-emp">
                                                    <span class="bi-av" style="background: linear-gradient(135deg,#f87171,#b91c1c);">{{ $iniciais($nomeEmpresa($e)) }}</span>
                                                    <div style="min-width:0;">
                                                        <div class="bi-emp-n" title="{{ $e->razao_social }}">{{ $nomeEmpresa($e) }}</div>
                                                        <div class="bi-emp-d">{{ $fmtDoc($e->cpf_cnpj) }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="bi-badge {{ $e->dias_vencida > 30 ? 'danger' : 'warn' }}">{{ $e->dias_vencida }} {{ $e->dias_vencida == 1 ? 'dia' : 'dias' }}</span>
                                                <div class="bi-mute" style="margin-top:3px;">{{ \Carbon\Carbon::parse($e->validate)->format('d/m/Y') }}</div>
                                            </td>
                                            <td class="bi-mute">
                                                {{ $e->ultima_emissao ? \Carbon\Carbon::parse($e->ultima_emissao)->format('d/m/Y') : 'Nunca emitiu' }}
                                            </td>
                                            <td class="bi-right">
                                                <a href="{{ route('admin.licencas.edit', $e->id) }}" class="bi-act">Renovar</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ===================== ÚLTIMAS EMISSORAS ===================== --}}
            <div class="bi-grid">
                <div class="bi-card">
                    <div class="bi-card-h">
                        <div>
                            <h3>Últimas 10 empresas que emitiram notas</h3>
                            <p>Atividade de emissão mais recente da plataforma</p>
                        </div>
                        <a href="{{ route('admin.notas.index') }}" class="bi-link">Ver notas →</a>
                    </div>
                    @if ($ultimasEmissoras->isEmpty())
                        <div class="bi-empty">Nenhuma nota emitida ainda.</div>
                    @else
                        <div style="overflow-x:auto;">
                            <table class="bi-table">
                                <thead>
                                    <tr>
                                        <th style="width:40px;">#</th>
                                        <th>Empresa</th>
                                        <th>Última nota</th>
                                        <th>Emitida em</th>
                                        <th class="bi-right">Valor</th>
                                        <th class="bi-right">Notas no mês / total</th>
                                        <th>Licença</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($ultimasEmissoras as $i => $e)
                                        @php
                                            $st = $statusLicenca($e->validate);
                                            $emitida = \Carbon\Carbon::parse($e->emitida_em);
                                        @endphp
                                        <tr>
                                            <td><span class="bi-rank">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span></td>
                                            <td>
                                                <div class="bi-emp">
                                                    <span class="bi-av">{{ $iniciais($nomeEmpresa($e)) }}</span>
                                                    <div style="min-width:0;">
                                                        <div class="bi-emp-n" title="{{ $e->razao_social }}">{{ $nomeEmpresa($e) }}</div>
                                                        <div class="bi-emp-d">{{ $fmtDoc($e->cpf_cnpj) }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="bi-chip">Nº {{ $e->num_nfse ?: '—' }}</span>
                                                @if ((int) $e->cancelada)
                                                    <span class="bi-badge danger" style="margin-left:4px;">Cancelada</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="bi-num" style="font-weight:600;">{{ $emitida->format('d/m/Y H:i') }}</div>
                                                <div class="bi-mute">{{ $emitida->locale('pt_BR')->diffForHumans() }}</div>
                                            </td>
                                            <td class="bi-right bi-num {{ (int) $e->cancelada ? 'bi-strike' : '' }}">{{ $fmtMoeda($e->valor) }}</td>
                                            <td class="bi-right bi-num">{{ $fmtNum($e->notas_mes) }} <span class="bi-mute">/ {{ $fmtNum($e->total_notas) }}</span></td>
                                            <td><span class="bi-badge {{ $st['classe'] }}">{{ $st['texto'] }}</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-area-admin-layout>
