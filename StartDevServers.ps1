# Variabels
$laravelDir = ".\RoosterAppV3-API"
$angularDir = ".\RoosterAppV3-Frontend"
$containers = @('mariadb', 'phpmyadmin')

Write-Host "Starting Dev Servers"

# 1. Boot Docker containers for databases etc.
foreach ($container in $containers)
{
    Write-Host "Start $container container..."
    docker start $container
}

#2. Start Laravel server
$script = "cd $laravelDir; php artisan serve"
Write-Host "Start Laravel Server..."
Start-Process powershell -ArgumentList "-NoExit", "-Command", $script

#3. Start Angular server
$script = "cd $angularDir; ng serve --open"
Write-Host "Start Angular Server...."
Start-Process powershell -ArgumentList "-NoExit", "-Command", $script