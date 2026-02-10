<!DOCTYPE html>
<html>
<head>
    <title>Garage Auto</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
    <h1>🚗 Garage Auto - Application</h1>
    <p>Laravel fonctionne avec PostgreSQL</p>
    
    <?php
    // Test PostgreSQL
    try {
        $conn = pg_connect("host=postgres dbname=garage_auto user=garage_user password=garage_password");
        if ($conn) {
            echo "<p class='success'>✅ PostgreSQL connecté</p>";
            
            // Test version
            $result = pg_query($conn, "SELECT version()");
            if ($result) {
                $row = pg_fetch_row($result);
                echo "<p>Version: " . htmlspecialchars($row[0]) . "</p>";
                pg_free_result($result);
            }
            pg_close($conn);
        } else {
            echo "<p class='error'>❌ Échec connexion PostgreSQL</p>";
        }
    } catch (Exception $e) {
        echo "<p class='error'>❌ Erreur: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    ?>
    
    <hr>
    <h2>Actions disponibles:</h2>
    <ul>
        <li><a href="/">Accueil</a></li>
        <li><a href="/test-db">Test Base de données</a></li>
    </ul>
</body>
</html>
