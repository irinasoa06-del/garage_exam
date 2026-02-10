param(
    [switch]$SkipBackend,
    [switch]$SkipWeb,
    [switch]$SkipMobile,
    [switch]$ForceInstall
)

$ErrorActionPreference = "Stop"

function Ensure-Command {
    param([Parameter(Mandatory)][string]$Name)
    if (-not (Get-Command $Name -ErrorAction SilentlyContinue)) {
        throw "La commande '$Name' est introuvable. Veuillez l'installer ou l'ajouter au PATH."
    }
}

function Invoke-InDirectory {
    param(
        [Parameter(Mandatory)][string]$Path,
        [Parameter(Mandatory)][scriptblock]$ScriptBlock
    )

    Push-Location $Path
    try {
        & $ScriptBlock
    }
    finally {
        Pop-Location
    }
}

function Run-Step {
    param(
        [Parameter(Mandatory)][string]$Message,
        [Parameter(Mandatory)][scriptblock]$Action
    )

    Write-Host "`n=== $Message ===" -ForegroundColor Cyan
    try {
        & $Action
        Write-Host "OK Termine" -ForegroundColor Green
    }
    catch {
        Write-Host "ERREUR : $_" -ForegroundColor Red
        throw
    }
}

$repoRoot = Split-Path -Parent $MyInvocation.MyCommand.Path
$backendPath = Join-Path $repoRoot "laravel-web"
$webPath = Join-Path $repoRoot "garage-web-front\vue-project"
$mobilePath = Join-Path $repoRoot "garage-mobile"

Write-Host "=== Lancement unifie du projet Garage ===" -ForegroundColor Yellow
Write-Host "Repertoire racine : $repoRoot"

Ensure-Command docker
Ensure-Command npm

if (-not (Test-Path $backendPath)) {
    throw "Repertoire backend introuvable : $backendPath"
}
if (-not (Test-Path $webPath)) {
    Write-Host "Frontend web introuvable : $webPath" -ForegroundColor Yellow
    $SkipWeb = $true
}
if (-not (Test-Path $mobilePath)) {
    Write-Host "Application mobile introuvable : $mobilePath" -ForegroundColor Yellow
    $SkipMobile = $true
}

if (-not $SkipBackend) {
    $composeFile = Join-Path $backendPath "docker-compose-image.yml"
    if (-not (Test-Path $composeFile)) {
        throw "Fichier docker compose introuvable : $composeFile"
    }

    Run-Step -Message "Backend - Preparation du fichier .env" -Action {
        Invoke-InDirectory -Path $backendPath -ScriptBlock {
            if (-not (Test-Path ".env")) {
                if (Test-Path ".env.backup") {
                    Copy-Item ".env.backup" ".env"
                    Write-Host "Fichier .env cree depuis .env.backup"
                } else {
                    $envContent = @(
                        "APP_NAME=Laravel",
                        "APP_ENV=local",
                        "APP_KEY=",
                        "APP_DEBUG=true",
                        "APP_URL=http://localhost:8000",
                        "",
                        "DB_CONNECTION=pgsql",
                        "DB_HOST=postgres",
                        "DB_PORT=5432",
                        "DB_DATABASE=garage_auto",
                        "DB_USERNAME=garage_user",
                        "DB_PASSWORD=garage_password",
                        "",
                        "FIREBASE_PROJECT_ID=",
                        "FIREBASE_CREDENTIALS="
                    ) -join "`n"
                    $envContent | Out-File ".env" -Encoding UTF8 -NoNewline
                    Write-Host "Fichier .env genere avec valeurs par defaut"
                }
            } else {
                Write-Host ".env deja present"
            }
        }
    }

    Run-Step -Message "Backend - Lancement des conteneurs Docker" -Action {
        Invoke-InDirectory -Path $backendPath -ScriptBlock {
            docker compose -f "docker-compose-image.yml" up -d --build
        }
    }

    Run-Step -Message "Backend - Installation des dependances Composer" -Action {
        Invoke-InDirectory -Path $backendPath -ScriptBlock {
            $vendorPath = Join-Path (Get-Location) "vendor"
            $needsInstall = $ForceInstall -or -not (Test-Path (Join-Path $vendorPath "autoload.php"))
            if ($needsInstall) {
                docker compose -f "docker-compose-image.yml" exec laravel composer install
            } else {
                Write-Host "Dependances deja installees"
            }
        }
    }

    Run-Step -Message "Backend - Migrations et seed" -Action {
        Invoke-InDirectory -Path $backendPath -ScriptBlock {
            docker compose -f "docker-compose-image.yml" exec laravel php artisan migrate --seed
        }
    }
}
else {
    Write-Host "Backend ignore (parametre --SkipBackend)" -ForegroundColor DarkYellow
}

if (-not $SkipWeb) {
    Run-Step -Message "Frontend web - Installation des dependances" -Action {
        Invoke-InDirectory -Path $webPath -ScriptBlock {
            $nodeModules = Join-Path (Get-Location) "node_modules"
            if ($ForceInstall -or -not (Test-Path $nodeModules)) {
                npm install
            } else {
                Write-Host "node_modules deja present"
            }
        }
    }

    Run-Step -Message "Frontend web - Demarrage du serveur Vite" -Action {
        $command = "Set-Location `"$webPath`"; npm run dev"
        Start-Process -FilePath "powershell" -ArgumentList "-NoExit", "-Command", $command
        Write-Host "Serveur web lance dans une nouvelle fenetre PowerShell"
    }
}
else {
    Write-Host "Frontend web ignore" -ForegroundColor DarkYellow
}

if (-not $SkipMobile) {
    Run-Step -Message "Application mobile - Installation des dependances" -Action {
        Invoke-InDirectory -Path $mobilePath -ScriptBlock {
            $nodeModules = Join-Path (Get-Location) "node_modules"
            if ($ForceInstall -or -not (Test-Path $nodeModules)) {
                npm install
            } else {
                Write-Host "node_modules deja present"
            }
        }
    }

    Run-Step -Message "Application mobile - Demarrage du serveur Ionic" -Action {
        $command = "Set-Location `"$mobilePath`"; npm run start"
        Start-Process -FilePath "powershell" -ArgumentList "-NoExit", "-Command", $command
        Write-Host "Serveur Ionic lance dans une nouvelle fenetre PowerShell"
    }
}
else {
    Write-Host "Application mobile ignoree" -ForegroundColor DarkYellow
}

Write-Host "`n=== Lancement termine ===" -ForegroundColor Yellow
Write-Host "Endpoints attendus :"
Write-Host "- API Laravel : http://localhost:8000/api/v1" -ForegroundColor Gray
if (-not $SkipWeb) {
    Write-Host "- Frontend web : http://localhost:5173" -ForegroundColor Gray
}
if (-not $SkipMobile) {
    Write-Host "- Application mobile : http://localhost:4200" -ForegroundColor Gray
}

Write-Host "Pour arreter les conteneurs : docker compose -f laravel-web/docker-compose-image.yml down" -ForegroundColor DarkGray
