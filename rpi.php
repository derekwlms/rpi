<?php
    require 'TeamRPI.php';
    if (isset($_POST['submit'])) {
        calculateRPI($_POST['schedules']);
    }

    function calculateRPI($schedules) {
        TeamRPI::parseSchedules($schedules);
        TeamRPI::computeAllRPIs();
        TeamRPI::sortByRPI();     
        show_teams(TeamRPI::getAllTeams());
    }

    function show_teams($teams) {
        echo '<table border="1">';
        echo '<th>Rank</th><th>Name</th><th>Record</th><th>Win %</th><th>RPI</th>';
        $i=1;
        foreach ($teams as $team) {
            echo '<tr border="1">';
            printf('<td border="1">%d</td>',$i++);
            printf('<td border="1">%s</td>',$team->getName());
            printf('<td border="1">%s</td>',$team->getRecord());
            printf('<td border="1">%.3f</td>',$team->getWinPercent());
            printf('<td border="1">%.3f</td>',$team->getRPI());
            echo '</tr>';
        }
        echo '</table>';
    }
?>
