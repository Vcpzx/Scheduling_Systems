<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex, nofollow">
  <title>Quick Campus Tune-Up | Campus Scheduler</title>
  <style>
    :root {
      --ink: #17211b;
      --muted: #66736a;
      --paper: #fffdf8;
      --green: #2f7d4a;
      --green-dark: #1f5d36;
      --mint: #dff1e3;
      --yellow: #f5c451;
      --coral: #e9795d;
      --line: #dce7dc;
    }

    * { box-sizing: border-box; }
    html { min-width: 320px; }
    body {
      margin: 0;
      min-height: 100vh;
      overflow-x: hidden;
      color: var(--ink);
      font-family: "Trebuchet MS", "Segoe UI", sans-serif;
      background:
        radial-gradient(circle at 9% 18%, rgba(245, 196, 81, .32) 0 5rem, transparent 5.1rem),
        radial-gradient(circle at 92% 84%, rgba(233, 121, 93, .2) 0 8rem, transparent 8.1rem),
        linear-gradient(135deg, #f4f8ef 0%, #fdf9ed 55%, #eef6f1 100%);
    }

    .maintenance-shell {
      width: min(100%, 1100px);
      min-height: 100vh;
      margin: 0 auto;
      padding: clamp(1.25rem, 5vw, 4rem) clamp(1rem, 5vw, 3rem);
      display: grid;
      align-content: center;
      gap: clamp(2rem, 6vw, 4rem);
    }

    .topbar { display: flex; align-items: center; justify-content: space-between; gap: 1rem; }
    .brand { display: inline-flex; align-items: center; gap: .7rem; color: var(--ink); text-decoration: none; font-weight: 800; letter-spacing: .01em; }
    .brand-mark { display: grid; place-items: center; width: 2.6rem; height: 2.6rem; color: white; background: var(--green); border-radius: .8rem .8rem .8rem .25rem; font-size: .8rem; box-shadow: .25rem .25rem 0 rgba(47, 125, 74, .18); }
    .status-pill { display: inline-flex; align-items: center; gap: .45rem; padding: .55rem .8rem; color: var(--green-dark); background: rgba(255, 255, 255, .72); border: 1px solid var(--line); border-radius: 999px; font-size: .78rem; font-weight: 700; }
    .status-dot { width: .55rem; height: .55rem; background: var(--yellow); border-radius: 50%; box-shadow: 0 0 0 .25rem rgba(245, 196, 81, .2); animation: pulse 1.8s ease-in-out infinite; }

    .content { display: grid; grid-template-columns: minmax(0, 1fr) minmax(250px, 360px); align-items: center; gap: clamp(2rem, 8vw, 7rem); }
    .eyebrow { margin: 0 0 1rem; color: var(--green); font-size: .8rem; font-weight: 800; letter-spacing: .14em; text-transform: uppercase; }
    h1 { max-width: 680px; margin: 0; font-size: clamp(2.5rem, 7vw, 5.7rem); line-height: .95; letter-spacing: -.04em; }
    .lede { max-width: 560px; margin: 1.5rem 0 0; color: var(--muted); font-size: clamp(1rem, 2vw, 1.2rem); line-height: 1.65; }
    .note { max-width: 520px; margin: 1.5rem 0 0; padding: 1rem 1.1rem; color: var(--green-dark); background: rgba(223, 241, 227, .75); border-left: .3rem solid var(--green); border-radius: .2rem .8rem .8rem .2rem; font-size: .9rem; line-height: 1.5; }

    .illustration { position: relative; min-height: 330px; display: grid; place-items: center; }
    .sun { position: absolute; top: 0; right: 4%; width: 5.5rem; height: 5.5rem; background: var(--yellow); border-radius: 50%; box-shadow: 0 0 0 1rem rgba(245, 196, 81, .12); }
    .cloud { position: absolute; top: 2.3rem; left: 3%; width: 7rem; height: 2.4rem; background: white; border-radius: 2rem; opacity: .72; }
    .cloud::before, .cloud::after { content: ""; position: absolute; bottom: 0; background: white; border-radius: 50%; }
    .cloud::before { left: 1.2rem; width: 3rem; height: 3rem; }
    .cloud::after { left: 3.5rem; width: 2.3rem; height: 2.3rem; }
    .board { position: relative; z-index: 1; width: min(100%, 300px); padding: 1.5rem; background: var(--paper); border: 1px solid var(--line); border-radius: 1.5rem .5rem 1.5rem .5rem; box-shadow: .8rem .8rem 0 rgba(47, 125, 74, .12), 0 1.4rem 3rem rgba(38, 66, 42, .1); transform: rotate(3deg); }
    .board-top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; color: var(--muted); font-size: .7rem; font-weight: 800; text-transform: uppercase; letter-spacing: .1em; }
    .pin { width: .8rem; height: .8rem; background: var(--coral); border-radius: 50%; box-shadow: .3rem .3rem 0 rgba(233, 121, 93, .18); }
    .board h2 { margin: 0; color: var(--green-dark); font-size: 1.6rem; line-height: 1.05; }
    .board p { margin: .75rem 0 1.3rem; color: var(--muted); font-size: .85rem; line-height: 1.5; }
    .progress { height: .7rem; overflow: hidden; background: #edf1e9; border-radius: 999px; }
    .progress span { display: block; width: 68%; height: 100%; background: linear-gradient(90deg, var(--green), #70b978); border-radius: inherit; animation: work 2.4s ease-in-out infinite alternate; }
    .tools { display: flex; align-items: end; justify-content: center; gap: .75rem; margin-top: 2rem; }
    .tool { position: relative; width: 2.8rem; height: 4.5rem; background: var(--coral); border-radius: .3rem .3rem .8rem .8rem; transform: rotate(-12deg); }
    .tool::before { content: ""; position: absolute; top: -.7rem; left: .35rem; width: 2.1rem; height: 1rem; background: #f2a079; border-radius: .6rem .6rem .2rem .2rem; }
    .tool:nth-child(2) { height: 3.5rem; background: var(--yellow); transform: rotate(10deg); }
    .tool:nth-child(2)::before { background: #ffe08a; }
    .tool:nth-child(3) { width: 4.5rem; height: 1.6rem; background: var(--green); border-radius: 1rem; transform: rotate(-4deg); }
    .tool:nth-child(3)::before { top: -.35rem; left: 1.7rem; width: 1rem; height: .5rem; background: #8acb91; }
    .footer { color: var(--muted); font-size: .78rem; text-align: center; }

    @keyframes pulse { 50% { opacity: .45; transform: scale(.8); } }
    @keyframes work { from { width: 52%; } to { width: 82%; } }
    @media (max-width: 720px) {
      .topbar { align-items: flex-start; }
      .status-pill { font-size: .7rem; }
      .content { grid-template-columns: 1fr; gap: 2rem; }
      .illustration { min-height: 260px; order: -1; }
      .board { max-width: 270px; }
      .footer { margin-top: -1rem; }
    }
    @media (max-width: 420px) {
      .topbar { flex-direction: column; }
      .status-pill { align-self: flex-start; }
      h1 { font-size: 2.8rem; }
      .illustration { min-height: 230px; transform: scale(.9); transform-origin: center top; margin-bottom: -1.5rem; }
    }
    @media (prefers-reduced-motion: reduce) {
      *, *::before, *::after { animation: none !important; }
    }
  </style>
</head>
<body>
  <main class="maintenance-shell">
    <header class="topbar">
      <a class="brand" href="/" aria-label="Campus Scheduler home">
        <span class="brand-mark">CS</span>
        <span>Campus Scheduler</span>
      </a>
      <div class="status-pill"><span class="status-dot" aria-hidden="true"></span> Campus tune-up in progress</div>
    </header>

    <section class="content" aria-labelledby="maintenance-title">
      <div>
        <p class="eyebrow">Be right back</p>
        <h1 id="maintenance-title">We are making room for something better.</h1>
        <p class="lede">The campus calendar is taking a short breather while we polish the schedule, tighten a few bolts, and get everything ready for your next class.</p>
        <p class="note"><strong>Your plans are safe.</strong> This is a temporary pause. Please check back soon and the scheduler will be ready to welcome you.</p>
      </div>

      <div class="illustration" aria-hidden="true">
        <span class="sun"></span>
        <span class="cloud"></span>
        <div>
          <div class="board">
            <div class="board-top"><span>Maintenance log</span><span class="pin"></span></div>
            <h2>Almost there!</h2>
            <p>Our tiny campus crew is doing the last checks before opening the doors.</p>
            <div class="progress"><span></span></div>
          </div>
          <div class="tools"><span class="tool"></span><span class="tool"></span><span class="tool"></span></div>
        </div>
      </div>
    </section>

    <p class="footer">Campus Scheduler · Thanks for your patience</p>
  </main>
</body>
</html>
