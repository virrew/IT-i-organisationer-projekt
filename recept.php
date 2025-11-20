<?php 
session_start(); 
?> 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recept</title>
</head>
<body>

    <section class="header">
    <h1>Mina recept</h1>
    <p>Översikt över dina aktiva och utgångna recept</p>
    </section>

    <!-- Sektion: Aktiva recept -->
    <section class="recept-list">
        <h2>Aktiva recept</h2>

        <!-- RECEPTKORT 1 -->
        <article class="recept-card">

            <!-- översta raden: namn + status -->
            <div class="recept-card-header">
                <h3 class="recept-namn">Metformin Actavis</h3>
                <span class="recept-status">Aktivt</span>
            </div>

            <!-- styrka och dosering -->
            <div class="recept-info">
                <div class="styrka">500 mg</div>
                <div class="dosering">2 tabletter 2 gånger dagligen</div>
            </div>

            <!-- förskrivare och giltighet -->
            <div class="recept-meta">
                <div class="forskrivare">
                    <span class="label">Förskrivare</span>
                    <span class="value">Dr. Anna Svensson</span>
                </div>

                <div class="giltig-tom">
                    <span class="label">Giltig t.o.m.</span>
                    <span class="value">15 oktober 2025</span>
                </div>

                <div class="utfardat">
                    <span class="label">Utfärdat</span>
                    <span class="value">15 oktober 2024</span>
                </div>
            </div>
        </article>

        <!-- RECEPTKORT 2 -->
        <article class="recept-card">

            <div class="recept-card-header">
                <h3 class="recept-namn">Enalapril Sandoz</h3>
                <span class="recept-status">Aktivt</span>
            </div>

            <div class="recept-info">
                <div class="styrka">10 mg</div>
                <div class="dosering">1 tablett 1 gång dagligen</div>
            </div>

            <div class="recept-meta">
                <div class="forskrivare">
                    <span class="label">Förskrivare</span>
                    <span class="value">Dr. Erik Andersson</span>
                </div>

                <div class="giltig-tom">
                    <span class="label">Giltig t.o.m.</span>
                    <span class="value">1 november 2025</span>
                </div>

                <div class="utfardat">
                    <span class="label">Utfärdat</span>
                    <span class="value">1 november 2024</span>
                </div>
            </div>
        </article>

    </section>

    <h2>Utgångna recept</h2>
        <!-- RECEPTKORT 1 -->
          <article class="recept-card">

            <div class="recept-card-header">
                <h3 class="recept-namn">Enalapril Sandoz</h3>
                <span class="recept-status">Aktivt</span>
            </div>

            <div class="recept-info">
                <div class="styrka">10 mg</div>
                <div class="dosering">1 tablett 1 gång dagligen</div>
            </div>

            <div class="recept-meta">
                <div class="forskrivare">
                    <span class="label">Förskrivare</span>
                    <span class="value">Dr. Erik Andersson</span>
                </div>

                <div class="giltig-tom">
                    <span class="label">Giltig t.o.m.</span>
                    <span class="value">1 november 2025</span>
                </div>

                <div class="utfardat">
                    <span class="label">Utfärdat</span>
                    <span class="value">1 november 2024</span>
                </div>
            </div>
        </article>
</body>
</html>